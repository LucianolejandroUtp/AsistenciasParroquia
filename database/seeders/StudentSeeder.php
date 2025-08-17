<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
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

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener IDs de grupos
        $groupA = DB::table('groups')->where('code', 'A')->first();
        $groupB = DB::table('groups')->where('code', 'B')->first();

        // LISTA NIÑOS DE PRIMERA COMUNION GRUPO A
        $studentsGroupA = [
            ['Antony Alexander', 'Alférez', 'Vilchez'],
            ['Luz Melani', 'Apaza', 'Apaza'], 
            ['Angelo Yoshua', 'Aybar', 'Alanoca'], 
            ['Karol Rose', 'Barriga', 'Curasi'], 
            ['Micaela Sumac', 'Caceres', 'Samayani'], 
            ['Darla Marian', 'Calle', 'Llanos'], 
            ['Elena Esmeralda', 'Castañeda', 'Mayhuire'], 
            ['Mayerly Andrea', 'Ccolque', 'Riveros'], 
            ['Gala Michelle', 'Chara', 'Tejada'], 
            ['Javier Nicolas David', 'Chavez', 'Chambi'], 
            ['Maya Belen', 'Chicata', 'Huanca'], 
            ['Gabriela Saori', 'Condori', 'Onofre'], 
            ['Gabriel Raul', 'Escriba', 'Catasi'],
            ['Paul Alfredo', 'Huaycho', 'Morochara'], 
            ['Alejandra Luana', 'Laura', 'Ramos'], 
            ['Sophia Catalina', 'Linares', 'Mamani'], 
            ['Adiel Arián', 'Llosa', 'Valdivia'], 
            ['Benjamin Mauricio', 'Lupo', 'Sanchez'], 
            ['Fabricio Martin', 'Mamani', 'Capira'], 
            ['Maia Luciana', 'Mamani', 'Choque'], 
            ['Estrella Dayana', 'Mamani', 'Velasquez'], 
            ['Fernanda Alexandra', 'Manchego', 'Choque'], 
            ['Yamilet Luciana', 'Medina', 'Albarracín'], 
            ['Daiara Milett', 'Molina', 'Garrafa'], 
            ['Yaretzi Alexandra', 'Neyra', 'Seña'],
            ['María Alejandra Belen', 'Paredes', 'Berrios'],
            ['Mileth', 'Puma', 'Puma'],
            ['Yerson Jose', 'Puma', 'Puma'], 
            ['Katerin Nicol', 'Quispe', 'Cruz'], 
            ['Dayron Adriano', 'Quispe', 'Palomino'], 
            ['Mathias Alejandro', 'Quispe', 'Velo'], 
            ['Shantal Alondra', 'Ramirez', 'Castro'], 
            ['Liam Fabricio', 'Rimache', 'Fora'], 
            ['Yamira Zhinay', 'Rodriguez', 'Quispe'], 
            ['Yasmín Michell', 'Sihuincha', 'Quispe'], 
            ['Samantha Abigail', 'Tito', 'Yaury'], 
            ['Lian Dayiro', 'Torres', 'Cruz'], 
            ['Richards Nicolas', 'Valer', 'Chumpe'], 
            ['Adriano Gabriel', 'Villanueva', 'Arotaype'], 
            ['Rodrigo Jesus', 'Yauri', 'Ccama']
        ];

        // LISTA NIÑOS DE PRIMERA COMUNION GRUPO B
        $studentsGroupB = [
            ['Zeyla Xiomara', 'Acero', 'Chirinos'], 
            ['Neymar Daniel', 'Alvarez', 'Huacarpuma'], 
            ['Adriana Belén', 'Apaza', 'Aquino'], 
            ['Danay Marinel', 'Apaza', 'Mamani'], 
            ['Fernando', 'Argüelles', 'Huamani'], 
            ['Juan Carlos Fabrizio', 'Benavente', 'Chullo'], 
            ['Eiyal Leonel', 'Canaza', 'Mamaní'], 
            ['Emily Abril', 'Castro', 'Ccoto'], 
            ['Heydi Valeria', 'Cayllahua', 'Colque'], 
            ['María de Fátima', 'Chaiña', 'Muñoz'], 
            ['Juvenka Nicole', 'Checa', 'Cazorla'], 
            ['Jhadde Anahi', 'Chino', 'de la Cruz'], 
            ['Jose Angel', 'Condori', 'Ccama'], 
            ['Edison Alfredo', 'Condori', 'Huillca'], 
            ['María de los Angeles', 'Condori', 'Onofre'], 
            ['Xiomara Tania', 'Cruz', 'Quelcca'], 
            ['Bryana', 'Enrique', 'Melchor'], 
            ['Valeria Nicole', 'Figueroa', 'López'], 
            ['Maira Nicole', 'Flores', 'Fara'], 
            ['Adrian Vicente', 'Huamani', 'Quispe'], 
            ['Amelia Kristel', 'Huaracha', 'Chávez'], 
            ['Lucio Abrahan', 'Hurtado', 'Ferro'], 
            ['Marcos Daniel', 'Llacho', 'Llacho'], 
            ['Aldair Dariel', 'Mamani', 'Quispe'],
            ['Angie Denisse', 'Mayta', 'Miranda'], 
            ['Antonio Andree', 'Neyra', 'Seña'],
            ['Vivianne Brenda', 'Nina', 'Nuñez'], 
            ['Luis Fernando', 'Olave', 'Huaman'], 
            ['Allison Lineth', 'Pari', 'Cayllahua'], 
            ['Juan Jesús', 'Pari', 'Cayllahua'], 
            ['David Eliseo', 'Puma', 'Checo'],
            ['Yesenia Angela', 'Quispe', 'Chirme'],
            ['Yoselin Maribel', 'Quispe', 'Chirme'],
            ['Saul Marcelo', 'Quispe', 'Mollo'], 
            ['Yamilet Valeska', 'Santander', 'Puma'], 
            ['Jhon Leonel', 'Taipe', 'Choquehuayta'], 
            ['Elisban David', 'Urbano', 'Quispe'], 
            ['Faviola Tibisay', 'Valdivia', 'Condori']
        ];

        // Insertar estudiantes del Grupo A
        $studentsDataA = [];
        foreach ($studentsGroupA as $index => $student) {
            $studentsDataA[] = [
                'group_id' => $groupA->id,
                'names' => $student[0],
                'paternal_surname' => $student[1],
                'maternal_surname' => $student[2],
                'order_number' => $index + 1,
                'student_code' => $this->generateStudentCode('A', $student[0], $student[1], $student[2])
            ];
        }

        // Insertar estudiantes del Grupo B
        $studentsDataB = [];
        foreach ($studentsGroupB as $index => $student) {
            $studentsDataB[] = [
                'group_id' => $groupB->id,
                'names' => $student[0],
                'paternal_surname' => $student[1],
                'maternal_surname' => $student[2],
                'order_number' => $index + 1,
                'student_code' => $this->generateStudentCode('B', $student[0], $student[1], $student[2])
            ];
        }

        // Insertar todos los registros
        DB::table('students')->insert(array_merge($studentsDataA, $studentsDataB));
    }
}
