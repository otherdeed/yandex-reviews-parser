<template>
  <div class="flex items-center gap-1">
    <div 
      v-for="star in 5" 
      :key="star"
      class="relative"
    >
      <div class="text-gray-300">
        <svg :width="size" :height="size" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
      </div>
      <div 
        class="absolute top-0 left-0 overflow-hidden"
        :style="{ width: `${getStarFill(star)}%` }"
      >
        <div class="text-yellow-400">
          <svg :width="size" :height="size" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
          </svg>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  rating: number
  size?: number
}

const props = withDefaults(defineProps<Props>(), {
  size: 20
})

const getStarFill = (starNumber: number): number => {
  if (props.rating >= starNumber) {
    return 100
  } else if (props.rating <= starNumber - 1) {
    return 0
  } else {
    return (props.rating - (starNumber - 1)) * 100
  }
}
</script>