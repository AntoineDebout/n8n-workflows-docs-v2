<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
    <!-- Header with visibility indicator -->
    <div class="p-6 pb-4">
      <div class="flex items-start justify-between">
        <div class="flex-1">
          <div class="flex items-center gap-2 mb-2">
            <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">
              {{ workflow.title }}
            </h3>
            <VisibilityBadge :visibility="workflow.visibility" />
          </div>
          
          <p v-if="workflow.descriptionExcerpt" class="text-gray-600 text-sm line-clamp-3 mb-4">
            {{ workflow.descriptionExcerpt }}
          </p>
        </div>
      </div>

      <!-- Tags -->
      <div v-if="workflow.tags.length > 0" class="flex flex-wrap gap-1 mb-4">
        <TagBadge
          v-for="tag in workflow.tags.slice(0, 3)"
          :key="tag"
          :tag="tag"
        />
        <span
          v-if="workflow.tags.length > 3"
          class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-600"
        >
          +{{ workflow.tags.length - 3 }}
        </span>
      </div>

      <!-- Metadata -->
      <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
        <div class="flex items-center gap-4">
          <span class="flex items-center gap-1">
            <UserIcon class="w-4 h-4" />
            {{ workflow.authorName }}
          </span>
          <span class="flex items-center gap-1">
            <ClockIcon class="w-4 h-4" />
            {{ workflow.updatedAt }}
          </span>
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <Link
          :href="route('workflows.show', workflow.slug)"
          class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-md transition-colors duration-150"
        >
          <EyeIcon class="w-4 h-4 mr-1" />
          Voir
        </Link>
        
        <Link
          v-if="workflow.canEdit"
          :href="route('workflows.edit', workflow.slug)"
          class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-600 hover:text-gray-700 hover:bg-gray-100 rounded-md transition-colors duration-150"
        >
          <PencilIcon class="w-4 h-4 mr-1" />
          Éditer
        </Link>
      </div>

      <!-- Delete button -->
      <button
        v-if="workflow.canDelete"
        type="button"
        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-md transition-colors duration-150"
        @click="confirmDelete"
      >
        <TrashIcon class="w-4 h-4 mr-1" />
        Supprimer
      </button>
    </div>

    <!-- Delete Confirmation Modal -->
    <Modal :show="showDeleteModal" @close="showDeleteModal = false">
      <div class="p-6">
        <div class="flex items-center mb-4">
          <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
            <ExclamationTriangleIcon class="h-6 w-6 text-red-600" />
          </div>
          <div class="ml-4">
            <h3 class="text-lg font-medium text-gray-900">
              Supprimer le workflow
            </h3>
            <p class="mt-2 text-sm text-gray-500">
              Êtes-vous sûr de vouloir supprimer "{{ workflow.title }}" ? Cette action est irréversible.
            </p>
          </div>
        </div>
        
        <div class="flex justify-end gap-3">
          <button
            type="button"
            class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:text-sm"
            @click="showDeleteModal = false"
          >
            Annuler
          </button>
          <button
            type="button"
            class="inline-flex justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:text-sm"
            :disabled="isDeleting"
            @click="deleteWorkflow"
          >
            <span v-if="isDeleting">Suppression...</span>
            <span v-else>Supprimer</span>
          </button>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import TagBadge from '@/Components/Workflows/TagBadge.vue'
import VisibilityBadge from '@/Components/Workflows/VisibilityBadge.vue'
import {
  EyeIcon,
  PencilIcon,
  TrashIcon,
  UserIcon,
  ClockIcon,
  ExclamationTriangleIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  workflow: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['deleted'])

const showDeleteModal = ref(false)
const isDeleting = ref(false)

const confirmDelete = () => {
  showDeleteModal.value = true
}

const deleteWorkflow = () => {
  isDeleting.value = true
  
  router.delete(route('workflows.destroy', props.workflow.slug), {
    onSuccess: () => {
      showDeleteModal.value = false
      emit('deleted')
    },
    onError: () => {
      isDeleting.value = false
    },
    onFinish: () => {
      isDeleting.value = false
    }
  })
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>