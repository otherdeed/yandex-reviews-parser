<template>
  <div class="flex justify-center items-center min-h-screen">
    <Form type="login"/>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth',
  layout: 'auth'
})
</script>

<style scoped>

</style>