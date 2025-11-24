// types/auth.ts
export type User = {
  id: number
  login: string
  url?: string
  created_at?: string
  updated_at?: string
}

export type AuthResponse = {
  token: string
  type: 'bearer'
  expires_in: number
  message?: string
  user?: User
}

export type AuthState = {
  setToken: (data: AuthResponse) => void
  clearToken: () => void
  isAuthenticated: () => boolean
  getToken: () => string | null
}