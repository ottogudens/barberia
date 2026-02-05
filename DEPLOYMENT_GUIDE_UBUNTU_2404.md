# Guía de Despliegue: Barbería SaaS en Ubuntu 24.04

Esta guía detalla paso a paso cómo desplegar la plataforma "Barbería SaaS" en un servidor limpio con **Ubuntu 24.04**. 

## Estrategia de Domino (IMPORTANTE)
Como este despliegue utilizará directamente una **IP Pública** (sin un dominio `.com` comprado aún), utilizaremos el servicio **nip.io** para simular subdominios reales.

*   Si tu IP es: `1.2.3.4`
*   Tu dominio principal será: `1.2.3.4.nip.io`
*   Tus tenants serán: `gold-luk.1.2.3.4.nip.io`, `test.1.2.3.4.nip.io`

Esto permite que la aplicación detecte los subdominios correctamente sin configurar DNS complejos.

---

## 1. Preparación del Servidor

Conéctate a tu servidor como `root`:
```bash
ssh root@TU_IP_PUBLICA
```

### 1.1 Actualizar el Sistema
```bash
apt update && apt upgrade -y
```

### 1.2 Crear Usuario de Despliegue (Seguridad)
No ejecutaremos la app como root.
```bash
adduser deploy
# Sigue las instrucciones para poner contraseña
usermod -aG sudo deploy
```

### 1.3 Configurar Firewall (UFW)
```bash
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw enable
```

---

## 2. Instalación del Stack LEMP

### 2.1 Instalar Nginx
```bash
apt install nginx -y
systemctl start nginx
systemctl enable nginx
```

### 2.2 Instalar MySQL 8.0
```bash
apt install mysql-server -y
mysql_secure_installation
```
*Sigue el asistente: VALIDATE PASSWORD (Y/N) -> N (para dev) o Y (prod), Set root password -> TU_PASSWORD_ROOT, Remove anonymous -> Y, Disallow root login remotely -> Y, Remove test db -> Y, Reload tables -> Y.*

### 2.3 Instalar PHP 8.2 (o superior)
```bash
apt install php-fpm php-mysql php-mbstring php-xml php-curl php-zip unzip -y
```

---

## 3. Configuración de Base de Datos

Entra a la consola de MySQL:
```bash
sudo mysql
```

Ejecuta los siguientes comandos SQL:
```sql
-- Crear Base de Datos
CREATE DATABASE barberia_prod;

-- Crear Usuario Dedicado (Cambia 'tupasswordseguro')
CREATE USER 'barberia_user'@'localhost' IDENTIFIED BY 'tupasswordseguro';

-- Dar Permisos
GRANT ALL PRIVILEGES ON barberia_prod.* TO 'barberia_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 4. Despliegue de la Aplicación

### 4.1 Clonar o Subir Código
Usaremos `/var/www/barberia` como directorio raíz.

```bash
# Opción A: Git Clone (Recomendado)
git clone https://github.com/tu-usuario/barberia-saas.git /var/www/barberia

# Opción B: Subir tu código actual por SCP (desde tu máquina local)
# scp -r /ruta/a/tu/proyecto/* deploy@TU_IP_PUBLICA:/var/www/barberia
```

### 4.2 Importar Base de Datos Inicial
Sube tu archivo `barberia.sql` (exportado de tu local) al servidor e impórtalo:
```bash
mysql -u barberia_user -p barberia_prod < /var/www/barberia/barberia.sql
```

### 4.3 Configurar Permisos
Nginx necesita ser dueño de los archivos para leerlos y escribirlos (si aplica).
```bash
chown -R www-data:www-data /var/www/barberia
chmod -R 755 /var/www/barberia
```

### 4.4 Configurar conexión (.env)
Crea el archivo `.env` en producción:
```bash
cp /var/www/barberia/.env.example /var/www/barberia/.env
nano /var/www/barberia/.env
```
Edita las credenciales:
```ini
DB_SERVER=localhost
DB_USER=barberia_user
DB_PASS=tupasswordseguro
DB_NAME=barberia_prod
```

---

## 5. Configuración de Nginx (La Magia de los Subdominios)

Crea un nuevo bloque de servidor:
```bash
nano /etc/nginx/sites-available/barberia
```

Pega el siguiente contenido (REEMPLAZA `TU_IP_PUBLICA` con tu IP real):

```nginx
server {
    listen 80;
    
    # Esta regex captura el subdominio en la variable $tenant
    # Ejemplo: gold-luk.1.2.3.4.nip.io -> $tenant = gold-luk
    server_name ~^(?<tenant>.+)\.TU_IP_PUBLICA\.nip\.io$ TU_IP_PUBLICA.nip.io;
    
    root /var/www/barberia;
    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock; # Verifica tu versión con 'php -v'
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
    
    # Logs separados para debug
    error_log /var/log/nginx/barberia_error.log;
    access_log /var/log/nginx/barberia_access.log;
}
```

Activa el sitio y reinicia Nginx:
```bash
ln -s /etc/nginx/sites-available/barberia /etc/nginx/sites-enabled/
rm /etc/nginx/sites-enabled/default
nginx -t # Verificar sintaxis
systemctl restart nginx
```

---

## 6. Verificación Final

1.  **Landing SaaS**: Abre en tu navegador `http://TU_IP_PUBLICA.nip.io`. Deberías ver la landing page de "Barbería SaaS".
2.  **Registro**: Ve a `http://TU_IP_PUBLICA.nip.io/register_tenant.php` y crea una nueva barbería (ej: `demo`).
3.  **Tenant**: El sistema debería redirigirte a `http://demo.TU_IP_PUBLICA.nip.io/admin/login.php`.
4.  **Login**: Ingresa con las credenciales que creaste.

¡Felicidades! Tu SaaS está operativo en producción.
