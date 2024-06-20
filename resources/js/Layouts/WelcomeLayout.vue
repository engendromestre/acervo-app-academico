<script setup>
import { Link } from '@inertiajs/inertia-vue3';
import { Disclosure, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { AcademicCapIcon } from "@heroicons/vue/24/outline";
import LightDarkButton from "@/Components/LightDarkButton.vue";

const props = defineProps({
    lang: Object,
    canLogin: Boolean
});

</script>
<template>
    <div class="min-h-screen dark:bg-gray-900">
        <Disclosure as="nav" class="" v-slot="{ open }">
            <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
                <div class="relative flex h-16 items-center justify-between">
                    <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
                        <a href="#" target="_blank">
                            <div class="flex flex-shrink-0 items-center">
                                <img class="block h-16 w-auto lg:hidden"
                                    src="@/Assets/images/logo.png"
                                    alt="Acervo Acadêmico" />
                                <img class="hidden h-16 w-auto lg:block"
                                    src="@/Assets/images/logo.png"
                                    alt="Acervo Acadêmico" />
                            </div>
                        </a>
                    </div>
                    <div v-if="canLogin"
                        class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
                        <!-- Profile dropdown -->
                        <Menu as="div" class="relative ml-3">
                            <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
                                <!-- LightDarkButton here -->
                                <LightDarkButton />
                                <MenuButton class="flex rounded-full pl-1.5">
                                    <span class="sr-only">Open user menu</span>
                                    <AcademicCapIcon class="h-6 w-6 rounded-full dark:text-white" aria-hidden="true" />
                                </MenuButton>
                            </div>
                            <transition enter-active-class="transition ease-out duration-100"
                                enter-from-class="transform opacity-0 scale-95"
                                enter-to-class="transform opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-75"
                                leave-from-class="transform opacity-100 scale-100"
                                leave-to-class="transform opacity-0 scale-95">
                                <MenuItems
                                    class="dark:bg-gray-700 absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                    <MenuItem v-slot="{ active }">
                                    <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                                        :class="[active ? 'bg-gray-100 ' : '', 'block px-4 py-2 text-sm text-gray-700']" class="dark:text-gray-400 dark:hover:bg-gray-800">
                                    Dashboard</Link>
                                    
                                    <Link v-else :href="route('login')"
                                        :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700']" class="dark:text-gray-400 dark:hover:bg-gray-800 ">
                                    Log in</Link>
                                    </MenuItem>
                                </MenuItems>
                            </transition>
                        </Menu>
                    </div>
                </div>
            </div>
        </Disclosure>

        <!-- Page Content -->
        <main>
            <slot />
        </main>
    </div>
</template>