<template>
  <div class="flex flex-col gap-4">
    <span class="text-xl">Подключить Яндекс</span>
    <div class="flex flex-col gap-2">
      <div>
          Укажите ссылку на Яндекс, пример
        <a 
          class="underline underline-offset-2 text-gray-400"
          href="https://yandex.ru/maps/org/samoye_populyarnoye_kafe/1010501395/reviews/"
          target="_blank"
        >
          https://yandex.ru/maps/org/samoye_populyarnoye_kafe/1010501395/reviews/
        </a>
      </div>
      <UiInput 
        v-model="url" 
        placeholder="https://yandex.ru/maps/org/samoye_populyarnoye_kafe/1010501395/reviews/"
        class="w-2/3"
      />
    </div>
    <div v-if="error" class="text-red-500 text-sm">
      {{ error }}
    </div>
    <UiButton
      :disabled="buttonState.disabled"
      class="w-[130px]"
      :loading="loading"
      @click="updateUrl(url)"
    >
      {{ buttonState.text }}
    </UiButton>
  </div>
</template>

<script setup lang="ts">
import { useUserStore } from '~/store/user'

const auth = useAuth()
const userStore = useUserStore()
const { currentUser } = storeToRefs(userStore)

const url = ref(currentUser.value?.url || '')
const loading = ref(false)
const error = ref('')

const buttonState = computed(() => {
  if(currentUser.value?.url == url.value || !url.value) {
    return {
      text: 'Сохранено',
      disabled: true
    }
  }
  return {
    text: 'Сохранить',
    disabled: false
  }
})

const updateUrl = async (newUrl: string):Promise<void> => {
  loading.value = true
  error.value = ''
  try {
    await auth.updateUrl(newUrl)
  } catch (err: unknown) {
    error.value = handleError(err)
    console.log("Error url: ", handleError(err))
  } finally {
    loading.value = false
  }
}

watch(currentUser, (newUser) => {
  if (newUser?.url) {
    url.value = newUser.url
  }
}, { immediate: true })

definePageMeta({
  middleware: 'auth'
})
</script>