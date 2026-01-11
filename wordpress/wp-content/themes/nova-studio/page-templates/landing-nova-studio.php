<?php
/**
 * Template Name: Nova Studio Landing
 * Description: Landing page completa de Nova Studio
 * 
 * @package Nova_Studio
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div id="nova-landing-page" class="nova-landing-page">
    
    <!-- SECCIÓN HERO -->
    <section class="nova-hero">
        <div class="nova-container">
            <div class="nova-hero-grid">
                <div class="nova-hero-content">
                    <h1 class="nova-hero-title animate-slide-up">
                        Diseñamos sitios web que convierten visitantes en clientes
                    </h1>
                    <p class="nova-hero-subtitle animate-slide-up delay-1">
                        Creamos experiencias digitales memorables que impulsan tu negocio con diseño centrado en conversión y tecnología de vanguardia.
                    </p>
                    <div class="nova-hero-cta animate-slide-up delay-2">
                        <a href="<?php echo home_url('/solicitar-presupuesto/'); ?>" class="nova-btn nova-btn-primary nova-btn-glow">
                            <span>Solicita tu presupuesto</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </a>
                        <a href="#servicios" class="nova-btn nova-btn-secondary">Ver nuestros servicios</a>
                    </div>
                    <div class="nova-hero-stats animate-fade-in delay-3">
                        <div class="stat-item">
                            <span class="stat-number nova-counter" data-target="120" data-suffix="+">0+</span>
                            <span class="stat-label">Proyectos completados</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number nova-counter" data-target="98" data-suffix="%">0%</span>
                            <span class="stat-label">Satisfacción del cliente</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">5 años</span>
                            <span class="stat-label">De experiencia</span>
                        </div>
                    </div>
                </div>
                <div class="nova-hero-image animate-fade-in delay-2">
                    <div class="hero-image-wrapper">
                        <div class="floating-card card-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                            <span>Diseño UI/UX</span>
                        </div>
                        <div class="floating-card card-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                            <span>Desarrollo Web</span>
                        </div>
                        <div class="floating-card card-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
                            <span>Estrategia Digital</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN SERVICIOS -->
    <section id="servicios" class="nova-services">
        <div class="nova-container">
            <div class="section-header">
                <span class="section-label">¿Qué hacemos?</span>
                <h2 class="section-title">Nuestros Servicios</h2>
                <p class="section-subtitle">Soluciones digitales completas que impulsan tu negocio hacia el éxito</p>
            </div>
            
            <div class="services-grid">
                <!-- Servicio 1: Diseño Web -->
                <div class="service-card" data-aos="fade-up" data-aos-delay="0">
                    <div class="service-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                            <line x1="8" y1="21" x2="16" y2="21"></line>
                            <line x1="12" y1="17" x2="12" y2="21"></line>
                        </svg>
                    </div>
                    <h3 class="service-title">Diseño Web UI/UX</h3>
                    <p class="service-description">
                        Creamos interfaces intuitivas y atractivas que mejoran la experiencia del usuario y aumentan las conversiones. Diseño centrado en el usuario con metodología Design Thinking.
                    </p>
                    <ul class="service-features">
                        <li><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> Diseño responsive</li>
                        <li><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> Prototipado interactivo</li>
                        <li><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> Testing de usabilidad</li>
                    </ul>
                </div>

                <!-- Servicio 2: Desarrollo Web -->
                <div class="service-card featured" data-aos="fade-up" data-aos-delay="100">
                    <div class="featured-badge">Más popular</div>
                    <div class="service-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="16 18 22 12 16 6"></polyline>
                            <polyline points="8 6 2 12 8 18"></polyline>
                        </svg>
                    </div>
                    <h3 class="service-title">Desarrollo Web</h3>
                    <p class="service-description">
                        Desarrollamos sitios web rápidos, seguros y escalables con las tecnologías más modernas. WordPress, React, Vue, y desarrollos personalizados según tus necesidades.
                    </p>
                    <ul class="service-features">
                        <li><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> WordPress & E-commerce</li>
                        <li><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> React & Vue.js</li>
                        <li><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> Optimización y rendimiento</li>
                    </ul>
                </div>

                <!-- Servicio 3: Estrategia Digital -->
                <div class="service-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="service-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                    <h3 class="service-title">Estrategia Digital</h3>
                    <p class="service-description">
                        Impulsamos tu visibilidad online con estrategias de marketing digital efectivas. SEO, SEM, Google Ads, Analytics y optimización de conversión para resultados medibles.
                    </p>
                    <ul class="service-features">
                        <li><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> SEO & SEM</li>
                        <li><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> Google Ads & Meta Ads</li>
                        <li><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> Analytics & optimización</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN DIFERENCIAL -->
    <section class="nova-diferencial">
        <div class="nova-container">
            <div class="diferencial-grid">
                <div class="diferencial-image" data-aos="fade-right">
                    <div class="image-wrapper">
                        <div class="gradient-overlay"></div>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 500" class="abstract-illustration">
                            <circle cx="250" cy="250" r="200" fill="#2563EB" opacity="0.1"/>
                            <circle cx="250" cy="250" r="150" fill="#F59E0B" opacity="0.1"/>
                            <path d="M250 100 L400 250 L250 400 L100 250 Z" fill="#2563EB" opacity="0.2"/>
                        </svg>
                    </div>
                </div>
                <div class="diferencial-content" data-aos="fade-left">
                    <span class="section-label">¿Por qué Nova Studio?</span>
                    <h2 class="section-title">Tu partner digital de confianza</h2>
                    <p class="section-intro">
                        No solo creamos sitios web, construimos soluciones digitales que impulsan resultados reales para tu negocio.
                    </p>
                    
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                            </div>
                            <div class="feature-text">
                                <h4>Enfoque en ROI y resultados medibles</h4>
                                <p>Cada proyecto está orientado a generar retorno de inversión con métricas claras y objetivos alcanzables.</p>
                            </div>
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
                            </div>
                            <div class="feature-text">
                                <h4>Diseño centrado en conversión</h4>
                                <p>Aplicamos principios de psicología del usuario y CRO para maximizar tus conversiones.</p>
                            </div>
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            </div>
                            <div class="feature-text">
                                <h4>Soporte continuo post-lanzamiento</h4>
                                <p>No te dejamos solo después del lanzamiento. Ofrecemos mantenimiento y optimización continua.</p>
                            </div>
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            </div>
                            <div class="feature-text">
                                <h4>Entrega a tiempo y sin sorpresas</h4>
                                <p>Metodología ágil con entregas iterativas y comunicación transparente en cada fase del proyecto.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN PROCESO -->
    <section class="nova-proceso">
        <div class="nova-container">
            <div class="section-header">
                <span class="section-label">¿Cómo trabajamos?</span>
                <h2 class="section-title">Nuestro Proceso</h2>
                <p class="section-subtitle">Un enfoque probado que garantiza resultados excepcionales</p>
            </div>

            <div class="proceso-timeline">
                <!-- Paso 1 -->
                <div class="proceso-step" data-aos="fade-up" data-aos-delay="0">
                    <div class="step-number">01</div>
                    <div class="step-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>
                    </div>
                    <h3 class="step-title">Descubrir</h3>
                    <p class="step-description">
                        Entendemos tu negocio, objetivos y audiencia. Investigamos a tu competencia y definimos la estrategia perfecta.
                    </p>
                    <div class="step-details">
                        <span>• Reunión inicial</span>
                        <span>• Análisis de mercado</span>
                        <span>• Definición de objetivos</span>
                    </div>
                </div>

                <!-- Conector -->
                <div class="timeline-connector" data-aos="fade-right" data-aos-delay="100"></div>

                <!-- Paso 2 -->
                <div class="proceso-step" data-aos="fade-up" data-aos-delay="100">
                    <div class="step-number">02</div>
                    <div class="step-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                    </div>
                    <h3 class="step-title">Diseñar</h3>
                    <p class="step-description">
                        Creamos wireframes y prototipos interactivos. Diseñamos la experiencia visual que encantará a tus usuarios.
                    </p>
                    <div class="step-details">
                        <span>• Wireframes</span>
                        <span>• Diseño UI/UX</span>
                        <span>• Prototipos clickeables</span>
                    </div>
                </div>

                <!-- Conector -->
                <div class="timeline-connector" data-aos="fade-right" data-aos-delay="200"></div>

                <!-- Paso 3 -->
                <div class="proceso-step" data-aos="fade-up" data-aos-delay="200">
                    <div class="step-number">03</div>
                    <div class="step-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                    </div>
                    <h3 class="step-title">Desarrollar</h3>
                    <p class="step-description">
                        Convertimos el diseño en código limpio y optimizado. Desarrollo ágil con entregas iterativas para tu feedback.
                    </p>
                    <div class="step-details">
                        <span>• Desarrollo frontend</span>
                        <span>• Integración backend</span>
                        <span>• Testing QA</span>
                    </div>
                </div>

                <!-- Conector -->
                <div class="timeline-connector" data-aos="fade-right" data-aos-delay="300"></div>

                <!-- Paso 4 -->
                <div class="proceso-step" data-aos="fade-up" data-aos-delay="300">
                    <div class="step-number">04</div>
                    <div class="step-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    </div>
                    <h3 class="step-title">Lanzar</h3>
                    <p class="step-description">
                        Optimizamos, testeamos y lanzamos tu proyecto. Monitoreamos el rendimiento y te acompañamos en el crecimiento.
                    </p>
                    <div class="step-details">
                        <span>• Deploy & optimización</span>
                        <span>• Formación y documentación</span>
                        <span>• Soporte continuo</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN TESTIMONIO -->
    <section class="nova-testimonio">
        <div class="nova-container">
            <div class="testimonio-wrapper" data-aos="zoom-in">
                <div class="quote-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z"/>
                    </svg>
                </div>
                
                <blockquote class="testimonio-quote">
                    "Nova Studio transformó completamente nuestra presencia digital. No solo crearon un sitio web hermoso, sino que implementaron una estrategia que triplicó nuestras conversiones en 3 meses. Su enfoque en resultados y atención al detalle es excepcional."
                </blockquote>
                
                <div class="testimonio-author">
                    <div class="author-avatar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="author-info">
                        <h4 class="author-name">María García</h4>
                        <p class="author-role">CEO, TechStartup</p>
                        <div class="author-rating">
                            <span>★★★★★</span>
                            <span class="rating-text">5.0</span>
                        </div>
                    </div>
                </div>

                <div class="testimonio-stats">
                    <div class="stat">
                        <span class="stat-value">+300%</span>
                        <span class="stat-label">Conversiones</span>
                    </div>
                    <div class="stat">
                        <span class="stat-value">60%</span>
                        <span class="stat-label">Reducción bounce rate</span>
                    </div>
                    <div class="stat">
                        <span class="stat-value">4.8s</span>
                        <span class="stat-label">Tiempo de carga</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN CTA FINAL -->
    <section id="contacto" class="nova-cta-final">
        <div class="nova-container">
            <div class="cta-content" data-aos="fade-up">
                <h2 class="cta-title">¿Listo para transformar tu presencia digital?</h2>
                <p class="cta-subtitle">
                    Agenda una llamada gratuita de 30 minutos y descubre cómo podemos ayudarte a alcanzar tus objetivos
                </p>
                
                <div class="cta-action-wrapper">
                    <a href="<?php echo home_url('/solicitar-presupuesto/'); ?>" class="nova-btn nova-btn-cta-large">
                        <span class="btn-icon">🚀</span>
                        <span class="btn-content">
                            <strong>Solicitar Presupuesto Gratis</strong>
                            <small>Respuesta en menos de 24 horas</small>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </a>
                </div>
                
                <div class="cta-benefits-grid">
                    <div class="benefit-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Consultoría gratuita</span>
                    </div>
                    <div class="benefit-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Sin compromiso</span>
                    </div>
                    <div class="benefit-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Propuesta en 48h</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="nova-footer">
        <div class="nova-container">
            <div class="footer-main">
                <div class="footer-column footer-about">
                    <div class="footer-logo">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logo-nova-studio.svg" alt="Nova Studio" class="footer-logo-img" style="height: 40px; width: auto; filter: brightness(0) invert(1);">
                    </div>
                    <p class="footer-description">
                        Transformamos ideas en experiencias digitales excepcionales que impulsan el crecimiento de tu negocio.
                    </p>
                    <div class="footer-social">
                        <a href="#" aria-label="LinkedIn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                        </a>
                        <a href="#" aria-label="Twitter">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>
                        </a>
                        <a href="#" aria-label="Instagram">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                        </a>
                        <a href="#" aria-label="GitHub">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>
                        </a>
                    </div>
                </div>

                <div class="footer-column">
                    <h4 class="footer-title">Servicios</h4>
                    <ul class="footer-links">
                        <li><a href="#servicios">Diseño Web UI/UX</a></li>
                        <li><a href="#servicios">Desarrollo Web</a></li>
                        <li><a href="#servicios">Estrategia Digital</a></li>
                        <li><a href="#servicios">E-commerce</a></li>
                        <li><a href="#servicios">SEO & Marketing</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h4 class="footer-title">Empresa</h4>
                    <ul class="footer-links">
                        <li><a href="#about">Sobre nosotros</a></li>
                        <li><a href="#proceso">Nuestro proceso</a></li>
                        <li><a href="#portfolio">Portfolio</a></li>
                        <li><a href="#blog">Blog</a></li>
                        <li><a href="#careers">Careers</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h4 class="footer-title">Contacto</h4>
                    <ul class="footer-contact">
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            <a href="mailto:hola@novastudio.com">hola@novastudio.com</a>
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            <a href="tel:+34900000000">+34 900 00 00 00</a>
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            <span>Madrid, España</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="footer-copyright">
                    © 2026 Nova Studio. Todos los derechos reservados.
                </p>
                <ul class="footer-legal">
                    <li><a href="#privacy">Política de Privacidad</a></li>
                    <li><a href="#terms">Términos y Condiciones</a></li>
                    <li><a href="#cookies">Cookies</a></li>
                </ul>
            </div>
        </div>
    </footer>

</div>

<?php
get_footer();
?>
