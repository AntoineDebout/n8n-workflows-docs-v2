<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200 flex flex-col h-[280px]">
    <div class="flex flex-col flex-1 p-6 pb-4">
      <!-- Title and Visibility -->
      <div class="flex items-center gap-2 mb-3">
        <h3 class="text-base font-medium text-gray-900 line-clamp-1">
          {{ workflow.title }}
        </h3>
        <VisibilityBadge :visibility="workflow.visibility" class="flex-shrink-0" />
      </div>
      
      <!-- Description (with flex-grow) -->
      <div class="flex-grow">
        <div v-if="workflow.descriptionExcerpt" class="text-gray-600 text-sm prose-sm mb-4 line-clamp-3">
          <MarkdownContent :content="workflow.descriptionExcerpt" :truncate="true" />
        </div>

        <!-- Tags -->
        <div v-if="workflow.tags.length > 0" class="flex flex-wrap gap-1 mb-4">
          <TagBadge
            v-for="tag in workflow.tags.slice(0, 3)"
            :key="tag"
            :tag="tag"
            class="flex-shrink-0"
          />
          <span
            v-if="workflow.tags.length > 3"
            class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-600 flex-shrink-0"
          >
            +{{ workflow.tags.length - 3 }}
          </span>
        </div>
      </div>

      <!-- Metadata (always at bottom) -->
      <div class="flex items-center justify-between text-sm text-gray-500 mt-auto">
        <div class="flex items-center gap-4">
          <span class="flex items-center gap-1 flex-shrink-0">
            <UserIcon class="w-4 h-4" />
            {{ workflow.authorName }}
          </span>
          <span class="flex items-center gap-1 flex-shrink-0">
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
          class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-md transition-colors duration-150"
        >
          <EyeIcon class="w-4 h-4" />
          Voir
        </Link>
        
        <Link
          v-if="workflow.canEdit"
          :href="route('workflows.edit', workflow.slug)"
          class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-gray-600 hover:text-gray-700 hover:bg-gray-100 rounded-md transition-colors duration-150"
        >
          <PencilIcon class="w-4 h-4" />
          Éditer
        </Link>
      </div>

      <!-- Delete button -->
      <button
        v-if="workflow.canDelete"
        type="button"
        class="group inline-flex items-center gap-2 rounded-md px-3 py-1.5 text-sm font-medium text-red-600 transition-colors duration-150 hover:bg-red-50 hover:text-red-700"
        @click="confirmDelete"
      >
        <TrashIcon class="h-4 w-4" />
        Supprimer
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import VisibilityBadge from '@/Components/VisibilityBadge.vue'
import TagBadge from '@/Components/TagBadge.vue'
import MarkdownContent from '@/Components/MarkdownContent.vue'
import {
  EyeIcon,
  PencilIcon,
  UserIcon,
  ClockIcon
} from '@heroicons/vue/24/outline'
import TrashIcon from '@/Components/icons/TrashIcon.vue'

const props = defineProps({
  workflow: {
    type: Object,
    required: true
  }
})

const isDeleting = ref(false)

const confirmDelete = () => {
  Swal.fire({
    title: 'Êtes-vous sûr ?',
    text: "Cette action est irréversible !",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Oui, supprimer',
    cancelButtonText: 'Annuler',
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
  }).then((result) => {
    if (result.isConfirmed) {
      deleteWorkflow()
    }
  })
}

const deleteWorkflow = () => {
  if (isDeleting.value) return
  isDeleting.value = true
  
  router.delete(route('workflows.destroy', props.workflow.slug), {
    onSuccess: () => {
      Swal.fire({
        title: 'Supprimé !',
        text: 'Le workflow a été supprimé avec succès',
        icon: 'success',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        },
        background: '#22c55e',
        color: '#ffffff',
        iconColor: '#ffffff'
      })
    },
    onError: () => {
      Swal.fire({
        title: 'Erreur',
        text: 'Une erreur est survenue lors de la suppression',
        icon: 'error',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        },
        background: '#ef4444',
        color: '#ffffff',
        iconColor: '#ffffff'
      })
      isDeleting.value = false
    },
    onFinish: () => {
      isDeleting.value = false
    }
  })
}
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>