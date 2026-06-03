# Admin Guide

## Login

URL: `/admin/login`
Default credentials:
- Email: `admin@typingmonkey.local`
- Password: `password`

**Change the password after first login.**

## Challenge Management

1. Go to **Challenges** → Create Challenge
2. Set status to **Active** to make it live
3. Assign a **Typing Text** with mode = "Challenge" to the challenge
4. Optionally assign a **Geofence Rule** and enable "Require Geofence"

## Typing Texts

- Mode **Rehearsal**: used in free practice, no challenge linked
- Mode **Challenge**: linked to a specific challenge

## Geofence Setup

1. Go to **Geofence** → Create Rule
2. Enter the center lat/lng of your event location
3. Set radius in meters (e.g. 500 for 500m radius)
4. Set a warning message for blocked users
5. Assign the rule to a challenge and enable "Require Geofence"

## Player Management

- View all players and their attempt history
- Block players to prevent further challenge participation
- Unblock at any time

## Settings

Configurable via Settings page:
- `site_name` — displayed site name
- `otp_expiry_minutes` — how long OTPs are valid
- `otp_max_attempts` — max wrong attempts before OTP is locked
