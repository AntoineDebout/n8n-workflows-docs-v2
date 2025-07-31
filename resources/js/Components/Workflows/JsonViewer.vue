<template>
  <div class="json-viewer">
    <div class="bg-gray-900 rounded-lg overflow-hidden">
      <div class="flex items-center justify-between px-4 py-2 bg-gray-800">
        <span class="text-sm font-medium text-gray-300">JSON</span>
        <div class="flex items-center gap-2">
          <button
            type="button"
            class="text-xs px-2 py-1 bg-gray-700 text-gray-300 rounded hover:bg-gray-600"
            @click="toggleCollapse"
          >
            {{ collapsed ? 'Développer' : 'Réduire' }}
          </button>
          <button
            type="button"
            class="text-xs px-2 py-1 bg-gray-700 text-gray-300 rounded hover:bg-gray-600"
            @click="toggleFormat"
          >
            {{ formatted ? 'Compact' : 'Formaté' }}
          </button>
        </div>
      </div>
      
      <div class="p-4 overflow-x-auto">
        <pre class="text-sm text-gray-100 whitespace-pre-wrap"><code v-html="displayedJson"></code></pre>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  json: {
    type: [Object, Array],
    required: true
  },
  maxDepth: {
    type: Number,
    default: 3
  }
})

const collapsed = ref(false)
const formatted = ref(true)

const displayedJson = computed(() => {
  let jsonString = ''
  
  if (formatted.value) {
    jsonString = JSON.stringify(props.json, null, 2)
  } else {
    jsonString = JSON.stringify(props.json)
  }
  
  if (collapsed.value) {
    return collapseJson(jsonString)
  }
  
  return highlightJson(jsonString)
})

const highlightJson = (jsonString) => {
  return jsonString
    .replace(/(".*?")\s*:/g, '<span class="text-blue-300">$1</span>:')
    .replace(/:\s*(".*?")/g, ': <span class="text-green-300">$1</span>')
    .replace(/:\s*(true|false)/g, ': <span class="text-yellow-300">$1</span>')
    .replace(/:\s*(null)/g, ': <span class="text-red-300">$1</span>')
    .replace(/:\s*(\d+\.?\d*)/g, ': <span class="text-orange-300">$1</span>')
    .replace(/([{}[\]])/g, '<span class="text-gray-400">$1</span>')
}

const collapseJson = (jsonString) => {
  const lines = jsonString.split('\n')
  const result = []
  let depth = 0
  let inCollapse = false
  
  for (let i = 0; i < lines.length; i++) {
    const line = lines[i]
    const trimmed = line.trim()
    
    if (trimmed.includes('{') || trimmed.includes('[')) {
      if (depth >= props.maxDepth && !inCollapse) {
        result.push(line.replace(/[{[].*/, trimmed.includes('{') ? '{ ... }' : '[ ... ]'))
        inCollapse = true
      } else {
        result.push(line)
      }
      if (trimmed.endsWith('{') || trimmed.endsWith('[')) {
        depth++
      }
    } else if (trimmed.includes('}') || trimmed.includes(']')) {
      if (trimmed.startsWith('}') || trimmed.startsWith(']')) {
        depth--
      }
      if (depth < props.maxDepth) {
        inCollapse = false
        if (!inCollapse) {
          result.push(line)
        }
      }
    } else if (!inCollapse) {
      result.push(line)
    }
  }
  
  return highlightJson(result.join('\n'))
}

const toggleCollapse = () => {
  collapsed.value = !collapsed.value
}

const toggleFormat = () => {
  formatted.value = !formatted.value
}
</script>

<style scoped>
.json-viewer {
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', 'Consolas', 'source-code-pro', monospace;
}
</style>