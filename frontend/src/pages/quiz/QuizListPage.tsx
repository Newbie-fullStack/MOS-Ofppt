import { useEffect, useMemo, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import { listQuizzes } from '../../services/quiz.service'
import type { Quiz } from '../../types'

function normalizeModule(raw: string | undefined) {
  const m = (raw ?? '').toUpperCase()
  if (m === 'WORD' || m === 'EXCEL' || m === 'POWERPOINT') return m
  if (m === 'PPT') return 'POWERPOINT'
  return m
}

export default function QuizListPage() {
  const params = useParams()
  const module = useMemo(() => normalizeModule(params.module), [params.module])
  const [items, setItems] = useState<Quiz[]>([])

  useEffect(() => {
    listQuizzes(module).then(setItems).catch(() => setItems([]))
  }, [module])

  return (
    <div>
      <h1 className="text-2xl font-semibold">Quiz {module}</h1>
      <div className="mt-6 grid gap-3">
        {items.map((q) => (
          <div key={q.id} className="rounded-xl bg-white shadow p-4 flex items-center justify-between">
            <div>
              <div className="font-semibold">{q.title}</div>
              <div className="text-sm text-slate-600 mt-1">{q.description}</div>
              <div className="text-xs text-slate-500 mt-2">
                {q.questionsCount ?? '—'} questions • {q.durationMin} min • seuil {q.passingScore}%
              </div>
            </div>
            <Link className="text-sm px-3 py-1.5 rounded-md bg-slate-900 text-white" to={`/quizzes/take/${q.id}`}>
              Commencer
            </Link>
          </div>
        ))}
        {items.length === 0 && <div className="text-sm text-slate-600">Aucun quiz publié.</div>}
      </div>
    </div>
  )
}

