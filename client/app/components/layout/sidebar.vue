<template>
  <div class="px-5 py-10 flex flex-col gap-3">
    <div class="flex items-center gap-2">
      <NuxtIcon name="logo" class="logo-icon" />
      <span class="text-3xl font-bold">Daily Grow</span>
    </div>
    <span class="text-xl opacity-40 text-semibold">
      {{ currentUser?.login }}
    </span>
    
    <div 
      class="flex items-center gap-2 bg-white rounded-xl shadow h-12 px-3"
    >
      <NuxtIcon name="settings" class="settings-icon text-gray-400" />
      <span class="text-xl">Отзывы</span>
    </div>

    <LayoutNavigation />
  </div>
</template>

<script setup lang="ts">
import { useUserStore } from '~/store/user'
const userStore = useUserStore()
const { currentUser } = storeToRefs(userStore)
</script>

<style scoped>
.logo-icon:deep(svg){
  height: 28px;
  width: 17px;
}
.settings-icon:deep(svg){
  width: 24px;
  height: 24px;
}
</style>