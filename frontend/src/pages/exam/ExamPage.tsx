import { useCallback, useEffect, useMemo, useState } from 'react'
import { Link, useNavigate, useParams } from 'react-router-dom'
import { CheckCircle2, ShieldAlert, XCircle } from 'lucide-react'
import ExamConditionsModal, { type ExamCondition } from '../../components/exam/ExamConditionsModal'
import { useExamIntegrity } from '../../hooks/useExamIntegrity'
import { beginExam, previewExam, submitExam } from '../../services/quiz.service'

function normalizeModule(raw: string | undefined) {
  const m = (raw ?? '').toUpperCase()
  if (m === 'WORD' || m === 'EXCEL' || m === 'POWERPOINT') return m
  if (m === 'PPT') return 'POWERPOINT'
  return m
}

type ExamPreview = {
  title: string
  durationMin: number
  totalQ: number
  passingScore: number
  conditions: ExamCondition[]
}

type ExamActive = {
  sessionId: string
  questions: any[]
  title: string
  durationMin: number
  totalQ: number
  passingScore: number
  startedAt: string
}

export default function ExamPage() {
  const navigate = useNavigate()
  const params = useParams()
  const module = useMemo(() => normalizeModule(params.module), [params.module])

  const [preview, setPreview] = useState<ExamPreview | null>(null)
  const [exam, setExam] = useState<ExamActive | null>(null)
  const [showConditions, setShowConditions] = useState(false)
  const [loadingPreview, setLoadingPreview] = useState(true)
  const [starting, setStarting] = useState(false)
  const [answers, setAnswers] = useState<Record<string, number>>({})
  const [submitting, setSubmitting] = useState(false)
  const [localStartMs, setLocalStartMs] = useState<number | null>(null)
  const [result, setResult] = useState<any | null>(null)
  const [loadError, setLoadError] = useState<string | null>(null)
  const [showConfirmSubmit, setShowConfirmSubmit] = useState(false)

  const examActive = !!exam && !result
  const { push, violationCount } = useExamIntegrity(exam?.sessionId ?? null, examActive)

  useEffect(() => {
    setLoadingPreview(true)
    setLoadError(null)
    previewExam(module)
      .then((data) => {
        if (!data?.totalQ) {
          setLoadError('Aucune question n’est associée à cet examen. Contactez votre formateur.')
          setPreview(null)
          return
        }
        setPreview({
          title: data.title,
          durationMin: data.durationMin,
          totalQ: data.totalQ,
          passingScore: data.passingScore,
          conditions: data.conditions ?? [],
        })
        setShowConditions(true)
      })
      .catch((err: unknown) => {
        setPreview(null)
        const msg = (err as { response?: { data?: { message?: string }; status?: number } })?.response
        if (msg?.status === 401) {
          setLoadError('Session expirée. Reconnectez-vous pour passer l’examen.')
        } else if (msg?.status === 404) {
          setLoadError(`Aucun examen blanc publié pour le module ${module}.`)
        } else {
          setLoadError(msg?.data?.message ?? 'Impossible de charger l’examen. Vérifiez que le serveur API est démarré.')
        }
      })
      .finally(() => setLoadingPreview(false))
  }, [module])

  const onAcceptConditions = useCallback(async () => {
    setStarting(true)
    try {
      const data = await beginExam(module)
      setExam({
        sessionId: data.sessionId,
        questions: data.questions,
        title: data.title,
        durationMin: data.durationMin,
        totalQ: data.totalQ,
        passingScore: data.passingScore,
        startedAt: data.startedAt,
      })
      setLocalStartMs(Date.now())
      setShowConditions(false)
    } catch {
      setShowConditions(true)
    } finally {
      setStarting(false)
    }
  }, [module])

  if (loadingPreview) {
    return <div className="text-sm text-slate-600">Chargement de l&apos;examen…</div>
  }

  if (!preview && !exam) {
    return (
      <div className="rounded-xl bg-white p-6 text-sm text-slate-600 shadow">
        <p className="font-medium text-slate-900">Examen indisponible</p>
        <p className="mt-2">{loadError ?? `Aucun examen blanc pour le module ${module}.`}</p>
        <Link to={`/modules/${module}/lessons`} className="mt-4 inline-block text-blue-600 underline">
          Retour aux leçons
        </Link>
      </div>
    )
  }

  const durationSec = exam && localStartMs ? Math.max(1, Math.round((Date.now() - localStartMs) / 1000)) : 0

  return (
    <div>
      <ExamConditionsModal
        open={showConditions && !!preview && !exam}
        title={preview?.title ?? 'Examen blanc MOS'}
        module={module}
        durationMin={preview?.durationMin ?? 0}
        totalQ={preview?.totalQ ?? 0}
        passingScore={preview?.passingScore ?? 70}
        conditions={preview?.conditions ?? []}
        loading={starting}
        onAccept={onAcceptConditions}
        onCancel={() => navigate(-1)}
      />

      {exam && !result && (
        <>
          <div className="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-blue-100 bg-blue-50/80 px-4 py-3">
            <div className="flex items-center gap-2 text-sm text-blue-900">
              <ShieldAlert className="h-5 w-5 shrink-0" aria-hidden />
              <span>Surveillance active — restez sur cet onglet.</span>
            </div>
            {violationCount > 0 && (
              <span className="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-900">
                {violationCount} mouvement(s) enregistré(s)
              </span>
            )}
          </div>

          <div className="flex items-center justify-between gap-3">
            <div>
              <h1 className="text-2xl font-semibold">Examen blanc {module}</h1>
              <p className="mt-1 text-sm text-slate-600">
                {exam.totalQ} questions · {exam.durationMin} min · seuil {exam.passingScore}%
              </p>
            </div>
            <button
              type="button"
              disabled={submitting}
              className="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60 hover:bg-slate-800"
              onClick={() => setShowConfirmSubmit(true)}
            >
              Soumettre
            </button>
          </div>

          <p className="mt-3 text-xs text-slate-500">Temps écoulé : {durationSec}s</p>
        </>
      )}

      {showConfirmSubmit && exam && !result && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
          <div className="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
            <h3 className="text-lg font-semibold text-slate-900">Confirmer la soumission</h3>
            <p className="mt-2 text-sm text-slate-600">
              Êtes-vous sûr de vouloir soumettre votre examen ? Cette action est irréversible.
            </p>
            <p className="mt-3 text-sm font-medium text-slate-700">
              Questions répondues : {Object.keys(answers).length} / {exam.totalQ}
            </p>
            <div className="mt-6 flex gap-3">
              <button
                type="button"
                onClick={() => setShowConfirmSubmit(false)}
                className="flex-1 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
              >
                Annuler
              </button>
              <button
                type="button"
                disabled={submitting}
                onClick={async () => {
                  setSubmitting(true)
                  push('exam_submit_clicked')
                  try {
                    const res = await submitExam(module, {
                      session_id: exam.sessionId,
                      answers,
                      duration_sec: durationSec,
                      started_at: exam.startedAt,
                    })
                    setResult(res)
                    setShowConfirmSubmit(false)
                  } finally {
                    setSubmitting(false)
                  }
                }}
                className="flex-1 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 disabled:opacity-60"
              >
                {submitting ? 'Soumission...' : 'Confirmer'}
              </button>
            </div>
          </div>
        </div>
      )}

      {result && (
        <div className="mt-6 rounded-2xl bg-white p-6 shadow-lg">
          <div className="flex items-center gap-3">
            {result.data.passed ? (
              <CheckCircle2 className="h-8 w-8 text-green-600" />
            ) : (
              <XCircle className="h-8 w-8 text-red-600" />
            )}
            <div>
              <p className="text-lg font-semibold text-slate-900">
                {result.data.passed ? 'Félicitations !' : 'Examen non réussi'}
              </p>
              <p className="text-sm text-slate-600">
                Score: <span className="font-semibold">{result.data.score}%</span> · Seuil: {exam?.passingScore ?? 70}%
              </p>
            </div>
          </div>

          <div className="mt-6 rounded-lg bg-slate-50 p-4">
            <div className="grid grid-cols-3 gap-4 text-center">
              <div>
                <p className="text-2xl font-bold text-slate-900">{result.data.score}%</p>
                <p className="text-xs text-slate-600">Score final</p>
              </div>
              <div>
                <p className="text-2xl font-bold text-slate-900">
                  {result.data.correctQuestions}/{result.data.totalQuestions}
                </p>
                <p className="text-xs text-slate-600">Correctes</p>
              </div>
              <div>
                <p className="text-2xl font-bold text-slate-900">{Math.floor(durationSec / 60)}min</p>
                <p className="text-xs text-slate-600">Durée</p>
              </div>
            </div>
          </div>

          <div className="mt-6">
            <p className="text-sm font-semibold text-slate-900">Correction détaillée</p>
            <div className="mt-3 max-h-96 space-y-3 overflow-y-auto">
              {Object.entries(result.data.details ?? {}).slice(0, 20).map(([qid, d]: [string, any]) => (
                <div key={qid} className="rounded-lg border border-slate-200 bg-white p-3">
                  <div className="flex items-start justify-between gap-2">
                    <p className="text-xs font-medium text-slate-500">Question {qid}</p>
                    {d.is_correct ? (
                      <span className="rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700">Correct</span>
                    ) : (
                      <span className="rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">Incorrect</span>
                    )}
                  </div>
                  <p className="mt-2 text-sm text-slate-700">
                    Votre réponse: <span className="font-medium">{d.selected ?? '—'}</span>
                  </p>
                  {!d.is_correct && (
                    <p className="mt-1 text-sm text-slate-700">
                      Réponse correcte: <span className="font-medium text-green-700">{d.correct_index}</span>
                    </p>
                  )}
                  {d.explanation && (
                    <p className="mt-2 rounded bg-blue-50 p-2 text-xs text-blue-900">{d.explanation}</p>
                  )}
                </div>
              ))}
            </div>
          </div>

          <div className="mt-6 flex gap-3">
            <Link
              to={`/modules/${module.toLowerCase()}/lessons`}
              className="flex-1 rounded-xl bg-blue-600 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-blue-700"
            >
              Retour aux cours
            </Link>
            <Link
              to="/dashboard"
              className="flex-1 rounded-xl border border-slate-200 px-4 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
              Tableau de bord
            </Link>
          </div>
        </div>
      )}

      {exam && !result && (
        <div className="mt-6 grid gap-4">
          {exam.questions.map((q: any, idx: number) => (
            <div key={q.id} className="rounded-xl bg-white p-4 shadow">
              <p className="text-sm text-slate-500">Question {idx + 1}</p>
              <p className="mt-1 font-medium">{q.questionText}</p>
              <div className="mt-3 grid gap-2">
                {q.options.map((opt: string, i: number) => (
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
      )}
    </div>
  )
}
