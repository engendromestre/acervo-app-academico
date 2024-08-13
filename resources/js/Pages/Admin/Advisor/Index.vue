<script setup>
import { computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/inertia-vue3";
import List from "@/Components/List.vue";
import RedButton from "@/Components/RedButton.vue";
import DefaultButton from "@/Components/DefaultButton.vue";
import GreenButton from "@/Components/GreenButton.vue";
import ReadList from "@/Components/ReadList.vue";
import CrudLayout from "@/Layouts/CrudLayout.vue";
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import BlueButton from '@/Components/BlueButton.vue';
import Modal from "@/Components/Modal.vue";
import { DialogTitle } from "@headlessui/vue";
import { PencilIcon, TrashIcon, EyeIcon, ServerIcon } from "@heroicons/vue/24/outline";
import { useForm } from "@inertiajs/inertia-vue3";
import { useStore } from "vuex";

const props = defineProps({
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
const HideModalState = () => {
    store.commit("hideModal");
    setTimeout(() => {
        props.flash.message = '';
        window.location.reload(true);
    }, 200);
};

function formFields(action) {
    const jsonFormFields = {};
    jsonFormFields['page'] = '1';
    Object.entries(props.fields).forEach((arr) => {
        if (arr[1][action]) {
            jsonFormFields[arr[0]] = arr[1]['initValue']
        }
    });
    return jsonFormFields;
}

function setValuesFormField(form) {
    Object.entries(store.getters.getItem).forEach((arr) => {
        if (typeof form[arr[0]] !== 'undefined') {
            form[arr[0]] = arr[1];
        }
    });
}

let form = useForm(formFields('create'));

const Submit = () => {
    form.page = store.getters.getPage;
    if (store.getters.getAction == 'create') {
        setValuesFormField(form);
        form.post(route("advisor.store"), {
            onSuccess: () => {
                if (form.hasErrors === false) {
                    setTimeout(() => { HideModalState() }, 1000);
                }
            }
        });
    }
    if (store.getters.getAction == 'update') {
         setValuesFormField(form);
         form.patch(route("advisor.update", store.getters.getItem.id), {
             onSuccess: () => {
                 if (form.hasErrors === false) {
                     setTimeout(() => { HideModalState() }, 1000);
                 }
             }
         });
    }
    if (store.getters.getAction == 'delete') {
        form.delete(route("advisor.destroy", store.getters.getItem.id), {
            onSuccess: () => {
                if (form.hasErrors === false) {
                    setTimeout(() => { HideModalState() }, 1000);
                }
            }
        });
    }
};

const breadcrumbs = computed(() => {
    return [
        { label: "Dashboard", href: "dashboard" },
        { label: "Advisors" },
    ];
});
</script>
<template>

    <Head :title="translate('Advisor')" />
    <AuthenticatedLayout :lang="lang" :breadcrumbs="breadcrumbs">
        <List :path="'/admin/advisor'" :fields="fields" :data="data" :can="can" :lang="lang" :flash="flash"
            :filters="filters" />
            <Modal>
            <template v-slot:content>
                <div class="sm:flex sm:items-start">
                    <template v-if="store.getters.getAction == 'read'">
                        <div
                            class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-gray-100 sm:mx-0 sm:h-10 sm:w-10">
                            <EyeIcon class="h-6 w-6 text-gray-700" aria-hidden="true" />
                        </div>
                    </template>
                    <template v-if="store.getters.getAction == 'create'">
                        <div
                            class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <ServerIcon class="h-6 w-6 text-blue-700" aria-hidden="true" />
                        </div>
                    </template>
                    <template v-if="store.getters.getAction == 'update'">
                        <div
                            class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <PencilIcon class="h-6 w-6 text-green-700" aria-hidden="true" />
                        </div>
                    </template>
                    <template v-if="store.getters.getAction == 'delete'">
                        <div
                            class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <TrashIcon class="h-6 w-6 text-red-600" aria-hidden="true" />
                        </div>
                    </template>
                    <template v-if="$page.props.flash.message">
                        <DialogTitle as="h3" class="mt-2 px-2 text-lg font-medium leading-6 dark:text-white" :class="store.getters.getAction == 'update' ? 'text-green-900' :
                        store.getters.getAction == 'delete' ? 'text-red-900' : 'text-blue-900'">
                            {{ translate($page.props.flash.message) }}
                        </DialogTitle>
                    </template>
                    <template v-else>
                        <DialogTitle as="h3" class="mt-2 px-2 text-lg font-medium leading-6 text-gray-900 dark:text-white">
                            {{ translate(store.getters.capitalizeAction) }} {{ translate('Advisor') }}
                        </DialogTitle>
                    </template>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <div class="mt-2">
                        <template v-if="store.getters.getAction == 'read'">
                            <CrudLayout>
                                <ReadList :fields="fields" :lang="lang"></ReadList>
                            </CrudLayout>
                        </template>
                        <template v-if="store.getters.getAction == 'update' || store.getters.getAction == 'create'">
                            <CrudLayout>
                                <div class="mt-4" v-for="property, field in fields" :key="field">
                                    <template
                                        v-if="property.dataType == 'text' && (property.update || property.create)">
                                        <div class="mt-2">
                                            <InputLabel class="" for="field" :value="translate(property.title)">
                                            </InputLabel>
                                            <TextInput :id="field" :type="property.dataType" class="mt-1 block w-full"
                                                :autofocus="property.position == 0" v-model="store.state.item[field]" />
                                            <InputError class="mt-2" :message="form.errors[field]" />
                                        </div>
                                    </template>
                                </div>
                            </CrudLayout>
                        </template>
                        <template v-if="store.getters.getAction == 'delete'">
                            <CrudLayout>
                                {{ translate('Are you sure you want to delete the record') }}
                                {{ store.getters.getItem.id }}
                                {{ "?" }}
                            </CrudLayout>
                        </template>
                    </div>
                </div>
            </template>
            <template v-slot:footer>
                <template v-if="store.getters.getAction == 'create'">
                    <BlueButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="Submit">
                        {{ translate(store.getters.capitalizeAction) }}
                    </BlueButton>
                </template>
                <template v-if="store.getters.getAction == 'update'">
                    <GreenButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="Submit">
                        {{ translate(store.getters.capitalizeAction) }}
                    </GreenButton>
                </template>
                <template v-if="store.getters.getAction == 'delete'">
                    <RedButton :type="'button'" @click="Submit">
                        {{ translate("Delete") }}
                    </RedButton>
                </template>

                <DefaultButton :type="'button'" class="mt-3" @click="HideModalState">
                    {{ translate("Cancel") }}
                </DefaultButton>
            </template>
        </Modal>
    </AuthenticatedLayout>
</template>