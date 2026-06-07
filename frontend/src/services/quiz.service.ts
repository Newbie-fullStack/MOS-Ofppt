import { api } from './api'
import type { Quiz, QuizAttempt } from '../types'

export async function listQuizzes(module: string) {
  const res = await api.get<{ data: Quiz[] }>(`/quizzes/${module}`)
  return res.data.data
}

export async function getQuiz(id: string) {
  const res = await api.get<{ data: Quiz }>(`/quizzes/${id}`)
  return res.data.data
}

export async function submitQuizAttempt(id: string, payload: { answers: Record<string, number>; duration_sec: number }) {
  const res = await api.post<{ data: QuizAttempt; newBadges?: unknown[] }>(`/quizzes/${id}/attempt`, payload)
  return res.data
}

export async function previewExam(module: string) {
  const res = await api.get<{ data: any }>(`/exam/${module}`)
  return res.data.data
}

export async function beginExam(module: string) {
  const res = await api.post<{ data: any }>(`/exam/${module}/begin`)
  return res.data.data
}

export async function logExamEvents(
  sessionId: string,
  events: Array<{ type: string; at: string; meta?: Record<string, unknown> }>,
) {
  await api.post(`/exam/session/${sessionId}/events`, { events })
}

export async function submitExam(
  module: string,
  payload: {
    session_id: string
    answers: Record<string, number>
    duration_sec: number
    started_at: string
  },
) {
  const res = await api.post<{ data: any; newBadges?: unknown[] }>(`/exam/${module}/submit`, payload)
  return res.data
}

