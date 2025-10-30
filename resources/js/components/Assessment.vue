<template>
    <div class="flex px-2 py-2 h-screen ">

        <!--    sidebar -->
        <div class="flex-grow p-4 mr-8 h-full">
            <h2 class="text-2xl font-bold">Document Content (ID: {{ documentId }})</h2>
            <div class="flex flex-col justify-start items-center">
                <div v-for="highlight in highlights" :key="highlight.start" class="mb-2 w-full">
                    <div :style="{ backgroundColor: highlight.color }" class="p-2 rounded-md">
                        Highlight from {{ highlight.start }} to {{ highlight.end }}
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-grow border border-gray-400 ps-12 p-4 rounded-md overflow-scroll h-[90vh]" ref="contentDiv">
        <pre>
            <div ref="content-bounds" v-html="formattedDocumentContent"/>

        </pre>

        </div>
    </div>
</template>

<script setup>
import {defineProps, onMounted, ref, useTemplateRef} from "vue";

const props = defineProps({
    documentId: {
        type: Number,
        required: true
    }
});

const documentId = ref(props.documentId);
const documentContent = ref('');
const formattedDocumentContent = ref('');

// Load document content from server
const loadDocumentContent = async (id) => {
    try {
        const response = await fetch(`/policy-documents/${id}/content`);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.text();
        documentContent.value = data;
        console.log('Document content loaded:', data);

    } catch (error) {
        console.error('Error loading document content:', error);
    }
};

// on highlight / selection of text inside content div, log the selected text
const contentBounds = useTemplateRef('content-bounds');

const highlights = ref([]);

const demoHighlight = {
    start: 3660,
    end: 4033,
    color: 'yellow'
};

const demoHighlight2 = {
    start: 60,
    end: 100,
    color: 'lightgreen'
};

const demoHighlight3 = {
    start: 110,
    end: 150,
    color: 'orange'
};

// add demo highlight to page, from start to end number of characters offset from start of contentBound div:
highlights.value.push(demoHighlight);
highlights.value.push(demoHighlight3);
highlights.value.push(demoHighlight2);

/**
 * <span id="data-offset-0">This is some sample text for the document content. Users can select text to highlight it.</span>-
 * <span id="data-offset-0">This is some sample <span style="background-color: yellow;" id="data-offset-20">text for the document content. Users can</span> select text to highlight it.</span>
 */

const addHighlightsToContent = () => {

    let content = documentContent.value;

    highlights.value.sort((a, b) => b.end - a.end)

    console.log(highlights);

        highlights.value.forEach(highlight => {
        const before = content.slice(0, highlight.start);
        const highlightedText = content.slice(highlight.start, highlight.end);
        const after = content.slice(highlight.end);
        content = `${before}<span style="background-color: ${highlight.color};">${highlightedText}</span><span data-offset="${highlight.end}">${after}</span>`;
    });

    formattedDocumentContent.value = content;

    console.log('highlight added', content);
};

const handleTextSelection = () => {
    const selection = window.getSelection();

    // if the selection is not inside the content div, return
    if (!contentBounds.value.contains(selection.anchorNode) || !contentBounds.value.contains(selection.focusNode)) {
        return;
    }

    const selectedText = selection.toString();
    if (selectedText) {
        console.log('Selected text:', selectedText);

        // get start and end index of selection relative to content div
        const range = selection.getRangeAt(0);
        if (!range.collapsed) {


            let offset = parseInt(range.startContainer.parentElement.dataset.offset ?? 0);

            console.log('offset:', offset);

            console.log('Range is collapsed');
            console.log('Start container:', range.startContainer);
            console.log('Start offset:', range.startOffset);
            console.log('End container:', range.endContainer);
            console.log('End offset:', range.endOffset);

            console.log('Test. text from documentContent:', documentContent.value.substring(range.startOffset, range.endOffset));

            // add new highlight to highlights array
            highlights.value.push({
                start: offset + range.startOffset,
                end: offset + range.endOffset,
                color: 'lightblue'
            });

        }
        // re-render the content with highlights
        addHighlightsToContent();

    }
};

// Get the document offset from a position
const describePos = function (node, offset) {
    // Convert current offset from character to bytes
    offset = new TextEncoder().encode(node.textContent.substring(0, offset)).length;
    while (!node.id) {
        if (node.previousSibling) {
            node = node.previousSibling;
            offset += new TextEncoder().encode(node.textContent).length;
        } else {
            node = node.parentNode;
        }
    }
    if (node.id.substring(0, 11) != 'doc-offset-') {
        return null;
    }
    return parseInt(node.id.substring(11)) + offset;
}


onMounted(async () => {
    await loadDocumentContent(documentId.value);

    addHighlightsToContent();

    window.addEventListener('mouseup', handleTextSelection);
})

</script>
