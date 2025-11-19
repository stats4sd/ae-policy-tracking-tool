<template>
    <div class="flex flex-col bg-bright-title-block min-w-96">
        <div
            class="mb-4 flex items-center mt-4 text-white px-4 justify-between"
        >
            <h5 class="text-lg font-semibold">Highlights</h5>
<!--            <a-->
<!--                href="#"-->
<!--                @click="showHighlightsSidebar = !showHighlightsSidebar"-->
<!--                class="text-sm hover:underline cursor-pointer"-->
<!--            >-->
<!--                {{ showHighlightsSidebar ? "Hide" : "Show" }}-->
<!--            </a>-->
        </div>

        <div
            class="flex flex-col bg-white justify-start items-center"
            :class="showHighlightsSidebar ? '' : 'hidden'"
        >
            <div
                v-for="highlight in highlights"
                :key="highlight.start_offset"
                class="mt-2 p-2 w-full cursor-pointer"
                :class="highlight.automatic ? 'hover:bg-blue-100 bg-blue-50' : `hover:bg-[#e8e8e9] bg-gray-50`"
                @click="currentHighlightId = highlight.id;"
            >
                <div
                    class="p-2 rounded-md flex justify-between items-center text-sm"
                >
                    <div>
                        <span v-if="highlight.automatic" class="text-blue-600 font-semibold"
                            >[AUTO]</span>
                        "{{
                            highlight.extract?.length > 50
                                ? highlight.extract.slice(0, 100) + "..."
                                : highlight.extract
                        }}"
                    </div>
                    <div class="flex justify-end items-center gap-x-4">
                        <button>
                            <SlPencil
                                class="cursor-pointer text-gray-600 hover:text-gray-800 w-8"
                                @click.stop="editHighlight(highlight.id)"
                            />
                        </button>
                        <button>
                            <SlTrash
                                class="cursor-pointer text-red-600 hover:text-red-800 mr-4"
                                @click.stop="deleteHighlight(highlight.id)"
                            />
                        </button>
                    </div>
                </div>
                <div class="w-full justify-start items-center p-2">
                    <small class="text-gray-500">
                        PRIORITY ACTIONS:
                        <span
                            v-for="priorityAction in highlight.priority_actions"
                            :key="priorityAction"
                            class="mr-2"
                        >
                            {{ priorityAction }}
                        </span>
                        <span
                            v-if="highlight.priority_actions.length === 0"
                            class="mr-2"
                        >
                            None
                        </span>
                    </small>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import { ref } from "vue";

import { SlTrash, SlPencil } from "vue-icons-plus/sl";

import { type Highlight } from "../composables/highlights";

const props = defineProps({
    highlights: {
        type: Array<Highlight>,
        required: true,
    },
});

const emit = defineEmits<{
    editHighlight: [];
    deleteHighlight: [];
}>();

const currentHighlightId = defineModel("currentHighlightId");

const showHighlightsSidebar = ref(true);

const editHighlight = (highlightId) => {
    emit("editHighlight", highlightId);
};

const deleteHighlight = (highlightId) => {
    console.log("deleting highlight with ID:", highlightId);
    // Emit an event or call a method to handle deletion
    emit("deleteHighlight", highlightId);
};
</script>
