#!/bin/bash
# =============================================================================
# Nova Studio - Script de Despliegue Completo a GCP
# =============================================================================

set -e

# Configuración
PROJECT="overtimeapptcspbv2"
ZONE="us-central1-c"
VM_NAME="nova-studio-wp"
GCLOUD="$HOME/google-cloud-sdk/bin/gcloud"

echo "=============================================="
echo "  🚀 Nova Studio - Despliegue a Google Cloud"
echo "=============================================="

# Obtener IP de la VM
echo ""
echo "📍 Obteniendo IP de la VM..."
VM_IP=$($GCLOUD compute instances describe $VM_NAME --zone=$ZONE --project=$PROJECT --format='get(networkInterfaces[0].accessConfigs[0].natIP)')
echo "   IP Externa: $VM_IP"

# Crear archivo tar del proyecto (excluyendo node_modules, .git, etc)
echo ""
echo "📦 Empaquetando proyecto..."
cd /workspaces/novaStudioTest
tar --exclude='.git' \
    --exclude='node_modules' \
    --exclude='.devcontainer' \
    --exclude='*.log' \
    -czvf /tmp/nova-studio.tar.gz \
    wordpress/ deploy/

echo "   ✅ Proyecto empaquetado: $(du -h /tmp/nova-studio.tar.gz | cut -f1)"

# Subir archivo a la VM
echo ""
echo "📤 Subiendo proyecto a la VM..."
$GCLOUD compute scp /tmp/nova-studio.tar.gz $VM_NAME:/tmp/ --zone=$ZONE --project=$PROJECT

# Ejecutar setup en la VM
echo ""
echo "🔧 Configurando VM y desplegando..."
$GCLOUD compute ssh $VM_NAME --zone=$ZONE --project=$PROJECT --command='
#!/bin/bash
set -e

echo "=== Actualizando sistema ==="
sudo apt-get update -qq

echo "=== Instalando Docker ==="
if ! command -v docker &> /dev/null; then
    curl -fsSL https://get.docker.com | sudo sh
    sudo usermod -aG docker $USER
fi

echo "=== Instalando Docker Compose ==="
if ! command -v docker-compose &> /dev/null; then
    sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    sudo chmod +x /usr/local/bin/docker-compose
fi

echo "=== Configurando proyecto ==="
sudo mkdir -p /opt/nova-studio
sudo chown $USER:$USER /opt/nova-studio
cd /opt/nova-studio

echo "=== Extrayendo archivos ==="
tar -xzf /tmp/nova-studio.tar.gz
rm /tmp/nova-studio.tar.gz

echo "=== Copiando docker-compose ==="
cp deploy/docker-compose.simple.yml docker-compose.yml

echo "=== Iniciando contenedores ==="
sudo docker-compose down 2>/dev/null || true
sudo docker-compose up -d

echo "=== Esperando a que WordPress esté listo (60s) ==="
sleep 60

echo "=== Instalando WP-CLI ==="
sudo docker exec nova_wordpress bash -c "curl -sO https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && chmod +x wp-cli.phar && mv wp-cli.phar /usr/local/bin/wp" 2>/dev/null || true

echo "=== Configuración completa ==="
'

# Obtener IP actualizada y configurar WordPress
echo ""
echo "🔧 Configurando WordPress con la IP correcta..."
$GCLOUD compute ssh $VM_NAME --zone=$ZONE --project=$PROJECT --command="
#!/bin/bash
cd /opt/nova-studio

# Verificar contenedores
echo '=== Estado de contenedores ==='
sudo docker-compose ps

# Configurar WordPress
SITE_URL=\"http://$VM_IP\"

echo \"=== Configurando WordPress con URL: \$SITE_URL ===\"

# Verificar si ya está instalado
if sudo docker exec nova_wordpress wp core is-installed --allow-root 2>/dev/null; then
    echo 'WordPress ya instalado, actualizando URLs...'
    sudo docker exec nova_wordpress wp option update home \"\$SITE_URL\" --allow-root
    sudo docker exec nova_wordpress wp option update siteurl \"\$SITE_URL\" --allow-root
else
    echo 'Instalando WordPress...'
    sudo docker exec nova_wordpress wp core install \\
        --url=\"\$SITE_URL\" \\
        --title=\"Nova Studio\" \\
        --admin_user=\"admin\" \\
        --admin_password=\"NovaAdmin2024!\" \\
        --admin_email=\"admin@novastudio.dev\" \\
        --skip-email \\
        --allow-root
fi

# Activar tema
echo '=== Activando tema Nova Studio ==='
sudo docker exec nova_wordpress wp theme activate nova-studio --allow-root 2>/dev/null || echo 'Tema activado o no encontrado'

# Activar plugin
echo '=== Activando plugin ==='
sudo docker exec nova_wordpress wp plugin activate nova-leads-simple --allow-root 2>/dev/null || echo 'Plugin activado o no encontrado'

# Configurar permalinks
echo '=== Configurando permalinks ==='
sudo docker exec nova_wordpress wp rewrite structure '/%postname%/' --allow-root 2>/dev/null || true
sudo docker exec nova_wordpress wp rewrite flush --allow-root 2>/dev/null || true

# Crear página de inicio
echo '=== Creando páginas ==='
LANDING_EXISTS=\$(sudo docker exec nova_wordpress wp post list --post_type=page --name=inicio --field=ID --allow-root 2>/dev/null)
if [ -z \"\$LANDING_EXISTS\" ]; then
    LANDING_ID=\$(sudo docker exec nova_wordpress wp post create --post_type=page --post_title='Inicio' --post_name='inicio' --post_status=publish --page_template='page-templates/landing-nova-studio.php' --porcelain --allow-root)
    echo \"Página inicio creada: \$LANDING_ID\"
else
    LANDING_ID=\$LANDING_EXISTS
    echo \"Página inicio existe: \$LANDING_ID\"
fi

# Crear página formulario
FORM_EXISTS=\$(sudo docker exec nova_wordpress wp post list --post_type=page --name=solicitar-presupuesto --field=ID --allow-root 2>/dev/null)
if [ -z \"\$FORM_EXISTS\" ]; then
    FORM_ID=\$(sudo docker exec nova_wordpress wp post create --post_type=page --post_title='Solicitar Presupuesto' --post_name='solicitar-presupuesto' --post_status=publish --page_template='page-templates/form-premium.php' --porcelain --allow-root)
    echo \"Página formulario creada: \$FORM_ID\"
fi

# Configurar página de inicio
echo '=== Configurando página de inicio estática ==='
sudo docker exec nova_wordpress wp option update show_on_front page --allow-root
sudo docker exec nova_wordpress wp option update page_on_front \$LANDING_ID --allow-root 2>/dev/null || true

echo '=== Limpiando caché ==='
sudo docker exec nova_wordpress wp cache flush --allow-root 2>/dev/null || true

echo ''
echo '✅ WordPress configurado!'
"

echo ""
echo "=============================================="
echo "  ✅ ¡DESPLIEGUE COMPLETADO!"
echo "=============================================="
echo ""
echo "🌐 Tu sitio está en: http://$VM_IP"
echo ""
echo "🔐 Panel de administración:"
echo "   URL: http://$VM_IP/wp-admin/"
echo "   Usuario: admin"
echo "   Contraseña: NovaAdmin2024!"
echo ""
echo "📄 Páginas:"
echo "   - Inicio: http://$VM_IP/"
echo "   - Formulario: http://$VM_IP/solicitar-presupuesto/"
echo ""
