# 📋 Sistema de Asistencias - Primera Comunión

**Sistema de gestión de asistencias para estudiantes de Primera Comunión** desarrollado con Laravel 12.0 y template TinyDash Bootstrap.

## 🎯 **Características Principales**

- **👥 Gestión de 78 estudiantes** divididos en 2 grupos (Grupo A: 40, Grupo B: 38)
- **📱 Sistema QR integrado** para registro rápido de asistencias
- **🎨 Interfaz moderna** con template TinyDash Bootstrap responsivo
- **📊 Reportes y estadísticas** de asistencia detallados
- **🔐 Sistema de autenticación** con roles (Admin, Profesor, Staff)

## 🛠️ **Stack Tecnológico**

- **Backend:** Laravel 12.0 + PHP 8.2+
- **Frontend:** TinyDash Bootstrap + Vite + TailwindCSS
- **Base de Datos:** MySQL/MariaDB con esquema personalizado
- **Códigos QR:** Sistema único basado en sílabas
- **Hot Reload:** Desarrollo moderno con recarga automática

## 🚀 **Inicio Rápido**

### **Prerrequisitos**
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL/MariaDB

### **Instalación**
```bash
# Clonar e instalar dependencias
git clone <repository>
cd AsistenciasParroquia
composer install
npm install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Base de datos
php artisan migrate
php artisan db:seed

# Iniciar desarrollo
composer run dev  # Servidor + Queue + Vite con hot reload
```

## 📚 **Documentación Completa**

### **📋 Requisitos y Planificación**
- [📝 Requisitos Funcionales](docs/sistema_asistencias/01_requisitos_funcionales.md)
- [🎯 Casos de Uso](docs/sistema_asistencias/02_casos_uso.md)
- [📊 Documentación de Base de Datos](docs/sistema_asistencias/database_documentation.md)
- [🚀 Plan de Implementación por Fases](docs/plan_implementacion/PLAN_FASES_IMPLEMENTACION.md)

### **🔧 Desarrollo y Configuración**
- [⚙️ Flujo de Desarrollo Laravel 12](docs/desarrollo/desarrollo_actualizado.md)
- [🛠️ Integración de Herramientas MCP](docs/desarrollo/mcp_integration_update.md)
- [📁 Estructura de Documentación](docs/desarrollo/documentacion_reorganizada.md)

### **🎨 Recursos de Desarrollo**
- [📁 Templates y Estilos](docs/recursos_desarrollo/templates_estilos/) - Material de referencia TinyDash
- [💡 Ejemplos de Código](docs/recursos_desarrollo/ejemplos_codigo/)
- [🔗 Referencias Externas](docs/recursos_desarrollo/referencias_externas/)

## 🌟 **Funcionalidades del Sistema**

- **📱 Escáner QR integrado** para registro de asistencias
- **👥 Gestión completa de estudiantes** con información detallada
- **📅 Sesiones de catequesis** programables y administrables
- **📊 Dashboard con estadísticas** y visualización de datos
- **📋 Reportes en PDF/Excel** para administración parroquial
- **🔒 Autenticación robusta** con control de acceso por roles

## 🚦 **Estado del Proyecto**

### **✅ Completado (Fase 1.1 - 1.2)**
- ✅ Esquema de base de datos con migraciones
- ✅ Seeders con generación automática de códigos QR
- ✅ Integración completa del template TinyDash
- ✅ Configuración del entorno de desarrollo optimizado
- ✅ Dashboard principal con estadísticas básicas

### **🔄 En Desarrollo (Fase 1.3)**
- 🔄 Sistema de autenticación completo
- 🔄 Middleware de autorización por roles

### **📋 Pendiente**
- 📋 Sistema completo de escáner QR
- 📋 Módulo de reportes avanzados
- 📋 Optimización para tablets y móviles

## 👨‍💻 **Contribución**

Este proyecto sigue las directrices de [Conventional Commits](docs/desarrollo/) y utiliza un flujo de desarrollo moderno con herramientas MCP integradas.

## 📄 **Licencia**

Proyecto desarrollado para uso parroquial. Consultar términos específicos de uso.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
