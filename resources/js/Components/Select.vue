<script setup>
import { ref, watch } from 'vue'
import { Listbox, ListboxButton, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid'
import { useStore } from "vuex";

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },
    field: String
});
const store = useStore();
const fields = Object.keys(props.data[0]);

let pos = 0;
if (store.state.action === 'update') {
    Object.entries(props.data).forEach((arr) => {
        if (arr[1].id === store.state.item[props.field]) {
            pos = arr[0];
        }
    });
}

store.state.item[props.field] = props.data[pos];
const selected = ref(props.data[pos]);

watch([selected], ([sel]) => {
    store.state.item[props.field] = sel;
});
</script>

<template v-if="fields">
    <Listbox as="div" v-model="selected">
        <div class="relative mt-1">
            <ListboxButton
                class="relative w-full cursor-default rounded-md border border-gray-300 bg-white py-2 pl-3 pr-10 text-left shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                <span class="flex items-center">
                    <img :src="selected.avatar" alt="" class="h-6 w-6 flex-shrink-0 rounded-full"
                        v-if="selected.avatar" />
                    <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                        </path>
                    </svg>
                    <span class="ml-3 block truncate" v-if="selected[fields[1]]">{{
        selected[fields[1]]
}}</span>
                </span>
                <span class="pointer-events-none absolute inset-y-0 right-0 ml-3 flex items-center pr-2">
                    <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                </span>
            </ListboxButton>

            <transition leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100"
                leave-to-class="opacity-0">
                <ListboxOptions
                    class="absolute z-10 mt-1 max-h-56 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                    <ListboxOption as="template" v-for="obj in data" :key="obj[fields[0]]" :value="obj"
                        v-slot="{ active, selected }">
                        <li
                            :class="[active ? 'text-white bg-indigo-600' : 'text-gray-900', 'relative cursor-default select-none py-2 pl-3 pr-9']">
                            <div class="flex items-center">
                                <img :src="obj.avatar" alt="" class="h-6 w-6 flex-shrink-0 rounded-full"
                                    v-if="obj.avatar" />
                                <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span :class="[selected ? 'font-semibold' : 'font-normal', 'ml-3 block truncate']">{{
        obj[fields[1]]
}}</span>
                            </div>

                            <span v-if="selected"
                                :class="[active ? 'text-white' : 'text-indigo-600', 'absolute inset-y-0 right-0 flex items-center pr-4']">
                                <CheckIcon class="h-5 w-5" aria-hidden="true" />
                            </span>
                        </li>
                    </ListboxOption>
                </ListboxOptions>
            </transition>
        </div>
    </Listbox>
</template>