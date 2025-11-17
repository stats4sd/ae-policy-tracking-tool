<template>
    <div class="px-2 py-2 h-screen">

        <div class="xl:flex">

            <!--    sidebar -->
            <div class="hidden xl:block flex-grow max-w-[45vw] mr-8 h-full">

                <!-- Recommendations / Priority Actions Filter -->
                <div class="flex flex-col bg-bright-title-block mb-2  min-w-96 ">
                    <div class=" flex items-center justify-between text-white py-4 px-4">
                        <h5 class="text-lg font-semibold ">
                            Filter by Recommendations / Priority Actions
                        </h5>
                        <a
                            href="#"
                            @click="showRecommendationsSidebar = !showRecommendationsSidebar"
                            class="text-sm  hover:underline cursor-pointer"
                        >
                            {{ showRecommendationsSidebar ? 'Hide' : 'Show' }}
                        </a>

                    </div>

                    <div
                        class="flex flex-col justify-start bg-white items-center"
                        :class="showRecommendationsSidebar ? '' : 'hidden'"
                    >
                        <div
                            v-for="recommendation in recommendations"
                            :key="recommendation.id"
                            class=" w-full cursor-pointer mt-2  p-4 hover:bg-[#e8e8e9]  bg-gray-50"
                        >
                            <div class="p-2 rounded-md flex justify-between items-center ">
                                <div>
                                    {{ recommendation.id }}: {{ recommendation.short_title }}
                                </div>
                            </div>
                            <div class="mx-12 mb-2 justify-start items-center border-l border-gray-400 pl-4">
                                <small class="text-gray-600">
                                    Priority Actions:
                                    <ul class="list-disc list-inside">
                                        <checkbox
                                            v-for="action in recommendation.priority_actions"
                                            :key="action.id"
                                        >
                                            {{ action.id }}: {{ action.name }}
                                        </checkbox>
                                    </ul>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Collapsible Highlights Card -->
                <HighlightsSidebar
                    :highlights="highlights"
                    v-model:currentHighlightId="currentHighlightId"
                />
            </div>
            <div class="">

                <div class="w-full flex items-center" ref="contentAndSearch">
                    <input
                        v-model.lazy="searchQuery"
                        type="text"
                        placeholder="Full Text Search..."
                        class="w-full bg-gray-100  border-0 px-4 py-2 rounded-xl mb-4 flex-grow-1"
                        @keydown.tab.prevent="nextSearch"
                        @keydown.shift.tab.prevent="prevSearch"
                    />
                    <button
                        @click="prevSearch"
                        class="theme_button mb-4 mx-2"
                    >
                        Previous
                    </button>
                    <button
                        @click="nextSearch"
                        class="theme_button mb-4"
                    >
                        Next
                    </button>
                </div>
                <div
                    class=" border border-gray-300 ps-12 p-4 rounded-md overflow-scroll h-[90vh]"
                    ref="contentDiv"
                >
                    <pre id="document_text">
                    <div ref="content-bounds" v-html="formattedDocumentContent"/>
                </pre>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal for text selection -->
    <HighlightModal
        v-if="showModal"
        title="Save Extracted Text"
        v-on:close="showModal = false"
    >
        <div class="w-full py-4 px-8 text-left rounded-md ">
            <h3 class="  font-bold">Current selection</h3>
            <div class="border-l-2 text-base border-black pl-6  mx-8 mt-6">
                {{
                    currentSelection ? currentSelection.toString() : ""
                }}"
            </div>
        </div>
        <div class="p-4">
            <div class="w-full bg-gray-100 p-4 rounded-md mt-4">
                <label class="block mb-4 font-semibold">Expand your selection</label>
                <ul class="list-disc list-inside text-sm text-gray-700">
                    <button
                        @click="expandSelectionToWordBoundaries"
                        class="px-4 py-2 bg-dark-title-block text-white font-bold rounded-lg hover:bg-[#5594b8]"
                    >
                        Expand Selection to full word(s)
                    </button>
                    <button
                        @click="expandSelectionToSentenceBoundaries"
                        class="px-4 py-2  ml-2 bg-dark-title-block text-white font-bold rounded-lg hover:bg-[#5594b8]"
                    >
                        Expand Selection to full sentence(s)
                    </button>
                </ul>

            </div>

            <div class="w-full bg-gray-100 p-4 rounded-md mt-4">
                <label class="block mb-4 font-semibold">Add Priority Actions to this highlight</label>
                <ul class="list-disc list-inside text-sm text-gray-700">
                    <li class="mb-2">(Not yet implemented) You can add comments or tags to this highlight after confirming.</li>
                </ul>
            </div>

            <div class="text-right mt-4">
                <button
                    @click="showModal = false"
                    class="px-4 py-2 theme_button mr-2 !bg-gray-500 hover:!bg-gray-400"
                >
                    Cancel
                </button>
                <button
                    @click="confirmHighlight(currentSelection); showModal = false"
                    class="mr-2 px-4 py-2 theme_button"
                >
                    Confirm
                </button>
            </div>
        </div>
    </HighlightModal>
</template>

<script setup lang="ts">
import {
    defineProps,
    onMounted,
    ref,
    watch
} from "vue";

import HighlightModal from "./HighlightModal.vue";
import { useHighlights } from "@/composables/highlights.ts";
import {useLiveSearch} from "@/composables/liveSearch.ts";
import {useTextSelection} from "@/composables/selectText.ts";
import {usePriorityActions} from "@/composables/priorityActions.ts";
import HighlightsSidebar from "@/components/HighlightsSidebar.vue";


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
    } catch (error) {
        console.error("Error loading document content:", error);
    }
};

onMounted(async (): Promise<void> => {
    await loadDocumentContent(documentId.value);
    await loadRecommendations();
});


const {
    highlights,
    confirmHighlight,
    currentHighlightId,
    focusCurrentHighlight,
    showModal,
    showHighlightsSidebar,
}
    = useHighlights(documentId)

const {
    searchQuery,
    searchMatches,
    currentSearchIndex,
    nextSearch,
    prevSearch,
    focusCurrentSearch
} = useLiveSearch(documentContent);

const {
    currentSelection,
    expandSelectionToWordBoundaries,
    expandSelectionToSentenceBoundaries
} = useTextSelection();

const {
    recommendations,
    loadRecommendations,
    showRecommendationsSidebar,
} = usePriorityActions();

// Utility: escape html
const escapeHtml = (s: string) =>
    s.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");

// Render combined HTML from documentContent, applying highlights and search spans.
// Every plain-text chunk is wrapped with a span[data-offset] so selection offset logic can locate base offset.
const renderContent = (): void => {
    const text = documentContent.value || "";
    const events: {
        pos: number;
        kind: "h_start" | "h_end" | "s_start" | "s_end";
        id: number;
        highlightId?: number | null;
        color?: string
    }[] = [];

    // highlight events
    highlights.value.forEach((h, idx) => {
        events.push({
            pos: h.start_offset,
            kind: "h_start",
            id: idx,
            highlightId: h.id || null,
            color: h.color
        });
        events.push({
            pos: h.end_offset,
            kind: "h_end",
            id: idx
        });
    });

    // search events
    searchMatches.value.forEach((m, idx) => {
        events.push({
            pos: m.start,
            kind: "s_start",
            id: idx
        });
        events.push({
            pos: m.end,
            kind: "s_end",
            id: idx
        });
    });

    // sort events: pos asc; when equal: start before end; for starts: highlight before search; for ends: search before highlight
    events.sort((a, b) => {
        if (a.pos !== b.pos) return a.pos - b.pos;
        const order = (e: typeof a) => {
            if (e.kind === "h_start") return 0;
            if (e.kind === "s_start") return 1;
            if (e.kind === "s_end") return 2;
            return 3; // h_end
        };
        return order(a) - order(b);
    });

    let out = "";
    let p = 0;
    const openStack: string[] = [];

    // helper to close tags in reverse order
    const closeTag = () => {
        const tag = openStack.pop();
        if (tag) out += "</span>";
    };

    for (let i = 0; i < events.length; i++) {
        const ev = events[i];
        if (ev.pos > p) {
            const chunk = text.slice(p, ev.pos);
            if (chunk.length > 0) {
                out += `<span data-offset="${p}">${escapeHtml(chunk)}</span>`;
            }
            p = ev.pos;
        }

        // handle event
        if (ev.kind === "h_start") {
            out += `<span class="doc-highlight" style="background-color: ${ev.color}" data-highlight-id="${ev.highlightId}">`;
            openStack.push("h");
        } else if (ev.kind === "s_start") {
            // differentiate current result visually
            const cls = ev.id === currentSearchIndex.value ? "search-result search-current" : "search-result";
            out += `<span class="${cls}" data-search-index="${ev.id}" style="background-color: rgba(173,216,230,0.6)">`;
            openStack.push("s");
        } else if (ev.kind === "s_end") {
            // close the most recent 's'
            // pop until we find 's'
            let popped = 0;
            while (openStack.length) {
                const top = openStack[openStack.length - 1];
                closeTag();
                popped++;
                if (top === "s") break;
            }
        } else if (ev.kind === "h_end") {
            // close the most recent 'h'
            while (openStack.length) {
                const top = openStack[openStack.length - 1];
                closeTag();
                if (top === "h") break;
            }
        }
    }

    // remaining tail
    if (p < text.length) {
        const tail = text.slice(p);
        out += `<span data-offset="${p}">${escapeHtml(tail)}</span>`;
    }

    // close any remaining open tags
    while (openStack.length) closeTag();

    formattedDocumentContent.value = out;

    // ensure current highlight is scrolled into view and visually marked

    console.log('renderContent: currentSearchIndex=', currentSearchIndex.value, 'currentHighlightId=', currentHighlightId.value);

    if (currentSearchIndex.value > -1) {
        focusCurrentSearch();
    } else if (currentHighlightId.value) {
        focusCurrentHighlight();
    }
};


// Re-render the content whenever:
// - document content changes
// - the highlights change
// - the search query or current search index changes

watch(
    [documentContent, highlights, searchMatches, currentSearchIndex, currentHighlightId],
    () => {
        renderContent();
    },
    {
        immediate: true,
        deep: true
    }
);

watch(
    [currentSelection],
    () => showModal.value = currentSelection.value !== null,
    {immediate: true}
)



</script>
