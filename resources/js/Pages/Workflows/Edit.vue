<template>
  <AuthenticatedLayout>
    <Head :title="`Éditer ${workflow.title}`" />

    <div class="py-12">
      <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center gap-4 mb-4">
            <Link
              :href="route('workflows.show', workflow.slug)"
              class="flex items-center text-gray-500 hover:text-gray-700"
            >
              <ArrowLeftIcon class="w-4 h-4 mr-1" />
              Retour au workflow
            </Link>
          </div>
          <h1 class="text-3xl font-bold text-gray-900">Éditer le workflow</h1>
          <p class="mt-2 text-sm text-gray-600">
            Modifiez les informations de votre workflow N8N
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
                  <p class="mt-1 text-xs text-gray-500">
                    Utilisez la syntaxe Markdown pour formater votre description
                  </p>
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

            <!-- JSON Workflow -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Workflow JSON *
              </label>
              
              <div class="mb-4">
                <div class="flex justify-between items-center mb-2">
                  <span class="text-sm text-gray-600">Configuration actuelle</span>
                  <button
                    type="button"
                    class="text-sm text-indigo-600 hover:text-indigo-700"
                    @click="formatJson"
                  >
                    Formater JSON
                  </button>
                </div>
                
                <textarea
                  v-model="jsonInput"
                  rows="15"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm font-mono text-sm"
                  :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': errors.workflow_json || jsonError }"
                  @input="validateJson"
                />
                
                <div class="mt-2 flex justify-between items-center">
                  <div>
                    <p v-if="jsonError" class="text-sm text-red-600">{{ jsonError }}</p>
                    <p v-else-if="jsonInput && !jsonError" class="text-sm text-green-600">
                      <CheckIcon class="w-4 h-4 inline mr-1" />
                      JSON valide ({{ nodeCount }} nœuds, {{ connectionCount }} connexions)
                    </p>
                  </div>
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
                :href="route('workflows.show', workflow.slug)"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Annuler
              </Link>
              <button
                type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                :disabled="processing || !isFormValid"
              >
                <span v-if="processing">Sauvegarde...</span>
                <span v-else>Sauvegarder les modifications</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import {
  ArrowLeftIcon,
  CheckIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  workflow: {
    type: Object,
    required: true
  },
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
  title: props.workflow.title,
  description: props.workflow.description || '',
  workflow_json: props.workflow.workflowJson,
  tags: [...(props.workflow.tags || [])],
  visibility: props.workflow.visibility
})

const { errors } = form

const jsonInput = ref('')
const jsonError = ref('')
const newTag = ref('')

const markdownPreview = computed(() => {
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

const nodeCount = computed(() => {
  try {
    const parsed = JSON.parse(jsonInput.value)
    return parsed.nodes ? parsed.nodes.length : 0
  } catch {
    return 0
  }
})

const connectionCount = computed(() => {
  try {
    const parsed = JSON.parse(jsonInput.value)
    return parsed.connections ? Object.keys(parsed.connections).length : 0
  } catch {
    return 0
  }
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
  validateJson()
  if (jsonError.value) return

  form.put(route('workflows.update', props.workflow.slug))
}

onMounted(() => {
  jsonInput.value = JSON.stringify(props.workflow.workflowJson, null, 2)
  validateJson()
})
</script>