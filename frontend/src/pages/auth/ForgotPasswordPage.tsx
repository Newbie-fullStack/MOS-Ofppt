import { useState } from 'react'
import { Link } from 'react-router-dom'
import { useForm } from 'react-hook-form'
import { z } from 'zod'
import { zodResolver } from '@hookform/resolvers/zod'
import { forgotPassword } from '../../services/auth.service'

const schema = z.object({
  email: z.string().email('Email invalide'),
})

type FormValues = z.infer<typeof schema>

export default function ForgotPasswordPage() {
  const [message, setMessage] = useState<string | null>(null)
  const [serverError, setServerError] = useState<string | null>(null)

  const {
    register,
    handleSubmit,
    formState: { errors, isSubmitting },
  } = useForm<FormValues>({
    resolver: zodResolver(schema),
    defaultValues: { email: '' },
  })

  return (
    <div className="min-h-full flex items-center justify-center p-6">
      <div className="w-full max-w-md rounded-xl bg-white shadow p-6">
        <h1 className="text-xl font-semibold">Mot de passe oublié</h1>
        <p className="text-sm text-slate-600 mt-1">On t’envoie un lien de réinitialisation.</p>

        <form
          className="mt-6 space-y-3"
          onSubmit={handleSubmit(async ({ email }) => {
            setServerError(null)
            setMessage(null)
            try {
              await forgotPassword(email)
              setMessage('Si le compte existe, un lien a été envoyé.')
            } catch (err: any) {
              setServerError(err?.response?.data?.message ?? 'Erreur.')
            }
          })}
        >
          <label className="block">
            <span className="text-sm font-medium">Email</span>
            <input className="mt-1 w-full rounded-md border border-slate-200 px-3 py-2" type="email" {...register('email')} />
            {errors.email && <div className="text-xs text-red-600 mt-1">{errors.email.message}</div>}
          </label>

          {serverError && <div className="text-sm text-red-600">{serverError}</div>}
          {message && <div className="text-sm text-emerald-700">{message}</div>}

          <button disabled={isSubmitting} className="w-full rounded-md bg-slate-900 text-white py-2 font-medium disabled:opacity-60">
            Envoyer le lien
          </button>
        </form>

        <div className="mt-4 text-sm text-slate-600">
          <Link className="text-slate-900 underline" to="/login">
            Retour connexion
          </Link>
        </div>
      </div>
    </div>
  )
}

