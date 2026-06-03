/**
 * Calculate WPM: (correct_characters / 5) / (duration_seconds / 60)
 */
export function calculateWpm(correctCharacters, durationSeconds) {
    if (!durationSeconds || durationSeconds <= 0) return 0;
    return Math.round((correctCharacters / 5) / (durationSeconds / 60));
}

/**
 * Calculate accuracy as percentage
 */
export function calculateAccuracy(correctCharacters, totalCharacters) {
    if (!totalCharacters || totalCharacters <= 0) return 0;
    return Math.round((correctCharacters / totalCharacters) * 10000) / 100;
}

/**
 * Compare typed input against original text character-by-character.
 * Returns stats object.
 */
export function analyzeInput(originalText, typedText) {
    const originalChars = originalText.split('');
    const typedChars = typedText.split('');

    let correctChars = 0;
    let wrongChars = 0;
    const total = originalChars.length;

    for (let i = 0; i < total; i++) {
        if (i < typedChars.length) {
            if (typedChars[i] === originalChars[i]) {
                correctChars++;
            } else {
                wrongChars++;
            }
        }
    }

    const originalWords = originalText.trim().split(/\s+/);
    const typedWords = typedText.trim().split(/\s+/);
    let correctWords = 0;
    let wrongWords = 0;

    originalWords.forEach((word, i) => {
        if (typedWords[i] === word) {
            correctWords++;
        } else {
            wrongWords++;
        }
    });

    return {
        totalCharacters: total,
        correctCharacters: correctChars,
        wrongCharacters: wrongChars,
        totalWords: originalWords.length,
        correctWords,
        wrongWords,
    };
}
