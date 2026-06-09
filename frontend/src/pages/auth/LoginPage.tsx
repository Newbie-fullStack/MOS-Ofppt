import { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useForm } from 'react-hook-form'
import { z } from 'zod'
import { zodResolver } from '@hookform/resolvers/zod'
import { useAuthStore } from '../../store/authStore'
import { login } from '../../services/auth.service'

const schema = z.object({
  email: z.string().email('Email invalide'),
  password: z.string().min(1, 'Mot de passe requis'),
})

type FormValues = z.infer<typeof schema>

export default function LoginPage() {
  const navigate = useNavigate()
  const setAuth = useAuthStore((s) => s.setAuth)
  const [error, setError] = useState<string | null>(null)

  const { register, handleSubmit, formState: { errors, isSubmitting } } = useForm<FormValues>({
    resolver: zodResolver(schema),
    defaultValues: { email: 'apprenant@mos-ofppt.ma', password: 'Test1234!' },
  })

  return (
    <div className="min-h-screen flex items-center justify-center bg-slate-950 p-6 relative overflow-hidden">
      {/* Abstract Background Decoration */}
      <div className="absolute top-0 left-0 w-full h-full bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-red-900/20 via-slate-950 to-slate-950" />

      {/* Glassmorphism Panel */}
      <div className="w-full max-w-md bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-3xl shadow-2xl z-10">
        <div className="flex justify-between items-center mb-8">
          <Link to="/" className="text-slate-400 hover:text-white transition text-sm flex items-center">
            ← Home
          </Link>
          <div className="text-right">
            <h1 className="text-3xl font-bold text-white mb-2">Hello!</h1>
            <p className="text-slate-400">Welcome Back</p>
          </div>
        </div>

        <form
          className="space-y-6"
          onSubmit={handleSubmit(async (values) => {
            setError(null)
            try {
              const data = await login(values)
              setAuth(data)
              navigate('/dashboard')
            } catch (err: any) {
              setError(err?.response?.data?.message ?? 'Erreur de connexion.')
            }
          })}
        >
          <input
            className="w-full bg-white/5 border-b border-white/20 px-0 py-3 text-white placeholder:text-slate-500 focus:outline-none focus:border-white transition"
            placeholder="Enter Email"
            {...register('email')}
          />
          {errors.email && <div className="text-xs text-red-400">{errors.email.message}</div>}

          <input
            className="w-full bg-white/5 border-b border-white/20 px-0 py-3 text-white placeholder:text-slate-500 focus:outline-none focus:border-white transition"
            type="password"
            placeholder="Password"
            {...register('password')}
          />
          {errors.password && <div className="text-xs text-red-400">{errors.password.message}</div>}

          <div className="text-right text-sm">
            <Link className="text-slate-400 hover:text-white" to="/forgot-password">Forgot Password?</Link>
          </div>

          {error && <div className="text-sm text-red-400">{error}</div>}

          <button disabled={isSubmitting} className="w-full bg-white text-slate-950 py-3 rounded-lg font-semibold hover:bg-slate-200 transition">
            Sign In
          </button>
        </form>

        <div className="mt-8 text-center text-sm text-slate-400">
          Don't have an account? <Link className="text-white font-semibold underline" to="/register">Create Account!</Link>
        </div>
      </div>
    </div>
  )
}

