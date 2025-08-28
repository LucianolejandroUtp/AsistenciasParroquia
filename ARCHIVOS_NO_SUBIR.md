# ============================================
# ARCHIVOS Y DIRECTORIOS A NO SUBIR AL HOSTING
# Lista para referencia durante el despliegue FTP
# ============================================

# DIRECTORIOS DE DESARROLLO (NO SUBIR)
# ----------------------------------------
node_modules/           # Dependencias Node.js (muy pesado)
.git/                   # Repositorio Git completo
tests/                  # Pruebas unitarias y de integración
.vscode/                # Configuración de Visual Studio Code
.idea/                  # Configuración de PHPStorm/IntelliJ

# ARCHIVOS DE CONFIGURACIÓN DE DESARROLLO (NO SUBIR)
# ------------------------------------------------
.env                    # Archivo de entorno local
.env.example            # Ejemplo de configuración
.gitignore              # Reglas de Git
.gitattributes          # Atributos de Git
phpunit.xml             # Configuración de tests
composer.lock           # Lock file (opcional, pero recomendado no subir)
package-lock.json       # Lock file de npm
package.json            # Configuración de npm
vite.config.js          # Configuración de Vite
README.md               # Documentación del proyecto
LICENSE.txt             # Archivo de licencia

# ARCHIVOS DE LOGS Y CACHE LOCAL (NO SUBIR)
# ----------------------------------------
storage/logs/*          # Logs locales (mantener estructura, no contenido)
storage/framework/cache/data/*  # Cache local
storage/framework/sessions/*    # Sesiones locales
storage/framework/views/*       # Vistas compiladas locales
bootstrap/cache/*       # Cache de bootstrap local

# ARCHIVOS DE DESARROLLO ESPECÍFICOS (NO SUBIR)
# --------------------------------------------
artisan                 # CLI de Laravel (no funcional en hosting compartido)
*.log                   # Archivos de log sueltos
.DS_Store               # Archivos de macOS
Thumbs.db               # Archivos de Windows
*.swp                   # Archivos temporales de Vim
*.swo                   # Archivos temporales de Vim
*~                      # Archivos de respaldo

# DOCUMENTACIÓN INTERNA (NO SUBIR)
# --------------------------------
docs/                   # Documentación del proyecto

# ============================================
# ARCHIVOS QUE SÍ SE DEBEN SUBIR
# ============================================

# ESTRUCTURA LARAVEL PRINCIPAL
app/                    # Lógica de la aplicación
bootstrap/              # Archivos de arranque (sin cache)
config/                 # Configuraciones
database/               # Migraciones y seeders
public/                 # Archivos públicos web
resources/              # Vistas, assets sin compilar
routes/                 # Definición de rutas
storage/                # Almacenamiento (con estructura, sin contenido)
vendor/                 # Dependencias de Composer

# ARCHIVOS DE CONFIGURACIÓN ESPECÍFICOS
.env.production         # Renombrar a .env en el servidor
public/.htaccess.production  # Renombrar a .htaccess en el servidor

# ============================================
# NOTAS IMPORTANTES
# ============================================

# 1. Renombrar archivos en servidor:
#    .env.production → .env
#    public/.htaccess.production → public/.htaccess

# 2. Permisos necesarios en servidor:
#    storage/ → 755 o 775
#    bootstrap/cache/ → 755 o 775

# 3. Estructura de storage a mantener:
#    storage/app/
#    storage/app/public/
#    storage/framework/
#    storage/framework/cache/
#    storage/framework/sessions/
#    storage/framework/views/
#    storage/logs/

# 4. Tamaño total estimado después de exclusiones: ~50-100MB
#    (vs ~200-400MB con node_modules incluido)