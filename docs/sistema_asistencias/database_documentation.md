# 📋 Sistema de Registro de Asistencias - Primera Comunión

## 📖 Tabla de Contenidos

1. [Resumen Ejecutivo](#resumen-ejecutivo)
2. [Arquitectura de la Base de Datos](#arquitectura-de-la-base-de-datos)
3. [Especificaciones Técnicas](#especificaciones-técnicas)
4. [Descripción Detallada de Tablas](#descripción-detallada-de-tablas)
5. [Relaciones y Constraints](#relaciones-y-constraints)
6. [Sistema de Códigos QR](#sistema-de-códigos-qr)
7. [Flujo de Trabajo](#flujo-de-trabajo)
8. [Guía de Implementación](#guía-de-implementación)
9. [APIs y Endpoints Recomendados](#apis-y-endpoints-recomendados)
10. [Consideraciones de Seguridad](#consideraciones-de-seguridad)
11. [Escalabilidad y Mantenimiento](#escalabilidad-y-mantenimiento)

---

## 🎯 Resumen Ejecutivo

Este sistema está diseñado para gestionar el registro de asistencias de estudiantes de Primera Comunión mediante códigos QR. La solución permite el seguimiento eficiente de la participación estudiantil con una interfaz moderna y tecnología de escaneo QR.

### Características Principales

- ✅ **78 estudiantes registrados** distribuidos en 2 grupos (A: 40, B: 38)
- ✅ **Sistema de códigos QR únicos** para cada estudiante
- ✅ **Gestión de sesiones de asistencia** con responsables definidos
- ✅ **Manejo de apellidos compuestos** y caracteres especiales
- ✅ **Estados de asistencia** (presente, ausente, tardío, justificado)
- ✅ **Trazabilidad completa** de registros y modificaciones

---

## 🏗️ Arquitectura de la Base de Datos

### Diagrama de Entidad-Relación

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│ user_types  │────│   users     │────│attendance_  │
│             │    │             │    │sessions     │
└─────────────┘    └─────────────┘    └─────┬───────┘
                                             │
┌─────────────┐    ┌─────────────┐          │
│   groups    │────│  students   │          │
│             │    │             │          │
└─────────────┘    └─────┬───────┘          │
                         │                  │
                         │    ┌─────────────┤
                         └────│ attendances │
                              │             │
                              └─────────────┘
```

### Principios de Diseño

1. **Normalización**: Tercera forma normal (3NF) para evitar redundancia
2. **Integridad Referencial**: Foreign keys con constraints activos
3. **Escalabilidad**: Índices optimizados para consultas frecuentes
4. **Flexibilidad**: Campos opcionales para extensibilidad futura

---

## ⚙️ Especificaciones Técnicas

### Configuración del Servidor

- **Motor de Base de Datos**: MariaDB 10.4.32+
- **Charset**: UTF8MB4 (soporte completo Unicode)
- **Collation**: utf8mb4_unicode_ci
- **Engine**: InnoDB (transacciones ACID)
- **Framework**: Laravel 11.x

### Requisitos del Sistema

- **PHP**: 8.2+
- **Laravel**: 11.x
- **Base de Datos**: MariaDB 10.4+ / MySQL 8.0+
- **Memoria**: Mínimo 512MB RAM
- **Almacenamiento**: 100MB para datos base

---

## 📊 Descripción Detallada de Tablas

### 1. `user_types` - Tipos de Usuario

Tabla de lookup que define los roles en el sistema.

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Identificador único |
| `name` | VARCHAR(255) | NOT NULL, UNIQUE | Nombre del tipo (Admin, Profesor, Staff) |
| `description` | VARCHAR(255) | NULLABLE | Descripción del rol |
| `estado` | ENUM | DEFAULT 'ACTIVO', NULLABLE | Estado del registro (ACTIVO, INACTIVO, ELIMINADO) |
| `unique_id` | CHAR(36) | UNIQUE, DEFAULT uuid() | UUID único generado automáticamente |
| `created_at` | TIMESTAMP | DEFAULT current_timestamp() | Fecha de creación automática |
| `updated_at` | TIMESTAMP | DEFAULT current_timestamp() ON UPDATE | Fecha de actualización automática |

**Datos Iniciales:**
```sql
INSERT INTO user_types (name, description) VALUES
('Admin', 'Administrador del sistema con acceso completo'),
('Profesor', 'Profesor responsable de grupos de estudiantes'),
('Staff', 'Personal de apoyo con acceso limitado');
```

---

### 2. `users` - Usuarios del Sistema

Almacena información de personas autorizadas para gestionar asistencias.

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Identificador único |
| `user_type_id` | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY | Relación con tipos de usuario |
| `name` | VARCHAR(255) | NOT NULL | Nombre completo del usuario |
| `email` | VARCHAR(255) | NULLABLE, UNIQUE | Correo electrónico (opcional) |
| `email_verified_at` | TIMESTAMP | NULLABLE | Verificación de email |
| `password` | VARCHAR(255) | NULLABLE | Hash de contraseña |
| `remember_token` | VARCHAR(100) | NULLABLE | Token de sesión persistente |
| `estado` | ENUM | DEFAULT 'ACTIVO', NULLABLE | Estado del registro (ACTIVO, INACTIVO, ELIMINADO) |
| `unique_id` | CHAR(36) | UNIQUE, DEFAULT uuid() | UUID único generado automáticamente |
| `created_at` | TIMESTAMP | DEFAULT current_timestamp() | Fecha de creación automática |
| `updated_at` | TIMESTAMP | DEFAULT current_timestamp() ON UPDATE | Fecha de actualización automática |

**Relaciones:**
- `user_type_id` → `user_types.id`

---

### 3. `groups` - Grupos de Estudiantes

Define los grupos organizacionales de estudiantes.

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Identificador único |
| `code` | VARCHAR(10) | NOT NULL, UNIQUE | Código corto (A, B) |
| `name` | VARCHAR(255) | NOT NULL | Nombre descriptivo |
| `description` | VARCHAR(255) | NULLABLE | Descripción adicional |
| `estado` | ENUM | DEFAULT 'ACTIVO', NULLABLE | Estado del registro (ACTIVO, INACTIVO, ELIMINADO) |
| `unique_id` | CHAR(36) | UNIQUE, DEFAULT uuid() | UUID único generado automáticamente |
| `created_at` | TIMESTAMP | DEFAULT current_timestamp() | Fecha de creación automática |
| `updated_at` | TIMESTAMP | DEFAULT current_timestamp() ON UPDATE | Fecha de actualización automática |

**Datos Iniciales:**
```sql
INSERT INTO groups (code, name, description) VALUES
('A', 'Grupo A', 'Primer grupo de Primera Comunión 2025'),
('B', 'Grupo B', 'Segundo grupo de Primera Comunión 2025');
```

---

### 4. `students` - Estudiantes Registrados ⭐

Tabla principal que almacena información de los 78 estudiantes.

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Identificador único |
| `group_id` | BIGINT UNSIGNED | NULLABLE, FOREIGN KEY | Grupo asignado |
| `names` | VARCHAR(255) | NOT NULL | Nombres completos |
| `paternal_surname` | VARCHAR(255) | NOT NULL | Apellido paterno |
| `maternal_surname` | VARCHAR(255) | NULLABLE | Apellido materno |
| `order_number` | INT | NOT NULL | Número de orden en lista |
| `student_code` | VARCHAR(255) | NULLABLE, UNIQUE | **Código QR único** |
| `estado` | ENUM | DEFAULT 'ACTIVO', NULLABLE | Estado del registro (ACTIVO, INACTIVO, ELIMINADO) |
| `unique_id` | CHAR(36) | UNIQUE, DEFAULT uuid() | UUID único generado automáticamente |
| `created_at` | TIMESTAMP | DEFAULT current_timestamp() | Fecha de creación automática |
| `updated_at` | TIMESTAMP | DEFAULT current_timestamp() ON UPDATE | Fecha de actualización automática |

**Índices Optimizados:**
- `students_group_id_order_number_unique` - Evita duplicados en listas
- `students_student_code_unique` - Garantiza códigos QR únicos
- `students_paternal_surname_index` - Búsqueda por apellido paterno
- `students_paternal_surname_maternal_surname_index` - Búsqueda por apellidos completos

**Relaciones:**
- `group_id` → `groups.id`

---

### 5. `attendance_sessions` - Sesiones de Asistencia

Define las sesiones donde se registran asistencias (clases, eventos, etc.).

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Identificador único |
| `created_by` | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY | Responsable/creador |
| `date` | DATE | NOT NULL | Fecha de la sesión |
| `time` | TIME | NULLABLE | Hora de inicio |
| `title` | VARCHAR(255) | NULLABLE | Título de la sesión |
| `notes` | TEXT | NULLABLE | Observaciones generales |
| `estado` | ENUM | DEFAULT 'ACTIVO', NULLABLE | Estado del registro (ACTIVO, INACTIVO, ELIMINADO) |
| `unique_id` | CHAR(36) | UNIQUE, DEFAULT uuid() | UUID único generado automáticamente |
| `created_at` | TIMESTAMP | DEFAULT current_timestamp() | Fecha de creación automática |
| `updated_at` | TIMESTAMP | DEFAULT current_timestamp() ON UPDATE | Fecha de actualización automática |

**Índices:**
- `attendance_sessions_date_index` - Búsqueda eficiente por fecha

**Relaciones:**
- `created_by` → `users.id`

---

### 6. `attendances` - Registros de Asistencia 🎯

Tabla transaccional que registra la asistencia individual de cada estudiante por sesión.

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Identificador único |
| `attendance_session_id` | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY | Sesión de referencia |
| `student_id` | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY | Estudiante evaluado |
| `status` | ENUM | NOT NULL | Estado de asistencia |
| `notes` | TEXT | NULLABLE | Observaciones específicas |
| `estado` | ENUM | DEFAULT 'ACTIVO', NULLABLE | Estado del registro (ACTIVO, INACTIVO, ELIMINADO) |
| `unique_id` | CHAR(36) | UNIQUE, DEFAULT uuid() | UUID único generado automáticamente |
| `created_at` | TIMESTAMP | DEFAULT current_timestamp() | Fecha de creación automática |
| `updated_at` | TIMESTAMP | DEFAULT current_timestamp() ON UPDATE | Fecha de actualización automática |

**Estados de Asistencia:**
- `'present'` - Presente (asistió normalmente)
- `'absent'` - Ausente (no asistió)
- `'late'` - Tardío (llegó tarde)
- `'justified'` - Justificado (ausencia con justificación)

**Constraints Únicos:**
- `attendances_attendance_session_id_student_id_unique` - Un estudiante por sesión

**Índices:**
- `attendances_status_index` - Reportes por estado de asistencia

**Relaciones:**
- `attendance_session_id` → `attendance_sessions.id`
- `student_id` → `students.id`

---

## 🔗 Relaciones y Constraints

### Diagrama de Foreign Keys

```
user_types (1) ←→ (N) users
     ↓
users (1) ←→ (N) attendance_sessions
     ↓
attendance_sessions (1) ←→ (N) attendances
     ↓
students (1) ←→ (N) attendances
     ↑
groups (1) ←→ (N) students
```

### Reglas de Integridad

1. **Cascada**: No implementada intencionalmente para preservar datos históricos
2. **Restrict**: Previene eliminación de registros con dependencias
3. **Unique Constraints**: Garantizan unicidad en códigos y combinaciones críticas

---

## 📱 Sistema de Códigos QR

### Algoritmo de Generación

El sistema genera códigos únicos siguiendo el patrón:
**`{GRUPO}-{NOMBRE}-{SILABA_PATERNO}-{SILABA_MATERNO}`**

#### Ejemplo de Transformación

| Datos Originales | Código Generado |
|------------------|-----------------|
| Grupo A, "Antony Alexander", "Alférez", "Vilchez" | `A-ANTONY-ALF-VILCH` |
| Grupo B, "María Alejandra", "Paredes", "Berrios" | `B-MARIA-PAR-BERR` |
| Grupo B, "Jhadde Anahi", "Chino", "de la Cruz" | `B-JHADDE-CHIN-DE` |

#### Reglas de Normalización

1. **Eliminación de tildes**: á→a, é→e, í→i, ó→o, ú→u
2. **Manejo de eñes**: ñ→Ñ (conserva el carácter, convierte a mayúscula)
3. **Apellidos compuestos**: Corta en el primer espacio ("de la Cruz" → "de")
4. **Extracción de sílabas**: Algoritmo basado en detección de vocales
5. **Nombres múltiples**: Solo toma el primer nombre

#### Código de Implementación

```php
/**
 * Genera código QR basado en sílabas
 */
private function generateStudentCode($groupCode, $names, $paternalSurname, $maternalSurname): string
{
    $firstName = $this->getFirstName($names);
    $paternalSyllable = $this->getFirstSyllable($paternalSurname);
    $maternalSyllable = $this->getFirstSyllable($maternalSurname);
    
    return "{$groupCode}-{$firstName}-{$paternalSyllable}-{$maternalSyllable}";
}

/**
 * Extrae primera sílaba con manejo de UTF-8
 */
private function getFirstSyllable($word): string
{
    $word = $this->normalizeText(trim($word));
    
    // Apellidos compuestos: cortar en primer espacio
    $spacePos = strpos($word, ' ');
    if ($spacePos !== false) {
        $word = substr($word, 0, $spacePos);
    }
    
    $vowels = ['A', 'E', 'I', 'O', 'U', 'a', 'e', 'i', 'o', 'u'];
    $syllable = '';
    $vowelCount = 0;
    
    $length = mb_strlen($word);
    for ($i = 0; $i < $length; $i++) {
        $char = mb_substr($word, $i, 1);
        
        if (in_array($char, $vowels)) {
            $vowelCount++;
            if ($vowelCount == 2) break; // Segunda vocal = fin de sílaba
        }
        
        $syllable .= $char;
    }
    
    return $this->toUpperCase($syllable);
}
```

### Características de los Códigos QR

- **Longitud**: 15-25 caracteres (óptimo para QR)
- **Unicidad**: Garantizada por constraint de base de datos
- **Legibilidad**: Fácil identificación visual
- **Escalabilidad**: Hasta 4,296 caracteres soportados por QR

---

## 🔄 Flujo de Trabajo

### 1. Gestión de Estudiantes

```mermaid
graph TD
    A[Carga de Listas] → B[Normalización de Nombres]
    B → C[Generación de Códigos QR]
    C → D[Validación de Unicidad]
    D → E[Almacenamiento en BD]
    E → F[Generación de QR Físicos]
```

### 2. Registro de Asistencias

```mermaid
graph TD
    A[Usuario Crea Sesión] → B[Define Fecha/Hora/Título]
    B → C[Sistema Lista Estudiantes]
    C → D[Escaneo QR / Marcado Manual]
    D → E[Registro en Base de Datos]
    E → F[Generación de Reportes]
```

### 3. Consultas y Reportes

```mermaid
graph TD
    A[Selección de Criterios] → B[Filtro por Fecha/Grupo/Estado]
    B → C[Consulta Optimizada]
    C → D[Cálculo de Estadísticas]
    D → E[Exportación de Datos]
```
---

## 📡 APIs y Endpoints Recomendados

### Autenticación

```php
POST   /api/auth/login
POST   /api/auth/logout
GET    /api/auth/me
```

### Gestión de Estudiantes

```php
GET    /api/students              # Listar todos
GET    /api/students/{id}         # Detalle específico
GET    /api/students/group/{groupId}  # Por grupo
GET    /api/students/search?q=    # Búsqueda
POST   /api/students/qr-scan      # Escaneo QR
```

### Sesiones de Asistencia

```php
GET    /api/attendance-sessions           # Listar sesiones
POST   /api/attendance-sessions           # Crear sesión
GET    /api/attendance-sessions/{id}      # Detalle sesión
PUT    /api/attendance-sessions/{id}      # Actualizar sesión
DELETE /api/attendance-sessions/{id}      # Eliminar sesión
```

### Registro de Asistencias

```php
GET    /api/attendances/session/{sessionId}     # Por sesión
POST   /api/attendances                         # Registrar asistencia
PUT    /api/attendances/{id}                    # Actualizar registro
GET    /api/attendances/student/{studentId}     # Historial estudiante
```

### Reportes y Estadísticas

```php
GET    /api/reports/attendance/summary          # Resumen general
GET    /api/reports/attendance/by-date         # Por fecha
GET    /api/reports/attendance/by-group        # Por grupo
GET    /api/reports/students/statistics        # Estadísticas estudiantes
POST   /api/reports/export                     # Exportar datos
```

### Ejemplo de Respuesta JSON

```json
{
  "success": true,
  "data": {
    "id": 1,
    "student_code": "A-ANTONY-ALF-VILCH",
    "names": "Antony Alexander",
    "paternal_surname": "Alférez",
    "maternal_surname": "Vilchez",
    "group": {
      "code": "A",
      "name": "Grupo A"
    },
    "order_number": 1,
    "total_sessions": 15,
    "present_count": 13,
    "absent_count": 2,
    "attendance_rate": 86.67
  },
  "meta": {
    "timestamp": "2025-08-16T10:30:00Z",
    "version": "1.0"
  }
}
```

---

## 🔐 Consideraciones de Seguridad

### Autenticación y Autorización

1. **Laravel Sanctum**: Tokens API seguros
2. **Roles y Permisos**: Basado en `user_types`
3. **Rate Limiting**: Prevención de abuso de APIs
4. **CORS**: Configuración restrictiva para frontend

### Protección de Datos

```php
// Middleware de autenticación
'auth:sanctum'

// Validación de permisos
Gate::define('manage-attendances', function ($user) {
    return in_array($user->user_type->name, ['Admin', 'Profesor']);
});

// Sanitización de inputs
$request->validate([
    'student_code' => 'required|string|exists:students,student_code',
    'status' => 'required|in:present,absent,late,justified'
]);
```

### Auditoría y Logs

- **Laravel Log**: Registro de operaciones críticas
- **Timestamps**: Tracking automático de cambios
- **Soft Deletes**: Opcional para preservar histórico

---

## 📈 Escalabilidad y Mantenimiento

### Optimizaciones de Performance

1. **Índices Estratégicos**:
   - Búsquedas por apellido
   - Filtros por fecha
   - Consultas por grupo

2. **Consultas Optimizadas**:
```sql
-- Reporte de asistencia por sesión (optimizado)
SELECT 
    s.student_code,
    s.names,
    s.paternal_surname,
    a.status,
    g.name as group_name
FROM attendances a
INNER JOIN students s ON a.student_id = s.id
INNER JOIN groups g ON s.group_id = g.id
WHERE a.attendance_session_id = ?
ORDER BY g.code, s.order_number;
```

3. **Caching**: Redis para consultas frecuentes

### Backup y Recuperación

```bash
# Backup diario automatizado
mysqldump -u user -p lista_ninios_2025 > backup_$(date +%Y%m%d).sql

# Restauración
mysql -u user -p lista_ninios_2025 < backup_20250816.sql
```

### Monitoring y Métricas

- **Sesiones activas**: Conteo en tiempo real
- **Estudiantes sin registrar**: Alertas automáticas
- **Performance de escaneo QR**: Métricas de velocidad

---

## 📚 Referencias

- [Laravel Documentation](https://laravel.com/docs)
- [MariaDB Reference](https://mariadb.com/kb/)
- [QR Code Standards](https://www.qrcode.com/en/)
- [UTF-8 Best Practices](https://www.w3.org/International/articles/definitions-characters/)

---

**© 2025 Sistema de Asistencias Primera Comunión**  
*Documentación técnica completa - Versión 1.0*