import axios from 'axios'
import { useAuthStore } from '../store/authStore'

const apiUrl = import.meta.env.VITE_API_URL as string | undefined

export const api = axios.create({
  baseURL: apiUrl ? `${apiUrl}/api/v1` : '/api/v1',
  withCredentials: true,
  headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
})

api.interceptors.request.use((config) => {
  const token = useAuthStore.getState().token
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

api.interceptors.response.use(
  (r) => r,
  (error) => {
    if (error.response?.status === 401) useAuthStore.getState().logout()
    return Promise.reject(error)
  },
)

