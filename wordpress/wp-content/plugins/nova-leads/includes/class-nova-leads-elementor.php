<?php
/**
 * Widget de Elementor para Nova Leads
 */

if (!defined('ABSPATH')) {
    exit;
}

class Nova_Leads_Elementor {
    
    private static $instance = null;
    
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        add_action('elementor/widgets/register', array($this, 'register_widgets'));
        add_action('elementor/elements/categories_registered', array($this, 'add_category'));
    }
    
    /**
     * Añadir categoría Nova
     */
    public function add_category($elements_manager) {
        $elements_manager->add_category('nova-studio', array(
            'title' => 'Nova Studio',
            'icon' => 'fa fa-plug',
        ));
    }
    
    /**
     * Registrar widgets
     */
    public function register_widgets($widgets_manager) {
        require_once NOVA_LEADS_PATH . 'includes/elementor/class-nova-form-widget.php';
        $widgets_manager->register(new Nova_Form_Widget());
    }
}

// Widget class
if (class_exists('\Elementor\Widget_Base')) {
    
    class Nova_Form_Widget extends \Elementor\Widget_Base {
        
        public function get_name() {
            return 'nova_lead_form';
        }
        
        public function get_title() {
            return 'Nova Lead Form';
        }
        
        public function get_icon() {
            return 'eicon-form-horizontal';
        }
        
        public function get_categories() {
            return array('nova-studio');
        }
        
        public function get_keywords() {
            return array('form', 'contact', 'lead', 'nova');
        }
        
        protected function register_controls() {
            
            // Sección de contenido
            $this->start_controls_section('content_section', array(
                'label' => 'Contenido',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ));
            
            $this->add_control('form_type', array(
                'label' => 'Tipo de formulario',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'simple',
                'options' => array(
                    'full' => 'Completo (con pasos)',
                    'simple' => 'Simple',
                    'cta' => 'CTA Compacto',
                ),
            ));
            
            $this->add_control('form_title', array(
                'label' => 'Título',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => 'Opcional',
            ));
            
            $this->add_control('form_description', array(
                'label' => 'Descripción',
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '',
                'placeholder' => 'Opcional',
            ));
            
            $this->add_control('button_text', array(
                'label' => 'Texto del botón',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Solicitar Información',
            ));
            
            $this->add_control('form_source', array(
                'label' => 'Identificador de origen',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'elementor_widget',
                'description' => 'Para tracking interno',
            ));
            
            $this->end_controls_section();
            
            // Campos a mostrar
            $this->start_controls_section('fields_section', array(
                'label' => 'Campos',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ));
            
            $this->add_control('show_company', array(
                'label' => 'Mostrar campos de empresa',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Sí',
                'label_off' => 'No',
                'default' => 'yes',
            ));
            
            $this->add_control('show_budget', array(
                'label' => 'Mostrar presupuesto',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Sí',
                'label_off' => 'No',
                'default' => 'yes',
            ));
            
            $this->add_control('show_urgency', array(
                'label' => 'Mostrar urgencia',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Sí',
                'label_off' => 'No',
                'default' => 'yes',
            ));
            
            $this->add_control('show_message', array(
                'label' => 'Mostrar mensaje',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Sí',
                'label_off' => 'No',
                'default' => 'yes',
            ));
            
            $this->end_controls_section();
            
            // Estilos
            $this->start_controls_section('style_section', array(
                'label' => 'Estilos',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ));
            
            $this->add_control('button_color', array(
                'label' => 'Color del botón',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#2563eb',
                'selectors' => array(
                    '{{WRAPPER}} .nova-btn--primary' => 'background-color: {{VALUE}};',
                ),
            ));
            
            $this->add_control('button_hover_color', array(
                'label' => 'Color hover del botón',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#1d4ed8',
                'selectors' => array(
                    '{{WRAPPER}} .nova-btn--primary:hover' => 'background-color: {{VALUE}};',
                ),
            ));
            
            $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), array(
                'name' => 'title_typography',
                'label' => 'Tipografía del título',
                'selector' => '{{WRAPPER}} .nova-lead-form__title',
            ));
            
            $this->end_controls_section();
        }
        
        protected function render() {
            $settings = $this->get_settings_for_display();
            
            echo do_shortcode(sprintf(
                '[nova_lead_form type="%s" title="%s" description="%s" button_text="%s" source="%s" show_company="%s" show_budget="%s" show_urgency="%s" show_message="%s"]',
                esc_attr($settings['form_type']),
                esc_attr($settings['form_title']),
                esc_attr($settings['form_description']),
                esc_attr($settings['button_text']),
                esc_attr($settings['form_source']),
                $settings['show_company'] === 'yes' ? 'yes' : 'no',
                $settings['show_budget'] === 'yes' ? 'yes' : 'no',
                $settings['show_urgency'] === 'yes' ? 'yes' : 'no',
                $settings['show_message'] === 'yes' ? 'yes' : 'no'
            ));
        }
    }
}
