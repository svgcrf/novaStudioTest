<?php
/**
 * Formulario de leads frontend
 */

if (!defined('ABSPATH')) {
    exit;
}

class Nova_Leads_Form {
    
    private static $instance = null;
    
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        add_shortcode('nova_lead_form', array($this, 'render_shortcode'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
    }
    
    /**
     * Assets del formulario
     */
    public function enqueue_assets() {
        wp_enqueue_style(
            'nova-leads-form',
            NOVA_LEADS_URL . 'assets/css/form.css',
            array(),
            NOVA_LEADS_VERSION
        );
        
        wp_enqueue_script(
            'nova-leads-form',
            NOVA_LEADS_URL . 'assets/js/form.js',
            array('jquery'),
            NOVA_LEADS_VERSION,
            true
        );
        
        wp_localize_script('nova-leads-form', 'novaLeads', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nova_leads_submit'),
            'messages' => array(
                'sending' => 'Enviando...',
                'success' => '¡Gracias! Te contactaremos pronto.',
                'error' => 'Hubo un error. Por favor intenta de nuevo.',
                'required' => 'Este campo es requerido',
                'invalid_email' => 'Email no válido',
                'invalid_phone' => 'Teléfono no válido',
            ),
        ));
    }
    
    /**
     * Shortcode [nova_lead_form]
     */
    public function render_shortcode($atts) {
        $atts = shortcode_atts(array(
            'type' => 'full',          // full, simple, cta, exit
            'title' => '',
            'description' => '',
            'button_text' => 'Solicitar Información',
            'source' => 'shortcode',
            'show_company' => 'yes',
            'show_budget' => 'yes',
            'show_urgency' => 'yes',
            'show_message' => 'yes',
            'class' => '',
        ), $atts, 'nova_lead_form');
        
        ob_start();
        
        $form_class = 'nova-lead-form nova-lead-form--' . esc_attr($atts['type']);
        if (!empty($atts['class'])) {
            $form_class .= ' ' . esc_attr($atts['class']);
        }
        ?>
        <div class="<?php echo $form_class; ?>">
            <?php if (!empty($atts['title'])) : ?>
                <h3 class="nova-lead-form__title"><?php echo esc_html($atts['title']); ?></h3>
            <?php endif; ?>
            
            <?php if (!empty($atts['description'])) : ?>
                <p class="nova-lead-form__description"><?php echo esc_html($atts['description']); ?></p>
            <?php endif; ?>
            
            <form class="nova-lead-form__form" data-source="<?php echo esc_attr($atts['source']); ?>">
                
                <!-- Honeypot anti-spam -->
                <div class="nova-hp" aria-hidden="true">
                    <input type="text" name="website_url" tabindex="-1" autocomplete="off">
                </div>
                
                <?php if ($atts['type'] === 'full') : ?>
                    <!-- Formulario completo con pasos -->
                    <?php echo $this->render_full_form($atts); ?>
                <?php elseif ($atts['type'] === 'simple') : ?>
                    <!-- Formulario simple -->
                    <?php echo $this->render_simple_form($atts); ?>
                <?php elseif ($atts['type'] === 'cta') : ?>
                    <!-- Formulario CTA compacto -->
                    <?php echo $this->render_cta_form($atts); ?>
                <?php elseif ($atts['type'] === 'exit') : ?>
                    <!-- Formulario exit popup -->
                    <?php echo $this->render_exit_form($atts); ?>
                <?php endif; ?>
                
                <!-- Hidden tracking fields -->
                <input type="hidden" name="time_on_page" value="0" class="nova-time-on-page">
                
                <div class="nova-lead-form__submit">
                    <button type="submit" class="nova-btn nova-btn--primary">
                        <span class="nova-btn__text"><?php echo esc_html($atts['button_text']); ?></span>
                        <span class="nova-btn__loading">
                            <svg class="nova-spinner" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="60 30"></circle>
                            </svg>
                        </span>
                    </button>
                </div>
                
                <div class="nova-lead-form__message"></div>
                
                <p class="nova-lead-form__privacy">
                    <small>Al enviar aceptas nuestra <a href="/politica-privacidad" target="_blank">política de privacidad</a></small>
                </p>
            </form>
            
            <!-- Mensaje de éxito -->
            <div class="nova-lead-form__success" style="display: none;">
                <div class="nova-success-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <h4>¡Mensaje Enviado!</h4>
                <p>Nos pondremos en contacto contigo muy pronto.</p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Formulario completo con pasos
     */
    private function render_full_form($atts) {
        ob_start();
        ?>
        <div class="nova-form-steps">
            <div class="nova-steps-indicator">
                <div class="nova-step active" data-step="1">
                    <span class="nova-step__number">1</span>
                    <span class="nova-step__label">Contacto</span>
                </div>
                <div class="nova-step" data-step="2">
                    <span class="nova-step__number">2</span>
                    <span class="nova-step__label">Empresa</span>
                </div>
                <div class="nova-step" data-step="3">
                    <span class="nova-step__number">3</span>
                    <span class="nova-step__label">Proyecto</span>
                </div>
            </div>
            
            <!-- Paso 1: Datos de contacto -->
            <div class="nova-form-step active" data-step="1">
                <div class="nova-form-row">
                    <div class="nova-form-group">
                        <label for="nova-name">Nombre completo <span class="required">*</span></label>
                        <input type="text" id="nova-name" name="name" required placeholder="Tu nombre">
                    </div>
                </div>
                
                <div class="nova-form-row nova-form-row--2col">
                    <div class="nova-form-group">
                        <label for="nova-email">Email <span class="required">*</span></label>
                        <input type="email" id="nova-email" name="email" required placeholder="tu@email.com">
                    </div>
                    <div class="nova-form-group">
                        <label for="nova-phone">Teléfono</label>
                        <input type="tel" id="nova-phone" name="phone" placeholder="+34 600 000 000">
                    </div>
                </div>
                
                <div class="nova-form-nav">
                    <button type="button" class="nova-btn nova-btn--secondary nova-next-step">
                        Siguiente
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Paso 2: Datos de empresa -->
            <div class="nova-form-step" data-step="2">
                <?php if ($atts['show_company'] === 'yes') : ?>
                <div class="nova-form-row nova-form-row--2col">
                    <div class="nova-form-group">
                        <label for="nova-company">Empresa</label>
                        <input type="text" id="nova-company" name="company" placeholder="Nombre de tu empresa">
                    </div>
                    <div class="nova-form-group">
                        <label for="nova-sector">Sector</label>
                        <select id="nova-sector" name="sector">
                            <option value="">Selecciona...</option>
                            <option value="tecnologia">Tecnología</option>
                            <option value="ecommerce">E-commerce</option>
                            <option value="servicios">Servicios profesionales</option>
                            <option value="salud">Salud</option>
                            <option value="educacion">Educación</option>
                            <option value="finanzas">Finanzas</option>
                            <option value="inmobiliario">Inmobiliario</option>
                            <option value="alimentacion">Alimentación</option>
                            <option value="turismo">Turismo</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                </div>
                
                <div class="nova-form-row">
                    <div class="nova-form-group">
                        <label for="nova-company-size">Tamaño de empresa</label>
                        <select id="nova-company-size" name="company_size">
                            <option value="">Selecciona...</option>
                            <option value="autonomo">Autónomo</option>
                            <option value="1-10">1-10 empleados</option>
                            <option value="11-50">11-50 empleados</option>
                            <option value="51-200">51-200 empleados</option>
                            <option value="200+">Más de 200</option>
                        </select>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="nova-form-nav">
                    <button type="button" class="nova-btn nova-btn--outline nova-prev-step">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                        Anterior
                    </button>
                    <button type="button" class="nova-btn nova-btn--secondary nova-next-step">
                        Siguiente
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Paso 3: Proyecto -->
            <div class="nova-form-step" data-step="3">
                <div class="nova-form-row">
                    <div class="nova-form-group">
                        <label for="nova-service">Servicio de interés</label>
                        <select id="nova-service" name="service">
                            <option value="">Selecciona...</option>
                            <option value="web-corporativa">Diseño Web Corporativo</option>
                            <option value="ecommerce">E-commerce / Tienda Online</option>
                            <option value="aplicacion">Aplicación Web/Móvil</option>
                            <option value="branding">Branding e Identidad</option>
                            <option value="marketing">Marketing Digital</option>
                            <option value="consultoria">Consultoría Tecnológica</option>
                        </select>
                    </div>
                </div>
                
                <?php if ($atts['show_budget'] === 'yes') : ?>
                <div class="nova-form-row nova-form-row--2col">
                    <div class="nova-form-group">
                        <label for="nova-budget">Presupuesto estimado</label>
                        <select id="nova-budget" name="budget">
                            <option value="">Selecciona...</option>
                            <option value="menos_5k">Menos de 5.000€</option>
                            <option value="5k_15k">5.000€ - 15.000€</option>
                            <option value="15k_30k">15.000€ - 30.000€</option>
                            <option value="30k_50k">30.000€ - 50.000€</option>
                            <option value="mas_50k">Más de 50.000€</option>
                        </select>
                    </div>
                    
                    <?php if ($atts['show_urgency'] === 'yes') : ?>
                    <div class="nova-form-group">
                        <label for="nova-urgency">¿Cuándo quieres empezar?</label>
                        <select id="nova-urgency" name="urgency">
                            <option value="">Selecciona...</option>
                            <option value="inmediato">Lo antes posible</option>
                            <option value="1_mes">En el próximo mes</option>
                            <option value="3_meses">En 1-3 meses</option>
                            <option value="6_meses">En 3-6 meses</option>
                            <option value="explorando">Solo explorando opciones</option>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php if ($atts['show_message'] === 'yes') : ?>
                <div class="nova-form-row">
                    <div class="nova-form-group">
                        <label for="nova-message">Cuéntanos sobre tu proyecto</label>
                        <textarea id="nova-message" name="message" rows="4" placeholder="¿Qué objetivos tienes? ¿Cuáles son los principales retos que enfrentas?"></textarea>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="nova-form-nav">
                    <button type="button" class="nova-btn nova-btn--outline nova-prev-step">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                        Anterior
                    </button>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Formulario simple
     */
    private function render_simple_form($atts) {
        ob_start();
        ?>
        <div class="nova-form-row">
            <div class="nova-form-group">
                <label for="nova-name">Nombre <span class="required">*</span></label>
                <input type="text" id="nova-name" name="name" required placeholder="Tu nombre">
            </div>
        </div>
        
        <div class="nova-form-row nova-form-row--2col">
            <div class="nova-form-group">
                <label for="nova-email">Email <span class="required">*</span></label>
                <input type="email" id="nova-email" name="email" required placeholder="tu@email.com">
            </div>
            <div class="nova-form-group">
                <label for="nova-phone">Teléfono</label>
                <input type="tel" id="nova-phone" name="phone" placeholder="+34 600 000 000">
            </div>
        </div>
        
        <div class="nova-form-row">
            <div class="nova-form-group">
                <label for="nova-service">Servicio</label>
                <select id="nova-service" name="service">
                    <option value="">¿En qué podemos ayudarte?</option>
                    <option value="web-corporativa">Diseño Web</option>
                    <option value="ecommerce">E-commerce</option>
                    <option value="aplicacion">Aplicación</option>
                    <option value="branding">Branding</option>
                    <option value="marketing">Marketing</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
        </div>
        
        <div class="nova-form-row">
            <div class="nova-form-group">
                <label for="nova-message">Mensaje</label>
                <textarea id="nova-message" name="message" rows="3" placeholder="Cuéntanos brevemente sobre tu proyecto..."></textarea>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Formulario CTA compacto
     */
    private function render_cta_form($atts) {
        ob_start();
        ?>
        <div class="nova-form-row nova-form-row--inline">
            <div class="nova-form-group">
                <input type="text" name="name" required placeholder="Tu nombre">
            </div>
            <div class="nova-form-group">
                <input type="email" name="email" required placeholder="tu@email.com">
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Formulario exit popup
     */
    private function render_exit_form($atts) {
        ob_start();
        ?>
        <div class="nova-form-row">
            <div class="nova-form-group">
                <input type="text" name="name" required placeholder="Tu nombre">
            </div>
        </div>
        <div class="nova-form-row">
            <div class="nova-form-group">
                <input type="email" name="email" required placeholder="tu@email.com">
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render inline para el landing page
     */
    public static function render_inline($type = 'simple', $options = array()) {
        $instance = self::instance();
        
        $defaults = array(
            'type' => $type,
            'title' => '',
            'description' => '',
            'button_text' => 'Enviar',
            'source' => 'inline',
            'show_company' => 'yes',
            'show_budget' => 'yes',
            'show_urgency' => 'yes',
            'show_message' => 'yes',
            'class' => '',
        );
        
        $atts = array_merge($defaults, $options);
        
        return $instance->render_shortcode($atts);
    }
}
