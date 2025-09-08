<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $skills = [
            [
                'name' => 'Reparación de PC',
                'description' => 'Habilidad para diagnosticar y reparar problemas de hardware en ordenadores de sobremesa.',
                'category' => 'hardware'
            ],
            [
                'name' => 'Reparación de Portátiles',
                'description' => 'Especialización en la reparación y mantenimiento de ordenadores portátiles.',
                'category' => 'hardware'
            ],
            [
                'name' => 'Redes LAN/WAN',
                'description' => 'Configuración y solución de problemas en redes locales y de área amplia.',
                'category' => 'networking'
            ],
            [
                'name' => 'Sistemas Operativos Windows',
                'description' => 'Instalación, configuración y resolución de problemas en sistemas operativos Windows.',
                'category' => 'software'
            ],
            [
                'name' => 'Sistemas Linux',
                'description' => 'Administración y soporte para sistemas basados en Linux.',
                'category' => 'software'
            ],
            [
                'name' => 'Configuración de Servidores',
                'description' => 'Instalación y administración de servidores de diversos tipos.',
                'category' => 'technical'
            ],
            [
                'name' => 'Soporte Remoto',
                'description' => 'Capacidad para diagnosticar y resolver problemas de forma remota.',
                'category' => 'technical'
            ],
            [
                'name' => 'Seguridad Informática',
                'description' => 'Conocimientos de protección de sistemas contra amenazas y vulnerabilidades.',
                'category' => 'security'
            ],
            [
                'name' => 'Impresoras/Periféricos',
                'description' => 'Reparación y mantenimiento de impresoras y otros dispositivos periféricos.',
                'category' => 'hardware'
            ],
            [
                'name' => 'Bases de Datos',
                'description' => 'Administración y resolución de problemas en sistemas de bases de datos.',
                'category' => 'software'
            ],
            [
                'name' => 'Certificación CompTIA A+',
                'description' => 'Certificación profesional en soporte técnico y resolución de problemas.',
                'category' => 'certification'
            ],
            [
                'name' => 'Certificación Microsoft MCP',
                'description' => 'Certificación profesional de Microsoft.',
                'category' => 'certification'
            ],
            [
                'name' => 'Certificación CISCO CCNA',
                'description' => 'Certificación profesional en redes Cisco.',
                'category' => 'certification'
            ],
            [
                'name' => 'Atención al Cliente',
                'description' => 'Habilidad para brindar un excelente servicio y comunicación con el cliente.',
                'category' => 'soft_skills'
            ],
            [
                'name' => 'Trabajo en Equipo',
                'description' => 'Capacidad para colaborar eficazmente con otros miembros del equipo.',
                'category' => 'soft_skills'
            ],
        ];

        foreach ($skills as $skillData) {
            Skill::create($skillData);
        }
    }
}
