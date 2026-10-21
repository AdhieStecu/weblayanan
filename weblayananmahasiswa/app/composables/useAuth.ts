export interface AuthUser {
  id: number
  name: string
  email: string
  nim?: string | null
  program_studi?: string | null
  semester?: number | null
  no_hp?: string | null
  avatar_url?: string | null
  role?: string
  created_at?: string
  updated_at?: string
}

export interface AuthResponse {
  status: 'success' | 'error'
  message?: string
  data?: {
    user: AuthUser
    token: string
  }
  errors?: Record<string, string[]>
}

export function useAuth() {
  const config = useRuntimeConfig()
  const token = useState<string | null>('auth_token', () => null)
  const user = useState<AuthUser | null>('auth_user', () => null)
  const isLoading = useState<boolean>('auth_loading', () => false)
  const isInitialized = useState<boolean>('auth_initialized', () => false)

  const isAuthenticated = computed(() => Boolean(token.value && user.value))

  // Inisialisasi auth dari localStorage pada browser client
  const initAuth = async () => {
    if (!import.meta.client || isInitialized.value) return
    isInitialized.value = true

    const savedToken = localStorage.getItem('silayanan_auth_token')
    const savedUser = localStorage.getItem('silayanan_auth_user')

    if (savedToken) {
      token.value = savedToken
      if (savedUser) {
        try {
          user.value = JSON.parse(savedUser)
        } catch {
          user.value = null
        }
      }
      // Validasi dan refresh profil dari server di background
      await fetchProfile().catch(() => {
        // Jika token invalid/expired, reset
        logoutLocal()
      })
    }
  }

  // Simpan sesi ke localStorage
  const saveSession = (newToken: string, newUser: AuthUser) => {
    token.value = newToken
    user.value = newUser
    if (import.meta.client) {
      localStorage.setItem('silayanan_auth_token', newToken)
      localStorage.setItem('silayanan_auth_user', JSON.stringify(newUser))
    }
  }

  // Bersihkan sesi lokal
  const logoutLocal = () => {
    token.value = null
    user.value = null
    if (import.meta.client) {
      localStorage.removeItem('silayanan_auth_token')
      localStorage.removeItem('silayanan_auth_user')
    }
  }

  // Ambil profil terbaru dari backend
  const fetchProfile = async (): Promise<AuthUser | null> => {
    if (!token.value) return null
    try {
      const res = await $fetch<{ status: string; data: AuthUser }>(`${config.public.apiBase}/auth/profile`, {
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${token.value}`,
        },
      })
      if (res.data) {
        user.value = res.data
        if (import.meta.client) {
          localStorage.setItem('silayanan_auth_user', JSON.stringify(res.data))
        }
        return res.data
      }
      return null
    } catch (err: any) {
      if (err?.response?.status === 401) {
        logoutLocal()
      }
      throw err
    }
  }

  // Login dengan Email atau NIM
  const login = async (identifier: string, password: string): Promise<AuthUser> => {
    isLoading.value = true
    try {
      const res = await $fetch<AuthResponse>(`${config.public.apiBase}/auth/login`, {
        method: 'POST',
        headers: {
          Accept: 'application/json',
        },
        body: { identifier, password },
      })

      if (res.status === 'success' && res.data) {
        saveSession(res.data.token, res.data.user)
        return res.data.user
      } else {
        throw new Error(res.message || 'Login gagal.')
      }
    } finally {
      isLoading.value = false
    }
  }

  // Registrasi akun mahasiswa baru
  const register = async (payload: {
    name: string
    email: string
    password: string
    nim?: string
    program_studi?: string
    semester?: number
    no_hp?: string
  }): Promise<AuthUser> => {
    isLoading.value = true
    try {
      const res = await $fetch<AuthResponse>(`${config.public.apiBase}/auth/register`, {
        method: 'POST',
        headers: {
          Accept: 'application/json',
        },
        body: payload,
      })

      if (res.status === 'success' && res.data) {
        saveSession(res.data.token, res.data.user)
        return res.data.user
      } else {
        throw new Error(res.message || 'Registrasi gagal.')
      }
    } finally {
      isLoading.value = false
    }
  }

  // Update profil mahasiswa
  const updateProfile = async (payload: {
    name: string
    email: string
    nim?: string
    program_studi?: string
    semester?: number
    no_hp?: string
    current_password?: string
    new_password?: string
  }): Promise<AuthUser> => {
    if (!token.value) throw new Error('Anda belum terotentikasi.')
    isLoading.value = true
    try {
      const res = await $fetch<{ status: string; message: string; data: AuthUser }>(
        `${config.public.apiBase}/auth/profile`,
        {
          method: 'PUT',
          headers: {
            Accept: 'application/json',
            Authorization: `Bearer ${token.value}`,
          },
          body: payload,
        }
      )

      if (res.status === 'success' && res.data) {
        user.value = res.data
        if (import.meta.client) {
          localStorage.setItem('silayanan_auth_user', JSON.stringify(res.data))
        }
        return res.data
      } else {
        throw new Error(res.message || 'Gagal memperbarui profil.')
      }
    } finally {
      isLoading.value = false
    }
  }

  // Logout dari backend & lokal
  const logout = async () => {
    if (token.value) {
      try {
        await $fetch(`${config.public.apiBase}/auth/logout`, {
          method: 'POST',
          headers: {
            Accept: 'application/json',
            Authorization: `Bearer ${token.value}`,
          },
        })
      } catch {
        // Abaikan error jaringan saat logout
      }
    }
    logoutLocal()
  }

  return {
    token,
    user,
    isAuthenticated,
    isLoading,
    initAuth,
    login,
    register,
    updateProfile,
    fetchProfile,
    logout,
  }
}
