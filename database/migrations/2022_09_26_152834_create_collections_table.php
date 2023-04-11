<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            
            /* $table->string('status'); //validado (V) Não Validado (N) - Aprovação do Arquivo pelo orientador
            $table->string('title'); //título
            $table->string('subtitle'); //subtitulo
            $table->string('authors'); //autores
            $table->string('workType'); //tipo de trabalho (Tese, TCC, Monografia)
            $table->string('institution'); //instituição de ensino
            $table->string('advisor'); // orientador
            $table->date('publicationYear'); //ano de publicação */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collections');
    }
};
