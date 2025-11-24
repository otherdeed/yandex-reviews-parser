import type { User } from "~/types/auth"

export const useUserStore = defineStore('user', () => {
  const currentUser = ref<User>()

  const setUser = (user:User):void =>{
    currentUser.value = user
  }

  return {
    //state
    currentUser,

    //action
    setUser
  }
})