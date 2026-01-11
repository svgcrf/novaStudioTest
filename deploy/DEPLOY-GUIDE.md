# 🚀 Guía de Despliegue - Nova Studio en Google Cloud VM

## Requisitos Previos

- VM Ubuntu 22.04 LTS en Google Cloud
- IP pública asignada
- Puerto 80 y 443 abiertos en el firewall de GCP
- Acceso SSH a la VM

---

## Paso 1: Conectar a la VM

```bash
# Desde Google Cloud Console o con gcloud CLI
gcloud compute ssh NOMBRE_VM --zone=ZONA

# O con SSH directo (si tienes la clave)
ssh -i ~/.ssh/tu_clave usuario@IP_PUBLICA
```

---

## Paso 2: Configurar la VM

```bash
# Descargar y ejecutar script de setup
curl -sSL https://raw.githubusercontent.com/svgcrf/novaStudioTest/main/deploy/setup-vm.sh | bash

# O si ya tienes los archivos:
chmod +x setup-vm.sh
./setup-vm.sh

# IMPORTANTE: Cerrar sesión y volver a entrar
exit
# Volver a conectar
```

---

## Paso 3: Subir archivos del proyecto

### Opción A: Clonar desde GitHub
```bash
cd /opt/nova-studio
git clone https://github.com/svgcrf/novaStudioTest.git .
```

### Opción B: Subir con SCP (desde tu máquina local)
```bash
# Desde tu máquina local
scp -r /ruta/local/novaStudioTest/* usuario@IP_VM:/opt/nova-studio/
```

### Opción C: Subir con gcloud
```bash
# Desde Cloud Shell o local con gcloud configurado
gcloud compute scp --recurse ./novaStudioTest/* NOMBRE_VM:/opt/nova-studio/ --zone=ZONA
```

---

## Paso 4: Iniciar Docker

```bash
cd /opt/nova-studio

# Usar docker-compose simple (sin SSL, para pruebas)
cp deploy/docker-compose.simple.yml docker-compose.yml

# Iniciar contenedores
docker-compose up -d

# Verificar que están corriendo
docker-compose ps
```

---

## Paso 5: Configurar WordPress

```bash
# Editar el script con tu IP
nano deploy/setup-wordpress.sh
# Cambiar TU_IP_AQUI por tu IP pública

# Ejecutar configuración
chmod +x deploy/setup-wordpress.sh
./deploy/setup-wordpress.sh http://TU_IP_PUBLICA
```

---

## Paso 6: Verificar instalación

1. Abre en el navegador: `http://TU_IP_PUBLICA`
2. Deberías ver la landing page de Nova Studio
3. Accede al admin: `http://TU_IP_PUBLICA/wp-admin/`
   - Usuario: `admin`
   - Contraseña: `NovaAdmin2024!`

---

## Configuración SSL (Opcional pero recomendado)

### Si tienes un dominio:

1. Apunta tu dominio a la IP de la VM (registro A en DNS)
2. Espera propagación DNS (puede tardar hasta 48h)
3. Configura SSL:

```bash
# Instalar certbot
sudo apt-get install certbot python3-certbot-nginx -y

# Obtener certificado
sudo certbot --nginx -d tudominio.com -d www.tudominio.com

# Renovación automática (ya configurada)
sudo certbot renew --dry-run
```

---

## Comandos Útiles

```bash
# Ver logs de WordPress
docker-compose logs -f wordpress

# Ver logs de la base de datos
docker-compose logs -f db

# Reiniciar servicios
docker-compose restart

# Parar servicios
docker-compose down

# Parar y eliminar volúmenes (CUIDADO: borra datos)
docker-compose down -v

# Ejecutar comando en WordPress
docker exec -it nova_wordpress bash

# Backup de base de datos
docker exec nova_db mysqldump -u wordpress -pNovaStudio2024! wordpress > backup.sql
```

---

## Estructura de Archivos en la VM

```
/opt/nova-studio/
├── docker-compose.yml
├── deploy/
│   ├── setup-vm.sh
│   ├── setup-wordpress.sh
│   └── ...
├── wordpress/
│   └── wp-content/
│       ├── themes/nova-studio/
│       └── plugins/nova-leads-simple.php
└── nginx/ (si usas SSL)
    ├── nginx.conf
    └── ssl/
```

---

## Troubleshooting

### Error: "Permission denied"
```bash
sudo chown -R $USER:$USER /opt/nova-studio
```

### Error: "Cannot connect to Docker daemon"
```bash
sudo systemctl start docker
# O reiniciar sesión para permisos de grupo
```

### WordPress no carga estilos
```bash
# Verificar que el tema está montado correctamente
docker exec nova_wordpress ls -la /var/www/html/wp-content/themes/
```

### Base de datos no conecta
```bash
# Ver logs
docker-compose logs db
# Esperar a que esté healthy
docker-compose ps
```

---

## Seguridad Recomendada

1. **Cambiar contraseñas por defecto** en `docker-compose.yml`
2. **Configurar firewall** solo puertos necesarios (22, 80, 443)
3. **Usar SSL** en producción
4. **Backups regulares** de la base de datos
5. **Actualizaciones** periódicas del sistema y Docker

---

## Contacto

Para soporte: admin@novastudio.dev
