import type { UseFetchOptions } from 'nuxt/app'

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
      if (token.value) {
        options.headers = {
          ...options.headers,
          Authorization: `Bearer ${token.value}`,
        }
      }
      if (import.meta.client) {
        const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/)
        if (match && match[1]) {
          options.headers = {
            ...options.headers,
            'X-XSRF-TOKEN': decodeURIComponent(match[1]),
          }
        }
      }
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
  }
}
