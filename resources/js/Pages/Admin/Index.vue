<template>
    <Head title="Administration" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Administration des utilisateurs
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Actions -->
                        <div class="flex flex-col sm:flex-row justify-end gap-2 mb-6">
                            <button
                                @click="showCreateModal = true"
                                class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 h-10 whitespace-nowrap"
                            >
                                Ajouter un utilisateur
                            </button>
                            <button
                                v-if="hasAnyChanges()"
                                @click="saveAllChanges"
                                class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 h-10 whitespace-nowrap"
                            >
                                Enregistrer les modifications
                            </button>
                        </div>

                        <!-- Filters -->
                        <div class="mb-4 space-y-4">
                            <div class="relative">
                                <input
                                    v-model="filters.search"
                                    type="search"
                                    placeholder="Rechercher..."
                                    class="w-full pl-10 pr-4 py-2 h-10 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                <div class="absolute left-3 top-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                                <div class="sm:col-span-5">
                                    <select
                                        v-model="filters.team"
                                        class="block w-full h-10 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    >
                                        <option value="">Toutes les équipes</option>
                                        <option v-for="team in teams" :key="team.id" :value="team.id">
                                            {{ team.name }}
                                        </option>
                                    </select>
                                </div>

                                <div class="sm:col-span-5">
                                    <select
                                        v-model="filters.role"
                                        class="block w-full h-10 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    >
                                        <option value="">Tous les rôles</option>
                                        <option v-for="role in roles" :key="role.id" :value="role.id">
                                            {{ role.name }}
                                        </option>
                                    </select>
                                </div>

                                <div class="sm:col-span-2">
                                    <button
                                        @click="resetFilters"
                                        class="w-full inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 h-10"
                                        title="Réinitialiser les filtres"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            v-for="column in columns"
                                            :key="column.key"
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                            @click="sort(column.key)"
                                        >
                                            {{ column.label }}
                                            <span v-if="filters.sort === column.key">
                                                {{ filters.direction === 'asc' ? '↑' : '↓' }}
                                            </span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="user in users.data" :key="user.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                            <div class="text-sm text-gray-500">{{ user.email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <select
                                                v-model="ensureUserChanges(user).team_id"
                                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                            >
                                                <option :value="null">Aucune équipe</option>
                                                <option v-for="team in teams" :key="team.id" :value="team.id">
                                                    {{ team.name }}
                                                </option>
                                            </select>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <select
                                                v-model="ensureUserChanges(user).user_role_id"
                                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                            >
                                                <option v-for="role in roles" :key="role.id" :value="role.id">
                                                    {{ role.name }}
                                                </option>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            <Pagination :links="users.links" />
                        </div>

                        <!-- Create User Modal -->
                        <Modal :show="showCreateModal" @close="closeCreateModal">
                            <div class="p-6">
                                <div class="flex items-center mb-6">
                                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-medium text-gray-900">
                                            Ajouter un utilisateur
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Remplissez les informations ci-dessous pour créer un nouvel utilisateur.
                                        </p>
                                    </div>
                                </div>

                                <form @submit.prevent="createUser" class="space-y-6">
                                    <!-- Informations personnelles -->
                                    <div class="space-y-8">
                                        <div class="col-span-6 sm:col-span-4">
                                            <label for="name" class="block text-sm font-medium text-gray-900">Nom</label>
                                            <div class="mt-2 relative rounded-md shadow-sm">
                                                <input
                                                    id="name"
                                                    type="text"
                                                    v-model="form.name"
                                                    class="block w-full rounded-md border-gray-300 pr-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10"
                                                    required
                                                >
                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-span-6 sm:col-span-4">
                                            <label for="email" class="block text-sm font-medium text-gray-900">Email</label>
                                            <div class="mt-2 relative rounded-md shadow-sm">
                                                <input
                                                    id="email"
                                                    type="email"
                                                    v-model="form.email"
                                                    class="block w-full rounded-md border-gray-300 pr-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10"
                                                    required
                                                >
                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-span-6 sm:col-span-4">
                                            <label for="password" class="block text-sm font-medium text-gray-900">Mot de passe</label>
                                            <div class="mt-2 relative rounded-md shadow-sm">
                                                <input
                                                    id="password"
                                                    type="password"
                                                    v-model="form.password"
                                                    class="block w-full rounded-md border-gray-300 pr-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10"
                                                    required
                                                >
                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Permissions -->
                                    <div class="space-y-8">        
                                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                                            <div>
                                                <label for="team_id" class="block text-sm font-medium text-gray-900">Équipe</label>
                                                <select
                                                    id="team_id"
                                                    v-model="form.team_id"
                                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10"
                                                >
                                                    <option :value="null">Aucune équipe</option>
                                                    <option v-for="team in teams" :key="team.id" :value="team.id">
                                                        {{ team.name }}
                                                    </option>
                                                </select>
                                            </div>

                                            <div>
                                                <label for="user_role_id" class="block text-sm font-medium text-gray-900">Rôle</label>
                                                <select
                                                    id="user_role_id"
                                                    v-model="form.user_role_id"
                                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10"
                                                    required
                                                >
                                                    <option v-for="role in roles" :key="role.id" :value="role.id">
                                                        {{ role.name }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                                        <button
                                            type="button"
                                            class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:text-sm"
                                            @click="closeCreateModal"
                                        >
                                            Annuler
                                        </button>
                                        <button
                                            type="submit"
                                            class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:text-sm"
                                        >
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            Créer l'utilisateur
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </Modal>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    users: Object,
    teams: Array,
    roles: Array,
    filters: Object
});

// Store temporary changes
const userChanges = ref(
    props.users.data.reduce((acc, user) => {
        acc[user.id] = {
            team_id: user.team_id,
            user_role_id: user.user_role_id
        };
        return acc;
    }, {})
);

// Watch for new users data and update userChanges
watch(() => props.users.data, (newUsers) => {
    newUsers.forEach(user => {
        if (!userChanges.value[user.id]) {
            userChanges.value[user.id] = {
                team_id: user.team_id,
                user_role_id: user.user_role_id
            };
        }
    });
}, { deep: true });

const columns = [
    { key: 'name', label: 'Utilisateur' },
    { key: 'team', label: 'Équipe' },
    { key: 'role', label: 'Rôle' }
];

let unwatchFilters;

const filters = ref({
    search: props.filters.search || '',
    team: props.filters.team || '',
    role: props.filters.role || '',
    sort: props.filters.sort || 'name',
    direction: props.filters.direction || 'asc'
});

// Check if there are changes to save and the user exists in userChanges
function hasTeamChanges(user) {
    return userChanges.value[user.id] && userChanges.value[user.id].team_id !== user.team_id;
}

function hasRoleChanges(user) {
    return userChanges.value[user.id] && userChanges.value[user.id].user_role_id !== user.user_role_id;
}

// Check if any user has pending changes
function hasAnyChanges() {
    return props.users.data.some(user => 
        (userChanges.value[user.id]?.team_id !== user.team_id) ||
        (userChanges.value[user.id]?.user_role_id !== user.user_role_id)
    );
}

// Initialize user if not exists
function ensureUserChanges(user) {
    if (!userChanges.value[user.id]) {
        userChanges.value[user.id] = {
            team_id: user.team_id,
            user_role_id: user.user_role_id
        };
    }
    return userChanges.value[user.id];
}

unwatchFilters = watch(filters, debounce((value) => {
    router.get('/admin', value, {
        preserveState: true,
        preserveScroll: true,
    });
}, 300), { deep: true });

function sort(column) {
    if (filters.value.sort === column) {
        filters.value.direction = filters.value.direction === 'asc' ? 'desc' : 'asc';
    } else {
        filters.value.sort = column;
        filters.value.direction = 'asc';
    }

    router.get('/admin', filters.value, {
        preserveState: true,
        preserveScroll: true,
    });
}

function updateUserTeam(user) {
    router.patch(route('admin.users.team', user.id), {
        team_id: userChanges.value[user.id].team_id
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Update the original user data after successful save
            user.team_id = userChanges.value[user.id].team_id;
        }
    });
}

function updateUserRole(user) {
    router.patch(route('admin.users.role', user.id), {
        user_role_id: userChanges.value[user.id].user_role_id
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Update the original user data after successful save
            user.user_role_id = userChanges.value[user.id].user_role_id;
        }
    });
}

// Save all pending changes
async function saveAllChanges() {
    const changedUsers = props.users.data.filter(user => 
        (userChanges.value[user.id]?.team_id !== user.team_id) ||
        (userChanges.value[user.id]?.user_role_id !== user.user_role_id)
    );

    for (const user of changedUsers) {
        if (userChanges.value[user.id]?.team_id !== user.team_id) {
            await router.patch(route('admin.users.team', user.id), {
                team_id: userChanges.value[user.id].team_id
            }, {
                preserveScroll: true,
                onSuccess: () => {
                    user.team_id = userChanges.value[user.id].team_id;
                }
            });
        }
        
        if (userChanges.value[user.id]?.user_role_id !== user.user_role_id) {
            await router.patch(route('admin.users.role', user.id), {
                user_role_id: userChanges.value[user.id].user_role_id
            }, {
                preserveScroll: true,
                onSuccess: () => {
                    user.user_role_id = userChanges.value[user.id].user_role_id;
                }
            });
        }
    }
}

const showCreateModal = ref(false);
const form = ref({
    name: '',
    email: '',
    password: '',
    team_id: null,
    user_role_id: 1
});

function closeCreateModal() {
    showCreateModal.value = false;
    form.value = {
        name: '',
        email: '',
        password: '',
        team_id: null,
        user_role_id: 1
    };
}

function createUser() {
    router.post(route('admin.users.store'), form.value, {
        onSuccess: () => {
            closeCreateModal();
        },
    });
}

// Reset filters to initial values
function resetFilters() {
    // Désactiver temporairement le watcher pour éviter la navigation automatique
    unwatchFilters?.();
    
    filters.value.search = '';
    filters.value.team = '';
    filters.value.role = '';
    filters.value.sort = 'name';
    filters.value.direction = 'asc';

    // Navigation directe vers l'URL propre
    router.get('/admin', {}, {
        preserveState: true,
        preserveScroll: true,
    });

    // Réactiver le watcher après la navigation
    unwatchFilters = watch(filters, debounce((value) => {
        router.get('/admin', value, {
            preserveState: true,
            preserveScroll: true,
        });
    }, 300), { deep: true });
}
</script>
