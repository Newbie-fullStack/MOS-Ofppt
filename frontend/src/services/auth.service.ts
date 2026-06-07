import { api } from './api'
import type { User } from '../types'

export async function login(payload: { email: string; password: string }) {
  const res = await api.post<{ data: { token: string; user: User } }>('/auth/login', payload)
  return res.data.data
}

export async function register(payload: {
  first_name: string
  last_name: string
  email: string
  password: string
  password_confirmation: string
  class_code: string
}) {
  const res = await api.post<{ data: { token: string; user: User } }>('/auth/register', payload)
  return res.data.data
}

export async function me() {
  const res = await api.get<{ data: User }>('/user')
  return res.data.data
}

export async function logout() {
  await api.post('/auth/logout')
}

export async function forgotPassword(email: string) {
  const res = await api.post('/auth/forgot-password', { email })
  return res.data
}

