<?php

namespace App\Services;

class ScoringService
{
    public function analyze(string $originalText, string $typedText, float $durationSeconds): array
    {
        $originalChars = mb_str_split($originalText);
        $typedChars = mb_str_split($typedText);
        $total = count($originalChars);

        $correctChars = 0;
        $wrongChars = 0;

        for ($i = 0; $i < $total; $i++) {
            if (isset($typedChars[$i])) {
                if ($typedChars[$i] === $originalChars[$i]) {
                    $correctChars++;
                } else {
                    $wrongChars++;
                }
            }
        }

        $originalWords = preg_split('/\s+/', trim($originalText));
        $typedWords = preg_split('/\s+/', trim($typedText));

        $correctWords = 0;
        $wrongWords = 0;

        foreach ($originalWords as $i => $word) {
            if (isset($typedWords[$i]) && $typedWords[$i] === $word) {
                $correctWords++;
            } else {
                $wrongWords++;
            }
        }

        $wpm = $durationSeconds > 0
            ? round(($correctChars / 5) / ($durationSeconds / 60), 2)
            : 0;

        $accuracy = $total > 0
            ? round(($correctChars / $total) * 100, 2)
            : 0;

        $mistakeCount = 0;
        $prevWasWrong = false;
        for ($i = 0; $i < $total; $i++) {
            $isWrong = isset($typedChars[$i]) && $typedChars[$i] !== $originalChars[$i];
            if ($isWrong && !$prevWasWrong) {
                $mistakeCount++;
            }
            $prevWasWrong = $isWrong;
        }

        return [
            'total_words' => count($originalWords),
            'correct_words' => $correctWords,
            'wrong_words' => $wrongWords,
            'total_characters' => $total,
            'correct_characters' => $correctChars,
            'wrong_characters' => $wrongChars,
            'wpm' => $wpm,
            'accuracy' => $accuracy,
            'mistake_count' => $mistakeCount,
        ];
    }
}
