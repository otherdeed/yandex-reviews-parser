<template>
  <div />
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})

await navigateTo('/reviews', { 
  external: false,
  replace: true
})
</script>