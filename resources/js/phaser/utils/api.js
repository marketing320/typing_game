const CSRF = () => document.querySelector('meta[name=csrf-token]')?.content || '';

export async function startAttempt(deviceFingerprint) {
    const res = await fetch('/challenge/start', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF() },
        body: JSON.stringify({ device_fingerprint: deviceFingerprint }),
    });
    return res.json();
}

export async function submitAttempt(attemptId, userInput, durationSeconds) {
    const res = await fetch('/challenge/submit', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF() },
        body: JSON.stringify({ attempt_id: attemptId, user_input: userInput, duration_seconds: durationSeconds }),
    });
    return res.json();
}
