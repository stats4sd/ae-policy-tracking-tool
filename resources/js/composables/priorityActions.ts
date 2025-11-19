import {
    ref
} from "vue";

export function usePriorityActions() {
    interface PriorityAction {
        id: string;
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

    const selectedPriorityActions = ref<string[]>([]); // array of selected priority action IDs

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


    const showRecommendationsSidebar = ref<boolean>(false);

    return {
        recommendations,
        loadRecommendations,
        showRecommendationsSidebar,
        selectedPriorityActions,
    }
}
