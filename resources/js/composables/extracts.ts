import { scrollToSelection } from "@/composables/scrollHandler.ts";
import { onMounted, ref, type Ref, useTemplateRef, watch } from "vue";

import { findOffsetAncestor } from "@/composables/findOffsetAncestor.ts";
import axios from "axios";

export interface Extract {
    id?: number;
    policy_document_id: number;
    extract: string;
    start_offset: number;
    end_offset: number;
    color: string;
    search_terms: Array<SearchTerm>;
    search_terms_list: string;
    priority_actions: Array<string>;
    automatic?: boolean;
    verified?: boolean;
    score_id: number;
}

export interface SearchTerm {
    id: number;
    phrase: string;
    priority_action_id: string,
}

export function useExtracts(documentId: Ref<number, number>) {
    const extracts = ref<Extract[]>([]);
    const currentExtractId = ref<number | null>(null);
    const currentExtract: Ref<Extract> = ref<Extract>(null);
    const extractPriorityActions = ref<string[]>([]); // array of selected priority action IDs
    const extractTypeId = ref<number| null>(null); // default extract type ID

    const showModal = ref<boolean>(false);
    const showExtractsSidebar = ref<boolean>(false);

    /*********** READ EXTRACTS FROM DATABASE ***********/
    onMounted(async (): Promise<void> => {
        await loadExtracts(documentId.value);
    });

    const loadExtracts = async (id: number): Promise<void> => {
        try {
            const response = await fetch(`/policy-documents/${id}/extracts`);
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }

            const data = await response.json();
            extracts.value = data;
            console.log("Extracts loaded:", data);
        } catch (error) {
            console.error("Error loading extracts:", error);
        }
    };

    const confirmExtract = async (
        currentSelection: Range,
    ): Promise<boolean> => {
        console.log(currentSelection);

        if (!currentSelection) return false;
        const start = currentSelection.startOffset;
        const end = currentSelection.endOffset;
        const offset = findOffsetAncestor(currentSelection.startContainer);

        const newExtract: Extract = {
            policy_document_id: documentId.value,
            extract: currentSelection.toString(),
            start_offset: start + offset,
            end_offset: end + offset,
            color: "yellow",
            priority_actions: extractPriorityActions.value,
            search_terms_list: "",
            search_terms: [],
            score_id: extractTypeId.value
        };

        const newExtractWithId: Extract =
            await saveExtractToDatabase(newExtract);
        extracts.value.push(newExtractWithId);

        // resort extracts by start_offset
        extracts.value.sort((a, b) => a.start_offset - b.start_offset);

        console.log("new extract with ID", newExtractWithId);

        const selection = window.getSelection();
        selection?.removeAllRanges();
        return true;
    };

    const saveExtractToDatabase = async (
        extract: Extract,
    ): Promise<Extract> => {
        try {
            const result = await axios.post("/extracts", extract);
            return result.data;
        } catch (error) {
            console.error("Error saving extract to database:", error);
        }
    };

    const focusCurrentExtract = (): void => {
        console.log("focus current extract", currentExtractId.value);
        // remove previous current extract markers
        const prev = document.querySelectorAll(".extract-current");
        prev.forEach((el) => el.classList.remove("extract-current"));

        if (!currentExtractId.value) return;
        const sel = document.querySelector(
            `[data-extract-id="${currentExtractId.value}"]`,
        ) as HTMLElement | null;
        if (!sel) return;
        sel.classList.add("extract-current");

        if (!scrollToSelection(sel)) {
            // fallback: scroll the element into view normally
            sel.scrollIntoView({
                behavior: "smooth",
                block: "center",
            });
        }
    };

    const editExtract = (extractId: number) => {
        currentExtractId.value = extractId;

        showModal.value = true;
    };

    // add watcher to update currentExtract when Id changes
    watch(currentExtractId, (newId) => {
        if (newId === null) return;
        currentExtract.value =
            extracts.value.find((h) => h.id === newId) || null;

        if (currentExtract.value) {
            console.log(
                "updating priority actions form",
                currentExtract.value.priority_actions,
            );
            console.log("updating type id form", currentExtract.value.score_id)
            extractPriorityActions.value =
                currentExtract.value.priority_actions;
            extractTypeId.value = currentExtract.value.score_id;
        } else {
            extractPriorityActions.value = [];
            extractTypeId.value = null;
        }
    });

    const saveExtract = async (): Promise<boolean> => {
        const updatedExtract: Extract = {
            id: currentExtract.value.id,
            policy_document_id: documentId.value,
            extract: currentExtract.value.extract,
            start_offset: currentExtract.value.start_offset,
            end_offset: currentExtract.value.end_offset,
            color: 'yellow', // verified extract color
            search_terms: currentExtract.value.search_terms,
            search_terms_list: currentExtract.value.search_terms_list,
            priority_actions: extractPriorityActions.value,
            score_id: extractTypeId.value, // if the user is saving the extract, it is considered verified
            verified: true,
        };

        console.log(extractPriorityActions.value);

        currentExtract.value = updatedExtract;

        console.log("saving extract", currentExtract.value);

        // update in local extracts array
        const index = extracts.value.findIndex(
            (h) => h.id === currentExtract.value.id,
        );
        if (index !== -1) {
            extracts.value[index] = currentExtract.value;
        }

        // send update to server
        try {
            await axios.put(
                `/extracts/${currentExtract.value.id}`,
                updatedExtract,
            );
            console.log("Extract updated successfully");
        } catch (error) {
            console.error("Error updating extract:", error);
            return false;
        }

        const selection = window.getSelection();
        selection?.removeAllRanges();
        return true;
    };

    const deleteExtract = async (extractId: number): Promise<void> => {
        // confirm deletion with the user
        const confirmed = window.confirm(
            "Are you sure you want to delete this extract?",
        );
        if (!confirmed) return;

        try {
            await axios.delete(`/extracts/${extractId}`);
            // remove from local extracts array
            extracts.value = extracts.value.filter(
                (h) => h.id !== extractId,
            );

            console.log("Extract deleted successfully");
        } catch (error) {
            console.error("Error deleting extract:", error);
        }

        showModal.value = false;
    };

    return {
        extracts,
        currentExtract,
        currentExtractId,
        showModal,
        showExtractsSidebar,
        extractPriorityActions,
        extractTypeId,
        confirmExtract,
        focusCurrentExtract,
        editExtract,
        saveExtract,
        deleteExtract,
    };
}
