import { AlertTriangle, Clock, Eye, Shield, Target } from 'lucide-react'

export type ExamCondition = {
  title: string
  description: string
}

type ExamConditionsModalProps = {
  open: boolean
  title: string
  module: string
  durationMin: number
  totalQ: number
  passingScore: number
  conditions: ExamCondition[]
  loading?: boolean
  onAccept: () => void
  onCancel: () => void
}

const icons = [Clock, Eye, Shield, AlertTriangle, Target]

export default function ExamConditionsModal({
  open,
  title,
  module,
  durationMin,
  totalQ,
  passingScore,
  conditions,
  loading,
  onAccept,
  onCancel,
}: ExamConditionsModalProps) {
  if (!open) return null

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
      <button
        type="button"
        className="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"
        aria-label="Fermer"
        onClick={onCancel}
      />
      <div
        role="dialog"
        aria-modal="true"
        aria-labelledby="exam-conditions-title"
        className="relative z-10 w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl ring-1 ring-slate-200"
      >
        <div className="flex items-start gap-3 rounded-2xl bg-amber-50 px-4 py-3 ring-1 ring-amber-200">
          <AlertTriangle className="mt-0.5 h-6 w-6 shrink-0 text-amber-600" aria-hidden />
          <div>
            <h2 id="exam-conditions-title" className="text-lg font-bold text-slate-900">
              Conditions de l&apos;examen
            </h2>
            <p className="mt-1 text-sm text-slate-600">
              {title} — {module} · {totalQ} questions · {durationMin} min · seuil {passingScore}%
            </p>
          </div>
        </div>

        <ul className="mt-5 max-h-[50vh] space-y-3 overflow-y-auto">
          {conditions.map((c, i) => {
            const Icon = icons[i % icons.length]
            return (
              <li key={c.title} className="flex gap-3 rounded-xl border border-slate-100 bg-slate-50/80 px-3 py-3">
                <Icon className="mt-0.5 h-5 w-5 shrink-0 text-blue-600" aria-hidden />
                <div>
                  <p className="text-sm font-semibold text-slate-900">{c.title}</p>
                  <p className="mt-0.5 text-xs leading-relaxed text-slate-600">{c.description}</p>
                </div>
              </li>
            )
          })}
        </ul>

        <p className="mt-4 text-xs text-slate-500">
          En cliquant sur « Je commence l&apos;examen », vous acceptez ces règles. Toute sortie d&apos;onglet ou
          changement de fenêtre sera enregistrée.
        </p>

        <div className="mt-5 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
          <button
            type="button"
            onClick={onCancel}
            disabled={loading}
            className="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-50"
          >
            Annuler
          </button>
          <button
            type="button"
            onClick={onAccept}
            disabled={loading}
            className="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50"
          >
            {loading ? 'Démarrage…' : "Je commence l'examen"}
          </button>
        </div>
      </div>
    </div>
  )
}
