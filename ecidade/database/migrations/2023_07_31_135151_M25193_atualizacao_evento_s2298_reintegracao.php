<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25193AtualizacaoEventoS2298Reintegracao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
        $this->upEstrutura();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
        $this->downEstrutura();
    }

    private function upDicionario() {
        $sql = <<<SQL
            INSERT INTO configuracoes.db_syscampo VALUES(10000569, 'h25_tiporeintegracao', 'int4','Tipo de Reintegração', 0, 'Tipo de Reintegração',1 , TRUE, FALSE, FALSE, 1, 'text' , 'Reintegração');
            INSERT INTO configuracoes.db_syscampo VALUES(10000570, 'h25_dataefetivoretorno', 'date','Data do Efetivo Retorno', 'null', 'Data do Efetivo Retorno',1 , TRUE, FALSE, FALSE, 1, 'text' , 'Data do Efetivo Retorno');
            INSERT INTO configuracoes.db_syscampo VALUES(10000571, 'h25_numeroprocesso', 'int4','Número do processo', 'null', 'Número do processo',20 , TRUE, FALSE, FALSE, 1, 'text' , 'Número do processo');
            INSERT INTO configuracoes.db_syscampo VALUES(10000572, 'h25_leianistia', 'int4','Lei de Anistia', 'null', 'Lei de Anistia',13 , TRUE, FALSE, FALSE, 1, 'text' , 'Lei de Anistia');

SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    private function upEstrutura() {
        $sql = <<<SQL
            ALTER TABLE recursoshumanos.rhadmissaodado ADD COLUMN h25_tiporeintegracao INT DEFAULT 0;
            ALTER TABLE recursoshumanos.rhadmissaodado ADD COLUMN h25_dataefetivoretorno DATE;
            ALTER TABLE recursoshumanos.rhadmissaodado ADD COLUMN h25_numeroprocesso VARCHAR(20) DEFAULT '';
            ALTER TABLE recursoshumanos.rhadmissaodado ADD COLUMN h25_leianistia VARCHAR(13) DEFAULT '';
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    private function downDicionario() {
        $sql = <<<SQL
            DELETE FROM configuracoes.db_syscampo WHERE codcam IN(10000569, 10000570, 10000571, 10000572); 
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    private function downEstrutura() {
        $sql = <<<SQL
                ALTER TABLE recursoshumanos.rhadmissaodado DROP COLUMN h25_tiporeintegracao;
                ALTER TABLE recursoshumanos.rhadmissaodado DROP COLUMN h25_dataefetivoretorno;
                ALTER TABLE recursoshumanos.rhadmissaodado DROP COLUMN h25_numeroprocesso;
                ALTER TABLE recursoshumanos.rhadmissaodado DROP COLUMN h25_leianistia;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

}
