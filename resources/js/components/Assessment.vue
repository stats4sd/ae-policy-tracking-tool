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
                <div class="flex flex-col bg-bright-title-block   min-w-96 ">
                    <div class="mb-4 flex items-center mt-4 text-white px-4 justify-between">
                        <h5 class="text-lg font-semibold">
                            Highlights
                        </h5>
                        <a
                            href="#"
                            @click="showHighlightsSidebar = !showHighlightsSidebar"
                            class="text-sm  hover:underline cursor-pointer"
                        >
                            {{ showHighlightsSidebar ? 'Hide' : 'Show' }}
                        </a>
                    </div>

                    <div
                        class="flex flex-col bg-white justify-start items-center"
                        :class="showHighlightsSidebar ? '' : 'hidden'"
                    >
                        <div
                            v-for="highlight in highlights"
                            :key="highlight.start_offset"
                            class="mt-2 p-2 w-full cursor-pointer  p-4 hover:bg-[#e8e8e9]  bg-gray-50"
                            @click="currentHighlightId = highlight.id; renderContent()"
                        >
                            <div class="p-2 rounded-md flex justify-between items-center ">
                                <div>
                                    <strong>Highlight:</strong>
                                    "{{ highlight.extract.length > 50 ? highlight.extract.slice(0, 50) + '...' : highlight.extract }}"
                                </div>
                                <div class="flex justify-end items-center gap-x-4">
                                    <div>
                                        <SlActionRedo
                                            class="cursor-pointer text-gray-600 hover:text-gray-800"
                                            @click.stop="currentHighlightId = highlight.id; renderContent()"
                                        />
                                    </div>
                                    <div>
                                        <SlTrash
                                            class="cursor-pointer text-red-600 hover:text-red-800 mr-4"
                                            @click.stop="highlights = highlights.filter(h => h.id !== highlight.id); renderContent()"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="w-full justify-start items-center p-2">
                                <small class="text-gray-500">
                                    TAGS GO HERE
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
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
                    <div ref="content-bounds" v-html="formattedDocumentContent"  />
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
                        @click="currentSelection = expandSelectionToWordBoundaries(currentSelection)"
                        class="px-4 py-2 bg-dark-title-block text-white font-bold rounded-lg hover:bg-[#5594b8]"
                    >
                        Expand Selection to full word(s)
                    </button>
                    <button
                        @click="currentSelection = expandSelectionToSentenceBoundaries(currentSelection)"
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
                    @click="confirmHighlight"
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
    useTemplateRef,
    watch
} from "vue";
import HighlightModal
    from "./HighlightModal.vue";
import axios
    from "axios";

import {
    SlActionRedo,
    SlTrash,
} from "vue-icons-plus/sl";

interface Highlight {
    id?: number;
    policy_document_id: number;
    extract: string;
    start_offset: number;
    end_offset: number;
    color: string;
}

interface Props {
    documentId: number;
}

const props = defineProps<Props>();

const documentId = ref<number>(props.documentId);
const documentContent = ref<string>("");
const formattedDocumentContent = ref<string>("");
const currentHighlightId = ref<number | null>(null);

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

const handleTextSelection = (): void => {
    const selection = window.getSelection();
    if (!selection) return;

    // if the selection is not inside the content div, return
    if (!contentBounds.value?.contains(selection.anchorNode) || !contentBounds.value?.contains(selection.focusNode)) {
        return;
    }

    const selectedText = selection.toString();
    if (selectedText) {
        currentSelection.value = selection.getRangeAt(0);
        if (!currentSelection.value.collapsed) {
            // find base offset from nearest ancestor
            const offset = findOffsetAncestor(currentSelection.value.startContainer);
            console.log("offset:", offset);
            showModal.value = true;
        }
    }
};

const currentSelection = ref<Range | null>(null);
const currentSelectedText = ref<string>("");

const confirmHighlight = async (): Promise<void> => {
    if (!currentSelection.value) return;
    const start = currentSelection.value.startOffset;
    const end = currentSelection.value.endOffset;
    const offset = findOffsetAncestor(currentSelection.value.startContainer);

    const newHighlight: Highlight = {
        policy_document_id: documentId.value,
        extract: currentSelection.value.toString(),
        start_offset: start + offset,
        end_offset: end + offset,
        color: "yellow",
    };

    const newHighlightWithId: Highlight = await saveHighlightToDatabase(newHighlight);
    highlights.value.push(newHighlightWithId);

    console.log('new highlight with ID', newHighlightWithId);

    // re-render with new highlight + search highlights
    // remove "current" search  and add "current" highlight
    currentSearchIndex.value = -1;
    currentHighlightId.value = newHighlightWithId.id;

    renderContent();

    const selection = window.getSelection();
    selection?.removeAllRanges();
    showModal.value = false;
};

const saveHighlightToDatabase = async (highlight: Highlight): Promise<Highlight> => {
    try {
        const result = await axios.post("/highlights", highlight);
        return result.data
    } catch (error) {
        console.error("Error saving highlight to database:", error);
    }
};


onMounted(async (): Promise<void> => {
    await loadDocumentContent(documentId.value);
    await loadHighlights(documentId.value);
    computeSearchMatches();
    window.addEventListener("mouseup", handleTextSelection);

    await loadRecommendations();


});

const loadHighlights = async (id: number): Promise<void> => {
    try {
        const response = await fetch(`/policy-documents/${id}/highlights`);
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }

        const data = await response.json();
        highlights.value = data;
        console.log("Highlights loaded:", data);
    } catch (error) {
        console.error("Error loading highlights:", error);
    }
};

// modal box that appears on text selection to add comments or tags to the selected text
const showModal = ref<boolean>(false);


// ************* Search Functionality *************

// Search state
const searchQuery = ref<string>("");
const searchMatches = ref<{
    start: number;
    end: number
}[]>([]);
const currentSearchIndex = ref<number>(-1);

const contentDiv = useTemplateRef<HTMLDivElement>("contentDiv");
const contentAndSearch = useTemplateRef<HTMLDivElement>("contentAndSearch");


// Utility: escape html
const escapeHtml = (s: string) =>
    s.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");


// watch searchQuery
watch(searchQuery, () => {
    computeSearchMatches();
});

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

const focusCurrentHighlight = (): void => {

    console.log('focus current highlight', currentHighlightId.value);
    // remove previous current highlight markers
    const prev = document.querySelectorAll(".highlight-current");
    prev.forEach((el) => el.classList.remove("highlight-current"));

    if (!currentHighlightId.value) return;
    const sel = document.querySelector(`[data-highlight-id="${currentHighlightId.value}"]`) as HTMLElement | null;
    if (!sel) return;
    sel.classList.add("highlight-current");

    if (!scrollToSelection(sel)) {
        // fallback: scroll the element into view normally
        sel.scrollIntoView({
            behavior: "smooth",
            block: "center"
        });
    }

}

const computeSearchMatches = (): void => {
    searchMatches.value = [];
    currentSearchIndex.value = -1;

    const q = searchQuery.value.trim();
    if (!q) {
        renderContent();
        return;
    }

    // escape special regex chars in query
    const escaped = q.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    const regex = new RegExp(escaped, "gi");
    let match;
    while ((match = regex.exec(documentContent.value)) !== null) {
        searchMatches.value.push({
            start: match.index,
            end: match.index + match[0].length
        });
        // avoid infinite loops on zero-length matches
        if (match.index === regex.lastIndex) regex.lastIndex++;
    }

    if (searchMatches.value.length > 0) currentSearchIndex.value = 0;
    renderContent();
};

const focusCurrentSearch = (): void => {

    console.log('focusCurrentSearch', currentSearchIndex.value);
    // remove previous current markers
    const prev = document.querySelectorAll(".search-current");
    prev.forEach((el) => el.classList.remove("search-current"));

    if (currentSearchIndex.value < 0) return;
    const sel = document.querySelector(`[data-search-index="${currentSearchIndex.value}"]`) as HTMLElement | null;
    if (!sel) return;
    sel.classList.add("search-current");

    if (!scrollToSelection(sel)) {
        // fallback: scroll the element into view normally
        sel.scrollIntoView({
            behavior: "smooth",
            block: "center"
        });
    }
};


const scrollToSelection = (sel: HTMLElement): boolean => {
    // ensure the search bar (above the scrollable content) is visible in the viewport
    if (contentAndSearch.value) {
        contentAndSearch.value.scrollIntoView({
            behavior: "smooth",
            block: "nearest"
        });
    }

    // if we have a scrollable content container, scroll it so `sel` is centered
    if (contentDiv.value && contentDiv.value.contains(sel)) {
        const container = contentDiv.value;
        const containerRect = container.getBoundingClientRect();
        const elRect = sel.getBoundingClientRect();

        // calculate element position relative to container scroll
        const relativeTop = elRect.top - containerRect.top + container.scrollTop;
        const desiredScrollTop = relativeTop - (container.clientHeight / 2) + (sel.clientHeight / 2);

        container.scrollTo({
            top: desiredScrollTop,
            behavior: "smooth"
        });
        return true;
    }

    return false;
}

// navigate search results
const nextSearch = (): void => {
    if (searchMatches.value.length === 0) return;
    currentSearchIndex.value = (currentSearchIndex.value + 1) % searchMatches.value.length;
    renderContent(); // re-render to update `search-current`
};

const prevSearch = (): void => {


    if (searchMatches.value.length === 0) return;

    // minus 2 because it triggers next-search first;
    currentSearchIndex.value = (currentSearchIndex.value - 2) % searchMatches.value.length;
    renderContent();
};

// helper: find nearest ancestor with data-offset (used by selection logic)
const findOffsetAncestor = (node: Node | null): number => {
    let el = node && node.nodeType === Node.ELEMENT_NODE ? (node as Element) : (node && node.parentElement);
    while (el) {
        const v = (el as HTMLElement).dataset?.offset;
        if (v !== undefined) return parseInt(v || "0");
        el = el.parentElement;
    }
    return 0;
};


const expandSelectionToWordBoundaries = (range: Range): Range => {

    // update selected range to word boundaries
    const isWordChar = (c: string) => /\w/.test(c);
    let startContainer = range.startContainer;
    let startOffset = range.startOffset;
    let endContainer = range.endContainer;
    let endOffset = range.endOffset;
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
    return newRange;

};

const expandSelectionToSentenceBoundaries = (range: Range): Range => {
    // update selected range to sentence boundaries
    const sentenceEndChars = [".", "!", "?"];
    let startContainer = range.startContainer;
    let startOffset = range.startOffset;
    let endContainer = range.endContainer;
    let endOffset = range.endOffset;
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
    return newRange;
}


/**** PRIORITY ACTIONS *****/

interface PriorityAction {
    id: number;
    name: string;
    recommendation_id: number;
}

interface Recommendation {
    id: number;
    short_title: string;
    name: string;
    priority_actions: PriorityAction[];
}

const recommendations = ref<Recommendation[]>([]);


const loadRecommendations = async (): Promise<void> => {
    try {
        const response = await fetch(`/recommendations`);
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }

        const data = await response.json();
        recommendations.value = data;
        console.log("Recommendations loaded:", data);
    } catch (error) {
        console.error("Error loading recommendations:", error);
    }
};

const showHighlightsSidebar = ref<boolean>(false);
const showRecommendationsSidebar = ref<boolean>(false);
</script>
