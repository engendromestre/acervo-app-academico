<script setup>
import { useStore } from "vuex";
import PaginateLink from './PaginateLink.vue';

const store = useStore();
const props = defineProps({
    links: {
        type: Object,
        default: () => ({}),
    }
});

</script>
<template>
    <nav aria-label="Paginate">
        <ul class="inline-flex items-center -space-x-px">
            <nav aria-label="Page navigation">
                <ul class="inline-flex items-center -space-x-px">
                    <li v-for="obj,key in links" :key="key">
                        <template v-if="key==0">
                            <PaginateLink :active="obj.active" :href="store.getters.getQuery && obj.url!==null ? obj.url+'&q='+store.getters.getQuery : obj.url"
                                class="block py-2 px-3 ml-0 leading-tight text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                <span class="sr-only">Previous</span>
                                <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </PaginateLink>
                        </template>

                        <template v-else-if="key==links.length-1">
                            <PaginateLink :active="obj.active" :href="store.getters.getQuery && obj.url!==null ? obj.url+'&q='+store.getters.getQuery : obj.url"
                                class="block py-2 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                <span class="sr-only">Next</span>
                                <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </PaginateLink>
                        </template>
                        <template v-else>
                            <PaginateLink :active="obj.active" :href="store.getters.getQuery ? obj.url+'&q='+store.getters.getQuery : obj.url">
                                {{obj.label}}   
                            </PaginateLink>
                        </template>
                    </li>
                </ul>
            </nav>
        </ul>
    </nav>
</template>