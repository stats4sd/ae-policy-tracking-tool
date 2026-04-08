import {
    onMounted,
    ref,
    useTemplateRef,
    type Ref
} from "vue";

import {
    findOffsetAncestor
} from "@/composables/findOffsetAncestor.ts";

export function useTextSelection(contentContainer: Ref<HTMLElement | null>) {

    const currentSelection = ref<Range | null>(null);


    onMounted((): void => {
        console.log('boo');
        contentContainer.value.addEventListener("mouseup", handleTextSelection);
    });

    const handleTextSelection = (): void => {
        const selection = window.getSelection();


        console.log('selection', selection);

        if (!selection) return;

        // if the selection is not inside the content div, return
        if (!contentContainer.value?.contains(selection.anchorNode) || !contentContainer.value?.contains(selection.focusNode)) {
            return;
        }


        const selectedText = selection.toString();

        console.log(selectedText);
        if (selectedText) {
            currentSelection.value = selection.getRangeAt(0);
            if (!currentSelection.value.collapsed) {
                // find base offset from nearest ancestor
                const offset = findOffsetAncestor(currentSelection.value.startContainer);
            }
        }
    };


    const expandSelectionToWordBoundaries = (): void => {

        // update selected range to word boundaries
        const isWordChar = (c: string) => /\w/.test(c);
        let startContainer = currentSelection.value.startContainer;
        let startOffset = currentSelection.value.startOffset;
        let endContainer = currentSelection.value.endContainer;
        let endOffset = currentSelection.value.endOffset;
        // Expand start
        while (startContainer.nodeType === Node.TEXT_NODE && startOffset > 0) {
            const text = startContainer.textContent || "";
            if (!isWordChar(text[startOffset - 1])) break;
            startOffset--;
        }
        // Expand end
        const textEnd = endContainer.textContent || "";
        while (endContainer.nodeType === Node.TEXT_NODE && endOffset < textEnd.length) {
            if (!isWordChar(textEnd[endOffset])) break;
            endOffset++;
        }
        const newRange = document.createRange();
        newRange.setStart(startContainer, startOffset);
        newRange.setEnd(endContainer, endOffset);

        currentSelection.value = newRange;

    };

    const expandSelectionToSentenceBoundaries = (): void => {
        // update selected range to sentence boundaries
        const sentenceEndChars = [".", "!", "?"];
        let startContainer = currentSelection.value.startContainer;
        let startOffset = currentSelection.value.startOffset;
        let endContainer = currentSelection.value.endContainer;
        let endOffset = currentSelection.value.endOffset;
        // Expand start
        while (startContainer.nodeType === Node.TEXT_NODE && startOffset > 0) {
            const text = startContainer.textContent || "";
            if (sentenceEndChars.includes(text[startOffset - 1])) break;
            startOffset--;
        }
        // Expand end
        const textEnd = endContainer.textContent || "";
        while (endContainer.nodeType === Node.TEXT_NODE && endOffset < textEnd.length) {
            if (sentenceEndChars.includes(textEnd[endOffset])) break;
            endOffset++;
        }
        const newRange = document.createRange();
        newRange.setStart(startContainer, startOffset);
        newRange.setEnd(endContainer, endOffset);

        currentSelection.value = newRange;

    }

    return {
        currentSelection,
        expandSelectionToWordBoundaries,
        expandSelectionToSentenceBoundaries,
    };
}
