import { useCallback, useEffect, useRef, useState } from 'react'
import { logExamEvents } from '../services/quiz.service'

export type ExamIntegrityEvent = {
  type: string
  at: string
  meta?: Record<string, unknown>
}

const CRITICAL_EVENTS = new Set([
  'tab_hidden',
  'window_blur',
  'page_close_attempt',
  'copy_attempt',
  'paste_attempt',
  'context_menu',
  'fullscreen_exit',
])

export function useExamIntegrity(sessionId: string | null, active: boolean) {
  const queueRef = useRef<ExamIntegrityEvent[]>([])
  const flushingRef = useRef(false)
  const [violationCount, setViolationCount] = useState(0)

  const push = useCallback((type: string, meta?: Record<string, unknown>) => {
    if (CRITICAL_EVENTS.has(type)) {
      setViolationCount((n) => n + 1)
    }
    queueRef.current.push({
      type,
      at: new Date().toISOString(),
      meta,
    })
  }, [])

  const flush = useCallback(async () => {
    if (!sessionId || !queueRef.current.length || flushingRef.current) return
    flushingRef.current = true
    const batch = queueRef.current.splice(0, 50)
    try {
      await logExamEvents(sessionId, batch)
    } catch {
      queueRef.current.unshift(...batch)
    } finally {
      flushingRef.current = false
    }
  }, [sessionId])

  useEffect(() => {
    if (!active || !sessionId) return

    const onVisibility = () => {
      push(document.hidden ? 'tab_hidden' : 'tab_visible', {
        visibilityState: document.visibilityState,
      })
      void flush()
    }

    const onBlur = () => {
      push('window_blur')
      void flush()
    }

    const onFocus = () => {
      push('window_focus')
      void flush()
    }

    const onBeforeUnload = () => {
      push('page_close_attempt')
      void flush()
    }

    const onCopy = () => {
      push('copy_attempt')
      void flush()
    }

    const onPaste = () => {
      push('paste_attempt')
      void flush()
    }

    const onContextMenu = (e: MouseEvent) => {
      push('context_menu', { x: e.clientX, y: e.clientY })
      void flush()
    }

    const onFullscreenChange = () => {
      if (!document.fullscreenElement) {
        push('fullscreen_exit')
        void flush()
      }
    }

    document.addEventListener('visibilitychange', onVisibility)
    window.addEventListener('blur', onBlur)
    window.addEventListener('focus', onFocus)
    window.addEventListener('beforeunload', onBeforeUnload)
    document.addEventListener('copy', onCopy)
    document.addEventListener('paste', onPaste)
    document.addEventListener('contextmenu', onContextMenu)
    document.addEventListener('fullscreenchange', onFullscreenChange)

    const interval = window.setInterval(() => void flush(), 5000)

    return () => {
      document.removeEventListener('visibilitychange', onVisibility)
      window.removeEventListener('blur', onBlur)
      window.removeEventListener('focus', onFocus)
      window.removeEventListener('beforeunload', onBeforeUnload)
      document.removeEventListener('copy', onCopy)
      document.removeEventListener('paste', onPaste)
      document.removeEventListener('contextmenu', onContextMenu)
      document.removeEventListener('fullscreenchange', onFullscreenChange)
      window.clearInterval(interval)
      void flush()
    }
  }, [active, sessionId, push, flush])

  return { push, flush, violationCount }
}
