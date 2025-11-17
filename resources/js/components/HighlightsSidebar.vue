<template>
    <div class="flex flex-col bg-bright-title-block   min-w-96 ">
        <div class="mb-4 flex items-center mt-4 text-white px-4 justify-between">
            <h5 class="text-lg font-semibold">
                Highlights
            </h5>
            <a
                href="#"
                @click="showHighlightsSidebar = !showHighlightsSidebar"
                class="text-sm  hover:underline cursor-pointer"
            >
                {{ showHighlightsSidebar ? 'Hide' : 'Show' }}
            </a>
        </div>

        <div
            class="flex flex-col bg-white justify-start items-center"
            :class="showHighlightsSidebar ? '' : 'hidden'"
        >
            <div
                v-for="highlight in highlights"
                :key="highlight.start_offset"
                class="mt-2 p-2 w-full cursor-pointer  hover:bg-[#e8e8e9]  bg-gray-50"
                @click="currentHighlightId = highlight.id; renderContent()"
            >
                <div class="p-2 rounded-md flex justify-between items-center ">
                    <div>
                        <strong>Highlight:</strong>
                        "{{ highlight.extract.length > 50 ? highlight.extract.slice(0, 50) + '...' : highlight.extract }}"
                    </div>
                    <div class="flex justify-end items-center gap-x-4">
                        <div>
                            <SlActionRedo
                                class="cursor-pointer text-gray-600 hover:text-gray-800"
                                @click.stop="currentHighlightId = highlight.id;"
                            />
                        </div>
                        <div>
                            <SlTrash
                                class="cursor-pointer text-red-600 hover:text-red-800 mr-4"
                                @click.stop="highlights = highlights.filter(h => h.id !== highlight.id);"
                            />
                        </div>
                    </div>
                </div>
                <div class="w-full justify-start items-center p-2">
                    <small class="text-gray-500">
                        TAGS GO HERE
                    </small>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>

import {ref} from 'vue';

const props = defineProps(
    {
        highlights: {
            type: Array,
            required: true
        },
    }
)

const currentHighlightId = defineModel('currentHighlightId');

const showHighlightsSidebar = ref(true);

</script>
