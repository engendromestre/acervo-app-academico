<script setup>
import { ref, watch } from "vue";
import InputLabel from '@/Components/InputLabel.vue';
import Radio from '@/Components/Radio.vue';
import { useStore } from "vuex";

const props = defineProps({
    lang: Object,
    roles: {
        type: Object,
        default: () => ({}),
    },
    model: {
        type: Object,
        default: () => ({}),
    },
    form: Object
});

const store = useStore();

if(store.getters.getAction == 'update') {
    const currentModel = props.model.find(obj => obj.user == store.getters.getItem.id);
    store.state.item.role = currentModel.role;
    props.form.role = store.state.item.role;
} else {
    store.state.item.role = '';
}

let r = ref(props.form.role);
watch([r], ([role]) => {
    store.state.item.role = role;
});
</script>
<template>
    <div class="mt-4 mr-4">
        <InputLabel :value="translate('Role')"></InputLabel>
        <div class="grid grid-cols-1 gap-1">
            <template v-for="obj, idx in roles" :key="idx">
                <div class="flex items-center" v-if="obj.name!='super-admin'">
                    <Radio class="block w-4 h-4" :value="obj.name" name="role" 
                        v-model:checked="r"></Radio>
                    <InputLabel class="ml-2 text-sm font-medium" for="field" :value="obj.name"></InputLabel>
                </div>
            </template>
        </div>
    </div>
</template>