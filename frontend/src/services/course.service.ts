import { api } from './api'
import type { AppModule, Lesson } from '../types'

export async function listModules() {
  const res = await api.get<{ data: any[] }>('/modules')
  return res.data.data
}

export async function listLessons(module: AppModule | string) {
  const res = await api.get<{ data: Lesson[] }>(`/modules/${module}/lessons`)
  return res.data.data
}

export async function getLesson(module: AppModule | string, slug: string) {
  const res = await api.get<{ data: Lesson }>(`/modules/${module}/lessons/${slug}`)
  return res.data.data
}

export async function enroll(module: AppModule | string) {
  const res = await api.post(`/modules/${module}/enroll`)
  return res.data
}

