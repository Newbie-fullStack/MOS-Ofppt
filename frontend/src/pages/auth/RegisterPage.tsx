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
    <div className="min-h-screen flex items-center justify-center bg-slate-950 p-6 relative overflow-hidden">
      {/* Abstract Background Decoration */}
      <div className="absolute top-0 left-0 w-full h-full bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-red-900/20 via-slate-950 to-slate-950" />

      {/* Glassmorphism Panel */}
      <div className="w-full max-w-md bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-3xl shadow-2xl z-10">
        <h1 className="text-3xl font-bold text-white mb-2">Create Account</h1>
        <p className="text-slate-400 mb-8">Join the MOS OFPPT platform.</p>

        <form
          className="space-y-4"
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
          <div className="grid grid-cols-2 gap-4">
            <input className="w-full bg-white/5 border-b border-white/20 px-0 py-3 text-white placeholder:text-slate-500 focus:outline-none focus:border-white transition" placeholder="First Name" {...register('first_name')} />
            <input className="w-full bg-white/5 border-b border-white/20 px-0 py-3 text-white placeholder:text-slate-500 focus:outline-none focus:border-white transition" placeholder="Last Name" {...register('last_name')} />
          </div>
          {(errors.first_name || errors.last_name) && <div className="text-xs text-red-400">Prénom et Nom requis</div>}

          <select className="w-full bg-white/5 border-b border-white/20 px-0 py-3 text-slate-400 placeholder:text-slate-500 focus:outline-none focus:border-white transition" {...register('class_code')}>
            {CLASS_CODES.map((code) => <option key={code} value={code} className="bg-slate-900">{code}</option>)}
          </select>

          <input className="w-full bg-white/5 border-b border-white/20 px-0 py-3 text-white placeholder:text-slate-500 focus:outline-none focus:border-white transition" type="email" placeholder="Email" {...register('email')} />
          {errors.email && <div className="text-xs text-red-400">{errors.email.message}</div>}

          <input className="w-full bg-white/5 border-b border-white/20 px-0 py-3 text-white placeholder:text-slate-500 focus:outline-none focus:border-white transition" type="password" placeholder="Password" {...register('password')} />
          <input className="w-full bg-white/5 border-b border-white/20 px-0 py-3 text-white placeholder:text-slate-500 focus:outline-none focus:border-white transition" type="password" placeholder="Confirm Password" {...register('password_confirmation')} />
          {(errors.password || errors.password_confirmation) && <div className="text-xs text-red-400">Mots de passe invalides</div>}

          {serverError && <div className="text-sm text-red-400">{serverError}</div>}

          <button disabled={isSubmitting} className="w-full bg-white text-slate-950 py-3 rounded-lg font-semibold hover:bg-slate-200 transition">
            Sign Up
          </button>
        </form>

        <div className="mt-8 text-center text-sm text-slate-400">
          Already have an account? <Link className="text-white font-semibold underline" to="/login">Sign In</Link>
        </div>
      </div>
    </div>
  )
}

