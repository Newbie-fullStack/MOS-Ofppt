import type { ReactNode } from 'react'
import { useEffect, useMemo, useState } from 'react'
import { Link, useLocation } from 'react-router-dom'
import {
  ArrowRight,
  BookOpenCheck,
  CheckCircle2,
  ClipboardList,
  Clock,
  Flame,
  LayoutGrid,
  Target,
} from 'lucide-react'
import BadgeIcon from '../components/common/BadgeIcon'
import CircularLessonProgress from '../components/dashboard/CircularLessonProgress'
import { listModules } from '../services/course.service'
import { getProgress } from '../services/progress.service'
import { getUserBadges, getUserStats } from '../services/user.service'
import { useAuthStore } from '../store/authStore'
import type { AppModule } from '../types'

const MODULE_LETTER: Record<string, string> = {
  WORD: 'W',
  EXCEL: 'X',
  POWERPOINT: 'P',
}

const MODULE_THEME: Record<string, { accent: string; btn: string }> = {
  WORD: {
    accent: '#2563eb',
    btn: 'bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-600/25',
  },
  EXCEL: {
    accent: '#16a34a',
    btn: 'bg-green-600 hover:bg-green-700 text-white shadow-lg shadow-green-600/25',
  },
  POWERPOINT: {
    accent: '#ea580c',
    btn: 'bg-orange-500 hover:bg-orange-600 text-white shadow-lg shadow-orange-500/25',
  },
}

const MODULE_PRODUCT: Record<string, string> = {
  WORD: 'Microsoft Word',
  EXCEL: 'Microsoft Excel',
  POWERPOINT: 'Microsoft PowerPoint',
}

type ModuleRow = {
  module: string
  label: string
  totalLessons: number
  completedLessons: number
  progressPct: number
  isEnrolled?: boolean
}

function normalizeLesson(rec: Record<string, unknown> | undefined) {
  if (!rec || typeof rec !== 'object') return null
  const r = rec as Record<string, unknown>
  const raw = r.app_module ?? r.appModule
  const slug = r.slug as string | undefined
  const title = r.title as string | undefined
  if (!slug || !title) return null
  return { slug, title, module: String(raw ?? '').toUpperCase() }
}

export default function DashboardPage() {
  const location = useLocation()
  const displayName = useAuthStore((s) => {
    const u = s.user
    const n = `${u?.firstName ?? ''}`.trim()
    return n || 'Apprenant'
  })

  const [modules, setModules] = useState<ModuleRow[]>([])
  const [progressRows, setProgressRows] = useState<Record<string, unknown>[]>([])
  const [stats, setStats] = useState<{ streakDays?: number } | null>(null)
  const [recentBadges, setRecentBadges] = useState<any[]>([])

  useEffect(() => {
    Promise.all([
      listModules().then(setModules).catch(() => setModules([])),
      getProgress().then(setProgressRows).catch(() => setProgressRows([])),
      getUserStats().then((d) => setStats(d)).catch(() => setStats(null)),
      getUserBadges().then(setRecentBadges).catch(() => setRecentBadges([])),
    ]).catch(() => undefined)
  }, [])

  useEffect(() => {
    if (location.hash !== '#mes-cours') return
    requestAnimationFrame(() => {
      document.getElementById('mes-cours')?.scrollIntoView({ behavior: 'smooth', block: 'start' })
    })
  }, [location.hash, modules.length])

  const inscribed = useMemo(() => {
    if (!modules.length) return 0
    const n = modules.filter((m) => m.isEnrolled).length
    return n || modules.length
  }, [modules])

  const coursTerminés = useMemo(() => modules.filter((m) => m.progressPct >= 100).length, [modules])
  const coursEnCours = useMemo(
    () => modules.filter((m) => m.progressPct > 0 && m.progressPct < 100).length,
    [modules]
  )
  const totalModules = modules.length || 3
  const pctComplétés = totalModules ? Math.round((coursTerminés / totalModules) * 100) : 0
  const pctEnCours = totalModules ? Math.round((coursEnCours / totalModules) * 100) : 0

  const resume = useMemo(() => {
    const cand = [...progressRows]
      .filter((row) => {
        const lesson = normalizeLesson(row.lesson as Record<string, unknown> | undefined)
        const completed = !!(row as { completed?: boolean }).completed
        return !!(lesson && !completed)
      })
      .sort((a, b) => {
        const ta = Date.parse(String((a as { updated_at?: string }).updated_at ?? 0))
        const tb = Date.parse(String((b as { updated_at?: string }).updated_at ?? 0))
        return tb - ta || 0
      })
      .find(Boolean) as Record<string, unknown> | undefined

    if (!cand) return null
    const lesson = normalizeLesson(cand.lesson as Record<string, unknown> | undefined)
    if (!lesson) return null
    const modRow = modules.find((m) => String(m.module).toUpperCase() === lesson.module)
    const pct = modRow?.progressPct ?? 0
    return { ...lesson, progressPct: pct }
  }, [progressRows, modules])

  const badgesPreview = recentBadges.slice(0, 3)

  const dashCard =
    'flex min-h-[220px] flex-col rounded-3xl p-6 shadow-md shadow-slate-200/45 ring-1 ring-slate-200/70'

  return (
    <div className="space-y-10">
      <header>
        <h1 className="text-3xl font-bold tracking-tight text-slate-900 md:text-[2rem]">
          Bonjour, {displayName} ! <span aria-hidden>👋</span>
        </h1>
        <p className="mt-2 max-w-xl text-[15px] text-slate-600">
          Continuez votre apprentissage et atteignez vos objectifs MOS avec l&apos;OFPPT.
        </p>
      </header>

      <section className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Statistiques">
        <StatCard
          tone="purple"
          icon={<LayoutGrid className="h-5 w-5" />}
          title="Cours inscrits"
          value={`${inscribed}`}
          subtitle="Modules MOS disponibles"
        />
        <StatCard
          tone="green"
          icon={<CheckCircle2 className="h-5 w-5" />}
          title="Terminés"
          value={`${coursTerminés}`}
          subtitle={`(${pctComplétés}% complétés)`}
        />
        <StatCard
          tone="amber"
          icon={<Clock className="h-5 w-5" />}
          title="En progrès"
          value={`${coursEnCours}`}
          subtitle={`(${pctEnCours}% en cours)`}
        />
        <StatCard
          tone="blue"
          icon={<Flame className="h-5 w-5" />}
          title="Série actuelle"
          value={`${stats?.streakDays ?? 0}`}
          subtitle="Jours consécutifs avec activité MOS"
        />
      </section>

      <section id="mes-cours" className="scroll-mt-28">
        <div className="mb-5 flex flex-wrap items-end justify-between gap-4">
          <h2 className="text-xl font-bold tracking-tight text-slate-900">Mes cours</h2>
          <button
            type="button"
            className="text-sm font-semibold text-blue-600 hover:text-blue-700"
            onClick={() =>
              document.getElementById('mes-cours')?.scrollIntoView({ behavior: 'smooth', block: 'start' })
            }
          >
            Voir tous les cours
            <ArrowRight className="ml-1 inline-block h-4 w-4 align-text-bottom" />
          </button>
        </div>
        <div className="grid gap-6 md:grid-cols-3">
          {modules.map((m) => {
            const modKey = String(m.module).toUpperCase()
            const theme = MODULE_THEME[modKey] ?? MODULE_THEME.WORD
            const letter = MODULE_LETTER[modKey] ?? modKey.slice(0, 1)
            const pctBar = Math.min(100, Math.round(Number(m.progressPct) || 0))
            const color = theme.accent

            return (
              <article
                key={m.module}
                className="flex flex-col overflow-hidden rounded-3xl bg-white shadow-md shadow-slate-200/45 ring-1 ring-slate-200/70"
              >
                <div className="flex items-start justify-between gap-4 p-6 pb-4">
                  <div className="flex min-w-0 items-center gap-3">
                    <span
                      className="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-lg font-bold text-white shadow-inner"
                      style={{ backgroundColor: color }}
                      aria-hidden
                    >
                      {letter}
                    </span>
                    <div className="min-w-0">
                      <p className="text-[11px] font-semibold uppercase tracking-wide text-slate-500">{modKey}</p>
                      <p className="truncate font-semibold text-slate-900">{m.label}</p>
                    </div>
                  </div>
                  <CircularLessonProgress value={pctBar} color={color} />
                </div>
                <div className="px-6 pb-5">
                  <p className="text-sm font-medium text-slate-700">
                    {m.completedLessons}/{m.totalLessons} leçons complétées
                  </p>
                  <div className="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                    <div className="h-full rounded-full" style={{ width: `${pctBar}%`, backgroundColor: color }} />
                  </div>
                </div>
                <div className="border-t border-slate-100 p-6 pt-5">
                  <Link
                    to={`/modules/${m.module}/lessons`}
                    className={`flex items-center justify-center gap-2 rounded-2xl py-3 text-sm font-semibold transition ${theme.btn}`}
                  >
                    Continuer
                    <ArrowRight className="h-4 w-4" />
                  </Link>
                </div>
              </article>
            )
          })}
          {!modules.length ? (
            <p className="col-span-full rounded-3xl bg-white px-6 py-10 text-center text-sm text-slate-600 ring-1 ring-slate-200">
              Connexion aux modules impossible pour le moment. Vérifiez l&apos;API ou réessayez.
            </p>
          ) : null}
        </div>
      </section>

      <section className="grid gap-6 lg:grid-cols-3">
        <div className={`${dashCard} bg-white`}>
          <h3 className="text-lg font-semibold text-slate-900">Continuez où vous étiez</h3>
          {resume ? (
            <>
              <div className="mt-6 flex items-center gap-3">
                <span
                  className="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl text-lg font-bold text-white"
                  style={{ backgroundColor: MODULE_THEME[resume.module]?.accent ?? '#2563eb' }}
                  aria-hidden
                >
                  {MODULE_LETTER[resume.module] ?? resume.module.slice(0, 1)}
                </span>
                <div className="min-w-0">
                  <p className="truncate text-[15px] font-medium text-slate-900">
                    {MODULE_PRODUCT[resume.module] ?? resume.module} — Leçon
                  </p>
                  <p className="truncate text-sm text-slate-600">{resume.title}</p>
                </div>
              </div>
              <div className="mt-5 h-2 overflow-hidden rounded-full bg-slate-100">
                <div
                  className="h-full rounded-full"
                  style={{
                    width: `${Math.min(100, Math.round(Number(resume.progressPct) || 0))}%`,
                    backgroundColor: MODULE_THEME[resume.module]?.accent ?? '#2563eb',
                  }}
                />
              </div>
              <Link
                to={`/modules/${resume.module as AppModule}/lessons/${resume.slug}`}
                className="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700"
              >
                Reprendre le cours
                <ArrowRight className="ml-2 h-4 w-4" />
              </Link>
            </>
          ) : (
            <>
              <p className="mt-4 text-sm text-slate-600">
                Aucune leçon non terminée suivie pour le moment. Commencez un module ci-dessus.
              </p>
              <Link
                to="/dashboard#mes-cours"
                className="mt-6 inline-flex w-full items-center justify-center rounded-2xl border border-slate-200 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
              >
                Explorer mes cours
              </Link>
            </>
          )}
        </div>

        <div className={`${dashCard} relative overflow-hidden bg-gradient-to-br from-indigo-50 via-white to-sky-50`}>
          <h3 className="text-lg font-semibold text-slate-900">Objectif du jour</h3>
          <p className="mt-3 text-[15px] text-slate-700">
            Terminez <span className="font-semibold">1 leçon</span> entre aujourd&apos;hui et demain pour garder votre
            série.
          </p>
          <div className="relative z-[1] mt-4 rounded-2xl border border-blue-100/90 bg-white/90 px-4 py-4 shadow-sm backdrop-blur-sm">
            <div className="flex items-start gap-3">
              <BookOpenCheck className="mt-0.5 h-5 w-5 shrink-0 text-blue-600" aria-hidden />
              <div>
                <p className="font-medium text-slate-900">Une leçon complétée</p>
                <p className="mt-2 text-xs text-slate-600">
                  À synchroniser : nous brancherons ici vos objectifs personnalisés dès disponibles côté API.
                </p>
              </div>
            </div>
          </div>
          <div className="pointer-events-none absolute bottom-4 right-4 hidden items-end gap-0 sm:flex">
            <span className="flex h-[52px] w-[44px] items-center justify-center rounded-xl border border-blue-100 bg-white shadow-xl">
              <ClipboardList className="h-7 w-7 text-blue-500" aria-hidden />
            </span>
            <span className="-mb-3 -ml-2 flex h-14 w-14 items-center justify-center rounded-full border border-amber-200 bg-amber-50 shadow-lg">
              <Target className="h-8 w-8 text-amber-600" aria-hidden />
            </span>
          </div>
        </div>

        <div className={`${dashCard} bg-white`}>
          <div className="flex items-start justify-between gap-4">
            <h3 className="text-lg font-semibold text-slate-900">Badges récents</h3>
            <Link to="/badges" className="shrink-0 text-sm font-semibold text-blue-600 hover:text-blue-700">
              Voir tous
            </Link>
          </div>
          <div className="mt-5 flex flex-1 flex-col justify-center gap-4">
            {badgesPreview.length ? (
              badgesPreview.map((b) => (
                <div key={b.id ?? b.name} className="flex items-center gap-3">
                  <BadgeIcon badgeId={b.id} name={b.name} size="md" />
                  <div className="min-w-0 flex-1">
                    <p className="truncate font-medium text-slate-900">{String(b.name ?? 'Badge')}</p>
                    <p className="truncate text-xs text-slate-500">{String(b.description ?? 'Récompense MOS OFPPT')}</p>
                  </div>
                </div>
              ))
            ) : (
              <>
                <PlaceholderBadge label="Premiers pas" desc="Terminez votre première leçon" kind="word" />
                <PlaceholderBadge label="Persévérant" desc="Réussissez un quiz de module" kind="exam" />
                <PlaceholderBadge label="Apprenant" desc="Conservez une série de plusieurs jours" kind="default" />
              </>
            )}
          </div>
        </div>
      </section>
    </div>
  )
}

function StatCard({
  tone,
  icon,
  title,
  value,
  subtitle,
}: {
  tone: 'purple' | 'green' | 'amber' | 'blue'
  icon: ReactNode
  title: string
  value: string
  subtitle: string
}) {
  const blobs: Record<string, string> = {
    purple: 'from-violet-500/85 to-purple-600/90 shadow-violet-500/35',
    green: 'from-emerald-400/90 to-green-600/90 shadow-green-600/35',
    amber: 'from-amber-400/95 to-orange-500/95 shadow-orange-500/35',
    blue: 'from-sky-400/95 to-blue-600/95 shadow-blue-600/35',
  }

  return (
    <article className="rounded-3xl bg-white px-6 py-5 shadow-md shadow-slate-200/35 ring-1 ring-slate-200/60">
      <div className="flex items-start justify-between gap-4">
        <div>
          <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">{title}</p>
          <p className="mt-2 text-[1.85rem] font-bold tabular-nums leading-none tracking-tight text-slate-900">{value}</p>
          <p className="mt-3 text-[13px] text-slate-600">{subtitle}</p>
        </div>
        <span
          className={`flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br text-white shadow-xl ${blobs[tone]} `}
        >
          {icon}
        </span>
      </div>
    </article>
  )
}

function PlaceholderBadge({
  label,
  desc,
  kind = 'default',
}: {
  label: string
  desc: string
  kind?: 'word' | 'excel' | 'powerpoint' | 'exam' | 'default'
}) {
  return (
    <div className="flex items-center gap-3 opacity-60">
      <BadgeIcon name={kind === 'default' ? label : `${kind} starter`} size="md" />
      <div className="min-w-0 flex-1">
        <p className="font-medium text-slate-900">{label}</p>
        <p className="truncate text-xs text-slate-500">{desc}</p>
      </div>
    </div>
  )
}
