<?php
/**
 * Template Name: Formulario de Contacto Premium
 * Description: Página dedicada con formulario multi-step animado
 * 
 * @package Nova_Studio
 */

if (!defined('ABSPATH')) exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Presupuesto - Nova Studio</title>
    <?php wp_head(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563EB;
            --primary-dark: #1D4ED8;
            --primary-light: #3B82F6;
            --accent: #F59E0B;
            --success: #10B981;
            --dark: #0F172A;
            --gray-900: #1E293B;
            --gray-700: #334155;
            --gray-500: #64748B;
            --gray-300: #CBD5E1;
            --gray-100: #F1F5F9;
            --white: #FFFFFF;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 50%, #0F172A 100%);
            min-height: 100vh;
            color: var(--white);
            overflow-x: hidden;
        }

        /* Background Animation */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }

        .bg-animation .circle {
            position: absolute;
            border-radius: 50%;
            animation: float 20s infinite ease-in-out;
        }

        .bg-animation .circle:nth-child(1) {
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.15) 0%, transparent 70%);
            top: -200px;
            right: -200px;
            animation-delay: 0s;
        }

        .bg-animation .circle:nth-child(2) {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.1) 0%, transparent 70%);
            bottom: -100px;
            left: -100px;
            animation-delay: -5s;
        }

        .bg-animation .circle:nth-child(3) {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: -10s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(30px, -30px) scale(1.05); }
            50% { transform: translate(-20px, 20px) scale(0.95); }
            75% { transform: translate(20px, 10px) scale(1.02); }
        }

        /* Header */
        .form-header {
            position: relative;
            z-index: 10;
            padding: 30px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .form-header .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--white);
        }

        .form-header .logo svg {
            width: 40px;
            height: 40px;
        }

        .form-header .logo-text {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 24px;
            font-weight: 700;
        }

        .form-header .close-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            color: var(--white);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .form-header .close-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(-5px);
        }

        /* Main Container */
        .form-container {
            position: relative;
            z-index: 10;
            max-width: 900px;
            margin: 0 auto;
            padding: 0 30px 60px;
        }

        /* Title Section */
        .form-title-section {
            text-align: center;
            margin-bottom: 50px;
            animation: fadeInUp 0.8s ease;
        }

        .form-title-section h1 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: clamp(32px, 5vw, 48px);
            font-weight: 800;
            margin-bottom: 16px;
            background: linear-gradient(135deg, var(--white) 0%, var(--gray-300) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .form-title-section p {
            font-size: 18px;
            color: var(--gray-500);
            max-width: 500px;
            margin: 0 auto;
        }

        /* Progress Steps */
        .progress-steps {
            display: flex;
            justify-content: center;
            gap: 0;
            margin-bottom: 50px;
            position: relative;
        }

        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            padding: 0 40px;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .progress-step::after {
            content: '';
            position: absolute;
            top: 20px;
            left: calc(50% + 30px);
            width: calc(100% - 60px);
            height: 2px;
            background: var(--gray-700);
            z-index: 0;
        }

        .progress-step:last-child::after {
            display: none;
        }

        .progress-step.active::after,
        .progress-step.completed::after {
            background: var(--primary);
        }

        .step-circle {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--gray-700);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            position: relative;
            z-index: 1;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .progress-step.active .step-circle {
            background: var(--primary);
            box-shadow: 0 0 0 8px rgba(37, 99, 235, 0.2);
            transform: scale(1.1);
        }

        .progress-step.completed .step-circle {
            background: var(--success);
        }

        .progress-step.completed .step-circle::after {
            content: '✓';
            font-size: 20px;
        }

        .progress-step.completed .step-number {
            display: none;
        }

        .step-label {
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-500);
            transition: color 0.3s ease;
        }

        .progress-step.active .step-label,
        .progress-step.completed .step-label {
            color: var(--white);
        }

        /* Form Card */
        .form-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 50px;
            position: relative;
            overflow: hidden;
        }

        .form-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
        }

        /* Form Steps */
        .form-step {
            display: none;
            animation: fadeInRight 0.5s ease;
        }

        .form-step.active {
            display: block;
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .step-header {
            margin-bottom: 40px;
        }

        .step-header h2 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .step-header p {
            color: var(--gray-500);
            font-size: 16px;
        }

        /* Form Groups */
        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-bottom: 24px;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-300);
            margin-bottom: 10px;
        }

        .form-group label .required {
            color: var(--accent);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 16px 20px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: var(--white);
            font-size: 16px;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: var(--gray-500);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(37, 99, 235, 0.1);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .form-group select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%2364748B' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 50px;
        }

        .form-group select option {
            background: var(--gray-900);
            color: var(--white);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 140px;
        }

        /* Service Cards */
        .service-options {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 30px;
        }

        @media (max-width: 700px) {
            .service-options {
                grid-template-columns: 1fr;
            }
        }

        .service-option {
            position: relative;
        }

        .service-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .service-option label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            padding: 24px 16px;
            background: rgba(255, 255, 255, 0.03);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .service-option label:hover {
            border-color: rgba(37, 99, 235, 0.5);
            background: rgba(37, 99, 235, 0.05);
        }

        .service-option input:checked + label {
            border-color: var(--primary);
            background: rgba(37, 99, 235, 0.15);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .service-option .service-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            background: rgba(37, 99, 235, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .service-option input:checked + label .service-icon {
            background: var(--primary);
        }

        .service-option .service-icon svg {
            width: 28px;
            height: 28px;
            stroke: var(--primary);
        }

        .service-option input:checked + label .service-icon svg {
            stroke: var(--white);
        }

        .service-option .service-name {
            font-weight: 600;
            font-size: 15px;
        }

        /* Budget Slider */
        .budget-slider-container {
            margin-bottom: 30px;
        }

        .budget-display {
            text-align: center;
            margin-bottom: 20px;
        }

        .budget-amount {
            font-size: 42px;
            font-weight: 800;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .budget-label {
            color: var(--gray-500);
            font-size: 14px;
        }

        .budget-slider {
            width: 100%;
            height: 8px;
            border-radius: 4px;
            background: var(--gray-700);
            appearance: none;
            cursor: pointer;
        }

        .budget-slider::-webkit-slider-thumb {
            appearance: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--primary);
            cursor: pointer;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.2);
            transition: transform 0.2s ease;
        }

        .budget-slider::-webkit-slider-thumb:hover {
            transform: scale(1.1);
        }

        .budget-range {
            display: flex;
            justify-content: space-between;
            margin-top: 12px;
            font-size: 13px;
            color: var(--gray-500);
        }

        /* Timeline Options */
        .timeline-options {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 30px;
        }

        @media (max-width: 600px) {
            .timeline-options {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .timeline-option {
            position: relative;
        }

        .timeline-option input {
            position: absolute;
            opacity: 0;
        }

        .timeline-option label {
            display: block;
            padding: 16px;
            text-align: center;
            background: rgba(255, 255, 255, 0.03);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .timeline-option label:hover {
            border-color: var(--primary);
        }

        .timeline-option input:checked + label {
            border-color: var(--primary);
            background: rgba(37, 99, 235, 0.15);
            color: var(--white);
        }

        /* Navigation Buttons */
        .form-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 16px 32px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-family: inherit;
        }

        .btn-prev {
            background: transparent;
            color: var(--gray-400);
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .btn-prev:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .btn-next {
            background: var(--primary);
            color: var(--white);
        }

        .btn-next:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.4);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: var(--white);
            padding: 18px 40px;
            font-size: 18px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(37, 99, 235, 0.4);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Success Screen */
        .success-screen {
            display: none;
            text-align: center;
            padding: 60px 40px;
            animation: fadeInUp 0.6s ease;
        }

        .success-screen.active {
            display: block;
        }

        .success-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 30px;
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: successPop 0.6s cubic-bezier(0.4, 0, 0.2, 1) 0.2s both;
        }

        @keyframes successPop {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .success-icon svg {
            width: 60px;
            height: 60px;
            stroke: var(--white);
            stroke-width: 3;
        }

        .success-screen h2 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 36px;
            margin-bottom: 16px;
        }

        .success-screen p {
            color: var(--gray-400);
            font-size: 18px;
            max-width: 400px;
            margin: 0 auto 30px;
        }

        .success-screen .btn {
            margin-top: 20px;
        }

        /* Trust Badges */
        .trust-section {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .trust-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--gray-500);
            font-size: 14px;
        }

        .trust-item svg {
            width: 20px;
            height: 20px;
            stroke: var(--success);
        }

        /* Loading */
        .btn.loading {
            pointer-events: none;
        }

        .btn.loading::after {
            content: '';
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-left: 10px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-header {
                padding: 20px;
            }

            .form-container {
                padding: 0 20px 40px;
            }

            .form-card {
                padding: 30px 20px;
            }

            .progress-step {
                padding: 0 15px;
            }

            .step-label {
                font-size: 12px;
            }

            .form-nav {
                flex-direction: column;
                gap: 15px;
            }

            .form-nav .btn {
                width: 100%;
                justify-content: center;
            }

            .trust-section {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Background Animation -->
    <div class="bg-animation">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
    </div>

    <!-- Header -->
    <header class="form-header">
        <a href="<?php echo home_url(); ?>" class="logo">
            <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="40" height="40" rx="10" fill="#2563EB"/>
                <path d="M12 28V12L20 20L28 12V28" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="logo-text">Nova Studio</span>
        </a>
        <a href="<?php echo home_url(); ?>" class="close-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Volver al sitio
        </a>
    </header>

    <!-- Main Container -->
    <main class="form-container">
        <!-- Title Section -->
        <div class="form-title-section">
            <h1>Cuéntanos sobre tu proyecto</h1>
            <p>Completa el formulario y te contactaremos en menos de 24 horas con una propuesta personalizada</p>
        </div>

        <!-- Progress Steps -->
        <div class="progress-steps">
            <div class="progress-step active" data-step="1">
                <div class="step-circle">
                    <span class="step-number">1</span>
                </div>
                <span class="step-label">Tus Datos</span>
            </div>
            <div class="progress-step" data-step="2">
                <div class="step-circle">
                    <span class="step-number">2</span>
                </div>
                <span class="step-label">Tu Proyecto</span>
            </div>
            <div class="progress-step" data-step="3">
                <div class="step-circle">
                    <span class="step-number">3</span>
                </div>
                <span class="step-label">Detalles</span>
            </div>
        </div>

        <!-- Form Card -->
        <div class="form-card">
            <form id="nova-premium-form">
                <!-- Step 1: Personal Info -->
                <div class="form-step active" data-step="1">
                    <div class="step-header">
                        <h2>¿Cómo te llamamos? 👋</h2>
                        <p>Necesitamos algunos datos para contactarte</p>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Nombre completo <span class="required">*</span></label>
                            <input type="text" id="name" name="name" placeholder="Tu nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email de trabajo <span class="required">*</span></label>
                            <input type="email" id="email" name="email" placeholder="tu@empresa.com" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Teléfono</label>
                            <input type="tel" id="phone" name="phone" placeholder="+34 600 000 000">
                        </div>
                        <div class="form-group">
                            <label for="company">Empresa</label>
                            <input type="text" id="company" name="company" placeholder="Nombre de tu empresa">
                        </div>
                    </div>

                    <div class="form-nav">
                        <div></div>
                        <button type="button" class="btn btn-next" data-next="2">
                            Continuar
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Project -->
                <div class="form-step" data-step="2">
                    <div class="step-header">
                        <h2>¿Qué necesitas? 🎯</h2>
                        <p>Selecciona el servicio que más se ajuste a tu proyecto</p>
                    </div>

                    <div class="service-options">
                        <div class="service-option">
                            <input type="radio" name="service" id="service-design" value="diseno-web">
                            <label for="service-design">
                                <div class="service-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="2" y="3" width="20" height="14" rx="2"/>
                                        <path d="M8 21h8M12 17v4"/>
                                    </svg>
                                </div>
                                <span class="service-name">Diseño Web UI/UX</span>
                            </label>
                        </div>
                        <div class="service-option">
                            <input type="radio" name="service" id="service-dev" value="desarrollo-web" checked>
                            <label for="service-dev">
                                <div class="service-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="16 18 22 12 16 6"/>
                                        <polyline points="8 6 2 12 8 18"/>
                                    </svg>
                                </div>
                                <span class="service-name">Desarrollo Web</span>
                            </label>
                        </div>
                        <div class="service-option">
                            <input type="radio" name="service" id="service-marketing" value="marketing">
                            <label for="service-marketing">
                                <div class="service-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                    </svg>
                                </div>
                                <span class="service-name">Marketing Digital</span>
                            </label>
                        </div>
                    </div>

                    <div class="budget-slider-container">
                        <label style="display: block; margin-bottom: 20px; font-weight: 600; color: var(--gray-300);">
                            Presupuesto aproximado
                        </label>
                        <div class="budget-display">
                            <span class="budget-amount" id="budget-display">3.000€</span>
                            <div class="budget-label">Presupuesto estimado</div>
                        </div>
                        <input type="range" class="budget-slider" id="budget-slider" name="budget" min="1000" max="20000" step="500" value="3000">
                        <div class="budget-range">
                            <span>1.000€</span>
                            <span>20.000€+</span>
                        </div>
                    </div>

                    <div class="form-nav">
                        <button type="button" class="btn btn-prev" data-prev="1">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 12H5M12 19l-7-7 7-7"/>
                            </svg>
                            Atrás
                        </button>
                        <button type="button" class="btn btn-next" data-next="3">
                            Continuar
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Details -->
                <div class="form-step" data-step="3">
                    <div class="step-header">
                        <h2>Últimos detalles 📝</h2>
                        <p>Cuéntanos más sobre tu proyecto y cuándo necesitas empezar</p>
                    </div>

                    <div class="form-group">
                        <label>¿Cuándo te gustaría empezar?</label>
                        <div class="timeline-options">
                            <div class="timeline-option">
                                <input type="radio" name="timeline" id="timeline-asap" value="inmediato" checked>
                                <label for="timeline-asap">Lo antes posible</label>
                            </div>
                            <div class="timeline-option">
                                <input type="radio" name="timeline" id="timeline-1month" value="1-mes">
                                <label for="timeline-1month">En 1 mes</label>
                            </div>
                            <div class="timeline-option">
                                <input type="radio" name="timeline" id="timeline-3months" value="3-meses">
                                <label for="timeline-3months">En 3 meses</label>
                            </div>
                            <div class="timeline-option">
                                <input type="radio" name="timeline" id="timeline-exploring" value="explorando">
                                <label for="timeline-exploring">Solo explorando</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="message">Cuéntanos sobre tu proyecto</label>
                        <textarea id="message" name="message" placeholder="Describe brevemente qué necesitas, tus objetivos, referencias que te gusten..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="referral">¿Cómo nos encontraste?</label>
                        <select id="referral" name="referral">
                            <option value="">Selecciona una opción</option>
                            <option value="google">Google</option>
                            <option value="redes-sociales">Redes sociales</option>
                            <option value="referido">Me recomendaron</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <div class="form-nav">
                        <button type="button" class="btn btn-prev" data-prev="2">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 12H5M12 19l-7-7 7-7"/>
                            </svg>
                            Atrás
                        </button>
                        <button type="submit" class="btn btn-submit">
                            Enviar Solicitud
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Success Screen -->
                <div class="success-screen" id="success-screen">
                    <div class="success-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    <h2>¡Solicitud Enviada!</h2>
                    <p>Gracias por contactarnos. Nuestro equipo revisará tu proyecto y te contactará en menos de 24 horas.</p>
                    <a href="<?php echo home_url(); ?>" class="btn btn-next">
                        Volver al inicio
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </form>

            <!-- Trust Badges -->
            <div class="trust-section">
                <div class="trust-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    <span>Datos 100% seguros</span>
                </div>
                <div class="trust-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <span>Respuesta en 24h</span>
                </div>
                <div class="trust-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <span>Sin compromiso</span>
                </div>
            </div>
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('nova-premium-form');
        const steps = document.querySelectorAll('.form-step');
        const progressSteps = document.querySelectorAll('.progress-step');
        const budgetSlider = document.getElementById('budget-slider');
        const budgetDisplay = document.getElementById('budget-display');
        let currentStep = 1;

        // Budget slider
        if (budgetSlider) {
            budgetSlider.addEventListener('input', function() {
                const value = parseInt(this.value);
                budgetDisplay.textContent = value >= 20000 ? '20.000€+' : value.toLocaleString('es-ES') + '€';
            });
        }

        // Navigation
        document.querySelectorAll('.btn-next').forEach(btn => {
            btn.addEventListener('click', function() {
                const nextStep = parseInt(this.dataset.next);
                if (validateStep(currentStep)) {
                    goToStep(nextStep);
                }
            });
        });

        document.querySelectorAll('.btn-prev').forEach(btn => {
            btn.addEventListener('click', function() {
                const prevStep = parseInt(this.dataset.prev);
                goToStep(prevStep);
            });
        });

        // Click on progress step
        progressSteps.forEach(step => {
            step.addEventListener('click', function() {
                const stepNum = parseInt(this.dataset.step);
                if (stepNum < currentStep) {
                    goToStep(stepNum);
                }
            });
        });

        function goToStep(step) {
            // Update steps visibility
            steps.forEach(s => s.classList.remove('active'));
            document.querySelector(`.form-step[data-step="${step}"]`).classList.add('active');

            // Update progress
            progressSteps.forEach(p => {
                const pStep = parseInt(p.dataset.step);
                p.classList.remove('active', 'completed');
                if (pStep < step) {
                    p.classList.add('completed');
                } else if (pStep === step) {
                    p.classList.add('active');
                }
            });

            currentStep = step;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function validateStep(step) {
            const currentStepEl = document.querySelector(`.form-step[data-step="${step}"]`);
            const requiredFields = currentStepEl.querySelectorAll('[required]');
            let valid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#EF4444';
                    valid = false;
                    setTimeout(() => {
                        field.style.borderColor = '';
                    }, 2000);
                }

                if (field.type === 'email' && field.value) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(field.value)) {
                        field.style.borderColor = '#EF4444';
                        valid = false;
                        setTimeout(() => {
                            field.style.borderColor = '';
                        }, 2000);
                    }
                }
            });

            return valid;
        }

        // Form submit
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!validateStep(currentStep)) return;

            const submitBtn = form.querySelector('.btn-submit');
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;

            const formData = new FormData(form);
            formData.append('action', 'nova_submit_lead');
            formData.append('nonce', '<?php echo wp_create_nonce("nova_leads_submit"); ?>');

            fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.classList.remove('loading');
                
                if (data.success) {
                    // Hide all steps
                    steps.forEach(s => s.classList.remove('active'));
                    // Show success
                    document.getElementById('success-screen').classList.add('active');
                    // Update progress
                    progressSteps.forEach(p => p.classList.add('completed'));
                } else {
                    alert(data.data || 'Error al enviar. Inténtalo de nuevo.');
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
                alert('Error de conexión. Inténtalo de nuevo.');
            });
        });
    });
    </script>

    <?php wp_footer(); ?>
</body>
</html>
