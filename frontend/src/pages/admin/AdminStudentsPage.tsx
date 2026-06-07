import { useEffect, useState } from 'react'
import { adminClasses, adminStudents, type AdminStudent } from '../../services/admin.service'
import type { ClassRoom } from '../../types'

export default function AdminStudentsPage() {
  const [items, setItems] = useState<AdminStudent[]>([])
  const [classes, setClasses] = useState<ClassRoom[]>([])
  const [classFilter, setClassFilter] = useState('')
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    adminClasses().then(setClasses).catch(() => setClasses([]))
  }, [])

  useEffect(() => {
    setLoading(true)
    const unassigned = classFilter === 'unassigned'
    const id = classFilter && !unassigned ? Number(classFilter) : undefined
    adminStudents(id, unassigned)
      .then((r) => setItems(r.data ?? []))
      .catch(() => setItems([]))
      .finally(() => setLoading(false))
  }, [classFilter])

  return (
    <div className="space-y-6">
      <div className="flex flex-wrap items-end justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold tracking-tight text-slate-900">Apprenants</h1>
          <p className="mt-1 text-sm text-slate-600">Liste des stagiaires par classe OFPPT.</p>
        </div>
        <label className="block">
          <span className="text-xs font-medium text-slate-500">Classe</span>
          <select
            className="mt-1 rounded-xl border border-slate-200 px-3 py-2 text-sm"
            value={classFilter}
            onChange={(e) => setClassFilter(e.target.value)}
          >
            <option value="">Toutes</option>
            {classes.map((c) => (
              <option key={c.id} value={c.id}>
                {c.code}
              </option>
            ))}
            <option value="unassigned">Sans classe</option>
          </select>
        </label>
      </div>

      <div className="overflow-hidden rounded-2xl bg-white shadow-md ring-1 ring-slate-200/70">
        <div className="grid grid-cols-12 gap-2 border-b border-slate-100 bg-slate-50/80 px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
          <div className="col-span-4">Nom</div>
          <div className="col-span-3">Email</div>
          <div className="col-span-2">Classe</div>
          <div className="col-span-1">XP</div>
          <div className="col-span-1">Série</div>
          <div className="col-span-1">Actif</div>
        </div>
        {loading ? (
          <p className="px-4 py-8 text-sm text-slate-600">Chargement…</p>
        ) : (
          items.map((u) => (
            <div
              key={u.id}
              className="grid grid-cols-12 gap-2 border-b border-slate-50 px-4 py-3 text-sm last:border-0"
            >
              <div className="col-span-4 font-medium text-slate-900">
                {u.first_name} {u.last_name}
              </div>
              <div className="col-span-3 truncate text-slate-600">{u.email}</div>
              <div className="col-span-2">
                {u.class_code ? (
                  <span className="rounded-md bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700">
                    {u.class_code}
                  </span>
                ) : (
                  <span className="text-xs text-amber-600">Non assigné</span>
                )}
              </div>
              <div className="col-span-1 tabular-nums">{u.xp_points}</div>
              <div className="col-span-1 tabular-nums">{u.streak_days}</div>
              <div className="col-span-1">{u.is_active ? 'Oui' : 'Non'}</div>
            </div>
          ))
        )}
        {!loading && items.length === 0 && (
          <p className="px-4 py-8 text-center text-sm text-slate-500">Aucun apprenant trouvé.</p>
        )}
      </div>
    </div>
  )
}
