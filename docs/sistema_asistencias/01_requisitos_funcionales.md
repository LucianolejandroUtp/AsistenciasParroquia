# Requisitos Funcionales - Sistema de Asistencias Primera Comunión

## Introducción

Este documento define los requisitos funcionales detallados del sistema de asistencias para estudiantes de Primera Comunión en la parroquia, especificando las funcionalidades que debe cumplir el sistema para satisfacer las necesidades de registro y seguimiento de asistencias de los 78 estudiantes distribuidos en 2 grupos.

## Actores del Sistema

### 1. **Staff Parroquial**
- **Descripción**: Personal de apoyo de la parroquia que asiste en el registro de asistencias
- **Permisos**: Marcar asistencias de estudiantes mediante escaneo QR, consultar listas de sesiones activas
- **Limitaciones**: Solo funciones de registro durante sesiones, sin administración de datos

### 2. **Catequista/Coordinador**
- **Descripción**: Responsable directo de la enseñanza y coordinación de grupos de Primera Comunión
- **Permisos**: Crear sesiones de catequesis, registrar asistencias, generar reportes de grupos, gestionar justificaciones
- **Limitaciones**: Gestión limitada a estudiantes de Primera Comunión

### 3. **Administrador Parroquial**
- **Descripción**: Responsable general del sistema y coordinador pastoral de Primera Comunión
- **Permisos**: Acceso completo al sistema, gestión de usuarios, configuración, reportes completos
- **Limitaciones**: Ninguna

## Módulos Funcionales

### 1. **Módulo de Gestión de Estudiantes y Usuarios**

#### RF-001: Gestión de Estudiantes
**Descripción**: El sistema debe permitir consultar y gestionar información de estudiantes de Primera Comunión.

**Criterios de Aceptación**:
- Mostrar lista completa de 78 estudiantes registrados
- Filtrar estudiantes por grupo (A: 40 estudiantes, B: 38 estudiantes)
- Buscar estudiantes por nombre, apellido o código QR
- Mostrar información completa: nombres, apellidos, grupo, número de orden
- Visualizar código QR único de cada estudiante

**Prioridad**: Alta
**Complejidad**: Media

#### RF-002: Sistema de Códigos QR
**Descripción**: El sistema debe manejar códigos QR únicos para identificación rápida de estudiantes.

**Criterios de Aceptación**:
- Mostrar código QR generado para cada estudiante
- Permitir escaneo de códigos QR desde dispositivos móviles/web
- Validar unicidad de códigos en el sistema
- Búsqueda instantánea de estudiante por código QR escaneado
- Manejo de errores para códigos no válidos

**Prioridad**: Alta
**Complejidad**: Alta

#### RF-003: Gestión de Grupos de Primera Comunión
**Descripción**: El sistema debe manejar la organización de estudiantes en grupos.

**Criterios de Aceptación**:
- Visualizar información de Grupo A (40 estudiantes) y Grupo B (38 estudiantes)
- Generar reportes específicos por grupo
- Permitir filtrado de funcionalidades por grupo
- Mostrar estadísticas comparativas entre grupos

**Prioridad**: Media
**Complejidad**: Baja

#### RF-004: Gestión de Usuarios del Sistema
**Descripción**: El sistema debe permitir gestionar usuarios con roles de Staff, Profesor y Administrador.

**Criterios de Aceptación**:
- Autenticación segura de usuarios
- Asignar roles: Staff, Profesor, Administrador
- Control de acceso basado en roles
- Gestión de sesiones de usuario
- Registro de actividad de usuarios

**Prioridad**: Alta
**Complejidad**: Alta

### 2. **Módulo de Registro de Asistencias de Estudiantes**

#### RF-005: Crear Sesiones de Catequesis
**Descripción**: El sistema debe permitir crear sesiones de catequesis donde se registrará la asistencia de estudiantes (clases, ensayos de ceremonia, retiros, actividades especiales).

**Criterios de Aceptación**:
- Crear sesiones con fecha, hora, título descriptivo (ej: "Catequesis - Los Sacramentos") y observaciones
- Asignar catequista responsable de la sesión
- Configurar sesión para Grupo A, Grupo B, o ambos grupos
- Generar lista automática de estudiantes esperados según grupo(s) seleccionado(s)
- Permitir edición y eliminación de sesiones no iniciadas

**Prioridad**: Crítica
**Complejidad**: Media

#### RF-006: Registrar Asistencia Individual
**Descripción**: El sistema debe permitir marcar la asistencia de cada estudiante en una sesión.

**Criterios de Aceptación**:
- Marcar asistencia con estados: presente, ausente, tardío, justificado
- Permitir registro manual desde lista de estudiantes
- Permitir registro por escaneo de código QR
- Añadir notas específicas por estudiante en cada sesión
- Un estudiante por sesión (constraint de unicidad)

**Prioridad**: Crítica
**Complejidad**: Media

#### RF-007: Marcado Rápido por Código QR
**Descripción**: El sistema debe permitir registro rápido de asistencia mediante escaneo de códigos QR únicos de cada estudiante.

**Criterios de Aceptación**:
- Interfaz de escaneo QR optimizada para tablets y móviles
- Identificación automática del estudiante y su grupo al escanear
- Marcado instantáneo como 'presente' con confirmación visual
- Mostrar nombre completo y grupo del estudiante escaneado
- Manejo de errores para códigos QR inválidos o estudiantes ya registrados
- Posibilidad de escaneo masivo durante entrada a sesiones

**Prioridad**: Alta
**Complejidad**: Alta

#### RF-008: Gestión de Justificaciones
**Descripción**: El sistema debe permitir justificar ausencias y llegadas tardías de estudiantes.

**Criterios de Aceptación**:
- Cambiar estado de ausencia a justificado
- Registrar motivo de la justificación
- Mantener historial de justificaciones por estudiante
- Diferentes tipos de justificación (médica, familiar, etc.)
- Aprobación de justificaciones por Profesor o Administrador

**Prioridad**: Alta
**Complejidad**: Media

### 3. **Módulo de Reportes y Consultas de Asistencia**

#### RF-009: Reporte de Asistencias por Sesión de Catequesis
**Descripción**: El sistema debe generar reportes detallados de asistencia para cada sesión de catequesis.

**Criterios de Aceptación**:
- Mostrar lista completa de los 78 estudiantes organizados por grupo
- Incluir estado de asistencia (presente, ausente, tardío, justificado)
- Filtrar por Grupo A (40 estudiantes) o Grupo B (38 estudiantes)
- Exportar reportes en Excel y PDF para coordinación parroquial
- Mostrar estadísticas por sesión: total presentes, ausentes, porcentaje de asistencia
- Incluir información del catequista responsable y fecha/hora de la sesión

**Prioridad**: Alta
**Complejidad**: Media

#### RF-010: Historial de Asistencias por Estudiante
**Descripción**: El sistema debe generar reportes individuales de asistencia por estudiante.

**Criterios de Aceptación**:
- Mostrar historial completo de un estudiante específico
- Calcular porcentaje de asistencia general
- Contar total de sesiones, presencias, ausencias, tardías
- Incluir justificaciones registradas
- Exportar certificado de asistencia individual

**Prioridad**: Alta
**Complejidad**: Media

#### RF-011: Dashboard Pastoral de Primera Comunión
**Descripción**: El sistema debe proporcionar un dashboard con métricas pastorales relevantes para el seguimiento de la preparación.

**Criterios de Aceptación**:
- Indicadores de asistencia general por grupo (A: 40, B: 38 estudiantes)
- Gráficos de evolución de asistencia durante el proceso de preparación
- Lista de estudiantes con asistencia insuficiente para Primera Comunión
- Comparativas de participación entre Grupo A y Grupo B
- Estadísticas de sesiones de catequesis, ensayos y retiros realizados
- Alertas para estudiantes que requieren atención pastoral especial

**Prioridad**: Media
**Complejidad**: Alta

#### RF-012: Reportes por Rango de Fechas
**Descripción**: Los usuarios deben poder generar reportes de períodos específicos.

**Criterios de Aceptación**:
- Filtrar asistencias por rango de fechas personalizado
- Generar reportes mensuales, semanales o personalizados
- Incluir estadísticas comparativas del período
- Exportar datos del período seleccionado
- Mostrar tendencias y patrones en el período

**Prioridad**: Media
**Complejidad**: Media

### 4. **Módulo de Configuración del Sistema**

#### RF-013: Configuración de Parámetros del Sistema
**Descripción**: El sistema debe permitir configurar parámetros operativos para el manejo de asistencias.

**Criterios de Aceptación**:
- Configurar tipos de justificaciones disponibles
- Definir parámetros de notificaciones
- Establecer políticas de exportación de datos
- Configurar formatos de reportes
- Gestionar configuraciones de códigos QR

**Prioridad**: Media
**Complejidad**: Media

#### RF-014: Gestión de Fechas Especiales
**Descripción**: El sistema debe manejar días especiales y eventos importantes.

**Criterios de Aceptación**:
- Registrar fechas de eventos especiales de Primera Comunión
- Marcar días de vacaciones o suspensiones
- Configurar sesiones especiales (ensayos, ceremonias)
- Generar calendario de actividades anuales
- Exclusión de fechas especiales en cálculos estadísticos

**Prioridad**: Media
**Complejidad**: Baja

#### RF-015: Configuración de Notificaciones
**Descripción**: El sistema debe permitir configurar notificaciones automáticas.

**Criterios de Aceptación**:
- Configurar notificaciones por email (opcional)
- Definir alerts para patrones de ausencia
- Personalizar mensajes de notificación
- Configurar destinatarios por tipo de alerta
- Registro de notificaciones enviadas

**Prioridad**: Baja
**Complejidad**: Media

### 5. **Módulo de Exportación e Integración**

#### RF-016: Exportación de Datos Educativos
**Descripción**: El sistema debe permitir exportar datos de asistencia en diferentes formatos.

**Criterios de Aceptación**:
- Exportar reportes de asistencia en Excel, PDF, CSV
- Generar certificados individuales de asistencia
- Exportar listas de estudiantes con estadísticas
- Configurar plantillas de exportación personalizadas
- Validar integridad de datos exportados

**Prioridad**: Media
**Complejidad**: Media

#### RF-017: API para Consultas Externas
**Descripción**: El sistema debe proporcionar una API REST para integraciones futuras.

**Criterios de Aceptación**:
- Endpoints para consulta de asistencias por estudiante
- Endpoints para estadísticas generales
- Autenticación y autorización de API
- Documentación básica de API
- Logs de acceso y uso

**Prioridad**: Baja
**Complejidad**: Alta

#### RF-018: Integración con Sistemas Parroquiales
**Descripción**: El sistema debe facilitar integración con otros sistemas de la parroquia.

**Criterios de Aceptación**:
- Exportar datos en formatos estándar
- Sincronización manual de información estudiantil
- Intercambio de reportes con coordinación
- Backup automático de datos críticos
- Validación de datos antes de exportar

**Prioridad**: Baja
**Complejidad**: Media

## Reglas de Negocio para Primera Comunión

### RN-001: Validación de Sesiones de Catequesis
- Solo se pueden crear sesiones para fechas presentes o futuras
- Una sesión debe tener un título descriptivo de la actividad pastoral
- No se pueden eliminar sesiones que ya tengan asistencias registradas
- El catequista responsable debe ser un usuario autenticado
- Las sesiones deben estar asociadas a al menos un grupo (A o B)

### RN-002: Registro de Asistencias en Catequesis
- Un estudiante solo puede tener un registro de asistencia por sesión
- Los estados válidos son: presente, ausente, tardío, justificado
- No se pueden registrar asistencias para sesiones futuras
- Solo catequistas y administradores pueden modificar asistencias existentes
- Los códigos QR deben ser validados contra la base de estudiantes activos

### RN-003: Justificaciones Pastorales
- Las justificaciones pueden registrarse hasta 7 días después de la sesión
- Solo se pueden justificar ausencias o llegadas tardías
- Las justificaciones requieren un motivo mínimo de 10 caracteres
- Catequistas pueden aprobar justificaciones de estudiantes de cualquier grupo
- Se deben registrar justificaciones por motivos médicos, familiares o escolares

### RN-004: Sistema de Códigos QR para Estudiantes
- Cada estudiante tiene un código QR único basado en su información personal
- Los códigos siguen el formato: {GRUPO}-{NOMBRE}-{SILABA_PATERNO}-{SILABA_MATERNO}
- El escaneo de QR marca automáticamente como 'presente' en sesiones activas
- Códigos QR inválidos no deben interrumpir el flujo de registro masivo
- Los códigos son inmutables durante todo el proceso de preparación

### RN-005: Organización de Grupos de Primera Comunión
- Grupo A tiene exactamente 40 estudiantes, Grupo B tiene 38 estudiantes
- Los estudiantes no pueden cambiar de grupo (dato maestro)
- Cada grupo mantiene un orden numérico establecido en las listas originales
- Los reportes pueden generarse por grupo individual o combinando ambos grupos
- Las sesiones pueden ser específicas por grupo o conjuntas para ambos grupos

## Requisitos de Rendimiento

### RP-001: Tiempo de Respuesta
- El registro de asistencia debe completarse en menos de 3 segundos
- El escaneo de códigos QR debe responder en menos de 2 segundos
- Los reportes básicos deben generarse en menos de 10 segundos
- La búsqueda de estudiantes debe responder en menos de 1 segundo

### RP-002: Capacidad
- El sistema debe soportar hasta 10 usuarios simultáneos
- Debe manejar sesiones con hasta 78 estudiantes sin degradación
- Los reportes deben generar hasta 1,000 registros de asistencia
- Almacenamiento para al menos 2 años de datos históricos

### RP-003: Disponibilidad
- El sistema debe estar disponible durante horarios de actividades
- Backup automático semanal de toda la información
- Recuperación de datos en caso de fallos
- Mantenimiento programado fuera de horarios de clases

## Requisitos de Seguridad Parroquial

### RS-001: Autenticación de Usuarios
- Catequistas y personal deben autenticarse con usuario/contraseña
- Contraseñas deben cumplir política básica de complejidad (mínimo 8 caracteres)
- Bloqueo automático después de 3 intentos fallidos consecutivos
- Sesiones deben expirar después de 2 horas de inactividad para seguridad

### RS-002: Control de Acceso por Roles
- Acceso basado en roles (Staff Parroquial, Catequista, Administrador Parroquial)
- Principio de menor privilegio aplicado según responsabilidades pastorales
- Logs de acciones sensibles (modificación de asistencias, creación de sesiones)
- Segregación de funciones entre registro y administración

### RS-003: Protección de Datos de Estudiantes
- Datos de estudiantes protegidos según normativas de protección de menores
- Comunicación segura recomendada en entorno de producción
- Logs de auditoría de cambios importantes en registros de asistencia
- Backup seguro de información estudiantil con cifrado

## Requisitos de Usabilidad

### RU-001: Interfaz
- Interfaz intuitiva adecuada para usuarios de parroquia
- Compatible con navegadores modernos (Chrome, Firefox, Safari, Edge)
- Diseño responsive para uso en tablets y dispositivos móviles
- Tiempo de carga de páginas menor a 5 segundos

### RU-002: Accesibilidad
- Interfaz clara y fácil de navegar
- Soporte para dispositivos táctiles (tablets)
- Navegación simple por teclado
- Contraste adecuado para diferentes condiciones de iluminación

### RU-003: Experiencia de Usuario
- Máximo 3 clics para realizar funciones comunes
- Mensajes de error claros y orientados a la solución
- Confirmaciones para acciones que modifican datos
- Ayuda contextual disponible en pantallas principales

Estos requisitos funcionales proporcionan una base sólida para el desarrollo del sistema de asistencias de Primera Comunión, asegurando que cubra todas las necesidades educativas de manera eficiente y adecuada para el contexto parroquial.
