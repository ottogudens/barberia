# Guía de Despliegue a Producción - Barberia SaaS

Este documento detalla los pasos necesarios para llevar tu plataforma SaaS a un entorno productivo en vivo.

## 1. Requisitos de Infraestructura

Para soportar la arquitectura de subdominios (`cliente.midominio.com`), necesitas un control más avanzado que un hosting compartido básico.

*   **Recomendado:** VPS (Servidor Privado Virtual) como DigitalOcean Droplet, AWS EC2, Linode, o Vultr.
*   **Dominio:** Un dominio propio (ej. `midominio.com`).
*   **Sistema Operativo:** Ubuntu 22.04 LTS o superior.
*   **Servidor Web:** Nginx (Recomendado por su fácil manejo de subdominios) o Apache.
*   **Base de Datos:** MySQL 8.0.

## 2. Configuración de DNS (Wildcard)

El núcleo de tu SaaS es que cualquier subdominio apunte a tu aplicación.

1.  En el panel de tu proveedor de dominio (GoDaddy, Namecheap, Cloudflare), crea un registro **A Record**.
2.  **Host/Name:** `*` (asterisco).
3.  **Value/Target:** La dirección IP Pública de tu servidor VPS.
4.  Crea otro registro **A Record** para `@` (raíz) apuntando a la misma IP.

Esto hará que `cualquier-cosa.midominio.com` llegue a tu servidor.

## 3. Configuración del Servidor Web (Ejemplo Nginx)

Debes configurar Nginx para aceptar cualquier subdominio y servir siempre la misma carpeta de código.

```nginx
server {
    listen 80;
    server_name midominio.com *.midominio.com; # El * es clave
    root /var/www/barberia; # Ruta donde subirás tu código

    index index.php index.html;

    location / {
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock; # Verifica tu versión de PHP
    }
}
```

## 4. Certificados SSL (HTTPS)

Para seguridad y confianza, necesitas HTTPS en todos los subdominios.

*   Usa **Certbot** (Let's Encrypt).
*   Solicita un **Certificado Wildcard**: `sudo certbot --nginx -d midominio.com -d *.midominio.com`.
*   *Nota: Los certificados Wildcard con Let's Encrypt generalmente requieren validación DNS.*

## 5. Implementación del Código

1.  **Subir Código:** Usa `git clone` en tu servidor para descargar este repositorio en `/var/www/barberia`.
2.  **Base de Datos:**
    *   Exporta tu base de datos local: `mysqldump -u user -p barberia > backup.sql`.
    *   Impórtala en el servidor: `mysql -u root -p barberia_prod < backup.sql`.
3.  **Archivo .env:**
    *   Crea un archivo `.env` en producción.
    *   **IMPORTANTE:** Cambia `DB_USER` y `DB_PASS` por credenciales seguras de producción.

## 6. Ajustes en la Aplicación

### tenant_context.php

Tu archivo actual ya soporta la lógica de subdominios, pero asegúrate de que la lógica de conteo de partes del dominio coincida con tu dominio real.

Si tu dominio es `barberiaapp.com` (2 partes):
*   `cliente.barberiaapp.com` tiene 3 partes.
*   El código actual `if (count($parts) > 2 ...)` funcionará correctamente y tomará `cliente` como slug.

### Email (SMTP)

PHP `mail()` suele irse a SPAM. Configura `PHPMailer` (o similar) con un servicio profesional como SendGrid o Amazon SES para enviar correos de confirmación de registro.

## 7. Pasos Finales

1.  Reinicia Nginx/Apache.
2.  Entra a `register.midominio.com` (o la ruta que decidas para registro).
3.  Crea una cuenta real.
4.  Verifica que el panel `cliente.midominio.com` cargue correctamente.
