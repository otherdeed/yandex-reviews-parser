// plugins/api.ts
import axios from 'axios'

export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig()

  const api = axios.create({
    baseURL: config.public.apiBase as string,
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    }
  })

  // Хранилище токена
  const token = useCookie<string | null>('auth.token', {
    maxAge: 60 * 60 * 24 * 30, // 30 дней
    sameSite: 'lax'
  })

  const expiresAt = useCookie<number | null>('auth.expiresAt', {
    maxAge: 60 * 60 * 24 * 30
  })

  // Вставляем токен в каждый запрос
  api.interceptors.request.use((config) => {
    if (token.value) {
      config.headers.Authorization = `Bearer ${token.value}`
    }
    return config
  })

  // Авто-refresh + 401 редирект
  api.interceptors.response.use(
    (response) => response,
    async (error) => {
      const originalRequest = error.config

      if (error.response?.status === 401 && !originalRequest._retry) {
        originalRequest._retry = true

        try {
          const { data } = await axios.post(
            `${config.public.apiBase}/refresh`,
            {},
            { headers: { Authorization: `Bearer ${token.value}` } }
          )

          token.value = data.token
          expiresAt.value = Date.now() + data.expires_in * 1000

          originalRequest.headers.Authorization = `Bearer ${data.token}`
          return api(originalRequest)
        } catch {
          // Refresh не сработал → логаут
          token.value = null
          expiresAt.value = null
          navigateTo('/login')
        }
      }

      return Promise.reject(error)
    }
  )

  return {
    provide: {
      api,
      auth: {
        setToken: (data: { token: string; expires_in: number }) => {
          token.value = data.token
          expiresAt.value = Date.now() + data.expires_in * 1000
        },
        clearToken: () => {
          token.value = null
          expiresAt.value = null
        },
        isAuthenticated: () => {
          if (!token.value || !expiresAt.value) return false
          return Date.now() < expiresAt.value - 5 * 60 * 1000 // 5 минут запас
        },
        getToken: () => token.value
      }
    }
  }
})