#!/bin/bash
# =============================================================================
# Nova Studio - Setup Script para Ubuntu 22.04 LTS (Google Cloud VM)
# =============================================================================

set -e

echo "=============================================="
echo "  Nova Studio - Configuración de VM"
echo "=============================================="

# Actualizar sistema
echo "📦 Actualizando sistema..."
sudo apt-get update && sudo apt-get upgrade -y

# Instalar dependencias básicas
echo "📦 Instalando dependencias..."
sudo apt-get install -y \
    apt-transport-https \
    ca-certificates \
    curl \
    gnupg \
    lsb-release \
    git \
    unzip \
    htop \
    nano

# Instalar Docker
echo "🐳 Instalando Docker..."
if ! command -v docker &> /dev/null; then
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
    echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
    sudo apt-get update
    sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin
    sudo usermod -aG docker $USER
    echo "✅ Docker instalado"
else
    echo "✅ Docker ya está instalado"
fi

# Instalar Docker Compose (standalone)
echo "🐳 Instalando Docker Compose..."
if ! command -v docker-compose &> /dev/null; then
    sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    sudo chmod +x /usr/local/bin/docker-compose
    echo "✅ Docker Compose instalado"
else
    echo "✅ Docker Compose ya está instalado"
fi

# Crear directorio del proyecto
echo "📁 Creando directorio del proyecto..."
sudo mkdir -p /opt/nova-studio
sudo chown $USER:$USER /opt/nova-studio

# Configurar firewall
echo "🔥 Configurando firewall..."
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw --force enable

echo ""
echo "=============================================="
echo "  ✅ Configuración base completada!"
echo "=============================================="
echo ""
echo "Próximos pasos:"
echo "1. Cierra sesión y vuelve a entrar (para permisos Docker)"
echo "2. Sube los archivos del proyecto a /opt/nova-studio"
echo "3. Ejecuta: cd /opt/nova-studio && docker-compose up -d"
echo ""
