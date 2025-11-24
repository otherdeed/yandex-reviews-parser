export type ReviewsData = {
  success: boolean
  reviews:{
    overall_rating: string
    total_reviews: number
    reviews: Review[]
  }
}

export type Review = {
  author: string
  rating: number
  date: string
  text: string
  likes: number
}