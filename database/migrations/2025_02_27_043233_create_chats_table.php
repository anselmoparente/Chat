<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_1_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('usuario_2_id')->constrained('usuarios')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mensagens', function (Blueprint $table) {
            $table->dropForeign(['chat_id']);
            $table->dropColumn('chat_id');
        });

        Schema::dropIfExists('chats');
    }
};
