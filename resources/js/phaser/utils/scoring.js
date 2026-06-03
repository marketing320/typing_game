export function calcWpm(correctChars, elapsedSeconds) {
    if (!elapsedSeconds || elapsedSeconds <= 0) return 0;
    return Math.round((correctChars / 5) / (elapsedSeconds / 60));
}

export function calcAccuracy(correctChars, totalTyped) {
    if (!totalTyped) return 100;
    return Math.round((correctChars / totalTyped) * 100);
}
