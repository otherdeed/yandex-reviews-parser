<template>
  <button
    :type="type"
    :class="[style, { 'opacity-50 cursor-not-allowed': disabled || loading }]"
  >
    <NuxtIcon v-if="loading" name="loading"  />
    <slot v-else />
  </button>
</template>

<script setup lang="ts">
type Props = {
  type?: 'button' | 'submit'
  disabled?: boolean
  loading?: boolean
}
withDefaults(defineProps<Props>(), {
  type: 'button',
  disabled: false,
  loading: false
})
const style = 'flex justify-center items-center p-1 bg-blue-400 text-white rounded hover:bg-blue-500 transition-color duration-200'
</script>