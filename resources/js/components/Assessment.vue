<template>
    <div class="px-2 py-2">
        <div class="xl:flex">
            <!--    sidebar -->
            <div class="hidden xl:block max-w-[35vw] mr-8 h-[80vh] overflow-scroll">
                <!-- Recommendations / Priority Actions Filter -->
                <div
                    class="flex flex-col mb-2 min-w-[350px]"
                    :class="
                        selectedPriorityActions.length > 0
                            ? 'border-2 border-yellow-400 bg-yellow-800'
                            : 'bg-bright-title-block'
                    "
                >
                    <div
                        class="flex items-center justify-between text-white py-4 px-4"
                    >
                        <h5 class="text-lg font-semibold">
                            Filter by Priority Actions
                        </h5>
                        <a
                            href="#"
                            @click="
                                showRecommendationsSidebar =
                                    !showRecommendationsSidebar
                            "
                            class="text-sm hover:underline cursor-pointer"
                        >
                            {{ showRecommendationsSidebar ? "Hide" : "Show" }}
                        </a>
                    </div>

                    <div
                        class="flex flex-col justify-start bg-white items-center"
                        :class="showRecommendationsSidebar ? '' : 'hidden'"
                    >
                        <div
                            v-for="recommendation in recommendations"
                            :key="recommendation.id"
                            class="w-full cursor-pointer mt-2 p-4 hover:bg-[#e8e8e9] bg-gray-50"
                        >
                            <div
                                class="p-2 rounded-md flex justify-between items-center"
                            >
                                <div>
                                    {{ recommendation.id }}:
                                    {{ recommendation.short_title }}
                                </div>
                            </div>
                            <div
                                class="mx-12 mb-2 justify-start items-center border-l border-gray-400 pl-4"
                            >
                                <small class="text-gray-600">
                                    <FormKit type="form" :actions="false">
                                        <FormKit
                                            type="checkbox"
                                            label=""
                                            :options="
                                                recommendation.priority_actions.map(
                                                    (action) => ({
                                                        label: action.code_and_short_name,
                                                        value: action.id,
                                                    }),
                                                )
                                            "
                                            v-model="selectedPriorityActions"
                                        />
                                    </FormKit>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter by Score -->
                <div
                    class="flex flex-col mb-4 min-w-[350px] bg-bright-title-block"
                >
                    <div
                        class="flex items-center justify-between text-white py-4 px-4"
                    >
                        <h5 class="text-lg font-semibold">
                            Filter by Extract Score
                        </h5>
                        <a
                            href="#"
                            @click="showTypeScoreSidebar = !showTypeScoreSidebar"
                            class="text-sm hover:underline cursor-pointer"
                        >
                            {{ showTypeScoreSidebar ? "Hide" : "Show" }}
                        </a>
                    </div>
                    <div
                        class="flex flex-col justify-start bg-white items-center"
                        :class="showTypeScoreSidebar ? '' : 'hidden'"
                    >
                        <div
                            class="w-full cursor-pointer mt-2 p-4 hover:bg-[#e8e8e9] bg-gray-50"
                        >
                            <div
                                class="p-2 rounded-md flex justify-between items-center"
                            >
                                <small class="text-gray-600">
                                    <FormKit type="form" :actions="false">
                                        <FormKit
                                            type="checkbox"
                                            label=""
                                            :options="
                                                types?.map(
                                                    (type) => ({
                                                        label: `( ${type.score} ) ${type.name}`,
                                                        value: type.id,
                                                    }),
                                                ) ?? []
                                            "
                                            v-model="selectedTypeScore"
                                        />
                                    </FormKit>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Collapsible Extracts Card -->
                <ExtractsSidebar
                    :extracts="filteredExtracts"
                    v-model:currentExtractId="currentExtractId"
                    @edit-extract="editExtract"
                    @delete-extract="deleteExtract"
                />
            </div>
            <div class="grow lg:max-w-[65vw]">
                <div class="w-full flex items-center" ref="contentAndSearch">
                    <input
                        v-model.lazy="searchQuery"
                        type="text"
                        placeholder="Full Text Search..."
                        class="w-full bg-gray-100 border-0 px-4 py-2 rounded-xl mb-4 grow-1"
                        @keydown.tab.prevent="nextSearch"
                        @keydown.shift.tab.prevent="prevSearch"
                    />
                    <button @click="prevSearch" class="theme_button mb-4 mx-2">
                        Previous
                    </button>
                    <button @click="nextSearch" class="theme_button mb-4">
                        Next
                    </button>
                </div>
                <div
                    v-if="selectedPriorityActions.length > 0"
                    class="flex items-center justify-center border-t border-gray-400"
                >
                    <div
                        class="bg-yellow-100 text-yellow-800 text-sm px-4 py-2 rounded-md my-2"
                    >
                        Filtering extracts by selected Priority Actions:
                        <b>{{ selectedPriorityActions.join(", ") }}</b>

                        <br />To clear the filter, deselect all Priority Actions
                        in the sidebar.
                    </div>
                </div>
                <div class="flex align-middle items-center w-full">
                    <div
                        class="border border-gray-300 ps-12 mx-auto rounded-md overflow-scroll h-[75vh]"
                        ref="contentDiv"
                    >
                        <div id="document_text">
                            <div ref="content-container" v-html="formattedDocumentContent"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for text selection -->
    <ExtractModal
        v-if="showModal"
        :title="currentExtract ? 'Edit Extract' : 'Save New Extract'"
        v-on:close="showModal = false"
    >
        <div class="w-full py-4 px-8 text-left rounded-md flex">
            <div class="grow">
                <h3 class="font-bold">
                    <span
                        v-if="
                            currentExtract?.automatic &&
                            currentExtract?.verified
                        "
                        class="text-blue-600"
                        >[Auto]</span
                    >
                    Selected Text
                </h3>
                <div class="border-l-2 text-base border-black pl-6 mx-8 mt-6">
                    "{{
                        currentExtract
                            ? currentExtract.extract
                            : currentSelection
                              ? currentSelection.toString()
                              : ""
                    }}"
                </div>
            </div>
            <div class="text-right py-4 flex align-top justify-start">
                <div class="flex flex-col space-y-2">
                    <button
                        @click="saveExtractEdits"
                        class="text-nowrap w-full text-xs mr-2 px-4 py-2 theme_button"
                    >
                        {{
                            currentExtract?.verified ||
                            !currentExtract?.automatic
                                ? "Save Extract"
                                : "Verify Extract"
                        }}
                    </button>
                    <button
                        @click="deleteExtract(currentExtract.id)"
                        class="text-nowrap w-full text-xs px-4 py-2 theme_button !bg-red-600 hover:!bg-red-500"
                    >
                        Delete Extract
                    </button>
                </div>
            </div>
        </div>
        <div class="p-4">
            <!-- Score assignment -->
            <div class="w-full bg-gray-100 p-4 rounded-md mt-4">
                <label class="block mb-2 font-semibold">Extract Score</label>
                <select
                    v-if="types"
                    v-model="extractTypeId"
                    class="w-full bg-white border border-gray-300 rounded-md px-4 py-2"
                >
                    <option :value="null">— No score assigned —</option>
                    <option
                        v-for="type in types"
                        :key="type.id"
                        :value="type.id"
                    >
                        ({{ type.score }}) {{ type.name }}
                    </option>
                </select>
                <p v-else class="text-sm text-gray-500">Loading scores…</p>
            </div>

            <div
                v-if="!currentExtract"
                class="w-full bg-gray-100 p-4 rounded-md mt-4"
            >
                <label class="block mb-4 font-semibold"
                    >Expand your selection</label
                >
                <ul class="list-disc list-inside text-sm text-gray-700">
                    <button
                        @click="expandSelectionToWordBoundaries"
                        class="px-4 py-2 bg-dark-title-block text-white font-bold rounded-lg hover:bg-[#5594b8]"
                    >
                        Expand Selection to full word(s)
                    </button>
                    <button
                        @click="expandSelectionToSentenceBoundaries"
                        class="px-4 py-2 ml-2 bg-dark-title-block text-white font-bold rounded-lg hover:bg-[#5594b8]"
                    >
                        Expand Selection to full sentence(s)
                    </button>
                </ul>
            </div>
            <div
                v-else-if="
                    currentExtract.automatic && !currentExtract.verified
                "
                class="w-full bg-gray-100 p-4 rounded-md mt-4"
            >
                This extract was automatically created from the search term(s): <b> {{ currentExtract.search_terms_list }}</b>. It will not appear in the
                final results until you confirm or delete it.
            </div>
            <div v-else class="w-full bg-gray-100 p-4 rounded-md mt-4">
                To change the selected text, please delete this extract and
                recreate it.
            </div>
            <!-- If no priority actions are selected, show all of them -->
            <div
                v-if="selectedPriorityActions.length === 0"
                class="w-full bg-gray-100 p-4 rounded-md mt-4 space-y-4"
            >
                <label class="block mb-4 font-semibold w-full"
                    >Add Priority Actions to this extract</label
                >

                <div
                    v-for="recommendation in recommendations"
                    class="p-4 bg-white rounded-md border border-gray-300"
                >
                    <h5>{{ recommendation.short_title }}</h5>

                    <div class="text-sm text-gray-700">
                        <FormKit type="form" :actions="false">
                            <FormKit
                                type="checkbox"
                                label=""
                                :options="
                                    recommendation.priority_actions.map(
                                        (action) => ({
                                            label:
                                                action.id + ': ' + action.name,
                                            value: action.id,
                                        }),
                                    )
                                "
                                v-model="extractPriorityActions"
                            />
                        </FormKit>
                    </div>
                </div>
            </div>

            <div v-else class="w-full bg-yellow-100 p-4 rounded-md mt-4">
                <span class="font-bold">This extract will be associated with the selected Priority
                Actions:</span>
                <ul class="list-none list-inside mt-2 text-center">
                    <li
                        v-for="actionId in selectedPriorityActions"
                        :key="actionId"
                    >
                        {{ actionId }}: {{ recommendations.find((rec) => rec.priority_actions.some((pa) => pa.id === actionId))?.priority_actions.find((pa) => pa.id === actionId)?.name }}
                    </li>
                </ul>
            </div>
        </div>
        <div class="text-right py-4">
            <button
                @click="showModal = false"
                class="px-4 py-2 theme_button mr-2 !bg-gray-500 hover:!bg-gray-400"
            >
                Cancel
            </button>
            <button
                @click="deleteExtract(currentExtract.id)"
                v-if="
                    currentExtract?.automatic && !currentExtract?.verified
                "
                class="mr-2 text-nowrap text-xs px-4 py-2 theme_button !bg-red-600 hover:!bg-red-500"
            >
                Delete Extract
            </button>
            <button
                @click="saveExtractEdits"
                class="text-nowrap text-xs mr-2 px-4 py-2 theme_button"
            >
                {{
                    currentExtract?.verified || !currentExtract?.automatic
                        ? "Save Extract"
                        : "Confirm Extract"
                }}
            </button>
        </div>
    </ExtractModal>
</template>

<script setup lang="ts">
import {
    defineProps,
    onMounted,
    type Ref,
    ref,
    type UnwrapRef,
    useTemplateRef,
    watch,
} from "vue";

import { marked } from "marked";

import ExtractModal from "./ExtractModal.vue";
import { useExtracts } from "@/composables/extracts.ts";
import { useLiveSearch } from "@/composables/liveSearch.ts";
import { useTextSelection } from "@/composables/selectText.ts";
import { usePriorityActions } from "@/composables/priorityActions.ts";
import ExtractsSidebar from "@/components/ExtractsSidebar.vue";

import { type Extract, type DocumentPage } from "@/composables/extracts.ts";

interface Props {
    documentId: number;
}

const props = defineProps<Props>();

const documentId = ref<number>(props.documentId);
const documentPages = ref<DocumentPage[]>([]);
const formattedDocumentContent = ref<string>("");

// get contentDiv element by ref
const contentContainer = useTemplateRef<HTMLDivElement>("content-container");

    // Load document pages from server
const loadDocumentPages = async (id: number): Promise<void> => {
    try {
        const response = await fetch(`/policy-documents/${id}/pages`);
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }

        documentPages.value = await response.json();
    } catch (error) {
        console.error("Error loading document pages:", error);
    }
};

onMounted(async (): Promise<void> => {
    await loadDocumentPages(documentId.value);
    await loadRecommendations();
    await loadTypes();

    updateFilteredExtracts();
    renderContent();
});

const {
    extracts,
    currentExtract,
    currentExtractId,
    showModal,
    extractPriorityActions,
    extractTypeId,
    confirmExtract,
    focusCurrentExtract,
    editExtract,
    saveExtract,
    deleteExtract,
} = useExtracts(documentId, contentContainer, documentPages);

const {
    searchQuery,
    searchMatches,
    currentSearchIndex,
    nextSearch,
    prevSearch,
    focusCurrentSearch,
} = useLiveSearch(documentPages);

const {
    currentSelection,
    expandSelectionToWordBoundaries,
    expandSelectionToSentenceBoundaries,
} = useTextSelection(contentContainer);

const {
    recommendations,
    loadRecommendations,
    showRecommendationsSidebar,
    selectedPriorityActions,
} = usePriorityActions();

watch(
    [currentSelection],
    () => {
        if (currentSelection.value) {
            showModal.value = true;
            currentExtract.value = null;
            currentExtractId.value = null;

            // if priority actions are selected for filtering, pre-fill the form
            if (selectedPriorityActions.value) {
                extractPriorityActions.value = [
                    ...selectedPriorityActions.value,
                ];
            } else {
                extractPriorityActions.value = [];
            }
        } else {
            // no selection
            showModal.value = false;
        }
    },
    { immediate: true },
);

const showTypeScoreSidebar = ref<boolean>(false);
const selectedTypeScore = ref<number[]>([]); // holds score record IDs (matches Extract.score_id)

// filter extracts by priority action
const filteredExtracts = ref<Extract[]>([]);

const updateFilteredExtracts = () => {
    filteredExtracts.value = extracts.value.filter((h) => {
        const matchesPriorityAction =
            selectedPriorityActions.value.length === 0 ||
            h.priority_actions.some((id) =>
                selectedPriorityActions.value.includes(id),
            );

        const matchesTypeScore =
            selectedTypeScore.value.length === 0 ||
            selectedTypeScore.value.includes(h.score_id);

        return matchesPriorityAction && matchesTypeScore;
    });
};

// Utility: escape html
const escapeHtml = (s: string) =>
    s.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");

// Track the global search match index across pages
let globalSearchIdx = 0;

// Render a single page's content with its extracts and search highlights
const renderPageContent = (page: DocumentPage): string => {
    const text = page.content || "";

    // Filter extracts and search matches for this page
    const pageExtracts = filteredExtracts.value.filter(h => h.page_number === page.page_number);
    const pageSearchMatches = searchMatches.value.filter(m => m.page_number === page.page_number);

    const events: {
        pos: number;
        kind: "h_start" | "h_end" | "s_start" | "s_end";
        id: number;
        extractId?: number | null;
        color?: string;
        globalSearchIndex?: number;
    }[] = [];

    // extract events
    pageExtracts.forEach((h, idx) => {
        events.push({
            pos: h.start_offset,
            kind: "h_start",
            id: idx,
            extractId: h.id || null,
            color: h.color,
        });
        events.push({
            pos: h.end_offset,
            kind: "h_end",
            id: idx,
        });
    });

    // search events — compute global index for each match on this page
    const pageSearchStartIdx = searchMatches.value.findIndex(
        m => m.page_number === page.page_number && m.start === pageSearchMatches[0]?.start
    );
    pageSearchMatches.forEach((m, idx) => {
        const globalIdx = pageSearchStartIdx >= 0 ? pageSearchStartIdx + idx : idx;
        events.push({
            pos: m.start,
            kind: "s_start",
            id: idx,
            globalSearchIndex: globalIdx,
        });
        events.push({
            pos: m.end,
            kind: "s_end",
            id: idx,
        });
    });

    // sort events
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

        if (ev.kind === "h_start") {
            out += `<span class="doc-extract cursor-pointer" style="background-color: ${ev.color}" data-extract-id="${ev.extractId}">`;
            openStack.push("h");
        } else if (ev.kind === "s_start") {
            const globalIdx = ev.globalSearchIndex ?? ev.id;
            const cls =
                globalIdx === currentSearchIndex.value
                    ? "search-result search-current"
                    : "search-result";
            out += `<span class="${cls}" data-search-index="${globalIdx}" style="background-color: rgba(173,216,230,0.6)">`;
            openStack.push("s");
        } else if (ev.kind === "s_end") {
            while (openStack.length) {
                const top = openStack[openStack.length - 1];
                closeTag();
                if (top === "s") break;
            }
        } else if (ev.kind === "h_end") {
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

    // Parse markdown per-page to avoid cross-page bleeding
    return marked(out) as string;
};

// Render combined HTML from all pages
const renderContent = (): void => {
    globalSearchIdx = 0;
    let fullHtml = "";

    for (const page of documentPages.value) {
        const pageHtml = renderPageContent(page);
        fullHtml += `<div class="page-separator text-center text-gray-400 text-sm py-2 my-4 border-t border-b border-gray-200">Page ${page.page_number}</div>`;
        fullHtml += `<div data-page="${page.page_number}" class="document-page">${pageHtml}</div>`;
    }

    formattedDocumentContent.value = fullHtml;

    console.log(
        "renderContent: currentSearchIndex=",
        currentSearchIndex.value,
        "currentExtractId=",
        currentExtractId.value,
    );

    if (currentSearchIndex.value > -1) {
        focusCurrentSearch();
    } else if (currentExtractId.value) {
        focusCurrentExtract();
    }

    // add event listeners to extracts for editing
    setTimeout(() => {
        const extractElements = document.querySelectorAll(".doc-extract");
        extractElements.forEach((el) => {
            el.addEventListener("click", (event) => {
                const extractId = el.getAttribute("data-extract-id");
                if (extractId) {
                    editExtract(parseInt(extractId, 10));
                }
            });
        });
    }, 0);
};

// Re-render the content whenever:
// - document pages change
// - the extracts change
// - the search query or current search index changes

watch(
    [
        documentPages,
        extracts,
        searchMatches,
        currentSearchIndex,
        currentExtractId,
    ],
    () => {
        renderContent();
    },
    {
        immediate: true,
        deep: true,
    },
);

watch(
    [extracts, selectedPriorityActions, selectedTypeScore],
    () => {
        updateFilteredExtracts();
        renderContent();
    },
    { immediate: true, deep: true },
);

const saveExtractEdits = async (): Promise<void> => {
    console.log("hi");
    let success = false;
    if (currentExtract.value) {
        // editing existing extract
        success = await saveExtract();
    } else if (currentSelection.value) {
        // creating new extract
        success = await confirmExtract(currentSelection.value);
    }

    if (success) {
        showModal.value = false;
    }
};

interface Score {
    id: number;
    score: number;
    name: string;
}

const types = ref<UnwrapRef<Score[]> | null>(null);

const loadTypes = async (): Promise<void> => {
    try {
        const response = await fetch(`/types`);
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }
        types.value = await response.json();
    } catch (error) {
        console.error("Error loading types:", error);
    }
};
</script>
