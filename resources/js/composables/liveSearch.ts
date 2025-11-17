import {
    onMounted,
    ref,
    type Ref,
    toValue,
    useTemplateRef,
    watch
} from "vue";
import {
    scrollToSelection
} from "@/composables/scrollHandler.ts";

export function useLiveSearch(documentContent: Ref<string>) {

    const searchQuery: Ref<string, string> = ref<string>("");
    const searchMatches: Ref<{
        start: number,
        end: number
    }[]> = ref<{
        start: number;
        end: number
    }[]>([]);
    const currentSearchIndex: Ref<number, number> = ref<number>(-1);

    // initial computation of search matches
    onMounted(() => {
        computeSearchMatches()
    })

    // when searchQuery changes, recompute search matches
    watch(searchQuery, () => {
        computeSearchMatches();
    });


    // function to populate results into searchMatches
    const computeSearchMatches = (): void => {

        // reset previous search results
        searchMatches.value = [];
        currentSearchIndex.value = -1;

        // // if search query is empty, render original content
        const q = searchQuery.value.trim();
        if (!q) {
            //     renderContent();
            return;
        }

        // escape special regex chars in query
        const escaped = q.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
        const regex = new RegExp(escaped, "gi");

        let match: RegExpExecArray | null;

        // find all matches and store inside searchMatches
        while ((match = regex.exec(toValue(documentContent))) !== null) {
            searchMatches.value.push({
                start: match.index,
                end: match.index + match[0].length
            });

            // avoid infinite loops on zero-length matches
            if (match.index === regex.lastIndex) regex.lastIndex++;
        }

        if (searchMatches.value.length > 0) currentSearchIndex.value = 0;


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

    // navigate search results
    const nextSearch = (): void => {
        if (searchMatches.value.length === 0) return;
        currentSearchIndex.value = (currentSearchIndex.value + 1) % searchMatches.value.length;
        // renderContent(); // re-render to update `search-current`
    };

    const prevSearch = (): void => {


        if (searchMatches.value.length === 0) return;

        // minus 2 because it triggers next-search first;
        currentSearchIndex.value = (currentSearchIndex.value - 2) % searchMatches.value.length;
        // renderContent();
    };


    return {
        searchQuery,
        searchMatches,
        currentSearchIndex,
        focusCurrentSearch,
        nextSearch,
        prevSearch,
        computeSearchMatches
    }


}
