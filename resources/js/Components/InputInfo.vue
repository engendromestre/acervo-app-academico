<script setup>
defineProps(['message']);
</script>

<template>
    <div v-show="message">
        <p class="text-sm text-blue-200">
            {{ message }}
        </p>
    </div>
</template>