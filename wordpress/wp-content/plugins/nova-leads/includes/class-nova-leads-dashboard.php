<?php
/**
 * Dashboard de Métricas
 */

if (!defined('ABSPATH')) {
    exit;
}

class Nova_Leads_Dashboard {
    
    private static $instance = null;
    
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        // Dashboard widget en el admin principal
        add_action('wp_dashboard_setup', array($this, 'add_dashboard_widget'));
    }
    
    public function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'nova_leads_dashboard_widget',
            '📊 Nova Leads - Resumen',
            array($this, 'render_dashboard_widget')
        );
    }
    
    public function render_dashboard_widget() {
        $stats = Nova_Leads_Admin::get_stats();
        ?>
        <div class="nova-widget-stats">
            <div class="nova-widget-stat">
                <span class="stat-number"><?php echo esc_html($stats['total']); ?></span>
                <span class="stat-label">Total Leads</span>
            </div>
            <div class="nova-widget-stat">
                <span class="stat-number"><?php echo esc_html($stats['this_month']); ?></span>
                <span class="stat-label">Este Mes</span>
            </div>
            <div class="nova-widget-stat">
                <span class="stat-number"><?php echo esc_html($stats['conversion_rate']); ?>%</span>
                <span class="stat-label">Tasa Conv.</span>
            </div>
        </div>
        
        <?php if (!empty($stats['recent'])) : ?>
            <h4 style="margin: 15px 0 10px;">Últimos Leads</h4>
            <ul style="margin: 0;">
                <?php foreach ($stats['recent'] as $lead) : ?>
                    <li style="margin-bottom: 5px;">
                        <a href="<?php echo get_edit_post_link($lead->ID); ?>">
                            <?php echo esc_html($lead->post_title); ?>
                        </a>
                        <span style="color: #666; font-size: 12px;">
                            - <?php echo human_time_diff(strtotime($lead->post_date), current_time('timestamp')); ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        
        <p style="margin-top: 15px; margin-bottom: 0;">
            <a href="<?php echo admin_url('admin.php?page=nova-leads'); ?>" class="button">
                Ver Dashboard Completo →
            </a>
        </p>
        
        <style>
            .nova-widget-stats {
                display: flex;
                gap: 20px;
                margin-bottom: 15px;
                padding-bottom: 15px;
                border-bottom: 1px solid #eee;
            }
            .nova-widget-stat {
                flex: 1;
                text-align: center;
            }
            .nova-widget-stat .stat-number {
                display: block;
                font-size: 28px;
                font-weight: 700;
                color: #2563EB;
            }
            .nova-widget-stat .stat-label {
                font-size: 12px;
                color: #666;
            }
        </style>
        <?php
    }
}
