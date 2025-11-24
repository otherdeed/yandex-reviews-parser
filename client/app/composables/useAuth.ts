import { useReviewsStore } from '~/store/reviews'
import { useUserStore } from '~/store/user'
import type { User, AuthResponse } from '~/types/auth'
import type { ReviewsData } from '~/types/review'

interface UseAuthReturn {
  user: Readonly<Ref<User | null>>
  loading: Readonly<Ref<boolean>>
  login: (login: string, password: string) => Promise<void>
  register: (login: string, password: string) => Promise<void>
  logout: () => Promise<void>
  fetchUser: () => Promise<void>
  updateUrl: (url: string) => Promise<void>
  isAuthenticated: () => boolean,
  getReviews: (url: string) => Promise<ReviewsData>
}

export const useAuth = (): UseAuthReturn => {
  const { $api } = useNuxtApp()
  const userStore = useUserStore()
  const { setUser } = userStore
  const reviewsStore = useReviewsStore()
  const { setReviews } = reviewsStore
  const api = $api as any as {
    get: <T>(url: string) => Promise<{ data: T }>
    post: <T>(url: string, body: any) => Promise<{ data: T }>
  }

  const auth = useNuxtApp().$auth as {
    setToken: (data: AuthResponse) => void
    clearToken: () => void
    isAuthenticated: () => boolean
  }

  const user = ref<User | null>(null)
  const loading = ref(false)

  const login = async (login: string, password: string):Promise<void> => {
    loading.value = true
    try {
      const { data } = await api.post<AuthResponse>('/login', { login, password })
      auth.setToken(data)
      await fetchUser()
      await navigateTo('/')
    } finally {
      loading.value = false
    }
  }
  const authInProgress = ref(false)
  const register = async (login: string, password: string): Promise<void> => {
    loading.value = true
    authInProgress.value = true
    try {
      const { data } = await api.post<AuthResponse>('/register', { login, password })
      auth.setToken(data)
      await fetchUser()
      await navigateTo('/reviews')
    } finally {
      loading.value = false
      authInProgress.value = false
    }
  }

  const fetchUser = async ():Promise<void> => {
    if (!auth.isAuthenticated()) {
      user.value = null
      return
    }
    try {
      const { data } = await api.get<User>('/me')
      user.value = data
      setUser(user.value)
    } catch {
      auth.clearToken()
      user.value = null
    }
  }

  const updateUrl = async (url: string):Promise<void> => {
    const { data } = await api.post<User>('/update-url', { url })
    if(data){
      user.value = data
      setUser(user.value)
    }
  }

  const getReviews =  async (url: string):Promise<ReviewsData> => {
    const { data } = await api.post<ReviewsData>('/parse-reviews', { url })
    if(data) {
      setReviews(data, url)
    }
    return data
  }

  const logout = async ():Promise<void> => {
    auth.clearToken()
    user.value = null
    await navigateTo('/login')
  }

  if (import.meta.client && auth.isAuthenticated() && !user.value) {
    fetchUser()
  }

  const isAuthenticated = ():boolean => {
    return auth.isAuthenticated() || authInProgress.value
  }

  return {
    user: readonly(user),
    loading: readonly(loading),
    login,
    register,
    logout,
    fetchUser,
    isAuthenticated,
    updateUrl,
    getReviews
  }
}