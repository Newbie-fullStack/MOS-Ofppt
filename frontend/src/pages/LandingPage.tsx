import { Link } from 'react-router-dom'
import { ArrowRight, BookOpen, Award, TrendingUp, Users } from 'lucide-react'
import TrueFocus from '../components/effects/TrueFocus'
import TargetCursor from '../components/effects/TargetCursor'
import './LandingPage.css'

export default function LandingPage() {
  return (
    <div className="landing-page">
      <TargetCursor 
        spinDuration={2}
        hideDefaultCursor={true}
        parallaxOn={true}
      />

      <section className="hero-section">
        <div className="hero-content">
          <div className="hero-title">
            <TrueFocus 
              sentence="Maîtrisez Microsoft Office"
              manualMode={false}
              blurAmount={3}
              borderColor="#3b82f6"
              glowColor="rgba(59, 130, 246, 0.6)"
              animationDuration={0.8}
              pauseBetweenAnimations={1.5}
            />
          </div>
          
          <p className="hero-subtitle cursor-target">
            Plateforme de formation MOS OFPPT - Préparez-vous aux certifications Microsoft Office Specialist
          </p>

          <div className="hero-cta">
            <Link to="/register" className="cta-button primary cursor-target">
              Commencer maintenant
              <ArrowRight className="ml-2 h-5 w-5" />
            </Link>
            <Link to="/login" className="cta-button secondary cursor-target">
              Se connecter
            </Link>
          </div>
        </div>

        <div className="hero-visual">
          <div className="floating-card card-1 cursor-target">
            <BookOpen className="h-8 w-8 text-blue-600" />
            <span>Word</span>
          </div>
          <div className="floating-card card-2 cursor-target">
            <TrendingUp className="h-8 w-8 text-green-600" />
            <span>Excel</span>
          </div>
          <div className="floating-card card-3 cursor-target">
            <Award className="h-8 w-8 text-purple-600" />
            <span>PowerPoint</span>
          </div>
        </div>
      </section>

      <section className="features-section">
        <h2 className="section-title cursor-target">Pourquoi choisir notre plateforme ?</h2>
        
        <div className="features-grid">
          <div className="feature-card cursor-target">
            <div className="feature-icon">
              <BookOpen className="h-10 w-10" />
            </div>
            <h3>Cours interactifs</h3>
            <p>Des leçons structurées avec des exercices pratiques pour chaque module Microsoft Office</p>
          </div>

          <div className="feature-card cursor-target">
            <div className="feature-icon">
              <Award className="h-10 w-10" />
            </div>
            <h3>Examens blancs</h3>
            <p>Préparez-vous avec des examens simulés dans des conditions réelles de certification</p>
          </div>

          <div className="feature-card cursor-target">
            <div className="feature-icon">
              <TrendingUp className="h-10 w-10" />
            </div>
            <h3>Suivi de progression</h3>
            <p>Suivez votre évolution avec des statistiques détaillées et des badges de réussite</p>
          </div>

          <div className="feature-card cursor-target">
            <div className="feature-icon">
              <Users className="h-10 w-10" />
            </div>
            <h3>Gestion de classes</h3>
            <p>Les formateurs peuvent créer des classes et suivre les performances de leurs apprenants</p>
          </div>
        </div>
      </section>

      <section className="modules-section">
        <h2 className="section-title cursor-target">Modules disponibles</h2>
        
        <div className="modules-grid">
          <div className="module-card word cursor-target">
            <div className="module-header">
              <BookOpen className="h-12 w-12" />
              <h3>Microsoft Word</h3>
            </div>
            <ul className="module-features">
              <li>Mise en forme de documents</li>
              <li>Tableaux et graphiques</li>
              <li>Publipostage</li>
              <li>Styles et modèles</li>
            </ul>
          </div>

          <div className="module-card excel cursor-target">
            <div className="module-header">
              <TrendingUp className="h-12 w-12" />
              <h3>Microsoft Excel</h3>
            </div>
            <ul className="module-features">
              <li>Formules et fonctions</li>
              <li>Tableaux croisés dynamiques</li>
              <li>Graphiques avancés</li>
              <li>Analyse de données</li>
            </ul>
          </div>

          <div className="module-card powerpoint cursor-target">
            <div className="module-header">
              <Award className="h-12 w-12" />
              <h3>Microsoft PowerPoint</h3>
            </div>
            <ul className="module-features">
              <li>Conception de diapositives</li>
              <li>Animations et transitions</li>
              <li>Multimédia</li>
              <li>Présentations interactives</li>
            </ul>
          </div>
        </div>
      </section>

      <section className="cta-section">
        <div className="cta-content">
          <h2 className="cursor-target">Prêt à commencer votre formation ?</h2>
          <p className="cursor-target">Rejoignez des centaines d'apprenants qui préparent leur certification MOS</p>
          <Link to="/register" className="cta-button large cursor-target">
            Créer un compte gratuit
            <ArrowRight className="ml-2 h-6 w-6" />
          </Link>
        </div>
      </section>

      <footer className="landing-footer">
        <p>© 2026 Plateforme MOS OFPPT - Tous droits réservés</p>
      </footer>
    </div>
  )
}
