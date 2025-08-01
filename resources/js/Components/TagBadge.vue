<script setup>
defineProps({
    tag: {
        type: String,
        required: true
    }
});
</script>

<template>
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
        {{ tag }}
    </span>
</template>
