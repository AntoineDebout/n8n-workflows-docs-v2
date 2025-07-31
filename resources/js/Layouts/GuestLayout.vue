<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gradient-to-br from-blue-50 via-white to-purple-50 pt-6 sm:justify-center sm:pt-0"
    >
        <div class="transform hover:scale-110 transition-transform duration-200">
            <Link href="/">
                <ApplicationLogo class="h-20 w-20 fill-current text-blue-600" />
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white/80 backdrop-blur-sm px-6 py-4 shadow-lg sm:max-w-md sm:rounded-lg border border-purple-100/50"
        >
            <slot />
        </div>
    </div>
</template>
