<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Sitemap Management</h1>
      <p class="text-gray-600 mt-2">Manage your website's sitemap</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Статус sitemap -->
      <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center">
          <div :class="status.exists ? 'bg-green-500' : 'bg-red-500'"
               class="w-12 h-12 rounded-lg flex items-center justify-center mr-4">
            <CheckCircleIcon v-if="status.exists" class="w-6 h-6 text-white" />
            <XCircleIcon v-else class="w-6 h-6 text-white" />
          </div>
          <div>
            <p class="text-sm font-medium text-gray-600">Sitemap Status</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">
              {{ status.exists ? 'Active' : 'Missing' }}
            </p>
            <p class="text-xs text-gray-500 mt-1">
              Last updated: {{ status.lastModified || 'Never' }}
            </p>
          </div>
        </div>
      </div>

      <!-- Количество URL -->
      <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center">
          <div class="w-12 h-12 rounded-lg bg-blue-500 flex items-center justify-center mr-4">
            <LinkIcon class="w-6 h-6 text-white" />
          </div>
          <div>
            <p class="text-sm font-medium text-gray-600">URL Count</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ status.urlCount }}</p>
            <p class="text-xs text-gray-500 mt-1">Size: {{ status.fileSize || '0 KB' }}</p>
          </div>
        </div>
      </div>

      <!-- Возраст -->
      <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center">
          <div class="w-12 h-12 rounded-lg bg-purple-500 flex items-center justify-center mr-4">
            <ClockIcon class="w-6 h-6 text-white" />
          </div>
          <div>
            <p class="text-sm font-medium text-gray-600">Age</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ status.age || 'Unknown' }}</p>
            <p class="text-xs text-gray-500 mt-1">Time since last generation</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Основные действия -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
      <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
      <div class="flex flex-wrap gap-3">
        <button
          @click="generateSitemap"
          :disabled="isGenerating"
          class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <RefreshIcon class="w-4 h-4 mr-2" :class="{ 'animate-spin': isGenerating }" />
          {{ isGenerating ? 'Generating...' : 'Generate Sitemap' }}
        </button>

        <a
          :href="sitemapUrl"
          target="_blank"
          class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
        >
          <EyeIcon class="w-4 h-4 mr-2" />
          View Sitemap
        </a>

        <button
          @click="validateSitemap"
          class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
        >
          <CheckCircleIcon class="w-4 h-4 mr-2" />
          Validate
        </button>

        <button
          @click="submitToSearchEngines"
          class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors"
        >
          <UploadIcon class="w-4 h-4 mr-2" />
          Submit to Search Engines
        </button>
      </div>
    </div>

    <!-- Статус и информация -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Детали sitemap -->
      <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Sitemap Details</h2>

        <div class="space-y-4">
          <div v-if="validationResult">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Validation Result</h3>
            <div :class="validationResult.valid ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'"
                 class="p-3 rounded-lg">
              <div class="flex items-center">
                <CheckCircleIcon v-if="validationResult.valid" class="w-5 h-5 mr-2" />
                <XCircleIcon v-else class="w-5 h-5 mr-2" />
                <span class="font-medium">{{ validationResult.message }}</span>
              </div>
              <div v-if="validationResult.errors" class="mt-2 text-sm">
                <ul class="list-disc pl-5">
                  <li v-for="(error, index) in validationResult.errors" :key="index">
                    {{ error }}
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div class="border-t pt-4">
            <h3 class="text-lg font-medium text-gray-900 mb-2">File Information</h3>
            <dl class="grid grid-cols-2 gap-4">
              <div>
                <dt class="text-sm font-medium text-gray-500">Location</dt>
                <dd class="text-sm text-gray-900">{{ sitemapUrl }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500">Last Modified</dt>
                <dd class="text-sm text-gray-900">{{ status.lastModified || 'N/A' }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500">File Size</dt>
                <dd class="text-sm text-gray-900">{{ status.fileSize || '0 KB' }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500">URL Count</dt>
                <dd class="text-sm text-gray-900">{{ status.urlCount }}</dd>
              </div>
            </dl>
          </div>
        </div>
      </div>

      <!-- Последние действия -->
      <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Activity</h2>

        <div class="space-y-3">
          <div v-for="activity in recentActivities" :key="activity.id"
               class="flex items-start p-3 bg-gray-50 rounded-lg">
            <div class="flex-shrink-0 mt-1">
              <div :class="activity.iconColor"
                   class="w-8 h-8 rounded-full flex items-center justify-center">
                <component :is="activity.icon" class="w-4 h-4 text-white" />
              </div>
            </div>
            <div class="ml-3 flex-1">
              <p class="text-sm font-medium text-gray-900">{{ activity.title }}</p>
              <p class="text-sm text-gray-500">{{ activity.description }}</p>
              <p class="text-xs text-gray-400 mt-1">{{ activity.time }}</p>
            </div>
          </div>
        </div>

        <div class="mt-6 pt-6 border-t">
          <h3 class="text-lg font-medium text-gray-900 mb-3">Tools</h3>
          <div class="space-y-2">
            <button
              @click="createBackup"
              class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
            >
              <DownloadIcon class="w-4 h-4 mr-2" />
              Create Backup
            </button>

            <button
              @click="clearCache"
              class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors"
            >
              <TrashIcon class="w-4 h-4 mr-2" />
              Clear Cache
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Уведомления -->
    <div class="fixed bottom-4 right-4 space-y-2">
      <div
        v-for="notification in notifications"
        :key="notification.id"
        :class="notification.type === 'success' ? 'bg-green-500' :
                notification.type === 'error' ? 'bg-red-500' : 'bg-blue-500'"
        class="rounded-lg shadow-lg p-4 text-white max-w-sm"
      >
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <CheckCircleIcon v-if="notification.type === 'success'" class="w-5 h-5" />
            <XCircleIcon v-else-if="notification.type === 'error'" class="w-5 h-5" />
            <InformationCircleIcon v-else class="w-5 h-5" />
          </div>
          <div class="ml-3 w-0 flex-1">
            <p class="text-sm font-medium">{{ notification.title }}</p>
            <p class="text-sm opacity-90">{{ notification.message }}</p>
          </div>
          <button
            @click="removeNotification(notification.id)"
            class="ml-4 flex-shrink-0 rounded-md opacity-70 hover:opacity-100"
          >
            <XIcon class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'
import {
  CheckCircleIcon,
  XCircleIcon,
  LinkIcon,
  ClockIcon,
  EyeIcon,
  RefreshIcon,
  UploadIcon,
  DownloadIcon,
  TrashIcon,
  InformationCircleIcon,
  XIcon
} from '@heroicons/vue/outline'

// Состояние
const isGenerating = ref(false)
const status = reactive({
  exists: false,
  lastModified: null,
  urlCount: 0,
  fileSize: '0 KB',
  age: 'Unknown'
})

const validationResult = ref(null)
const notifications = ref([])
const notificationId = ref(0)

// URL
const sitemapUrl = `${window.location.origin}/sitemap.xml`

// Недавние действия
const recentActivities = ref([
  {
    id: 1,
    title: 'Sitemap Status Checked',
    description: 'System checked sitemap status',
    time: 'Just now',
    icon: 'CheckCircleIcon',
    iconColor: 'bg-blue-500'
  }
])

// Методы
const showNotification = (type, title, message) => {
  const id = ++notificationId.value
  notifications.value.push({ id, type, title, message })
  setTimeout(() => removeNotification(id), 5000)
}

const removeNotification = (id) => {
  notifications.value = notifications.value.filter(n => n.id !== id)
}

const fetchStatus = async () => {
  try {
    const response = await axios.get('/api/sitemap/status')
    Object.assign(status, response.data)
  } catch (error) {
    console.error('Failed to fetch sitemap status:', error)
  }
}

const generateSitemap = async () => {
  if (isGenerating.value) return

  isGenerating.value = true

  try {
    const response = await axios.post('/api/sitemap/generate')

    if (response.data.success) {
      showNotification('success', 'Success', 'Sitemap generated successfully')

      // Обновляем статус
      await fetchStatus()

      // Добавляем активность
      recentActivities.value.unshift({
        id: Date.now(),
        title: 'Sitemap Generated',
        description: 'Sitemap was regenerated',
        time: 'Just now',
        icon: 'RefreshIcon',
        iconColor: 'bg-green-500'
      })
    } else {
      showNotification('error', 'Error', response.data.message)
    }
  } catch (error) {
    showNotification('error', 'Error', error.response?.data?.message || 'Failed to generate sitemap')
  } finally {
    isGenerating.value = false
  }
}

const validateSitemap = async () => {
  try {
    const response = await axios.post('/api/sitemap/validate')
    validationResult.value = response.data

    if (response.data.valid) {
      showNotification('success', 'Valid', 'Sitemap is valid')
    } else {
      showNotification('error', 'Invalid', 'Sitemap has validation errors')
    }
  } catch (error) {
    showNotification('error', 'Error', 'Failed to validate sitemap')
  }
}

const submitToSearchEngines = async () => {
  try {
    const response = await axios.post('/api/sitemap/submit')

    if (response.data.success) {
      showNotification('success', 'Submitted', 'Sitemap submitted to search engines')

      // Добавляем активность
      recentActivities.value.unshift({
        id: Date.now(),
        title: 'Submitted to Search Engines',
        description: 'Sitemap was submitted to Google and Bing',
        time: 'Just now',
        icon: 'UploadIcon',
        iconColor: 'bg-purple-500'
      })
    }
  } catch (error) {
    showNotification('error', 'Error', error.response?.data?.message || 'Failed to submit to search engines')
  }
}

const createBackup = async () => {
  try {
    const response = await axios.post('/api/sitemap/backup')

    if (response.data.success) {
      showNotification('success', 'Backup Created', 'Sitemap backup created successfully')

      // Скачиваем backup
      window.open(response.data.backup_url, '_blank')
    }
  } catch (error) {
    showNotification('error', 'Error', error.response?.data?.message || 'Failed to create backup')
  }
}

const clearCache = async () => {
  try {
    const response = await axios.post('/api/sitemap/clear-cache')

    if (response.data.success) {
      showNotification('success', 'Cache Cleared', 'Cache cleared successfully')
    }
  } catch (error) {
    showNotification('error', 'Error', error.response?.data?.message || 'Failed to clear cache')
  }
}

// Хуки жизненного цикла
onMounted(() => {
  fetchStatus()

  // Обновляем статус каждые 30 секунд
  setInterval(fetchStatus, 30000)
})
</script>
