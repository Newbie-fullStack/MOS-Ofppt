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

  const {
    register,
    handleSubmit,
    formState: { errors, isSubmitting },
  } = useForm<FormValues>({
    resolver: zodResolver(schema),
    defaultValues: { email: 'apprenant@mos-ofppt.ma', password: 'Test1234!' },
  })

  return (
    <div className="min-h-full flex items-center justify-center p-6">
      <div className="w-full max-w-md rounded-xl bg-white shadow p-6">
        <h1 className="text-xl font-semibold">Connexion</h1>
        <p className="text-sm text-slate-600 mt-1">Accède à la plateforme MOS OFPPT.</p>

        <form
          className="mt-6 space-y-3"
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
          <label className="block">
            <span className="text-sm font-medium">Email</span>
            <input
              className="mt-1 w-full rounded-md border border-slate-200 px-3 py-2"
              type="email"
              {...register('email')}
            />
            {errors.email && <div className="text-xs text-red-600 mt-1">{errors.email.message}</div>}
          </label>

          <label className="block">
            <span className="text-sm font-medium">Mot de passe</span>
            <input
              className="mt-1 w-full rounded-md border border-slate-200 px-3 py-2"
              type="password"
              {...register('password')}
            />
            {errors.password && <div className="text-xs text-red-600 mt-1">{errors.password.message}</div>}
          </label>

          {error && <div className="text-sm text-red-600">{error}</div>}

          <button disabled={isSubmitting} className="w-full rounded-md bg-slate-900 text-white py-2 font-medium disabled:opacity-60">
            Se connecter
          </button>
        </form>

        <div className="mt-4 flex items-center justify-between text-sm text-slate-600">
          <Link className="text-slate-900 underline" to="/forgot-password">
            Mot de passe oublié ?
          </Link>
          <Link className="text-slate-900 underline" to="/register">
            Créer un compte
          </Link>
        </div>
      </div>
    </div>
  )
}

