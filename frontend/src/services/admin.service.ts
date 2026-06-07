import { api } from './api'
import type { ClassRoom } from '../types'

export type AdminStat = {
  key: string
  label: string
  value: number
  suffix?: string
}

export type AdminDashboardData = {
  stats: AdminStat[]
  recentClasses: Array<{ id: number; name: string; code: string; membersCount: number }>
}

export type ExamSubmission = {
  id: number
  userId: number
  fullName: string
  classCode?: string
  module: string
  moduleLabel: string
  score: number
  passed: boolean
  totalQuestions: number
  correctQuestions: number
  durationSec: number
  completedAt: string
  sessionId?: string
  violationCount: number
  integrityLogs: Array<{
    type: string
    at: string
    meta?: Record<string, unknown>
  }>
}

export type AdminReportsData = {
  topQuizUsers: Array<{
    userId: number
    fullName: string
    email?: string
    classCode?: string
    avgScore: number
    bestScore: number
    attempts: number
  }>
  moduleExamStats: Array<{
    module: string
    moduleLabel: string
    attempts: number
    avgScore: number
    passedCount: number
    passRate: number
  }>
  classOverview: Array<{
    id: number
    code: string
    name: string
    trainerName: string
    membersCount: number
    quizAttempts: number
    examAttempts: number
    avgQuizScore: number | null
  }>
  studentsWithoutClass: number
  recentExamSubmissions: ExamSubmission[]
  filterClassId: number | null
}

export type AdminStudent = {
  id: number
  email: string
  first_name: string
  last_name: string
  xp_points: number
  streak_days: number
  is_active: boolean
  class_code?: string
  class_name?: string
}

export async function adminDashboard() {
  const res = await api.get<{ data: AdminDashboardData }>('/admin/dashboard')
  return res.data.data
}

export async function adminStudents(classRoomId?: number, unassigned?: boolean) {
  const res = await api.get<{ data: AdminStudent[]; meta: unknown }>('/admin/students', {
    params: {
      ...(classRoomId ? { class_room_id: classRoomId } : {}),
      ...(unassigned ? { unassigned: 1 } : {}),
    },
  })
  return res.data
}

export async function adminClasses() {
  const res = await api.get<{ data: ClassRoom[] }>('/admin/classes')
  return res.data.data
}

export async function adminCreateClass(payload: {
  name: string
  code: string
  description?: string
  trainer_id?: number
}) {
  const res = await api.post<{ data: ClassRoom; message: string }>('/admin/classes', payload)
  return res.data
}

export async function adminClassDetail(id: number) {
  const res = await api.get<{ data: ClassRoom & { members?: AdminStudent[] } }>(`/admin/classes/${id}`)
  return res.data.data
}

export async function adminAddMember(classId: number, userId: number) {
  const res = await api.post(`/admin/classes/${classId}/members`, { user_id: userId })
  return res.data
}

export async function adminRemoveMember(classId: number, userId: number) {
  const res = await api.delete(`/admin/classes/${classId}/members/${userId}`)
  return res.data
}

export async function adminReports(classRoomId?: number) {
  const res = await api.get<{ data: AdminReportsData }>('/admin/reports', {
    params: classRoomId ? { class_room_id: classRoomId } : undefined,
  })
  return res.data.data
}

export async function adminUnassignedStudents() {
  const res = await api.get<{ data: AdminStudent[] }>('/admin/students', {
    params: { unassigned: 1 },
  })
  return res.data.data
}
