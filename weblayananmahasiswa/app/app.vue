<template>
  <div class="app-layout" :data-theme="currentThemeId" @mousemove="handleMouseMove">
    <!-- Habit Dynamic Animated Background System -->
    <div class="habit-background-stage" aria-hidden="true">
      <!-- Habit Ambient Image Layer -->
      <div
        class="habit-bg-image"
        :style="{
          backgroundImage: `url('${activeWallpaperUrl}')`,
          filter: `blur(${wallpaperBlur}px) brightness(0.88) contrast(1.08)`
        }"
      ></div>

      <!-- Atmospheric Darkness Scrim: GUARANTEES contrast and zero 'warna nyaru' -->
      <div
        class="habit-scrim-overlay"
        :style="{ opacity: wallpaperDarkness }"
      ></div>

      <!-- Interactive Dynamic Mouse Spotlight Glow -->
      <div
        v-if="spotlightEnabled"
        class="habit-mouse-spotlight"
        :style="{
          background: `radial-gradient(650px circle at ${mouseX}px ${mouseY}px, ${activeThemeConfig.spotlightGlow}, transparent 70%)`
        }"
      ></div>

      <!-- Fluid Floating Glow Orbs (Dynamic to active theme) -->
      <div class="dynamic-orb orb-1"></div>
      <div class="dynamic-orb orb-2"></div>
      <div class="dynamic-orb orb-3"></div>
      <div class="dynamic-orb orb-pulse"></div>

      <!-- Subtle Cyber Grid Mesh -->
      <div class="habit-grid-mesh"></div>

      <!-- Ambient Floating Sparks / Particles -->
      <div v-if="particlesEnabled" class="habit-particles">
        <span
          v-for="p in particles"
          :key="p.id"
          class="habit-particle"
          :style="{
            left: p.left,
            top: p.top,
            width: p.size,
            height: p.size,
            animationDuration: p.duration,
            animationDelay: p.delay,
            opacity: p.opacity
          }"
        ></span>
      </div>

      <!-- Vignette Atmosphere Overlay -->
      <div class="habit-vignette"></div>
    </div>

    <!-- Floating Toast Notifications -->
    <div class="toast-container">
      <transition-group name="toast">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          class="toast-card"
          :class="'toast-' + toast.type"
        >
          <div class="toast-icon">
            <span v-if="toast.type === 'success'">✓</span>
            <span v-else-if="toast.type === 'error'">✕</span>
            <span v-else>ℹ</span>
          </div>
          <div class="toast-content">
            <div class="toast-title">{{ toast.title }}</div>
            <div class="toast-message">{{ toast.message }}</div>
          </div>
          <button class="toast-close" @click="removeToast(toast.id)">×</button>
        </div>
      </transition-group>
    </div>

    <!-- Header Navbar -->
    <header class="app-navbar">
      <div class="navbar-container">
        <div class="brand-group">
          <div class="brand-logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
              <path d="M6 12v5c3 3 9 3 12 0v-5"/>
            </svg>
          </div>
          <div class="brand-text">
            <div class="brand-title">SiLayanan <span class="brand-highlight">Mahasiswa</span></div>
            <div class="brand-subtitle">Portal Layanan Terpadu &bull; Nuxt 4 + Laravel 13 &bull; Habit Edition</div>
          </div>
        </div>

        <div class="navbar-actions">
          <!-- Dynamic Background & Wallpaper Toggle Button -->
          <button
            class="theme-tag-btn"
            title="Ganti Wallpaper & Tema Tampilan"
            @click="showWallpaperModal = true"
          >
            <span class="theme-dot"></span>
            <span class="theme-name">{{ activeThemeConfig.name }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="theme-chevron">
              <path d="m6 9 6 6 6-6"/>
            </svg>
          </button>

          <!-- Backend Connection Badge -->
          <div class="status-indicator" :class="connectionStatus">
            <span class="status-ping-dot"></span>
            <span class="status-label">{{ connectionText }}</span>
            <span v-if="pingLatency !== null" class="latency-badge">{{ pingLatency }}ms</span>
            <button class="btn-icon-refresh" title="Cek ulang koneksi API" @click="checkBackendHealth">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" :class="{ 'spin-anim': isCheckingHealth }">
                <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
              </svg>
            </button>
          </div>

          <!-- Auth Buttons / User Pill -->
          <template v-if="isAuthenticated && user">
            <div class="user-menu-group">
              <button class="user-profile-btn" title="Klik untuk edit profil mahasiswa" @click="openProfileModal()">
                <div class="user-avatar-circle">
                  {{ (user.name || 'M').charAt(0).toUpperCase() }}
                </div>
                <div class="user-info-text">
                  <span class="user-name-display">{{ user.name }}</span>
                  <span class="user-nim-display">{{ user.nim || 'Mahasiswa' }}</span>
                </div>
              </button>

              <button class="btn-logout-icon" title="Logout dari sistem" @click="handleLogout">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                  <polyline points="16 17 21 12 16 7"></polyline>
                  <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
              </button>
            </div>
          </template>
          <template v-else>
            <div class="user-menu-group">
              <button class="btn-auth-outline" @click="showLoginModal = true">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                  <polyline points="10 17 15 12 10 7"></polyline>
                  <line x1="15" y1="12" x2="3" y2="12"></line>
                </svg>
                <span>Masuk</span>
              </button>
              <button class="btn-auth-solid" @click="showRegisterModal = true">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="8.5" cy="7" r="4"></circle>
                  <line x1="20" y1="8" x2="20" y2="14"></line>
                  <line x1="23" y1="11" x2="17" y2="11"></line>
                </svg>
                <span>Daftar</span>
              </button>
            </div>
          </template>

          <button class="btn btn-primary btn-glow" @click="openCreateModal()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Buat Pengajuan</span>
          </button>
        </div>
      </div>
    </header>

    <!-- Main Container -->
    <main class="main-wrapper">
      <!-- Hero Banner / Overview -->
      <section class="hero-section">
        <div class="hero-content">
          <div class="hero-badge-container">
            <span class="hero-badge">
              <span class="badge-pulse-dot"></span>
              Portal Administrasi Akademik Digital
            </span>
          </div>
          <h1 class="hero-title">
            Pengajuan Layanan Mahasiswa Lebih <span class="highlight-emerald">Cepat</span>, <span class="highlight-emerald">Transparan</span> & Otomatis
          </h1>
          <p class="hero-desc">
            Kelola seluruh permohonan surat keterangan aktif, rekomendasi magang, legalisir, hingga pengajuan cuti secara online dengan sistem pelacakan tiket langsung dari backend database.
          </p>
        </div>

        <!-- Quick Stats Grid -->
        <div class="stats-grid">
          <div class="stat-card stat-total">
            <div class="stat-icon-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
              </svg>
            </div>
            <div class="stat-info">
              <span class="stat-number">{{ stats.total }}</span>
              <span class="stat-name">Total Permohonan</span>
            </div>
          </div>

          <div class="stat-card stat-pending">
            <div class="stat-icon-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
            </div>
            <div class="stat-info">
              <span class="stat-number">{{ stats.menunggu }}</span>
              <span class="stat-name">Menunggu Review</span>
            </div>
          </div>

          <div class="stat-card stat-process">
            <div class="stat-icon-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path>
              </svg>
            </div>
            <div class="stat-info">
              <span class="stat-number">{{ stats.diproses }}</span>
              <span class="stat-name">Sedang Diproses</span>
            </div>
          </div>

          <div class="stat-card stat-completed">
            <div class="stat-icon-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
              </svg>
            </div>
            <div class="stat-info">
              <span class="stat-number">{{ stats.selesai }}</span>
              <span class="stat-name">Selesai / Terbit</span>
            </div>
          </div>
        </div>
      </section>

      <!-- Tab Navigation Switcher -->
      <div class="tabs-nav-bar">
        <div class="tabs-list">
          <button
            class="tab-btn"
            :class="{ active: activeTab === 'crud' }"
            @click="activeTab = 'crud'"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="3" width="7" height="7"></rect>
              <rect x="14" y="3" width="7" height="7"></rect>
              <rect x="14" y="14" width="7" height="7"></rect>
              <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            <span>Manajemen Pengajuan (CRUD)</span>
            <span class="tab-counter">{{ pengajuanList.length }}</span>
          </button>

          <button
            class="tab-btn"
            :class="{ active: activeTab === 'katalog' }"
            @click="activeTab = 'katalog'"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
              <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
            <span>Katalog Master Layanan</span>
            <span class="tab-counter">{{ masterLayanan.length }}</span>
          </button>

          <button
            class="tab-btn"
            :class="{ active: activeTab === 'tracking' }"
            @click="activeTab = 'tracking'"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <span>Lacak Status Tiket</span>
          </button>

          <button
            class="tab-btn"
            :class="{ active: activeTab === 'profile' }"
            @click="openProfileTab"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <span>Profil Mahasiswa</span>
            <span v-if="isAuthenticated && user" class="tab-counter" style="background: rgba(16, 185, 129, 0.2); color: #34d399;">✓</span>
          </button>
        </div>

        <button class="btn btn-outline-primary btn-sm" @click="refreshAllData">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :class="{ 'spin-anim': isLoadingList }">
            <polyline points="23 4 23 10 17 10"></polyline>
            <polyline points="1 20 1 14 7 14"></polyline>
            <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
          </svg>
          <span>Refresh Data</span>
        </button>
      </div>

      <!-- TAB 1: CRUD MANAJEMEN PENGAJUAN -->
      <section v-if="activeTab === 'crud'" class="tab-pane">
        <!-- Filter and Search Toolbar -->
        <div class="toolbar-card">
          <div class="search-box">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="search-icon">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari berdasarkan NIM, Nama Mahasiswa, atau Kode Tiket..."
              class="search-input"
              @input="onSearchChange"
            />
            <button v-if="searchQuery" class="clear-search-btn" @click="searchQuery = ''; loadPengajuan()">×</button>
          </div>

          <div class="filter-group">
            <div class="select-wrapper">
              <label class="filter-label">Status:</label>
              <select v-model="selectedStatus" class="select-control" @change="loadPengajuan">
                <option value="semua">Semua Status</option>
                <option value="menunggu">Menunggu</option>
                <option value="diproses">Diproses</option>
                <option value="selesai">Selesai</option>
                <option value="ditolak">Ditolak</option>
              </select>
            </div>

            <div class="select-wrapper">
              <label class="filter-label">Layanan:</label>
              <select v-model="selectedJenisLayanan" class="select-control" @change="loadPengajuan">
                <option value="semua">Semua Layanan</option>
                <option v-for="lay in masterLayanan" :key="lay.id" :value="lay.nama">
                  {{ lay.nama }}
                </option>
              </select>
            </div>

            <div class="view-toggle">
              <button
                class="view-btn"
                :class="{ active: viewMode === 'grid' }"
                title="Tampilan Grid Card"
                @click="viewMode = 'grid'"
              >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="3" width="7" height="7"></rect>
                  <rect x="14" y="3" width="7" height="7"></rect>
                  <rect x="14" y="14" width="7" height="7"></rect>
                  <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
              </button>
              <button
                class="view-btn"
                :class="{ active: viewMode === 'table' }"
                title="Tampilan Tabel"
                @click="viewMode = 'table'"
              >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="8" y1="6" x2="21" y2="6"></line>
                  <line x1="8" y1="12" x2="21" y2="12"></line>
                  <line x1="8" y1="18" x2="21" y2="18"></line>
                  <line x1="3" y1="6" x2="3.01" y2="6"></line>
                  <line x1="3" y1="12" x2="3.01" y2="12"></line>
                  <line x1="3" y1="18" x2="3.01" y2="18"></line>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="isLoadingList" class="loading-container">
          <div class="loading-spinner"></div>
          <p class="loading-text">Memuat data pengajuan dari database Laravel...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="pengajuanList.length === 0" class="empty-box">
          <div class="empty-icon-circle">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
              <polyline points="14 2 14 8 20 8"></polyline>
              <line x1="9" y1="15" x2="15" y2="15"></line>
            </svg>
          </div>
          <h3 class="empty-title">Tidak ada permohonan yang ditemukan</h3>
          <p class="empty-desc">
            {{ searchQuery || selectedStatus !== 'semua' || selectedJenisLayanan !== 'semua'
              ? 'Coba ganti filter atau kata kunci pencarian Anda.'
              : 'Belum ada data pengajuan dalam database. Klik tombol di bawah untuk membuat pengajuan pertama!' }}
          </p>
          <button class="btn btn-primary" @click="openCreateModal()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Buat Pengajuan Baru</span>
          </button>
        </div>

        <!-- GRID CARD VIEW -->
        <div v-else-if="viewMode === 'grid'" class="cards-grid">
          <div
            v-for="item in pengajuanList"
            :key="item.id"
            class="pengajuan-card"
            :class="'card-border-' + item.status"
          >
            <!-- Card Header -->
            <div class="card-top">
              <div class="ticket-badge" @click="copyTicket(item.kode_tiket)" title="Klik untuk menyalin nomor tiket">
                <span class="font-mono">{{ item.kode_tiket }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                  <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                </svg>
              </div>

              <!-- Status Badge -->
              <span class="status-pill" :class="'status-' + item.status">
                <span class="status-dot"></span>
                <span>{{ formatStatus(item.status) }}</span>
              </span>
            </div>

            <!-- Service & Applicant Info -->
            <div class="card-body">
              <h3 class="service-title-text">{{ item.jenis_layanan }}</h3>

              <div class="student-profile">
                <div class="avatar-circle">{{ item.nama_mahasiswa.charAt(0).toUpperCase() }}</div>
                <div class="profile-text">
                  <div class="student-name">{{ item.nama_mahasiswa }}</div>
                  <div class="student-meta font-mono">{{ item.nim }} &bull; {{ item.program_studi }} (Smtr {{ item.semester }})</div>
                </div>
              </div>

              <div class="keperluan-preview">
                <span class="keperluan-label">Keperluan:</span>
                <p class="keperluan-text">{{ item.keperluan }}</p>
              </div>

              <!-- Petugas Note if available -->
              <div v-if="item.catatan_petugas" class="note-box" :class="'note-' + item.status">
                <span class="note-icon">💬</span>
                <p class="note-text">{{ item.catatan_petugas }}</p>
              </div>
            </div>

            <!-- Card Footer -->
            <div class="card-footer-actions">
              <span class="submission-date">
                {{ formatDate(item.tanggal_pengajuan || item.created_at) }}
              </span>

              <div class="action-buttons-group">
                <button
                  class="action-btn action-view"
                  title="Lihat Detail Lengkap"
                  @click="openDetailModal(item)"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                </button>

                <button
                  class="action-btn action-edit"
                  title="Ubah Data / Update Status"
                  @click="openEditModal(item)"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                  </svg>
                </button>

                <button
                  class="action-btn action-delete"
                  title="Hapus Permohonan"
                  @click="openDeleteModal(item)"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- TABLE VIEW -->
        <div v-else class="table-card">
          <div class="table-responsive">
            <table class="data-table">
              <thead>
                <tr>
                  <th>No. Tiket</th>
                  <th>Mahasiswa / NIM</th>
                  <th>Program Studi</th>
                  <th>Jenis Layanan</th>
                  <th>Tanggal</th>
                  <th>Status</th>
                  <th class="text-right">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in pengajuanList" :key="item.id">
                  <td>
                    <span class="ticket-cell font-mono" @click="copyTicket(item.kode_tiket)">
                      {{ item.kode_tiket }}
                    </span>
                  </td>
                  <td>
                    <div class="table-student-name">{{ item.nama_mahasiswa }}</div>
                    <div class="table-student-nim font-mono">{{ item.nim }}</div>
                  </td>
                  <td>
                    <span>{{ item.program_studi }}</span>
                    <span class="badge-sub font-mono">Smtr {{ item.semester }}</span>
                  </td>
                  <td>
                    <span class="table-service-name">{{ item.jenis_layanan }}</span>
                  </td>
                  <td class="text-muted font-sm">
                    {{ formatDate(item.tanggal_pengajuan || item.created_at) }}
                  </td>
                  <td>
                    <span class="status-pill font-xs" :class="'status-' + item.status">
                      <span class="status-dot"></span>
                      <span>{{ formatStatus(item.status) }}</span>
                    </span>
                  </td>
                  <td class="text-right">
                    <div class="action-buttons-inline">
                      <button class="btn-table-icon" title="Lihat Detail" @click="openDetailModal(item)">
                        👁️
                      </button>
                      <button class="btn-table-icon" title="Edit" @click="openEditModal(item)">
                        ✏️
                      </button>
                      <button class="btn-table-icon text-danger" title="Hapus" @click="openDeleteModal(item)">
                        🗑️
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- TAB 2: KATALOG MASTER LAYANAN -->
      <section v-if="activeTab === 'katalog'" class="tab-pane">
        <div class="catalog-header">
          <div>
            <h2 class="section-title">Katalog Layanan Mahasiswa Kampus</h2>
            <p class="section-desc">Pilih layanan yang Anda perlukan dan ajukan permohonan secara instan.</p>
          </div>
          <span class="badge-count">{{ masterLayanan.length }} Jenis Layanan</span>
        </div>

        <div class="catalog-grid">
          <div v-for="lay in masterLayanan" :key="lay.id" class="catalog-card">
            <div class="catalog-card-header">
              <span class="category-tag">{{ lay.kategori }}</span>
              <span class="sla-badge">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span>Estimasi: {{ lay.estimasi }}</span>
              </span>
            </div>

            <h3 class="catalog-title">{{ lay.nama }}</h3>
            <p class="catalog-desc">{{ lay.deskripsi }}</p>

            <div v-if="lay.persyaratan && lay.persyaratan.length" class="req-box">
              <div class="req-title">Persyaratan Berkas:</div>
              <ul class="req-list">
                <li v-for="(req, idx) in lay.persyaratan" :key="idx">
                  <span class="check-icon">✓</span>
                  <span>{{ req }}</span>
                </li>
              </ul>
            </div>

            <div class="catalog-footer">
              <span class="service-state-badge">{{ lay.status }}</span>
              <button class="btn btn-primary btn-sm" @click="applyFromCatalog(lay)">
                <span>Ajukan Layanan Ini</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                  <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- TAB 3: LACAK STATUS TIKET -->
      <section v-if="activeTab === 'tracking'" class="tab-pane">
        <div class="tracker-card">
          <div class="tracker-intro">
            <h2 class="section-title">Pelacakan Status Pengajuan</h2>
            <p class="section-desc">Masukkan Nomor Kode Tiket (misal: <code>PLM-202610-0001</code>) atau NIM Anda untuk mengecek riwayat verifikasi.</p>

            <div class="tracking-search-bar">
              <input
                v-model="trackingQuery"
                type="text"
                placeholder="Contoh: PLM-202610-0001 atau 2201010042"
                class="tracking-input font-mono"
                @keyup.enter="performTracking"
              />
              <button class="btn btn-primary" :disabled="isTrackingLoading" @click="performTracking">
                <span v-if="isTrackingLoading" class="spin-anim">⏳</span>
                <span v-else>Lacak Sekarang</span>
              </button>
            </div>
          </div>

          <!-- Tracking Result -->
          <div v-if="trackingResult" class="tracking-result-box">
            <div class="result-header">
              <div>
                <span class="font-mono text-muted">Nomor Tiket:</span>
                <h3 class="font-mono result-ticket">{{ trackingResult.kode_tiket }}</h3>
              </div>
              <span class="status-pill status-lg" :class="'status-' + trackingResult.status">
                <span class="status-dot"></span>
                <span>{{ formatStatus(trackingResult.status) }}</span>
              </span>
            </div>

            <!-- Timeline Stepper -->
            <div class="stepper-horizontal">
              <div class="step-point" :class="getStepClass(trackingResult.status, 1)">
                <div class="step-circle">1</div>
                <div class="step-label">Diterima</div>
                <div class="step-sub">{{ formatDate(trackingResult.tanggal_pengajuan || trackingResult.created_at) }}</div>
              </div>
              <div class="step-line" :class="{ 'step-line-active': isStepActive(trackingResult.status, 2) }"></div>

              <div class="step-point" :class="getStepClass(trackingResult.status, 2)">
                <div class="step-circle">2</div>
                <div class="step-label">Verifikasi Berkas</div>
                <div class="step-sub">Admin Akademik</div>
              </div>
              <div class="step-line" :class="{ 'step-line-active': isStepActive(trackingResult.status, 3) }"></div>

              <div class="step-point" :class="getStepClass(trackingResult.status, 3)">
                <div class="step-circle">3</div>
                <div class="step-label">Tanda Tangan Pimpinan</div>
                <div class="step-sub">Wakil Dekan / Rektorat</div>
              </div>
              <div class="step-line" :class="{ 'step-line-active': isStepActive(trackingResult.status, 4) }"></div>

              <div class="step-point" :class="getStepClass(trackingResult.status, 4)">
                <div class="step-circle">4</div>
                <div class="step-label">Dokumen Siap</div>
                <div class="step-sub">{{ trackingResult.status === 'selesai' ? 'Selesai Terbit' : 'Menunggu Proses' }}</div>
              </div>
            </div>

            <div class="tracking-detail-grid">
              <div class="t-item">
                <span class="t-label">Nama Mahasiswa</span>
                <span class="t-value">{{ trackingResult.nama_mahasiswa }}</span>
              </div>
              <div class="t-item">
                <span class="t-label">NIM</span>
                <span class="t-value font-mono">{{ trackingResult.nim }}</span>
              </div>
              <div class="t-item">
                <span class="t-label">Program Studi</span>
                <span class="t-value">{{ trackingResult.program_studi }} (Semester {{ trackingResult.semester }})</span>
              </div>
              <div class="t-item">
                <span class="t-label">Jenis Layanan</span>
                <span class="t-value text-primary font-semibold">{{ trackingResult.jenis_layanan }}</span>
              </div>
            </div>

            <div v-if="trackingResult.catatan_petugas" class="tracking-officer-note">
              <strong>Catatan dari Petugas Akademik:</strong>
              <p>{{ trackingResult.catatan_petugas }}</p>
            </div>
          </div>
        </div>
      </section>

      <!-- TAB 4: PROFIL MAHASISWA & EDIT PROFIL -->
      <section v-if="activeTab === 'profile'" class="tab-pane">
        <div class="profile-card-container">
          <!-- If Authenticated -->
          <div v-if="isAuthenticated && user" class="profile-section-wrapper">
            <!-- Hero Card -->
            <div class="profile-hero-card">
              <div class="profile-hero-left">
                <div class="profile-avatar-large">
                  {{ (user.name || 'M').charAt(0).toUpperCase() }}
                </div>
                <div class="profile-hero-info">
                  <div class="profile-hero-name">{{ user.name }}</div>
                  <div class="profile-hero-meta">
                    <span v-if="user.nim" class="profile-meta-nim">NIM: {{ user.nim }}</span>
                    <span class="profile-meta-item">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                      </svg>
                      {{ user.program_studi || 'Program Studi Umum' }} &bull; Semester {{ user.semester || 1 }}
                    </span>
                    <span class="profile-meta-item">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                      </svg>
                      {{ user.email }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="profile-hero-actions">
                <button class="btn btn-sm btn-outline-primary" @click="openCreateModal()">
                  + Ajukan Layanan
                </button>
              </div>
            </div>

            <!-- Profile Edit Form Box -->
            <div class="profile-form-box mt-4">
              <div class="profile-section-heading">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                <span>Edit Data Profil Mahasiswa</span>
              </div>

              <div v-if="profileSuccessMessage" class="alert-liquid-info">
                <span>✓ {{ profileSuccessMessage }}</span>
              </div>

              <form @submit.prevent="handleSaveProfile">
                <div class="form-grid-2">
                  <div class="form-group">
                    <label class="form-label required">Nama Lengkap Mahasiswa</label>
                    <input
                      v-model="profileForm.name"
                      type="text"
                      class="form-control"
                      placeholder="Nama lengkap Anda"
                      required
                    />
                    <span v-if="profileErrors.name" class="error-text">{{ profileErrors.name[0] }}</span>
                  </div>

                  <div class="form-group">
                    <label class="form-label required">Alamat Email</label>
                    <input
                      v-model="profileForm.email"
                      type="email"
                      class="form-control"
                      placeholder="email@student.ac.id"
                      required
                    />
                    <span v-if="profileErrors.email" class="error-text">{{ profileErrors.email[0] }}</span>
                  </div>

                  <div class="form-group">
                    <label class="form-label">NIM (Nomor Induk Mahasiswa)</label>
                    <input
                      v-model="profileForm.nim"
                      type="text"
                      placeholder="Contoh: 2201010042"
                      class="form-control font-mono"
                    />
                    <span v-if="profileErrors.nim" class="error-text">{{ profileErrors.nim[0] }}</span>
                  </div>

                  <div class="form-group">
                    <label class="form-label">Program Studi</label>
                    <select v-model="profileForm.program_studi" class="form-control">
                      <option value="Teknik Informatika">Teknik Informatika</option>
                      <option value="Sistem Informasi">Sistem Informasi</option>
                      <option value="Ilmu Komunikasi">Ilmu Komunikasi</option>
                      <option value="Manajemen Bisnis">Manajemen Bisnis</option>
                      <option value="Akuntansi">Akuntansi</option>
                      <option value="Desain Komunikasi Visual">Desain Komunikasi Visual</option>
                      <option value="Teknik Elektro">Teknik Elektro</option>
                      <option value="Hukum">Hukum</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label class="form-label">Semester</label>
                    <select v-model.number="profileForm.semester" class="form-control font-mono">
                      <option v-for="s in 14" :key="s" :value="s">Semester {{ s }}</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label class="form-label">No. WhatsApp / HP</label>
                    <input
                      v-model="profileForm.no_hp"
                      type="text"
                      placeholder="Contoh: 08123456789"
                      class="form-control font-mono"
                    />
                  </div>
                </div>

                <!-- Password Change Box -->
                <div class="profile-password-box">
                  <div class="profile-password-header">
                    <div class="profile-password-title">Ganti Kata Sandi (Opsional)</div>
                    <div class="profile-password-sub">Kosongkan jika Anda tidak ingin mengubah password akun Anda saat ini.</div>
                  </div>

                  <div class="form-grid-2">
                    <div class="form-group">
                      <label class="form-label">Password Saat Ini</label>
                      <input
                        v-model="profileForm.current_password"
                        type="password"
                        placeholder="Masukkan password saat ini"
                        class="form-control"
                      />
                      <span v-if="profileErrors.current_password" class="error-text">{{ profileErrors.current_password[0] }}</span>
                    </div>

                    <div class="form-group">
                      <label class="form-label">Password Baru</label>
                      <input
                        v-model="profileForm.new_password"
                        type="password"
                        placeholder="Minimal 6 karakter"
                        class="form-control"
                      />
                      <span v-if="profileErrors.new_password" class="error-text">{{ profileErrors.new_password[0] }}</span>
                    </div>
                  </div>
                </div>

                <div class="mt-4 flex gap-3 justify-end">
                  <button type="submit" class="btn btn-primary" :disabled="isSavingProfile">
                    <span v-if="isSavingProfile" class="spin-anim">⏳</span>
                    <span v-else>Simpan Perubahan Profil</span>
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- If Not Authenticated -->
          <div v-else class="empty-state-card text-center p-5">
            <div class="empty-icon-box mx-auto">
              <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
            </div>
            <h3 class="text-xl font-bold mt-3 text-white">Akun Mahasiswa Belum Masuk</h3>
            <p class="text-muted max-w-md mx-auto mt-2">
              Silakan login atau daftar akun mahasiswa baru untuk mengelola profil Anda dan mengisi formulir pengajuan surat secara otomatis.
            </p>
            <div class="flex gap-3 justify-center mt-5">
              <button class="btn btn-outline-primary" @click="showLoginModal = true">
                Masuk ke Akun
              </button>
              <button class="btn btn-primary" @click="showRegisterModal = true">
                Daftar Akun Mahasiswa
              </button>
            </div>
          </div>
        </div>
      </section>
    </main>

    <!-- MODAL 1: CREATE PENGAJUAN (POST) -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showCreateModal" class="modal-backdrop" @click.self="showCreateModal = false">
          <div class="modal-card">
        <div class="modal-header">
          <div>
            <h2 class="modal-title">Formulir Pengajuan Layanan Baru</h2>
            <p class="modal-subtitle">Isi data identitas diri dan keperluan layanan mahasiswa</p>
          </div>
          <button class="btn-close" @click="showCreateModal = false">×</button>
        </div>

        <form @submit.prevent="submitCreateForm" class="modal-form">
          <div v-if="isAuthenticated && user" class="alert-liquid-info">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
              <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <span>Data identitas telah terisi otomatis dari profil akun <strong>{{ user.name }}</strong> ({{ user.nim || 'NIM Aktif' }}).</span>
          </div>
          <div v-else class="alert-liquid-info" style="background: rgba(59, 130, 246, 0.1); border-color: rgba(59, 130, 246, 0.25); color: #bfdbfe;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <span>Tips: <button type="button" class="auth-switch-btn" @click="showCreateModal = false; showLoginModal = true">Masuk ke akun</button> agar data identitas Anda terisi otomatis.</span>
          </div>

          <div class="form-grid-2">
            <!-- NIM -->
            <div class="form-group">
              <label class="form-label required">NIM (Nomor Induk Mahasiswa)</label>
              <input
                v-model="createForm.nim"
                type="text"
                placeholder="Contoh: 2201010042"
                class="form-control font-mono"
                required
              />
              <span v-if="formErrors.nim" class="error-text">{{ formErrors.nim[0] }}</span>
            </div>

            <!-- Nama Mahasiswa -->
            <div class="form-group">
              <label class="form-label required">Nama Lengkap Mahasiswa</label>
              <input
                v-model="createForm.nama_mahasiswa"
                type="text"
                placeholder="Nama sesuai KTM"
                class="form-control"
                required
              />
              <span v-if="formErrors.nama_mahasiswa" class="error-text">{{ formErrors.nama_mahasiswa[0] }}</span>
            </div>

            <!-- Email -->
            <div class="form-group">
              <label class="form-label required">Email Mahasiswa</label>
              <input
                v-model="createForm.email"
                type="email"
                placeholder="nama@student.kampus.ac.id"
                class="form-control"
                required
              />
              <span v-if="formErrors.email" class="error-text">{{ formErrors.email[0] }}</span>
            </div>

            <!-- No HP / WhatsApp -->
            <div class="form-group">
              <label class="form-label">No. WhatsApp / HP</label>
              <input
                v-model="createForm.no_hp"
                type="text"
                placeholder="08xxxxxxxxxx"
                class="form-control font-mono"
              />
            </div>

            <!-- Program Studi -->
            <div class="form-group">
              <label class="form-label required">Program Studi</label>
              <select v-model="createForm.program_studi" class="form-control" required>
                <option value="" disabled>Pilih Program Studi</option>
                <option value="Teknik Informatika">Teknik Informatika</option>
                <option value="Sistem Informasi">Sistem Informasi</option>
                <option value="Teknik Elektro">Teknik Elektro</option>
                <option value="Manajemen Bisnis">Manajemen Bisnis</option>
                <option value="Akuntansi">Akuntansi</option>
                <option value="Ilmu Hukum">Ilmu Hukum</option>
                <option value="Sastra Inggris">Sastra Inggris</option>
              </select>
              <span v-if="formErrors.program_studi" class="error-text">{{ formErrors.program_studi[0] }}</span>
            </div>

            <!-- Semester -->
            <div class="form-group">
              <label class="form-label required">Semester Saat Ini</label>
              <select v-model.number="createForm.semester" class="form-control" required>
                <option v-for="sem in 14" :key="sem" :value="sem">Semester {{ sem }}</option>
              </select>
            </div>
          </div>

          <!-- Jenis Layanan -->
          <div class="form-group">
            <label class="form-label required">Jenis Layanan yang Diajukan</label>
            <select v-model="createForm.jenis_layanan" class="form-control" required>
              <option value="" disabled>Pilih jenis layanan</option>
              <option v-for="lay in masterLayanan" :key="lay.id" :value="lay.nama">
                {{ lay.nama }} ({{ lay.kategori }})
              </option>
            </select>
            <span v-if="formErrors.jenis_layanan" class="error-text">{{ formErrors.jenis_layanan[0] }}</span>
          </div>

          <!-- Keperluan -->
          <div class="form-group">
            <label class="form-label required">Keperluan / Keterangan Pengajuan</label>
            <textarea
              v-model="createForm.keperluan"
              rows="3"
              placeholder="Jelaskan untuk keperluan apa surat/layanan ini dibutuhkan (misal: syarat beasiswa, tunjangan orang tua, dll)..."
              class="form-control"
              required
            ></textarea>
            <span v-if="formErrors.keperluan" class="error-text">{{ formErrors.keperluan[0] }}</span>
          </div>

          <!-- Lampiran Dokumen -->
          <div class="form-group">
            <label class="form-label">Nama Berkas Pendukung (Opsional)</label>
            <input
              v-model="createForm.dokumen_lampiran"
              type="text"
              placeholder="Contoh: krs_semester_5_dan_ktm.pdf"
              class="form-control font-mono"
            />
            <span class="help-text">Tuliskan nama file pendukung yang diserahkan ke bagian loket atau dilampirkan.</span>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="showCreateModal = false">Batal</button>
            <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
              <span v-if="isSubmitting" class="spin-anim">⏳</span>
              <span v-else>Kirim Pengajuan</span>
            </button>
          </div>
        </form>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- MODAL 2: EDIT PENGAJUAN (UPDATE) -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showEditModal" class="modal-backdrop" @click.self="showEditModal = false">
          <div class="modal-card">
        <div class="modal-header">
          <div>
            <h2 class="modal-title">Edit Permohonan: <span class="font-mono text-primary">{{ editForm.kode_tiket }}</span></h2>
            <p class="modal-subtitle">Perbarui data identitas mahasiswa atau status persetujuan</p>
          </div>
          <button class="btn-close" @click="showEditModal = false">×</button>
        </div>

        <form @submit.prevent="submitEditForm" class="modal-form">
          <div class="form-grid-2">
            <div class="form-group">
              <label class="form-label required">NIM</label>
              <input v-model="editForm.nim" type="text" class="form-control font-mono" required />
              <span v-if="editFormErrors.nim" class="error-text">{{ editFormErrors.nim[0] }}</span>
            </div>

            <div class="form-group">
              <label class="form-label required">Nama Mahasiswa</label>
              <input v-model="editForm.nama_mahasiswa" type="text" class="form-control" required />
              <span v-if="editFormErrors.nama_mahasiswa" class="error-text">{{ editFormErrors.nama_mahasiswa[0] }}</span>
            </div>

            <div class="form-group">
              <label class="form-label required">Email</label>
              <input v-model="editForm.email" type="email" class="form-control" required />
              <span v-if="editFormErrors.email" class="error-text">{{ editFormErrors.email[0] }}</span>
            </div>

            <div class="form-group">
              <label class="form-label">No. HP / WhatsApp</label>
              <input v-model="editForm.no_hp" type="text" class="form-control font-mono" />
            </div>

            <div class="form-group">
              <label class="form-label required">Program Studi</label>
              <select v-model="editForm.program_studi" class="form-control" required>
                <option value="Teknik Informatika">Teknik Informatika</option>
                <option value="Sistem Informasi">Sistem Informasi</option>
                <option value="Teknik Elektro">Teknik Elektro</option>
                <option value="Manajemen Bisnis">Manajemen Bisnis</option>
                <option value="Akuntansi">Akuntansi</option>
                <option value="Ilmu Hukum">Ilmu Hukum</option>
                <option value="Sastra Inggris">Sastra Inggris</option>
              </select>
              <span v-if="editFormErrors.program_studi" class="error-text">{{ editFormErrors.program_studi[0] }}</span>
            </div>

            <div class="form-group">
              <label class="form-label required">Semester</label>
              <select v-model.number="editForm.semester" class="form-control" required>
                <option v-for="sem in 14" :key="sem" :value="sem">Semester {{ sem }}</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label required">Jenis Layanan</label>
            <select v-model="editForm.jenis_layanan" class="form-control" required>
              <option v-for="lay in masterLayanan" :key="lay.id" :value="lay.nama">
                {{ lay.nama }}
              </option>
            </select>
            <span v-if="editFormErrors.jenis_layanan" class="error-text">{{ editFormErrors.jenis_layanan[0] }}</span>
          </div>

          <div class="form-group">
            <label class="form-label required">Keperluan</label>
            <textarea v-model="editForm.keperluan" rows="3" class="form-control" required></textarea>
            <span v-if="editFormErrors.keperluan" class="error-text">{{ editFormErrors.keperluan[0] }}</span>
          </div>

          <!-- Bagian Kontrol Petugas / Admin -->
          <div class="admin-control-box">
            <h4 class="admin-box-title">Panel Petugas Akademik (Update Status)</h4>

            <div class="form-grid-2">
              <div class="form-group">
                <label class="form-label required">Status Persetujuan</label>
                <select v-model="editForm.status" class="form-control font-semibold">
                  <option value="menunggu">🟡 Menunggu Review</option>
                  <option value="diproses">🔵 Sedang Diproses</option>
                  <option value="selesai">🟢 Selesai / Terbit</option>
                  <option value="ditolak">🔴 Ditolak</option>
                </select>
              </div>

              <div class="form-group">
                <label class="form-label">Catatan Petugas / Alasan</label>
                <input
                  v-model="editForm.catatan_petugas"
                  type="text"
                  placeholder="Contoh: Dokumen telah dicap dan siap diambil di loket..."
                  class="form-control"
                />
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="showEditModal = false">Batal</button>
            <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
              <span v-if="isSubmitting" class="spin-anim">⏳</span>
              <span v-else>Simpan Perubahan</span>
            </button>
          </div>
        </form>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- MODAL 3: DETAIL PENGAJUAN (READ) -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showDetailModal && selectedDetail" class="modal-backdrop" @click.self="showDetailModal = false">
          <div class="modal-card modal-card-detail">
        <div class="modal-header">
          <div>
            <span class="font-mono text-muted">Detail Tiket</span>
            <h2 class="modal-title font-mono text-primary">{{ selectedDetail.kode_tiket }}</h2>
          </div>
          <button class="btn-close" @click="showDetailModal = false">×</button>
        </div>

        <div class="modal-body-content">
          <div class="detail-status-banner" :class="'banner-' + selectedDetail.status">
            <div>
              <div class="status-banner-title">Status: {{ formatStatus(selectedDetail.status) }}</div>
              <div class="status-banner-sub">Diajukan pada {{ formatDate(selectedDetail.tanggal_pengajuan || selectedDetail.created_at) }}</div>
            </div>
            <button class="btn btn-sm btn-outline-white" @click="openEditModal(selectedDetail); showDetailModal = false">
              Ubah Status
            </button>
          </div>

          <div class="detail-section">
            <h4 class="detail-section-title">Informasi Pemohon</h4>
            <div class="detail-grid-info">
              <div class="d-item">
                <span class="d-label">Nama Lengkap</span>
                <span class="d-val font-semibold">{{ selectedDetail.nama_mahasiswa }}</span>
              </div>
              <div class="d-item">
                <span class="d-label">NIM</span>
                <span class="d-val font-mono">{{ selectedDetail.nim }}</span>
              </div>
              <div class="d-item">
                <span class="d-label">Program Studi</span>
                <span class="d-val">{{ selectedDetail.program_studi }} (Semester {{ selectedDetail.semester }})</span>
              </div>
              <div class="d-item">
                <span class="d-label">Kontak Email / No HP</span>
                <span class="d-val">{{ selectedDetail.email }} / {{ selectedDetail.no_hp || '-' }}</span>
              </div>
            </div>
          </div>

          <div class="detail-section">
            <h4 class="detail-section-title">Layanan yang Dimohon</h4>
            <div class="service-box-preview">
              <div class="font-semibold text-lg">{{ selectedDetail.jenis_layanan }}</div>
              <p class="text-muted font-sm mt-1">Keperluan: {{ selectedDetail.keperluan }}</p>
              <div v-if="selectedDetail.dokumen_lampiran" class="lampiran-tag font-mono mt-2">
                📎 Berkas Lampiran: {{ selectedDetail.dokumen_lampiran }}
              </div>
            </div>
          </div>

          <div v-if="selectedDetail.catatan_petugas" class="detail-section">
            <h4 class="detail-section-title">Catatan Petugas Verifikasi</h4>
            <div class="officer-note-box">
              <p>{{ selectedDetail.catatan_petugas }}</p>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" @click="showDetailModal = false">Tutup</button>
          <button class="btn btn-primary" @click="windowPrint">Cetak Tanda Terima</button>
        </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- MODAL 4: KONFIRMASI HAPUS (DELETE) -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showDeleteModal && itemToDelete" class="modal-backdrop" @click.self="showDeleteModal = false">
          <div class="modal-card modal-card-sm">
            <div class="delete-icon-box">
              <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
            </div>
            <h3 class="delete-title">Hapus Pengajuan Layanan?</h3>
            <p class="delete-desc">
              Apakah Anda yakin ingin menghapus tiket <strong class="font-mono">{{ itemToDelete.kode_tiket }}</strong> atas nama <strong>{{ itemToDelete.nama_mahasiswa }}</strong>? Tindakan ini tidak dapat dibatalkan.
            </p>

            <div class="modal-footer justify-center">
              <button class="btn btn-secondary" @click="showDeleteModal = false">Batal</button>
              <button class="btn btn-danger" :disabled="isSubmitting" @click="confirmDelete">
                <span v-if="isSubmitting" class="spin-anim">⏳</span>
                <span v-else>Ya, Hapus Sekarang</span>
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- MODAL 5: LOGIN MAHASISWA -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showLoginModal" class="modal-backdrop" @click.self="showLoginModal = false">
          <div class="modal-card" style="max-width: 480px;">
            <div class="modal-header">
              <div>
                <h2 class="modal-title">Masuk Akun Mahasiswa</h2>
                <p class="modal-subtitle">Akses data permohonan & kelola profil akademik Anda</p>
              </div>
              <button class="btn-close" @click="showLoginModal = false">×</button>
            </div>

            <form @submit.prevent="handleLogin" class="modal-form">
              <div v-if="loginErrorMessage" class="alert-liquid-danger">
                <span>⚠️ {{ loginErrorMessage }}</span>
              </div>

              <div class="form-group">
                <label class="form-label required">Email atau NIM</label>
                <input
                  v-model="loginForm.identifier"
                  type="text"
                  placeholder="Masukkan email mahasiswa atau NIM"
                  class="form-control"
                  required
                />
                <span v-if="loginErrors.identifier" class="error-text">{{ loginErrors.identifier[0] }}</span>
              </div>

              <div class="form-group mt-3">
                <label class="form-label required">Kata Sandi</label>
                <input
                  v-model="loginForm.password"
                  type="password"
                  placeholder="Masukkan password akun Anda"
                  class="form-control"
                  required
                />
                <span v-if="loginErrors.password" class="error-text">{{ loginErrors.password[0] }}</span>
              </div>

              <div class="modal-footer mt-4">
                <button type="button" class="btn btn-secondary" @click="showLoginModal = false">Batal</button>
                <button type="submit" class="btn btn-primary" :disabled="isAuthLoading">
                  <span v-if="isAuthLoading" class="spin-anim">⏳</span>
                  <span v-else>Masuk Sekarang</span>
                </button>
              </div>

              <div class="auth-switch-note">
                Belum memiliki akun mahasiswa?
                <button
                  type="button"
                  class="auth-switch-btn"
                  @click="showLoginModal = false; showRegisterModal = true"
                >
                  Daftar Disini
                </button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- MODAL 6: REGISTER MAHASISWA -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showRegisterModal" class="modal-backdrop" @click.self="showRegisterModal = false">
          <div class="modal-card" style="max-width: 580px;">
            <div class="modal-header">
              <div>
                <h2 class="modal-title">Pendaftaran Akun Mahasiswa</h2>
                <p class="modal-subtitle">Daftarkan akun untuk pengajuan layanan yang lebih cepat</p>
              </div>
              <button class="btn-close" @click="showRegisterModal = false">×</button>
            </div>

            <form @submit.prevent="handleRegister" class="modal-form">
              <div v-if="registerErrorMessage" class="alert-liquid-danger">
                <span>⚠️ {{ registerErrorMessage }}</span>
              </div>

              <div class="form-grid-2">
                <div class="form-group">
                  <label class="form-label required">Nama Lengkap</label>
                  <input
                    v-model="registerForm.name"
                    type="text"
                    placeholder="Nama sesuai KTM"
                    class="form-control"
                    required
                  />
                  <span v-if="registerErrors.name" class="error-text">{{ registerErrors.name[0] }}</span>
                </div>

                <div class="form-group">
                  <label class="form-label required">Email Mahasiswa</label>
                  <input
                    v-model="registerForm.email"
                    type="email"
                    placeholder="email@student.ac.id"
                    class="form-control"
                    required
                  />
                  <span v-if="registerErrors.email" class="error-text">{{ registerErrors.email[0] }}</span>
                </div>

                <div class="form-group">
                  <label class="form-label">NIM (Nomor Induk Mahasiswa)</label>
                  <input
                    v-model="registerForm.nim"
                    type="text"
                    placeholder="Contoh: 2201010042"
                    class="form-control font-mono"
                  />
                  <span v-if="registerErrors.nim" class="error-text">{{ registerErrors.nim[0] }}</span>
                </div>

                <div class="form-group">
                  <label class="form-label">Program Studi</label>
                  <select v-model="registerForm.program_studi" class="form-control">
                    <option value="Teknik Informatika">Teknik Informatika</option>
                    <option value="Sistem Informasi">Sistem Informasi</option>
                    <option value="Ilmu Komunikasi">Ilmu Komunikasi</option>
                    <option value="Manajemen Bisnis">Manajemen Bisnis</option>
                    <option value="Akuntansi">Akuntansi</option>
                    <option value="Desain Komunikasi Visual">Desain Komunikasi Visual</option>
                    <option value="Teknik Elektro">Teknik Elektro</option>
                    <option value="Hukum">Hukum</option>
                  </select>
                </div>

                <div class="form-group">
                  <label class="form-label">Semester</label>
                  <select v-model.number="registerForm.semester" class="form-control font-mono">
                    <option v-for="s in 14" :key="s" :value="s">Semester {{ s }}</option>
                  </select>
                </div>

                <div class="form-group">
                  <label class="form-label">No. WhatsApp / HP</label>
                  <input
                    v-model="registerForm.no_hp"
                    type="text"
                    placeholder="08xxxxxxxxxx"
                    class="form-control font-mono"
                  />
                </div>
              </div>

              <div class="form-group mt-3">
                <label class="form-label required">Kata Sandi</label>
                <input
                  v-model="registerForm.password"
                  type="password"
                  placeholder="Minimal 6 karakter"
                  class="form-control"
                  required
                />
                <span v-if="registerErrors.password" class="error-text">{{ registerErrors.password[0] }}</span>
              </div>

              <div class="modal-footer mt-4">
                <button type="button" class="btn btn-secondary" @click="showRegisterModal = false">Batal</button>
                <button type="submit" class="btn btn-primary" :disabled="isAuthLoading">
                  <span v-if="isAuthLoading" class="spin-anim">⏳</span>
                  <span v-else>Daftar & Masuk</span>
                </button>
              </div>

              <div class="auth-switch-note">
                Sudah memiliki akun mahasiswa?
                <button
                  type="button"
                  class="auth-switch-btn"
                  @click="showRegisterModal = false; showLoginModal = true"
                >
                  Masuk Disini
                </button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- MODAL 7: EDIT PROFIL MAHASISWA MODAL -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showProfileModal" class="modal-backdrop" @click.self="showProfileModal = false">
          <div class="modal-card modal-card-detail">
            <div class="modal-header">
              <div>
                <h2 class="modal-title">Profil & Data Mahasiswa</h2>
                <p class="modal-subtitle">Perbarui data diri untuk pengajuan layanan otomatis</p>
              </div>
              <button class="btn-close" @click="showProfileModal = false">×</button>
            </div>

            <form @submit.prevent="handleSaveProfile" class="modal-form">
              <div v-if="profileSuccessMessage" class="alert-liquid-info">
                <span>✓ {{ profileSuccessMessage }}</span>
              </div>

              <div class="form-grid-2">
                <div class="form-group">
                  <label class="form-label required">Nama Lengkap</label>
                  <input
                    v-model="profileForm.name"
                    type="text"
                    class="form-control"
                    required
                  />
                  <span v-if="profileErrors.name" class="error-text">{{ profileErrors.name[0] }}</span>
                </div>

                <div class="form-group">
                  <label class="form-label required">Email</label>
                  <input
                    v-model="profileForm.email"
                    type="email"
                    class="form-control"
                    required
                  />
                  <span v-if="profileErrors.email" class="error-text">{{ profileErrors.email[0] }}</span>
                </div>

                <div class="form-group">
                  <label class="form-label">NIM (Nomor Induk Mahasiswa)</label>
                  <input
                    v-model="profileForm.nim"
                    type="text"
                    class="form-control font-mono"
                  />
                  <span v-if="profileErrors.nim" class="error-text">{{ profileErrors.nim[0] }}</span>
                </div>

                <div class="form-group">
                  <label class="form-label">Program Studi</label>
                  <select v-model="profileForm.program_studi" class="form-control">
                    <option value="Teknik Informatika">Teknik Informatika</option>
                    <option value="Sistem Informasi">Sistem Informasi</option>
                    <option value="Ilmu Komunikasi">Ilmu Komunikasi</option>
                    <option value="Manajemen Bisnis">Manajemen Bisnis</option>
                    <option value="Akuntansi">Akuntansi</option>
                    <option value="Desain Komunikasi Visual">Desain Komunikasi Visual</option>
                    <option value="Teknik Elektro">Teknik Elektro</option>
                    <option value="Hukum">Hukum</option>
                  </select>
                </div>

                <div class="form-group">
                  <label class="form-label">Semester</label>
                  <select v-model.number="profileForm.semester" class="form-control font-mono">
                    <option v-for="s in 14" :key="s" :value="s">Semester {{ s }}</option>
                  </select>
                </div>

                <div class="form-group">
                  <label class="form-label">No. WhatsApp / HP</label>
                  <input
                    v-model="profileForm.no_hp"
                    type="text"
                    class="form-control font-mono"
                  />
                </div>
              </div>

              <!-- Ganti Password -->
              <div class="profile-password-box">
                <div class="profile-password-header">
                  <div class="profile-password-title">Ganti Kata Sandi (Opsional)</div>
                  <div class="profile-password-sub">Kosongkan jika tidak ingin mengubah kata sandi Anda.</div>
                </div>

                <div class="form-grid-2">
                  <div class="form-group">
                    <label class="form-label">Password Lama Saat Ini</label>
                    <input
                      v-model="profileForm.current_password"
                      type="password"
                      placeholder="Masukkan password lama"
                      class="form-control"
                    />
                    <span v-if="profileErrors.current_password" class="error-text">{{ profileErrors.current_password[0] }}</span>
                  </div>

                  <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <input
                      v-model="profileForm.new_password"
                      type="password"
                      placeholder="Minimal 6 karakter"
                      class="form-control"
                    />
                    <span v-if="profileErrors.new_password" class="error-text">{{ profileErrors.new_password[0] }}</span>
                  </div>
                </div>
              </div>

              <div class="modal-footer mt-4">
                <button type="button" class="btn btn-secondary" @click="showProfileModal = false">Tutup</button>
                <button type="submit" class="btn btn-primary" :disabled="isSavingProfile">
                  <span v-if="isSavingProfile" class="spin-anim">⏳</span>
                  <span v-else>Simpan Perubahan</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- ==================================================================== -->
    <!-- MODAL: PERSONALISASI WALLPAPER & TEMA                                -->
    <!-- ==================================================================== -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showWallpaperModal" class="modal-backdrop" @click.self="showWallpaperModal = false">
          <div class="modal-card modal-card-wallpaper">
            <div class="modal-header">
              <div class="modal-title-group">
                <div class="modal-icon-badge wallpaper-modal-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                    <circle cx="9" cy="9" r="2"/>
                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                  </svg>
                </div>
                <div>
                  <h3 class="modal-title">Ganti Wallpaper & Tema Tampilan</h3>
                  <p class="modal-subtitle">Pilih latar visual favorit Anda. Komponen teks dan kartu dijamin tetap tajam, kontras tinggi, dan tidak nyaru.</p>
                </div>
              </div>
              <button class="btn-close-modal" @click="showWallpaperModal = false">&times;</button>
            </div>

            <div class="modal-body wallpaper-modal-body">
              <!-- Section: Preset Wallpapers -->
              <div class="wallpaper-section-title">
                <span>Preset Wallpaper Atmosferik</span>
                <span class="wallpaper-count-badge">6 Tema Tersedia</span>
              </div>

              <div class="wallpaper-presets-grid">
                <button
                  v-for="preset in wallpaperPresets"
                  :key="preset.id"
                  class="wallpaper-card-item"
                  :class="{ 'active': currentThemeId === preset.id && wallpaperType === 'preset' }"
                  @click="selectPreset(preset.id)"
                >
                  <div class="wallpaper-thumb-wrapper">
                    <img :src="preset.thumbnail" :alt="preset.name" class="wallpaper-thumb-img" loading="lazy" />
                    <div class="wallpaper-thumb-overlay" :style="{ background: preset.thumbTint }"></div>
                    <div v-if="currentThemeId === preset.id && wallpaperType === 'preset'" class="wallpaper-active-check">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                      </svg>
                    </div>
                  </div>
                  <div class="wallpaper-item-meta">
                    <div class="wallpaper-item-name">
                      <span class="wallpaper-item-dot" :style="{ background: preset.dotColor }"></span>
                      <span>{{ preset.name }}</span>
                    </div>
                    <div class="wallpaper-item-desc">{{ preset.desc }}</div>
                  </div>
                </button>
              </div>

              <!-- Section: Custom Wallpaper -->
              <div class="wallpaper-section-title mt-2">
                <span>Wallpaper Kustom Sendiri</span>
                <span class="font-xs text-muted">Upload gambar atau masukkan URL gambar</span>
              </div>

              <div class="wallpaper-custom-container">
                <div class="custom-input-row">
                  <input
                    v-model="customUrlInput"
                    type="url"
                    class="form-control font-sm font-mono"
                    placeholder="https://... URL gambar dari web (Unsplash, dll)"
                    @keyup.enter="applyCustomWallpaperUrl"
                  />
                  <button
                    class="btn-custom-apply"
                    :disabled="!customUrlInput.trim()"
                    @click="applyCustomWallpaperUrl"
                  >
                    Terapkan URL
                  </button>
                </div>

                <div class="custom-file-row">
                  <label class="btn-custom-upload" for="wallpaper-file-input">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                      <polyline points="17 8 12 3 7 8"/>
                      <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    <span>Upload Gambar dari Laptop / Komputer</span>
                  </label>
                  <input
                    id="wallpaper-file-input"
                    type="file"
                    accept="image/*"
                    class="sr-only"
                    @change="handleFileUpload"
                  />
                  <span v-if="wallpaperType === 'custom'" class="badge-custom-active">
                    ✓ Wallpaper Kustom Aktif
                  </span>
                </div>
              </div>

              <!-- Section: Contrast & Aesthetics Protection Controls -->
              <div class="wallpaper-section-title mt-2">
                <span>Proteksi Kontras & Efek Visual</span>
                <span class="font-xs text-primary">Jaminan Teks & Komponen Tetap Nyaman Dibaca</span>
              </div>

              <div class="wallpaper-tuning-box">
                <div class="tuning-slider-row">
                  <div class="tuning-label-group">
                    <label class="tuning-label">Kegelapan Lapisan Latar (Scrim Atmosferik)</label>
                    <span class="tuning-value font-mono">{{ Math.round(wallpaperDarkness * 100) }}%</span>
                  </div>
                  <input
                    v-model.number="wallpaperDarkness"
                    type="range"
                    min="0.30"
                    max="0.88"
                    step="0.02"
                    class="range-slider"
                    @input="saveWallpaperPreferences"
                  />
                  <div class="tuning-hint">Menjaga teks dan kartu tetap kontras tinggi sehingga tidak pernah bertabrakan atau nyaru dengan gambar latar.</div>
                </div>

                <div class="tuning-slider-row">
                  <div class="tuning-label-group">
                    <label class="tuning-label">Tingkat Blur Latar (Kedalaman Optik)</label>
                    <span class="tuning-value font-mono">{{ wallpaperBlur }}px</span>
                  </div>
                  <input
                    v-model.number="wallpaperBlur"
                    type="range"
                    min="0"
                    max="28"
                    step="2"
                    class="range-slider"
                    @input="saveWallpaperPreferences"
                  />
                  <div class="tuning-hint">Melembutkan detail gambar latar agar elemen antarmuka tetap bersih, fokus, dan tidak pusing dilihat.</div>
                </div>

                <div class="tuning-toggles-row">
                  <label class="tuning-toggle-item">
                    <input v-model="particlesEnabled" type="checkbox" @change="saveWallpaperPreferences" />
                    <span class="toggle-text">Efek Partikel Berpendar</span>
                  </label>
                  <label class="tuning-toggle-item">
                    <input v-model="spotlightEnabled" type="checkbox" @change="saveWallpaperPreferences" />
                    <span class="toggle-text">Sorotan Cahaya Kursor (Spotlight)</span>
                  </label>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button class="btn btn-secondary" @click="resetToDefaultTheme">
                Reset ke Bawaan
              </button>
              <button class="btn btn-primary" @click="confirmAndCloseWallpaperModal">
                Selesai & Simpan
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Footer -->
    <footer class="app-footer">
      <div class="footer-container">
        <p>&copy; 2026 Portal Web Layanan Mahasiswa &bull; Fullstack CRUD Nuxt 4 & Laravel 13 API SQLite</p>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import {
  useApiClient,
  type MasterLayanan,
  type Pengajuan,
  type PengajuanStats
} from '~/composables/useApi'
import { useAuth } from '~/composables/useAuth'

const {
  checkPing,
  getMasterLayanan,
  getStats,
  getPengajuanList,
  getPengajuanDetail,
  createPengajuan,
  updatePengajuan,
  deletePengajuan,
} = useApiClient()

// Auth State & Methods
const {
  user,
  token,
  isAuthenticated,
  isLoading: isAuthLoading,
  initAuth,
  login,
  register: registerAuth,
  updateProfile,
  logout,
} = useAuth()

// Active Tab
const activeTab = ref<'crud' | 'katalog' | 'tracking' | 'profile'>('crud')
const viewMode = ref<'grid' | 'table'>('grid')

// Backend Health State
const connectionStatus = ref<'connected' | 'offline' | 'checking'>('checking')
const connectionText = ref('Memeriksa...')
const pingLatency = ref<number | null>(null)
const isCheckingHealth = ref(false)

// Data State
const masterLayanan = ref<MasterLayanan[]>([])
const pengajuanList = ref<Pengajuan[]>([])
const stats = reactive<PengajuanStats>({
  total: 0,
  menunggu: 0,
  diproses: 0,
  selesai: 0,
  ditolak: 0,
})

// Filter State
const searchQuery = ref('')
const selectedStatus = ref('semua')
const selectedJenisLayanan = ref('semua')
const isLoadingList = ref(false)
let searchDebounceTimer: any = null

// Modals State
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDetailModal = ref(false)
const showDeleteModal = ref(false)
const showLoginModal = ref(false)
const showRegisterModal = ref(false)
const showProfileModal = ref(false)

const isSubmitting = ref(false)
const selectedDetail = ref<Pengajuan | null>(null)
const itemToDelete = ref<Pengajuan | null>(null)
const formErrors = ref<Record<string, string[]>>({})
const editFormErrors = ref<Record<string, string[]>>({})

// Auth & Profile Forms State
const loginForm = reactive({
  identifier: '',
  password: '',
})
const loginErrors = ref<Record<string, string[]>>({})
const loginErrorMessage = ref('')

const registerForm = reactive({
  name: '',
  email: '',
  password: '',
  nim: '',
  program_studi: 'Teknik Informatika',
  semester: 1,
  no_hp: '',
})
const registerErrors = ref<Record<string, string[]>>({})
const registerErrorMessage = ref('')

const profileForm = reactive({
  name: '',
  email: '',
  nim: '',
  program_studi: 'Teknik Informatika',
  semester: 1,
  no_hp: '',
  current_password: '',
  new_password: '',
})
const profileErrors = ref<Record<string, string[]>>({})
const profileSuccessMessage = ref('')
const isSavingProfile = ref(false)

// Tracking State
const trackingQuery = ref('')
const trackingResult = ref<Pengajuan | null>(null)
const isTrackingLoading = ref(false)

// Forms
const createForm = reactive({
  nim: '',
  nama_mahasiswa: '',
  email: '',
  no_hp: '',
  fakultas: '',
  program_studi: 'Teknik Informatika',
  semester: 5,
  jenis_layanan: '',
  keperluan: '',
  dokumen_lampiran: '',
})

const editForm = reactive<Partial<Pengajuan>>({
  id: undefined,
  kode_tiket: '',
  nim: '',
  nama_mahasiswa: '',
  email: '',
  no_hp: '',
  fakultas: '',
  program_studi: '',
  semester: 1,
  jenis_layanan: '',
  keperluan: '',
  status: 'menunggu',
  catatan_petugas: '',
})

// Toast System
interface Toast {
  id: number
  type: 'success' | 'error' | 'info'
  title: string
  message: string
}
const toasts = ref<Toast[]>([])

const addToast = (type: 'success' | 'error' | 'info', title: string, message: string) => {
  const id = Date.now()
  toasts.value.push({ id, type, title, message })
  setTimeout(() => {
    removeToast(id)
  }, 4000)
}

const removeToast = (id: number) => {
  toasts.value = toasts.value.filter(t => t.id !== id)
}

// 1. Health Check
const checkBackendHealth = async () => {
  isCheckingHealth.value = true
  const startTime = Date.now()
  try {
    const res = await checkPing()
    pingLatency.value = Date.now() - startTime
    if (res.status === 'connected') {
      connectionStatus.value = 'connected'
      connectionText.value = 'API Terhubung'
    } else {
      connectionStatus.value = 'offline'
      connectionText.value = 'API Offline'
    }
  } catch (err) {
    connectionStatus.value = 'offline'
    connectionText.value = 'API Offline'
    pingLatency.value = null
  } finally {
    isCheckingHealth.value = false
  }
}

// 2. Load Master Layanan
const loadMasterLayanan = async () => {
  try {
    const res = await getMasterLayanan()
    if (res.data) {
      masterLayanan.value = res.data
      const firstService = masterLayanan.value[0]
      if (firstService && !createForm.jenis_layanan) {
        createForm.jenis_layanan = firstService.nama
      }
    }
  } catch (err) {
    console.error('Gagal mengambil master layanan:', err)
  }
}

// 3. Load Stats
const loadStats = async () => {
  try {
    const res = await getStats()
    if (res.data) {
      Object.assign(stats, res.data)
    }
  } catch (err) {
    console.error('Gagal mengambil statistik:', err)
  }
}

// 4. Load Pengajuan List with Filters
const loadPengajuan = async () => {
  isLoadingList.value = true
  try {
    const res = await getPengajuanList({
      search: searchQuery.value,
      status: selectedStatus.value,
      jenis_layanan: selectedJenisLayanan.value,
    })
    pengajuanList.value = res.data || []
  } catch (err) {
    console.error('Gagal memuat daftar pengajuan:', err)
    addToast('error', 'Koneksi Gagal', 'Tidak dapat mengambil data pengajuan dari server backend.')
  } finally {
    isLoadingList.value = false
  }
}

const onSearchChange = () => {
  clearTimeout(searchDebounceTimer)
  searchDebounceTimer = setTimeout(() => {
    loadPengajuan()
  }, 350)
}

const refreshAllData = async () => {
  await Promise.all([checkBackendHealth(), loadMasterLayanan(), loadStats(), loadPengajuan()])
  addToast('info', 'Data Diperbarui', 'Data pengajuan dan statistik telah disinkronkan.')
}

// 5. Create Pengajuan (CREATE)
const openCreateModal = (defaultService?: string) => {
  formErrors.value = {}
  if (isAuthenticated.value && user.value) {
    if (user.value.nim) createForm.nim = user.value.nim
    if (user.value.name) createForm.nama_mahasiswa = user.value.name
    if (user.value.email) createForm.email = user.value.email
    if (user.value.no_hp) createForm.no_hp = user.value.no_hp
    if (user.value.program_studi) createForm.program_studi = user.value.program_studi
    if (user.value.semester) createForm.semester = user.value.semester
  }
  if (defaultService) {
    createForm.jenis_layanan = defaultService
  } else if (!createForm.jenis_layanan && masterLayanan.value[0]) {
    createForm.jenis_layanan = masterLayanan.value[0].nama
  }
  showCreateModal.value = true
}

const submitCreateForm = async () => {
  isSubmitting.value = true
  formErrors.value = {}
  try {
    const res = await createPengajuan(createForm)
    if (res.status === 'success' && res.data) {
      addToast('success', 'Berhasil Diajukan!', `Permohonan diterbitkan dengan tiket ${res.data.kode_tiket}.`)
      showCreateModal.value = false
      // Reset form
      createForm.nim = ''
      createForm.nama_mahasiswa = ''
      createForm.email = ''
      createForm.no_hp = ''
      createForm.keperluan = ''
      createForm.dokumen_lampiran = ''
      // Reload
      await Promise.all([loadPengajuan(), loadStats()])
    }
  } catch (err: any) {
    if (err?.data?.errors) {
      formErrors.value = err.data.errors
      addToast('error', 'Validasi Gagal', 'Periksa kembali isian formulir Anda.')
    } else {
      addToast('error', 'Gagal Mengirim', err?.data?.message || 'Terjadi kesalahan pada server backend.')
    }
  } finally {
    isSubmitting.value = false
  }
}

// 6. Edit Pengajuan (UPDATE)
const openEditModal = (item: Pengajuan) => {
  editFormErrors.value = {}
  Object.assign(editForm, {
    id: item.id,
    kode_tiket: item.kode_tiket,
    nim: item.nim,
    nama_mahasiswa: item.nama_mahasiswa,
    email: item.email,
    no_hp: item.no_hp || '',
    fakultas: item.fakultas || '',
    program_studi: item.program_studi,
    semester: item.semester,
    jenis_layanan: item.jenis_layanan,
    keperluan: item.keperluan,
    status: item.status,
    catatan_petugas: item.catatan_petugas || '',
  })
  showEditModal.value = true
}

const submitEditForm = async () => {
  if (!editForm.id) return
  isSubmitting.value = true
  editFormErrors.value = {}
  try {
    const res = await updatePengajuan(editForm.id, editForm)
    if (res.status === 'success') {
      addToast('success', 'Data Diperbarui', `Perubahan pada tiket ${editForm.kode_tiket} berhasil disimpan.`)
      showEditModal.value = false
      await Promise.all([loadPengajuan(), loadStats()])
    }
  } catch (err: any) {
    if (err?.data?.errors) {
      editFormErrors.value = err.data.errors
      addToast('error', 'Validasi Gagal', 'Periksa kembali data formulir.')
    } else {
      addToast('error', 'Gagal Memperbarui', err?.data?.message || 'Gagal menyimpan perubahan.')
    }
  } finally {
    isSubmitting.value = false
  }
}

// 7. Delete Pengajuan (DELETE)
const openDeleteModal = (item: Pengajuan) => {
  itemToDelete.value = item
  showDeleteModal.value = true
}

const confirmDelete = async () => {
  if (!itemToDelete.value) return
  isSubmitting.value = true
  try {
    const res = await deletePengajuan(itemToDelete.value.id)
    if (res.status === 'success') {
      addToast('info', 'Pengajuan Dihapus', `Tiket ${itemToDelete.value.kode_tiket} berhasil dihapus.`)
      showDeleteModal.value = false
      itemToDelete.value = null
      await Promise.all([loadPengajuan(), loadStats()])
    }
  } catch (err: any) {
    addToast('error', 'Gagal Menghapus', err?.data?.message || 'Gagal menghapus data dari server.')
  } finally {
    isSubmitting.value = false
  }
}

// 8. Detail Modal (READ)
const openDetailModal = (item: Pengajuan) => {
  selectedDetail.value = item
  showDetailModal.value = true
}

// 9. Catalog Application Action
const applyFromCatalog = (lay: MasterLayanan) => {
  openCreateModal(lay.nama)
}

// 10. Tracking Action
const performTracking = async () => {
  if (!trackingQuery.value.trim()) return
  isTrackingLoading.value = true
  trackingResult.value = null
  try {
    const res = await getPengajuanDetail(trackingQuery.value.trim())
    if (res.data) {
      trackingResult.value = res.data
      addToast('success', 'Ditemukan', `Data permohonan tiket ${res.data.kode_tiket} ditemukan.`)
    } else {
      addToast('error', 'Tidak Ditemukan', 'Tidak ada data pengajuan dengan nomor atau NIM tersebut.')
    }
  } catch (err) {
    addToast('error', 'Tidak Ditemukan', 'Tidak ditemukan tiket atau NIM pengajuan yang cocok.')
  } finally {
    isTrackingLoading.value = false
  }
}

// Helpers
const formatStatus = (status: string) => {
  switch (status) {
    case 'menunggu': return 'Menunggu Review'
    case 'diproses': return 'Sedang Diproses'
    case 'selesai': return 'Selesai Terbit'
    case 'ditolak': return 'Ditolak'
    default: return status
  }
}

const formatDate = (dateString?: string) => {
  if (!dateString) return '-'
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    })
  } catch {
    return dateString
  }
}

const copyTicket = async (ticket: string) => {
  try {
    await navigator.clipboard.writeText(ticket)
    addToast('info', 'Tersalin', `Nomor tiket ${ticket} disalin ke clipboard!`)
  } catch {
    addToast('info', 'Tiket', ticket)
  }
}

const isStepActive = (status: string, step: number) => {
  if (status === 'ditolak') return false
  if (step === 1) return true
  if (step === 2 && (status === 'diproses' || status === 'selesai')) return true
  if (step === 3 && (status === 'diproses' || status === 'selesai')) return true
  if (step === 4 && status === 'selesai') return true
  return false
}

const getStepClass = (status: string, step: number) => {
  if (status === 'ditolak') return 'step-rejected'
  if (isStepActive(status, step)) return 'step-done'
  return ''
}

const windowPrint = () => {
  window.print()
}

// Wallpaper & Theme Customization System
interface WallpaperPreset {
  id: string
  name: string
  desc: string
  bgUrl: string
  thumbnail: string
  dotColor: string
  thumbTint: string
  spotlightGlow: string
}

const wallpaperPresets: WallpaperPreset[] = [
  {
    id: 'emerald',
    name: 'Habit Emerald',
    desc: 'Latar default bernuansa hutan cyber dengan pendar zamrud & mint.',
    bgUrl: '/bg/habit-bg.jpg',
    thumbnail: '/bg/habit-bg.jpg',
    dotColor: '#10b981',
    thumbTint: 'rgba(16, 185, 129, 0.25)',
    spotlightGlow: 'rgba(16, 185, 129, 0.22)',
  },
  {
    id: 'nebula',
    name: 'Midnight Nebula',
    desc: 'Nuansa angkasa kosmik biru sapphire & lembayung bintang.',
    bgUrl: '/bg/nebula-bg.jpg',
    thumbnail: '/bg/nebula-bg.jpg',
    dotColor: '#3b82f6',
    thumbTint: 'rgba(59, 130, 246, 0.25)',
    spotlightGlow: 'rgba(59, 130, 246, 0.22)',
  },
  {
    id: 'cyberpunk',
    name: 'Cyberpunk Obsidian',
    desc: 'Geometris arang gelap dengan pendar amber neon & rubi.',
    bgUrl: '/bg/cyberpunk-bg.jpg',
    thumbnail: '/bg/cyberpunk-bg.jpg',
    dotColor: '#f43f5e',
    thumbTint: 'rgba(244, 63, 94, 0.25)',
    spotlightGlow: 'rgba(244, 63, 94, 0.22)',
  },
  {
    id: 'aurora',
    name: 'Deep Aurora',
    desc: 'Pendar cahaya kutub utara teal & cyan malam dramatis.',
    bgUrl: '/bg/aurora-bg.jpg',
    thumbnail: '/bg/aurora-bg.jpg',
    dotColor: '#06b6d4',
    thumbTint: 'rgba(6, 182, 212, 0.25)',
    spotlightGlow: 'rgba(6, 182, 212, 0.22)',
  },
  {
    id: 'amethyst',
    name: 'Dark Amethyst',
    desc: 'Kemewahan kristal ungu violet & kabut eterik berkelas.',
    bgUrl: '/bg/amethyst-bg.jpg',
    thumbnail: '/bg/amethyst-bg.jpg',
    dotColor: '#8b5cf6',
    thumbTint: 'rgba(139, 92, 246, 0.25)',
    spotlightGlow: 'rgba(139, 92, 246, 0.22)',
  },
  {
    id: 'slate',
    name: 'Minimal Slate',
    desc: 'OLED hitam murni grafit dengan tekstur matte perak minimalis.',
    bgUrl: '/bg/slate-bg.jpg',
    thumbnail: '/bg/slate-bg.jpg',
    dotColor: '#cbd5e1',
    thumbTint: 'rgba(203, 213, 225, 0.2)',
    spotlightGlow: 'rgba(148, 163, 184, 0.2)',
  },
]

// State Personalisasi Wallpaper
const showWallpaperModal = ref(false)
const currentThemeId = ref('emerald')
const wallpaperType = ref<'preset' | 'custom'>('preset')
const customWallpaperUrl = ref('')
const customUrlInput = ref('')
const wallpaperBlur = ref(14)
const wallpaperDarkness = ref(0.65)
const particlesEnabled = ref(true)
const spotlightEnabled = ref(true)

// Computed Active Wallpaper & Config
const activeThemeConfig = computed(() => {
  return wallpaperPresets.find(p => p.id === currentThemeId.value) || wallpaperPresets[0]
})

const activeWallpaperUrl = computed(() => {
  if (wallpaperType.value === 'custom' && customWallpaperUrl.value) {
    return customWallpaperUrl.value
  }
  return activeThemeConfig.value.bgUrl
})

const selectPreset = (presetId: string) => {
  currentThemeId.value = presetId
  wallpaperType.value = 'preset'
  saveWallpaperPreferences()
  addToast('success', 'Tema Diganti', `Wallpaper & tema ${activeThemeConfig.value.name} aktif.`)
}

const applyCustomWallpaperUrl = () => {
  if (!customUrlInput.value.trim()) return
  customWallpaperUrl.value = customUrlInput.value.trim()
  wallpaperType.value = 'custom'
  currentThemeId.value = 'custom'
  saveWallpaperPreferences()
  addToast('success', 'Wallpaper Kustom Aktif', 'Gambar dari URL berhasil diterapkan dengan proteksi kontras.')
}

const handleFileUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (!target.files || target.files.length === 0) return
  const file = target.files[0]
  if (file.size > 8 * 1024 * 1024) {
    addToast('error', 'Ukuran Terlalu Besar', 'Maksimal ukuran wallpaper adalah 8MB.')
    return
  }
  const reader = new FileReader()
  reader.onload = (e) => {
    if (e.target?.result) {
      customWallpaperUrl.value = e.target.result as string
      wallpaperType.value = 'custom'
      currentThemeId.value = 'custom'
      saveWallpaperPreferences()
      addToast('success', 'Wallpaper Kustom Aktif', 'Gambar lokal berhasil dimuat sebagai latar.')
    }
  }
  reader.readAsDataURL(file)
}

const resetToDefaultTheme = () => {
  currentThemeId.value = 'emerald'
  wallpaperType.value = 'preset'
  customWallpaperUrl.value = ''
  customUrlInput.value = ''
  wallpaperBlur.value = 14
  wallpaperDarkness.value = 0.65
  particlesEnabled.value = true
  spotlightEnabled.value = true
  saveWallpaperPreferences()
  addToast('info', 'Reset Berhasil', 'Wallpaper dan tema dikembalikan ke Habit Emerald default.')
}

const confirmAndCloseWallpaperModal = () => {
  saveWallpaperPreferences()
  showWallpaperModal.value = false
  addToast('success', 'Tersimpan', 'Preferensi wallpaper Anda tersimpan dengan aman.')
}

const saveWallpaperPreferences = () => {
  if (typeof window === 'undefined') return
  try {
    const pref = {
      themeId: currentThemeId.value,
      wallpaperType: wallpaperType.value,
      customUrl: customWallpaperUrl.value,
      blur: wallpaperBlur.value,
      darkness: wallpaperDarkness.value,
      particles: particlesEnabled.value,
      spotlight: spotlightEnabled.value,
    }
    localStorage.setItem('silayanan_wallpaper_pref', JSON.stringify(pref))
  } catch (e) {
    // ignore
  }
}

const loadWallpaperPreferences = () => {
  if (typeof window === 'undefined') return
  try {
    const saved = localStorage.getItem('silayanan_wallpaper_pref')
    if (saved) {
      const pref = JSON.parse(saved)
      if (pref.themeId) currentThemeId.value = pref.themeId
      if (pref.wallpaperType) wallpaperType.value = pref.wallpaperType
      if (pref.customUrl) {
        customWallpaperUrl.value = pref.customUrl
        customUrlInput.value = pref.customUrl
      }
      if (typeof pref.blur === 'number') wallpaperBlur.value = pref.blur
      if (typeof pref.darkness === 'number') wallpaperDarkness.value = pref.darkness
      if (typeof pref.particles === 'boolean') particlesEnabled.value = pref.particles
      if (typeof pref.spotlight === 'boolean') spotlightEnabled.value = pref.spotlight
    }
  } catch (e) {
    // fallback
  }
}

// Habit Dynamic Background State & Ambient Sparkles
const mouseX = ref(600)
const mouseY = ref(300)

const handleMouseMove = (e: MouseEvent) => {
  mouseX.value = e.clientX
  mouseY.value = e.clientY
}

const particles = ref(
  Array.from({ length: 22 }, (_, i) => ({
    id: i,
    left: `${(i * 4.7 + 3) % 96}%`,
    top: `${(i * 7.9 + 4) % 94}%`,
    size: `${(i % 3) * 2 + 3}px`,
    duration: `${12 + (i % 6) * 4}s`,
    delay: `${-(i * 1.6)}s`,
    opacity: 0.2 + (i % 5) * 0.12,
  }))
)

// 8. Auth & Profile Management
const syncProfileForm = () => {
  if (user.value) {
    profileForm.name = user.value.name || ''
    profileForm.email = user.value.email || ''
    profileForm.nim = user.value.nim || ''
    profileForm.program_studi = user.value.program_studi || 'Teknik Informatika'
    profileForm.semester = user.value.semester || 1
    profileForm.no_hp = user.value.no_hp || ''
    profileForm.current_password = ''
    profileForm.new_password = ''
  }
}

const openProfileTab = () => {
  syncProfileForm()
  activeTab.value = 'profile'
}

const openProfileModal = () => {
  syncProfileForm()
  showProfileModal.value = true
}

const handleLogin = async () => {
  loginErrors.value = {}
  loginErrorMessage.value = ''
  try {
    const loggedIn = await login(loginForm.identifier, loginForm.password)
    addToast('success', 'Selamat Datang!', `Berhasil masuk sebagai ${loggedIn.name}`)
    showLoginModal.value = false
    loginForm.identifier = ''
    loginForm.password = ''
    syncProfileForm()
  } catch (err: any) {
    if (err?.data?.errors) {
      loginErrors.value = err.data.errors
    }
    loginErrorMessage.value = err?.data?.message || err?.message || 'Login gagal. Cek kembali akun Anda.'
  }
}

const handleRegister = async () => {
  registerErrors.value = {}
  registerErrorMessage.value = ''
  try {
    const newUser = await registerAuth({
      name: registerForm.name,
      email: registerForm.email,
      password: registerForm.password,
      nim: registerForm.nim || undefined,
      program_studi: registerForm.program_studi,
      semester: Number(registerForm.semester),
      no_hp: registerForm.no_hp || undefined,
    })
    addToast('success', 'Registrasi Berhasil!', `Selamat datang di SiLayanan, ${newUser.name}`)
    showRegisterModal.value = false
    syncProfileForm()
  } catch (err: any) {
    if (err?.data?.errors) {
      registerErrors.value = err.data.errors
    }
    registerErrorMessage.value = err?.data?.message || err?.message || 'Pendaftaran gagal. Periksa kembali kelengkapan formulir.'
  }
}

const handleLogout = async () => {
  await logout()
  addToast('info', 'Sesi Berakhir', 'Anda telah berhasil logout.')
  if (activeTab.value === 'profile') {
    activeTab.value = 'crud'
  }
}

const handleSaveProfile = async () => {
  isSavingProfile.value = true
  profileErrors.value = {}
  profileSuccessMessage.value = ''
  try {
    const payload: any = {
      name: profileForm.name,
      email: profileForm.email,
      nim: profileForm.nim,
      program_studi: profileForm.program_studi,
      semester: Number(profileForm.semester),
      no_hp: profileForm.no_hp,
    }
    if (profileForm.new_password) {
      payload.current_password = profileForm.current_password
      payload.new_password = profileForm.new_password
    }
    await updateProfile(payload)
    profileSuccessMessage.value = 'Profil Anda berhasil diperbarui!'
    addToast('success', 'Profil Diperbarui', 'Informasi akun mahasiswa berhasil disimpan.')
    profileForm.current_password = ''
    profileForm.new_password = ''
    if (showProfileModal.value) {
      setTimeout(() => {
        showProfileModal.value = false
      }, 700)
    }
  } catch (err: any) {
    if (err?.data?.errors) {
      profileErrors.value = err.data.errors
    }
    addToast('error', 'Gagal Simpan', err?.data?.message || err?.message || 'Terjadi kesalahan saat menyimpan profil.')
  } finally {
    isSavingProfile.value = false
  }
}

onMounted(async () => {
  loadWallpaperPreferences()
  await initAuth()
  syncProfileForm()
  await checkBackendHealth()
  await Promise.all([loadMasterLayanan(), loadStats(), loadPengajuan()])
})
</script>

<style>
/* Main visual styles and Habit dynamic theme are located in ~/assets/css/habit.css */
</style>
