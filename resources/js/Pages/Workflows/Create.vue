<template>
  <AuthenticatedLayout>
    <Head title="Créer un workflow" />

    <div class="py-12">
      <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center gap-4 mb-4">
            <Link
              :href="route('workflows.index')"
              class="flex items-center text-gray-500 hover:text-gray-700"
            >
              <ArrowLeftIcon class="w-4 h-4 mr-1" />
              Retour à la liste
            </Link>
          </div>
          <h1 class="text-3xl font-bold text-gray-900">Créer un nouveau workflow</h1>
          <p class="mt-2 text-sm text-gray-600">
            Documentez votre workflow N8N pour le partager avec votre équipe
          </p>
        </div>

        <!-- Form -->
        <div class="bg-white shadow rounded-lg">
          <form @submit.prevent="submitForm" class="space-y-8 p-8">
            <!-- Title -->
            <div>
              <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                Titre *
              </label>
              <input
                id="title"
                v-model="form.title"
                type="text"
                required
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': errors.title }"
                placeholder="Ex: Synchronisation CRM vers Slack"
              />
              <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
            </div>

            <!-- Description -->
            <div>
              <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                Description (Markdown supporté)
              </label>
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div>
                  <textarea
                    id="description"
                    v-model="form.description"
                    rows="12"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': errors.description }"
                    placeholder="Décrivez le workflow, son objectif, ses étapes..."
                  />
                  <div class="flex items-center justify-between mt-2">
                    <button
                      type="button"
                      @click="showGenerateModal = true"
                      class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-gradient-to-r from-purple-600 to-indigo-600 rounded-md hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                      <SparklesIcon class="w-4 h-4 mr-2" />
                      Générer avec l'IA
                    </button>
                  </div>
                </div>
                <div class="hidden lg:block">
                  <div class="border border-gray-200 rounded-md p-4 h-full bg-gray-50">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Aperçu</h4>
                    <div 
                      v-if="form.description"
                      class="prose prose-sm max-w-none text-gray-700"
                      v-html="markdownPreview"
                    />
                    <p v-else class="text-sm text-gray-500 italic">
                      L'aperçu apparaîtra ici...
                    </p>
                  </div>
                </div>
              </div>
              <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
            </div>

            <!-- JSON Upload/Input -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Workflow JSON *
              </label>
              
              <!-- Upload or Manual Input Toggle -->
              <div class="mb-4">
                <div class="flex space-x-4">
                  <button
                    type="button"
                    class="px-3 py-2 text-sm font-medium rounded-md"
                    :class="inputMethod === 'upload' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:text-gray-700'"
                    @click="inputMethod = 'upload'"
                  >
                    <CloudArrowUpIcon class="w-4 h-4 inline mr-1" />
                    Upload fichier
                  </button>
                  <button
                    type="button"
                    class="px-3 py-2 text-sm font-medium rounded-md"
                    :class="inputMethod === 'manual' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:text-gray-700'"
                    @click="inputMethod = 'manual'"
                  >
                    <CodeBracketIcon class="w-4 h-4 inline mr-1" />
                    Saisie manuelle
                  </button>
                </div>
              </div>

              <!-- File Upload -->
              <div v-if="inputMethod === 'upload'" class="mb-4">
                <div
                  class="border-2 border-dashed border-gray-300 rounded-lg p-6"
                  :class="{ 'border-indigo-400 bg-indigo-50': isDragOver, 'border-red-300': errors.json_file }"
                  @dragover.prevent="isDragOver = true"
                  @dragleave.prevent="isDragOver = false"
                  @drop.prevent="handleFileDrop($event)"
                >
                  <div class="text-center">
                    <CloudArrowUpIcon class="mx-auto h-12 w-12 text-gray-400" />
                    <div class="mt-4">
                      <label for="json_file" class="cursor-pointer">
                        <span class="mt-2 block text-sm font-medium text-gray-900">
                          Glissez votre fichier JSON ici ou
                          <span class="text-indigo-600 hover:text-indigo-500">parcourez</span>
                        </span>
                        <input
                          id="json_file"
                          type="file"
                          accept=".json"
                          class="sr-only"
                          @change="handleFileSelect($event)"
                        />
                      </label>
                      <p class="mt-1 text-xs text-gray-500">JSON uniquement, max 2MB</p>
                    </div>
                  </div>
                </div>
                <p v-if="uploadedFileName" class="mt-2 text-sm text-green-600">
                  <CheckIcon class="w-4 h-4 inline mr-1" />
                  {{ uploadedFileName }}
                </p>
                <p v-if="errors.json_file" class="mt-1 text-sm text-red-600">{{ errors.json_file }}</p>
              </div>

              <!-- Manual JSON Input -->
              <div v-if="inputMethod === 'manual'">
                <textarea
                  v-model="jsonInput"
                  rows="12"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm font-mono text-sm"
                  :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': errors.workflow_json || jsonError }"
                  placeholder='{"nodes": [], "connections": {}}'
                  @input="validateJson"
                />
                <div class="mt-2 flex justify-between items-center">
                  <div>
                    <p v-if="jsonError" class="text-sm text-red-600">{{ jsonError }}</p>
                    <p v-else-if="jsonInput && !jsonError" class="text-sm text-green-600">
                      <CheckIcon class="w-4 h-4 inline mr-1" />
                      JSON valide
                    </p>
                  </div>
                  <button
                    type="button"
                    class="text-sm text-indigo-600 hover:text-indigo-700"
                    @click="formatJson"
                  >
                    Formater JSON
                  </button>
                </div>
              </div>
              
              <p v-if="errors.workflow_json" class="mt-1 text-sm text-red-600">{{ errors.workflow_json }}</p>
            </div>

            <!-- Tags -->
            <div>
              <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                Tags
              </label>
              <div class="mb-2">
                <input
                  v-model="newTag"
                  type="text"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  placeholder="Tapez un tag et appuyez sur Entrée"
                  @keydown.enter.prevent="addTag"
                  @keydown.comma.prevent="addTag"
                />
                <p class="mt-1 text-xs text-gray-500">
                  Appuyez sur Entrée ou virgule pour ajouter un tag
                </p>
              </div>
              
              <!-- Current Tags -->
              <div v-if="form.tags.length > 0" class="flex flex-wrap gap-2 mb-2">
                <span
                  v-for="(tag, index) in form.tags"
                  :key="index"
                  class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-indigo-100 text-indigo-700"
                >
                  {{ tag }}
                  <button
                    type="button"
                    class="ml-2 inline-flex items-center p-0.5 rounded-full hover:bg-indigo-200"
                    @click="removeTag(index)"
                  >
                    <XMarkIcon class="w-3 h-3" />
                  </button>
                </span>
              </div>

              <!-- Suggested Tags -->
              <div v-if="suggestedTags.length > 0" class="mb-2">
                <p class="text-xs text-gray-500 mb-1">Tags suggérés :</p>
                <div class="flex flex-wrap gap-1">
                  <button
                    v-for="tag in suggestedTags"
                    :key="tag"
                    type="button"
                    class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded hover:bg-gray-200"
                    @click="addSuggestedTag(tag)"
                  >
                    + {{ tag }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Visibility -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-3">
                Visibilité
              </label>
              <div class="space-y-3">
                <div
                  v-for="(label, value) in visibilityOptions"
                  :key="value"
                  class="flex items-center"
                >
                  <input
                    :id="value"
                    v-model="form.visibility"
                    type="radio"
                    :value="value"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                  />
                  <label :for="value" class="ml-3 block text-sm font-medium text-gray-700">
                    {{ label }}
                  </label>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
              <Link
                :href="route('workflows.index')"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Annuler
              </Link>
              <button
                type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                :disabled="processing || !isFormValid"
              >
                <span v-if="processing">Création...</span>
                <span v-else>Créer le workflow</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modale de génération de description -->
    <GenerateDescriptionModal
      v-model:show="showGenerateModal"
      @description-generated="form.description = $event"
    />
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import GenerateDescriptionModal from '@/Components/Workflow/GenerateDescriptionModal.vue'
import Swal from 'sweetalert2'
import {
  ArrowLeftIcon,
  CloudArrowUpIcon,
  CodeBracketIcon,
  CheckIcon,
  XMarkIcon,
  SparklesIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  availableTags: {
    type: Array,
    default: () => []
  },
  visibilityOptions: {
    type: Object,
    default: () => ({})
  }
})

const form = useForm({
  title: '',
  description: '',
  workflow_json: {},
  tags: [],
  visibility: 'private'
})

const { errors } = form

const inputMethod = ref('upload')
const jsonInput = ref('')
const jsonError = ref('')
const newTag = ref('')
const isDragOver = ref(false)
const uploadedFileName = ref('')
const showGenerateModal = ref(false)

const markdownPreview = computed(() => {
  // Simple markdown to HTML conversion (you might want to use a proper library like marked.js)
  if (!form.description) return ''
  
  return form.description
    .replace(/^### (.*$)/gim, '<h3>$1</h3>')
    .replace(/^## (.*$)/gim, '<h2>$1</h2>')
    .replace(/^# (.*$)/gim, '<h1>$1</h1>')
    .replace(/\*\*(.*)\*\*/gim, '<strong>$1</strong>')
    .replace(/\*(.*)\*/gim, '<em>$1</em>')
    .replace(/`(.*)`/gim, '<code>$1</code>')
    .replace(/\n/gim, '<br>')
})

const suggestedTags = computed(() => {
  return props.availableTags.filter(tag => 
    !form.tags.includes(tag) && 
    tag.toLowerCase().includes(newTag.value.toLowerCase())
  ).slice(0, 5)
})

const isFormValid = computed(() => {
  return form.title && 
         Object.keys(form.workflow_json).length > 0 && 
         !jsonError.value
})

const validateJson = () => {
  if (!jsonInput.value.trim()) {
    jsonError.value = ''
    form.workflow_json = {}
    return
  }

  try {
    const parsed = JSON.parse(jsonInput.value)
    if (!parsed.nodes || !Array.isArray(parsed.nodes)) {
      jsonError.value = 'Le JSON doit contenir un tableau "nodes"'
      return
    }
    jsonError.value = ''
    form.workflow_json = parsed
  } catch (e) {
    jsonError.value = 'JSON invalide: ' + e.message
  }
}

const formatJson = () => {
  if (jsonInput.value.trim()) {
    try {
      const parsed = JSON.parse(jsonInput.value)
      jsonInput.value = JSON.stringify(parsed, null, 2)
      validateJson()
    } catch (e) {
      // Ignore formatting errors
    }
  }
}

const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    processFile(file)
  }
}

const handleFileDrop = (event) => {
  isDragOver.value = false
  const file = event.dataTransfer.files[0]
  if (file && file.type === 'application/json') {
    processFile(file)
  }
}

const processFile = (file) => {
  const reader = new FileReader()
  reader.onload = (e) => {
    try {
      const json = JSON.parse(e.target.result)
      form.workflow_json = json
      uploadedFileName.value = file.name
      jsonInput.value = JSON.stringify(json, null, 2)
    } catch (error) {
      alert('Erreur lors de la lecture du fichier JSON')
    }
  }
  reader.readAsText(file)
}

const addTag = () => {
  const tag = newTag.value.trim()
  if (tag && !form.tags.includes(tag)) {
    form.tags.push(tag)
    newTag.value = ''
  }
}

const addSuggestedTag = (tag) => {
  if (!form.tags.includes(tag)) {
    form.tags.push(tag)
  }
}

const removeTag = (index) => {
  form.tags.splice(index, 1)
}

const submitForm = () => {
  if (inputMethod.value === 'manual') {
    validateJson()
    if (jsonError.value) return
  }

  form.post(route('workflows.store'), {
    onSuccess: () => {
      Swal.fire({
        title: 'Succès !',
        text: 'Le workflow a été créé avec succès',
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
        background: '#22c55e', // vert
        color: '#ffffff', // texte blanc
        iconColor: '#ffffff' // icône blanche
      })
    },
    onError: (errors) => {
      let errorMessage = Object.values(errors).join('\n')
      Swal.fire({
        title: 'Erreur',
        text: errorMessage,
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
        background: '#ef4444', // rouge
        color: '#ffffff', // texte blanc
        iconColor: '#ffffff' // icône blanche
      })
    }
  })
}

// Watch for JSON input changes when in manual mode
watch(jsonInput, () => {
  if (inputMethod.value === 'manual') {
    validateJson()
  }
})
</script>