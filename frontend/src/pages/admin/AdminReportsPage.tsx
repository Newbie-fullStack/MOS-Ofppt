import { useEffect, useState } from 'react'
import { adminClasses, adminReports, type AdminReportsData, type ExamSubmission } from '../../services/admin.service'
import type { ClassRoom } from '../../types'
import { AlertTriangle, CheckCircle2, ChevronDown, ChevronUp, XCircle } from 'lucide-react'

export default function AdminReportsPage() {
  const [data, setData] = useState<AdminReportsData | null>(null)
  const [classes, setClasses] = useState<ClassRoom[]>([])
  const [classFilter, setClassFilter] = useState<string>('')
  const [expandedExam, setExpandedExam] = useState<number | null>(null)

  useEffect(() => {
    adminClasses().then(setClasses).catch(() => setClasses([]))
  }, [])

  useEffect(() => {
    const id = classFilter ? Number(classFilter) : undefined
    adminReports(id).then(setData).catch(() => setData(null))
  }, [classFilter])

  return (
    <div className="space-y-8">
      <div className="flex flex-wrap items-end justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold tracking-tight text-slate-900">Rapports</h1>
          <p className="mt-1 text-sm text-slate-600">Performances quiz et examens blancs par classe.</p>
        </div>
        <label className="block">
          <span className="text-xs font-medium text-slate-500">Filtrer par classe</span>
          <select
            className="mt-1 rounded-xl border border-slate-200 px-3 py-2 text-sm"
            value={classFilter}
            onChange={(e) => setClassFilter(e.target.value)}
          >
            <option value="">Toutes les classes</option>
            {classes.map((c) => (
              <option key={c.id} value={c.id}>
                {c.code} — {c.name}
              </option>
            ))}
          </select>
        </label>
      </div>

      {data && data.studentsWithoutClass > 0 && (
        <div className="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
          {data.studentsWithoutClass} apprenant(s) sans classe assignée (DD101, DD201, DD102 ou DD202).
        </div>
      )}

      <section className="rounded-2xl bg-white shadow-md ring-1 ring-slate-200/70">
        <div className="border-b border-slate-100 px-5 py-4">
          <h2 className="font-semibold text-slate-900">Top apprenants — Quiz</h2>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full min-w-[520px] text-sm">
            <thead>
              <tr className="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-500">
                <th className="px-5 py-3">Apprenant</th>
                <th className="px-5 py-3">Classe</th>
                <th className="px-5 py-3">Moyenne</th>
                <th className="px-5 py-3">Meilleur</th>
                <th className="px-5 py-3">Tentatives</th>
              </tr>
            </thead>
            <tbody>
              {(data?.topQuizUsers ?? []).map((row) => (
                <tr key={row.userId} className="border-b border-slate-50 last:border-0">
                  <td className="px-5 py-3">
                    <p className="font-medium text-slate-900">{row.fullName}</p>
                    <p className="text-xs text-slate-500">{row.email}</p>
                  </td>
                  <td className="px-5 py-3">{row.classCode ?? '—'}</td>
                  <td className="px-5 py-3 font-semibold">{row.avgScore}%</td>
                  <td className="px-5 py-3">{row.bestScore}%</td>
                  <td className="px-5 py-3">{row.attempts}</td>
                </tr>
              ))}
              {!data?.topQuizUsers?.length && (
                <tr>
                  <td colSpan={5} className="px-5 py-8 text-center text-slate-500">
                    Aucune tentative de quiz enregistrée.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </section>

      <div className="grid gap-6 lg:grid-cols-2">
        <section className="rounded-2xl bg-white shadow-md ring-1 ring-slate-200/70">
          <div className="border-b border-slate-100 px-5 py-4">
            <h2 className="font-semibold text-slate-900">Examens blancs par module</h2>
          </div>
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead>
                <tr className="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-500">
                  <th className="px-5 py-3">Module</th>
                  <th className="px-5 py-3">Tentatives</th>
                  <th className="px-5 py-3">Moyenne</th>
                  <th className="px-5 py-3">Réussite</th>
                </tr>
              </thead>
              <tbody>
                {(data?.moduleExamStats ?? []).map((row) => (
                  <tr key={row.module} className="border-b border-slate-50 last:border-0">
                    <td className="px-5 py-3 font-medium">{row.moduleLabel}</td>
                    <td className="px-5 py-3">{row.attempts}</td>
                    <td className="px-5 py-3">{row.avgScore}%</td>
                    <td className="px-5 py-3">
                      {row.passedCount}/{row.attempts} ({row.passRate}%)
                    </td>
                  </tr>
                ))}
                {!data?.moduleExamStats?.length && (
                  <tr>
                    <td colSpan={4} className="px-5 py-8 text-center text-slate-500">
                      Aucun examen blanc soumis.
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        </section>

        <section className="rounded-2xl bg-white shadow-md ring-1 ring-slate-200/70">
          <div className="border-b border-slate-100 px-5 py-4">
            <h2 className="font-semibold text-slate-900">Synthèse par classe</h2>
          </div>
          <div className="divide-y divide-slate-100">
            {(data?.classOverview ?? []).map((row) => (
              <div key={row.id} className="px-5 py-4">
                <div className="flex items-start justify-between gap-2">
                  <div>
                    <p className="font-bold text-blue-600">{row.code}</p>
                    <p className="text-sm text-slate-700">{row.name}</p>
                    <p className="mt-1 text-xs text-slate-500">Formateur : {row.trainerName}</p>
                  </div>
                  <span className="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                    {row.membersCount} élèves
                  </span>
                </div>
                <div className="mt-3 flex flex-wrap gap-4 text-xs text-slate-600">
                  <span>Quiz : {row.quizAttempts}</span>
                  <span>Examens : {row.examAttempts}</span>
                  <span>Moy. quiz : {row.avgQuizScore != null ? `${row.avgQuizScore}%` : '—'}</span>
                </div>
              </div>
            ))}
            {!data?.classOverview?.length && (
              <p className="px-5 py-8 text-center text-sm text-slate-500">Aucune classe. Exécutez le seeder.</p>
            )}
          </div>
        </section>
      </div>

      <section className="rounded-2xl bg-white shadow-md ring-1 ring-slate-200/70">
        <div className="border-b border-slate-100 px-5 py-4">
          <h2 className="font-semibold text-slate-900">Examens blancs soumis (20 derniers)</h2>
          <p className="mt-1 text-xs text-slate-500">Détails des examens avec logs de surveillance</p>
        </div>
        <div className="divide-y divide-slate-100">
          {(data?.recentExamSubmissions ?? []).map((exam) => {
            const isExpanded = expandedExam === exam.id
            const logTypes = exam.integrityLogs.reduce((acc, log) => {
              acc[log.type] = (acc[log.type] || 0) + 1
              return acc
            }, {} as Record<string, number>)

            return (
              <div key={exam.id} className="px-5 py-4">
                <div className="flex items-start justify-between gap-4">
                  <div className="flex-1">
                    <div className="flex items-center gap-3">
                      <p className="font-semibold text-slate-900">{exam.fullName}</p>
                      <span className="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">
                        {exam.classCode ?? 'Sans classe'}
                      </span>
                      {exam.passed ? (
                        <CheckCircle2 className="h-4 w-4 text-green-600" />
                      ) : (
                        <XCircle className="h-4 w-4 text-red-600" />
                      )}
                    </div>
                    <p className="mt-1 text-sm text-slate-600">
                      {exam.moduleLabel} · Score: <span className="font-semibold">{exam.score}%</span> · {exam.correctQuestions}/{exam.totalQuestions} correctes · {Math.floor(exam.durationSec / 60)}min {exam.durationSec % 60}s
                    </p>
                    <p className="mt-1 text-xs text-slate-500">
                      Soumis le {new Date(exam.completedAt).toLocaleString('fr-FR')}
                    </p>
                  </div>
                  <div className="flex items-center gap-3">
                    {exam.violationCount > 0 && (
                      <div className="flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-900">
                        <AlertTriangle className="h-3.5 w-3.5" />
                        {exam.violationCount} alerte{exam.violationCount > 1 ? 's' : ''}
                      </div>
                    )}
                    <button
                      type="button"
                      onClick={() => setExpandedExam(isExpanded ? null : exam.id)}
                      className="rounded-lg border border-slate-200 p-2 hover:bg-slate-50"
                    >
                      {isExpanded ? <ChevronUp className="h-4 w-4" /> : <ChevronDown className="h-4 w-4" />}
                    </button>
                  </div>
                </div>

                {isExpanded && (
                  <div className="mt-4 rounded-lg bg-slate-50 p-4">
                    <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">Logs de surveillance</p>
                    <div className="mt-3 grid gap-2">
                      {Object.entries(logTypes).map(([type, count]) => {
                        const labels: Record<string, { label: string; color: string }> = {
                          conditions_accepted: { label: 'Conditions acceptées', color: 'text-green-700' },
                          exam_started: { label: 'Examen démarré', color: 'text-blue-700' },
                          tab_switch: { label: 'Changement d\'onglet', color: 'text-amber-700' },
                          window_blur: { label: 'Perte de focus', color: 'text-amber-700' },
                          copy_paste: { label: 'Copier-coller', color: 'text-red-700' },
                          context_menu: { label: 'Menu contextuel', color: 'text-red-700' },
                          exam_submitted: { label: 'Examen soumis', color: 'text-green-700' },
                        }
                        const info = labels[type] || { label: type, color: 'text-slate-700' }
                        return (
                          <div key={type} className="flex items-center justify-between text-sm">
                            <span className={info.color}>{info.label}</span>
                            <span className="font-medium text-slate-900">{count}×</span>
                          </div>
                        )
                      })}
                    </div>
                    {exam.integrityLogs.length > 0 && (
                      <details className="mt-4">
                        <summary className="cursor-pointer text-xs font-medium text-slate-600 hover:text-slate-900">
                          Voir tous les logs ({exam.integrityLogs.length})
                        </summary>
                        <div className="mt-2 max-h-64 space-y-1 overflow-y-auto rounded border border-slate-200 bg-white p-2 text-xs font-mono">
                          {exam.integrityLogs.map((log, idx) => (
                            <div key={idx} className="text-slate-700">
                              <span className="text-slate-400">[{new Date(log.at).toLocaleTimeString('fr-FR')}]</span>{' '}
                              <span className="font-semibold">{log.type}</span>
                              {log.meta && Object.keys(log.meta).length > 0 && (
                                <span className="text-slate-500"> {JSON.stringify(log.meta)}</span>
                              )}
                            </div>
                          ))}
                        </div>
                      </details>
                    )}
                  </div>
                )}
              </div>
            )
          })}
          {!data?.recentExamSubmissions?.length && (
            <p className="px-5 py-8 text-center text-sm text-slate-500">Aucun examen blanc soumis.</p>
          )}
        </div>
      </section>
    </div>
  )
}
