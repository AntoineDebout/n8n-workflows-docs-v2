<script setup>
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import MarkdownContent from '@/Components/MarkdownContent.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    workflows: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <GuestLayout>
        <Head title="N8N Documentation Manager" />

        <div class="text-center mb-8">
            <h1 class="text-3xl md:text-4xl font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                N8N Documentation Manager
            </h1>
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-100 to-purple-100 transform -skew-y-2 rounded-lg"></div>
                <p class="relative text-gray-700 text-sm md:text-base max-w-md mx-auto p-4">
                    Découvrez et explorez notre collection de workflows n8n publics. Connectez-vous pour créer et partager les vôtres.
                </p>
            </div>
        </div>

        <div class="flex justify-center gap-4 mb-8">
            <Link 
                :href="route('login')"
                class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-6 py-2 rounded-lg transition-all duration-200 transform hover:scale-105"
            >
                Se connecter
            </Link>
            <Link 
                :href="route('register')"
                class="bg-white text-gray-700 border border-gray-300 hover:border-gray-400 px-6 py-2 rounded-lg transition-all duration-200 transform hover:scale-105"
            >
                S'inscrire
            </Link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-4">
            <div 
                v-for="workflow in workflows" 
                :key="workflow.id"
                class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-purple-100/50 hover:shadow-xl transition-all duration-200 flex flex-col h-[280px]"
            >
                <!-- Card Content -->
                <div class="p-6 flex-1 overflow-hidden">
                    <h3 class="text-base font-medium text-gray-800 mb-2 line-clamp-1">{{ workflow.title }}</h3>
                    <div class="text-gray-600 mb-4 h-[60px] overflow-hidden">
                        <MarkdownContent :content="workflow.description" :truncate="true" />
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span 
                            v-for="tag in workflow.tags" 
                            :key="tag"
                            class="px-3 py-1 text-sm bg-purple-100 text-purple-700 rounded-full flex-shrink-0"
                        >
                            {{ tag }}
                        </span>
                    </div>
                </div>

                <!-- Fixed Footer -->
                <div class="p-6 pt-3 border-t border-gray-100 mt-auto bg-gray-50/50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500 flex-shrink-0">
                            Par {{ workflow.author }} · {{ workflow.created_at }}
                        </div>
                        <Link
                            :href="route('workflows.public.show', workflow.slug)"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition-all duration-200 flex-shrink-0"
                        >
                            <span>Voir</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
