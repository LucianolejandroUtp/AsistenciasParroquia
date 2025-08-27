<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Group;
use Illuminate\Support\Facades\Gate;

class StudentController extends Controller
{
    /**
     * Mostrar lista completa de estudiantes.
     */
    public function index()
    {
        // Obtener todos los estudiantes con sus grupos y estadísticas de asistencia
        $students = Student::with(['group', 'attendances.attendanceSession'])
            ->orderBy('names')
            ->orderBy('paternal_surname')
            ->get()
            ->map(function ($student) {
                // Calcular estadísticas de asistencia
                $totalSessions = $student->attendances->pluck('attendanceSession')->unique('id')->count();
                $attendedSessions = $student->attendances->count();
                $attendancePercentage = $totalSessions > 0 ? round(($attendedSessions / $totalSessions) * 100) : 0;

                return (object) [
                    'id' => $student->id,
                    'full_name' => $student->names . ' ' . $student->paternal_surname . ($student->maternal_surname ? ' ' . $student->maternal_surname : ''),
                    'names' => $student->names,
                    'paternal_surname' => $student->paternal_surname,
                    'maternal_surname' => $student->maternal_surname,
                    'qr_code' => $student->qr_code,
                    'student_code' => $student->student_code,
                    'group_name' => $student->group ? $student->group->name : 'Sin Grupo',
                    'group_id' => $student->group_id,
                    'order_number' => $student->order_number,
                    'status' => $student->estado,
                    'total_sessions' => $totalSessions,
                    'attended_sessions' => $attendedSessions,
                    'attendance_percentage' => $attendancePercentage,
                    'created_at' => $student->created_at->format('d/m/Y'),
                    'updated_at' => $student->updated_at->format('d/m/Y H:i')
                ];
            });

        // Obtener todos los grupos para filtros
        $groups = Group::orderBy('name')->get();

        return view('students.index', compact('students', 'groups'));
    }

    /**
     * Obtener datos de estudiantes para DataTables AJAX.
     */
    public function ajaxData(Request $request)
    {
        // Obtener todos los estudiantes con sus grupos y estadísticas de asistencia
        $students = Student::with(['group', 'attendances.attendanceSession'])
            ->orderBy('names')
            ->orderBy('paternal_surname')
            ->get()
            ->map(function ($student) {
                // Calcular estadísticas de asistencia
                $totalSessions = $student->attendances->pluck('attendanceSession')->unique('id')->count();
                $attendedSessions = $student->attendances->count();
                $attendancePercentage = $totalSessions > 0 ? round(($attendedSessions / $totalSessions) * 100) : 0;

                // Generar HTML para las celdas
                $orderHtml = '<span class="badge badge-soft-primary">' . ($student->order_number ?? '-') . '</span>';
                
                $nameHtml = '<div><strong>' . $student->names . ' ' . $student->paternal_surname . 
                           ($student->maternal_surname ? ' ' . $student->maternal_surname : '') . '</strong></div>';
                
                $groupHtml = $student->group ? 
                    '<span class="badge badge-' . (strpos($student->group->name, 'A') !== false ? 'primary' : 'info') . '">' . 
                    $student->group->name . '</span>' : 
                    '<span class="badge badge-secondary">Sin Grupo</span>';
                
                $attendanceHtml = '<div class="d-flex align-items-center">' .
                    '<div class="progress flex-fill mr-2" style="height: 6px;">' .
                    '<div class="progress-bar bg-' . ($attendancePercentage >= 80 ? 'success' : ($attendancePercentage >= 60 ? 'warning' : 'danger')) . 
                    '" style="width: ' . $attendancePercentage . '%"></div></div>' .
                    '<span class="small text-muted">' . $attendancePercentage . '%</span></div>' .
                    '<small class="text-muted">' . $attendedSessions . '/' . $totalSessions . ' sesiones</small>';
                
                $statusHtml = '<span class="badge badge-' . ($student->estado == 'ACTIVO' ? 'success' : 'secondary') . '">' . 
                             $student->estado . '</span>';
                
                $actionsHtml = '<div class="btn-group btn-group-sm" role="group" aria-label="Acciones">' .
                    '<button type="button" class="btn btn-outline-primary btn-view-details" title="Ver Detalles" data-student-id="' . $student->id . '">' .
                    '<span class="fe fe-eye fe-12"></span></button>' .
                    '<button type="button" class="btn btn-outline-secondary btn-edit-student" title="Editar" data-student-id="' . $student->id . '">' .
                    '<span class="fe fe-edit-2 fe-12"></span></button>' .
                    '<button type="button" class="btn btn-outline-info btn-view-qr" title="Ver QR" data-url="' . route('students.qr.modal', ['student' => $student->id]) . '">' .
                    '<span class="fe fe-maximize fe-12"></span></button></div>';

                return [
                    $orderHtml,
                    $nameHtml,
                    $groupHtml,
                    $attendanceHtml,
                    $statusHtml,
                    $actionsHtml
                ];
            });

        return response()->json([
            'data' => $students
        ]);
    }

    /**
     * Crear un nuevo estudiante.
     */
    public function store(Request $request)
    {
        // Validar datos de entrada usando nombres exactos de la migración
        $data = $request->validate([
            'names' => 'required|string|max:191',
            'paternal_surname' => 'required|string|max:191',
            'maternal_surname' => 'nullable|string|max:191',
            'group_id' => 'required|exists:groups,id',
            'order_number' => 'required|integer|min:1|max:100',
        ], [
            'names.required' => 'Los nombres son obligatorios',
            'paternal_surname.required' => 'El apellido paterno es obligatorio',
            'group_id.required' => 'Debe seleccionar un grupo',
            'group_id.exists' => 'El grupo seleccionado no es válido',
            'order_number.required' => 'El número de orden es obligatorio',
            'order_number.min' => 'El número de orden debe ser mayor a 0',
            'order_number.max' => 'El número de orden no puede ser mayor a 100',
        ]);

        // Verificar que el número de orden no esté duplicado en el grupo
        $exists = Student::where('group_id', $data['group_id'])
                         ->where('order_number', $data['order_number'])
                         ->where('estado', '!=', 'ELIMINADO')
                         ->exists();

        if ($exists) {
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Ya existe un estudiante con este número de orden en el grupo seleccionado'
                ], 409);
            }
            return back()->withErrors([
                'order_number' => 'Ya existe un estudiante con este número de orden en el grupo seleccionado'
            ])->withInput();
        }

        // Crear el estudiante usando los campos exactos de la migración
        $student = new Student();
        $student->names = $data['names'];
        $student->paternal_surname = $data['paternal_surname'];
        $student->maternal_surname = $data['maternal_surname'] ?? null;
        $student->group_id = $data['group_id'];
        $student->order_number = $data['order_number'];
        // Los campos student_code, unique_id, estado, created_at, updated_at se manejan automáticamente
        $student->save();

        // Generar código QR automáticamente usando el mismo algoritmo del seeder
        if (!$student->student_code) {
            $group = Group::find($data['group_id']);
            $groupCode = $group ? $group->code : 'X';
            $student->student_code = $this->generateStudentCode($groupCode, $data['names'], $data['paternal_surname'], $data['maternal_surname'] ?? '');
            $student->save();
        }

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Estudiante creado correctamente',
                'student' => [
                    'id' => $student->id,
                    'full_name' => $student->names . ' ' . $student->paternal_surname . ($student->maternal_surname ? ' ' . $student->maternal_surname : ''),
                    'names' => $student->names,
                    'paternal_surname' => $student->paternal_surname,
                    'maternal_surname' => $student->maternal_surname,
                    'group_id' => $student->group_id,
                    'order_number' => $student->order_number,
                    'student_code' => $student->student_code,
                    'status' => $student->estado
                ]
            ], 201);
        }

        return redirect()->route('students.index')->with('success', 'Estudiante creado correctamente');
    }

    /**
     * Mostrar códigos QR de estudiantes.
     */
    public function qrCodes(Request $request)
    {
        // Parámetros de paginación
    $perPage = $request->get('per_page', 20); // 20 por defecto
        $page = $request->get('page', 1);
        
        // Construir query y aplicar filtros (grupo y búsqueda global)
        $query = Student::with('group')
            ->where('estado', 'ACTIVO');

        // Filtrar por grupo si se proporciona
        if ($request->filled('group')) {
            $query->where('group_id', $request->input('group'));
        }

        // Búsqueda global: nombres, apellidos, código QR y código de estudiante
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('names', 'like', "%{$q}%")
                    ->orWhere('paternal_surname', 'like', "%{$q}%")
                    ->orWhere('maternal_surname', 'like', "%{$q}%")
                    ->orWhere('student_code', 'like', "%{$q}%");
            });
        }

        // Ejecutar paginación y mantener query string para links
        $qrCodes = $query->orderBy('names')->paginate($perPage)->withQueryString();

        // Mapear datos para la vista
        $qrCodes->getCollection()->transform(function ($student) {
            return (object) [
                'id' => $student->id,
                'full_name' => $student->names . ' ' . $student->paternal_surname . ($student->maternal_surname ? ' ' . $student->maternal_surname : ''),
                'qr_code' => $student->student_code,
                'group_name' => $student->group ? $student->group->name : 'Sin Grupo',
                'last_scanned' => $student->updated_at->format('Y-m-d H:i:s'),
                'total_scans' => rand(5, 15) // Mock temporal para total_scans
            ];
        });

        $qrStats = (object) [
            'total_codes' => Student::where('estado', 'ACTIVO')->count(),
            'active_codes' => Student::where('estado', 'ACTIVO')->count(),
            'total_scans_today' => 45,
            'last_generated' => '2025-08-10 09:00:00'
        ];

        return view('students.qr-codes', compact('qrCodes', 'qrStats'));
    }

    /**
     * Devuelve partial con detalles del estudiante (para peticiones AJAX)
     */
    public function details(Request $request, Student $student)
    {
        // Mapear a objeto simple igual que en index
        $totalSessions = $student->attendances->pluck('attendanceSession')->unique('id')->count();
        $attendedSessions = $student->attendances->count();
        $attendancePercentage = $totalSessions > 0 ? round(($attendedSessions / $totalSessions) * 100) : 0;

        $dto = (object) [
            'id' => $student->id,
            'full_name' => $student->names . ' ' . $student->paternal_surname . ($student->maternal_surname ? ' ' . $student->maternal_surname : ''),
            'names' => $student->names,
            'paternal_surname' => $student->paternal_surname,
            'maternal_surname' => $student->maternal_surname,
            'group_name' => $student->group ? $student->group->name : 'Sin Grupo',
            'order_number' => $student->order_number,
            'status' => $student->estado,
            'total_sessions' => $totalSessions,
            'attended_sessions' => $attendedSessions,
            'attendance_percentage' => $attendancePercentage,
        ];

        if ($request->ajax()) {
            return view('students.partials._details', ['student' => $dto]);
        }

        // Fallback: redirigir a la vista completa (o mostrar una página simple)
        return redirect()->route('students.index');
    }

    /**
     * Devuelve partial con el formulario de edición (AJAX)
     */
    public function edit(Request $request, Student $student)
    {
        // Mapear a objeto simple para la vista
        $dto = (object) [
            'id' => $student->id,
            'names' => $student->names,
            'paternal_surname' => $student->paternal_surname,
            'maternal_surname' => $student->maternal_surname,
            'group_id' => $student->group_id,
            'order_number' => $student->order_number,
            'student_code' => $student->student_code,
            'status' => $student->estado,
        ];

        if ($request->ajax()) {
            $groups = Group::orderBy('name')->get();
            return view('students.partials._edit_form', ['student' => $dto, 'groups' => $groups]);
        }

        return redirect()->route('students.index');
    }

    /**
     * Actualiza los datos del estudiante (PUT)
     */
    public function update(Request $request, Student $student)
    {
        // Validar campos; usar nombres exactos según migración
        $data = $request->validate([
            'names' => 'required|string|max:191',
            'paternal_surname' => 'required|string|max:191',
            'maternal_surname' => 'nullable|string|max:191',
            'group_id' => 'nullable|exists:groups,id',
            'order_number' => 'required|integer|min:1',
            // Remover student_code de la validación - no debe ser editable
            'status' => 'required|in:ACTIVO,INACTIVO,ELIMINADO'
        ]);

        // Asignar y guardar
        $student->names = $data['names'];
        $student->paternal_surname = $data['paternal_surname'];
        $student->maternal_surname = $data['maternal_surname'] ?? null;
        $student->group_id = $data['group_id'] ?? null;
        $student->order_number = $data['order_number'];
        $student->estado = $data['status'];
        
        // IMPORTANTE: student_code se mantiene INMUTABLE una vez creado
        // Esto sigue las mejores prácticas empresariales donde los identificadores
        // únicos no cambian para mantener estabilidad y trazabilidad
        // Si es absolutamente necesario cambiar un código, debe hacerse manualmente
        // por un administrador con justificación documentada
        
        $student->save();

        if ($request->ajax()) {
            // Refrescar relaciones necesarias
            $student->load('group');

            $studentDto = (object) [
                'id' => $student->id,
                'order_number' => $student->order_number,
                'full_name' => $student->names . ' ' . $student->paternal_surname . ($student->maternal_surname ? ' ' . $student->maternal_surname : ''),
                'group_name' => $student->group ? $student->group->name : 'Sin Grupo',
                'status' => $student->estado,
            ];

            return response()->json(['message' => 'Estudiante actualizado correctamente', 'student' => $studentDto]);
        }

        return redirect()->route('students.index')->with('success', 'Estudiante actualizado correctamente');
    }

    /**
     * Mostrar partial con QR de un estudiante (para modal AJAX).
     *
     * Nota: no usar el nombre qrCodes/qrcodes para evitar colisiones con el método
     * que devuelve la vista completa de códigos QR (qrCodes).
     */
    public function showQrModal(Request $request, Student $student)
    {
    // Autorizar usando policy existente (reusar 'view' si aplica)
    Gate::authorize('view', $student);

        // Si se solicita vía AJAX, devolver la vista parcial con el estudiante
        if ($request->ajax()) {
            return view('students.partials._qrcodes', ['student' => $student]);
        }

        // Fallback: redirigir a la lista de estudiantes
        return redirect()->route('students.index');
    }

    /**
     * Normaliza el texto removiendo tildes pero manteniendo eñes
     */
    private function normalizeText($text): string
    {
        $search = ['á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú',
                  'à', 'è', 'ì', 'ò', 'ù', 'À', 'È', 'Ì', 'Ò', 'Ù',
                  'ä', 'ë', 'ï', 'ö', 'ü', 'Ä', 'Ë', 'Ï', 'Ö', 'Ü'];
        $replace = ['a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U',
                   'a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U',
                   'a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U'];
        
        return str_replace($search, $replace, $text);
    }

    /**
     * Extrae la primera sílaba de una palabra
     */
    private function getFirstSyllable($word): string
    {
        $word = $this->normalizeText(trim($word));
        
        // Si hay espacio, cortar en el primer espacio (para apellidos compuestos)
        $spacePos = strpos($word, ' ');
        if ($spacePos !== false) {
            $word = substr($word, 0, $spacePos);
        }
        
        // Solo vocales básicas sin tildes
        $vowels = ['A', 'E', 'I', 'O', 'U', 'a', 'e', 'i', 'o', 'u'];
        
        $syllable = '';
        $vowelCount = 0;
        
        // Usar funciones multibyte para manejar caracteres UTF-8 correctamente
        $length = mb_strlen($word);
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($word, $i, 1);
            
            if (in_array($char, $vowels)) {
                $vowelCount++;
                // Si esta es la segunda vocal, cortamos SIN incluirla
                if ($vowelCount == 2) {
                    break;
                }
            }
            
            // Solo incluir el carácter si no es la segunda vocal
            $syllable .= $char;
        }
        
        return $this->toUpperCase($syllable);
    }
    
    /**
     * Convierte texto a mayúsculas manejando correctamente la Ñ
     */
    private function toUpperCase($text): string
    {
        return str_replace('ñ', 'Ñ', strtoupper($text));
    }
    
    /**
     * Extrae el primer nombre (antes del primer espacio)
     */
    private function getFirstName($fullName): string
    {
        $names = explode(' ', trim($fullName));
        return $this->toUpperCase($this->normalizeText($names[0]));
    }
    
    /**
     * Genera el código de estudiante basado en sílabas
     */
    private function generateStudentCode($groupCode, $names, $paternalSurname, $maternalSurname): string
    {
        $firstName = $this->getFirstName($names);
        $paternalSyllable = $this->getFirstSyllable($paternalSurname);
        $maternalSyllable = $this->getFirstSyllable($maternalSurname);
        
        return "{$groupCode}-{$firstName}-{$paternalSyllable}-{$maternalSyllable}";
    }
}