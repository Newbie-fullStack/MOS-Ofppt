import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { GraduationCap, Layers, LineChart, Target, Users } from 'lucide-react'
import { adminDashboard, type AdminDashboardData } from '../../services/admin.service'

const statIcons: Record<string, typeof Users> = {
  students: Users,
  trainers: GraduationCap,
  classes: Layers,
  quizAttempts: Target,
  examAttempts: LineChart,
  examPassRate: Target,
}

export default function AdminDashboardPage() {
  const [data, setData] = useState<AdminDashboardData | null>(null)

  useEffect(() => {
    adminDashboard().then(setData).catch(() => setData(null))
  }, [])

  return (
    <div className="space-y-8">
      <div>
        <h1 className="text-2xl font-bold tracking-tight text-slate-900">Administration</h1>
        <p className="mt-1 text-sm text-slate-600">Vue d&apos;ensemble des classes DD101–DD202 et de la progression MOS.</p>
      </div>

      <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        {(data?.stats ?? []).map((stat) => {
          const Icon = statIcons[stat.key] ?? Users
          return (
            <article
              key={stat.key}
              className="rounded-2xl bg-white p-5 shadow-md shadow-slate-200/40 ring-1 ring-slate-200/70"
            >
              <div className="flex items-start justify-between gap-3">
                <div>
                  <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">{stat.label}</p>
                  <p className="mt-2 text-3xl font-bold tabular-nums text-slate-900">
                    {stat.value}
                    {stat.suffix ?? ''}
                  </p>
                </div>
                <span className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                  <Icon className="h-5 w-5" aria-hidden />
                </span>
              </div>
            </article>
          )
        })}
        {!data && <div className="col-span-full text-sm text-slate-600">Chargement des statistiques…</div>}
      </div>

      {data?.recentClasses?.length ? (
        <section>
          <div className="mb-4 flex items-center justify-between">
            <h2 className="text-lg font-semibold text-slate-900">Classes OFPPT</h2>
            <Link to="/admin/classes" className="text-sm font-semibold text-blue-600 hover:text-blue-700">
              Gérer les classes
            </Link>
          </div>
          <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            {data.recentClasses.map((c) => (
              <Link
                key={c.id}
                to="/admin/classes"
                className="rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-blue-200 hover:shadow-md"
              >
                <p className="text-lg font-bold text-blue-600">{c.code}</p>
                <p className="mt-1 line-clamp-2 text-sm font-medium text-slate-900">{c.name}</p>
                <p className="mt-2 text-xs text-slate-500">{c.membersCount} apprenant(s)</p>
              </Link>
            ))}
          </div>
        </section>
      ) : null}
    </div>
  )
}
