# Compartir la aplicación en LAN

Este documento explica cómo exponer la aplicación Laravel en la red local (LAN) cuando usas `composer run dev`.

Resumen de cambios realizados
- Se actualizó `composer.json` para ejecutar `php artisan serve --host=0.0.0.0 --port=8080` dentro del script `dev`.
- Se actualizó `package.json` para ejecutar `vite --host` en el script `dev`.

Cómo funciona ahora
- `composer run dev` ejecuta (concurrently):
  - `php artisan serve --host=0.0.0.0 --port=8080` (servidor PHP disponible en todas las interfaces, puerto 8080)
  - `php artisan queue:listen --tries=1` (cola)
  - `npm run dev` → `vite --host` (servidor de assets accesible desde la LAN)

Ejecutar localmente
1. En PowerShell, en la raíz del proyecto:

```
composer run dev
```

2. Desde otra máquina en la misma red, abre en el navegador:

```
http://<IP_DEL_PC_DESARROLLO>:8080
```

Sustituye `<IP_DEL_PC_DESARROLLO>` por la IP local (p.ej. 192.168.1.106). Puedes obtenerla con `ipconfig`.

Abrir puertos en Windows Firewall
1. Abrir "Windows Defender Firewall con seguridad avanzada".
2. Crear una regla de entrada:
   - Tipo de regla: Puerto
   - Puerto local específico: 8080 (TCP)
   - Permitir la conexión
   - Aplicar a los perfiles deseados (Privado/Red de trabajo)
   - Nombre: "Laravel dev 8080"

Alternativas seguras para exponer a Internet
- ngrok (recomendado para compartir temporalmente): `ngrok http 8080`.
- LocalTunnel: `npx localtunnel --port 8080`.

Advertencias de seguridad
- `php artisan serve` no está pensado para producción. Para exposiciones públicas usar Nginx/Apache con PHP-FPM y HTTPS.
- Desactivar o proteger endpoints sensibles antes de exponer la aplicación públicamente.

Siguientes pasos recomendados
- Configurar HTTPS con un proxy reverso si vas a exponer el sitio públicamente.
- Crear un entorno de staging con Nginx/Apache para pruebas en red.

