<script setup>
import { ref, watch } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { PlusIcon, DocumentArrowDownIcon } from "@heroicons/vue/24/outline";
import Paginate from "@/Components/Paginate.vue";
import Table from "@/Components/Table.vue";
import TableToPDF from "@/Components/TableToPDF.vue";
import AlertWarning from "@/Components/AlertWarning.vue";
import { useStore } from "vuex";
import html2pdf from "html2pdf.js";

const props = defineProps({
    path: String,
    fields: {
        type: Object,
        default: () => ({}),
    },
    data: {
        type: Object,
        default: () => ({}),
    },
    can: {
        type: Object,
        default: () => ({}),
    },
    lang: Object,
    flash: Object,
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const store = useStore();
const setAction = (action, item, page) => {
    store.commit('toggleModal');
    store.commit('setAction', action);
    store.commit('setPage', page);
    store.commit('setItem', item);
}

let q = ref(props.filters.q);
watch([q], ([query]) => {
    Inertia.get(
        props.path,
        { q: query },
        {
            preserveState: true,
            replace: true,
        }
    );
    store.commit('setQuery', q);
});

const dataLength = props.data.data.length;


function exportToPDF() {
    const element = document.getElementById('printableArea');
    const options = {
        margin: 1,
        filename: 'meu-documento.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    // Temporariamente tornar a div visível para capturar o conteúdo
    element.style.visibility = 'visible';

    // Use html2pdf para gerar o PDF
    html2pdf().from(element).set(options).save().finally(() => {
        // Esconder a div novamente após a captura do conteúdo
        element.style.visibility = 'hidden';
    });
}
</script>
<template>
    <div class="py-12" ref="document">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-5">
            <div class="overflow-hidden">
                <div class="flex justify-end items=center p-5">
                    <div class="flex space-x-2 md:space-x-8 items-center">
                        <div class="relative z-0" v-if="dataLength > 0">
                            <input type="text" id="floating_standard"
                                class="block py-2.5 px-0 w-full text-sm text-gray-900
                                bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white
                                dark:border-gray-600 dark:focus:border-gray-500 focus:outline-none focus:ring-0 focus:border-gray-600 peer" placeholder=" "
                                v-model="q" autocomplete="off" />
                            <label for="floating_standard"
                                class="absolute text-sm text-gray-500 dark:text-gray-300 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-gray-600 peer-focus:dark:text-gray-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                {{ translate("Find") }}
                            </label>
                        </div>
                        <button v-if="can.create" type="button" :title="translate('Create Record')"
                            @click="setAction('create')"
                            class="inline-flex justify-center rounded-md border border-transparent bg-blue-100 px-4 py-2 text-sm font-medium text-blue-900 hover:bg-blue-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                            <span class="iconify mr-1" data-icon="gridicons:create" data-inline="false"></span>
                            <PlusIcon class="h-6 w-6 text-green-700" aria-hidden="true" />
                        </button>
                        <button type="button" :title="translate('Export to PDF')" @click="exportToPDF"
                            class="inline-flex justify-center rounded-md border border-transparent bg-red-100 px-4 py-2 text-sm font-medium text-red-900 hover:bg-red-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                            <span class="iconify mr-1" data-icon="gridicons:create" data-inline="false"></span>
                            <DocumentArrowDownIcon class="h-6 w-6 text-red-700" aria-hidden="true" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-2">
            <div class="overflow-hidden shadow-sm sm:rounded-lg" v-if="dataLength > 0">
                <Table :fields="fields" :items="data" :can="can" :lang="lang"></Table>
                <div class="overflow-hidden sm:rounded-lg text-end">
                    <div class="flex justify-end items=center p-5">
                        <Paginate :links="data.links"></Paginate>
                    </div>
                </div>
                <div id="printableArea">
                    <TableToPDF :fields="fields" :items="data" :can="can" :lang="lang" />
                </div>
            </div>
            <div class="p-16 bg-white h-48 overflow-hidden shadow-sm sm:rounded-lg" v-else>
                <AlertWarning :message="translate('No record found')" />
            </div>
        </div>
    </div>
</template>

<style scoped>
#printableArea {
    visibility: hidden;
    /* Esconde a div */
    background: white;
    padding: 20px;
}
</style>