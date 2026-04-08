/**
 * Calculate the cumulative text offset of a DOM position within a container.
 * Walks all text nodes in document order from the container to the target node,
 * summing their lengths, then adds the offset within the target node.
 *
 * This is used to map a browser Range position (node + offset) to a flat
 * character offset within the container's visible text content.
 */
export function getTextOffset(container: Node, targetNode: Node, offsetInNode: number): number {
    let cumulative = 0;
    const walker = document.createTreeWalker(container, NodeFilter.SHOW_TEXT);
    let node: Node | null;
    while ((node = walker.nextNode())) {
        if (node === targetNode) {
            return cumulative + offsetInNode;
        }
        cumulative += node.textContent?.length || 0;
    }
    return cumulative + offsetInNode;
}
