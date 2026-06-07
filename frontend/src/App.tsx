import { Navigate, Route, Routes, useLocation } from 'react-router-dom'
import { useAuthStore } from './store/authStore'
import { useAuthInit } from './hooks/useAuthInit'
import AppShell from './components/layout/AppShell'
import LandingPage from './pages/LandingPage'
import LoginPage from './pages/auth/LoginPage'
import RegisterPage from './pages/auth/RegisterPage'
import ForgotPasswordPage from './pages/auth/ForgotPasswordPage'
import DashboardPage from './pages/DashboardPage'
import ModuleLessonsPage from './pages/modules/ModuleLessonsPage'
import LessonDetailPage from './pages/modules/LessonDetailPage'
import QuizListPage from './pages/quiz/QuizListPage'
import QuizTakePage from './pages/quiz/QuizTakePage'
import ExamPage from './pages/exam/ExamPage'
import ProgressPage from './pages/progress/ProgressPage'
import ProfilePage from './pages/ProfilePage'
import CertificatesPage from './pages/CertificatesPage'
import BadgesPage from './pages/BadgesPage'
import AdminDashboardPage from './pages/admin/AdminDashboardPage'
import AdminStudentsPage from './pages/admin/AdminStudentsPage'
import AdminClassesPage from './pages/admin/AdminClassesPage'
import AdminReportsPage from './pages/admin/AdminReportsPage'
import SelectClassPage from './pages/SelectClassPage'

function Protected({ children }: { children: React.ReactNode }) {
  const token = useAuthStore((s) => s.token)
  const ready = useAuthInit()
  if (!ready) return <div className="p-6 text-sm text-slate-600">Chargement…</div>
  if (!token) return <Navigate to="/login" replace />
  return <>{children}</>
}

function RequireRole({ roles, children }: { roles: Array<'TRAINER' | 'ADMIN'>; children: React.ReactNode }) {
  const user = useAuthStore((s) => s.user)
  if (!user) return <div className="p-6 text-sm text-slate-600">Chargement…</div>
  if (!roles.includes(user.role as 'TRAINER' | 'ADMIN')) return <Navigate to="/dashboard" replace />
  return <>{children}</>
}

function RequireStudentClass({ children }: { children: React.ReactNode }) {
  const user = useAuthStore((s) => s.user)
  const { pathname } = useLocation()
  if (user?.role === 'STUDENT' && user.needsClassSelection && pathname !== '/select-class') {
    return <Navigate to="/select-class" replace />
  }
  return <>{children}</>
}

export default function App() {
  return (
    <Routes>
      <Route path="/" element={<LandingPage />} />
      <Route path="/login" element={<LoginPage />} />
      <Route path="/register" element={<RegisterPage />} />
      <Route path="/forgot-password" element={<ForgotPasswordPage />} />
      <Route
        element={
          <Protected>
            <RequireStudentClass>
              <AppShell />
            </RequireStudentClass>
          </Protected>
        }
      >
        <Route path="/select-class" element={<SelectClassPage />} />
        <Route path="/dashboard" element={<DashboardPage />} />
        <Route path="/modules/:module/lessons" element={<ModuleLessonsPage />} />
        <Route path="/modules/:module/lessons/:slug" element={<LessonDetailPage />} />
        <Route path="/quizzes/:module" element={<QuizListPage />} />
        <Route path="/quizzes/take/:id" element={<QuizTakePage />} />
        <Route path="/exam/:module" element={<ExamPage />} />
        <Route path="/progress" element={<ProgressPage />} />
        <Route path="/profile" element={<ProfilePage />} />
        <Route path="/certificates" element={<CertificatesPage />} />
        <Route path="/badges" element={<BadgesPage />} />

        <Route
          path="/admin/dashboard"
          element={
            <RequireRole roles={['TRAINER', 'ADMIN']}>
              <AdminDashboardPage />
            </RequireRole>
          }
        />
        <Route
          path="/admin/students"
          element={
            <RequireRole roles={['TRAINER', 'ADMIN']}>
              <AdminStudentsPage />
            </RequireRole>
          }
        />
        <Route
          path="/admin/classes"
          element={
            <RequireRole roles={['TRAINER', 'ADMIN']}>
              <AdminClassesPage />
            </RequireRole>
          }
        />
        <Route
          path="/admin/reports"
          element={
            <RequireRole roles={['TRAINER', 'ADMIN']}>
              <AdminReportsPage />
            </RequireRole>
          }
        />
      </Route>
      <Route path="*" element={<Navigate to="/dashboard" replace />} />
    </Routes>
  )
}

