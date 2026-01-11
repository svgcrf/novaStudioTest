<?php
/**
 * Template: Dashboard de Nova Leads
 */

if (!defined('ABSPATH')) {
    exit;
}

$stats = Nova_Leads_Admin::get_stats();

// Calcular tendencia del mes
$last_month = date('Y-m', strtotime('-1 month'));
$last_month_count = $stats['by_month'][$last_month] ?? 0;
$trend = 0;
if ($last_month_count > 0) {
    $trend = round((($stats['this_month'] - $last_month_count) / $last_month_count) * 100);
}
?>

<div class="wrap nova-admin-dashboard">
    <div class="nova-dashboard-header">
        <h1>
            <span class="dashicons dashicons-chart-bar"></span>
            Nova Leads Dashboard
        </h1>
        <div class="nova-dashboard-actions">
            <a href="<?php echo admin_url('edit.php?post_type=nova_lead'); ?>" class="button">
                Ver Todos los Leads
            </a>
            <a href="<?php echo admin_url('admin.php?page=nova-leads-export'); ?>" class="button button-primary">
                <span class="dashicons dashicons-download" style="margin-top: 3px;"></span>
                Exportar
            </a>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="nova-stats-grid">
        <div class="nova-stat-card" data-stat="total">
            <div class="nova-stat-card__icon blue">
                <span class="dashicons dashicons-groups"></span>
            </div>
            <div class="nova-stat-card__content">
                <div class="nova-stat-card__value"><?php echo number_format($stats['total']); ?></div>
                <div class="nova-stat-card__label">Total Leads</div>
            </div>
        </div>
        
        <div class="nova-stat-card" data-stat="month">
            <div class="nova-stat-card__icon green">
                <span class="dashicons dashicons-calendar-alt"></span>
            </div>
            <div class="nova-stat-card__content">
                <div class="nova-stat-card__value"><?php echo number_format($stats['this_month']); ?></div>
                <div class="nova-stat-card__label">Este Mes</div>
                <?php if ($trend !== 0) : ?>
                <div class="nova-stat-card__trend <?php echo $trend > 0 ? 'up' : 'down'; ?>">
                    <?php echo $trend > 0 ? '↑' : '↓'; ?> <?php echo abs($trend); ?>% vs mes anterior
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="nova-stat-card" data-stat="conversion">
            <div class="nova-stat-card__icon yellow">
                <span class="dashicons dashicons-yes-alt"></span>
            </div>
            <div class="nova-stat-card__content">
                <div class="nova-stat-card__value"><?php echo $stats['conversion_rate']; ?>%</div>
                <div class="nova-stat-card__label">Tasa Conversión</div>
            </div>
        </div>
        
        <div class="nova-stat-card" data-stat="new">
            <div class="nova-stat-card__icon purple">
                <span class="dashicons dashicons-star-filled"></span>
            </div>
            <div class="nova-stat-card__content">
                <div class="nova-stat-card__value"><?php echo number_format($stats['by_status']['Nuevo'] ?? 0); ?></div>
                <div class="nova-stat-card__label">Nuevos sin Gestionar</div>
            </div>
        </div>
    </div>
    
    <!-- Charts -->
    <div class="nova-charts-grid">
        <div class="nova-chart-card">
            <h3>Leads por Mes</h3>
            <div class="nova-chart-container">
                <canvas id="nova-monthly-chart" data-stats='<?php echo json_encode($stats['by_month']); ?>'></canvas>
            </div>
        </div>
        
        <div class="nova-chart-card">
            <h3>Por Estado</h3>
            <div class="nova-chart-container">
                <canvas id="nova-status-chart" data-stats='<?php echo json_encode($stats['by_status']); ?>'></canvas>
            </div>
        </div>
    </div>
    
    <!-- Secondary Stats -->
    <div class="nova-charts-grid">
        <div class="nova-chart-card">
            <h3>Por Servicio</h3>
            <div class="nova-mini-stats">
                <?php foreach ($stats['by_service'] as $service => $count) : ?>
                <div class="nova-mini-stat">
                    <span class="nova-mini-stat__label"><?php echo esc_html($service); ?></span>
                    <span class="nova-mini-stat__value"><?php echo number_format($count); ?></span>
                </div>
                <?php endforeach; ?>
                <?php if (empty($stats['by_service'])) : ?>
                <p style="color: #9ca3af; text-align: center; padding: 20px;">Sin datos aún</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="nova-chart-card">
            <h3>Por Dispositivo</h3>
            <div class="nova-chart-container" style="height: 200px;">
                <canvas id="nova-device-chart" data-stats='<?php echo json_encode($stats['by_device']); ?>'></canvas>
            </div>
        </div>
    </div>
    
    <!-- Budget Distribution -->
    <div class="nova-charts-grid">
        <div class="nova-chart-card">
            <h3>Por Presupuesto</h3>
            <div class="nova-mini-stats">
                <?php 
                $budget_labels = array(
                    'menos_5k' => 'Menos de 5.000€',
                    '5k_15k' => '5.000€ - 15.000€',
                    '15k_30k' => '15.000€ - 30.000€',
                    '30k_50k' => '30.000€ - 50.000€',
                    'mas_50k' => 'Más de 50.000€',
                );
                foreach ($stats['by_budget'] as $budget => $count) : 
                    $label = $budget_labels[$budget] ?? $budget;
                ?>
                <div class="nova-mini-stat">
                    <span class="nova-mini-stat__label"><?php echo esc_html($label); ?></span>
                    <span class="nova-mini-stat__value"><?php echo number_format($count); ?></span>
                </div>
                <?php endforeach; ?>
                <?php if (empty($stats['by_budget'])) : ?>
                <p style="color: #9ca3af; text-align: center; padding: 20px;">Sin datos aún</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="nova-chart-card">
            <h3>Por Fuente</h3>
            <div class="nova-mini-stats">
                <?php foreach ($stats['by_source'] as $source => $count) : ?>
                <div class="nova-mini-stat">
                    <span class="nova-mini-stat__label"><?php echo esc_html($source); ?></span>
                    <span class="nova-mini-stat__value"><?php echo number_format($count); ?></span>
                </div>
                <?php endforeach; ?>
                <?php if (empty($stats['by_source'])) : ?>
                <p style="color: #9ca3af; text-align: center; padding: 20px;">Sin datos aún</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Recent Leads -->
    <div class="nova-recent-leads">
        <h3>Leads Recientes</h3>
        <?php if (!empty($stats['recent'])) : ?>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Servicio</th>
                    <th>Estado</th>
                    <th>Score</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats['recent'] as $lead) : 
                    $status = wp_get_object_terms($lead->ID, 'lead_status', array('fields' => 'slugs'));
                    $status_slug = !empty($status) ? $status[0] : 'nuevo';
                    $status_name = !empty($status) ? wp_get_object_terms($lead->ID, 'lead_status', array('fields' => 'names'))[0] : 'Nuevo';
                    
                    $service = wp_get_object_terms($lead->ID, 'lead_service', array('fields' => 'names'));
                    $service_name = !empty($service) ? $service[0] : '-';
                    
                    $score = get_post_meta($lead->ID, '_lead_score', true) ?: 0;
                    $score_class = $score >= 70 ? 'high' : ($score >= 40 ? 'medium' : 'low');
                ?>
                <tr>
                    <td><?php echo get_the_date('d/m/Y H:i', $lead); ?></td>
                    <td><strong><?php echo esc_html(get_post_meta($lead->ID, '_lead_name', true)); ?></strong></td>
                    <td>
                        <a href="mailto:<?php echo esc_attr(get_post_meta($lead->ID, '_lead_email', true)); ?>">
                            <?php echo esc_html(get_post_meta($lead->ID, '_lead_email', true)); ?>
                        </a>
                    </td>
                    <td><?php echo esc_html($service_name); ?></td>
                    <td><span class="nova-status <?php echo esc_attr($status_slug); ?>"><?php echo esc_html($status_name); ?></span></td>
                    <td><span class="nova-lead-score <?php echo $score_class; ?>"><?php echo $score; ?></span></td>
                    <td>
                        <a href="<?php echo get_edit_post_link($lead->ID); ?>" class="button button-small">Ver</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else : ?>
        <p style="padding: 40px; text-align: center; color: #9ca3af;">
            No hay leads aún. Los nuevos leads aparecerán aquí.
        </p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
