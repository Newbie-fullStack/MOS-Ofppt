export default function CertificatesPage() {
  return (
    <div className="mx-auto max-w-2xl">
      <h1 className="text-2xl font-bold tracking-tight text-slate-900">Certificats MOS</h1>
      <p className="mt-3 text-slate-600">
        Une fois vos examens MOS Certiport validés ou votre parcours certifié par votre centre OFPPT, vos
        attestations apparaîtront ici en PDF.
      </p>
      <div className="mt-8 rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center shadow-sm">
        <p className="text-sm font-medium text-slate-500">Aucun certificat téléchargeable pour le moment.</p>
        <p className="mt-2 text-xs text-slate-400">
          Complétez les modules ou contactez votre formateur pour le suivi officiel MOS.
        </p>
      </div>
    </div>
  )
}
