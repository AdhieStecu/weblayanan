<template>
  <div class="page-container">
    <header class="app-header">
      <div class="header-inner">
        <div class="brand">
          <div class="brand-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
              <path d="M6 12v5c3 3 9 3 12 0v-5"/>
            </svg>
          </div>
          <div>
            <h1 class="brand-title">Web Layanan Mahasiswa</h1>
            <p class="brand-subtitle">Frontend Nuxt 4 &bull; Backend Laravel 12 API</p>
          </div>
        </div>

        <div class="connection-badge" :class="connectionState">
          <span class="status-dot"></span>
          <span class="status-text">{{ connectionLabel }}</span>
        </div>
      </div>
    </header>

    <main class="main-content">
      <!-- Status Card -->
      <section class="card status-card">
        <div class="card-header">
          <div>
            <h2 class="card-title">Status Komunikasi API</h2>
            <p class="card-desc">Memverifikasi pertukaran data antara Nuxt Frontend dan Laravel Backend</p>
          </div>
          <button class="btn btn-primary" :disabled="isLoading" @click="checkConnection">
            <svg v-if="isLoading" class="spinner" viewBox="0 0 50 50">
              <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
            </svg>
            <span v-else>Cek Koneksi Ulang</span>
          </button>
        </div>

        <div class="info-grid">
          <div class="info-item">
            <span class="info-label">API Base URL</span>
            <span class="info-value font-mono">{{ config.public.apiBase }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Backend Host</span>
            <span class="info-value font-mono">{{ config.public.backendUrl }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Status HTTP</span>
            <span class="info-value" :class="connectionState === 'connected' ? 'text-success' : 'text-danger'">
              {{ pingData ? '200 OK (Terhubung)' : (hasError ? 'Gagal Terhubung' : 'Memeriksa...') }}
            </span>
          </div>
          <div class="info-item">
            <span class="info-label">Waktu Respons Server</span>
            <span class="info-value">{{ serverTimestamp || '-' }}</span>
          </div>
        </div>

        <div v-if="pingData" class="alert alert-success">
          <div class="alert-icon">✓</div>
          <div>
            <strong>Berhasil!</strong> {{ pingData.message }}
          </div>
        </div>

        <div v-if="hasError" class="alert alert-error">
          <div class="alert-icon">!</div>
          <div>
            <strong>Backend belum aktif atau tidak dapat dijangkau.</strong>
            <p>Jalankan backend Laravel di terminal dengan perintah: <code class="code-inline">php artisan serve</code> di dalam folder <code class="code-inline">beckend</code>.</p>
          </div>
        </div>
      </section>

      <!-- Layanan Mahasiswa Section (Fetched from Laravel API) -->
      <section class="card">
        <div class="card-header">
          <div>
            <h2 class="card-title">Daftar Layanan Mahasiswa (Data dari API)</h2>
            <p class="card-desc">Data diambil langsung dari endpoint Laravel <code class="code-inline">/api/layanan</code></p>
          </div>
          <span class="badge-pill">{{ servicesList.length }} Layanan Aktif</span>
        </div>

        <div v-if="isLoadingServices" class="loading-state">
          <p>Memuat data layanan dari backend...</p>
        </div>

        <div v-else-if="servicesList.length > 0" class="services-grid">
          <div v-for="item in servicesList" :key="item.id" class="service-item">
            <div class="service-category">{{ item.kategori }}</div>
            <h3 class="service-name">{{ item.nama }}</h3>
            <p class="service-desc">{{ item.deskripsi }}</p>
            <div class="service-footer">
              <span class="badge-status">{{ item.status }}</span>
              <button class="btn-sm">Ajukan</button>
            </div>
          </div>
        </div>

        <div v-else class="empty-state">
          <p>Belum ada data yang dimuat. Pastikan server backend sedang berjalan.</p>
        </div>
      </section>

      <!-- Panduan Panduan Integrasi -->
      <section class="card">
        <h2 class="card-title">Konfigurasi yang Telah Diterapkan</h2>
        <div class="steps-list">
          <div class="step-box">
            <span class="step-num">1</span>
            <div>
              <h4>Laravel API & CORS Setup</h4>
              <p>Diaktifkan rute API di <code class="code-inline">routes/api.php</code>, middleware stateful Sanctum di <code class="code-inline">bootstrap/app.php</code>, dan konfigurasi origin <code class="code-inline">http://localhost:3000</code> di <code class="code-inline">config/cors.php</code>.</p>
            </div>
          </div>

          <div class="step-box">
            <span class="step-num">2</span>
            <div>
              <h4>Frontend Environment & RuntimeConfig</h4>
              <p>Variabel <code class="code-inline">NUXT_PUBLIC_API_BASE</code> diatur di <code class="code-inline">.env</code> dan di-inject via <code class="code-inline">runtimeConfig</code> di <code class="code-inline">nuxt.config.ts</code>.</p>
            </div>
          </div>

          <div class="step-box">
            <span class="step-num">3</span>
            <div>
              <h4>Composable Reusable <code class="code-inline">useApi()</code></h4>
              <p>Disediakan di <code class="code-inline">app/composables/useApi.ts</code> yang otomatis menyertakan base URL, header JSON, dan credentials untuk autentikasi Sanctum.</p>
            </div>
          </div>
        </div>
      </section>
    </main>

    <footer class="app-footer">
      <p>&copy; 2026 Portal Layanan Mahasiswa &bull; Nuxt & Laravel API Communication</p>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'

const config = useRuntimeConfig()
const { apiFetch } = useApiClient()

const isLoading = ref(false)
const isLoadingServices = ref(false)
const hasError = ref(false)
const pingData = ref<any>(null)
const servicesList = ref<any[]>([])

const connectionState = computed(() => {
  if (isLoading.value) return 'connecting'
  if (pingData.value) return 'connected'
  return 'offline'
})

const connectionLabel = computed(() => {
  if (isLoading.value) return 'Menghubungkan...'
  if (pingData.value) return 'API Terhubung'
  return 'API Offline'
})

const serverTimestamp = computed(() => {
  if (!pingData.value?.server_time) return null
  return new Date(pingData.value.server_time).toLocaleString('id-ID')
})

const checkConnection = async () => {
  isLoading.value = true
  hasError.value = false
  try {
    const res = await apiFetch<any>('/ping')
    pingData.value = res
  } catch (err) {
    console.error('Koneksi backend gagal:', err)
    pingData.value = null
    hasError.value = true
  } finally {
    isLoading.value = false
  }
}

const fetchServices = async () => {
  isLoadingServices.value = true
  try {
    const res = await apiFetch<any>('/layanan')
    servicesList.value = res.data || []
  } catch (err) {
    console.error('Gagal mengambil data layanan:', err)
  } finally {
    isLoadingServices.value = false
  }
}

onMounted(() => {
  checkConnection()
  fetchServices()
})
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap');

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  background-color: #f8fafc;
  color: #1e293b;
  line-height: 1.6;
}

.font-mono {
  font-family: 'JetBrains Mono', monospace;
}

.page-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.app-header {
  background: #ffffff;
  border-bottom: 1px solid #e2e8f0;
  padding: 1.25rem 2rem;
  position: sticky;
  top: 0;
  z-index: 10;
}

.header-inner {
  max-width: 1100px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.brand {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.brand-icon {
  width: 44px;
  height: 44px;
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
  color: #ffffff;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
}

.brand-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: #0f172a;
}

.brand-subtitle {
  font-size: 0.85rem;
  color: #64748b;
}

.connection-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.4rem 0.9rem;
  border-radius: 9999px;
  font-size: 0.85rem;
  font-weight: 600;
  transition: all 0.2s ease;
}

.connection-badge.connected {
  background-color: #dcfce7;
  color: #15803d;
}

.connection-badge.offline {
  background-color: #fee2e2;
  color: #b91c1c;
}

.connection-badge.connecting {
  background-color: #fef3c7;
  color: #b45309;
}

.status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: currentColor;
}

.main-content {
  max-width: 1100px;
  width: 100%;
  margin: 2rem auto;
  padding: 0 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.75rem;
  flex: 1;
}

.card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 1.75rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.5rem;
  gap: 1rem;
  flex-wrap: wrap;
}

.card-title {
  font-size: 1.2rem;
  font-weight: 700;
  color: #0f172a;
}

.card-desc {
  font-size: 0.9rem;
  color: #64748b;
  margin-top: 0.25rem;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.6rem 1.25rem;
  border-radius: 8px;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  border: none;
  transition: all 0.2s;
}

.btn-primary {
  background-color: #2563eb;
  color: #ffffff;
}

.btn-primary:hover:not(:disabled) {
  background-color: #1d4ed8;
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-sm {
  padding: 0.4rem 0.85rem;
  font-size: 0.8rem;
  border-radius: 6px;
  background-color: #eff6ff;
  color: #2563eb;
  border: 1px solid #bfdbfe;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-sm:hover {
  background-color: #2563eb;
  color: #ffffff;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1rem;
  margin-bottom: 1.25rem;
}

.info-item {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  padding: 0.9rem 1rem;
  border-radius: 10px;
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}

.info-label {
  font-size: 0.75rem;
  text-transform: uppercase;
  color: #64748b;
  font-weight: 600;
  letter-spacing: 0.05em;
}

.info-value {
  font-size: 0.95rem;
  font-weight: 600;
  color: #0f172a;
}

.text-success {
  color: #16a34a;
}

.text-danger {
  color: #dc2626;
}

.code-inline {
  background-color: #f1f5f9;
  color: #0f172a;
  padding: 0.15rem 0.4rem;
  border-radius: 4px;
  font-size: 0.85em;
  font-family: 'JetBrains Mono', monospace;
}

.alert {
  display: flex;
  gap: 0.75rem;
  padding: 1rem 1.25rem;
  border-radius: 10px;
  font-size: 0.92rem;
  margin-top: 1rem;
}

.alert-success {
  background-color: #f0fdf4;
  border: 1px solid #bbf7d0;
  color: #166534;
}

.alert-error {
  background-color: #fef2f2;
  border: 1px solid #fecaca;
  color: #991b1b;
}

.alert-icon {
  font-weight: bold;
  font-size: 1.1rem;
}

.services-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.25rem;
}

.service-item {
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 1.25rem;
  background-color: #ffffff;
  display: flex;
  flex-direction: column;
  transition: transform 0.2s, box-shadow 0.2s;
}

.service-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
}

.service-category {
  font-size: 0.75rem;
  font-weight: 600;
  color: #2563eb;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 0.5rem;
}

.service-name {
  font-size: 1.05rem;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 0.5rem;
}

.service-desc {
  font-size: 0.875rem;
  color: #64748b;
  flex: 1;
  margin-bottom: 1rem;
}

.service-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 0.75rem;
  border-top: 1px solid #f1f5f9;
}

.badge-status {
  font-size: 0.75rem;
  font-weight: 600;
  padding: 0.25rem 0.6rem;
  border-radius: 6px;
  background-color: #f0fdf4;
  color: #166534;
}

.badge-pill {
  font-size: 0.8rem;
  background-color: #f1f5f9;
  color: #475569;
  font-weight: 600;
  padding: 0.35rem 0.85rem;
  border-radius: 9999px;
}

.steps-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-top: 1rem;
}

.step-box {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
  padding: 1rem;
  border-radius: 10px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
}

.step-num {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background-color: #2563eb;
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.85rem;
  font-weight: bold;
  flex-shrink: 0;
}

.step-box h4 {
  font-size: 0.95rem;
  font-weight: 600;
  color: #0f172a;
  margin-bottom: 0.2rem;
}

.step-box p {
  font-size: 0.875rem;
  color: #64748b;
}

.empty-state, .loading-state {
  text-align: center;
  padding: 2.5rem 1rem;
  color: #64748b;
  font-size: 0.95rem;
}

.app-footer {
  text-align: center;
  padding: 1.5rem;
  font-size: 0.85rem;
  color: #94a3b8;
  border-top: 1px solid #e2e8f0;
  background: #ffffff;
}

.spinner {
  animation: rotate 1.5s linear infinite;
  width: 18px;
  height: 18px;
}

.spinner .path {
  stroke: #ffffff;
  stroke-linecap: round;
  animation: dash 1.5s ease-in-out infinite;
}

@keyframes rotate {
  100% {
    transform: rotate(360deg);
  }
}

@keyframes dash {
  0% {
    stroke-dasharray: 1, 150;
    stroke-dashoffset: 0;
  }
  50% {
    stroke-dasharray: 90, 150;
    stroke-dashoffset: -35;
  }
  100% {
    stroke-dasharray: 90, 150;
    stroke-dashoffset: -124;
  }
}
</style>
