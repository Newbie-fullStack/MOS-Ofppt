import { create } from 'zustand'
import type { User } from '../types'

type AuthState = {
  token: string | null
  user: User | null
  setAuth: (payload: { token: string; user: User }) => void
  setUser: (user: User | null) => void
  logout: () => void
}

const TOKEN_KEY = 'mos_token'

export const useAuthStore = create<AuthState>((set) => ({
  token: localStorage.getItem(TOKEN_KEY),
  user: null,
  setAuth: ({ token, user }) => {
    localStorage.setItem(TOKEN_KEY, token)
    set({ token, user })
  },
  setUser: (user) => set({ user }),
  logout: () => {
    localStorage.removeItem(TOKEN_KEY)
    set({ token: null, user: null })
  },
}))

