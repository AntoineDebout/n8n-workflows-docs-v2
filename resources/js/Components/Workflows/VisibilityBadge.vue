<template>
  <span 
    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
    :class="badgeClasses"
    :title="visibilityTooltip"
  >
    <component :is="visibilityIcon" class="w-3 h-3 mr-1" />
    {{ visibilityLabel }}
  </span>
</template>

<script setup>
import { computed } from 'vue'
import {
  LockClosedIcon,
  UserGroupIcon,
  GlobeAltIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  visibility: {
    type: String,
    required: true,
    validator: (value) => ['private', 'team', 'public'].includes(value)
  }
})

const visibilityConfig = {
  private: {
    label: 'Privé',
    icon: LockClosedIcon,
    classes: 'bg-gray-100 text-gray-700',
    tooltip: 'Visible uniquement par vous'
  },
  team: {
    label: 'Équipe',
    icon: UserGroupIcon,
    classes: 'bg-blue-100 text-blue-700',
    tooltip: 'Visible par votre équipe'
  },
  public: {
    label: 'Public',
    icon: GlobeAltIcon,
    classes: 'bg-green-100 text-green-700',
    tooltip: 'Visible par tous'
  }
}

const visibilityLabel = computed(() => {
  return visibilityConfig[props.visibility]?.label || 'Inconnu'
})

const visibilityIcon = computed(() => {
  return visibilityConfig[props.visibility]?.icon || LockClosedIcon
})

const badgeClasses = computed(() => {
  return visibilityConfig[props.visibility]?.classes || 'bg-gray-100 text-gray-700'
})

const visibilityTooltip = computed(() => {
  return visibilityConfig[props.visibility]?.tooltip || ''
})

</script>