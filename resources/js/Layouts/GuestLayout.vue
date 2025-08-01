<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 pt-6 pb-12"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div class="transform hover:scale-110 transition-transform duration-200">
                    <Link href="/">
                        <ApplicationLogo class="h-12 w-12 fill-current text-blue-600" />
                    </Link>
                </div>
                <div class="flex gap-4">
                    <Link
                        v-if="$page.url !== '/login'"
                        :href="route('login')"
                        class="text-gray-600 hover:text-gray-900"
                    >
                        Se connecter
                    </Link>
                    <Link
                        v-if="$page.url !== '/register'"
                        :href="route('register')"
                        class="text-gray-600 hover:text-gray-900"
                    >
                        S'inscrire
                    </Link>
                </div>
            </div>
        </div>

        <div
            :class="{
                'max-w-md': $page.url === '/login' || $page.url === '/register',
                'max-w-7xl': $page.url === '/'
            }"
            class="mt-6 mx-auto px-4 sm:px-6 lg:px-8"
        >
            <slot />
        </div>
    </div>
</template>
