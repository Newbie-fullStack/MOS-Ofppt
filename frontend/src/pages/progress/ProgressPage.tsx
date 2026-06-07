import { useEffect, useState } from 'react'
import { getProgress } from '../../services/progress.service'

export default function ProgressPage() {
  const [items, setItems] = useState<any[]>([])

  useEffect(() => {
    getProgress().then(setItems).catch(() => setItems([]))
  }, [])

  return (
    <div>
      <h1 className="text-2xl font-semibold">Progression</h1>
      <div className="mt-6 rounded-xl bg-white shadow overflow-hidden">
        <div className="grid grid-cols-12 gap-2 px-4 py-3 text-xs font-medium text-slate-500 border-b">
          <div className="col-span-6">Leçon</div>
          <div className="col-span-2">Module</div>
          <div className="col-span-2">Statut</div>
          <div className="col-span-2">Temps</div>
        </div>
        {items.map((p) => (
          <div key={`${p.user_id}-${p.lesson_id}`} className="grid grid-cols-12 gap-2 px-4 py-3 text-sm border-b last:border-b-0">
            <div className="col-span-6">{p.lesson?.title ?? p.lesson_id}</div>
            <div className="col-span-2">{p.lesson?.app_module ?? '-'}</div>
            <div className="col-span-2">{p.completed ? 'Terminée' : 'En cours'}</div>
            <div className="col-span-2">{p.time_spent_sec ?? 0}s</div>
          </div>
        ))}
        {items.length === 0 && <div className="px-4 py-6 text-sm text-slate-600">Aucune progression.</div>}
      </div>
    </div>
  )
}

