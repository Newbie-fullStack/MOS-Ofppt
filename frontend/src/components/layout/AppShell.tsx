import { Outlet, useLocation, useNavigate } from 'react-router-dom'
import { useEffect, useState } from 'react'
import AppSidebar from './AppSidebar'
import AppTopBar from './AppTopBar'
import { useAuthStore } from '../../store/authStore'
import { logout } from '../../services/auth.service'

export default function AppShell() {
  const navigate = useNavigate()
  const location = useLocation()
  const doLogout = useAuthStore((s) => s.logout)
  const role = useAuthStore((s) => s.user?.role)
  const canAdmin = role === 'TRAINER' || role === 'ADMIN'

  const isExamPage = location.pathname.startsWith('/exam/')
  const [sidebarOpen, setSidebarOpen] = useState(false)

  useEffect(() => {
    document.body.style.overflow = sidebarOpen ? 'hidden' : ''
    return () => {
      document.body.style.overflow = ''
    }
  }, [sidebarOpen])

  const closeSidebar = () => setSidebarOpen(false)

  if (isExamPage) {
    return (
      <div className="min-h-full bg-slate-50">
        <div className="mx-auto max-w-5xl px-4 py-6 md:px-8 md:py-8">
          <Outlet />
        </div>
      </div>
    )
  }

  return (
    <div className="flex min-h-full">
      {sidebarOpen ? (
        <button
          type="button"
          className="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-[1px] md:hidden"
          aria-label="Fermer le menu"
          onClick={closeSidebar}
        />
      ) : null}

      <aside
        className={`fixed inset-y-0 left-0 z-50 flex w-[272px] shrink-0 flex-col border-r border-slate-200/80 bg-white shadow-xl transition-transform duration-200 ease-out md:relative md:z-0 md:translate-x-0 md:shadow-none ${
          sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'
        }`}
      >
        <div className="flex min-h-0 flex-1 flex-col overflow-y-auto">
          <AppSidebar canAdmin={canAdmin} onNavigate={closeSidebar} />
        </div>
        <div className="border-t border-slate-100 px-4 py-3 md:hidden">
          <button
            type="button"
            className="w-full rounded-xl border border-slate-200 py-2 text-sm font-medium text-slate-700"
            onClick={async () => {
              try {
                await logout()
              } finally {
                doLogout()
                navigate('/login')
              }
            }}
          >
            Déconnexion
          </button>
        </div>
      </aside>

      <div className="flex min-h-full min-w-0 flex-1 flex-col">
        <AppTopBar onOpenSidebar={() => setSidebarOpen(true)} />
        <div className="flex-1 overflow-auto px-4 py-6 md:px-8 md:py-8 lg:px-10">
          <Outlet />
        </div>
      </div>
    </div>
  )
}
