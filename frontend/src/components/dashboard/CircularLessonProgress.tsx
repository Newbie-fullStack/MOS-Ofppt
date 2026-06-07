/** Anneau circulaire de progression pour les cartes module (réutilisable hors Recharts). */
export default function CircularLessonProgress({
  value,
  size = 72,
  stroke = 7,
  color,
}: {
  value: number
  size?: number
  stroke?: number
  color: string
}) {
  const pct = Math.min(100, Math.max(0, Math.round(value)))
  const r = (size - stroke) / 2
  const c = 2 * Math.PI * r
  const offset = c - (pct / 100) * c

  return (
    <div className="relative shrink-0" style={{ width: size, height: size }}>
      <svg width={size} height={size} className="-rotate-90" aria-hidden>
        <circle
          cx={size / 2}
          cy={size / 2}
          r={r}
          stroke="#e2e8f0"
          strokeWidth={stroke}
          fill="none"
        />
        <circle
          cx={size / 2}
          cy={size / 2}
          r={r}
          stroke={color}
          strokeWidth={stroke}
          fill="none"
          strokeDasharray={c}
          strokeDashoffset={offset}
          strokeLinecap="round"
        />
      </svg>
      <span className="pointer-events-none absolute inset-0 flex items-center justify-center text-[13px] font-bold tabular-nums text-slate-800">
        {pct}%
      </span>
    </div>
  )
}
