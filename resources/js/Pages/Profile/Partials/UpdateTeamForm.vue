<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Gestion d'équipe
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Choisissez l'équipe à laquelle vous souhaitez appartenir.
            </p>
        </header>

        <form @submit.prevent="form.patch(route('profile.team.update'))" class="mt-6 space-y-6">
            <div>
                <InputLabel for="team" value="Équipe" />

                <select
                    id="team"
                    v-model="form.team_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option :value="null">Aucune équipe</option>
                    <option v-for="team in teams" :key="team.id" :value="team.id">
                        {{ team.name }}
                    </option>
                </select>

                <InputError class="mt-2" :message="form.errors.team_id" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Sauvegarder</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">
                        Sauvegardé.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>

<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    teams: {
        type: Array,
        required: true
    },
    currentTeam: {
        type: Object,
        required: false,
        default: null
    }
});

const form = useForm({
    team_id: props.currentTeam?.id || null
});
</script>
