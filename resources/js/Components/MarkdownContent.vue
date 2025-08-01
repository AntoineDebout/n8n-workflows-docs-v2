<script setup>
import { marked } from 'marked';
import { computed } from 'vue';

const props = defineProps({
    content: {
        type: String,
        required: true
    },
    truncate: {
        type: Boolean,
        default: false
    }
});

const htmlContent = computed(() => {
    // Récupérer uniquement le premier paragraphe si truncate est true
    if (props.truncate) {
        const firstParagraph = props.content.split('\n\n')[0]
            .replace(/^#+ /, '') // Supprimer les titres Markdown
            .replace(/\n/g, ' '); // Remplacer les sauts de ligne par des espaces
        return marked(firstParagraph);
    }
    return marked(props.content);
});
</script>

<template>
    <div 
        class="prose prose-sm max-w-none"
        v-html="htmlContent"
    />
</template>
