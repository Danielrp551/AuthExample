<?php

namespace Database\Seeders;

use App\Models\Encuesta;
use App\Models\Horario;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EncuestaSeeder extends Seeder
{

    public function run(): void
    {
        Encuesta::factory(5)->create();
    }
}
