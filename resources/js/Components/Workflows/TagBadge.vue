<template>
  <span 
    class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium transition-colors duration-150"
    :class="badgeClasses"
  >
    <TagIcon class="w-3 h-3 mr-1" />
    {{ tag }}
  </span>
</template>

<script setup>
import { computed } from 'vue'
import { TagIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  tag: {
    type: String,
    required: true
  },
  variant: {
    type: String,
    default: 'default', // default, primary, secondary, success, warning, error
    validator: (value) => ['default', 'primary', 'secondary', 'success', 'warning', 'error'].includes(value)
  }
})

const badgeClasses = computed(() => {
  const variants = {
    default: 'bg-gray-100 text-gray-700 hover:bg-gray-200',
    primary: 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200',
    secondary: 'bg-purple-100 text-purple-700 hover:bg-purple-200',
    success: 'bg-green-100 text-green-700 hover:bg-green-200',
    warning: 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200',
    error: 'bg-red-100 text-red-700 hover:bg-red-200'
  }
  
  return variants[props.variant] || variants.default
})

// Fonction utilitaire pour déterminer automatiquement la couleur basée sur le contenu du tag
const getTagVariant = (tag) => {
  const tagLower = tag.toLowerCase()
  
  if (tagLower.includes('api') || tagLower.includes('http') || tagLower.includes('webhook')) {
    return 'primary'
  }
  if (tagLower.includes('database') || tagLower.includes('sql') || tagLower.includes('db')) {
    return 'secondary'
  }
  if (tagLower.includes('email') || tagLower.includes('notification') || tagLower.includes('slack')) {
    return 'success'
  }
  if (tagLower.includes('error') || tagLower.includes('fail') || tagLower.includes('alert')) {
    return 'error'
  }
  if (tagLower.includes('schedule') || tagLower.includes('cron') || tagLower.includes('timer')) {
    return 'warning'
  }
  
  return 'default'
}
</script>