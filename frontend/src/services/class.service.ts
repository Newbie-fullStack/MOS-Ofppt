import { api } from './api'
import type { ClassRoom } from '../types'

export async function listAvailableClasses() {
  const res = await api.get<{ data: ClassRoom[] }>('/classes/available')
  return res.data.data
}

export async function joinClass(classCode: string) {
  const res = await api.post<{ data: { classRoom: ClassRoom }; message: string }>('/classes/join', {
    class_code: classCode,
  })
  return res.data
}
