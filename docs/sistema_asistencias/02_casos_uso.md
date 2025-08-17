# Casos de Uso - Sistema de Asistencias Primera Comunión

## Introducción

Este documento describe los casos de uso detallados del sistema de asistencias para estudiantes de Primera Comunión en la parroquia, incluyendo flujos de registro de asistencias, gestión de sesiones de catequesis, manejo de códigos QR, y generación de reportes pastorales.

## Casos de Uso Principales

### CU-001: Crear Sesión de Catequesis

**Actor Principal**: Catequista/Administrador Parroquial
**Actores Secundarios**: Sistema de Notificaciones
**Precondiciones**: 
- El usuario está autenticado con rol de Catequista o Administrador Parroquial
- Existen los grupos A y B de Primera Comunión en el sistema

**Flujo Principal**:
1. El catequista accede a la sección "Sesiones de Catequesis"
2. El catequista hace clic en "Crear Nueva Sesión"
3. El sistema muestra el formulario de creación de sesión
4. El catequista ingresa fecha, hora, título (ej: "Los Sacramentos - Eucaristía") y observaciones pastorales
5. El catequista selecciona grupo(s) participantes (A, B, o ambos)
6. El sistema valida que la fecha no sea anterior a hoy
7. El sistema valida que todos los campos obligatorios estén completos
8. El sistema crea la sesión y asigna el catequista como responsable
9. El sistema genera la lista de estudiantes esperados según grupo(s) seleccionado(s)
10. El sistema muestra confirmación y redirige a la vista de la sesión creada

**Flujos Alternativos**:

**6a. Fecha anterior a hoy**:
- 6a.1. El sistema muestra error "No se pueden crear sesiones para fechas pasadas"
- 6a.2. El sistema solicita corrección de la fecha
- 6a.3. Retorna al paso 4

**7a. Campos incompletos**:
- 7a.1. El sistema resalta campos faltantes
- 7a.2. El sistema muestra mensaje "Complete todos los campos obligatorios"
- 7a.3. Retorna al paso 4

**Postcondiciones**:
- Sesión de catequesis creada en la base de datos
- Lista de estudiantes esperados generada automáticamente
- Catequista responsable asignado a la sesión
- Log de auditoría registrado

---

### CU-002: Registrar Asistencia de Estudiante de Primera Comunión

**Actor Principal**: Catequista/Staff Parroquial/Administrador Parroquial
**Precondiciones**:
- Existe una sesión de catequesis activa o programada
- El usuario está autenticado
- Los 78 estudiantes están registrados en el sistema (40 en Grupo A, 38 en Grupo B)

**Flujo Principal**:
1. El usuario accede a la sesión de catequesis donde registrará asistencias
2. El sistema muestra la lista de estudiantes esperados para la sesión por grupo
3. El usuario selecciona un estudiante de la lista
4. El usuario selecciona el estado de asistencia (presente, ausente, tardío, justificado)
5. El usuario opcionalmente agrega observaciones pastorales del estudiante
6. El sistema valida que no exista registro previo para ese estudiante en la sesión
7. El sistema guarda el registro de asistencia
8. El sistema actualiza la vista mostrando el estado registrado con indicador visual
9. El sistema permite continuar con el siguiente estudiante

**Flujos Alternativos**:

**6a. Asistencia ya registrada**:
- 6a.1. El sistema detecta registro existente
- 6a.2. El sistema muestra opción "Modificar asistencia existente"
- 6a.3. Si acepta, permite actualizar el registro
- 6a.4. Si rechaza, regresa al paso 3

**4a. Marcado masivo por grupo**:
- 4a.1. El usuario selecciona "Marcar todos del Grupo A/B como presentes"
- 4a.2. El sistema solicita confirmación
- 4a.3. El sistema registra a todos los estudiantes del grupo como presentes
- 4a.4. El sistema permite modificaciones individuales posteriores

**Postcondiciones**:
- Registro de asistencia creado o actualizado
- Estado visible en la interfaz con colores distintivos
- Estadísticas de sesión actualizadas automáticamente

---

### CU-003: Registrar Asistencia por Código QR de Estudiante

**Actor Principal**: Catequista/Staff Parroquial/Administrador Parroquial
**Actores Secundarios**: Sistema de Cámara, Base de Datos de Estudiantes

**Flujo Principal**:
1. El usuario accede a la función "Escanear QR" dentro de una sesión de catequesis activa
2. El sistema solicita permisos de cámara al navegador/dispositivo
3. El sistema activa la cámara y muestra la interfaz de escaneo optimizada
4. El usuario enfoca el código QR del estudiante con la cámara
5. El sistema detecta y decodifica el código QR del estudiante
6. El sistema busca al estudiante correspondiente en la base de datos de Primera Comunión
7. El sistema valida que el estudiante pertenezca al grupo de la sesión (A o B)
8. El sistema registra automáticamente la asistencia como "presente"
9. El sistema muestra confirmación visual con nombre completo del estudiante y su grupo
10. El sistema permite continuar escaneando el siguiente código sin interrupciones

**Tipos de Códigos QR para Estudiantes**:
- Formato: {GRUPO}-{NOMBRE}-{SILABA_PATERNO}-{SILABA_MATERNO}
- Ejemplo: "A-ANTONY-ALF-VILCH" (Antony Alexander Alférez Vilchez, Grupo A)
- Ejemplo: "B-MARIA-PAR-BERR" (María Alejandra Paredes Berrios, Grupo B)

**Flujos Alternativos**:

**2a. Permisos de cámara denegados**:
- 2a.1. El sistema muestra mensaje de error claro
- 2a.2. El sistema ofrece instrucción para habilitar cámara
- 2a.3. El sistema permite cambiar a registro manual de asistencias

**6a. Código QR no válido o no reconocido**:
- 6a.1. El sistema muestra error "Código QR de estudiante no reconocido"
- 6a.2. El sistema permite intentar escanear nuevamente
- 6a.3. El sistema ofrece opción de búsqueda manual por nombre

**7a. Estudiante de grupo incorrecto**:
- 7a.1. El sistema detecta que el estudiante no pertenece al grupo de la sesión
- 7a.2. El sistema muestra advertencia con información del estudiante y su grupo real
- 7a.3. El sistema permite confirmar o cancelar el registro

**8a. Asistencia ya registrada**:
- 8a.1. El sistema detecta registro existente para el estudiante
- 8a.2. El sistema muestra estado actual y permite modificación
- 8a.3. El usuario puede cambiar el estado si es necesario

### CU-004: Gestionar Justificaciones de Ausencias Estudiantiles

**Actor Principal**: Catequista/Administrador Parroquial
**Precondiciones**: Existen estudiantes con ausencias registradas en sesiones de catequesis

**Flujo Principal**:
1. El catequista accede a "Justificaciones de Ausencias"
2. El sistema muestra lista de ausencias por justificar de los estudiantes
3. El catequista selecciona una ausencia específica
4. El sistema muestra detalles completos del estudiante y la sesión perdida
5. El catequista revisa la información de la ausencia
6. El catequista registra la justificación (médica, familiar, escolar, etc.)
7. El catequista ingresa comentarios pastorales (obligatorio)
8. El sistema actualiza el estado de la asistencia a "justificado"
9. El sistema registra la fecha y responsable de la justificación
10. El sistema actualiza las métricas de asistencia del estudiante

**Flujos Alternativos**:

**6a. Justificación con seguimiento pastoral**:
- 6a.1. El catequista puede marcar para seguimiento especial
- 6a.2. Especifica acciones pastorales de acompañamiento
- 6a.3. El sistema programa recordatorios de seguimiento

**6b. Ausencia injustificada**:
- 6b.1. El catequista determina que la ausencia no tiene justificación válida
- 6b.2. El sistema mantiene el estado como "ausente"
- 6b.3. Se registra nota pastoral para futura referencia

---

### CU-005: Generar Reporte Pastoral de Asistencias

**Actor Principal**: Catequista/Administrador Parroquial
**Precondiciones**: Existen sesiones de catequesis y registros de asistencia

**Flujo Principal**:
1. El usuario accede a "Reportes Pastorales"
2. El sistema muestra tipos de reportes disponibles según rol
3. El usuario selecciona el tipo de reporte deseado
4. El usuario especifica los filtros (fechas, grupos, estudiantes)
5. El sistema valida los parámetros del reporte
6. El usuario selecciona el formato de salida (PDF, Excel, Pantalla)
7. El sistema genera el reporte con los datos pastorales solicitados
8. El sistema presenta el reporte o inicia descarga
9. El sistema registra la generación del reporte para auditoría

**Tipos de Reportes Pastorales Disponibles**:

**Para Catequistas**:
- Asistencias por grupo (A o B)
- Resumen mensual de preparación
- Estudiantes que requieren atención pastoral
- Progreso individual de estudiantes

**Para Administrador Parroquial**:
- Reporte general de ambos grupos
- Análisis de participación en el proceso
- Estadísticas de preparación para Primera Comunión
- Estudiantes en riesgo de no completar preparación
- Comparativo entre Grupo A y Grupo B

**Flujos Alternativos**:

**5a. Parámetros inválidos**:
- 5a.1. El sistema detecta fechas incorrectas o filtros incompatibles
- 5a.2. Muestra mensaje de error específico pastoral
- 5a.3. Solicita corrección de parámetros

**7a. Reporte muy extenso**:
- 7a.1. El sistema detecta que el reporte excederá límites
- 7a.2. Ofrece opciones: dividir por grupos o filtrar más
- 7a.3. Permite programar generación para descarga posterior

### CU-006: Consultar Historial de Estudiante de Primera Comunión

**Actor Principal**: Catequista/Administrador Parroquial
**Precondiciones**: Existen estudiantes registrados con historial de asistencias

**Flujo Principal**:
1. El usuario accede a "Consulta de Estudiantes"
2. El usuario busca al estudiante por nombre, apellido o código QR
3. El sistema presenta la información completa del estudiante
4. El usuario selecciona "Ver Historial de Asistencias"
5. El sistema muestra el historial completo de participación en catequesis
6. El sistema calcula y presenta estadísticas de asistencia
7. El sistema muestra observaciones pastorales registradas
8. El usuario puede generar reporte individual del estudiante
9. El sistema permite exportar el historial para seguimiento pastoral

**Información del Historial Mostrada**:
- Datos básicos: nombre completo, grupo, número de orden
- Total de sesiones de catequesis programadas
- Total de presencias, ausencias, tardanzas
- Porcentaje de asistencia general
- Justificaciones aprobadas
- Observaciones pastorales por sesión
- Estado de preparación para Primera Comunión

**Flujos Alternativos**:

**2a. Búsqueda por código QR**:
- 2a.1. El usuario escanea el código QR del estudiante
- 2a.2. El sistema identifica automáticamente al estudiante
- 2a.3. Procede directamente al paso 4

**6a. Estudiante con asistencia insuficiente**:
- 6a.1. El sistema detecta asistencia menor al mínimo requerido
- 6a.2. Muestra alerta pastoral sobre riesgo en preparación
- 6a.3. Sugiere acciones de seguimiento y acompañamiento

---

### CU-007: Dashboard Pastoral en Tiempo Real

**Actor Principal**: Catequista/Administrador Parroquial
**Precondiciones**: Acceso al sistema durante período de preparación

**Flujo Principal**:
1. El usuario accede al dashboard pastoral principal
2. El sistema muestra estado actual de la preparación para Primera Comunión
3. El sistema presenta métricas pastorales en tiempo real
4. El usuario puede filtrar por Grupo A (40 estudiantes) o Grupo B (38 estudiantes)
5. El sistema actualiza información automáticamente cada 5 minutos
6. El sistema resalta alertas pastorales que requieren atención
7. El usuario puede drill-down en cualquier métrica para ver detalles

**Información Mostrada en Dashboard**:
- Estudiantes participando activamente en preparación
- Estudiantes con ausencias recurrentes
- Progreso general por grupo en el proceso de preparación
- Próximas sesiones de catequesis programadas
- Estudiantes que requieren seguimiento pastoral especial
- Comparativo de participación entre ambos grupos
- Alertas para estudiantes en riesgo de no completar preparación

**Flujos Alternativos**:

**6a. Alerta pastoral crítica detectada**:
- 6a.1. El sistema resalta la alerta en color rojo
- 6a.2. Muestra notificación popup con detalles
- 6a.3. Permite acción inmediata (contactar familia, programar reunión)

---

## Casos de Uso de Excepción

### CU-009: Recuperación de Entrada/Salida Perdida

**Escenario**: Empleado olvida marcar entrada o salida

**Actor Principal**: Supervisor/RRHH
**Flujo Principal**:
1. El supervisor detecta entrada/salida faltante
2. Accede a "Correcciones de Asistencia"
3. Busca al empleado y fecha específica
4. Selecciona tipo de corrección (entrada o salida perdida)
5. Ingresa la hora aproximada con justificación
6. El sistema requiere aprobación de nivel superior para correcciones
7. El sistema registra la corrección con flag especial
8. El sistema notifica al empleado sobre la corrección
9. El sistema mantiene log de auditoría detallado

---

### CU-010: Manejo de Falla del Sistema

**Escenario**: Sistema no disponible durante horario crítico

**Flujo de Contingencia**:
1. Empleados reportan a supervisor imposibilidad de marcar asistencia
2. Supervisor activa protocolo de contingencia manual
3. Empleados reportan verbalmente o por otro medio su asistencia
4. Supervisor mantiene registro manual temporal
5. Cuando sistema se restaura, supervisor ingresa datos manualmente
6. Sistema valida y procesa entradas de contingencia
7. Sistema marca registros como "Recuperación Post-Falla"
8. Se genera reporte de incidente para análisis

---

### CU-011: Detección de Fraude en Asistencias

**Escenario**: Detección de patrones sospechosos

**Flujo Principal**:
1. Sistema de IA detecta patrones anómalos automáticamente
2. Sistema genera alerta para RRHH
3. RRHH revisa los patrones detectados
4. RRHH investiga con supervisor y empleado involucrado
5. Si se confirma irregularidad, se inicia proceso disciplinario
6. Sistema ajusta algoritmos de detección basado en hallazgos

**Patrones Detectados**:
- Entradas/salidas desde ubicaciones sospechosas
- Patrones de tiempo demasiado regulares (posible automatización)
- Registros fuera de horario sin justificación
- Marcado por terceros (buddy punching)

---

## Casos de Uso de Integración

### CU-012: Integración con Sistema de Nómina

**Actor Principal**: Sistema Automatizado
**Periodicidad**: Mensual o según configuración

**Flujo Principal**:
1. Sistema programa exportación automática
2. Sistema calcula horas trabajadas por empleado para el período
3. Sistema aplica reglas de negocio para cálculo de nómina
4. Sistema genera archivo en formato requerido por nómina
5. Sistema transfiere archivo de forma segura
6. Sistema de nómina confirma recepción exitosa
7. Sistema registra exportación completada
8. En caso de error, sistema notifica a administrador

**Datos Exportados**:
- Horas regulares trabajadas
- Horas extra por tipo
- Ausencias justificadas/injustificadas
- Días de vacaciones tomados
- Incapacidades médicas
- Bonos por puntualidad

---

### CU-013: Integración con Control de Acceso Físico

**Actor Principal**: Sistema de Control de Acceso
**Flujo en Tiempo Real**:

**Entrada al Edificio**:
1. Empleado pasa tarjeta en lector de acceso
2. Sistema de acceso valida credenciales
3. Sistema de acceso envía evento al sistema de asistencias
4. Sistema de asistencias registra automáticamente la entrada
5. Sistema valida si es hora de entrada normal
6. Si hay retraso, aplica reglas automáticamente
7. Sistema confirma registro a sistema de acceso

**Salida del Edificio**:
1. Sistema detecta salida del empleado
2. Valida que haya registrado entrada previa
3. Registra automáticamente la salida
4. Calcula horas trabajadas
5. Actualiza dashboard en tiempo real

---

### CU-014: Integración con Sistema de Recursos Humanos

**Actor Principal**: Sistema RRHH
**Flujo Bidireccional**:

**Sincronización de Empleados**:
1. Sistema RRHH notifica cambios en empleados
2. Sistema de asistencias valida y procesa cambios
3. Actualiza información local de empleados
4. Notifica sobre inconsistencias si las hay
5. Confirma sincronización exitosa

**Intercambio de Métricas**:
1. Sistema comparte métricas de asistencia
2. Sistema RRHH integra datos en expedientes
3. Genera alertas automáticas por patrones problemáticos
4. Facilita procesos de evaluación de desempeño

---

## Validaciones y Reglas Transversales

### Validaciones de Entrada de Datos

**Validaciones Temporales**:
- No se pueden registrar asistencias futuras
- Correcciones limitadas a 7 días máximo
- Horarios deben tener al menos 4 horas de diferencia entre entrada y salida

**Validaciones de Negocio**:
- Empleado debe estar activo para registrar asistencia
- Supervisor solo puede gestionar empleados de su equipo
- Justificaciones requieren motivo mínimo de 10 caracteres

**Validaciones de Seguridad**:
- Todos los cambios requieren autenticación válida
- Acciones administrativas requieren doble confirmación
- Logs de auditoría inmutables para todas las operaciones críticas

Estos casos de uso proporcionan una guía detallada para el desarrollo e implementación del sistema, cubriendo tanto el flujo normal como las situaciones excepcionales que pueden presentarse en un entorno real de trabajo.
