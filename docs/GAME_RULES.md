# Game Rules

## Rehearsal Mode

- No login required
- Unlimited attempts
- Uses the active rehearsal typing text
- Shows live WPM, accuracy, mistakes, time
- Results are saved anonymously (via localStorage ID)
- Does NOT appear on the official leaderboard

## Challenge Mode

1. Player clicks Challenge Mode
2. Browser requests geolocation (if geofence is enabled for the challenge)
3. If outside allowed radius → blocked with warning message
4. Player enters email and username → OTP sent via email
5. Player verifies OTP (valid 10 minutes, max 5 attempts)
6. System checks if player has a remaining attempt
7. **Attempt is recorded as "started" immediately when game begins**
8. Player types the challenge text
9. On completion, result is submitted to backend
10. Backend recalculates WPM and accuracy (frontend score is display-only)
11. Result shown with leaderboard link

## One Life Rule

- Once the challenge starts, the attempt is consumed
- Refreshing/closing the tab counts as a used attempt
- Admin can enable "retry next day" to allow one attempt per calendar day

## Scoring

- **WPM** = (correct_characters / 5) / (duration_seconds / 60)
- **Accuracy** = (correct_characters / total_characters) × 100
- Backend score is always final

## Leaderboard Ranking

1. Highest WPM first
2. If tied: Higher accuracy wins
3. If still tied: Shorter duration wins
