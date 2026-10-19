<?php

require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['session.driver' => 'array']);
session(['challenge_email' => 'player@example.com']);

$challenge = new App\Models\TypingChallenge(['title' => 'The daily speed run', 'status' => 'active']);
$challenge->id = 1;
$player = new App\Models\Player(['username' => 'flowstate', 'email' => 'player@example.com']);
$text = new App\Models\TypingText(['title' => 'Find your rhythm', 'content' => 'Small steps make a big difference. Find your rhythm and let your fingers do the talking.', 'difficulty' => 'medium', 'language' => 'en']);
$text->id = 1;
$attempt = new App\Models\ChallengeAttempt(['wpm' => 72, 'accuracy' => 98.5, 'correct_words' => 18, 'wrong_words' => 1, 'correct_characters' => 89, 'wrong_characters' => 1, 'mistake_count' => 1, 'duration_seconds' => 15.3]);
$attempt->setRelation('player', $player)->setRelation('challenge', $challenge);
$entries = collect([
    ['rank' => 1, 'username' => 'keyboardcrush', 'score' => 93.2, 'wpm' => 96, 'accuracy' => 97.1, 'duration_seconds' => 12.1],
    ['rank' => 2, 'username' => 'flowstate', 'score' => 70.9, 'wpm' => 72, 'accuracy' => 98.5, 'duration_seconds' => 15.3],
    ['rank' => 3, 'username' => 'justvibing', 'score' => 62.7, 'wpm' => 66, 'accuracy' => 95, 'duration_seconds' => 18.8],
]);
$pages = [
    'home' => ['home', ['activeChallenge' => $challenge]],
    'practice' => ['rehearsal.index', compact('text')],
    'access' => ['challenge.access', ['requireGeofence' => false]],
    'otp' => ['challenge.otp', []],
    'play' => ['challenge.play', compact('player', 'challenge', 'text') + ['referralOptions' => ['Social media', 'Friend / Family', 'Event poster', 'Other']]],
    'result' => ['challenge.result', compact('attempt')],
    'leaderboard' => ['leaderboard.index', compact('entries') + ['activeChallenge' => $challenge]],
    'login' => ['admin.auth.login', ['errors' => new Illuminate\Support\ViewErrorBag]],
    'admin' => ['admin.dashboard', ['stats' => ['total_players' => 128, 'total_attempts' => 256, 'avg_wpm' => 56.2, 'highest_wpm' => 96, 'today_attempts' => 24, 'active_challenge' => $challenge], 'recentAttempts' => collect()]],
];
$directory = __DIR__.'/ui-preview';
if (!is_dir($directory)) mkdir($directory);
$manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
$css = file_get_contents(public_path('build/'.$manifest['resources/css/app.css']['file']));
foreach ($pages as $name => [$view, $data]) {
    $html = view($view, $data)->render();
    // Embed the compiled styles for a local visual preview; no application data is changed.
    $html = preg_replace('~<link[^>]+rel="stylesheet"[^>]*>~', '', $html);
    $html = str_replace('</head>', '<style>'.$css.'</style></head>', $html);
    file_put_contents($directory.'/'.$name.'.html', $html);
}
echo 'Rendered '.count($pages)." preview pages.\n";
