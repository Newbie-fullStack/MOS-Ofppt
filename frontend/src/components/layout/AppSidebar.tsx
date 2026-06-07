import {
  LayoutDashboard,
  BookOpen,
  TrendingUp,
  GraduationCap,
  Medal,
  Settings,
  HeadphonesIcon,
  Shield,
  Users,
  Layers,
  LineChart,
} from 'lucide-react'
import { NavLink, useLocation } from 'react-router-dom'

function navClasses(active: boolean) {
  return `flex items-center gap-3 rounded-xl px-3 py-2.5 text-[15px] font-medium transition-colors ${
    active ? 'bg-blue-50 text-blue-600 shadow-sm shadow-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
  }`
}

export type AppSidebarProps = {
  canAdmin?: boolean
  onNavigate?: () => void
}

export default function AppSidebar({ canAdmin = false, onNavigate }: AppSidebarProps) {
  const { pathname, hash } = useLocation()

  const dashActive = pathname === '/dashboard' && (!hash || hash === '')
  const mesCoursActive = pathname === '/dashboard' && hash === '#mes-cours'

  const wrapClick = () => {
    onNavigate?.()
  }

  return (
    <div className="flex h-full min-h-0 flex-col gap-8 px-4 py-6">
      <LinkBrand onClick={wrapClick} />

      <nav className="flex flex-col gap-1" aria-label="Navigation principale">
        <NavLink end to="/dashboard" className={() => navClasses(dashActive)} onClick={wrapClick}>
          <LayoutDashboard className="h-[22px] w-[22px] shrink-0" aria-hidden />
          Dashboard
        </NavLink>

        <NavLink
          to="/dashboard#mes-cours"
          className={() => navClasses(mesCoursActive)}
          onClick={(e) => {
            wrapClick()
            if (pathname === '/dashboard') {
              e.preventDefault()
              window.location.hash = 'mes-cours'
              document.getElementById('mes-cours')?.scrollIntoView({ behavior: 'smooth' })
            }
          }}
        >
          <BookOpen className="h-[22px] w-[22px] shrink-0" aria-hidden />
          Mes cours
        </NavLink>

        <NavLink to="/progress" className={({ isActive }) => navClasses(isActive)} onClick={wrapClick}>
          <TrendingUp className="h-[22px] w-[22px] shrink-0" aria-hidden />
          Progression
        </NavLink>

        <NavLink to="/certificates" className={({ isActive }) => navClasses(isActive)} onClick={wrapClick}>
          <GraduationCap className="h-[22px] w-[22px] shrink-0" aria-hidden />
          Certificats
        </NavLink>

        <NavLink to="/badges" className={({ isActive }) => navClasses(isActive)} onClick={wrapClick}>
          <Medal className="h-[22px] w-[22px] shrink-0" aria-hidden />
          Badges
        </NavLink>

        <NavLink to="/profile" className={({ isActive }) => navClasses(isActive)} onClick={wrapClick}>
          <Settings className="h-[22px] w-[22px] shrink-0" aria-hidden />
          Paramètres
        </NavLink>
      </nav>

      {canAdmin ? (
        <div className="rounded-2xl border border-slate-100 bg-slate-50/80 p-3">
          <p className="mb-2 flex items-center gap-2 px-1 text-xs font-semibold uppercase tracking-wide text-slate-500">
            <Shield className="h-3.5 w-3.5" aria-hidden />
            Administration
          </p>
          <div className="flex flex-col gap-0.5">
            <NavLink
              to="/admin/dashboard"
              className={({ isActive }) => navClasses(isActive)}
              onClick={wrapClick}
            >
              <LayoutDashboard className="h-[20px] w-[20px] shrink-0" aria-hidden />
              Dashboard admin
            </NavLink>
            <NavLink
              to="/admin/students"
              className={({ isActive }) => navClasses(isActive)}
              onClick={wrapClick}
            >
              <Users className="h-[20px] w-[20px] shrink-0" aria-hidden />
              Apprenants
            </NavLink>
            <NavLink
              to="/admin/classes"
              className={({ isActive }) => navClasses(isActive)}
              onClick={wrapClick}
            >
              <Layers className="h-[20px] w-[20px] shrink-0" aria-hidden />
              Classes
            </NavLink>
            <NavLink
              to="/admin/reports"
              className={({ isActive }) => navClasses(isActive)}
              onClick={wrapClick}
            >
              <LineChart className="h-[20px] w-[20px] shrink-0" aria-hidden />
              Rapports
            </NavLink>
          </div>
        </div>
      ) : null}

      <div className="mt-auto">
        <a
          href="mailto:contact@mos-ofppt.ma?subject=Aide%20MOS%20OFPPT"
          className="flex items-center gap-2 px-3 py-2 text-sm text-slate-500 hover:text-blue-600"
          onClick={wrapClick}
        >
          <HeadphonesIcon className="h-4 w-4 shrink-0" aria-hidden />
          Besoin d&apos;aide ? Contactez-nous
        </a>
      </div>
    </div>
  )
}

function LinkBrand({ onClick }: { onClick: () => void }) {
  return (
    <NavLink
      to="/dashboard"
      className="flex items-center gap-2 rounded-xl px-1 py-0.5 text-lg font-semibold tracking-tight text-slate-900 hover:text-blue-600"
      onClick={onClick}
    >
      <span className="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-white shadow-lg shadow-blue-600/25">
        <GraduationCap className="h-6 w-6" aria-hidden />
      </span>
      <span>MOS OFPPT</span>
    </NavLink>
  )
}
