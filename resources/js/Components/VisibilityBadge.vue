<script setup>
defineProps({
    visibility: {
        type: String,
        required: true,
        validator: value => ['public', 'private', 'team'].includes(value)
    }
});
</script>

<template>
    <span 
        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
        :class="{
            'bg-green-100 text-green-800': visibility === 'public',
            'bg-gray-100 text-gray-800': visibility === 'private',
            'bg-blue-100 text-blue-800': visibility === 'team'
        }"
    >
        {{ visibility === 'public' ? 'Public' : visibility === 'team' ? 'Équipe' : 'Privé' }}
    </span>
</template>
