/**
 * Nova Leads - Admin JavaScript
 */

(function($) {
    'use strict';
    
    /**
     * Inicializar Dashboard
     */
    function initDashboard() {
        if ($('.nova-admin-dashboard').length === 0) return;
        
        // Initialize charts
        initCharts();
        
        // Auto-refresh stats
        setInterval(refreshStats, 60000);
    }
    
    /**
     * Inicializar Charts
     */
    function initCharts() {
        // Leads por mes
        var monthlyCtx = document.getElementById('nova-monthly-chart');
        if (monthlyCtx && typeof Chart !== 'undefined') {
            var monthlyData = JSON.parse(monthlyCtx.dataset.stats || '{}');
            
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: Object.keys(monthlyData),
                    datasets: [{
                        label: 'Leads',
                        data: Object.values(monthlyData),
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
        
        // Leads por estado
        var statusCtx = document.getElementById('nova-status-chart');
        if (statusCtx && typeof Chart !== 'undefined') {
            var statusData = JSON.parse(statusCtx.dataset.stats || '{}');
            
            var colors = {
                'Nuevo': '#3b82f6',
                'Contactado': '#f59e0b',
                'Negociación': '#8b5cf6',
                'Propuesta': '#ec4899',
                'Convertido': '#10b981',
                'Perdido': '#ef4444'
            };
            
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(statusData),
                    datasets: [{
                        data: Object.values(statusData),
                        backgroundColor: Object.keys(statusData).map(function(k) {
                            return colors[k] || '#6b7280';
                        })
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
        
        // Leads por dispositivo
        var deviceCtx = document.getElementById('nova-device-chart');
        if (deviceCtx && typeof Chart !== 'undefined') {
            var deviceData = JSON.parse(deviceCtx.dataset.stats || '{}');
            
            new Chart(deviceCtx, {
                type: 'pie',
                data: {
                    labels: Object.keys(deviceData),
                    datasets: [{
                        data: Object.values(deviceData),
                        backgroundColor: ['#2563eb', '#10b981', '#f59e0b']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    }
    
    /**
     * Refrescar estadísticas
     */
    function refreshStats() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'nova_get_stats',
                nonce: novaLeadsAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    updateStatCards(response.data);
                }
            }
        });
    }
    
    /**
     * Actualizar tarjetas de estadísticas
     */
    function updateStatCards(data) {
        if (data.total !== undefined) {
            $('.nova-stat-card[data-stat="total"] .nova-stat-card__value').text(data.total);
        }
        if (data.this_month !== undefined) {
            $('.nova-stat-card[data-stat="month"] .nova-stat-card__value').text(data.this_month);
        }
        if (data.conversion_rate !== undefined) {
            $('.nova-stat-card[data-stat="conversion"] .nova-stat-card__value').text(data.conversion_rate + '%');
        }
    }
    
    /**
     * Actualizar estado del lead
     */
    function initStatusUpdate() {
        $(document).on('change', '.nova-status-select', function() {
            var $select = $(this);
            var leadId = $select.data('lead-id');
            var status = $select.val();
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'nova_update_status',
                    nonce: novaLeadsAdmin.nonce,
                    lead_id: leadId,
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        showNotice('Estado actualizado', 'success');
                    } else {
                        showNotice('Error al actualizar', 'error');
                    }
                }
            });
        });
    }
    
    /**
     * Gestión de notas
     */
    function initNotes() {
        // Add note
        $(document).on('click', '.nova-add-note-btn', function() {
            var $container = $(this).closest('.nova-notes-section');
            var $textarea = $container.find('.nova-note-input');
            var leadId = $container.data('lead-id');
            var note = $textarea.val().trim();
            
            if (!note) return;
            
            var $btn = $(this);
            $btn.prop('disabled', true);
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'nova_add_note',
                    nonce: novaLeadsAdmin.nonce,
                    lead_id: leadId,
                    note: note
                },
                success: function(response) {
                    if (response.success) {
                        var noteHtml = '<div class="nova-note" data-id="' + response.data.id + '">' +
                            '<div class="nova-note__header">' +
                                '<span class="nova-note__author">' + response.data.author + '</span>' +
                                '<span class="nova-note__date">' + response.data.date + '</span>' +
                                '<button class="nova-note__delete" title="Eliminar">&times;</button>' +
                            '</div>' +
                            '<div class="nova-note__content">' + escapeHtml(response.data.note) + '</div>' +
                        '</div>';
                        
                        $container.find('.nova-notes-list').prepend(noteHtml);
                        $textarea.val('');
                        showNotice('Nota añadida', 'success');
                    }
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        });
        
        // Delete note
        $(document).on('click', '.nova-note__delete', function() {
            if (!confirm('¿Eliminar esta nota?')) return;
            
            var $note = $(this).closest('.nova-note');
            var noteId = $note.data('id');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'nova_delete_note',
                    nonce: novaLeadsAdmin.nonce,
                    note_id: noteId
                },
                success: function(response) {
                    if (response.success) {
                        $note.fadeOut(300, function() {
                            $(this).remove();
                        });
                    }
                }
            });
        });
    }
    
    /**
     * Webhook settings
     */
    function initWebhook() {
        $('#nova-save-webhook').on('click', function() {
            var url = $('#nova-webhook-url').val();
            var $btn = $(this);
            
            $btn.prop('disabled', true).text('Guardando...');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'nova_save_webhook',
                    nonce: novaLeadsAdmin.nonce,
                    webhook_url: url
                },
                success: function(response) {
                    if (response.success) {
                        showNotice('Webhook guardado', 'success');
                    } else {
                        showNotice('Error al guardar', 'error');
                    }
                },
                complete: function() {
                    $btn.prop('disabled', false).text('Guardar');
                }
            });
        });
    }
    
    /**
     * Mostrar notificación
     */
    function showNotice(message, type) {
        var $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
        $('.wrap h1').first().after($notice);
        
        setTimeout(function() {
            $notice.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }
    
    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Initialize
    $(document).ready(function() {
        initDashboard();
        initStatusUpdate();
        initNotes();
        initWebhook();
    });
    
})(jQuery);
