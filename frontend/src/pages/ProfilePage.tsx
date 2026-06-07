import { useEffect, useState } from 'react'
import { getUserBadges, getUserStats } from '../services/user.service'
import { useAuthStore } from '../store/authStore'

export default function ProfilePage() {
  const user = useAuthStore((s) => s.user)
  const [stats, setStats] = useState<any | null>(null)
  const [badges, setBadges] = useState<any[]>([])

  useEffect(() => {
    getUserStats().then(setStats).catch(() => setStats(null))
    getUserBadges().then(setBadges).catch(() => setBadges([]))
  }, [])

  return (
    <div>
      <h1 className="text-2xl font-semibold">Profil</h1>

      <div className="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div className="rounded-xl bg-white shadow p-4">
          <div className="text-sm text-slate-500">Utilisateur</div>
          <div className="font-semibold mt-1">{user ? `${user.firstName} ${user.lastName}` : '—'}</div>
          <div className="text-sm text-slate-600 mt-1">{user?.email}</div>
        </div>

        <div className="rounded-xl bg-white shadow p-4">
          <div className="text-sm text-slate-500">Statistiques</div>
          <div className="mt-2 text-sm">
            XP: <span className="font-medium">{stats?.xpPoints ?? '—'}</span>
          </div>
          <div className="mt-1 text-sm">
            Streak: <span className="font-medium">{stats?.streakDays ?? '—'}</span>
          </div>
          <div className="mt-1 text-sm">
            Badges: <span className="font-medium">{stats?.badgesCount ?? '—'}</span>
          </div>
        </div>

        <div className="rounded-xl bg-white shadow p-4">
          <div className="text-sm text-slate-500">Badges</div>
          <div className="mt-2 flex flex-wrap gap-2">
            {badges.map((b) => (
              <div key={b.id} className="text-xs rounded-full bg-slate-100 px-3 py-1">
                {b.name ?? b.id}
              </div>
            ))}
            {badges.length === 0 && <div className="text-sm text-slate-600">Aucun badge.</div>}
          </div>
        </div>
      </div>
    </div>
  )
}

