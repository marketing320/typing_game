// k6 load test — models real leaderboard viewers polling /leaderboard/data every 5s.
//
// Run (from the VPS, via Docker, against the INTERNAL container port — bypasses
// TLS and the host nginx rate limit so you measure raw app+DB capacity):
//
//   docker run --rm -i --network host grafana/k6 run - < loadtest/leaderboard.js
//
// Override target host or ramp ceiling:
//   docker run --rm -i --network host -e BASE=http://127.0.0.1:8084 -e PEAK=1000 \
//       grafana/k6 run - < loadtest/leaderboard.js
//
// To test the PUBLIC edge instead (only after relaxing the rate limit):
//   ... -e BASE=https://tm.brightstarcomp.my ...

import http from 'k6/http';
import { sleep, check } from 'k6';
import { Rate } from 'k6/metrics';

const BASE = __ENV.BASE || 'http://127.0.0.1:8084';
const PEAK = parseInt(__ENV.PEAK || '1000', 10);

const errors = new Rate('app_errors');

export const options = {
  scenarios: {
    // The dominant real-world load: many browsers each polling every 5s.
    leaderboard_viewers: {
      executor: 'ramping-vus',
      startVUs: 0,
      gracefulRampDown: '10s',
      stages: [
        { duration: '1m', target: Math.round(PEAK * 0.1) },  // warm up
        { duration: '2m', target: Math.round(PEAK * 0.3) },
        { duration: '2m', target: Math.round(PEAK * 0.6) },
        { duration: '3m', target: PEAK },                     // climb to peak
        { duration: '3m', target: PEAK },                     // hold at peak
        { duration: '1m', target: 0 },                        // ramp down
      ],
    },
  },
  thresholds: {
    http_req_duration: ['p(95)<800', 'p(99)<2000'], // tune to your SLA
    http_req_failed:   ['rate<0.01'],               // <1% errors
    app_errors:        ['rate<0.01'],
  },
};

export default function () {
  const res = http.get(`${BASE}/leaderboard/data`, {
    headers: { Accept: 'application/json' },
    tags: { endpoint: 'leaderboard_data' },
  });

  const ok = check(res, {
    'status is 200': (r) => r.status === 200,
    'has entries':   (r) => r.status === 200 && r.body && r.body.includes('entries'),
  });
  errors.add(!ok);

  // Mirror the frontend's 5s poll, with a little jitter so requests don't sync up.
  sleep(5 + Math.random());
}
