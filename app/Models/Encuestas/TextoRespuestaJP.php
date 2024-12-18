<?php

namespace App\Models\Encuestas;

use App\Models\Usuarios\JefePractica;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TextoRespuestaJP extends Model
{
    protected $table = "texto_respuesta_jp";
    protected $fillable = ['jp_horario_id', 'encuesta_pregunta_id', 'respuesta'];
    public function encuestaPregunta() : BelongsTo
    {
        return $this->belongsTo(EncuestaPregunta::class, 'encuesta_pregunta_id');
    }

    public function jpHorario()  : BelongsTo
    {
        return $this->belongsTo(JefePractica::class, 'jp_horario_id');
    }
}
