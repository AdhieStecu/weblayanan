import type { UseFetchOptions } from '#app'

export interface MasterLayanan {
  id: number
  nama: string
  kategori: string
  deskripsi: string
  estimasi: string
  persyaratan: string[] | null
  status: string
  icon: string
  created_at?: string
  updated_at?: string
}

export interface Pengajuan {
  id: number
  kode_tiket: string
  nim: string
  nama_mahasiswa: string
  email: string
  no_hp: string | null
  fakultas: string | null
  program_studi: string
  semester: number
  layanan_id: number | null
  jenis_layanan: string
  keperluan: string
  dokumen_lampiran: string | null
  status: 'menunggu' | 'diproses' | 'selesai' | 'ditolak'
  catatan_petugas: string | null
  tanggal_pengajuan: string
  created_at: string
  updated_at: string
  layanan?: MasterLayanan
}

export interface PengajuanStats {
  total: number
  menunggu: number
  diproses: number
  selesai: number
  ditolak: number
}

export interface CmsSettings {
  hero_banner_image: string
  hero_title: string
  hero_subtitle: string
  faculty_name: string
  campus_name: string
  notice_text: string
  announcement_badge: string
  contact_whatsapp: string
  contact_email: string
}

export interface CmsContentItem {
  id: number
  key: string
  value: string
  type: string
  group: string
  description: string
  created_at?: string
  updated_at?: string
}

export interface ApiResponse<T> {
  status: 'success' | 'error' | 'connected'
  message?: string
  data?: T
  errors?: Record<string, string[]>
  total?: number
  app?: string
  server_time?: string
}

/**
 * Composable dasar untuk query reactive useFetch
 */
export function useApi<T>(url: string | (() => string), options: UseFetchOptions<T> = {}) {
  const config = useRuntimeConfig()
  const token = useState<string | null>('auth_token', () => null)

  const authHeaders: Record<string, string> = {}
  if (token.value) {
    authHeaders['Authorization'] = `Bearer ${token.value}`
  }

  return useFetch(url, {
    baseURL: config.public.apiBase,
    headers: {
      Accept: 'application/json',
      ...authHeaders,
      ...options.headers,
    },
    credentials: 'include',
    ...options,
  })
}

/**
 * Composable API client untuk operasi CRUD imperative ($fetch)
 */
export function useApiClient() {
  const config = useRuntimeConfig()
  const token = useState<string | null>('auth_token', () => null)

  const apiFetch = $fetch.create({
    baseURL: config.public.apiBase,
    headers: {
      Accept: 'application/json',
    },
    credentials: 'include',
    onRequest({ options }) {
      const headers = new Headers(options.headers)
      if (token.value) {
        headers.set('Authorization', `Bearer ${token.value}`)
      }
      if (import.meta.client) {
        const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/)
        if (match && match[1]) {
          headers.set('X-XSRF-TOKEN', decodeURIComponent(match[1]))
        }
      }
      options.headers = headers
    },
  })

  // Inisialisasi CSRF cookie Sanctum SPA
  const getCsrfCookie = async () => {
    return await $fetch('/sanctum/csrf-cookie', {
      baseURL: config.public.backendUrl,
      credentials: 'include',
    })
  }

  // Cek koneksi backend
  const checkPing = async () => {
    return await apiFetch<ApiResponse<any>>('/ping')
  }

  // Ambil data master layanan
  const getMasterLayanan = async () => {
    return await apiFetch<ApiResponse<MasterLayanan[]>>('/layanan')
  }

  // Ambil statistik permohonan
  const getStats = async () => {
    return await apiFetch<ApiResponse<PengajuanStats>>('/pengajuan/stats')
  }

  // Ambil daftar pengajuan dengan filter
  const getPengajuanList = async (params: { search?: string; status?: string; jenis_layanan?: string } = {}) => {
    const queryParams = new URLSearchParams()
    if (params.search) queryParams.append('search', params.search)
    if (params.status && params.status !== 'semua') queryParams.append('status', params.status)
    if (params.jenis_layanan && params.jenis_layanan !== 'semua') queryParams.append('jenis_layanan', params.jenis_layanan)

    const qs = queryParams.toString()
    return await apiFetch<ApiResponse<Pengajuan[]>>(`/pengajuan${qs ? '?' + qs : ''}`)
  }

  // Detail pengajuan
  const getPengajuanDetail = async (idOrCode: string | number) => {
    return await apiFetch<ApiResponse<Pengajuan>>(`/pengajuan/${idOrCode}`)
  }

  // Tambah pengajuan baru (CREATE)
  const createPengajuan = async (data: Partial<Pengajuan>) => {
    return await apiFetch<ApiResponse<Pengajuan>>('/pengajuan', {
      method: 'POST',
      body: data,
    })
  }

  // Update pengajuan (UPDATE)
  const updatePengajuan = async (id: number, data: Partial<Pengajuan>) => {
    return await apiFetch<ApiResponse<Pengajuan>>(`/pengajuan/${id}`, {
      method: 'PUT',
      body: data,
    })
  }

  // Hapus pengajuan (DELETE)
  const deletePengajuan = async (id: number) => {
    return await apiFetch<ApiResponse<any>>(`/pengajuan/${id}`, {
      method: 'DELETE',
    })
  }

  // Update status cepat (PATCH)
  const updatePengajuanStatus = async (id: number, status: string, catatanPetugas?: string) => {
    return await apiFetch<ApiResponse<Pengajuan>>(`/pengajuan/${id}/status`, {
      method: 'PATCH',
      body: {
        status,
        catatan_petugas: catatanPetugas,
      },
    })
  }

  // Setujui pengajuan layanan oleh Administrator (APPROVE)
  const approvePengajuan = async (id: number, catatanPetugas?: string) => {
    return await apiFetch<ApiResponse<Pengajuan>>(`/pengajuan/${id}/approve`, {
      method: 'POST',
      body: {
        catatan_petugas: catatanPetugas,
      },
    })
  }

  // Tolak pengajuan layanan oleh Administrator (REJECT)
  const rejectPengajuan = async (id: number, catatanPetugas: string) => {
    return await apiFetch<ApiResponse<Pengajuan>>(`/pengajuan/${id}/reject`, {
      method: 'POST',
      body: {
        catatan_petugas: catatanPetugas,
      },
    })
  }

  // Ambil pengaturan konten CMS landing page
  const getCmsContent = async () => {
    return await apiFetch<{
      status: string
      message: string
      data: {
        settings: CmsSettings
        items: CmsContentItem[]
      }
    }>('/cms/content')
  }

  // Update teks/pengaturan CMS (POST)
  const updateCmsContent = async (payload: { key: string; value: string }) => {
    return await apiFetch<ApiResponse<any>>('/cms/content', {
      method: 'POST',
      body: payload,
    })
  }

  // Upload gambar CMS lewat API (POST multipart)
  const uploadCmsImage = async (formData: FormData) => {
    return await apiFetch<ApiResponse<any>>('/cms/upload', {
      method: 'POST',
      body: formData,
    })
  }

  // Reset CMS ke default UMPKU (POST)
  const resetCmsContent = async () => {
    return await apiFetch<ApiResponse<any>>('/cms/reset', {
      method: 'POST',
    })
  }

  // Mengambil daftar seluruh pengguna terdaftar (Admin)
  const getAllUsers = async (params?: { role?: string; search?: string }) => {
    return await apiFetch<ApiResponse<any[]>>('/users', {
      params,
    })
  }

  return {
    apiFetch,
    getCsrfCookie,
    checkPing,
    getMasterLayanan,
    getStats,
    getPengajuanList,
    getPengajuanDetail,
    createPengajuan,
    updatePengajuan,
    deletePengajuan,
    updatePengajuanStatus,
    approvePengajuan,
    rejectPengajuan,
    getCmsContent,
    updateCmsContent,
    uploadCmsImage,
    resetCmsContent,
    getAllUsers,
  }
}
