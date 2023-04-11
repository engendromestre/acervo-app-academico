<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { useStore } from "vuex";

const props = defineProps({
    lang: Object,
    permissions: {
        type: Object,
        default: () => ({}),
    },
    roleHasPermissions: {
        type: Object,
        default: () => ({}),
    },
    form: Object
});

const store = useStore();

function setPermissions() {
    let currentPermissions = [];
    Object.entries(props.roleHasPermissions).forEach((arr) => {
        if(store.getters.getItem['id'] == arr[1]['role_id']) { 
            currentPermissions.push(arr[1]['permission_id']);
        }
    });
    let currentPermissionsName = [];
    Object.values(props.permissions).forEach((arr2) => {
       if(currentPermissions.includes(arr2['id'])) {
        currentPermissionsName.push(arr2['name']);
       }
    });
    props.form['permissions'] = currentPermissionsName;
}

setPermissions();
</script>
<template>
    <div class="mt-4 mr-4">
        <InputLabel class="" for="field" :value="translate('Permissions')"></InputLabel>
        <div class="grid grid-cols-2 gap-2">
            <template v-for="obj, idx in permissions" :key="idx">
                <div class="flex items-center">
                    <Checkbox :value="obj.name" class="block w-4 h-4" name="permissions"
                        v-model:checked="form.permissions" />
                    <InputLabel class="ml-2 text-sm font-medium" for="field" :value="obj.name">
                    </InputLabel>
                </div>
            </template>
        </div>
    </div>
</template>