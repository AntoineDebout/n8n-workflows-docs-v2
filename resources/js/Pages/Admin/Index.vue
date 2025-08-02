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
                        <!-- Filters and Actions -->
                        <div class="mb-4 flex justify-between items-center">
                            <div class="flex-1 pr-4">
                                <div class="relative">
                                    <input
                                        v-model="search"
                                        type="search"
                                        placeholder="Rechercher..."
                                        class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                    <div class="absolute left-3 top-3">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div v-if="hasAnyChanges()">
                                <button
                                    @click="saveAllChanges"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    Enregistrer les modifications
                                </button>
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
                                            <span v-if="sortColumn === column.key">
                                                {{ sortDirection === 'asc' ? '↑' : '↓' }}
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

const search = ref(props.filters.search);
const sortColumn = ref(props.filters.sort || 'name');
const sortDirection = ref(props.filters.direction || 'asc');

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

watch(search, debounce((value) => {
    router.get('/admin', { search: value, sort: sortColumn.value, direction: sortDirection.value }, {
        preserveState: true,
        preserveScroll: true,
    });
}, 300));

function sort(column) {
    if (sortColumn.value === column) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortColumn.value = column;
        sortDirection.value = 'asc';
    }

    router.get('/admin', {
        search: search.value,
        sort: sortColumn.value,
        direction: sortDirection.value
    }, {
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
</script>
