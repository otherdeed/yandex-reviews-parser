import type { ReviewsData } from "~/types/review"

export const useReviewsStore = defineStore('review', () => {
  const reviews = ref<ReviewsData>()
  const reviewsUrl = ref()
  const setReviews = (Datareviews:ReviewsData, url: string):void =>{
    reviews.value = Datareviews
    reviewsUrl.value = url
  }

  return {
    //state
    setReviews,

    //action
    reviews,
    reviewsUrl
  }
})