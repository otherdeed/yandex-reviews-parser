// utils/handleError.ts
import type { AxiosError } from 'axios'

interface BackendValidationErrors {
  errors?: Record<string, string[]>
  message?: string
}

export function handleError(error: unknown): string {
  if (!error || typeof error !== 'object') {
    return 'Нет соединения с сервером'
  }

  // Axios-ошибки
  if ('isAxiosError' in error && error.isAxiosError) {
    const axiosError = error as AxiosError<BackendValidationErrors>
    const res = axiosError.response

    if (res?.status === 422) {
      return 'Проверьте правильность введённых данных'
    }

    if (res?.status === 401) {
      return 'Неверный логин или пароль'
    }

    if (res?.status && res.status >= 500) {
      return 'Ошибка сервера, попробуйте позже'
    }

    if (res?.data?.message) {
      return res.data.message
    }

    return 'Что-то пошло не так'
  }

  if ('message' in error && typeof (error as any).message === 'string') {
    return (error as any).message
  }

  return 'Произошла неизвестная ошибка'
}