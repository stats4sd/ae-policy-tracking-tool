import {
    useTemplateRef
} from "vue";

const contentDiv = useTemplateRef<HTMLDivElement>("contentDiv");
const contentAndSearch = useTemplateRef<HTMLDivElement>("contentAndSearch");


export function scrollToSelection(sel: HTMLElement): boolean {
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
