import { api } from './api'

export async function getProgress() {
  const res = await api.get<{ data: any[] }>('/progress')
  return res.data.data
}

export async function updateProgress(lessonId: number, payload: { completed?: boolean; time_spent_sec?: number }) {
  const res = await api.patch(`/progress/${lessonId}`, payload)
  return res.data
}

