<template>
  <component :is="$page.props.auth.user ? AuthenticatedLayout : GuestLayout">
    <Head :title="workflow.title" />

    <div class="py-12">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center gap-4 mb-4">
            <Link
              :href="$page.props.auth.user ? route('workflows.index') : '/'"
              class="flex items-center text-gray-500 hover:text-gray-700"
            >
              <ArrowLeftIcon class="w-4 h-4 mr-1" />
              Retour à la liste
            </Link>
          </div>
          
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-center gap-3 mb-2">
                <h1 class="text-3xl font-bold text-gray-900">{{ workflow.title }}</h1>
                <VisibilityBadge :visibility="workflow.visibility" />
              </div>
              
              <div class="flex items-center gap-6 text-sm text-gray-500 mb-4">
                <span class="flex items-center gap-1">
                  <UserIcon class="w-4 h-4" />
                  {{ workflow.authorName }}
                </span>
                <span class="flex items-center gap-1">
                  <ClockIcon class="w-4 h-4" />
                  Créé le {{ workflow.createdAt }}
                </span>
                <span v-if="workflow.createdAt !== workflow.updatedAt" class="flex items-center gap-1">
                  <PencilIcon class="w-4 h-4" />
                  Modifié le {{ workflow.updatedAt }}
                </span>
              </div>

              <!-- Tags -->
              <div v-if="workflow.tags.length > 0" class="flex flex-wrap gap-2 mb-6">
                <TagBadge
                  v-for="tag in workflow.tags"
                  :key="tag"
                  :tag="tag"
                />
              </div>
            </div>

            <!-- Actions -->
            <div v-if="canEdit || canDelete" class="flex items-center gap-2 ml-6">
              <Link
                v-if="canEdit"
                :href="route('workflows.edit', workflow.slug)"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                <PencilIcon class="w-4 h-4 mr-2" />
                Éditer
              </Link>
              
              <button
                v-if="canDelete"
                type="button"
                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                @click="confirmDelete"
              >
                <TrashIcon class="w-4 h-4 mr-2" />
                Supprimer
              </button>
            </div>
          </div>
        </div>

        <!-- Content Tabs -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
              <button
                type="button"
                class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap"
                :class="activeTab === 'description' 
                  ? 'border-indigo-500 text-indigo-600' 
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                @click="activeTab = 'description'"
              >
                <DocumentTextIcon class="w-4 h-4 inline mr-2" />
                Description
              </button>
              
              <button
                type="button"
                class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap"
                :class="activeTab === 'json' 
                  ? 'border-indigo-500 text-indigo-600' 
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                @click="activeTab = 'json'"
              >
                <CodeBracketIcon class="w-4 h-4 inline mr-2" />
                JSON Workflow
              </button>
              
              <button
                type="button"
                class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap"
                :class="activeTab === 'preview' 
                  ? 'border-indigo-500 text-indigo-600' 
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                @click="activeTab = 'preview'"
              >
                <EyeIcon class="w-4 h-4 inline mr-2" />
                Aperçu Visual
              </button>
            </nav>
          </div>

          <!-- Tab Content -->
          <div class="p-6">
            <!-- Description Tab -->
            <div v-if="activeTab === 'description'">
              <div v-if="workflow.description" class="prose max-w-none">
                <div v-html="renderedMarkdown" />
              </div>
              <div v-else class="text-center py-12 text-gray-500">
                <DocumentIcon class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                <p>Aucune description fournie pour ce workflow.</p>
              </div>
            </div>

            <!-- JSON Tab -->
            <div v-if="activeTab === 'json'">
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Configuration JSON</h3>
                <div class="flex gap-2">
                  <button
                    type="button"
                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    @click="copyToClipboard"
                  >
                    <ClipboardIcon v-if="!copied" class="w-4 h-4 mr-1" />
                    <CheckIcon v-else class="w-4 h-4 mr-1 text-green-500" />
                    {{ copied ? 'Copié!' : 'Copier' }}
                  </button>
                  
                  <button
                    type="button"
                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    @click="downloadJson"
                  >
                    <ArrowDownTrayIcon class="w-4 h-4 mr-1" />
                    Télécharger
                  </button>
                </div>
              </div>
              
              <JsonViewer :json="workflow.workflowJson" />
            </div>

            <!-- Preview Tab -->
            <div v-if="activeTab === 'preview'" class="relative">
                <n8n-demo :workflow="JSON.stringify(workflow.workflowJson, null, 2)" />
            </div>
          </div>
        </div>

        <!-- JSON Summary -->
        <div v-if="workflowSummary" class="mt-6 bg-gray-50 rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Résumé du workflow</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
              <div class="text-2xl font-bold text-indigo-600">{{ workflowSummary.nodeCount }}</div>
              <div class="text-sm text-gray-500">Nœuds</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-green-600">{{ workflowSummary.connectionCount }}</div>
              <div class="text-sm text-gray-500">Connexions</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-purple-600">{{ workflowSummary.uniqueNodeTypes.length }}</div>
              <div class="text-sm text-gray-500">Types de nœuds</div>
            </div>
          </div>
          
          <div v-if="workflowSummary.uniqueNodeTypes.length > 0" class="mt-6">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Types de nœuds utilisés:</h4>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="nodeType in workflowSummary.uniqueNodeTypes"
                :key="nodeType"
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
              >
                {{ nodeType }}
              </span>
            </div>
          </div>
        </div>

        <!-- Delete Modal -->
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
    </div>
  </component>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import ArrowLeftIcon from '@/Components/icons/ArrowLeftIcon.vue';
import UserIcon from '@/Components/icons/UserIcon.vue';
import ClockIcon from '@/Components/icons/ClockIcon.vue';
import PencilIcon from '@/Components/icons/PencilIcon.vue';
import TagBadge from '@/Components/TagBadge.vue';
import VisibilityBadge from '@/Components/VisibilityBadge.vue';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Modal from '@/Components/Modal.vue'
import JsonViewer from '@/Components/Workflows/JsonViewer.vue'
import {
  ArrowDownTrayIcon,
  CubeIcon,
  ExclamationTriangleIcon,
  ClipboardIcon,
  CheckIcon
} from '@heroicons/vue/24/outline'
import { ref, computed } from 'vue'

const props = defineProps({
  workflow: {
    type: Object,
    required: true
  },
  canEdit: {
    type: Boolean,
    default: false
  },
  canDelete: {
    type: Boolean,
    default: false
  }
})

const activeTab = ref('description')
const showDeleteModal = ref(false)
const isDeleting = ref(false)
const copied = ref(false)

const renderedMarkdown = computed(() => {
  if (!props.workflow.description) return ''
  
  // Simple markdown to HTML conversion
  return props.workflow.description
    .replace(/^### (.*$)/gim, '<h3 class="text-lg font-semibold mt-6 mb-3">$1</h3>')
    .replace(/^## (.*$)/gim, '<h2 class="text-xl font-semibold mt-8 mb-4">$1</h2>')
    .replace(/^# (.*$)/gim, '<h1 class="text-2xl font-bold mt-8 mb-6">$1</h1>')
    .replace(/\*\*(.*?)\*\*/gim, '<strong class="font-semibold">$1</strong>')
    .replace(/\*(.*?)\*/gim, '<em class="italic">$1</em>')
    .replace(/`(.*?)`/gim, '<code class="bg-gray-100 px-1 py-0.5 rounded text-sm font-mono">$1</code>')
    .replace(/```([\s\S]*?)```/gim, '<pre class="bg-gray-100 p-4 rounded-lg overflow-x-auto"><code class="text-sm">$1</code></pre>')
    .replace(/^- (.*$)/gim, '<li class="ml-4">$1</li>')
    .replace(/\n\n/gim, '</p><p class="mb-4">')
    .replace(/\n/gim, '<br>')
    .replace(/^/, '<p class="mb-4">')
    .replace(/$/, '</p>')
})

const workflowSummary = computed(() => {
  const json = props.workflow.workflowJson
  if (!json || !json.nodes) return null

  const nodeCount = json.nodes.length
  const connectionCount = json.connections ? Object.keys(json.connections).length : 0
  const uniqueNodeTypes = [...new Set(json.nodes.map(node => node.type || 'Unknown'))]

  return {
    nodeCount,
    connectionCount,
    uniqueNodeTypes
  }
})

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
      router.visit(route('workflows.index'))
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

const copyToClipboard = async () => {
  try {
    await navigator.clipboard.writeText(JSON.stringify(props.workflow.workflowJson, null, 2))
    copied.value = true
    setTimeout(() => {
      copied.value = false
    }, 2000)
  } catch (err) {
    console.error('Erreur lors de la copie:', err)
  }
}

const downloadJson = () => {
  const dataStr = JSON.stringify(props.workflow.workflowJson, null, 2)
  const dataBlob = new Blob([dataStr], { type: 'application/json' })
  const url = URL.createObjectURL(dataBlob)
  const link = document.createElement('a')
  link.href = url
  link.download = `${props.workflow.title.replace(/[^a-z0-9]/gi, '_').toLowerCase()}.json`
  link.click()
  URL.revokeObjectURL(url)
}
</script>