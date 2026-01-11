<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Header Principal -->
<header class="nova-header" id="nova-header">
    <div class="nova-container">
        <div class="header-inner">
            <!-- Logo -->
            <div class="header-logo">
                <?php nova_studio_logo(); ?>
            </div>
            
            <!-- Navegación -->
            <nav class="header-nav" id="main-nav">
                <ul class="nav-menu">
                    <li><a href="#servicios">Servicios</a></li>
                    <li><a href="#proceso">Proceso</a></li>
                    <li><a href="#about">Nosotros</a></li>
                    <li><a href="#contacto">Contacto</a></li>
                </ul>
            </nav>
            
            <!-- CTA Header -->
            <div class="header-cta">
                <a href="#contacto" class="nova-btn nova-btn-primary nova-btn-sm">
                    Empezar proyecto
                </a>
            </div>
            
            <!-- Mobile Toggle -->
            <button class="mobile-menu-toggle" id="mobile-toggle" aria-label="Menú">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</header>

<main id="main-content" class="nova-main">
