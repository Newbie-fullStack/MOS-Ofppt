import { useEffect, useMemo, useState } from 'react'
import { Link, useNavigate, useParams } from 'react-router-dom'
import { getQuiz, submitQuizAttempt } from '../../services/quiz.service'
import type { Quiz } from '../../types'

export default function QuizTakePage() {
  const params = useParams()
  const id = params.id ?? ''
  const navigate = useNavigate()
  const [quiz, setQuiz] = useState<Quiz | null>(null)
  const [answers, setAnswers] = useState<Record<string, number>>({})
  const [startedAt] = useState(() => Date.now())
  const [submitting, setSubmitting] = useState(false)
  const [result, setResult] = useState<any | null>(null)
  const durationSec = useMemo(() => Math.max(1, Math.round((Date.now() - startedAt) / 1000)), [startedAt])

  useEffect(() => {
    getQuiz(id).then(setQuiz).catch(() => setQuiz(null))
  }, [id])

  if (!quiz) return <div className="text-sm text-slate-600">Chargement…</div>

  return (
    <div>
      <div className="flex items-center justify-between gap-3">
        <div>
          <Link className="text-sm text-slate-600 hover:underline" to={`/quizzes/${quiz.appModule}`}>
            ← Retour
          </Link>
          <h1 className="text-2xl font-semibold mt-2">{quiz.title}</h1>
          <div className="text-sm text-slate-600 mt-1">{quiz.description}</div>
        </div>
        <button
          disabled={submitting}
          className="text-sm px-3 py-1.5 rounded-md bg-slate-900 text-white disabled:opacity-60"
          onClick={async () => {
            setSubmitting(true)
            try {
              const res = await submitQuizAttempt(quiz.id, { answers, duration_sec: durationSec })
              setResult(res)
            } finally {
              setSubmitting(false)
            }
          }}
        >
          Soumettre
        </button>
      </div>

      <div className="mt-3 text-xs text-slate-500">Temps: {durationSec}s</div>

      {result && (
        <div className="mt-6 rounded-xl bg-white shadow p-4">
          <div className="font-semibold">Résultat</div>
          <div className="text-sm text-slate-700 mt-2">
            Score: <span className="font-medium">{result.data.score}%</span> • Réussite:{' '}
            <span className="font-medium">{result.data.passed ? 'Oui' : 'Non'}</span>
          </div>
          <div className="mt-4 flex gap-2">
            <button className="text-sm px-3 py-1.5 rounded-md bg-slate-900 text-white" onClick={() => navigate('/dashboard')}>
              Retour dashboard
            </button>
            <button className="text-sm px-3 py-1.5 rounded-md border border-slate-200" onClick={() => setResult(null)}>
              Revoir
            </button>
          </div>
        </div>
      )}

      <div className="mt-6 grid gap-4">
        {(quiz.questions ?? []).map((q, idx) => (
          <div key={q.id} className="rounded-xl bg-white shadow p-4">
            <div className="text-sm text-slate-500">Question {idx + 1}</div>
            <div className="font-medium mt-1">{q.questionText}</div>
            <div className="mt-3 grid gap-2">
              {q.options.map((opt, i) => (
                <label key={i} className="flex items-center gap-2 text-sm">
                  <input
                    type="radio"
                    name={q.id}
                    checked={answers[q.id] === i}
                    onChange={() => setAnswers((a) => ({ ...a, [q.id]: i }))}
                  />
                  <span>{opt}</span>
                </label>
              ))}
            </div>
          </div>
        ))}
      </div>
    </div>
  )
}

