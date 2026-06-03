# Security Notes

## Backend Score Trust

Frontend WPM/accuracy values are **display-only**. The backend always recalculates from the submitted `user_input` string. Never trust client-submitted scores.

## OTP Security

- OTPs are 6-digit numeric codes
- Expire after 10 minutes (configurable)
- Max 5 wrong attempts per OTP before lockout
- Old OTPs are invalidated when a new one is requested
- Cooldown: 60 seconds between OTP requests for the same email
- Error messages never reveal whether an email exists

## Attempt Locking

- Attempt row created with `status = started` immediately when game begins
- Prevents double attempts even on refresh/disconnect

## Device Fingerprint

Frontend generates a fingerprint from:
- User agent
- Screen dimensions
- Timezone
- Browser language
- Random localStorage ID

Stored per attempt for suspicious pattern detection.

## CSRF Protection

All POST routes are CSRF-protected via Laravel's built-in middleware. JSON API routes use `X-CSRF-TOKEN` header.

## Rate Limiting

Consider adding Laravel rate limiting to:
- `POST /challenge/request-otp` (60s cooldown enforced in service)
- `POST /challenge/verify-otp`

Add to `bootstrap/app.php` → `withMiddleware` if stricter limits needed.
