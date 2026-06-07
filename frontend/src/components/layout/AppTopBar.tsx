import { useEffect, useRef, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { ChevronDown, Menu, Search } from 'lucide-react'
import { useAuthStore } from '../../store/authStore'
import { logout } from '../../services/auth.service'

export type AppTopBarProps = {
  onOpenSidebar?: () => void
}

export default function AppTopBar({ onOpenSidebar }: AppTopBarProps) {
  const navigate = useNavigate()
  const user = useAuthStore((s) => s.user)
  const doLogout = useAuthStore((s) => s.logout)

  const [menuOpen, setMenuOpen] = useState(false)
  const btnRef = useRef<HTMLButtonElement>(null)

  useEffect(() => {
    if (!menuOpen) return
    const close = (e: MouseEvent) => {
      if (btnRef.current && !btnRef.current.contains(e.target as Node)) {
        const panel = document.getElementById('user-menu-panel')
        if (panel && !panel.contains(e.target as Node)) setMenuOpen(false)
      }
    }
    document.addEventListener('mousedown', close)
    return () => document.removeEventListener('mousedown', close)
  }, [menuOpen])

  const firstName = user?.firstName ?? 'Stagiaire'

  const handleLogout = async () => {
    setMenuOpen(false)
    try {
      await logout()
    } finally {
      doLogout()
      navigate('/login')
    }
  }

  return (
    <header className="sticky top-0 z-30 flex min-h-[72px] items-center gap-4 border-b border-slate-200/80 bg-white/90 px-4 py-3 backdrop-blur-md md:px-8">
      <button
        type="button"
        className="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm md:hidden"
        aria-label="Ouvrir le menu"
        onClick={onOpenSidebar}
      >
        <Menu className="h-5 w-5" />
      </button>

      <div className="relative mx-auto flex w-full max-w-2xl flex-1">
        <Search className="pointer-events-none absolute left-4 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-slate-400" />
        <input
          type="search"
          placeholder="Rechercher un cours…"
          className="w-full rounded-2xl border border-slate-200/90 bg-slate-50 py-2.5 pl-11 pr-4 text-sm text-slate-800 placeholder:text-slate-400 focus:border-blue-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          aria-label="Rechercher un cours"
        />
      </div>

      <div className="flex shrink-0 items-center gap-2 md:gap-4">
        <div className="relative">
          <button
            ref={btnRef}
            type="button"
            id="user-menu-trigger"
            className="flex items-center gap-2 rounded-xl border border-slate-200 bg-white py-1.5 pl-1.5 pr-2 shadow-sm transition hover:bg-slate-50"
            aria-expanded={menuOpen}
            aria-haspopup="true"
            onClick={() => setMenuOpen((o) => !o)}
          >
            <span className="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-600 text-[13px] font-bold uppercase text-white">
              {firstName.slice(0, 1)}
            </span>
            <span className="hidden max-w-[100px] truncate text-sm font-medium text-slate-800 md:inline">{firstName}</span>
            <ChevronDown className={`hidden h-4 w-4 text-slate-500 transition md:inline ${menuOpen ? 'rotate-180' : ''}`} />
          </button>

          {menuOpen ? (
            <div
              id="user-menu-panel"
              role="menu"
              className="absolute right-0 top-full z-40 mt-2 w-52 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-xl"
            >
              <button
                type="button"
                role="menuitem"
                className="block w-full px-4 py-2.5 text-left text-sm text-slate-700 hover:bg-slate-50"
                onClick={() => {
                  setMenuOpen(false)
                  navigate('/profile')
                }}
              >
                Mon profil
              </button>
              <button
                type="button"
                role="menuitem"
                className="block w-full px-4 py-2.5 text-left text-sm text-rose-600 hover:bg-rose-50"
                onClick={handleLogout}
              >
                Déconnexion
              </button>
            </div>
          ) : null}
        </div>
      </div>
    </header>
  )
}
