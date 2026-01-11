# novaStudioTest - WordPress Dev Environment

## 🚀 Entorno WordPress + MariaDB para Codespaces

Este repositorio configura automáticamente un entorno de desarrollo WordPress con Docker, listo para pruebas de diseño web con Elementor.

## 📦 Stack

- **WordPress**: latest
- **MariaDB**: 10.6
- **Puerto**: 8080

## ⚡ Inicio Rápido

### En GitHub Codespaces (Recomendado)

1. Haz clic en **"Code" → "Codespaces" → "Create codespace on main"**
2. Espera a que el Codespace inicie (~1-2 minutos)
3. Los contenedores se levantan automáticamente
4. Ve a la pestaña **"PORTS"** en la parte inferior
5. Busca el puerto **8080** y haz clic en el icono 🌐 para abrir

### Marcar puerto como público

1. En la pestaña **PORTS**, haz clic derecho sobre el puerto 8080
2. Selecciona **"Port Visibility" → "Public"**
3. Ahora puedes compartir la URL con cualquiera

### Manual (ya dentro del Codespace)

```bash
docker compose up -d
```

## 🔧 Comandos Útiles

```bash
# Ver logs de WordPress
docker logs wordpress_app -f

# Ver logs de MariaDB
docker logs wordpress_db -f

# Reiniciar servicios
docker compose restart

# Detener servicios
docker compose down

# Detener y eliminar volúmenes (reset completo)
docker compose down -v
```

## 🔐 Credenciales de Base de Datos

| Variable | Valor |
|----------|-------|
| Host | db |
| Database | wordpress |
| User | wordpress |
| Password | wordpress |

## 📝 Instalación de WordPress

1. Abre la URL del puerto 8080
2. Selecciona idioma
3. Configura título del sitio, usuario admin y contraseña
4. ¡Listo! Puedes instalar Elementor desde Plugins → Añadir nuevo

---

*Configurado para pruebas técnicas de diseño web*
Quiz STOnline - FREE -
