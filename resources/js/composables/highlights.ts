import { scrollToSelection } from "@/composables/scrollHandler.ts";
import { onMounted, ref, type Ref, useTemplateRef, watch } from "vue";

import { findOffsetAncestor } from "@/composables/findOffsetAncestor.ts";
import axios from "axios";

export interface Highlight {
    id?: number;
    policy_document_id: number;
    extract: string;
    start_offset: number;
    end_offset: number;
    color: string;
    priority_actions: Array<string>;
    automatic?: boolean;
    verified?: boolean;
}

export function useHighlights(documentId: Ref<number, number>) {
    const highlights = ref<Highlight[]>([]);
    const currentHighlightId = ref<number | null>(null);
    const currentHighlight: Ref<Highlight> = ref<Highlight>(null);
    const highlightPriorityActions = ref<string[]>([]); // array of selected priority action IDs

    const showModal = ref<boolean>(false);
    const showHighlightsSidebar = ref<boolean>(false);

    /*********** READ HIGHLIGHTS FROM DATABASE ***********/
    onMounted(async (): Promise<void> => {
        await loadHighlights(documentId.value);
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

    const confirmHighlight = async (
        currentSelection: Range,
    ): Promise<boolean> => {
        console.log(currentSelection);

        if (!currentSelection) return false;
        const start = currentSelection.startOffset;
        const end = currentSelection.endOffset;
        const offset = findOffsetAncestor(currentSelection.startContainer);

        const newHighlight: Highlight = {
            policy_document_id: documentId.value,
            extract: currentSelection.toString(),
            start_offset: start + offset,
            end_offset: end + offset,
            color: "yellow",
            priority_actions: highlightPriorityActions.value,
        };

        const newHighlightWithId: Highlight =
            await saveHighlightToDatabase(newHighlight);
        highlights.value.push(newHighlightWithId);

        // resort highlights by start_offset
        highlights.value.sort((a, b) => a.start_offset - b.start_offset);

        console.log("new highlight with ID", newHighlightWithId);

        const selection = window.getSelection();
        selection?.removeAllRanges();
        return true;
    };

    const saveHighlightToDatabase = async (
        highlight: Highlight,
    ): Promise<Highlight> => {
        try {
            const result = await axios.post("/highlights", highlight);
            return result.data;
        } catch (error) {
            console.error("Error saving highlight to database:", error);
        }
    };

    const focusCurrentHighlight = (): void => {
        console.log("focus current highlight", currentHighlightId.value);
        // remove previous current highlight markers
        const prev = document.querySelectorAll(".highlight-current");
        prev.forEach((el) => el.classList.remove("highlight-current"));

        if (!currentHighlightId.value) return;
        const sel = document.querySelector(
            `[data-highlight-id="${currentHighlightId.value}"]`,
        ) as HTMLElement | null;
        if (!sel) return;
        sel.classList.add("highlight-current");

        if (!scrollToSelection(sel)) {
            // fallback: scroll the element into view normally
            sel.scrollIntoView({
                behavior: "smooth",
                block: "center",
            });
        }
    };

    const editHighlight = (highlightId: number) => {
        currentHighlightId.value = highlightId;

        showModal.value = true;
    };

    // add watcher to update currentHighlight when Id changes
    watch(currentHighlightId, (newId) => {
        if (newId === null) return;
        currentHighlight.value =
            highlights.value.find((h) => h.id === newId) || null;

        if (currentHighlight.value) {
            console.log(
                "updating priority actions form",
                currentHighlight.value.priority_actions,
            );
            highlightPriorityActions.value =
                currentHighlight.value.priority_actions;
        } else {
            highlightPriorityActions.value = [];
        }
    });

    const saveHighlight = async (): Promise<boolean> => {
        const updatedHighlight: Highlight = {
            id: currentHighlight.value.id,
            policy_document_id: documentId.value,
            extract: currentHighlight.value.extract,
            start_offset: currentHighlight.value.start_offset,
            end_offset: currentHighlight.value.end_offset,
            color: 'yellow', // verified highlight color
            priority_actions: highlightPriorityActions.value,
            verified: true // if the user is saving the highlight, it is considered verified
        };

        console.log(highlightPriorityActions.value);

        currentHighlight.value = updatedHighlight;

        console.log("saving highlight", currentHighlight.value);

        // update in local highlights array
        const index = highlights.value.findIndex(
            (h) => h.id === currentHighlight.value.id,
        );
        if (index !== -1) {
            highlights.value[index] = currentHighlight.value;
        }

        // send update to server
        try {
            await axios.put(
                `/highlights/${currentHighlight.value.id}`,
                updatedHighlight,
            );
            console.log("Highlight updated successfully");
        } catch (error) {
            console.error("Error updating highlight:", error);
            return false;
        }

        const selection = window.getSelection();
        selection?.removeAllRanges();
        return true;
    };

    const deleteHighlight = async (highlightId: number): Promise<void> => {
        // confirm deletion with the user
        const confirmed = window.confirm(
            "Are you sure you want to delete this highlight?",
        );
        if (!confirmed) return;

        try {
            await axios.delete(`/highlights/${highlightId}`);
            // remove from local highlights array
            highlights.value = highlights.value.filter(
                (h) => h.id !== highlightId,
            );

            console.log("Highlight deleted successfully");
        } catch (error) {
            console.error("Error deleting highlight:", error);
        }

        showModal.value = false;
    };

    return {
        highlights,
        currentHighlight,
        currentHighlightId,
        showModal,
        showHighlightsSidebar,
        highlightPriorityActions,
        confirmHighlight,
        focusCurrentHighlight,
        editHighlight,
        saveHighlight,
        deleteHighlight,
    };
}
