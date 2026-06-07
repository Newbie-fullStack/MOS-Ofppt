import { useEffect, useMemo, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import { getLesson } from '../../services/course.service'
import { updateProgress } from '../../services/progress.service'
import LessonContent from '../../components/course/LessonContent'
import type { Lesson } from '../../types'

function normalizeModule(raw: string | undefined) {
  const m = (raw ?? '').toUpperCase()
  if (m === 'WORD' || m === 'EXCEL' || m === 'POWERPOINT') return m
  if (m === 'PPT') return 'POWERPOINT'
  return m
}

export default function LessonDetailPage() {
  const params = useParams()
  const module = useMemo(() => normalizeModule(params.module), [params.module])
  const slug = params.slug ?? ''
  const [lesson, setLesson] = useState<Lesson | null>(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    setLoading(true)
    getLesson(module, slug)
      .then(setLesson)
      .finally(() => setLoading(false))
  }, [module, slug])

  if (loading) return <div className="text-sm text-slate-600">Chargement…</div>
  if (!lesson) return <div className="text-sm text-slate-600">Leçon introuvable.</div>

  return (
    <div>
      <div className="flex items-center justify-between">
        <div>
          <Link className="text-sm text-slate-600 hover:underline" to={`/modules/${module}/lessons`}>
            ← Retour
          </Link>
          <h1 className="text-2xl font-semibold mt-2">{lesson.title}</h1>
          <p className="text-sm text-slate-600 mt-1">{lesson.description}</p>
        </div>
        <button
          className="text-sm px-3 py-1.5 rounded-md bg-emerald-600 text-white"
          onClick={() => updateProgress(lesson.id, { completed: true, time_spent_sec: 60 })}
        >
          Marquer comme terminée
        </button>
      </div>

      <div className="mt-6 rounded-xl bg-white shadow p-5">
        <div className="text-sm font-medium">Objectifs</div>
        <ul className="mt-2 list-disc pl-5 text-sm text-slate-700">
          {(lesson.objectives ?? []).map((o, idx) => (
            <li key={idx}>{o}</li>
          ))}
        </ul>

        <div className="text-sm font-medium mt-6">Contenu</div>
        <div className="mt-3">
          <LessonContent contentJson={(lesson.content as any) ?? { blocks: [] }} moduleColor={module} />
        </div>
      </div>
    </div>
  )
}

