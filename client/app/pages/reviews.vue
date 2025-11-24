<template>
  <div>
    <div v-if="data">
      <div class="border rounded-xl p-1 px-2 flex items-center w-[140px] justify-between mb-2">
        <NuxtIcon name="dot" />
        <span>Яндекс карты</span>
      </div>
      
      <div class="flex items-start gap-3">
        <div class="flex flex-col gap-3">
          <div
            v-for="review in (data as ReviewsData)?.reviews?.reviews || []"
            :key="review.author + review.date"
            class="border p-3 rounded-xl"
          >
              <Review 
              :author="review.author"
              :date="review.date"
              :likes="review.likes"
              :rating="review.rating"
              :text="review.text"
            />
          </div>
        </div>

        <div class="sticky top-3">
          <ReviewTotal 
            :total-reviews="(data as ReviewsData)?.reviews?.total_reviews || 0"
            :rating="(data as ReviewsData)?.reviews?.overall_rating || '0'"
          />
        </div>
      </div>
    </div>
    <div 
      v-if="pending"
      class="w-full h-[calc(100vh-150px)] flex flex-col justify-center items-center space-y-4"
    >
      <NuxtIcon name="loading" class="text-gray-500 scale-150" />
      <p class="text-gray-600 font-medium">Парсим данные...</p>
    </div>
    <div
      v-if="error"
      class="w-full h-[calc(100vh-150px)] flex flex-col justify-center items-center space-y-4"
    >
      <div class="bg-red-50 border border-red-200 rounded-lg p-6 max-w-md text-center">
        <div class="text-red-500 text-lg font-semibold mb-2">Ошибка</div>
        <p class="text-red-700">{{ error.message }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useReviewsStore } from '~/store/reviews'
import { useUserStore } from '~/store/user'
import type { ReviewsData } from '~/types/review'

definePageMeta({
  middleware: 'auth'
})

const auth = useAuth()
const userStore = useUserStore()
const { currentUser } = storeToRefs(userStore)

const reviewsStore = useReviewsStore()
const { reviews, reviewsUrl } = storeToRefs(reviewsStore)

const { data, error, pending } = useAsyncData('get-reviews', async () => {
  if(reviews.value) {
    if(reviewsUrl.value === currentUser.value?.url) {
      return reviews.value
    }
  }

  await auth.fetchUser()
  
  if(!currentUser.value?.url){
    throw Error('Вы не указали ссылку для парсинга')
  }

  return auth.getReviews(currentUser.value?.url || '')
})
</script>