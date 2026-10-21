<template>
  <div class="app-layout">
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
            <div class="brand-subtitle">Portal Layanan Terpadu &bull; Nuxt 4 + Laravel 13</div>
          </div>
        </div>

        <div class="navbar-actions">
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
          <span class="hero-badge">Portal Administrasi Akademik Digital</span>
          <h1 class="hero-title">Pengajuan Layanan Mahasiswa Lebih Cepat, Transparan & Otomatis</h1>
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
    </main>

    <!-- MODAL 1: CREATE PENGAJUAN (POST) -->
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

    <!-- MODAL 2: EDIT PENGAJUAN (UPDATE) -->
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
            </div>

            <div class="form-group">
              <label class="form-label required">Nama Mahasiswa</label>
              <input v-model="editForm.nama_mahasiswa" type="text" class="form-control" required />
            </div>

            <div class="form-group">
              <label class="form-label required">Email</label>
              <input v-model="editForm.email" type="email" class="form-control" required />
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
          </div>

          <div class="form-group">
            <label class="form-label required">Keperluan</label>
            <textarea v-model="editForm.keperluan" rows="3" class="form-control" required></textarea>
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

    <!-- MODAL 3: DETAIL PENGAJUAN (READ) -->
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

    <!-- MODAL 4: KONFIRMASI HAPUS (DELETE) -->
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

    <!-- Footer -->
    <footer class="app-footer">
      <div class="footer-container">
        <p>&copy; 2026 Portal Web Layanan Mahasiswa &bull; Fullstack CRUD Nuxt 4 & Laravel 13 API SQLite</p>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import {
  useApiClient,
  type MasterLayanan,
  type Pengajuan,
  type PengajuanStats
} from '~/composables/useApi'

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

// Active Tab
const activeTab = ref<'crud' | 'katalog' | 'tracking'>('crud')
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
const isSubmitting = ref(false)
const selectedDetail = ref<Pengajuan | null>(null)
const itemToDelete = ref<Pengajuan | null>(null)
const formErrors = ref<Record<string, string[]>>({})

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
      if (masterLayanan.value.length > 0 && !createForm.jenis_layanan) {
        createForm.jenis_layanan = masterLayanan.value[0].nama
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
  if (defaultService) {
    createForm.jenis_layanan = defaultService
  } else if (!createForm.jenis_layanan && masterLayanan.value.length > 0) {
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
  try {
    const res = await updatePengajuan(editForm.id, editForm)
    if (res.status === 'success') {
      addToast('success', 'Data Diperbarui', `Perubahan pada tiket ${editForm.kode_tiket} berhasil disimpan.`)
      showEditModal.value = false
      await Promise.all([loadPengajuan(), loadStats()])
    }
  } catch (err: any) {
    addToast('error', 'Gagal Memperbarui', err?.data?.message || 'Gagal menyimpan perubahan.')
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

onMounted(async () => {
  await checkBackendHealth()
  await Promise.all([loadMasterLayanan(), loadStats(), loadPengajuan()])
})
</script>

<style>
/* CSS Stylesheet Design - Clean, Modern & Aesthetic */
:root {
  --primary: #4f46e5;
  --primary-hover: #4338ca;
  --primary-glow: rgba(79, 70, 229, 0.25);
  --slate-50: #f8fafc;
  --slate-100: #f1f5f9;
  --slate-200: #e2e8f0;
  --slate-300: #cbd5e1;
  --slate-600: #475569;
  --slate-700: #334155;
  --slate-800: #1e293b;
  --slate-900: #0f172a;

  --amber-50: #fffbeb;
  --amber-100: #fef3c7;
  --amber-700: #b45309;
  --amber-border: #fcd34d;

  --blue-50: #eff6ff;
  --blue-100: #dbeafe;
  --blue-700: #1d4ed8;
  --blue-border: #93c5fd;

  --emerald-50: #f0fdf4;
  --emerald-100: #dcfce7;
  --emerald-700: #15803d;
  --emerald-border: #86efac;

  --rose-50: #fff1f2;
  --rose-100: #fee2e2;
  --rose-700: #be123c;
  --rose-border: #fca5a5;
}

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  background-color: #f8fafc;
  background-image: radial-gradient(at 10% 20%, rgba(99, 102, 241, 0.04) 0px, transparent 50%),
                    radial-gradient(at 90% 10%, rgba(14, 165, 233, 0.04) 0px, transparent 50%),
                    radial-gradient(at 50% 90%, rgba(168, 85, 247, 0.03) 0px, transparent 50%);
  color: var(--slate-800);
  line-height: 1.6;
  -webkit-font-smoothing: antialiased;
}

.font-mono {
  font-family: 'JetBrains Mono', monospace;
}

.font-sm { font-size: 0.85rem; }
.font-xs { font-size: 0.75rem; }
.font-semibold { font-weight: 600; }
.text-muted { color: var(--slate-600); }
.text-primary { color: var(--primary); }
.text-danger { color: #dc2626; }
.text-right { text-align: right; }
.mt-1 { margin-top: 0.25rem; }
.mt-2 { margin-top: 0.5rem; }
.justify-center { justify-content: center; }

/* Layout */
.app-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* Navbar */
.app-navbar {
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid rgba(226, 232, 240, 0.8);
  position: sticky;
  top: 0;
  z-index: 40;
  padding: 0.85rem 2rem;
}

.navbar-container {
  max-width: 1240px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1.5rem;
}

.brand-group {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

.brand-logo {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
}

.brand-title {
  font-size: 1.15rem;
  font-weight: 800;
  letter-spacing: -0.02em;
  color: var(--slate-900);
}

.brand-highlight {
  background: linear-gradient(135deg, #4f46e5, #9333ea);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.brand-subtitle {
  font-size: 0.75rem;
  color: var(--slate-600);
  font-weight: 500;
}

.navbar-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

/* Status Indicator */
.status-indicator {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.35rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.78rem;
  font-weight: 600;
  transition: all 0.2s ease;
  border: 1px solid transparent;
}

.status-indicator.connected {
  background-color: var(--emerald-50);
  color: var(--emerald-700);
  border-color: var(--emerald-border);
}

.status-indicator.offline {
  background-color: var(--rose-50);
  color: var(--rose-700);
  border-color: var(--rose-border);
}

.status-indicator.checking {
  background-color: var(--amber-50);
  color: var(--amber-700);
  border-color: var(--amber-border);
}

.status-ping-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background-color: currentColor;
  box-shadow: 0 0 8px currentColor;
}

.latency-badge {
  font-family: 'JetBrains Mono', monospace;
  font-size: 0.7rem;
  padding: 0.1rem 0.35rem;
  border-radius: 4px;
  background: rgba(0, 0, 0, 0.05);
}

.btn-icon-refresh {
  background: none;
  border: none;
  cursor: pointer;
  color: currentColor;
  display: flex;
  align-items: center;
  opacity: 0.7;
  transition: opacity 0.2s;
  padding: 2px;
}

.btn-icon-refresh:hover {
  opacity: 1;
}

/* Main Container */
.main-wrapper {
  max-width: 1240px;
  width: 100%;
  margin: 1.75rem auto;
  padding: 0 1.5rem;
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 1.75rem;
}

/* Hero Section */
.hero-section {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.hero-badge {
  display: inline-block;
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--primary);
  background: #eef2ff;
  padding: 0.35rem 0.85rem;
  border-radius: 9999px;
  border: 1px solid #c7d2fe;
  margin-bottom: 0.6rem;
}

.hero-title {
  font-size: 1.85rem;
  font-weight: 800;
  letter-spacing: -0.03em;
  color: var(--slate-900);
  line-height: 1.25;
  max-width: 800px;
}

.hero-desc {
  font-size: 0.95rem;
  color: var(--slate-600);
  max-width: 780px;
  margin-top: 0.4rem;
}

/* Stats Cards */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1rem;
}

.stat-card {
  background: #ffffff;
  border: 1px solid var(--slate-200);
  border-radius: 14px;
  padding: 1.15rem 1.35rem;
  display: flex;
  align-items: center;
  gap: 1.15rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
  transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 18px rgba(0, 0, 0, 0.06);
}

.stat-icon-wrapper {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.stat-total .stat-icon-wrapper { background: #eef2ff; color: #4f46e5; }
.stat-pending .stat-icon-wrapper { background: #fef3c7; color: #d97706; }
.stat-process .stat-icon-wrapper { background: #e0e7ff; color: #4338ca; }
.stat-completed .stat-icon-wrapper { background: #dcfce7; color: #16a34a; }

.stat-info {
  display: flex;
  flex-direction: column;
}

.stat-number {
  font-size: 1.6rem;
  font-weight: 800;
  color: var(--slate-900);
  line-height: 1.1;
}

.stat-name {
  font-size: 0.8rem;
  color: var(--slate-600);
  font-weight: 600;
  margin-top: 0.2rem;
}

/* Tabs Navigation */
.tabs-nav-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 2px solid var(--slate-200);
  gap: 1rem;
  flex-wrap: wrap;
}

.tabs-list {
  display: flex;
  gap: 0.5rem;
}

.tab-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.15rem;
  border: none;
  background: none;
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--slate-600);
  cursor: pointer;
  position: relative;
  transition: all 0.2s;
  border-bottom: 2px solid transparent;
  margin-bottom: -2px;
}

.tab-btn:hover {
  color: var(--primary);
}

.tab-btn.active {
  color: var(--primary);
  border-bottom-color: var(--primary);
}

.tab-counter {
  font-size: 0.72rem;
  background: var(--slate-200);
  color: var(--slate-700);
  padding: 0.15rem 0.5rem;
  border-radius: 9999px;
  font-weight: 700;
}

.tab-btn.active .tab-counter {
  background: #e0e7ff;
  color: var(--primary);
}

/* Toolbar Card */
.toolbar-card {
  background: #ffffff;
  border: 1px solid var(--slate-200);
  border-radius: 14px;
  padding: 1rem 1.25rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.search-box {
  position: relative;
  flex: 1;
  min-width: 280px;
}

.search-icon {
  position: absolute;
  left: 0.9rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--slate-600);
}

.search-input {
  width: 100%;
  padding: 0.65rem 2.2rem 0.65rem 2.4rem;
  border-radius: 10px;
  border: 1px solid var(--slate-300);
  font-size: 0.88rem;
  font-family: inherit;
  transition: all 0.2s;
  background: #f8fafc;
}

.search-input:focus {
  outline: none;
  border-color: var(--primary);
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
}

.clear-search-btn {
  position: absolute;
  right: 0.8rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  font-size: 1.1rem;
  color: var(--slate-600);
  cursor: pointer;
}

.filter-group {
  display: flex;
  align-items: center;
  gap: 0.85rem;
  flex-wrap: wrap;
}

.select-wrapper {
  display: flex;
  align-items: center;
  gap: 0.4rem;
}

.filter-label {
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--slate-600);
}

.select-control {
  padding: 0.6rem 0.85rem;
  border-radius: 8px;
  border: 1px solid var(--slate-300);
  font-size: 0.85rem;
  background-color: #ffffff;
  color: var(--slate-800);
  cursor: pointer;
  font-family: inherit;
}

.select-control:focus {
  outline: none;
  border-color: var(--primary);
}

.view-toggle {
  display: flex;
  border: 1px solid var(--slate-300);
  border-radius: 8px;
  overflow: hidden;
}

.view-btn {
  padding: 0.55rem 0.75rem;
  border: none;
  background: #ffffff;
  color: var(--slate-600);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s;
}

.view-btn.active {
  background: #eef2ff;
  color: var(--primary);
}

/* Cards Grid */
.cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
  gap: 1.25rem;
}

.pengajuan-card {
  background: #ffffff;
  border: 1px solid var(--slate-200);
  border-radius: 14px;
  padding: 1.35rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
  transition: transform 0.2s, box-shadow 0.2s;
}

.pengajuan-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 22px rgba(0, 0, 0, 0.08);
}

.card-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.5rem;
}

.ticket-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  background: #f1f5f9;
  border: 1px solid #cbd5e1;
  padding: 0.25rem 0.6rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 700;
  color: var(--slate-700);
  cursor: pointer;
  transition: background-color 0.15s;
}

.ticket-badge:hover {
  background-color: #e2e8f0;
}

/* Status Pill */
.status-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.3rem 0.65rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.02em;
}

.status-pill.status-lg {
  padding: 0.5rem 1rem;
  font-size: 0.88rem;
}

.status-menunggu {
  background: var(--amber-100);
  color: var(--amber-700);
  border: 1px solid var(--amber-border);
}

.status-diproses {
  background: var(--blue-100);
  color: var(--blue-700);
  border: 1px solid var(--blue-border);
}

.status-selesai {
  background: var(--emerald-100);
  color: var(--emerald-700);
  border: 1px solid var(--emerald-border);
}

.status-ditolak {
  background: var(--rose-100);
  color: var(--rose-700);
  border: 1px solid var(--rose-border);
}

.status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: currentColor;
}

/* Card Body */
.card-body {
  display: flex;
  flex-direction: column;
  gap: 0.85rem;
  flex: 1;
}

.service-title-text {
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--slate-900);
  line-height: 1.35;
}

.student-profile {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.avatar-circle {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.95rem;
  flex-shrink: 0;
}

.profile-text {
  display: flex;
  flex-direction: column;
}

.student-name {
  font-size: 0.92rem;
  font-weight: 700;
  color: var(--slate-800);
}

.student-meta {
  font-size: 0.78rem;
  color: var(--slate-600);
}

.keperluan-preview {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  padding: 0.65rem 0.85rem;
  border-radius: 8px;
  font-size: 0.83rem;
}

.keperluan-label {
  font-weight: 700;
  color: var(--slate-700);
  display: block;
  font-size: 0.75rem;
  text-transform: uppercase;
  margin-bottom: 0.15rem;
}

.keperluan-text {
  color: var(--slate-600);
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.note-box {
  display: flex;
  align-items: flex-start;
  gap: 0.5rem;
  padding: 0.6rem 0.75rem;
  border-radius: 8px;
  font-size: 0.8rem;
}

.note-menunggu { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
.note-diproses { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
.note-selesai { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
.note-ditolak { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

/* Card Footer */
.card-footer-actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 0.85rem;
  border-top: 1px solid var(--slate-100);
}

.submission-date {
  font-size: 0.76rem;
  color: var(--slate-600);
  font-weight: 500;
}

.action-buttons-group {
  display: flex;
  gap: 0.4rem;
}

.action-btn {
  width: 32px;
  height: 32px;
  border-radius: 6px;
  border: 1px solid var(--slate-200);
  background: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.15s;
  color: var(--slate-700);
}

.action-btn:hover {
  background: var(--slate-100);
}

.action-view:hover { color: var(--primary); border-color: var(--primary); background: #eef2ff; }
.action-edit:hover { color: #d97706; border-color: #d97706; background: #fffbeb; }
.action-delete:hover { color: #dc2626; border-color: #dc2626; background: #fef2f2; }

/* Table View */
.table-card {
  background: #ffffff;
  border: 1px solid var(--slate-200);
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
}

.table-responsive {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
  font-size: 0.88rem;
}

.data-table th {
  background: #f8fafc;
  padding: 0.85rem 1.15rem;
  font-weight: 700;
  color: var(--slate-600);
  border-bottom: 1px solid var(--slate-200);
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.data-table td {
  padding: 0.95rem 1.15rem;
  border-bottom: 1px solid var(--slate-100);
  vertical-align: middle;
}

.data-table tbody tr:hover {
  background-color: #f8fafc;
}

.ticket-cell {
  background: #f1f5f9;
  padding: 0.2rem 0.5rem;
  border-radius: 4px;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
}

.table-student-name {
  font-weight: 700;
  color: var(--slate-900);
}

.table-student-nim {
  font-size: 0.78rem;
  color: var(--slate-600);
}

.badge-sub {
  font-size: 0.72rem;
  background: #f1f5f9;
  padding: 0.15rem 0.4rem;
  border-radius: 4px;
  margin-left: 0.4rem;
  color: var(--slate-600);
}

.table-service-name {
  font-weight: 600;
  color: var(--slate-800);
}

.action-buttons-inline {
  display: flex;
  gap: 0.5rem;
  justify-content: flex-end;
}

.btn-table-icon {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1rem;
  padding: 0.2rem;
  transition: transform 0.15s;
}

.btn-table-icon:hover {
  transform: scale(1.15);
}

/* Catalog View */
.catalog-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.25rem;
}

.section-title {
  font-size: 1.35rem;
  font-weight: 800;
  color: var(--slate-900);
}

.section-desc {
  font-size: 0.88rem;
  color: var(--slate-600);
}

.badge-count {
  background: #eef2ff;
  color: var(--primary);
  font-weight: 700;
  font-size: 0.82rem;
  padding: 0.35rem 0.85rem;
  border-radius: 9999px;
  border: 1px solid #c7d2fe;
}

.catalog-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 1.35rem;
}

.catalog-card {
  background: #ffffff;
  border: 1px solid var(--slate-200);
  border-radius: 14px;
  padding: 1.4rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
}

.catalog-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.category-tag {
  font-size: 0.72rem;
  font-weight: 700;
  color: var(--primary);
  background: #eef2ff;
  padding: 0.2rem 0.55rem;
  border-radius: 4px;
  text-transform: uppercase;
}

.sla-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  font-size: 0.75rem;
  color: var(--slate-600);
  font-weight: 600;
}

.catalog-title {
  font-size: 1.15rem;
  font-weight: 700;
  color: var(--slate-900);
}

.catalog-desc {
  font-size: 0.88rem;
  color: var(--slate-600);
  line-height: 1.5;
}

.req-box {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 0.85rem;
  margin-top: auto;
}

.req-title {
  font-size: 0.76rem;
  font-weight: 700;
  text-transform: uppercase;
  color: var(--slate-700);
  margin-bottom: 0.4rem;
}

.req-list {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  font-size: 0.8rem;
  color: var(--slate-600);
}

.req-list li {
  display: flex;
  align-items: flex-start;
  gap: 0.4rem;
}

.check-icon {
  color: #16a34a;
  font-weight: bold;
}

.catalog-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 1rem;
  border-top: 1px solid var(--slate-100);
}

.service-state-badge {
  font-size: 0.75rem;
  color: #15803d;
  font-weight: 700;
}

/* Tracker Tab */
.tracker-card {
  background: #ffffff;
  border: 1px solid var(--slate-200);
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.tracker-intro {
  max-width: 640px;
  margin: 0 auto 2rem auto;
  text-align: center;
}

.tracking-search-bar {
  display: flex;
  gap: 0.5rem;
  margin-top: 1.25rem;
}

.tracking-input {
  flex: 1;
  padding: 0.75rem 1rem;
  border: 1.5px solid var(--slate-300);
  border-radius: 10px;
  font-size: 0.95rem;
}

.tracking-input:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
}

.tracking-result-box {
  background: #f8fafc;
  border: 1px solid var(--slate-200);
  border-radius: 12px;
  padding: 1.75rem;
  max-width: 820px;
  margin: 0 auto;
}

.result-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--slate-200);
}

.result-ticket {
  font-size: 1.45rem;
  font-weight: 800;
  color: var(--primary);
}

/* Horizontal Stepper */
.stepper-horizontal {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 2.25rem;
  position: relative;
}

.step-point {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  z-index: 2;
  position: relative;
  width: 120px;
}

.step-circle {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: var(--slate-200);
  color: var(--slate-600);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
  transition: all 0.3s ease;
}

.step-point.step-done .step-circle {
  background: #22c55e;
  color: #ffffff;
  box-shadow: 0 0 12px rgba(34, 197, 94, 0.4);
}

.step-point.step-rejected .step-circle {
  background: #ef4444;
  color: #ffffff;
}

.step-label {
  font-size: 0.82rem;
  font-weight: 700;
  color: var(--slate-800);
}

.step-sub {
  font-size: 0.72rem;
  color: var(--slate-600);
  margin-top: 0.15rem;
}

.step-line {
  flex: 1;
  height: 3px;
  background: var(--slate-200);
  margin-top: -1.8rem;
  z-index: 1;
}

.step-line.step-line-active {
  background: #22c55e;
}

.tracking-detail-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 1rem;
  background: #ffffff;
  padding: 1.25rem;
  border-radius: 10px;
  border: 1px solid var(--slate-200);
}

.t-item {
  display: flex;
  flex-direction: column;
}

.t-label {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  color: var(--slate-600);
}

.t-value {
  font-size: 0.92rem;
  color: var(--slate-900);
  margin-top: 0.2rem;
}

.tracking-officer-note {
  margin-top: 1rem;
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  padding: 1rem;
  border-radius: 8px;
  font-size: 0.88rem;
  color: #1e40af;
}

/* Modals */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.55);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 50;
  padding: 1.5rem;
  animation: fadeIn 0.15s ease-out;
}

.modal-card {
  background: #ffffff;
  border-radius: 16px;
  width: 100%;
  max-width: 620px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.18);
  display: flex;
  flex-direction: column;
  animation: modalScaleUp 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.modal-card-detail {
  max-width: 680px;
}

.modal-card-sm {
  max-width: 440px;
  text-align: center;
  padding: 1.5rem;
}

.modal-header {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid var(--slate-200);
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
}

.modal-title {
  font-size: 1.25rem;
  font-weight: 800;
  color: var(--slate-900);
}

.modal-subtitle {
  font-size: 0.82rem;
  color: var(--slate-600);
  margin-top: 0.2rem;
}

.btn-close {
  background: none;
  border: none;
  font-size: 1.4rem;
  color: var(--slate-600);
  cursor: pointer;
  line-height: 1;
}

.modal-form {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.15rem;
}

.form-grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.form-label {
  font-size: 0.82rem;
  font-weight: 700;
  color: var(--slate-700);
}

.form-label.required::after {
  content: " *";
  color: #dc2626;
}

.form-control {
  padding: 0.65rem 0.85rem;
  border: 1px solid var(--slate-300);
  border-radius: 8px;
  font-size: 0.88rem;
  font-family: inherit;
  transition: all 0.15s;
}

.form-control:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
}

.error-text {
  font-size: 0.75rem;
  color: #dc2626;
  font-weight: 600;
}

.help-text {
  font-size: 0.72rem;
  color: var(--slate-600);
}

.admin-control-box {
  background: #f8fafc;
  border: 1.5px dashed var(--slate-300);
  padding: 1rem;
  border-radius: 10px;
  margin-top: 0.5rem;
}

.admin-box-title {
  font-size: 0.85rem;
  font-weight: 700;
  color: var(--slate-800);
  margin-bottom: 0.75rem;
}

.modal-footer {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 0.75rem;
  padding: 1.15rem 1.5rem;
  border-top: 1px solid var(--slate-200);
  background: #f8fafc;
  border-radius: 0 0 16px 16px;
}

/* Detail Modal Content */
.modal-body-content {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.detail-status-banner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.25rem;
  border-radius: 10px;
}

.banner-menunggu { background: #fef3c7; color: #b45309; }
.banner-diproses { background: #e0e7ff; color: #3730a3; }
.banner-selesai { background: #dcfce7; color: #166534; }
.banner-ditolak { background: #fee2e2; color: #991b1b; }

.status-banner-title {
  font-weight: 800;
  font-size: 1.05rem;
}

.status-banner-sub {
  font-size: 0.78rem;
  opacity: 0.85;
}

.detail-section-title {
  font-size: 0.8rem;
  font-weight: 700;
  text-transform: uppercase;
  color: var(--slate-600);
  letter-spacing: 0.05em;
  margin-bottom: 0.6rem;
}

.detail-grid-info {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.85rem;
  background: #f8fafc;
  padding: 1rem;
  border-radius: 8px;
  border: 1px solid var(--slate-200);
}

.d-item {
  display: flex;
  flex-direction: column;
}

.d-label {
  font-size: 0.72rem;
  color: var(--slate-600);
  font-weight: 600;
}

.d-val {
  font-size: 0.88rem;
  color: var(--slate-900);
}

.service-box-preview {
  background: #ffffff;
  border: 1px solid var(--slate-200);
  padding: 1rem;
  border-radius: 8px;
}

.lampiran-tag {
  font-size: 0.78rem;
  color: var(--primary);
  background: #eef2ff;
  padding: 0.35rem 0.6rem;
  border-radius: 6px;
  display: inline-block;
}

.officer-note-box {
  background: #eff6ff;
  border-left: 4px solid var(--primary);
  padding: 0.85rem 1rem;
  border-radius: 0 8px 8px 0;
  font-size: 0.88rem;
  color: var(--slate-700);
}

/* Delete Modal */
.delete-icon-box {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: #fee2e2;
  color: #dc2626;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem auto;
}

.delete-title {
  font-size: 1.25rem;
  font-weight: 800;
  color: var(--slate-900);
}

.delete-desc {
  font-size: 0.88rem;
  color: var(--slate-600);
  margin: 0.5rem 0 1.5rem 0;
}

/* Buttons */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.45rem;
  padding: 0.6rem 1.15rem;
  border-radius: 8px;
  font-size: 0.88rem;
  font-weight: 600;
  cursor: pointer;
  border: none;
  transition: all 0.15s ease;
  font-family: inherit;
}

.btn-primary {
  background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
  color: #ffffff;
}

.btn-primary:hover:not(:disabled) {
  background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
  transform: translateY(-1px);
}

.btn-glow {
  box-shadow: 0 4px 14px var(--primary-glow);
}

.btn-secondary {
  background: var(--slate-200);
  color: var(--slate-700);
}

.btn-secondary:hover {
  background: var(--slate-300);
}

.btn-danger {
  background: #dc2626;
  color: #ffffff;
}

.btn-danger:hover:not(:disabled) {
  background: #b91c1c;
}

.btn-outline-primary {
  background: #ffffff;
  color: var(--primary);
  border: 1px solid var(--slate-300);
}

.btn-outline-primary:hover {
  border-color: var(--primary);
  background: #eef2ff;
}

.btn-outline-white {
  background: rgba(255, 255, 255, 0.8);
  border: 1px solid rgba(0, 0, 0, 0.15);
  color: inherit;
  font-weight: 700;
}

.btn-sm {
  padding: 0.4rem 0.8rem;
  font-size: 0.8rem;
}

/* Toast Container */
.toast-container {
  position: fixed;
  bottom: 1.5rem;
  right: 1.5rem;
  z-index: 99;
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
  max-width: 380px;
}

.toast-card {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  background: #ffffff;
  border-radius: 12px;
  padding: 0.85rem 1rem;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
  border-left: 4px solid var(--primary);
}

.toast-success { border-left-color: #16a34a; }
.toast-error { border-left-color: #dc2626; }
.toast-info { border-left-color: var(--primary); }

.toast-icon {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 0.8rem;
  flex-shrink: 0;
}

.toast-success .toast-icon { background: #dcfce7; color: #16a34a; }
.toast-error .toast-icon { background: #fee2e2; color: #dc2626; }
.toast-info .toast-icon { background: #eef2ff; color: var(--primary); }

.toast-content {
  flex: 1;
}

.toast-title {
  font-size: 0.85rem;
  font-weight: 700;
  color: var(--slate-900);
}

.toast-message {
  font-size: 0.78rem;
  color: var(--slate-600);
}

.toast-close {
  background: none;
  border: none;
  font-size: 1.2rem;
  color: var(--slate-600);
  cursor: pointer;
}

/* Loading & Empty States */
.loading-container {
  text-align: center;
  padding: 3.5rem 1rem;
}

.loading-spinner {
  width: 36px;
  height: 36px;
  border: 3px solid var(--slate-200);
  border-top-color: var(--primary);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto 1rem auto;
}

.loading-text {
  font-size: 0.9rem;
  color: var(--slate-600);
}

.empty-box {
  background: #ffffff;
  border: 1px dashed var(--slate-300);
  border-radius: 16px;
  padding: 3.5rem 1.5rem;
  text-align: center;
  max-width: 540px;
  margin: 1.5rem auto;
}

.empty-icon-circle {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: #eef2ff;
  color: var(--primary);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem auto;
}

.empty-title {
  font-size: 1.15rem;
  font-weight: 800;
  color: var(--slate-900);
}

.empty-desc {
  font-size: 0.88rem;
  color: var(--slate-600);
  margin: 0.4rem 0 1.5rem 0;
}

/* Footer */
.app-footer {
  margin-top: auto;
  border-top: 1px solid var(--slate-200);
  background: #ffffff;
  padding: 1.5rem 2rem;
  text-align: center;
  font-size: 0.82rem;
  color: var(--slate-600);
}

/* Animations */
@keyframes spin {
  to { transform: rotate(360deg); }
}

.spin-anim {
  animation: spin 1s linear infinite;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes modalScaleUp {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}

.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(30px);
}

.toast-leave-to {
  opacity: 0;
  transform: translateY(20px);
}

/* Responsive */
@media (max-width: 768px) {
  .app-navbar {
    padding: 0.75rem 1rem;
  }
  .form-grid-2 {
    grid-template-columns: 1fr;
  }
  .stepper-horizontal {
    flex-direction: column;
    gap: 1.5rem;
    align-items: flex-start;
  }
  .step-line {
    display: none;
  }
  .step-point {
    width: 100%;
    flex-direction: row;
    gap: 1rem;
    text-align: left;
  }
}
</style>
