import { useMemo, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useForm } from 'react-hook-form'
import { z } from 'zod'
import { zodResolver } from '@hookform/resolvers/zod'
import { register as registerApi } from '../../services/auth.service'
import { useAuthStore } from '../../store/authStore'
import { CLASS_CODES } from '../../types'

const schema = z
  .object({
    first_name: z.string().min(1, 'Prénom requis').max(100),
    last_name: z.string().min(1, 'Nom requis').max(100),
    email: z.string().email('Email invalide'),
    password: z.string().min(8, 'Min 8 caractères'),
    password_confirmation: z.string().min(8, 'Min 8 caractères'),
    class_code: z.enum(CLASS_CODES, { message: 'Sélectionnez votre classe' }),
  })
  .refine((v) => v.password === v.password_confirmation, {
    message: 'Les mots de passe ne correspondent pas',
    path: ['password_confirmation'],
  })

type FormValues = z.infer<typeof schema>

export default function RegisterPage() {
  const navigate = useNavigate()
  const setAuth = useAuthStore((s) => s.setAuth)
  const [serverError, setServerError] = useState<string | null>(null)

  const defaultValues = useMemo<FormValues>(
    () => ({
      first_name: '',
      last_name: '',
      email: '',
      password: '',
      password_confirmation: '',
      class_code: 'DD101',
    }),
    [],
  )

  const {
    register,
    handleSubmit,
    formState: { errors, isSubmitting },
  } = useForm<FormValues>({
    resolver: zodResolver(schema),
    defaultValues,
  })

  return (
    <div className="min-h-full flex items-center justify-center p-6">
      <div className="w-full max-w-md rounded-xl bg-white shadow p-6">
        <h1 className="text-xl font-semibold">Créer un compte</h1>
        <p className="text-sm text-slate-600 mt-1">Inscription MOS OFPPT.</p>

        <form
          className="mt-6 space-y-3"
          onSubmit={handleSubmit(async (values) => {
            setServerError(null)
            try {
              const data = await registerApi(values)
              setAuth(data)
              navigate('/dashboard')
            } catch (err: any) {
              setServerError(err?.response?.data?.message ?? 'Erreur inscription.')
            }
          })}
        >
          <div className="grid grid-cols-2 gap-3">
            <label className="block">
              <span className="text-sm font-medium">Prénom</span>
              <input className="mt-1 w-full rounded-md border border-slate-200 px-3 py-2" {...register('first_name')} />
              {errors.first_name && <div className="text-xs text-red-600 mt-1">{errors.first_name.message}</div>}
            </label>
            <label className="block">
              <span className="text-sm font-medium">Nom</span>
              <input className="mt-1 w-full rounded-md border border-slate-200 px-3 py-2" {...register('last_name')} />
              {errors.last_name && <div className="text-xs text-red-600 mt-1">{errors.last_name.message}</div>}
            </label>
          </div>

          <label className="block">
            <span className="text-sm font-medium">Classe OFPPT</span>
            <select className="mt-1 w-full rounded-md border border-slate-200 px-3 py-2" {...register('class_code')}>
              {CLASS_CODES.map((code) => (
                <option key={code} value={code}>
                  {code}
                </option>
              ))}
            </select>
            {errors.class_code && <div className="mt-1 text-xs text-red-600">{errors.class_code.message}</div>}
          </label>

          <label className="block">
            <span className="text-sm font-medium">Email</span>
            <input className="mt-1 w-full rounded-md border border-slate-200 px-3 py-2" type="email" {...register('email')} />
            {errors.email && <div className="text-xs text-red-600 mt-1">{errors.email.message}</div>}
          </label>

          <label className="block">
            <span className="text-sm font-medium">Mot de passe</span>
            <input className="mt-1 w-full rounded-md border border-slate-200 px-3 py-2" type="password" {...register('password')} />
            {errors.password && <div className="text-xs text-red-600 mt-1">{errors.password.message}</div>}
          </label>

          <label className="block">
            <span className="text-sm font-medium">Confirmation</span>
            <input
              className="mt-1 w-full rounded-md border border-slate-200 px-3 py-2"
              type="password"
              {...register('password_confirmation')}
            />
            {errors.password_confirmation && (
              <div className="text-xs text-red-600 mt-1">{errors.password_confirmation.message}</div>
            )}
          </label>

          {serverError && <div className="text-sm text-red-600">{serverError}</div>}

          <button disabled={isSubmitting} className="w-full rounded-md bg-slate-900 text-white py-2 font-medium disabled:opacity-60">
            S’inscrire
          </button>
        </form>

        <div className="mt-4 text-sm text-slate-600">
          Déjà un compte ? <Link className="text-slate-900 underline" to="/login">Se connecter</Link>
        </div>
      </div>
    </div>
  )
}

