/**
 * Build a character-level alignment map between raw markdown and
 * the plain text produced by rendering it (i.e. the DOM's textContent).
 *
 * Uses greedy matching: walks both strings in parallel, skipping
 * markdown syntax characters in the raw string that don't appear
 * in the rendered plain text.
 *
 * Returns charMap where charMap[plainIndex] = rawIndex for each
 * character in the plain text.
 */
export function buildCharMap(rawMarkdown: string, plainText: string): number[] {
    const charMap: number[] = [];
    let r = 0;

    for (let p = 0; p < plainText.length; p++) {
        while (r < rawMarkdown.length && rawMarkdown[r] !== plainText[p]) {
            r++;
        }
        charMap[p] = r < rawMarkdown.length ? r : rawMarkdown.length;
        if (r < rawMarkdown.length) r++;
    }

    return charMap;
}

/**
 * Convert a plain-text (visible) offset to a raw markdown offset.
 *
 * @param charMap - from buildCharMap()
 * @param plainOffset - cursor position in the plain text (between-character)
 * @param rawLen - length of the raw markdown string
 * @param isEnd - true if this is an end-of-range offset (position after last char)
 */
export function plainToRawOffset(
    charMap: number[],
    plainOffset: number,
    rawLen: number,
    isEnd: boolean = false,
): number {
    if (charMap.length === 0) return 0;

    if (isEnd) {
        // End offset = position after the character at plainOffset - 1
        if (plainOffset <= 0) return charMap[0];
        const lastIdx = Math.min(plainOffset - 1, charMap.length - 1);
        return charMap[lastIdx] + 1;
    }

    // Start offset = position of the character at plainOffset
    if (plainOffset >= charMap.length) return rawLen;
    return charMap[Math.max(0, plainOffset)];
}
