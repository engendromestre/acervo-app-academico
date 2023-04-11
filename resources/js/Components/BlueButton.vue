<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <!-- inline-flex w-full justify-center rounded-md border border-transparent bg-blue-100 px-4 py-2 text-sm font-medium text-blue-900 hover:bg-blue-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 -->
    <button :type="type"
        class="inline-flex w-full justify-center rounded-md border border-transparent bg-blue-100 px-4 py-2 text-base font-medium text-blue-900 shadow-sm hover:bg-blue-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
        <slot />
    </button>
</template>