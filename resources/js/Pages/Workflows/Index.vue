<template>
  <AuthenticatedLayout>
    <Head title="Workflows" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="sm:flex sm:items-center sm:justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Workflows N8N</h1>
              <p class="mt-2 text-sm text-gray-700">
                Documentez et partagez vos workflows N8N avec votre équipe
              </p>
            </div>
            <div class="mt-4 sm:mt-0">
              <Link
                :href="route('workflows.create')"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                <PlusIcon class="w-4 h-4 mr-2" />
                Nouveau Workflow
              </Link>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-6 p-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <div>
              <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                Rechercher
              </label>
              <div class="relative">
                <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
                <input
                  id="search"
                  v-model="filters.search"
                  type="text"
                  placeholder="Titre, description..."
                  class="pl-10 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  @input="debouncedSearch"
                />
              </div>
            </div>

            <!-- Tags Filter -->
            <div>
              <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                Tags
              </label>
              <select
                id="tags"
                v-model="filters.tags"
                multiple
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
                <option v-for="tag in availableTags" :key="tag" :value="tag">
                  {{ tag }}
                </option>
              </select>
            </div>

            <!-- Sort -->
            <div>
              <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">
                Trier par
              </label>
              <select
                id="sort"
                v-model="sortBy"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                @change="handleSort"
              >
                <option value="created_at-desc">Plus récents</option>
                <option value="created_at-asc">Plus anciens</option>
                <option value="title-asc">Titre A-Z</option>
                <option value="title-desc">Titre Z-A</option>
                <option value="updated_at-desc">Dernière modification</option>
              </select>
            </div>
          </div>

          <!-- Active Filters -->
          <div v-if="hasActiveFilters" class="mt-4 flex flex-wrap gap-2">
            <span class="text-sm text-gray-500">Filtres actifs:</span>
            <span
              v-if="filters.search"
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
            >
              Recherche: "{{ filters.search }}"
              <button
                type="button"
                class="ml-1 inline-flex items-center p-0.5 rounded-full text-blue-400 hover:bg-blue-200 hover:text-blue-500"
                @click="clearSearch"
              >
                <XMarkIcon class="w-3 h-3" />
              </button>
            </span>
            <span
              v-for="tag in filters.tags"
              :key="tag"
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
            >
              Tag: {{ tag }}
              <button
                type="button"
                class="ml-1 inline-flex items-center p-0.5 rounded-full text-green-400 hover:bg-green-200 hover:text-green-500"
                @click="removeTag(tag)"
              >
                <XMarkIcon class="w-3 h-3" />
              </button>
            </span>
            <button
              type="button"
              class="text-sm text-gray-500 hover:text-gray-700"
              @click="clearAllFilters"
            >
              Effacer tous les filtres
            </button>
          </div>
        </div>

        <!-- Results -->
        <div v-if="workflows.length === 0" class="text-center py-12">
          <DocumentIcon class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun workflow</h3>
          <p class="mt-1 text-sm text-gray-500">
            {{ hasActiveFilters ? 'Aucun workflow ne correspond à vos critères.' : 'Commencez par créer votre premier workflow.' }}
          </p>
          <div class="mt-6">
            <Link
              v-if="!hasActiveFilters"
              :href="route('workflows.create')"
              class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
            >
              <PlusIcon class="w-4 h-4 mr-2" />
              Nouveau Workflow
            </Link>
          </div>
        </div>

        <!-- Workflows Grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <WorkflowCard
            v-for="workflow in workflows"
            :key="workflow.slug"
            :workflow="workflow"
            @deleted="handleWorkflowDeleted"
          />
        </div>

        <!-- Pagination -->
        <div v-if="pagination.last_page > 1" class="mt-8">
          <nav class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0">
            <div class="-mt-px flex w-0 flex-1">
              <Link
                v-if="pagination.current_page > 1"
                :href="route('workflows.index', { ...currentFilters, page: pagination.current_page - 1 })"
                class="inline-flex items-center border-t-2 border-transparent pt-4 pl-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700"
              >
                <ArrowLongLeftIcon class="mr-3 h-5 w-5 text-gray-400" />
                Précédent
              </Link>
            </div>
            <div class="hidden md:-mt-px md:flex">
              <span class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500">
                Page {{ pagination.current_page }} sur {{ pagination.last_page }}
              </span>
            </div>
            <div class="-mt-px w-0 flex-1 justify-end">
              <Link
                v-if="pagination.has_more"
                :href="route('workflows.index', { ...currentFilters, page: pagination.current_page + 1 })"
                class="inline-flex items-center border-t-2 border-transparent pt-4 pl-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700"
              >
                Suivant
                <ArrowLongRightIcon class="ml-3 h-5 w-5 text-gray-400" />
              </Link>
            </div>
          </nav>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import WorkflowCard from '@/Components/Workflows/WorkflowCard.vue'
import {
  PlusIcon,
  MagnifyingGlassIcon,
  DocumentIcon,
  XMarkIcon,
  ArrowLongLeftIcon,
  ArrowLongRightIcon
} from '@heroicons/vue/24/outline'
import { debounce } from 'lodash'

const props = defineProps({
  workflows: {
    type: Array,
    default: () => []
  },
  pagination: {
    type: Object,
    default: () => ({})
  },
  availableTags: {
    type: Array,
    default: () => []
  },
  filters: {
    type: Object,
    default: () => ({})
  }
})

const filters = ref({
  search: props.filters.search || '',
  tags: props.filters.tags || [],
  author: props.filters.author || ''
})

const sortBy = ref('created_at-desc')

const hasActiveFilters = computed(() => {
  return filters.value.search || filters.value.tags.length > 0 || filters.value.author
})

const currentFilters = computed(() => {
  const params = {}
  if (filters.value.search) params.search = filters.value.search
  if (filters.value.tags.length > 0) params.tags = filters.value.tags
  if (filters.value.author) params.author = filters.value.author
  
  const [sort_by, sort_direction] = sortBy.value.split('-')
  params.sort_by = sort_by
  params.sort_direction = sort_direction
  
  return params
})

const debouncedSearch = debounce(() => {
  applyFilters()
}, 300)

const applyFilters = () => {
  router.get(route('workflows.index'), currentFilters.value, {
    preserveState: true,
    replace: true
  })
}

const handleSort = () => {
  applyFilters()
}

const clearSearch = () => {
  filters.value.search = ''
  applyFilters()
}

const removeTag = (tag) => {
  filters.value.tags = filters.value.tags.filter(t => t !== tag)
  applyFilters()
}

const clearAllFilters = () => {
  filters.value = {
    search: '',
    tags: [],
    author: ''
  }
  sortBy.value = 'created_at-desc'
  applyFilters()
}

const handleWorkflowDeleted = () => {
  router.reload()
}

watch(() => filters.value.tags, () => {
  applyFilters()
}, { deep: true })
</script>