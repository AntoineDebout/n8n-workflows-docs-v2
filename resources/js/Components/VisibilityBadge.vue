<script setup>
defineProps({
    visibility: {
        type: String,
        required: true,
        validator: value => ['public', 'private'].includes(value)
    }
});
</script>

<template>
    <span 
        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
        :class="{
            'bg-green-100 text-green-800': visibility === 'public',
            'bg-gray-100 text-gray-800': visibility === 'private'
        }"
    >
        {{ visibility === 'public' ? 'Public' : 'Privé' }}
    </span>
</template>
