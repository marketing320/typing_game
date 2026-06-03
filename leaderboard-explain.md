# How the Leaderboard Works

## Step 1 — Scoring (ScoringService.php)

When a player finishes typing, `ScoringService::analyze()` compares their input to the original text character-by-character and produces two key numbers.

### WPM (Words Per Minute)

```
WPM = (correct characters ÷ 5) ÷ (duration in minutes)
```

- **Correct characters** = only the characters the player typed that exactly matched the original.
- Dividing by **5** is the standard "net WPM" formula — the industry convention treats every 5 characters as one "word", regardless of actual word length.
- Dividing by **duration in minutes** normalises for speed.

Example: 200 correct chars in 60 seconds → (200 ÷ 5) ÷ 1 = **40 WPM**

### Accuracy (%)

```
Accuracy = (correct characters ÷ total characters in the original text) × 100
```

- `total characters` is the length of the **original** text, not what the player typed.
- A missed or wrong character counts against you.
- Result is rounded to 2 decimal places.

Example: 180 correct out of 200 chars → (180 ÷ 200) × 100 = **90.00%**

### Mistake Count (displayed but not used for ranking)

A "mistake" is counted once per *run* of wrong characters — e.g. typing three wrong characters in a row is 1 mistake, not 3. Used for display only.

---

## Step 2 — Score (LeaderboardService.php)

Score is a single number that rewards both speed and accuracy:

```
Score = WPM × (Accuracy ÷ 100)
```

Examples:
| WPM | Accuracy | Score |
|---|---|---|
| 80 | 95.00% | 76.00 |
| 60 | 100.00% | 60.00 |
| 90 | 80.00% | 72.00 |

A player who types faster but sloppily can be beaten by a slower but more accurate player. Score is shown on the leaderboard as the primary headline stat.

---

## Step 3 — Ranking (LeaderboardService.php)

Attempts are first sorted by **three criteria in order**:

| Priority | Column | Direction | Reason |
|---|---|---|---|
| 1st | `wpm` | Descending | Fastest typist ranks higher |
| 2nd | `accuracy` | Descending | If WPM is equal, more accurate player wins |
| 3rd | `duration_seconds` | Ascending | If still equal, whoever finished quicker wins |

```php
->orderByDesc('wpm')
->orderByDesc('accuracy')
->orderBy('duration_seconds')
```

### Olympic / Skip Ranking (1-2-2-4)

After sorting, ranks are assigned with **Olympic-style shared ranking**. Players who are identical on all three columns (WPM, accuracy, and duration) receive the **same rank number**. The next distinct player's rank **skips** to their actual 1-based position in the list.

```
1. Alice   90 WPM  100.00%  11.4s   ← tied
1. Bob     90 WPM  100.00%  11.4s   ← tied, same rank
3. Carol   75 WPM  97.50%   17.8s   ← rank skips to 3 (position in sorted list)
4. Dave    70 WPM  95.00%   20.1s
```

This is implemented in `LeaderboardService::rankAttempts()`. While iterating the sorted list, if an entry's WPM, accuracy, and duration all equal the previous entry's, the rank is kept. Otherwise, rank = current 1-based index.

---

## Where Each Method Is Used

- `getForChallenge($challenge)` — used on the public leaderboard when an active challenge exists; shows only attempts for that specific challenge, capped at 50 entries.
- `getGlobal()` — fallback when no active challenge; shows all completed attempts across all challenges, also capped at 50.

---

## Seeder

`LeaderboardSeeder` seeds **50 fake players and attempts** against the active challenge:

- **4 deliberate ties** — two pairs with identical WPM, accuracy, and computed duration — to demonstrate the shared ranking.
- **46 random entries** spread across a realistic WPM range: slow (20–40), average (41–70), good (71–100), and fast (101–130).

Run with:
```bash
php artisan db:seed --class=LeaderboardSeeder
```

Or as part of the full seed:
```bash
php artisan db:seed
```
