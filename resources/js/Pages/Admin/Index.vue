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
                        <!-- Filters -->
                        <div class="mb-4 flex justify-between">
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
                                                v-model="user.team_id"
                                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                                @change="updateUserTeam(user)"
                                            >
                                                <option :value="null">Aucune équipe</option>
                                                <option v-for="team in teams" :key="team.id" :value="team.id">
                                                    {{ team.name }}
                                                </option>
                                            </select>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <select
                                                v-model="user.user_role_id"
                                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                                @change="updateUserRole(user)"
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
import { ref, watch } from 'vue';
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

const columns = [
    { key: 'name', label: 'Utilisateur' },
    { key: 'team', label: 'Équipe' },
    { key: 'role', label: 'Rôle' }
];

const search = ref(props.filters.search);
const sortColumn = ref(props.filters.sort || 'name');
const sortDirection = ref(props.filters.direction || 'asc');

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
        team_id: user.team_id
    }, {
        preserveScroll: true
    });
}

function updateUserRole(user) {
    router.patch(route('admin.users.role', user.id), {
        user_role_id: user.user_role_id
    }, {
        preserveScroll: true
    });
}
</script>
