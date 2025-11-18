// helper: find nearest ancestor with data-offset (used by selection logic)
export function findOffsetAncestor(node: Node | null): number {
    let el = node && node.nodeType === Node.ELEMENT_NODE ? (node as Element) : (node && node.parentElement);
    while (el) {
        const v = (el as HTMLElement).dataset?.offset;
        if (v !== undefined) return parseInt(v || "0");
        el = el.parentElement;
    }
    return 0;
}
