<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensagem extends Model
{
    use HasFactory;

    protected $table = 'mensagens';

    protected $fillable = [
        'texto',
        'usuario_envio_id',
        'usuario_recebimento_id',
        'near',
    ];

    public function usuarioEnvio()
    {
        return $this->belongsTo(Usuario::class, 'usuario_envio_id');
    }

    public function usuarioRecebimento()
    {
        return $this->belongsTo(Usuario::class, 'usuario_recebimento_id');
    }
}
