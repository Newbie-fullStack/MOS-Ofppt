// src/components/course/LessonContent.tsx
// Composant qui rend les blocs de contenu d'une leçon de façon visuelle et professionnelle

import { useState } from 'react'
import {
  BookOpen, Lightbulb, AlertTriangle, Target,
  ChevronDown, ChevronUp, CheckCircle2, Keyboard
} from 'lucide-react'

// ── Types ─────────────────────────────────────────────────────────

interface ContentBlock {
  type: 'intro' | 'steps' | 'definition' | 'tip' | 'warning' | 'exam_focus' | 'shortcut_table'
  title?: string
  text?: string
  term?: string
  tip?: string
  items?: StepItem[] | string[]
  rows?: ShortcutRow[]
}

interface StepItem {
  num: number
  action: string
  detail: string
}

interface ShortcutRow {
  keys: string
  action: string
}

interface LessonContentProps {
  contentJson: { blocks: ContentBlock[] }
  moduleColor?: string
}

// ── MODULE COLORS ─────────────────────────────────────────────────
const MODULE_COLORS: Record<string, { bg: string; border: string; text: string; light: string }> = {
  WORD:        { bg: '#185FA5', border: '#185FA5', text: '#185FA5', light: '#EBF3FC' },
  EXCEL:       { bg: '#1D6A47', border: '#1D6A47', text: '#1D6A47', light: '#E8F5EE' },
  POWERPOINT:  { bg: '#C43E1C', border: '#C43E1C', text: '#C43E1C', light: '#FDEEE9' },
}

// ── BLOCK RENDERERS ───────────────────────────────────────────────

function IntroBlock({ block, color }: { block: ContentBlock; color: typeof MODULE_COLORS.WORD }) {
  return (
    <div style={{
      background: `linear-gradient(135deg, ${color.light}, #fff)`,
      borderLeft: `4px solid ${color.bg}`,
      borderRadius: '0 12px 12px 0',
      padding: '20px 24px',
      marginBottom: '20px',
    }}>
      <div style={{ display: 'flex', alignItems: 'flex-start', gap: '12px' }}>
        <div style={{
          background: color.bg, borderRadius: '8px',
          padding: '8px', flexShrink: 0, marginTop: '2px'
        }}>
          <BookOpen size={18} color="#fff" />
        </div>
        <div>
          {block.title && (
            <h3 style={{ fontSize: '17px', fontWeight: '700', color: '#1a1a1a', marginBottom: '8px' }}>
              {block.title}
            </h3>
          )}
          <p style={{ fontSize: '14px', lineHeight: '1.75', color: '#374151', margin: 0 }}>
            {block.text}
          </p>
          {block.tip && (
            <p style={{
              marginTop: '12px', fontSize: '13px', color: color.text,
              fontWeight: '500', fontStyle: 'italic'
            }}>
              💡 {block.tip}
            </p>
          )}
        </div>
      </div>
    </div>
  )
}

function StepsBlock({ block, color }: { block: ContentBlock; color: typeof MODULE_COLORS.WORD }) {
  const [expanded, setExpanded] = useState<number | null>(null)
  const steps = block.items as StepItem[]

  return (
    <div style={{
      border: '1px solid #E5E7EB', borderRadius: '12px',
      overflow: 'hidden', marginBottom: '20px',
    }}>
      {/* Header */}
      <div style={{
        background: color.bg, padding: '14px 20px',
        display: 'flex', alignItems: 'center', gap: '8px',
      }}>
        <div style={{
          width: '24px', height: '24px', borderRadius: '50%',
          background: 'rgba(255,255,255,0.25)',
          display: 'flex', alignItems: 'center', justifyContent: 'center',
          fontSize: '13px', fontWeight: '700', color: '#fff'
        }}>
          {steps?.length}
        </div>
        <span style={{ fontWeight: '600', color: '#fff', fontSize: '15px' }}>
          {block.title}
        </span>
      </div>

      {/* Steps */}
      <div style={{ padding: '4px 0' }}>
        {steps?.map((step, i) => (
          <div
            key={i}
            style={{
              borderBottom: i < steps.length - 1 ? '1px solid #F3F4F6' : 'none',
            }}
          >
            <button
              onClick={() => setExpanded(expanded === i ? null : i)}
              style={{
                width: '100%', padding: '14px 20px',
                display: 'flex', alignItems: 'center', gap: '14px',
                background: expanded === i ? '#F9FAFB' : 'transparent',
                border: 'none', cursor: 'pointer', textAlign: 'left',
                transition: 'background 0.15s',
              }}
            >
              {/* Step number */}
              <div style={{
                width: '30px', height: '30px', borderRadius: '50%', flexShrink: 0,
                background: expanded === i ? color.bg : color.light,
                color: expanded === i ? '#fff' : color.text,
                display: 'flex', alignItems: 'center', justifyContent: 'center',
                fontSize: '13px', fontWeight: '700', transition: 'all 0.2s',
              }}>
                {step.num}
              </div>
              <span style={{ flex: 1, fontSize: '14px', fontWeight: '500', color: '#111827' }}>
                {step.action}
              </span>
              {expanded === i
                ? <ChevronUp size={16} color="#9CA3AF" />
                : <ChevronDown size={16} color="#9CA3AF" />
              }
            </button>

            {/* Detail */}
            {expanded === i && (
              <div style={{
                padding: '0 20px 14px 64px',
                animation: 'fadeIn 0.2s ease',
              }}>
                <p style={{
                  fontSize: '13.5px', color: '#4B5563', lineHeight: '1.65',
                  background: color.light, padding: '10px 14px',
                  borderRadius: '8px', margin: 0,
                }}>
                  {step.detail}
                </p>
              </div>
            )}
          </div>
        ))}
      </div>
    </div>
  )
}

function DefinitionBlock({ block, color }: { block: ContentBlock; color: typeof MODULE_COLORS.WORD }) {
  return (
    <div style={{
      display: 'flex', gap: '0', marginBottom: '20px',
      border: '1px solid #E5E7EB', borderRadius: '12px', overflow: 'hidden',
    }}>
      <div style={{
        background: color.bg, width: '6px', flexShrink: 0,
      }} />
      <div style={{ padding: '16px 20px', flex: 1 }}>
        <span style={{
          display: 'inline-block', background: color.light,
          color: color.text, padding: '2px 10px', borderRadius: '20px',
          fontSize: '11px', fontWeight: '700', textTransform: 'uppercase',
          letterSpacing: '0.5px', marginBottom: '8px',
        }}>
          Définition
        </span>
        {block.term && (
          <h4 style={{ fontSize: '15px', fontWeight: '700', color: '#111827', marginBottom: '6px' }}>
            {block.term}
          </h4>
        )}
        <p style={{ fontSize: '14px', color: '#374151', lineHeight: '1.7', margin: 0 }}>
          {block.text}
        </p>
      </div>
    </div>
  )
}

function TipBlock({ block }: { block: ContentBlock }) {
  return (
    <div style={{
      background: '#FFFBEB', border: '1px solid #FDE68A',
      borderRadius: '12px', padding: '16px 20px', marginBottom: '20px',
      display: 'flex', gap: '12px', alignItems: 'flex-start',
    }}>
      <div style={{
        background: '#F59E0B', borderRadius: '8px',
        padding: '6px', flexShrink: 0,
      }}>
        <Lightbulb size={16} color="#fff" />
      </div>
      <div>
        {block.title && (
          <p style={{ fontWeight: '700', color: '#92400E', fontSize: '14px', marginBottom: '4px' }}>
            {block.title}
          </p>
        )}
        <p style={{ fontSize: '13.5px', color: '#78350F', lineHeight: '1.65', margin: 0 }}>
          {block.text}
        </p>
      </div>
    </div>
  )
}

function WarningBlock({ block }: { block: ContentBlock }) {
  return (
    <div style={{
      background: '#FEF2F2', border: '1px solid #FECACA',
      borderRadius: '12px', padding: '16px 20px', marginBottom: '20px',
      display: 'flex', gap: '12px', alignItems: 'flex-start',
    }}>
      <div style={{
        background: '#EF4444', borderRadius: '8px',
        padding: '6px', flexShrink: 0,
      }}>
        <AlertTriangle size={16} color="#fff" />
      </div>
      <div>
        {block.title && (
          <p style={{ fontWeight: '700', color: '#7F1D1D', fontSize: '14px', marginBottom: '4px' }}>
            {block.title}
          </p>
        )}
        <p style={{ fontSize: '13.5px', color: '#991B1B', lineHeight: '1.65', margin: 0 }}>
          {block.text}
        </p>
      </div>
    </div>
  )
}

function ExamFocusBlock({ block, color }: { block: ContentBlock; color: typeof MODULE_COLORS.WORD }) {
  const items = block.items as string[]
  return (
    <div style={{
      background: 'linear-gradient(135deg, #0F172A, #1E293B)',
      borderRadius: '12px', padding: '20px 24px', marginBottom: '20px',
    }}>
      <div style={{ display: 'flex', alignItems: 'center', gap: '10px', marginBottom: '16px' }}>
        <div style={{
          background: color.bg, borderRadius: '8px', padding: '6px',
        }}>
          <Target size={16} color="#fff" />
        </div>
        <span style={{ fontWeight: '700', color: '#F8FAFC', fontSize: '15px' }}>
          {block.title || '🎯 Points clés pour l\'examen MOS'}
        </span>
      </div>
      <div style={{ display: 'flex', flexDirection: 'column', gap: '8px' }}>
        {items?.map((item, i) => (
          <div key={i} style={{ display: 'flex', alignItems: 'flex-start', gap: '10px' }}>
            <CheckCircle2 size={16} color={color.bg} style={{ flexShrink: 0, marginTop: '2px' }} />
            <span style={{ fontSize: '13.5px', color: '#CBD5E1', lineHeight: '1.55' }}>
              {item}
            </span>
          </div>
        ))}
      </div>
    </div>
  )
}

function ShortcutTableBlock({ block, color }: { block: ContentBlock; color: typeof MODULE_COLORS.WORD }) {
  return (
    <div style={{
      border: '1px solid #E5E7EB', borderRadius: '12px',
      overflow: 'hidden', marginBottom: '20px',
    }}>
      <div style={{
        background: '#F8FAFC', padding: '12px 20px',
        display: 'flex', alignItems: 'center', gap: '8px',
        borderBottom: '1px solid #E5E7EB',
      }}>
        <Keyboard size={16} color={color.text} />
        <span style={{ fontWeight: '600', color: '#374151', fontSize: '14px' }}>
          {block.title}
        </span>
      </div>
      <table style={{ width: '100%', borderCollapse: 'collapse' }}>
        <tbody>
          {block.rows?.map((row, i) => (
            <tr key={i} style={{
              background: i % 2 === 0 ? '#fff' : '#F9FAFB',
              borderBottom: i < (block.rows?.length ?? 0) - 1 ? '1px solid #F3F4F6' : 'none',
            }}>
              <td style={{ padding: '10px 20px', width: '200px' }}>
                {row.keys.split('+').map((k, ki) => (
                  <span key={ki}>
                    <kbd style={{
                      display: 'inline-block', padding: '2px 8px',
                      background: color.light, color: color.text,
                      border: `1px solid ${color.border}`,
                      borderRadius: '6px', fontSize: '12px',
                      fontFamily: 'monospace', fontWeight: '600',
                    }}>
                      {k.trim()}
                    </kbd>
                    {ki < row.keys.split('+').length - 1 && (
                      <span style={{ margin: '0 4px', color: '#9CA3AF', fontSize: '12px' }}>+</span>
                    )}
                  </span>
                ))}
              </td>
              <td style={{ padding: '10px 20px', fontSize: '13.5px', color: '#374151' }}>
                {row.action}
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  )
}

// ── MAIN COMPONENT ────────────────────────────────────────────────

export default function LessonContent({ contentJson, moduleColor = 'WORD' }: LessonContentProps) {
  const color = MODULE_COLORS[moduleColor] ?? MODULE_COLORS.WORD
  const blocks = contentJson?.blocks ?? []

  if (blocks.length === 0) {
    return (
      <div style={{
        textAlign: 'center', padding: '60px 20px',
        color: '#9CA3AF', fontSize: '14px',
      }}>
        <BookOpen size={40} color="#D1D5DB" style={{ marginBottom: '12px' }} />
        <p>Le contenu de cette leçon est en cours de rédaction.</p>
        <p style={{ fontSize: '12px', marginTop: '4px' }}>Revenez bientôt !</p>
      </div>
    )
  }

  return (
    <div style={{ maxWidth: '760px' }}>
      <style>{`
        @keyframes fadeIn {
          from { opacity: 0; transform: translateY(-4px); }
          to   { opacity: 1; transform: translateY(0); }
        }
      `}</style>

      {blocks.map((block, i) => {
        switch (block.type) {
          case 'intro':
            return <IntroBlock key={i} block={block} color={color} />
          case 'steps':
            return <StepsBlock key={i} block={block} color={color} />
          case 'definition':
            return <DefinitionBlock key={i} block={block} color={color} />
          case 'tip':
            return <TipBlock key={i} block={block} />
          case 'warning':
            return <WarningBlock key={i} block={block} />
          case 'exam_focus':
            return <ExamFocusBlock key={i} block={block} color={color} />
          case 'shortcut_table':
            return <ShortcutTableBlock key={i} block={block} color={color} />
          default:
            return null
        }
      })}
    </div>
  )
}
