export type AppModule = 'WORD' | 'EXCEL' | 'POWERPOINT'
export type Role = 'STUDENT' | 'TRAINER' | 'ADMIN'
export type Difficulty = 'BEGINNER' | 'INTERMEDIATE' | 'ADVANCED'

export interface ClassRoom {
  id: number
  name: string
  code: string
  description?: string | null
  is_active?: boolean
  members_count?: number
  trainer?: { id: number; first_name: string; last_name: string; email: string }
}

export interface User {
  id: number | string
  email: string
  firstName: string
  lastName: string
  role: Role
  avatarUrl?: string | null
  xpPoints: number
  streakDays: number
  isActive?: boolean
  classRoom?: { id: number; name: string; code: string } | null
  needsClassSelection?: boolean
}

export const CLASS_CODES = ['DD101', 'DD201', 'DD102', 'DD202'] as const

export interface Lesson {
  id: number
  slug: string
  appModule: AppModule
  title: string
  description: string
  order: number
  durationMin: number
  difficulty: Difficulty
  objectives: string[]
  mosObjectives: string[]
  thumbnailUrl?: string | null
  isPublished: boolean
  content?: unknown
}

export interface Quiz {
  id: string
  appModule: AppModule
  title: string
  description: string
  durationMin: number
  passingScore: number
  isExamMode: boolean
  isPublished: boolean
  questionsCount?: number
  questions?: Question[]
}

export interface Question {
  id: string
  appModule: AppModule
  domain: string
  difficulty: Difficulty
  questionText: string
  options: string[]
}

export interface QuizAttempt {
  id: number
  score: number
  totalQuestions: number
  correctQuestions: number
  passed: boolean
  durationSec: number
  completedAt?: string
  quiz?: { id: string; title?: string; passingScore?: number }
}

