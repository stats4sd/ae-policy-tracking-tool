import {
    scrollToSelection
} from "@/composables/scrollHandler.ts";
import {
    onMounted,
    ref,
    type Ref,
    useTemplateRef
} from "vue";

import {
    findOffsetAncestor
} from "@/composables/findOffsetAncestor.ts";
import axios
    from "axios";

export function useHighlights(documentId: Ref<number, number>) {

    interface Highlight {
        id?: number;
        policy_document_id: number;
        extract: string;
        start_offset: number;
        end_offset: number;
        color: string;
    }

    const highlights = ref<Highlight[]>([]);

    const showModal = ref<boolean>(false);


    /*********** READ HIGHLIGHTS FROM DATABASE ***********/
    onMounted(async (): Promise<void> => {
        await loadHighlights(documentId.value);
    })

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




    const confirmHighlight = async (currentSelection: Range): Promise<void> => {

        console.log(currentSelection);

        if (!currentSelection) return;
        const start = currentSelection.startOffset;
        const end = currentSelection.endOffset;
        const offset = findOffsetAncestor(currentSelection.startContainer);

        const newHighlight: Highlight = {
            policy_document_id: documentId.value,
            extract: currentSelection.toString(),
            start_offset: start + offset,
            end_offset: end + offset,
            color: "yellow",
        };

        const newHighlightWithId: Highlight = await saveHighlightToDatabase(newHighlight);
        highlights.value.push(newHighlightWithId);

        console.log('new highlight with ID', newHighlightWithId);

        const selection = window.getSelection();
        selection?.removeAllRanges();

        // // re-render with new highlight + search highlights
        // // remove "current" search  and add "current" highlight
        // currentSearchIndex.value = -1;
        // currentHighlightId.value = newHighlightWithId.id;
        //
        // renderContent();
        //
        // showModal.value = false;
    };

    const saveHighlightToDatabase = async (highlight: Highlight): Promise<Highlight> => {
        try {
            const result = await axios.post("/highlights", highlight);
            return result.data
        } catch (error) {
            console.error("Error saving highlight to database:", error);
        }
    };


    const currentHighlightId = ref<number | null>(null);

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


    return {
        highlights,
        confirmHighlight,
        currentHighlightId,
        focusCurrentHighlight,
        showModal,
    }



}
