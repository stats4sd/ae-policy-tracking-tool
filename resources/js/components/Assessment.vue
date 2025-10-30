<template>
    <div class="flex px-2 py-2 h-screen">
        <!--    sidebar -->
        <div class="flex-grow p-4 mr-8 h-full">
            <h2 class="text-2xl font-bold">
                Document Content (ID: {{ documentId }})
            </h2>
            <div class="flex flex-col justify-start items-center">
                <div
                    v-for="highlight in highlights"
                    :key="highlight.start"
                    class="mb-2 w-full"
                >
                    <div
                        :style="{ backgroundColor: highlight.color }"
                        class="p-2 rounded-md"
                    >
                        Highlight from {{ highlight.start }} to
                        {{ highlight.end }}
                    </div>
                </div>
            </div>
        </div>

        <div
            class="flex-grow border border-gray-400 ps-12 p-4 rounded-md overflow-scroll h-[90vh]"
            ref="contentDiv"
        >
            <pre>
            <div ref="content-bounds" v-html="formattedDocumentContent"/>

        </pre>
        </div>
    </div>

    <!-- Modal for text selection -->
    <HighlightModal
        v-if="showModal"
        title="Confirm Action"
        width="sm"
        v-on:close="showModal = false"
    >
        <p class="text-gray-800">
            Highlighted text: "<strong>{{
                currentSelection ? currentSelection.toString() : ""
            }}</strong
            >"
        </p>

        <p>Confirm highlight?</p>
        <div class="text-right mt-4">
            <button
                @click="showModal = false"
                class="px-4 py-2 text-sm text-gray-600 focus:outline-none hover:underline"
            >
                Cancel
            </button>
            <button
                @click="confirmHighlight"
                class="mr-2 px-4 py-2 text-sm rounded text-white bg-green-500 focus:outline-none hover:bg-green-400"
            >
                Confirm
            </button>
        </div>
    </HighlightModal>
</template>

<script setup lang="ts">
import { defineProps, onMounted, ref, useTemplateRef } from "vue";
import HighlightModal from "./HighlightModal.vue";

interface Highlight {
    start: number;
    end: number;
    color: string;
}

interface Props {
    documentId: number;
}

const props = defineProps<Props>();

const documentId = ref<number>(props.documentId);
const documentContent = ref<string>("");
const formattedDocumentContent = ref<string>("");

// Load document content from server
const loadDocumentContent = async (id: number): Promise<void> => {
    try {
        const response = await fetch(`/policy-documents/${id}/content`);
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }

        const data = await response.text();
        documentContent.value = data;
        console.log("Document content loaded:", data);
    } catch (error) {
        console.error("Error loading document content:", error);
    }
};

// on highlight / selection of text inside content div, log the selected text
const contentBounds = useTemplateRef<HTMLDivElement>("content-bounds");

const highlights = ref<Highlight[]>([]);

const addHighlightsToContent = (): void => {
    let content = documentContent.value;

    highlights.value.sort((a, b) => b.end - a.end);

    console.log(highlights);

    highlights.value.forEach((highlight) => {
        const before = content.slice(0, highlight.start);
        const highlightedText = content.slice(highlight.start, highlight.end);
        const after = content.slice(highlight.end);
        content = `${before}<span style="background-color: ${highlight.color};">${highlightedText}</span><span data-offset="${highlight.end}">${after}</span>`;
    });

    formattedDocumentContent.value = content;

    console.log("highlight added", content);
};

const handleTextSelection = (): void => {
    const selection = window.getSelection();

    // if the selection is not inside the content div, return
    if (
        !contentBounds.value?.contains(selection?.anchorNode) ||
        !contentBounds.value?.contains(selection?.focusNode)
    ) {
        return;
    }

    const selectedText = selection?.toString();
    if (selectedText) {
        console.log("Selected text:", selectedText);

        // get start and end index of selection relative to content div
        currentSelection.value = selection.getRangeAt(0);

        if (!currentSelection.value.collapsed) {
            const offsetElement = currentSelection.value.startContainer
                .parentElement as HTMLElement;
            let offset = parseInt(offsetElement?.dataset.offset ?? "0");

            console.log("offset:", offset);

            console.log("Range is collapsed");
            console.log(
                "Start container:",
                currentSelection.value.startContainer
            );
            console.log("Start offset:", currentSelection.value.startOffset);
            console.log("End container:", currentSelection.value.endContainer);
            console.log("End offset:", currentSelection.value.endOffset);

            console.log(
                "Test. text from documentContent:",
                documentContent.value.substring(
                    currentSelection.value.startOffset,
                    currentSelection.value.endOffset
                )
            );

            showModal.value = true;
        }
    }
};

const currentSelection = ref<Range | null>(null);
const currentSelectedText = ref<string>("");

const confirmHighlight = async (): void => {
    if (currentSelection.value) {
        const start = currentSelection.value.startOffset;
        const end = currentSelection.value.endOffset;

        const offsetElement = currentSelection.value.startContainer
            .parentElement as HTMLElement;

        let offset = parseInt(offsetElement?.dataset.offset ?? "0");

        console.log("offset:", offset);

        highlights.value.push({
            start: start + offset,
            end: end + offset,
            color: "yellow",
        });

        // save to database
        const result = await saveHighlightToDatabase({
            documentId: documentId.value,
            text: currentSelection.value.toString(),
            start: start + offset,
            end: end + offset,
            color: "yellow",
        });

        addHighlightsToContent();

        // clear selection
        const selection = window.getSelection();
        selection?.removeAllRanges();

        showModal.value = false;

        console.log("Highlight confirmed from", start, "to", end);
        console.log("SHow modal:", showModal.value);
    }
};

const saveHighlightToDatabase = async (highlight: {
    documentId: number;
    text: string;
    start: number;
    end: number;
    color: string;
}): Promise<void> => {
    try {
        const response = await fetch(`/highlights`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(highlight),
        });

        if (!response.ok) {
            throw new Error("Network response was not ok");
        }

        console.log("Highlight saved to database:", highlight);
    } catch (error) {
        console.error("Error saving highlight to database:", error);
    }
}


onMounted(async (): Promise<void> => {
    await loadDocumentContent(documentId.value);

    addHighlightsToContent();

    window.addEventListener("mouseup", handleTextSelection);
});

// modal box that appears on text selection to add comments or tags to the selected text
const showModal = ref<boolean>(false);
</script>
