// k6 load test — a more realistic mix: most users watch the leaderboard, some
// browse the public pages. (Sign-up/OTP is intentionally excluded — it's
// rate-limited to 5/min/IP and sends synchronous SMTP, so it isn't a throughput
// path you load-test blindly.)
//
//   docker run --rm -i --network host grafana/k6 run - < loadtest/mixed-journey.js

import http from 'k6/http';
import { sleep, check, group } from 'k6';

const BASE = __ENV.BASE || 'http://127.0.0.1:8084';
const PEAK = parseInt(__ENV.PEAK || '1000', 10);

export const options = {
  scenarios: {
    // ~85% of load: leaderboard pollers
    viewers: {
      executor: 'ramping-vus',
      startVUs: 0,
      stages: [
        { duration: '2m', target: Math.round(PEAK * 0.85 * 0.3) },
        { duration: '3m', target: Math.round(PEAK * 0.85) },
        { duration: '3m', target: Math.round(PEAK * 0.85) },
        { duration: '1m', target: 0 },
      ],
      exec: 'viewer',
    },
    // ~15% of load: people browsing rehearsal / challenge pages
    browsers: {
      executor: 'ramping-vus',
      startVUs: 0,
      stages: [
        { duration: '2m', target: Math.round(PEAK * 0.15 * 0.3) },
        { duration: '3m', target: Math.round(PEAK * 0.15) },
        { duration: '3m', target: Math.round(PEAK * 0.15) },
        { duration: '1m', target: 0 },
      ],
      exec: 'browser',
    },
  },
  thresholds: {
    http_req_duration: ['p(95)<1000'],
    http_req_failed:   ['rate<0.02'],
  },
};

export function viewer() {
  http.get(`${BASE}/leaderboard`);          // one initial full-page load
  for (let i = 0; i < 6; i++) {             // then poll a handful of times
    const r = http.get(`${BASE}/leaderboard/data`, { headers: { Accept: 'application/json' } });
    check(r, { 'lb 200': (res) => res.status === 200 });
    sleep(5 + Math.random());
  }
}

export function browser() {
  group('browse', () => {
    check(http.get(`${BASE}/`),                  { 'home 200':      (r) => r.status === 200 });
    sleep(2 + Math.random() * 2);
    check(http.get(`${BASE}/rehearsal`),         { 'rehearsal 200': (r) => r.status === 200 });
    sleep(3 + Math.random() * 3);
    check(http.get(`${BASE}/challenge/access`),  { 'access 200':    (r) => r.status === 200 });
    sleep(2 + Math.random() * 2);
  });
}
