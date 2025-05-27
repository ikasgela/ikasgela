<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SkillsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('skills')->insert([
            3 =>
                [
                    'id' => 4,
                    'name' => 'RA1 - Identificación de los elementos de un programa informático',
                    'description' => 'Reconoce la estructura de un programa informático, identificando y relacionando los elementos propios del lenguaje de programación utilizado.',
                ],
            4 =>
                [
                    'id' => 5,
                    'name' => 'RA2 - Escritura y prueba de programas sencillos',
                    'description' => 'Escribe y prueba programas sencillos, reconociendo y aplicando los fundamentos de la programación orientada a objetos.',
                ],
            5 =>
                [
                    'id' => 6,
                    'name' => 'RA3 - Escritura de programas utilizando las estructuras de control del lenguaje',
                    'description' => 'Escribe y depura código, analizando y utilizando las estructuras de control del lenguaje.',
                ],
            6 =>
                [
                    'id' => 7,
                    'name' => 'RA4 - Realización de programas aplicando los principios de la programación orientada a objetos',
                    'description' => 'Desarrolla programas organizados en clases, analizando y aplicando los principios de la programación orientada a objetos.',
                ],
            7 =>
                [
                    'id' => 8,
                    'name' => 'RA5 - Realización de programas utilizando interfaces gráficos',
                    'description' => 'Realiza  operaciones de entrada  y salida  de información,  utilizando procedimientos específicos del lenguaje y librerías de clases.',
                ],
            8 =>
                [
                    'id' => 9,
                    'name' => 'RA6 - Desarrollo de programas que utilicen tipos avanzados de datos',
                    'description' => 'Escribe  programas  que  manipulen  información,  seleccionando  y  utilizando  tipos avanzados de datos.',
                ],
            9 =>
                [
                    'id' => 10,
                    'name' => 'RA7 - Realización de programas que utilicen características avanzadas de los lenguajes orientados a objetos',
                    'description' => 'Desarrolla programas aplicando características avanzadas de los lenguajes orientados a objetos y del entorno de programación.',
                ],
            10 =>
                [
                    'id' => 11,
                    'name' => 'RA8 - Utilización de bases de datos orientadas a objetos',
                    'description' => 'Utiliza  Bases  de  Datos  Orientadas  a  Objetos,  analizando  sus  características  y aplicando técnicas para mantener la persistencia de la información.',
                ],
            11 =>
                [
                    'id' => 12,
                    'name' => 'RA9 - Utilización de bases de datos relacionales',
                    'description' => 'Gestiona  información  almacenada  en  bases  de  datos  relacionales  manteniendo  la integridad y consistencia de los datos.',
                ],
        ]);

    }
}
