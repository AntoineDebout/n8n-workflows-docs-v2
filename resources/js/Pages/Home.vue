<script setup>
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
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
                class="bg-white/80 backdrop-blur-sm p-6 rounded-lg shadow-lg border border-purple-100/50 hover:shadow-xl transition-all duration-200"
            >
                <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ workflow.title }}</h3>
                <p class="text-gray-600 mb-4">{{ workflow.description }}</p>
                <div class="flex flex-wrap gap-2">
                    <span 
                        v-for="tag in workflow.tags" 
                        :key="tag"
                        class="px-3 py-1 text-sm bg-purple-100 text-purple-700 rounded-full"
                    >
                        {{ tag }}
                    </span>
                </div>
                <div class="mt-4 text-sm text-gray-500">
                    Par {{ workflow.author }} · {{ workflow.created_at }}
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
