import {
  useLayoutEffect,
  useRef,
  useState,
  useCallback,
  type KeyboardEvent,
  type CSSProperties,
} from 'react'
import { Link } from 'react-router-dom'
import { gsap } from 'gsap'
import { ArrowUpRight } from 'lucide-react'
import './CardNav.css'

export type CardNavLinkItem = {
  label: string
  ariaLabel?: string
  href: string
}

export type CardNavItem = {
  label: string
  bgColor: string
  textColor: string
  links: CardNavLinkItem[]
}

export type CardNavProps = {
  logo?: string
  logoAlt?: string
  brandText?: string
  items: CardNavItem[]
  className?: string
  ease?: string
  baseColor?: string
  menuColor?: string
  buttonBgColor?: string
  buttonTextColor?: string
  ctaLabel?: string
  onCtaClick?: () => void
  /** Classes Tailwind/CSS pour personnaliser le bouton CTA (ex. bordure, fond). */
  ctaClassName?: string
  ctaStyle?: CSSProperties
}

function NavCardLink({
  link,
  onNavigate,
}: {
  link: CardNavLinkItem
  onNavigate: () => void
}) {
  const internal = link.href.startsWith('/')
  const label = link.ariaLabel ?? link.label
  const body = (
    <>
      <ArrowUpRight className="nav-card-link-icon" aria-hidden />
      {link.label}
    </>
  )
  if (internal) {
    return (
      <Link to={link.href} className="nav-card-link" aria-label={label} onClick={onNavigate}>
        {body}
      </Link>
    )
  }
  return (
    <a href={link.href} className="nav-card-link" aria-label={label} onClick={onNavigate}>
      {body}
    </a>
  )
}

const CardNav = ({
  logo,
  logoAlt = 'Logo',
  brandText,
  items,
  className = '',
  ease = 'power3.out',
  baseColor = '#fff',
  menuColor,
  buttonBgColor = '#111',
  buttonTextColor = 'white',
  ctaLabel,
  onCtaClick,
  ctaClassName,
  ctaStyle,
}: CardNavProps) => {
  const [isHamburgerOpen, setIsHamburgerOpen] = useState(false)
  const [isExpanded, setIsExpanded] = useState(false)
  const navRef = useRef<HTMLElement | null>(null)
  const cardsRef = useRef<(HTMLDivElement | null)[]>([])
  const tlRef = useRef<gsap.core.Timeline | null>(null)

  const cardElements = useCallback(() => {
    return cardsRef.current.filter((el): el is HTMLDivElement => el != null)
  }, [])

  const calculateHeight = useCallback(() => {
    const navEl = navRef.current
    if (!navEl) return 260

    const isMobile = window.matchMedia('(max-width: 768px)').matches
    if (isMobile) {
      const contentEl = navEl.querySelector('.card-nav-content')
      if (contentEl instanceof HTMLElement) {
        const wasVisible = contentEl.style.visibility
        const wasPointerEvents = contentEl.style.pointerEvents
        const wasPosition = contentEl.style.position
        const wasHeight = contentEl.style.height

        contentEl.style.visibility = 'visible'
        contentEl.style.pointerEvents = 'auto'
        contentEl.style.position = 'static'
        contentEl.style.height = 'auto'

        void contentEl.offsetHeight

        const topBar = 60
        const padding = 16
        const contentHeight = contentEl.scrollHeight

        contentEl.style.visibility = wasVisible
        contentEl.style.pointerEvents = wasPointerEvents
        contentEl.style.position = wasPosition
        contentEl.style.height = wasHeight

        return topBar + contentHeight + padding
      }
    }
    return 260
  }, [])

  const createTimeline = useCallback(() => {
    const navEl = navRef.current
    if (!navEl) return null

    const cards = cardElements()
    gsap.set(navEl, { height: 60, overflow: 'hidden' })
    if (cards.length) {
      gsap.set(cards, { y: 50, opacity: 0 })
    }

    const tl = gsap.timeline({ paused: true })

    tl.to(navEl, {
      height: calculateHeight,
      duration: 0.4,
      ease,
    })

    if (cards.length) {
      tl.to(cards, { y: 0, opacity: 1, duration: 0.4, ease, stagger: 0.08 }, '-=0.1')
    }

    return tl
  }, [calculateHeight, cardElements, ease])

  useLayoutEffect(() => {
    const tl = createTimeline()
    tlRef.current = tl

    return () => {
      tl?.kill()
      tlRef.current = null
    }
  }, [createTimeline, items])

  useLayoutEffect(() => {
    const handleResize = () => {
      if (!tlRef.current) return

      if (isExpanded) {
        const newHeight = calculateHeight()
        gsap.set(navRef.current, { height: newHeight })

        tlRef.current.kill()
        const newTl = createTimeline()
        if (newTl) {
          newTl.progress(1)
          tlRef.current = newTl
        }
      } else {
        tlRef.current.kill()
        const newTl = createTimeline()
        if (newTl) {
          tlRef.current = newTl
        }
      }
    }

    window.addEventListener('resize', handleResize)
    return () => window.removeEventListener('resize', handleResize)
  }, [calculateHeight, createTimeline, isExpanded])

  const collapseMenu = useCallback(() => {
    const tl = tlRef.current
    if (!tl || !isExpanded) return
    setIsHamburgerOpen(false)
    tl.eventCallback('onReverseComplete', () => setIsExpanded(false))
    tl.reverse()
  }, [isExpanded])

  const toggleMenu = useCallback(() => {
    const tl = tlRef.current
    if (!tl) return
    if (!isExpanded) {
      setIsHamburgerOpen(true)
      setIsExpanded(true)
      tl.play(0)
    } else {
      collapseMenu()
    }
  }, [collapseMenu, isExpanded])

  const onHamburgerKeyDown = (e: KeyboardEvent) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault()
      toggleMenu()
    }
  }

  const setCardRef = (i: number) => (el: HTMLDivElement | null) => {
    cardsRef.current[i] = el
  }

  const displayItems = (items ?? []).slice(0, 6)

  return (
    <div className={`card-nav-container ${className}`.trim()}>
      <nav
        ref={navRef}
        className={`card-nav ${isExpanded ? 'open' : ''}`}
        style={{ backgroundColor: baseColor }}
        aria-label="Navigation principale"
      >
        <div className="card-nav-top">
          <button
            type="button"
            className={`hamburger-menu ${isHamburgerOpen ? 'open' : ''}`}
            onClick={toggleMenu}
            aria-expanded={isExpanded}
            aria-controls="card-nav-panels"
            aria-label={isExpanded ? 'Fermer le menu' : 'Ouvrir le menu'}
            tabIndex={0}
            style={{ color: menuColor ?? '#000' }}
            onKeyDown={onHamburgerKeyDown}
          >
            <span className="hamburger-line" />
            <span className="hamburger-line" />
          </button>

          <div className="logo-container">
            {logo ? (
              <img src={logo} alt={logoAlt} className="logo" />
            ) : (
              <span className="logo-brand-text">{brandText ?? logoAlt}</span>
            )}
          </div>

          {onCtaClick != null ? (
            <button
              type="button"
              className={`card-nav-cta-button ${ctaClassName ?? ''}`.trim()}
              style={{ backgroundColor: buttonBgColor, color: buttonTextColor, ...(ctaStyle ?? {}) }}
              onClick={onCtaClick}
            >
              {ctaLabel ?? 'Action'}
            </button>
          ) : (
            <div className="card-nav-trailing-placeholder" aria-hidden />
          )}
        </div>

        <div
          id="card-nav-panels"
          className="card-nav-content"
          aria-hidden={!isExpanded}
        >
          {displayItems.map((item, idx) => (
            <div
              key={`${item.label}-${idx}`}
              className="nav-card"
              ref={setCardRef(idx)}
              style={{ backgroundColor: item.bgColor, color: item.textColor }}
            >
              <div className="nav-card-label">{item.label}</div>
              <div className="nav-card-links">
                {item.links.map((lnk, i) => (
                  <NavCardLink key={`${lnk.label}-${i}`} link={lnk} onNavigate={collapseMenu} />
                ))}
              </div>
            </div>
          ))}
        </div>
      </nav>
    </div>
  )
}

export default CardNav
