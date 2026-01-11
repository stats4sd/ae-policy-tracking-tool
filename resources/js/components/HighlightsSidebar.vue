<template>
    <div class="flex flex-col bg-bright-title-block min-w-96">
        <div
            class="mb-4 flex items-center mt-4 text-white px-4 justify-between"
        >
            <div class="flex justify-between w-full">
                <h5 class="text-lg font-semibold">Highlights</h5>
                <div class="badge badge-info">
                    {{ highlights.length }} entries found
                </div>
            </div>
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
                class="mt-2 p-0 w-full cursor-pointer"
                :class="
                    highlight.automatic && !highlight.verified
                        ? 'hover:bg-blue-100 bg-blue-50'
                        : `hover:bg-[#e8e8e9] bg-gray-50`
                "
                @click="currentHighlightId = highlight.id"
            >
                <div
                    class="grid grid-cols-8 justify-between items-center w-full h-full border border-blue-200"
                >
                    <div
                        class="col-span-6 rounded-md text-sm pl-2 py-2 pr-2 hover:bg-blue-100"
                    >
                        <div>
                            <span
                                v-if="highlight.automatic && !highlight.verified"
                                class="text-blue-600 font-semibold"
                                >[AUTO]</span
                            >
                            "{{
                                highlight.extract?.length > 50
                                    ? highlight.extract.slice(0, 100) + "..."
                                    : highlight.extract
                            }}"
                        </div>
                        <small class="text-gray-500">
                            PRIORITY ACTIONS:
                            <span
                                v-for="priorityAction in highlight.priority_actions"
                                :key="priorityAction"
                                class="mr-2"
                                :class="highlight.type_id === 2 ? 'font-bold text-red-600' : (highlight.type_id === 3 ? 'font-bold text-green-600' : 'font-bold text-blue-600')"
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

                    <div
                        class="h-full border-l-2 border-blue-200 hover:bg-blue-300 flex justify-center items-center"
                        @click.stop="editHighlight(highlight.id)"
                    >
                        <button>
                            <SlPencil
                                class="cursor-pointer text-gray-600 hover:text-blue-600 hover:font-bold"
                            />
                        </button>
                    </div>
                    <div
                        class="h-full border-l-2 border-blue-200 hover:bg-red-300 flex justify-center items-center"
                        @click.stop="deleteHighlight(highlight.id)"
                    >
                        <button>
                            <SlTrash
                                class="cursor-pointer text-red-500 hover:text-red-900 hover:font-bold hover:border-red-500"
                            />
                        </button>
                    </div>
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
