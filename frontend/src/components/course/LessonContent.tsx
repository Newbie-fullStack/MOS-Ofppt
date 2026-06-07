import { useState } from 'react'
import { BookOpen, Lightbulb, AlertTriangle, Target, ChevronDown, ChevronUp, CheckCircle2, Keyboard } from 'lucide-react'

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

export default function LessonContent({ contentJson, moduleColor = 'WORD' }: { contentJson: { blocks: ContentBlock[] }; moduleColor?: string }) {
  const MODULE_COLORS: Record<string, { bg: string; border: string; text: string; light: string }> = {
    WORD: { bg: '#185FA5', border: '#185FA5', text: '#185FA5', light: '#EBF3FC' },
    EXCEL: { bg: '#1D6A47', border: '#1D6A47', text: '#1D6A47', light: '#E8F5EE' },
    POWERPOINT: { bg: '#C43E1C', border: '#C43E1C', text: '#C43E1C', light: '#FDEEE9' },
  }
  const color = MODULE_COLORS[moduleColor] ?? MODULE_COLORS.WORD
  const blocks = contentJson?.blocks ?? []

  if (blocks.length === 0) {
    return (
      <div className="text-center py-14 text-slate-400 text-sm">
        <div className="flex justify-center mb-3">
          <BookOpen size={40} color="#D1D5DB" />
        </div>
        <p>Le contenu de cette leçon est en cours de rédaction.</p>
        <p className="text-xs mt-1">Revenez bientôt !</p>
      </div>
    )
  }

  function IntroBlock({ block }: { block: ContentBlock }) {
    return (
      <div className="rounded-xl border-l-4 p-5 mb-5" style={{ background: `linear-gradient(135deg, ${color.light}, #fff)`, borderLeftColor: color.bg }}>
        <div className="flex gap-3">
          <div className="rounded-lg p-2 shrink-0" style={{ background: color.bg }}>
            <BookOpen size={18} color="#fff" />
          </div>
          <div>
            {block.title && <h3 className="text-[17px] font-bold text-slate-900 mb-2">{block.title}</h3>}
            <p className="text-sm leading-7 text-slate-700">{block.text}</p>
            {block.tip && <p className="mt-3 text-sm italic font-medium" style={{ color: color.text }}>{block.tip}</p>}
          </div>
        </div>
      </div>
    )
  }

  function StepsBlock({ block }: { block: ContentBlock }) {
    const [expanded, setExpanded] = useState<number | null>(null)
    const steps = block.items as StepItem[]
    return (
      <div className="border border-slate-200 rounded-xl overflow-hidden mb-5 bg-white">
        <div className="px-5 py-3 flex items-center gap-2" style={{ background: color.bg }}>
          <div className="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white" style={{ background: 'rgba(255,255,255,0.25)' }}>
            {steps?.length ?? 0}
          </div>
          <span className="font-semibold text-white">{block.title}</span>
        </div>
        <div>
          {steps?.map((step, i) => (
            <div key={i} className={i < steps.length - 1 ? 'border-b border-slate-100' : ''}>
              <button
                className={`w-full px-5 py-3 flex items-center gap-3 text-left ${expanded === i ? 'bg-slate-50' : ''}`}
                onClick={() => setExpanded(expanded === i ? null : i)}
              >
                <div
                  className="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                  style={{
                    background: expanded === i ? color.bg : color.light,
                    color: expanded === i ? '#fff' : color.text,
                  }}
                >
                  {step.num}
                </div>
                <span className="text-sm font-medium text-slate-900 flex-1">{step.action}</span>
                {expanded === i ? <ChevronUp size={16} color="#9CA3AF" /> : <ChevronDown size={16} color="#9CA3AF" />}
              </button>
              {expanded === i && (
                <div className="px-5 pb-3 pl-16">
                  <div className="text-sm text-slate-700 leading-6 rounded-lg p-3" style={{ background: color.light }}>
                    {step.detail}
                  </div>
                </div>
              )}
            </div>
          ))}
        </div>
      </div>
    )
  }

  function DefinitionBlock({ block }: { block: ContentBlock }) {
    return (
      <div className="border border-slate-200 rounded-xl overflow-hidden mb-5 bg-white flex">
        <div className="w-1.5" style={{ background: color.bg }} />
        <div className="p-5">
          <span className="inline-block text-[11px] font-bold uppercase tracking-wide px-3 py-0.5 rounded-full" style={{ background: color.light, color: color.text }}>
            Définition
          </span>
          {block.term && <div className="mt-2 font-bold text-slate-900">{block.term}</div>}
          <p className="mt-2 text-sm text-slate-700 leading-7">{block.text}</p>
        </div>
      </div>
    )
  }

  function TipBlock({ block }: { block: ContentBlock }) {
    return (
      <div className="bg-amber-50 border border-amber-200 rounded-xl p-5 mb-5 flex gap-3">
        <div className="bg-amber-500 rounded-lg p-1.5 shrink-0">
          <Lightbulb size={16} color="#fff" />
        </div>
        <div>
          {block.title && <div className="font-bold text-amber-900 text-sm">{block.title}</div>}
          <div className="text-sm text-amber-900/90 leading-6 mt-1">{block.text}</div>
        </div>
      </div>
    )
  }

  function WarningBlock({ block }: { block: ContentBlock }) {
    return (
      <div className="bg-red-50 border border-red-200 rounded-xl p-5 mb-5 flex gap-3">
        <div className="bg-red-500 rounded-lg p-1.5 shrink-0">
          <AlertTriangle size={16} color="#fff" />
        </div>
        <div>
          {block.title && <div className="font-bold text-red-900 text-sm">{block.title}</div>}
          <div className="text-sm text-red-900/90 leading-6 mt-1">{block.text}</div>
        </div>
      </div>
    )
  }

  function ExamFocusBlock({ block }: { block: ContentBlock }) {
    const items = block.items as string[]
    return (
      <div className="rounded-xl p-5 mb-5 text-slate-200" style={{ background: 'linear-gradient(135deg, #0F172A, #1E293B)' }}>
        <div className="flex items-center gap-2 mb-4">
          <div className="rounded-lg p-1.5" style={{ background: color.bg }}>
            <Target size={16} color="#fff" />
          </div>
          <div className="font-bold text-slate-50">{block.title ?? "🎯 Points clés pour l'examen MOS"}</div>
        </div>
        <div className="grid gap-2">
          {items?.map((it, i) => (
            <div key={i} className="flex gap-2 items-start">
              <CheckCircle2 size={16} color={color.bg} className="mt-0.5 shrink-0" />
              <div className="text-sm leading-6">{it}</div>
            </div>
          ))}
        </div>
      </div>
    )
  }

  function ShortcutTableBlock({ block }: { block: ContentBlock }) {
    return (
      <div className="border border-slate-200 rounded-xl overflow-hidden mb-5 bg-white">
        <div className="bg-slate-50 px-5 py-3 flex items-center gap-2 border-b border-slate-200">
          <Keyboard size={16} color={color.text} />
          <span className="font-semibold text-slate-700 text-sm">{block.title}</span>
        </div>
        <table className="w-full border-collapse">
          <tbody>
            {block.rows?.map((row, i) => (
              <tr key={i} className={i % 2 === 0 ? 'bg-white' : 'bg-slate-50'}>
                <td className="px-5 py-2 w-56">
                  {row.keys.split('+').map((k, ki) => (
                    <span key={ki}>
                      <kbd className="inline-block px-2 py-0.5 rounded-md text-xs font-semibold border" style={{ background: color.light, color: color.text, borderColor: color.border }}>
                        {k.trim()}
                      </kbd>
                      {ki < row.keys.split('+').length - 1 && <span className="mx-1 text-slate-400 text-xs">+</span>}
                    </span>
                  ))}
                </td>
                <td className="px-5 py-2 text-sm text-slate-700">{row.action}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    )
  }

  return (
    <div className="max-w-3xl">
      {blocks.map((block, i) => {
        switch (block.type) {
          case 'intro':
            return <IntroBlock key={i} block={block} />
          case 'steps':
            return <StepsBlock key={i} block={block} />
          case 'definition':
            return <DefinitionBlock key={i} block={block} />
          case 'tip':
            return <TipBlock key={i} block={block} />
          case 'warning':
            return <WarningBlock key={i} block={block} />
          case 'exam_focus':
            return <ExamFocusBlock key={i} block={block} />
          case 'shortcut_table':
            return <ShortcutTableBlock key={i} block={block} />
          default:
            return null
        }
      })}
    </div>
  )
}

