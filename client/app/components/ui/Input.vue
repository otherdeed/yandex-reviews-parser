<template>
  <input
    :value="modelValue"
    :class="styles"
    :placeholder="placeholder"
    :type="type"
    :autocomplete="autocomplete"
    @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
  >
</template>

<script setup lang="ts">
type Props = {
  modelValue: string
  placeholder?: string
  type?: 'text' | 'password'
  autocomplete?: string
}

withDefaults(defineProps<Props>(), {
  placeholder: '',
  type: 'text',
  autocomplete: ''
})

defineEmits<{
  'update:modelValue': [value: string]
}>()

const styles = 'border border-gray-300 rounded p-1 text-gray-700 placeholder-gray-400 focus:border-gray-400 focus:ring-1 focus:ring-gray-400 outline-none'
</script>