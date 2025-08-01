<template>
  <TransitionRoot appear :show="show" as="template">
    <Dialog as="div" @close="close" class="relative z-10">
      <TransitionChild
        as="template"
        enter="duration-300 ease-out"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="duration-200 ease-in"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black bg-opacity-25" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
          <TransitionChild
            as="template"
            enter="duration-300 ease-out"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95"
          >
            <DialogPanel class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
              <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900">
                Générer une description avec l'IA
              </DialogTitle>
              
              <div class="mt-4">
                <label for="ai-prompt" class="block text-sm font-medium text-gray-700 mb-2">
                  Décrivez brièvement votre workflow
                </label>
                <textarea
                  id="ai-prompt"
                  v-model="prompt"
                  rows="3"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  placeholder="Ex: Un workflow qui synchronise les contacts du CRM vers Slack quand ils sont mis à jour"
                />
              </div>

              <div class="mt-6 flex justify-end space-x-3">
                <button
                  type="button"
                  class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                  @click="close"
                >
                  Annuler
                </button>
                <button
                  type="button"
                  class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
                  :disabled="!prompt.trim() || isLoading"
                  @click="generate"
                >
                  <template v-if="isLoading">
                    <span class="inline-flex items-center">
                      <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      Génération...
                    </span>
                  </template>
                  <template v-else>
                    Générer
                  </template>
                </button>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { Dialog, DialogPanel, DialogTitle, TransitionRoot, TransitionChild } from '@headlessui/vue'
import Swal from 'sweetalert2'

// Configure Axios pour inclure les cookies et le token CSRF
axios.defaults.withCredentials = true
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
const token = document.head.querySelector('meta[name="csrf-token"]')
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
} else {
    console.error('CSRF token not found')
}

const props = defineProps({
  show: {
    type: Boolean,
    required: true
  }
})

const emit = defineEmits(['update:show', 'description-generated'])

const prompt = ref('')
const isLoading = ref(false)

const close = () => {
  prompt.value = ''
  emit('update:show', false)
}

const generate = async () => {
  if (!prompt.value.trim() || isLoading.value) return
  
  isLoading.value = true
  try {
    const response = await axios.post(route('api.workflows.generate-description'), {
      description: prompt.value
    })
    
    emit('description-generated', response.data.description)
    close()
    
    Swal.fire({
      title: 'Succès !',
      text: 'Description générée avec succès',
      icon: 'success',
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      background: '#22c55e',
      color: '#ffffff',
      iconColor: '#ffffff'
    })
  } catch (error) {
    console.error('Erreur lors de la génération de la description:', error);
    Swal.fire({
      title: 'Erreur',
      text: error.response?.data?.message || 'Erreur lors de la génération',
      icon: 'error',
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      background: '#ef4444',
      color: '#ffffff',
      iconColor: '#ffffff'
    })
  } finally {
    isLoading.value = false
  }
}
</script>
