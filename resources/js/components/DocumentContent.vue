<template>
    <div ref="content-bounds" v-html="documentContent"/>
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
    start: 10,
    end: 50,
    color: 'yellow'
};

// add demo highlight to page, from start to end number of characters offset from start of contentBound div:
highlights.value.push(demoHighlight);

const addHighlightToContent = () => {
    let content = documentContent.value;
    highlights.value.forEach(highlight => {
        const before = content.slice(0, highlight.start);
        const highlightedText = content.slice(highlight.start, highlight.end);
        const after = content.slice(highlight.end);
        content = `${before}<span style="background-color: ${highlight.color};">${highlightedText}</span>${after}`;
    });
    documentContent.value = content;

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
            var start = describePos(range.startContainer, range.startOffset);
            var end = describePos(range.endContainer, range.endOffset);
            if (start !== null && end !== null) {
                return [start, end];
            }
        }


        // add new highlight to highlights array
        highlights.value.push({
            start: start,
            end: end,
            color: 'lightblue'
        });

        // re-render the content with highlights
        addHighlightToContent();

    }
};

// Get the document offset from a position
const describePos = function(node, offset) {
  // Convert current offset from character to bytes
  offset = new TextEncoder().encode(node.textContent.substring(0, offset)).length;
  while(!node.id) {
    if(node.previousSibling) {
      node = node.previousSibling;
      offset += new TextEncoder().encode(node.textContent).length;
    } else {
      node = node.parentNode;
    }
  }
  if(node.id.substring(0, 11) != 'doc-offset-') {
    return null;
  }
  return parseInt(node.id.substring(11)) + offset;
}



onMounted(async () => {
    await loadDocumentContent(documentId.value);

    addHighlightToContent();

    window.addEventListener('mouseup', handleTextSelection);
})


</script>
