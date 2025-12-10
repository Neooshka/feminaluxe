<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Sitemap Management</h1>
      <p class="text-gray-600 mt-2">Manage and monitor your website's sitemap</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <StatCard
        title="Sitemap Status"
        :value="status.exists ? 'Active' : 'Missing'"
        :icon="status.exists ? 'CheckCircleIcon' : 'XCircleIcon'"
        :color="status.exists ? 'green' : 'red'"
        :subtitle="`Last updated: ${status.lastModified || 'Never'}`"
      />

      <StatCard
        title="URL Count"
        :value="status.urlCount?.toString() || '0'"
        icon="LinkIcon"
        color="blue"
        :subtitle="`Size: ${status.fileSize || '0 KB'}`"
      />

      <StatCard
        title="Age"
        :value="status.age || 'Unknown'"
        icon="ClockIcon"
        color="purple"
        subtitle="Time since last generation"
      />

      <StatCard
        title="Next Generation"
        :value="nextGenerationTime"
        icon="CalendarIcon"
        color="yellow"
        subtitle="Scheduled automatic update"
      />
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left Column: Actions & Quick Stats -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Actions Panel -->
        <div class="bg-white rounded-xl shadow p-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">Actions</h2>

          <div class="flex flex-wrap gap-3 mb-6">
            <button
              @click="generateSitemap"
              :disabled="isGenerating"
              class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <RefreshIcon class="w-4 h-4 mr-2" :class="{ 'animate-spin': isGenerating }" />
              {{ isGenerating ? 'Generating...' : 'Generate Now' }}
            </button>

            <a
              :href="sitemapUrl"
              target="_blank"
              class="inline-flex items-center px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
            >
              <EyeIcon class="w-4 h-4 mr-2" />
              View Sitemap
            </a>

            <a
              :href="robotsUrl"
              target="_blank"
              class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
            >
              <CogIcon class="w-4 h-4 mr-2" />
              View Robots.txt
            </a>

            <button
              @click="clearCache"
              :disabled="isClearingCache"
              class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <TrashIcon class="w-4 h-4 mr-2" :class="{ 'animate-spin': isClearingCache }" />
              {{ isClearingCache ? 'Clearing...' : 'Clear Cache' }}
            </button>
          </div>

          <!-- Real-time Progress -->
          <div v-if="generationProgress.show" class="mb-6">
            <div class="flex justify-between mb-2">
              <span class="text-sm font-medium text-gray-700">Generation Progress</span>
              <span class="text-sm font-medium text-gray-900">{{ generationProgress.percentage }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
              <div
                class="bg-blue-600 h-2.5 rounded-full transition-all duration-300"
                :style="{ width: `${generationProgress.percentage}%` }"
              ></div>
            </div>
            <p class="text-sm text-gray-600 mt-2">{{ generationProgress.message }}</p>
          </div>

          <!-- Recent Activity -->
          <div>
            <h3 class="text-lg font-medium text-gray-900 mb-3">Recent Activity</h3>
            <div class="space-y-3">
              <div
                v-for="activity in recentActivities"
                :key="activity.id"
                class="flex items-start p-3 bg-gray-50 rounded-lg"
              >
                <div class="flex-shrink-0 mt-1">
                  <div :class="activity.iconColor" class="w-8 h-8 rounded-full flex items-center justify-center">
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
          </div>
        </div>

        <!-- Statistics by Content Type -->
        <div class="bg-white rounded-xl shadow p-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">Content Statistics</h2>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead>
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Content Type
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Included URLs
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Last Updated
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="stat in stats" :key="stat.type">
                  <td class="px-4 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div :class="stat.iconBgColor" class="w-8 h-8 rounded-lg flex items-center justify-center mr-3">
                        <component :is="stat.icon" class="w-4 h-4 text-white" />
                      </div>
                      <span class="text-sm font-medium text-gray-900">{{ stat.type }}</span>
                    </div>
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap">
                    <span class="text-sm text-gray-900">{{ stat.count }}</span>
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap">
                    <span
                      :class="stat.active ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                      class="px-2 py-1 text-xs font-medium rounded-full"
                    >
                      {{ stat.active ? 'Enabled' : 'Disabled' }}
                    </span>
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ stat.lastUpdated || 'Never' }}
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap text-sm">
                    <button
                      @click="toggleContentType(stat.type)"
                      :class="stat.active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900'"
                      class="font-medium"
                    >
                      {{ stat.active ? 'Disable' : 'Enable' }}
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Right Column: Settings & Tools -->
      <div class="space-y-6">
        <!-- Sitemap Settings -->
        <div class="bg-white rounded-xl shadow p-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">Sitemap Settings</h2>

          <div class="space-y-4">
            <div>
              <label class="flex items-center">
                <input
                  type="checkbox"
                  v-model="settings.autoGenerate"
                  class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                />
                <span class="ml-2 text-sm text-gray-700">Auto-generate on content changes</span>
              </label>
            </div>

            <div>
              <label class="flex items-center">
                <input
                  type="checkbox"
                  v-model="settings.submitToGoogle"
                  class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                />
                <span class="ml-2 text-sm text-gray-700">Auto-submit to Google Search Console</span>
              </label>
            </div>

            <div>
              <label class="flex items-center">
                <input
                  type="checkbox"
                  v-model="settings.enableCaching"
                  class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                />
                <span class="ml-2 text-sm text-gray-700">Enable caching ({{ settings.cacheDuration }} minutes)</span>
              </label>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Update Frequency</label>
              <select
                v-model="settings.updateFrequency"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              >
                <option value="hourly">Every Hour</option>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
              </select>
            </div>

            <button
              @click="saveSettings"
              :disabled="isSavingSettings"
              class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <SaveIcon class="w-4 h-4 mr-2" :class="{ 'animate-spin': isSavingSettings }" />
              {{ isSavingSettings ? 'Saving...' : 'Save Settings' }}
            </button>
          </div>
        </div>

        <!-- Quick Tools -->
        <div class="bg-white rounded-xl shadow p-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Tools</h2>

          <div class="space-y-3">
            <button
              @click="validateSitemap"
              class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors"
            >
              <CheckCircleIcon class="w-4 h-4 mr-2" />
              Validate Sitemap
            </button>

            <button
              @click="submitToSearchEngines"
              class="w-full inline-flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-colors"
            >
              <UploadIcon class="w-4 h-4 mr-2" />
              Submit to Search Engines
            </button>

            <button
              @click="downloadBackup"
              class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
            >
              <DownloadIcon class="w-4 h-4 mr-2" />
              Download Backup
            </button>
          </div>
        </div>

        <!-- Scheduled Jobs -->
        <div class="bg-white rounded-xl shadow p-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">Scheduled Jobs</h2>

          <div class="space-y-3">
            <div
              v-for="job in scheduledJobs"
              :key="job.id"
              class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
            >
              <div>
                <p class="text-sm font-medium text-gray-900">{{ job.name }}</p>
                <p class="text-xs text-gray-500">{{ job.schedule }}</p>
              </div>
              <span
                :class="job.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                class="px-2 py-1 text-xs font-medium rounded-full"
              >
                {{ job.status }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Notifications -->
    <div class="fixed bottom-4 right-4 space-y-2">
      <Transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-for="notification in notifications"
          :key="notification.id"
          :class="notification.type === 'success' ? 'bg-green-500' : notification.type === 'error' ? 'bg-red-500' : 'bg-blue-500'"
          class="rounded-lg shadow-lg p-4 text-white max-w-sm"
        >
          <div class="flex items-start">
            <div class="flex-shrink-0">
              <component
                :is="notification.type === 'success' ? CheckCircleIcon : notification.type === 'error' ? XCircleIcon : InformationCircleIcon"
                class="w-5 h-5"
              />
            </div>
            <div class="ml-3 w-0 flex-1">
              <p class="text-sm font-medium">{{ notification.title }}</p>
              <p class="text-sm opacity-90">{{ notification.message }}</p>
            </div>
            <button
              @click="removeNotification(notification.id)"
              class="ml-4 flex-shrink-0 rounded-md opacity-70 hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-white"
            >
              <span class="sr-only">Close</span>
              <XIcon class="w-4 h-4" />
            </button>
          </div>
        </div>
      </Transition>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import axios from 'axios'
import {
  CheckCircleIcon,
  XCircleIcon,
  LinkIcon,
  ClockIcon,
  CalendarIcon,
  EyeIcon,
  CogIcon,
  RefreshIcon,
  TrashIcon,
  SaveIcon,
  UploadIcon,
  DownloadIcon,
  InformationCircleIcon,
  XIcon,
  DocumentTextIcon,
  CollectionIcon,
  BookOpenIcon,
  TagIcon,
  ShoppingBagIcon
} from '@heroicons/vue/outline'

// Component state
const isGenerating = ref(false)
const isClearingCache = ref(false)
const isSavingSettings = ref(false)

// Status data
const status = reactive({
  exists: false,
  lastModified: null,
  urlCount: 0,
  fileSize: '0 KB',
  age: 'Unknown'
})

// Statistics
const stats = ref([
  {
    type: 'Static Pages',
    count: 8,
    active: true,
    icon: DocumentTextIcon,
    iconBgColor: 'bg-blue-500',
    lastUpdated: '2 hours ago'
  },
  {
    type: 'Categories',
    count: 12,
    active: true,
    icon: CollectionIcon,
    iconBgColor: 'bg-green-500',
    lastUpdated: '1 hour ago'
  },
  {
    type: 'Blog Posts',
    count: 45,
    active: true,
    icon: BookOpenIcon,
    iconBgColor: 'bg-purple-500',
    lastUpdated: '30 minutes ago'
  },
  {
    type: 'Products',
    count: 23,
    active: true,
    icon: ShoppingBagIcon,
    iconBgColor: 'bg-yellow-500',
    lastUpdated: '5 hours ago'
  },
  {
    type: 'Tags',
    count: 67,
    active: false,
    icon: TagIcon,
    iconBgColor: 'bg-red-500',
    lastUpdated: '1 day ago'
  }
])

// Settings
const settings = reactive({
  autoGenerate: true,
  submitToGoogle: true,
  enableCaching: true,
  cacheDuration: 60,
  updateFrequency: 'daily'
})

// Generation progress
const generationProgress = reactive({
  show: false,
  percentage: 0,
  message: ''
})

// Notifications
const notifications = ref([])
const notificationId = ref(0)

// URLs
const sitemapUrl = `${window.location.origin}/sitemap.xml`
const robotsUrl = `${window.location.origin}/robots.txt`

// Scheduled jobs
const scheduledJobs = ref([
  { id: 1, name: 'Daily Sitemap Generation', schedule: 'Every day at 03:00', status: 'active' },
  { id: 2, name: 'Cache Cleanup', schedule: 'Every Sunday at 04:00', status: 'active' },
  { id: 3, name: 'Search Engine Submission', schedule: 'On generation', status: 'active' }
])

// Recent activities
const recentActivities = ref([
  {
    id: 1,
    title: 'Sitemap Generated',
    description: '45 URLs added successfully',
    time: '2 hours ago',
    icon: CheckCircleIcon,
    iconColor: 'bg-green-500'
  },
  {
    id: 2,
    title: 'New Blog Post',
    description: 'Added to sitemap automatically',
    time: '1 hour ago',
    icon: DocumentTextIcon,
    iconColor: 'bg-blue-500'
  },
  {
    id: 3,
    title: 'Cache Cleared',
    description: 'Sitemap cache refreshed',
    time: '3 hours ago',
    icon: TrashIcon,
    iconColor: 'bg-yellow-500'
  }
])

// Computed properties
const nextGenerationTime = computed(() => {
  const now = new Date()
  now.setHours(3, 0, 0, 0)
  if (now < new Date()) {
    now.setDate(now.getDate() + 1)
  }
  return now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
})

// Methods
const showNotification = (type, title, message) => {
  const id = ++notificationId.value
  notifications.value.push({ id, type, title, message })
  setTimeout(() => removeNotification(id), 5000)
}

const removeNotification = (id) => {
  notifications.value = notifications.value.filter(n => n.id !== id)
}

const generateSitemap = async () => {
  if (isGenerating.value) return

  isGenerating.value = true
  generationProgress.show = true
  generationProgress.percentage = 0
  generationProgress.message = 'Starting generation...'

  try {
    // Simulate progress updates
    const progressInterval = setInterval(() => {
      if (generationProgress.percentage < 100) {
        generationProgress.percentage += 10
        generationProgress.message = `Generating... ${generationProgress.percentage}% complete`
      }
    }, 500)

    // API call
    const response = await axios.post('/api/sitemap/generate')

    clearInterval(progressInterval)
    generationProgress.percentage = 100
    generationProgress.message = 'Generation complete!'

    showNotification('success', 'Success', 'Sitemap generated successfully')

    // Update status
    await fetchStatus()

    // Hide progress after 2 seconds
    setTimeout(() => {
      generationProgress.show = false
    }, 2000)

  } catch (error) {
    showNotification('error', 'Error', error.response?.data?.message || 'Failed to generate sitemap')
    generationProgress.message = 'Generation failed'
  } finally {
    isGenerating.value = false
  }
}

const clearCache = async () => {
  if (isClearingCache.value) return

  isClearingCache.value = true

  try {
    await axios.post('/api/sitemap/clear-cache')
    showNotification('success', 'Success', 'Cache cleared successfully')
  } catch (error) {
    showNotification('error', 'Error', error.response?.data?.message || 'Failed to clear cache')
  } finally {
    isClearingCache.value = false
  }
}

const saveSettings = async () => {
  isSavingSettings.value = true

  try {
    await axios.put('/api/sitemap/settings', settings)
    showNotification('success', 'Success', 'Settings saved successfully')
  } catch (error) {
    showNotification('error', 'Error', error.response?.data?.message || 'Failed to save settings')
  } finally {
    isSavingSettings.value = false
  }
}

const toggleContentType = (type) => {
  const stat = stats.value.find(s => s.type === type)
  if (stat) {
    stat.active = !stat.active
    showNotification('info', 'Updated', `${type} ${stat.active ? 'enabled' : 'disabled'}`)
  }
}

const validateSitemap = async () => {
  try {
    const response = await axios.post('/api/sitemap/validate')
    showNotification('success', 'Validated', 'Sitemap validation complete')
  } catch (error) {
    showNotification('error', 'Error', 'Sitemap validation failed')
  }
}

const submitToSearchEngines = async () => {
  try {
    const response = await axios.post('/api/sitemap/submit')
    showNotification('success', 'Submitted', 'Sitemap submitted to search engines')
  } catch (error) {
    showNotification('error', 'Error', 'Failed to submit to search engines')
  }
}

const downloadBackup = async () => {
  try {
    const response = await axios.get('/api/sitemap/backup', { responseType: 'blob' })
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `sitemap-backup-${new Date().toISOString().split('T')[0]}.xml`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    showNotification('success', 'Downloaded', 'Backup downloaded successfully')
  } catch (error) {
    showNotification('error', 'Error', 'Failed to download backup')
  }
}

const fetchStatus = async () => {
  try {
    const response = await axios.get('/api/sitemap/status')
    Object.assign(status, response.data)
  } catch (error) {
    console.error('Failed to fetch sitemap status:', error)
  }
}

// Lifecycle hooks
onMounted(() => {
  fetchStatus()

  // Poll for status updates every 30 seconds
  setInterval(fetchStatus, 30000)
})
</script>
