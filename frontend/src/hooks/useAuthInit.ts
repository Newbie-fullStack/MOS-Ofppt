import { useEffect, useState } from 'react'
import { me } from '../services/auth.service'
import { useAuthStore } from '../store/authStore'

export function useAuthInit() {
  const token = useAuthStore((s) => s.token)
  const user = useAuthStore((s) => s.user)
  const setUser = useAuthStore((s) => s.setUser)
  const logout = useAuthStore((s) => s.logout)
  const [ready, setReady] = useState(false)

  useEffect(() => {
    let cancelled = false
    async function run() {
      if (!token) {
        setReady(true)
        return
      }
      if (user) {
        setReady(true)
        return
      }
      try {
        const u = await me()
        if (!cancelled) setUser(u)
      } catch {
        if (!cancelled) logout()
      } finally {
        if (!cancelled) setReady(true)
      }
    }
    run()
    return () => {
      cancelled = true
    }
  }, [token, user, setUser, logout])

  return ready
}

