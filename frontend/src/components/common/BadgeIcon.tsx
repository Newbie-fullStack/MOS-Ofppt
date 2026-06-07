type BadgeIconProps = {
  badgeId?: string
  name?: string
  size?: 'sm' | 'md' | 'lg'
  className?: string
}

const sizeMap = {
  sm: 'h-9 w-9',
  md: 'h-11 w-11',
  lg: 'h-14 w-14',
}

function resolveKind(badgeId?: string, name?: string): 'word' | 'excel' | 'powerpoint' | 'exam' | 'default' {
  const hay = `${badgeId ?? ''} ${name ?? ''}`.toLowerCase()
  if (hay.includes('word')) return 'word'
  if (hay.includes('excel')) return 'excel'
  if (hay.includes('powerpoint') || hay.includes('ppt')) return 'powerpoint'
  if (hay.includes('examen') || hay.includes('exam')) return 'exam'
  return 'default'
}

export default function BadgeIcon({ badgeId, name, size = 'md', className = '' }: BadgeIconProps) {
  const kind = resolveKind(badgeId, name)
  const dim = sizeMap[size]

  const base = `${dim} shrink-0 rounded-xl shadow-inner ring-1 ring-black/5 ${className}`

  if (kind === 'word') {
    return (
      <span className={`${base} flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-700`} aria-hidden>
        <svg viewBox="0 0 32 32" className="h-[58%] w-[58%]" fill="none">
          <text x="16" y="22" textAnchor="middle" fill="white" fontSize="16" fontWeight="700" fontFamily="system-ui, sans-serif">
            W
          </text>
        </svg>
      </span>
    )
  }

  if (kind === 'excel') {
    return (
      <span className={`${base} flex items-center justify-center bg-gradient-to-br from-emerald-500 to-green-700`} aria-hidden>
        <svg viewBox="0 0 32 32" className="h-[58%] w-[58%]" fill="none">
          <rect x="6" y="6" width="8" height="8" rx="1" fill="white" fillOpacity="0.95" />
          <rect x="18" y="6" width="8" height="8" rx="1" fill="white" fillOpacity="0.75" />
          <rect x="6" y="18" width="8" height="8" rx="1" fill="white" fillOpacity="0.75" />
          <rect x="18" y="18" width="8" height="8" rx="1" fill="white" fillOpacity="0.95" />
        </svg>
      </span>
    )
  }

  if (kind === 'powerpoint') {
    return (
      <span className={`${base} flex items-center justify-center bg-gradient-to-br from-orange-500 to-orange-700`} aria-hidden>
        <svg viewBox="0 0 32 32" className="h-[58%] w-[58%]" fill="none">
          <text x="16" y="22" textAnchor="middle" fill="white" fontSize="16" fontWeight="700" fontFamily="system-ui, sans-serif">
            P
          </text>
        </svg>
      </span>
    )
  }

  if (kind === 'exam') {
    return (
      <span className={`${base} flex items-center justify-center bg-gradient-to-br from-violet-500 to-indigo-700`} aria-hidden>
        <svg viewBox="0 0 32 32" className="h-[58%] w-[58%]" fill="none">
          <path
            d="M10 8h12l2 4v14H10V8z"
            stroke="white"
            strokeWidth="2"
            strokeLinejoin="round"
            fill="white"
            fillOpacity="0.2"
          />
          <path d="M12 16h8M12 20h6" stroke="white" strokeWidth="2" strokeLinecap="round" />
        </svg>
      </span>
    )
  }

  return (
    <span className={`${base} flex items-center justify-center bg-gradient-to-br from-amber-400 to-amber-600`} aria-hidden>
      <svg viewBox="0 0 32 32" className="h-[58%] w-[58%]" fill="none">
        <circle cx="16" cy="14" r="6" fill="white" fillOpacity="0.95" />
        <path d="M8 26c2-4 5-6 8-6s6 2 8 6" stroke="white" strokeWidth="2" strokeLinecap="round" />
      </svg>
    </span>
  )
}
