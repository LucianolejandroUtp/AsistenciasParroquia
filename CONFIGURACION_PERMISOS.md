# ============================================
# CONFIGURACIÓN DE PERMISOS POST-DESPLIEGUE
# Instrucciones para configurar después de subir archivos
# ============================================

## PERMISOS NECESARIOS EN EL SERVIDOR

### Directorios que necesitan permisos de escritura:
```
storage/                           → 755 o 775
storage/app/                       → 755 o 775
storage/app/public/                → 755 o 775
storage/framework/                 → 755 o 775
storage/framework/cache/           → 755 o 775
storage/framework/sessions/        → 755 o 775
storage/framework/views/           → 755 o 775
storage/logs/                      → 755 o 775
bootstrap/cache/                   → 755 o 775
```

### Archivos que pueden necesitar permisos específicos:
```
.env                               → 644 (lectura para web server)
public/.htaccess                   → 644
public/index.php                   → 644
```

## COMANDOS PARA FILE MANAGER DEL HOSTING

Si tu hosting tiene File Manager en el panel de control:

1. **Navegar a directorio Parroquia/**
2. **Click derecho en carpeta storage/ → Permisos/Permissions**
3. **Configurar: 755 (rwxr-xr-x) o 775 (rwxrwxr-x)**
4. **Marcar "Apply to subdirectories" o "Recursivo"**
5. **Repetir para bootstrap/cache/**

## COMANDOS VIA FTP CLIENT (FileZilla)

Si tu cliente FTP lo permite:

1. **Click derecho en storage/ → File permissions**
2. **Configurar: 755 o 775**
3. **Marcar "Recurse into subdirectories"**
4. **Aplicar**

## VERIFICACIÓN POST-CONFIGURACIÓN

### URLs a probar después del despliegue:

1. **Página principal:**
   ```
   https://giandei.orgfree.com/Parroquia/public/
   ```

2. **Verificar assets:**
   ```
   https://giandei.orgfree.com/Parroquia/public/build/manifest.json
   ```

3. **Verificar rutas Laravel (si hay):**
   ```
   https://giandei.orgfree.com/Parroquia/public/login
   https://giandei.orgfree.com/Parroquia/public/dashboard
   ```

### Solución de problemas comunes:

#### Error 500 - Internal Server Error
- **Causa probable:** Permisos incorrectos en storage/ o bootstrap/cache/
- **Solución:** Configurar permisos 755 o 775 en directorios mencionados

#### Error 404 - Not Found
- **Causa probable:** .htaccess no funciona o mod_rewrite deshabilitado
- **Solución:** Verificar que .htaccess.production se renombró a .htaccess

#### Páginas sin estilos
- **Causa probable:** Assets no cargados o rutas incorrectas
- **Solución:** Verificar que carpeta build/ se subió correctamente

#### Error de base de datos
- **Causa probable:** Credenciales incorrectas en .env
- **Solución:** Verificar DB_DATABASE, DB_USERNAME, DB_PASSWORD

## LOGS PARA DEPURACIÓN

### Ubicación de logs en el servidor:
```
/Parroquia/storage/logs/laravel.log
```

### Ver últimas líneas del log (si tienes acceso SSH):
```bash
tail -f /path/to/Parroquia/storage/logs/laravel.log
```

### Si no tienes SSH:
- Descargar archivo laravel.log via FTP
- Abrir con editor de texto
- Revisar últimas entradas para errores