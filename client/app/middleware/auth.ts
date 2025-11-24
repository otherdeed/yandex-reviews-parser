// middleware/auth.global.ts
export default defineNuxtRouteMiddleware((to) => {
  const { $auth } = useNuxtApp()
  
  const publicPages = ['/login', '/register']
  const isPublicPage = publicPages.includes(to.path)
  const isAuthenticated = $auth.isAuthenticated()

  if (!isAuthenticated && !isPublicPage) {
    return navigateTo('/login')
  }
  if (isAuthenticated && isPublicPage) {
    return navigateTo('/reviews')
  }
})