<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $table = 'chats';

    protected $fillable = [
        'usuario_1_id',
        'usuario_2_id'
    ];

    public function usuario1()
    {
        return $this->belongsTo(Usuario::class, 'usuario_1_id');
    }

    public function usuario2()
    {
        return $this->belongsTo(Usuario::class, 'usuario_2_id');
    }

    public function mensagens()
    {
        return $this->hasMany(Mensagem::class, 'chat_id');
    }
}
