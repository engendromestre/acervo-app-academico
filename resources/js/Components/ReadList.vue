<script setup>
import { useStore } from "vuex";

const props = defineProps({
    fields: {
        type: Object,
        default: () => ({})
    },
    lang: {
        type: Object,
        default: () => ({})
    }
});

const store = useStore();
const list = [];
for (const [key, property] of Object.entries(props.fields)) {
    if (property['read']) {
        let obj = {};
        if (property['fixedValues']) {
            for (const [objKey, objProperty] of Object.entries(property['fixedValues'])) {
                let objPropertyField = Object.keys(objProperty).filter(
                    (propertyField) => {
                        if (propertyField != 'id')
                            return propertyField;
                    }
                );
                if (objProperty['id'] === store.getters.getItem[key]) {
                    obj[property['title']] = objProperty[ objPropertyField[0] ];
                }
            }
        } else {
            obj[property['title']] = store.getters.getItem[key];
        }
        list.push(obj);
    }
}
</script>
<template>
    <div class="overflow-hidden bg-white shadow sm:rounded-lg">
        <div class="listRecord border-t border-gray-200">
            <template v-for="obj, idx in list" :key="idx">
                <template v-for="objValue, ObjIdx in obj" :key="ObjIdx">
                    <dl>
                        <div class="px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
                            :class="idx % 2 == 0 ? 'bg-white' : 'bg-gray-100'">
                            <dt class="text-sm font-medium text-gray-500">{{ translate(ObjIdx) }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ objValue }}</dd>
                        </div>
                    </dl>
                </template>
            </template>
        </div>
    </div>
</template>

<style>
    .listRecord dl dd {
        word-break: break-word;
    }
</style>