<template>
    <div 
        v-if="showNotice"
        class="fixed bottom-0 left-0 right-0 bg-gray-900 text-white py-4 px-6 shadow-lg z-50"
    >
        <div class="container mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex-1">
                <p class="text-sm md:text-base">
                    Мы используем файлы cookie для работы сайта. Продолжая использовать сайт, 
                    вы соглашаетесь с обработкой персональных данных в соответствии с 
                    <a 
                        :href="route('privacy')" 
                        class="underline hover:text-gray-300 transition"
                    >
                        Политикой конфиденциальности
                    </a>.
                </p>
            </div>
            <div class="flex items-center gap-4">
                <a 
                    :href="route('cookie')" 
                    class="text-sm underline hover:text-gray-300 transition"
                >
                    Подробнее
                </a>
                <button 
                    @click="acceptCookies"
                    class="bg-white text-gray-900 px-6 py-2 rounded-lg font-medium hover:bg-gray-100 transition"
                >
                    Понятно
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { route } from 'ziggy-js'

const showNotice = ref(false)

const acceptCookies = () => {
    localStorage.setItem('cookies_accepted', 'true')
    showNotice.value = false
}

onMounted(() => {
    const accepted = localStorage.getItem('cookies_accepted')
    if (!accepted) {
        showNotice.value = true
    }
})
</script>