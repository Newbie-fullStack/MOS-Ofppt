import { useEffect, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { Users } from 'lucide-react'
import { joinClass, listAvailableClasses } from '../services/class.service'
import { me } from '../services/auth.service'
import { useAuthStore } from '../store/authStore'
import type { ClassRoom } from '../types'
import { CLASS_CODES } from '../types'

export default function SelectClassPage() {
  const navigate = useNavigate()
  const setUser = useAuthStore((s) => s.setUser)
  const user = useAuthStore((s) => s.user)

  const [classes, setClasses] = useState<ClassRoom[]>([])
  const [selected, setSelected] = useState('')
  const [error, setError] = useState<string | null>(null)
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    listAvailableClasses()
      .then(setClasses)
      .catch(() => {
        setClasses(
          CLASS_CODES.map((code) => ({
            id: 0,
            code,
            name: `Classe ${code}`,
          })),
        )
      })
  }, [])

  const submit = async () => {
    if (!selected) {
      setError('Veuillez choisir une classe.')
      return
    }
    setError(null)
    setLoading(true)
    try {
      await joinClass(selected)
      const u = await me()
      setUser(u)
      navigate('/dashboard', { replace: true })
    } catch (err: unknown) {
      const msg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message
      setError(msg ?? 'Impossible de rejoindre cette classe.')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="mx-auto flex min-h-[60vh] max-w-lg flex-col justify-center px-4 py-10">
      <div className="rounded-3xl bg-white p-8 shadow-lg ring-1 ring-slate-200/80">
        <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-600">
          <Users className="h-6 w-6" aria-hidden />
        </div>
        <h1 className="mt-4 text-2xl font-bold text-slate-900">Choisissez votre classe</h1>
        <p className="mt-2 text-sm text-slate-600">
          Bonjour {user?.firstName ?? 'apprenant'}, sélectionnez le groupe OFPPT auquel vous appartenez pour accéder à la
          plateforme MOS.
        </p>

        <div className="mt-6 grid grid-cols-2 gap-3">
          {(classes.length ? classes : CLASS_CODES.map((c) => ({ id: 0, code: c, name: c }))).map((c) => {
            const active = selected === c.code
            return (
              <button
                key={c.code}
                type="button"
                onClick={() => setSelected(c.code)}
                className={`rounded-2xl border-2 px-4 py-4 text-left transition ${
                  active
                    ? 'border-blue-600 bg-blue-50 shadow-sm'
                    : 'border-slate-200 bg-white hover:border-slate-300'
                }`}
              >
                <span className="text-lg font-bold text-slate-900">{c.code}</span>
                <span className="mt-1 block line-clamp-2 text-xs text-slate-500">{c.name}</span>
              </button>
            )
          })}
        </div>

        {error && <p className="mt-4 text-sm text-red-600">{error}</p>}

        <button
          type="button"
          disabled={loading || !selected}
          onClick={submit}
          className="mt-6 w-full rounded-2xl bg-blue-600 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-50"
        >
          {loading ? 'Inscription en cours…' : 'Confirmer ma classe'}
        </button>
      </div>
    </div>
  )
}
