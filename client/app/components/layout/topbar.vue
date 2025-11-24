<template>
  <div class="flex items-center justify-end h-[75px] bg-white border-b w-full px-10">
    <button @click="logout">
      <NuxtIcon name="logout" class="topbar-icon text-gray-400"/>
    </button>
  </div>
</template>

<script setup lang="ts">
const { logout } = useAuth()
</script>

<style scoped>
.topbar-icon:deep(svg){
  width: 20px;
  height: 20px;
}
</style>