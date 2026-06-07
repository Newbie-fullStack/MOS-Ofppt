import { useEffect, useState } from 'react'
import BadgeIcon from '../components/common/BadgeIcon'
import { getUserBadges } from '../services/user.service'

export default function BadgesPage() {
  const [badges, setBadges] = useState<any[]>([])

  useEffect(() => {
    getUserBadges().then(setBadges).catch(() => setBadges([]))
  }, [])

  return (
    <div>
      <h1 className="text-2xl font-bold tracking-tight text-slate-900">Badges</h1>
      <p className="mt-2 text-sm text-slate-600">
        Récompenses obtenues en progressant dans les cours et quiz MOS.
      </p>
      <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        {badges.map((b) => (
          <article
            key={b.id ?? b.name}
            className="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm transition hover:border-blue-100 hover:shadow-md"
          >
            <div className="flex items-start gap-4">
              <BadgeIcon badgeId={b.id} name={b.name} size="lg" />
              <div>
                <h2 className="font-semibold text-slate-900">{b.name}</h2>
                {b.description ? <p className="mt-1 text-sm text-slate-600">{b.description}</p> : null}
                {typeof b.xpReward === 'number' ? (
                  <p className="mt-3 text-xs font-medium text-blue-600">+{b.xpReward} XP</p>
                ) : null}
              </div>
            </div>
          </article>
        ))}
      </div>
      {badges.length === 0 ? (
        <div className="mt-12 rounded-3xl border border-dashed border-slate-200 bg-white p-10 text-center text-sm text-slate-500 shadow-sm">
          Aucun badge pour l’instant. Continuez les leçons et les quiz pour en débloquer.
        </div>
      ) : null}
    </div>
  )
}
