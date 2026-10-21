import type { UseFetchOptions } from 'nuxt/app'

/**
 * Composable untuk query API Laravel menggunakan reactive useFetch
 */
export function useApi<T>(url: string | (() => string), options: UseFetchOptions<T> = {}) {
  const config = useRuntimeConfig()

  return useFetch(url, {
    baseURL: config.public.apiBase,
    headers: {
      Accept: 'application/json',
      ...options.headers,
    },
    credentials: 'include',
    ...options,
  })
}

/**
 * Composable untuk request imperative API ($fetch) dan CSRF Sanctum
 */
export function useApiClient() {
  const config = useRuntimeConfig()

  const apiFetch = $fetch.create({
    baseURL: config.public.apiBase,
    headers: {
      Accept: 'application/json',
    },
    credentials: 'include',
  })

  // Inisialisasi CSRF cookie untuk autentikasi Sanctum SPA
  const getCsrfCookie = async () => {
    return await $fetch('/sanctum/csrf-cookie', {
      baseURL: config.public.backendUrl,
      credentials: 'include',
    })
  }

  return {
    apiFetch,
    getCsrfCookie,
  }
}
