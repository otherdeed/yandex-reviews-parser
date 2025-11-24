<template>
  <div class="flex flex-col gap-1">
    <NuxtLink 
      v-for="item in navigation" 
      :key="item.url"
      :to="item.url"
      class="flex items-center rounded-xl h-6 px-11"
      :class="[
        $route.path === item.url 
          ? 'bg-white shadow' 
          : ''
      ]"
    >
      <span>{{ item.name }}</span>
    </NuxtLink>
  </div>
</template>

<script setup lang="ts">
const navigation = [
  { name: 'Отзывы', url: '/reviews' },
  { name: 'Настройка', url: '/setting' }
]
</script>

<style scoped>

</style>