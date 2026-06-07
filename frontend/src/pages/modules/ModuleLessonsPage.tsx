import { useEffect, useMemo, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import { enroll, listLessons } from '../../services/course.service'
import type { Lesson } from '../../types'

function normalizeModule(raw: string | undefined) {
  const m = (raw ?? '').toUpperCase()
  if (m === 'WORD' || m === 'EXCEL' || m === 'POWERPOINT') return m
  if (m === 'PPT') return 'POWERPOINT'
  return m
}

export default function ModuleLessonsPage() {
  const params = useParams()
  const module = useMemo(() => normalizeModule(params.module), [params.module])
  const [lessons, setLessons] = useState<Lesson[]>([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    setLoading(true)
    listLessons(module)
      .then(setLessons)
      .finally(() => setLoading(false))
  }, [module])

  return (
    <div>
      <div className="flex items-center justify-between gap-3">
        <div>
          <h1 className="text-2xl font-semibold">Leçons {module}</h1>
          <p className="text-sm text-slate-600 mt-1">Parcours de cours et objectifs MOS.</p>
        </div>
        <div className="flex items-center gap-2">
          <Link className="text-sm px-3 py-1.5 rounded-md border border-slate-200 hover:bg-slate-50" to={`/quizzes/${module}`}>
            Quiz
          </Link>
          <Link className="text-sm px-3 py-1.5 rounded-md border border-slate-200 hover:bg-slate-50" to={`/exam/${module}`}>
            Examen blanc
          </Link>
          <button
            className="text-sm px-3 py-1.5 rounded-md bg-slate-900 text-white"
            onClick={() => enroll(module)}
          >
            S’inscrire au module
          </button>
        </div>
      </div>

      {loading ? (
        <div className="mt-6 text-sm text-slate-600">Chargement…</div>
      ) : (
        <div className="mt-6 grid gap-3">
          {lessons.map((l) => (
            <Link
              key={l.id}
              className="rounded-xl bg-white shadow p-4 hover:shadow-md transition"
              to={`/modules/${module}/lessons/${l.slug}`}
            >
              <div className="flex items-center justify-between">
                <div className="font-semibold">
                  {l.order}. {l.title}
                </div>
                <div className="text-xs text-slate-500">{l.durationMin} min</div>
              </div>
              <div className="text-sm text-slate-600 mt-1">{l.description}</div>
            </Link>
          ))}
          {lessons.length === 0 && <div className="text-sm text-slate-600">Aucune leçon publiée.</div>}
        </div>
      )}
    </div>
  )
}

