<template>
    <div class="flex flex-col bg-bright-title-block min-w-96">
        <div
            class="mb-4 flex items-center mt-4 text-white px-4 justify-between"
        >
            <div class="flex justify-between w-full">
                <h5 class="text-lg font-semibold">Extracts</h5>
                <div class="badge badge-info">
                    {{ extracts.length }} entries found
                </div>
            </div>
            <!--            <a-->
            <!--                href="#"-->
            <!--                @click="showExtractsSidebar = !showExtractsSidebar"-->
            <!--                class="text-sm hover:underline cursor-pointer"-->
            <!--            >-->
            <!--                {{ showExtractsSidebar ? "Hide" : "Show" }}-->
            <!--            </a>-->
        </div>

        <div
            class="flex flex-col bg-white justify-start items-center"
            :class="showExtractsSidebar ? '' : 'hidden'"
        >
            <div
                v-for="extract in extracts"
                :key="extract.start_offset"
                class="mt-2 p-0 w-full cursor-pointer"
                :class="
                    extract.automatic && !extract.verified
                        ? 'hover:bg-blue-100 bg-blue-50'
                        : `hover:bg-[#e8e8e9] bg-gray-50`
                "
                @click="currentExtractId = extract.id"
            >
                <div
                    class="grid grid-cols-8 justify-between items-center w-full h-full border border-blue-200"
                >
                    <div
                        class="col-span-6 rounded-md text-sm pl-2 py-2 pr-2 hover:bg-blue-100"
                    >
                        <div>
                            <span
                                v-if="extract.automatic && !extract.verified"
                                class="text-blue-600 font-semibold"
                                >[AUTO]</span
                            >
                            "{{
                                extract.extract?.length > 50
                                    ? extract.extract.slice(0, 100) + "..."
                                    : extract.extract
                            }}"
                        </div>
                        <small class="text-gray-500">
                            PRIORITY ACTIONS:
                            <span
                                v-for="priorityAction in extract.priority_actions"
                                :key="priorityAction"
                                class="mr-2"
                                :class="extract.type_id === 2 ? 'font-bold text-red-600' : (extract.type_id === 3 ? 'font-bold text-green-600' : 'font-bold text-blue-600')"
                            >
                                {{ priorityAction }}
                            </span>
                            <span
                                v-if="extract.priority_actions?.length == 0"
                                class="mr-2"
                            >
                                None
                            </span>
                        </small>
                    </div>

                    <div
                        class="h-full border-l-2 border-blue-200 hover:bg-blue-300 flex justify-center items-center"
                        @click.stop="editExtract(extract.id)"
                    >
                        <button>
                            <SlPencil
                                class="cursor-pointer text-gray-600 hover:text-blue-600 hover:font-bold"
                            />
                        </button>
                    </div>
                    <div
                        class="h-full border-l-2 border-blue-200 hover:bg-red-300 flex justify-center items-center"
                        @click.stop="deleteExtract(extract.id)"
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

import { type Extract } from "../composables/extracts";

const props = defineProps({
    extracts: {
        type: Array<Extract>,
        required: true,
    },
});

const emit = defineEmits<{
    editExtract: [];
    deleteExtract: [];
}>();

const currentExtractId = defineModel("currentExtractId");

const showExtractsSidebar = ref(true);

const editExtract = (extractId) => {
    emit("editExtract", extractId);
};

const deleteExtract = (extractId) => {
    console.log("deleting extract with ID:", extractId);
    // Emit an event or call a method to handle deletion
    emit("deleteExtract", extractId);
};


</script>
