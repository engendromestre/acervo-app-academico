<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import AlertWarning from "@/Components/AlertWarning.vue";
import { useStore } from "vuex";
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    fields: Object,
    lang: Object,
    canLogin: Boolean,
    courses: Object,
    collections: Object,
    data: {
        type: Object,
        default: () => ({}),
    }
});

const store = useStore();

const setAction = async (id) => {
    const item = await axios.post(route('welcome.getVisit', id));
    store.commit('setItem', item.data);
    store.commit('toggleModal');
}

const incrementDocument = (id) => {
    const form = useForm({
        'id': id,
        '_method': 'patch',
        'canLogin': props.canLogin,
        'courses': props.courses,
        'collections': props.collections,
        'documents': props.data,
        'lang': props.lang,
        'fields': props.fields
    });

    form.patch(route("welcome.visitsIncrement"), {
        onError: (e) => {
            console.error(e);
        },
        onSuccess: (response) => {
            if (response.props.redirectTo) {
                Inertia.visit(response.props.redirectTo);
            }
        }
    });
}

const count = ref(1);
</script>
<template>
    <template v-if="Object.keys(data).length > 0">
        <div class="p-5">
            <fieldset class="border border-gray-500 text-gray-500 dark:text-gray-400 text-sm rounded-lg p-3">
                <legend>&nbsp;Resultados&nbsp;</legend>
                <template v-if="data['msg']">
                    <AlertWarning :message="data.msg" />
                </template>
                <template v-else>
                    <template v-for="obj, idx in data" :key="idx">
                        <div class="grid grid-rows-1 md:grid-rows-8 gap-4 mb-2.5" v-if="idx <= count">
                            <div
                                class="p-4 w-full bg-white rounded-lg border shadow-md dark:bg-gray-800 dark:border-gray-700">
                                <a class="text-base font-medium text-gray-900 dark:text-white">
                                    {{ obj.title }}
                                </a>

                                <a class="text-sm font-medium text-gray-900 dark:text-white">
                                    <template v-if="obj.subtitle">
                                        &nbsp;{{ obj.subtitle }}
                                    </template>
                                     - {{ obj.publicationYear }}
                                </a>
                                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400 text-ellipsis overflow-hidden">
                                    {{ obj.collection.name }} -
                                    {{ obj.course.name }} -
                                    {{ obj.advisor }} -
                                    {{ obj.author }}
                                </p>
                                <hr class="mt-3 h-px bg-gray-200 border-0 dark:bg-gray-700" />
                                <div class="mt-3 flex space-x-4 float-right">
                                    <a :title="obj.file.replace('documents/', '')" :href="obj.file"
                                        target="_blank">
                                        <button type="button" @click="incrementDocument(obj.id)"
                                            class="px-1 py-1 text-gray-900 bg-white hover:bg-gray-100 border border-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700">

                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                                </path>
                                            </svg>

                                        </button>
                                    </a>
                                    <button type="button" @click="setAction(obj.id)"
                                        class="px-1 py-1 text-gray-900 bg-white hover:bg-gray-100 border border-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div class="flex flex-col items-center">
                        <button type="button" @click="count++"
                            :disabled="count === Object.keys(data).length - 1 || count >= Object.keys(data).length"
                            :title="count === Object.keys(data).length - 1 || count >= Object.keys(data).length ? 'Todos os recursos mostrados' : 'Mostrar Mais'"
                            :class="count === Object.keys(data).length - 1 || count >= Object.keys(data).length
                                ? 'cursor-not-allowed px-1 py-1 text-gray-900 bg-white hover:bg-gray-100 border border-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700'
                                : 'px-1 py-1 text-gray-900 bg-white hover:bg-gray-100 border border-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700'">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                class="w-6 h-6"
                                :class="count === Object.keys(data).length - 1 || count >= Object.keys(data).length ? 'stroke-gray-200 dark:stroke-gray-700' : 'stroke-current'">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3" />
                            </svg>
                            <span class="sr-only">Show More</span>
                        </button>
                    </div>
                </template>
            </fieldset>
        </div>
    </template>
</template>

<style>
button {
    word-break: break-word;
}
</style>