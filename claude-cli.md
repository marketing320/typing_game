# Project: Typing Monkey — Complete Mobile-First Typing Challenge Game

You are building a complete production-ready monorepo project called **Typing Monkey**.

This is NOT an MVP. Build a complete working application with backend, frontend, admin panel, database migrations, authentication, email OTP flow, game logic, geolocation restriction, scoring system, leaderboard, and polished Phaser.js game scene animation.

## Tech Stack

Use:

- Laravel 13
- Laravel Blade
- MySQL
- Phaser.js
- Vite
- Tailwind CSS
- Alpine.js where useful
- Node.js mailer service using Nodemailer for OTP email delivery
- Monorepo structure
- Mobile-first responsive UI
- Clean, readable code
- Proper validation
- Secure backend scoring
- Admin-configurable challenge system

Database name:

```sql
typing_game
Main Goal

Create a mobile-first typing challenge game inspired by a fun “Typing Monkey” theme.

The game has 2 landing page options:

Rehearsal Mode
No registration required
User can practise typing
WPM and accuracy are shown
Attempt is not counted as official challenge attempt
Challenge Mode
User must enter:
Email
Username
OTP is sent to email using Nodemailer
User verifies OTP
User can enter the official challenge only after OTP verification
User only has 1 life
User can only attempt once unless admin allows next-calendar-day retry
Before entering Challenge Mode, system checks geolocation
If user is outside allowed radius, show warning popup and block challenge access
Monorepo Structure

Create a complete monorepo like this:

typing-monkey/
├── apps/
│   ├── web/                         # Laravel 13 app
│   │   ├── app/
│   │   ├── bootstrap/
│   │   ├── config/
│   │   ├── database/
│   │   ├── public/
│   │   ├── resources/
│   │   │   ├── css/
│   │   │   ├── js/
│   │   │   │   ├── phaser/
│   │   │   │   │   ├── scenes/
│   │   │   │   │   ├── objects/
│   │   │   │   │   ├── utils/
│   │   │   │   │   └── game.js
│   │   │   └── views/
│   │   ├── routes/
│   │   ├── tests/
│   │   ├── composer.json
│   │   ├── package.json
│   │   └── vite.config.js
│   │
│   └── mailer/                      # Node.js Nodemailer OTP service
│       ├── src/
│       │   ├── index.js
│       │   ├── mailer.js
│       │   └── templates/
│       ├── package.json
│       └── .env.example
│
├── packages/
│   ├── shared/
│   │   ├── scoring/
│   │   │   └── scoring.js
│   │   ├── geofence/
│   │   │   └── haversine.js
│   │   └── constants/
│   │       └── game.js
│
├── docs/
│   ├── SETUP.md
│   ├── DATABASE.md
│   ├── GAME_RULES.md
│   ├── ADMIN_GUIDE.md
│   └── SECURITY_NOTES.md
│
├── .env.example
├── README.md
├── package.json
└── pnpm-workspace.yaml

Use pnpm workspace.

Required Features
1. Landing Page

Create a polished mobile-first landing page.

It should show:

Typing Monkey logo/title
Short game description
Two large cards:
Rehearsal Mode
Challenge Mode

Each card should have:

Icon or animated monkey-style illustration using CSS/Phaser-style assets
Clear CTA button
Mobile responsive layout
Fun but clean UI

Suggested visual style:

Warm jungle/game theme
Monkey mascot
Banana accents
Rounded cards
Soft shadows
Mobile-first touch-friendly buttons
Smooth hover and tap animations
2. Rehearsal Mode

Rehearsal mode should:

Not require login
Load active rehearsal wording set by admin
Allow unlimited practice
Show live typing feedback
Show WPM
Show accuracy
Show mistakes
Show elapsed time
Show completion result screen
Save anonymous rehearsal attempt if possible

Use localStorage anonymous ID for rehearsal tracking.

3. Challenge Mode

Challenge mode flow:

Click Challenge Mode
→ Browser requests geolocation
→ Backend checks allowed radius
→ If outside radius, show warning popup
→ If inside radius, continue to email + username form
→ User submits email + username
→ Backend creates/updates player
→ Generate OTP
→ Send OTP using Node.js Nodemailer service
→ User verifies OTP
→ Backend checks one-try rule
→ Start official challenge
→ Save attempt as started
→ Phaser game starts
→ User types challenge text
→ One life only
→ If user makes too many fatal mistakes or exits, mark failed
→ On completion, submit result
→ Backend recalculates WPM and accuracy
→ Save result
→ Show result screen
→ Show leaderboard
4. Admin Panel

Build complete admin panel using Laravel Blade.

Admin must be able to:

Dashboard

Show:

Total players
Total challenge attempts
Average WPM
Highest WPM
Today’s attempts
Active challenge
Geofence status
Recent submissions
Challenge Management

Admin can:

Create challenge
Edit challenge
Delete challenge
Set challenge status:
Draft
Active
Ended
Set start date/time
Set end date/time
Configure:
Allow retry next calendar day: yes/no
Max attempts per day
Require geofence: yes/no
Select geofence rule
Typing Text Management

Admin can create wording for:

Challenge Mode
Rehearsal Mode

Fields:

Title
Mode
Content
Difficulty
Language
Active status

Admin should be able to preview the text.

Geofence Management

Admin can create and edit geofence rule:

Name
Latitude
Longitude
Radius in meters
Warning message
Active status
Player Management

Admin can view:

Username
Email
Verified status
Total attempts
Best WPM
Best accuracy
Block/unblock player
Attempts Management

Admin can view all attempts:

Player
Challenge
Mode
WPM
Accuracy
Duration
Status
IP address
Device fingerprint
Geolocation data
Within radius or not
Created date

Admin can filter by:

Challenge
Date
Status
Player
WPM range
Leaderboard

Admin and public users can see leaderboard.

Leaderboard should show:

Rank
Username
WPM
Accuracy
Duration
Completed time

Ranking rule:

Highest WPM first
If same WPM, higher accuracy wins
If same accuracy, shorter duration wins
5. Database Schema

Create Laravel migrations for these tables:

admins
id
name
email unique
password
role enum: super_admin, admin
is_active boolean
timestamps
players
id
username
email unique
email_verified_at nullable
last_login_at nullable
is_blocked boolean default false
timestamps
email_otps
id
email
otp_code
purpose enum: login, register, challenge_access
expires_at
verified_at nullable
attempts integer default 0
max_attempts integer default 5
ip_address nullable
user_agent nullable
timestamps
indexes: email, expires_at
typing_challenges
id
title
description nullable
status enum: draft, active, ended
start_at nullable
end_at nullable
allow_retry_next_day boolean default false
max_attempts_per_day integer default 1
require_geofence boolean default false
geofence_rule_id nullable
created_by nullable
timestamps
typing_texts
id
challenge_id nullable
mode enum: challenge, rehearsal
title nullable
content longtext
language default en
difficulty enum: easy, medium, hard
is_active boolean default true
created_by nullable
timestamps
geofence_rules
id
name
latitude decimal(10,7)
longitude decimal(10,7)
radius_meters integer default 100
warning_message nullable
is_active boolean default true
timestamps
challenge_attempts
id
challenge_id
player_id
typing_text_id
status enum: started, completed, failed, disqualified
started_at nullable
completed_at nullable
duration_seconds decimal(10,3) nullable
total_words integer default 0
correct_words integer default 0
wrong_words integer default 0
total_characters integer default 0
correct_characters integer default 0
wrong_characters integer default 0
wpm decimal(8,2) default 0
accuracy decimal(5,2) default 0
mistake_count integer default 0
remaining_lives integer default 1
user_input longtext nullable
ip_address nullable
user_agent nullable
latitude decimal(10,7) nullable
longitude decimal(10,7) nullable
distance_from_allowed_meters decimal(10,2) nullable
is_within_geofence boolean nullable
device_fingerprint nullable
timestamps
rehearsal_attempts
id
typing_text_id
anonymous_id nullable
started_at nullable
completed_at nullable
duration_seconds decimal(10,3) nullable
total_words integer default 0
correct_words integer default 0
wrong_words integer default 0
total_characters integer default 0
correct_characters integer default 0
wrong_characters integer default 0
wpm decimal(8,2) default 0
accuracy decimal(5,2) default 0
user_input longtext nullable
ip_address nullable
user_agent nullable
device_fingerprint nullable
timestamps
player_devices
id
player_id nullable
device_fingerprint
ip_address nullable
user_agent nullable
first_seen_at nullable
last_seen_at nullable
timestamps
system_settings
id
setting_key unique
setting_value text nullable
timestamps
6. Laravel Backend Requirements

Create:

Models
Admin
Player
EmailOtp
TypingChallenge
TypingText
ChallengeAttempt
RehearsalAttempt
GeofenceRule
PlayerDevice
SystemSetting

Use relationships properly.

Example:

TypingChallenge hasMany TypingText
TypingChallenge hasMany ChallengeAttempt
Player hasMany ChallengeAttempt
TypingText hasMany ChallengeAttempt
TypingText hasMany RehearsalAttempt
Controllers

Create frontend controllers:

HomeController
RehearsalController
ChallengeAccessController
OtpController
ChallengeGameController
LeaderboardController

Create admin controllers:

AdminAuthController
AdminDashboardController
AdminChallengeController
AdminTypingTextController
AdminGeofenceController
AdminPlayerController
AdminAttemptController
AdminLeaderboardController
AdminSettingController
Services

Create service classes:

OtpService
MailerService
GeofenceService
ScoringService
ChallengeAttemptService
DeviceFingerprintService
LeaderboardService

Keep business logic inside services, not controllers.

7. OTP Email Flow

Use Node.js Nodemailer service under:

apps/mailer

Laravel should call the mailer service through HTTP API.

Mailer service endpoints:

POST /send-otp

Payload:

{
  "email": "user@example.com",
  "otp": "123456",
  "username": "User"
}

The Node mailer service should:

Validate required fields
Send OTP email
Use environment SMTP credentials
Return success/error JSON

Create .env.example:

MAILER_PORT=4001
SMTP_HOST=smtp.example.com
SMTP_PORT=587
SMTP_USER=
SMTP_PASS=
SMTP_FROM_NAME="Typing Monkey"
SMTP_FROM_EMAIL=no-reply@example.com

Laravel .env.example should include:

APP_NAME="Typing Monkey"
APP_URL=http://localhost:8000

DB_DATABASE=typing_game
DB_USERNAME=root
DB_PASSWORD=

OTP_EXPIRY_MINUTES=10
OTP_MAX_ATTEMPTS=5

MAILER_SERVICE_URL=http://localhost:4001
8. Phaser.js Game Requirements

Build Phaser game inside:

apps/web/resources/js/phaser

Required Phaser files:

game.js
scenes/BootScene.js
scenes/PreloadScene.js
scenes/MenuScene.js
scenes/TypingScene.js
scenes/ResultScene.js
objects/MonkeyMascot.js
objects/TypingTextDisplay.js
objects/BananaProgressBar.js
utils/scoring.js
utils/api.js
Visual Direction

The game should look like a fun Typing Monkey game.

Style:

Cartoon monkey mascot
Jungle-inspired background
Banana progress bar
Wooden signboard panels
Floating leaves
Soft cloud movement
Animated vines
Mobile-friendly large typing area
Cute success animation when word is correct
Monkey jumps/claps when user types correctly
Monkey gets shocked when user makes mistake
Banana meter fills as user progresses
Smooth transition into result screen

Use simple generated graphics if no image assets exist.

Phaser should draw placeholder graphics using shapes where needed:

Monkey face can be simple circle shapes
Banana can be curved yellow-like graphic
Vines/leaves can be simple shapes
Wooden panels can be rectangles with borders

Do not require external paid assets.

Game Mechanics

The typing game should:

Display the full text
Highlight current word
Highlight typed characters
Mark correct characters
Mark wrong characters
Count mistakes
Detect WPM live
Detect accuracy live
Detect completion
Stop timer when completed
Submit result to backend
Support mobile input

Important mobile requirement:

Use a hidden HTML input field focused by tapping the game area so mobile keyboard appears properly.

Phaser should handle the visual game display, but typing input should come from DOM input for mobile compatibility.

Challenge Mode Rules

For Challenge Mode:

User has 1 life
If challenge is started, save attempt immediately as started
If user refreshes/exits after starting, that attempt is considered used
On completion, submit result
Backend recalculates score
Frontend score is only for display
Backend score is final
Rehearsal Mode Rules

For Rehearsal Mode:

No login required
Unlimited attempts
Save anonymous attempt if possible
Show result only
Do not show official leaderboard rank
9. Backend Scoring Rules

Implement scoring in Laravel backend.

WPM formula:

WPM = (correct_characters / 5) / (duration_seconds / 60)

Accuracy formula:

Accuracy = (correct_characters / total_characters) * 100

Backend must compare submitted user input against original typing text.

Do not trust frontend WPM.

The backend should calculate:

Total words
Correct words
Wrong words
Total characters
Correct characters
Wrong characters
WPM
Accuracy
Mistake count
Duration
10. Geolocation Rules

When user clicks Challenge Mode:

Frontend should request browser geolocation.

Send to backend:

{
  "latitude": 3.1234567,
  "longitude": 101.1234567
}

Backend should:

Get active challenge
Check if geofence is required
Get selected geofence rule
Calculate distance using Haversine formula
If within radius, allow
If outside radius, block and return warning message

Response if allowed:

{
  "allowed": true
}

Response if blocked:

{
  "allowed": false,
  "message": "You are outside the allowed event area."
}

Also store geolocation info in challenge_attempts.

11. Security Requirements

Implement:

CSRF protection
Laravel validation
Rate limit OTP request
Rate limit OTP verification
OTP expiry
OTP max attempts
Do not reveal whether email already exists
Recalculate score on backend
Prevent duplicate challenge attempts
Store started attempt immediately
Track IP address
Track user agent
Generate simple device fingerprint on frontend
Prevent blocked players from entering challenge
Admin auth middleware
Secure admin routes

Device fingerprint can be based on:

User agent
Screen size
Timezone
Browser language
Canvas fingerprint if easy
Random localStorage ID

Do not overcomplicate fingerprinting, but include enough to flag suspicious attempts.

12. Routes

Create frontend routes:

GET  /
GET  /rehearsal
POST /rehearsal/submit

GET  /challenge/check-location
POST /challenge/request-otp
POST /challenge/verify-otp
GET  /challenge/play
POST /challenge/start
POST /challenge/submit

GET /leaderboard

Create admin routes:

GET  /admin/login
POST /admin/login
POST /admin/logout

GET /admin/dashboard

GET /admin/challenges
GET /admin/challenges/create
POST /admin/challenges
GET /admin/challenges/{id}/edit
PUT /admin/challenges/{id}
DELETE /admin/challenges/{id}

GET /admin/typing-texts
GET /admin/typing-texts/create
POST /admin/typing-texts
GET /admin/typing-texts/{id}/edit
PUT /admin/typing-texts/{id}
DELETE /admin/typing-texts/{id}

GET /admin/geofence
GET /admin/geofence/create
POST /admin/geofence
GET /admin/geofence/{id}/edit
PUT /admin/geofence/{id}
DELETE /admin/geofence/{id}

GET /admin/players
GET /admin/players/{id}
POST /admin/players/{id}/block
POST /admin/players/{id}/unblock

GET /admin/attempts
GET /admin/leaderboard
GET /admin/settings
POST /admin/settings

Use RESTful route names.

13. Blade UI Requirements

Create layouts:

resources/views/layouts/app.blade.php
resources/views/layouts/admin.blade.php

Frontend pages:

home.blade.php
rehearsal/index.blade.php
challenge/access.blade.php
challenge/otp.blade.php
challenge/play.blade.php
challenge/result.blade.php
leaderboard/index.blade.php

Admin pages:

admin/auth/login.blade.php
admin/dashboard.blade.php
admin/challenges/index.blade.php
admin/challenges/create.blade.php
admin/challenges/edit.blade.php
admin/typing-texts/index.blade.php
admin/typing-texts/create.blade.php
admin/typing-texts/edit.blade.php
admin/geofence/index.blade.php
admin/geofence/create.blade.php
admin/geofence/edit.blade.php
admin/players/index.blade.php
admin/players/show.blade.php
admin/attempts/index.blade.php
admin/leaderboard/index.blade.php
admin/settings/index.blade.php

Use Tailwind CSS.

UI should be:

Mobile-first
Clean
Fun
Polished
Touch friendly
Not too corporate
Monkey/jungle themed for frontend
Simple admin dashboard for backend
14. Seeders

Create seeders for:

Super admin
Default challenge
Default rehearsal typing text
Default challenge typing text
Default geofence rule
System settings

Default admin:

Email: admin@typingmonkey.local
Password: password

Show this clearly in README.

Seed default sample challenge:

Title: Typing Monkey Grand Challenge
Status: active
Allow retry next day: false
Require geofence: false

Sample rehearsal text:

The little monkey jumps from branch to branch while collecting bananas in the bright morning sun.

Sample challenge text:

Speed and focus are the keys to winning this typing challenge. Keep your eyes on the words and type with confidence.
15. README Requirements

Create complete README with:

Project description
Tech stack
Folder structure
Requirements
Installation steps
Environment setup
Database setup
How to run Laravel
How to run Vite
How to run mailer service
How to run queue if needed
Admin login
Game rules
OTP setup
Geofence setup
Troubleshooting

Example commands:

pnpm install
cd apps/web
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve

cd apps/mailer
cp .env.example .env
pnpm install
pnpm dev

cd apps/web
pnpm dev
16. Testing Requirements

Create useful tests where practical:

OTP generation test
OTP expiry test
Geofence distance test
Scoring calculation test
Challenge one-attempt rule test
Leaderboard ranking test

Use Laravel feature/unit tests.

17. Completion Requirement

Do not stop after creating only file structure.

Fully implement:

Working Laravel app
Working admin login
Working admin CRUD
Working player OTP flow
Working challenge access flow
Working Phaser game
Working mobile input
Working WPM calculation
Working result submission
Working leaderboard
Working geofence check
Working seed data
Working README

At the end, provide:

Summary of completed features
Installation commands
Admin login credentials
How to test Rehearsal Mode
How to test Challenge Mode
Any known limitations
18. Important Implementation Notes
Mobile Input

Phaser keyboard events alone are not enough for mobile.

Use a hidden or styled HTML input/textarea.

When user taps the Phaser game area:

input.focus()

Then sync input value to Phaser scene.

Attempt Locking

For Challenge Mode:

Create attempt when game starts
If user refreshes, the attempt remains used
Do not allow another attempt if admin does not allow retry
If admin allows retry next calendar day, allow only one attempt per day
Backend Score Trust

Frontend can display live WPM, but backend must be final authority.

Never trust frontend submitted WPM or accuracy.

Frontend submits:

{
  "attempt_id": 1,
  "user_input": "typed text here",
  "started_at": "...",
  "completed_at": "...",
  "duration_seconds": 42.381
}

Backend calculates:

WPM
Accuracy
Correct characters
Wrong characters
Correct words
Wrong words
Mistakes
One Life

Interpret “one life” as:

Once official challenge starts, it is consumed
If player exits, refreshes, closes tab, or fails, they cannot retry unless admin allows retry next day

Do not make the player lose instantly from one typo, because typing games commonly allow correction. But count mistakes.

Add admin setting later if needed for strict one-mistake elimination.

19. Design Quality

Make the frontend feel like a finished game, not a plain form.

The user should see:

Animated monkey mascot
Banana progress bar
Jungle/game background
Smooth typing feedback
Result celebration
Clean leaderboard

Admin should feel like a clean dashboard.

Use Tailwind utility classes, responsive layouts, and reusable Blade components.

20. Build Now

Start building the full monorepo.

Create all necessary files.

Prefer clean code over shortcuts.

When generating code, make sure it is consistent, runnable, and follows the structure above.


---

A small but important note: for this project, I’d suggest you **don’t let Phaser control all typing directly**. For mobile, the better structure is:

```txt
HTML input / textarea handles real typing
Phaser handles animation + game display
Laravel Backend handles final scoring
