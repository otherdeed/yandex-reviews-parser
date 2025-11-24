<template>
  <form class="flex flex-col justify-center gap-2 border rounded p-4 max-w-[400px] w-full" @submit.prevent="submit">
    <div class="flex items-center">
      <NuxtIcon name="logo" />
      <span class="font-semibold text-xl">{{ typeForm.text }}</span>
    </div>
    
    <UiInput v-model="data.login" placeholder="Login" autocomplete="username" />
    <div v-if="errors.login" class="text-red-500 text-sm">{{ errors.login }}</div>
    
    <UiInput v-model="data.password" type="password" placeholder="Password" autocomplete="current-password" />
    <div v-if="errors.password" class="text-red-500 text-sm">{{ errors.password }}</div>
    
    <UiButton 
      type="submit"
      :loading="loading"
    >
      {{ typeForm.text }}
    </UiButton>
      <div v-if="errors.server" class="text-red-500 text-sm">{{ errors.server }}</div>
    <NuxtLink 
      class="text-sm text-blue-300"
      :to="typeForm.link"
    >
      {{ typeForm.textLink }}
    </NuxtLink>
  </form>
</template>

<script setup lang="ts">
import { z } from 'zod'

const { login, register } = useAuth()

type Props = {
  type: 'login' | 'register'
}

const props = defineProps<Props>()

const typeForm = computed(() => {
  return {
    link: props.type === 'register' ? '/login' : '/register',
    text: props.type === 'register' ? 'Register' : 'Log in',
    textLink: props.type === 'register' ? 'Log in' : "Register"
  }
})


const loginSchema = z.object({
  login: z.string()
    .min(3, 'Логин должен содержать минимум 3 символа')
    .max(20, 'Логин должен содержать максимум 20 символов'),
  password: z.string()
    .min(6, 'Пароль должен содержать минимум 6 символов')
    .max(50, 'Пароль должен содержать максимум 50 символов')
})

const loading = ref(false)

const data = reactive({
  login: '',
  password: ''
})

const errors = reactive({
  login: '',
  password: '',
  server: ''
})

async function submit():Promise<void> {
  const result = loginSchema.safeParse(data)

  errors.login = ''
  errors.password = ''
  errors.server = ''
  
  if (!result.success) {
    result.error.issues.forEach(issue => {
      if (issue.path[0] === 'login') {
        errors.login = issue.message
      } else if (issue.path[0] === 'password') {
        errors.password = issue.message
      }
    })
    return
  }
  try {
    loading.value = true
    if (props.type === 'login') {
      await login(data.login, data.password)
    } else {
      await register(data.login, data.password)
    }
  } catch (error: unknown) {
    errors.server = handleError(error)
  } finally {
    loading.value = false
  }
}
</script>