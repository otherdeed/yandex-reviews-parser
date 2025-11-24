<template>
  <div class="flex flex-col gap-1 bg-gray-100 rounded-xl p-1">
    <div class="flex justify-between">
      <span>{{ date }}</span>
      <ReviewStars :rating="rating" />
    </div>
    <div class="flex gap-2 items-center">
      <span>{{ author }}</span>

      <div class="flex gap-1 items-center">
        <NuxtIcon name="heart"/>
        {{ likes }}
      </div>
    </div>
    <span class="text-sm">{{ text }}</span>
  </div>
</template>

<script setup lang="ts">
import type { Review } from '~/types/review'

withDefaults(defineProps<Review>(), {
  author: '',
  rating: 0,
  date: '',
  text: '',
  likes: 0
})
</script>

<style scoped>

</style>