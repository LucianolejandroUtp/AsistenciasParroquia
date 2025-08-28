# 🚀 CHECKLIST COMPLETO DE DESPLIEGUE
## Sistema de Asistencias Primera Comunión - Hosting Compartido

### ✅ PRE-DESPLIEGUE (COMPLETADO)
- [x] **Optimización Composer:** Dependencias de producción instaladas
- [x] **Cache Laravel:** Configuraciones, rutas, vistas y eventos cacheados
- [x] **Assets compilados:** Frontend optimizado para producción
- [x] **Configuración .env.production:** Optimizada para hosting compartido
- [x] **.htaccess optimizado:** Configuración de seguridad y performance
- [x] **Documentación:** Guías de archivos y permisos creadas

---

## 📤 PROCESO DE SUBIDA FTP

### PASO 1: Preparación de archivos locales
```bash
# 1. Renombrar archivos para producción
# En tu computadora, antes de subir:

# Crear copia de .env.production como .env-para-servidor
copy .env.production .env-para-servidor

# Crear copia de .htaccess.production como .htaccess-para-servidor  
copy public\.htaccess.production public\.htaccess-para-servidor
```

### PASO 2: Conectar FileZilla
```
Host: ftp.giandei.orgfree.com (o el que te proporcionaron)
Usuario: [tu_usuario_ftp]
Contraseña: [tu_contraseña_ftp]
Puerto: 21 (predeterminado)
```

### PASO 3: Crear estructura en servidor
```
# En FileZilla, lado derecho (servidor):
# 1. Navegar a directorio raíz
# 2. Crear carpeta "Parroquia" si no existe
# 3. Entrar a carpeta Parroquia/
```

### PASO 4: Subir archivos (ORDEN IMPORTANTE)
```
📁 ESTRUCTURA A CREAR EN /Parroquia/:

1. Subir CARPETAS principales:
   ├── app/                 (toda la carpeta)
   ├── bootstrap/           (toda la carpeta) 
   ├── config/              (toda la carpeta)
   ├── database/            (toda la carpeta)
   ├── public/              (toda la carpeta)
   ├── resources/           (toda la carpeta)
   ├── routes/              (toda la carpeta)
   ├── storage/             (toda la carpeta)
   └── vendor/              (toda la carpeta)

2. Subir ARCHIVOS de configuración:
   ├── .env-para-servidor                    → renombrar a .env
   └── public/.htaccess-para-servidor        → renombrar a public/.htaccess
```

### PASO 5: Renombrar archivos en servidor
```
# En FileZilla, lado servidor:
# 1. Click derecho en ".env-para-servidor" → Renombrar → ".env"
# 2. Navegar a public/
# 3. Click derecho en ".htaccess-para-servidor" → Renombrar → ".htaccess"
```

### PASO 6: Configurar permisos
```
# Para cada directorio, click derecho → File permissions:

storage/                 → 755 (rwxr-xr-x) ✓ Aplicar a subdirectorios
bootstrap/cache/         → 755 (rwxr-xr-x) ✓ Aplicar a subdirectorios

# Verificar archivos clave:
.env                     → 644 (rw-r--r--)
public/.htaccess         → 644 (rw-r--r--)
public/index.php         → 644 (rw-r--r--)
```

---

## 🧪 VERIFICACIÓN POST-DESPLIEGUE

### URLs a probar:
1. **Página principal:**
   ```
   https://giandei.orgfree.com/Parroquia/public/
   ```

2. **Assets (debe responder JSON):**
   ```
   https://giandei.orgfree.com/Parroquia/public/build/manifest.json
   ```

3. **Health check Laravel (si funciona):**
   ```
   https://giandei.orgfree.com/Parroquia/public/up
   ```

### Verificaciones técnicas:
- [ ] **Página carga sin errores 500**
- [ ] **Estilos CSS se aplican correctamente**
- [ ] **JavaScript funciona (sin errores en consola)**
- [ ] **Base de datos conecta (si hay formularios)**
- [ ] **Logs no muestran errores críticos**

---

## 🚨 SOLUCIÓN RÁPIDA DE PROBLEMAS

### Error 500 - Internal Server Error
```
Causa: Permisos incorrectos
Solución: Verificar permisos 755 en storage/ y bootstrap/cache/
```

### Error 404 - Page Not Found  
```
Causa: .htaccess no funciona
Solución: Verificar que .htaccess está en public/ y mod_rewrite activo
```

### Página sin estilos
```
Causa: Assets no cargan
Solución: Verificar que carpeta public/build/ se subió completamente
```

### Error de base de datos
```
Causa: Credenciales incorrectas
Solución: Verificar .env con datos exactos del hosting
```

---

## 📋 ARCHIVOS NO SUBIDOS (CONFIRMACIÓN)

### ❌ NO subir estos archivos/carpetas:
```
node_modules/           # ~200MB - Solo dependencias desarrollo
.git/                   # ~50MB - Repositorio completo
tests/                  # ~5MB - Pruebas unitarias
.env                    # Configuración local
package.json           # Config npm desarrollo
composer.lock          # Lock file (opcional no subir)
README.md              # Documentación
docs/                  # Documentación interna
artisan                # CLI Laravel (no funcional en hosting)
```

### ✅ Total estimado a subir: ~80-120MB

---

## 🎯 PRÓXIMOS PASOS DESPUÉS DEL DESPLIEGUE

1. **Probar funcionalidad completa**
2. **Verificar logs por errores**
3. **Configurar backups regulares**
4. **Documentar URL final para usuarios**
5. **Configurar monitoreo básico**

---

## 📞 CONTACTO DE EMERGENCIA

Si encuentras problemas:
1. **Revisar logs:** `/Parroquia/storage/logs/laravel.log`
2. **Verificar permisos:** storage/ y bootstrap/cache/
3. **Consultar documentación:** `CONFIGURACION_PERMISOS.md`

---

**URL FINAL DE ACCESO:**
```
🌐 https://giandei.orgfree.com/Parroquia/public/
```