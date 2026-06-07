import { useCallback, useEffect, useState } from 'react'
import { Plus, UserMinus, UserPlus, X } from 'lucide-react'
import {
  adminAddMember,
  adminClassDetail,
  adminClasses,
  adminCreateClass,
  adminRemoveMember,
  adminStudents,
} from '../../services/admin.service'
import type { AdminStudent } from '../../services/admin.service'
import type { ClassRoom } from '../../types'
import { CLASS_CODES } from '../../types'

export default function AdminClassesPage() {
  const [items, setItems] = useState<ClassRoom[]>([])
  const [selectedId, setSelectedId] = useState<number | null>(null)
  const [detail, setDetail] = useState<(ClassRoom & { members?: AdminStudent[] }) | null>(null)
  const [unassigned, setUnassigned] = useState<AdminStudent[]>([])
  const [showForm, setShowForm] = useState(false)
  const [form, setForm] = useState({ name: '', code: 'DD101', description: '' })
  const [msg, setMsg] = useState<string | null>(null)

  const reload = useCallback(() => {
    adminClasses().then(setItems).catch(() => setItems([]))
  }, [])

  useEffect(() => {
    reload()
    adminStudents(undefined, true)
      .then((r) => setUnassigned(r.data ?? []))
      .catch(() => setUnassigned([]))
  }, [reload])

  const openDetail = async (id: number) => {
    setSelectedId(id)
    try {
      const d = await adminClassDetail(id)
      setDetail(d)
    } catch {
      setDetail(null)
    }
  }

  const createClass = async (e: React.FormEvent) => {
    e.preventDefault()
    setMsg(null)
    try {
      await adminCreateClass(form)
      setShowForm(false)
      setForm({ name: '', code: 'DD101', description: '' })
      setMsg('Classe créée.')
      reload()
    } catch (err: unknown) {
      const m = (err as { response?: { data?: { message?: string } } })?.response?.data?.message
      setMsg(m ?? 'Erreur lors de la création.')
    }
  }

  const addStudent = async (userId: number) => {
    if (!selectedId) return
    try {
      await adminAddMember(selectedId, userId)
      await openDetail(selectedId)
      setUnassigned((prev) => prev.filter((s) => s.id !== userId))
      setMsg('Apprenant ajouté.')
    } catch {
      setMsg('Impossible d\'ajouter cet apprenant.')
    }
  }

  const removeStudent = async (userId: number) => {
    if (!selectedId) return
    try {
      await adminRemoveMember(selectedId, userId)
      await openDetail(selectedId)
      reload()
      setMsg('Apprenant retiré.')
    } catch {
      setMsg('Erreur lors du retrait.')
    }
  }

  return (
    <div className="space-y-6">
      <div className="flex flex-wrap items-end justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold tracking-tight text-slate-900">Classes</h1>
          <p className="mt-1 text-sm text-slate-600">Gérez les groupes DD101, DD201, DD102 et DD202.</p>
        </div>
        <button
          type="button"
          onClick={() => setShowForm(true)}
          className="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
        >
          <Plus className="h-4 w-4" />
          Nouvelle classe
        </button>
      </div>

      {msg && <p className="text-sm text-blue-700">{msg}</p>}

      {showForm && (
        <form onSubmit={createClass} className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
          <div className="mb-3 flex items-center justify-between">
            <h2 className="font-semibold">Créer une classe</h2>
            <button type="button" onClick={() => setShowForm(false)} aria-label="Fermer">
              <X className="h-5 w-5 text-slate-400" />
            </button>
          </div>
          <div className="grid gap-3 sm:grid-cols-2">
            <label className="block text-sm">
              Code
              <select
                className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2"
                value={form.code}
                onChange={(e) => setForm((f) => ({ ...f, code: e.target.value }))}
              >
                {CLASS_CODES.map((c) => (
                  <option key={c} value={c}>
                    {c}
                  </option>
                ))}
              </select>
            </label>
            <label className="block text-sm sm:col-span-2">
              Nom
              <input
                className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2"
                value={form.name}
                onChange={(e) => setForm((f) => ({ ...f, name: e.target.value }))}
                required
                placeholder="Développement Digital — Groupe 101"
              />
            </label>
            <label className="block text-sm sm:col-span-2">
              Description
              <textarea
                className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2"
                rows={2}
                value={form.description}
                onChange={(e) => setForm((f) => ({ ...f, description: e.target.value }))}
              />
            </label>
          </div>
          <button type="submit" className="mt-4 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white">
            Enregistrer
          </button>
        </form>
      )}

      <div className="grid gap-6 lg:grid-cols-2">
        <div className="space-y-3">
          {items.map((c) => (
            <button
              key={c.id}
              type="button"
              onClick={() => openDetail(c.id)}
              className={`w-full rounded-2xl border p-4 text-left transition ${
                selectedId === c.id ? 'border-blue-400 bg-blue-50/50' : 'border-slate-200 bg-white hover:border-slate-300'
              }`}
            >
              <div className="flex items-center justify-between">
                <span className="text-lg font-bold text-blue-600">{c.code}</span>
                <span className="text-xs text-slate-500">{c.members_count ?? 0} élèves</span>
              </div>
              <p className="mt-1 font-medium text-slate-900">{c.name}</p>
              <p className="mt-1 text-sm text-slate-500 line-clamp-2">{c.description ?? '—'}</p>
            </button>
          ))}
          {items.length === 0 && (
            <p className="text-sm text-slate-600">
              Aucune classe. Lancez <code className="text-xs">php artisan db:seed --class=ClassRoomSeeder</code>.
            </p>
          )}
        </div>

        <div className="rounded-2xl border border-slate-200 bg-white p-5 min-h-[280px]">
          {detail ? (
            <>
              <h2 className="text-lg font-bold text-slate-900">
                {detail.code} — {detail.name}
              </h2>
              <p className="mt-1 text-sm text-slate-600">{detail.description}</p>
              <h3 className="mt-6 text-sm font-semibold uppercase tracking-wide text-slate-500">Membres</h3>
              <ul className="mt-3 space-y-2">
                {(detail.members ?? []).map((m) => (
                  <li key={m.id} className="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2 text-sm">
                    <span>
                      {m.first_name} {m.last_name}
                      <span className="ml-2 text-xs text-slate-500">{m.email}</span>
                    </span>
                    <button
                      type="button"
                      onClick={() => removeStudent(m.id)}
                      className="text-red-600 hover:text-red-700"
                      title="Retirer"
                    >
                      <UserMinus className="h-4 w-4" />
                    </button>
                  </li>
                ))}
                {!detail.members?.length && (
                  <li className="text-sm text-slate-500">Aucun apprenant dans cette classe.</li>
                )}
              </ul>
              {unassigned.length > 0 && (
                <>
                  <h3 className="mt-6 text-sm font-semibold uppercase tracking-wide text-slate-500">
                    Ajouter un apprenant
                  </h3>
                  <ul className="mt-2 max-h-40 space-y-1 overflow-y-auto">
                    {unassigned.map((s) => (
                      <li key={s.id} className="flex items-center justify-between rounded-lg border border-dashed border-slate-200 px-3 py-2 text-sm">
                        <span>
                          {s.first_name} {s.last_name}
                        </span>
                        <button
                          type="button"
                          onClick={() => addStudent(s.id)}
                          className="text-blue-600 hover:text-blue-700"
                          title="Ajouter"
                        >
                          <UserPlus className="h-4 w-4" />
                        </button>
                      </li>
                    ))}
                  </ul>
                </>
              )}
            </>
          ) : (
            <p className="text-sm text-slate-500">Sélectionnez une classe pour gérer les apprenants.</p>
          )}
        </div>
      </div>
    </div>
  )
}
