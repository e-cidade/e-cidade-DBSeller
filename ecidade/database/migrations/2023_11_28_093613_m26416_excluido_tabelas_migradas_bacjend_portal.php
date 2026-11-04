<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26416ExcluidoTabelasMigradasBacjendPortal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('matriculaonline.noticias');
        Schema::dropIfExists('matriculaonline.mensagens_personalizadas');
        Schema::dropIfExists('matriculaonline.tipos_mensagens_personalizadas');
        Schema::dropIfExists('matriculaonline.documentos');
        Schema::dropIfExists('matriculaonline.imagens_personalizadas');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
