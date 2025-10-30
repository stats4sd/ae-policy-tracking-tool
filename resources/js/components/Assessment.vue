<template>
    <div class="px-2 py-2 h-screen">

        <div class="flex">

            <!--    sidebar -->
            <div class="flex-grow p-4 mr-8 h-full">
                <h2 class="text-2xl font-bold">
                    Document Content (ID: {{ documentId }})
                </h2>
                <div class="flex flex-col justify-start items-center">
                    <div
                        v-for="highlight in highlights"
                        :key="highlight.start_offset"
                        class="mb-2 w-full"
                    >
                        <div
                            :style="{ backgroundColor: highlight.color }"
                            class="p-2 rounded-md flex justify-between"
                        >
                            <div>

                                Highlight from {{ highlight.start_offset }} to
                                {{ highlight.end_offset }}
                            </div>

                            <!-- Delete icon -->
                            <div
                                class="cursor-pointer text-red-600 hover:text-red-800"
                                @click="highlights = highlights.filter(h => h !== highlight); addHighlightsToContent()"
                            >
                                &#10060;
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div>

                <div class="w-full" ref="contentAndSearch">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Full Text Search..."
                        class="w-full border border-gray-400 p-2 rounded-md mb-4"
                        @keydown.tab.prevent="nextSearch"
                        @keydown.shift.tab.prevent="prevSearch"
                    />
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

    highlights.value.push(newHighlight);
    await saveHighlightToDatabase(newHighlight);

    // re-render with new highlight + search highlights
    computeSearchMatches();

    const selection = window.getSelection();
    selection?.removeAllRanges();
    showModal.value = false;
};

const saveHighlightToDatabase = async (highlight: Highlight): Promise<void> => {
    try {
        await axios.post("/highlights", highlight);
    } catch (error) {
        console.error("Error saving highlight to database:", error);
    }
};


onMounted(async (): Promise<void> => {
    await loadDocumentContent(documentId.value);
    await loadHighlights(documentId.value);
    computeSearchMatches();
    window.addEventListener("mouseup", handleTextSelection);
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
const searchMatches = ref<{ start: number; end: number }[]>([]);
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
    const events: { pos: number; kind: "h_start" | "h_end" | "s_start" | "s_end"; id: number; color?: string }[] = [];

    // highlight events
    highlights.value.forEach((h, idx) => {
        events.push({ pos: h.start_offset, kind: "h_start", id: idx, color: h.color });
        events.push({ pos: h.end_offset, kind: "h_end", id: idx });
    });

    // search events
    searchMatches.value.forEach((m, idx) => {
        events.push({ pos: m.start, kind: "s_start", id: idx });
        events.push({ pos: m.end, kind: "s_end", id: idx });
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
            out += `<span class="doc-highlight" style="background-color: ${ev.color}">`;
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
    focusCurrentSearch();
};


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
        searchMatches.value.push({ start: match.index, end: match.index + match[0].length });
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

     // ensure the search bar (above the scrollable content) is visible in the viewport
    if (contentAndSearch.value) {
        contentAndSearch.value.scrollIntoView({ behavior: "smooth", block: "nearest" });
    }

    // if we have a scrollable content container, scroll it so `sel` is centered
    if (contentDiv.value && contentDiv.value.contains(sel)) {
        const container = contentDiv.value;
        const containerRect = container.getBoundingClientRect();
        const elRect = sel.getBoundingClientRect();

        // calculate element position relative to container scroll
        const relativeTop = elRect.top - containerRect.top + container.scrollTop;
        const desiredScrollTop = relativeTop - (container.clientHeight / 2) + (sel.clientHeight / 2);

        container.scrollTo({ top: desiredScrollTop, behavior: "smooth" });
        return;
    }

    // fallback: scroll the element into view normally
    sel.scrollIntoView({ behavior: "smooth", block: "center" });
};

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

// --- existing highlight/selection logic, updated to use findOffsetAncestor ---

const addHighlightsToContent = (): void => {
    // keep for backward compatibility but rely on renderContent for final output
    renderContent();
};


</script>


<style scoped>

</style>
