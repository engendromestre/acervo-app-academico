<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { Link } from '@inertiajs/inertia-vue3';
import { useStore } from "vuex";

const props = defineProps({
    lang: Object
});

const breadcrumbs = computed(() => {
    return [ { label: 'Dashboard', href: 'dashboard' } ];
});

const menus = [
    {
        'href': 'user.index',
        'svg_d': 'M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z',
        'label': 'Register User'
    },
    {
        'href': 'role.index',
        'svg_d': 'M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z',
        'label': 'Register Role'
    },
    {
        'href': 'permission.index',
        'svg_d': 'M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z',
        'label': 'Register Permission'
    },
    {
        'href': 'collection.index',
        'svg_d': 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
        'label': 'Register Collection'
    },
    {
        'href': 'course.index',
        'svg_d': 'M6.429 9.75L2.25 12l4.179 2.25m0-4.5l5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0l4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0l-5.571 3-5.571-3',
        'label': 'Register Course'
    },
    {
        'href': 'document.index',
        'svg_d': 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
        'label': 'Register Document'
    },

];

const store = useStore();
const clearURLQueryString = () => {
    store.commit('setQuery', '');
}
</script>
<template>
    <Head :title="translate('Dashboard')" />

    <AuthenticatedLayout :lang="lang" :breadcrumbs="breadcrumbs">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ translate('Dashboard') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="justify-items-center">
                    <div
                        class="flex flex-col items-center justify-center space-y-6 dark:bg-gray-900 px-4 sm:flex-row sm:space-x-6 sm:space-y-0">
                        <template v-for="menu, idx in menus" :key="idx">
                            <Link :href="route(menu.href)" @click="clearURLQueryString"
                                class="h-48 w-full max-w-xs overflow-hidden rounded-lg 
                                    bg-white dark:bg-gray-700 dark:text-gray-400 shadow-md dark:shadow-gray-500 duration-300 hover:scale-105 hover:shadow-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="mx-auto mt-8 h-16 w-16">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="menu.svg_d" />
                            </svg>
                            <h1 class="px-1 my-8 mt-2 text-center text-2xl font-bold text-gray-500 dark:text-gray-500">
                                {{ translate(menu.label) }}
                            </h1>
                            </Link>
                        </template>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

  
