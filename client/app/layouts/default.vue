<!-- layouts/default.vue -->
<template>
  <div class="flex h-screen">
    <LayoutSidebar class="w-72 flex-shrink-0 bg-gray-100" />
    <div class="flex-1 flex flex-col min-w-0">
      <LayoutTopbar class="flex-shrink-0" />
      <main class="flex-1 overflow-y-auto">
        <div class="p-6">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>