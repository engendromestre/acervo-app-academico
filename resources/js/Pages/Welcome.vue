<script setup>
import { ref, onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/inertia-vue3';
import { DialogTitle } from "@headlessui/vue";
import CrudLayout from "@/Layouts/CrudLayout.vue";
import ReadList from "@/Components/ReadList.vue";
import Checkbox from '@/Components/Checkbox.vue';
import InputLabel from '@/Components/InputLabel.vue';
import WelcomeLayout from "@/Layouts/WelcomeLayout.vue";
import ResultLayout from "@/Layouts/WelcomeResultLayout.vue";
import Modal from "@/Components/Modal.vue";
import Footer from "@/Components/Footer.vue";

const props = defineProps({
  fields: Object,
  lang: Object,
  canLogin: Boolean,
  canRegister: Boolean,
  laravelVersion: String,
  phpVersion: String,
  courses: Object,
  collections: Object,
  data: {
    type: Object,
    default: () => ({}),
  }

});

let form = useForm({
  'canLogin': props.canLogin,
  'courses': props.courses,
  'collections': props.collections,
  'lang': props.lang,
  'fields': props.fields,
  'q': '',
  'chkQuery': [],
  'selCourses': '',
  'selCollections': '',
  'selPublicationYear': ''
});

const emit = defineEmits(['update:data']);

const submit = () => {
  form.post(route('welcome'), {
    onSuccess: (res) => {
      emit("update:data", res.props.data);
    }
  });
};

const reset = () => {
  window.location.reload();
}

const q = ref(null);
const clearInputQ = () => {
  form.q = "";
  q.value.value = "";
  q.value.focus();
}

const optionsQuery = [
  { id: 'collections', label: 'Collections', checked: false, disabled: false },
  { id: 'courses', label: 'Courses', checked: false, disabled: false },
  { id: 'publicationYear', label: 'Publication Year', checked: false, disabled: false }
];

onMounted(() => {
  optionsQuery.map((oq) => {
    if (oq.checked)
      form.chkQuery.push(oq.id)
  });
});

const optCheckedShowHide = () => {
  let optionsChecked = Object.values(form.chkQuery);
  const allOptions = optionsQuery.map((oq) => oq.id);

  allOptions.forEach((chk) => {
    if (document.getElementById("opt" + chk[0].toUpperCase() + chk.slice(1))) {
      if (optionsChecked.includes(chk)) {
        document.getElementById("opt" + chk[0].toUpperCase() + chk.slice(1)).classList.remove('hide');
      } else {
        document.getElementById("opt" + chk[0].toUpperCase() + chk.slice(1)).classList.add('hide');
        form['sel' + chk[0].toUpperCase() + chk.slice(1)] = '';
      }
    }
  });
}
const lastYear = ref(new Date().getFullYear());
</script>
<template>

  <Head title="Acervo App" />
  <WelcomeLayout :lang="lang" :canLogin="canLogin">
    <div class="py-12 sm:py-8">
      <div class="mx-auto max-w-7xl px-4 mb-3">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
          <div class="col-start-1 col-span-6 md:col-start-2 md: col-span-3">
            <div class="relative w-full">
              <input type="text" id="floating_outlined" v-model="form.q" ref="q"
                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-200 focus:outline-none focus:ring-0 focus:border-blue-200 peer"
                placeholder=" " autofocus />
              <button type="button" class="flex absolute inset-y-0 right-0 items-center pr-3" title="Limpar"
                @click="clearInputQ">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                  stroke="currentColor"
                  class="w-6 h-6 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
              <label for="floating_outlined"
                class="absolute text-xs md:text-lg lg:text-lg text-gray-500 dark:text-gray-400 duration-300 
                transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-900 px-2 
                peer-focus:px-2 peer-focus:text-blue-900 dark:peer-focus:text-blue-200 peer-placeholder-shown:scale-100 
                peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 
                peer-focus:-translate-y-4 left-1">
                Pesquisar por: Autor, Orientador, Título ou Subtítulo
              </label>
            </div>
          </div>
          <div class="col-span-2">
            <button type="button" @click="submit"
              class="relative inline-flex items-center justify-center p-0.5 mb-2 mr-2 overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-cyan-500 to-blue-500 group-hover:from-cyan-500 group-hover:to-blue-500 hover:text-white dark:text-white focus:ring-4 focus:outline-none focus:ring-cyan-200 dark:focus:ring-cyan-800">
              <span
                class="relative px-5 py-2.5 transition-all ease-in duration-75 bg-white dark:bg-gray-900 rounded-md group-hover:bg-opacity-0">
                Pesquisar
              </span>
            </button>
            <button @click="reset"
              class="relative inline-flex items-center justify-center p-0.5 mb-2 mr-2 overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-red-200 via-red-300 to-yellow-200 group-hover:from-red-200 group-hover:via-red-300 group-hover:to-yellow-200 dark:text-white dark:hover:text-gray-900 focus:ring-4 focus:outline-none focus:ring-red-100 dark:focus:ring-red-400">
              <span
                class="relative px-5 py-2.5 transition-all ease-in duration-75 bg-white dark:bg-gray-900 rounded-md group-hover:bg-opacity-0">
                Limpar
              </span>
            </button>
          </div>
        </div>

        <div class="mx-auto max-w-7xl px-4 mb-3">
          <fieldset class="border border-gray-500 text-gray-500 dark:text-gray-400 text-sm rounded-lg p-3">
            <legend>&nbsp;Mais Opções de Consulta&nbsp;</legend>

            <div class="grid md:grid-cols-1 md:gap-6">
              <div class="flex">
                <template v-for="obj in optionsQuery">
                  <label class="flex items-center mr-4">
                    <Checkbox v-model:checked="form.chkQuery" :disabled="obj.disabled" :value="obj.id" @change="optCheckedShowHide()" 
                    class="w-4 h-4 rounded focus:ring-2 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600"
                    :class="obj.disabled 
                    ? 'text-blue-300 cursor-not-allowed' 
                    : 'text-blue-600'"  
                     />
                    <span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{
                      translate(obj.label)
                    }}</span>
                  </label>
                </template>

              </div>
            </div>

            <div class="grid md:grid-cols-3 md:gap-6 mt-2">

              <div id="optCollections" class="relative z-0 w-full mb-4 group hide">
                <InputLabel :value="translate('Tipo:')"
                  class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400" />
                <select
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  v-model="form.selCollections">
                  <option value="">Escolha um Item...</option>
                  <template v-for="obj in collections">
                    <option :value="obj.id">{{ obj.name }}</option>
                  </template>
                </select>
              </div>

              <div id="optCourses" class="relative z-0 w-full mb-4 group hide">
                <InputLabel :value="translate('Curso:')"
                  class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400" />
                <select
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  v-model="form.selCourses">
                  <option value="">Escolha um Item...</option>
                  <template v-for="obj in courses">
                    <option :value="obj.id">{{ obj.name }}</option>
                  </template>
                </select>
              </div>

              <div id="optPublicationYear" class="relative z-0 w-full mb-4 group hide">
                <InputLabel :value="translate('Ano de publicação:')"
                  class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400" />
                <input type="text" id="ano_publicacao" minlength="4" maxlength="4" :placeholder="'Ex:. ' + lastYear"
                  onkeypress="return event.charCode >= 48 && event.charCode <= 57" v-model="form.selPublicationYear"
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
              </div>

            </div>
          </fieldset>
        </div>
      </div>

      <div class="mx-auto max-w-7xl px-4 mb-3">
        <ResultLayout :data.sync="data" :lang="lang" :canLogin="canLogin" :courses="courses" :collections="collections" :fields="fields">
        </ResultLayout>
        <Modal :reloadPage="false">
          <template v-slot:content>
            <div class="sm:flex sm:items-start">
              <div
                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-gray-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                  stroke="currentColor" class="w-6 h-6">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                </svg>
              </div>
              <DialogTitle as="h3" class="mt-2 px-2 text-lg font-medium leading-6 text-gray-900 dark:text-white">
                Resultados</DialogTitle>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <div class="mt-2">
                <CrudLayout>
                  <ReadList :fields="fields" :lang="lang"></ReadList>
                </CrudLayout>
              </div>
            </div>
          </template>
        </Modal>
      </div>
    </div>
    <div class="mb-9"></div>
    <Footer />
  </WelcomeLayout>
</template>

<style>
.hide {
  display: none;
}
</style>