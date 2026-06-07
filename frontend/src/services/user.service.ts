import { api } from './api'

export async function getUserStats() {
  const res = await api.get<{ data: any }>('/user/stats')
  return res.data.data
}

export async function getUserBadges() {
  const res = await api.get<{ data: any[] }>('/user/badges')
  return res.data.data
}

