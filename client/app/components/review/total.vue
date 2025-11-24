<template>
  <div class="border rounded-xl p-3 w-[270px]">
    <div class="flex items-center gap-2">
      <span class="text-5xl">{{ rating }}</span>
      <ReviewStars 
        :rating="Number(rating)"
        :size="30"
      />
    </div>
    <div class="border h-[1px] my-4" />
    <span>Всего отзывов: <strong>{{ totalReviews }}</strong> </span>
  </div>
</template>

<script setup lang="ts">
type Props = {
  totalReviews: number
  rating: string
}
defineProps<Props>()
</script>

<style scoped>

</style>