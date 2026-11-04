<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26053CriacaoCamposTabelaRhteutri extends Migration
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

    public function upDicionario()
    {
        $sql  = <<<SQL
INSERT INTO db_syscampo VALUES (1015577,'rh67_anousu','int4','Ano da Competência','0', 'Ano da Competência',10,'f','f','f',1,'text','Ano da Competência');
INSERT INTO db_syscampo VALUES (1015578,'rh67_mesusu','int4','Mês de Competência','0', 'Mês de Competência',10,'f','f','f',1,'text','Mês de Competência');
INSERT INTO db_syscampo VALUES (1015579,'rh67_valor','float4','Valor do Vale Transporte','0', 'Valor do Vale Transporte',10,'t','f','f',4,'text','Valor do Vale Transporte');
INSERT INTO db_sysarqcamp VALUES (1915,1015577,11,0);
INSERT INTO db_sysarqcamp VALUES (1915,1015578,12,0);
INSERT INTO db_sysarqcamp VALUES (1915,1015579,13,0);
SQL;
            DB::connection()->getPdo()->exec($sql);
    }

    private function upEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL

ALTER TABLE rhteutri ADD column rh67_anousu INTEGER NOT NULL;
ALTER TABLE rhteutri ADD column rh67_mesusu INTEGER NOT NULL;
ALTER TABLE rhteutri ADD column rh67_valor FLOAT NULL;

CREATE INDEX rhteutri_rh67_anousu_rh67_mesusu_in ON pessoal.rhteutri(rh67_anousu,rh67_mesusu);


SQL
);
    }

    private function downEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
ALTER TABLE rhteutri DROP column rh67_anousu;
ALTER TABLE rhteutri DROP column rh67_mesusu;
ALTER TABLE rhteutri DROP column rh67_valor;

SQL
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function downDicionario()
    {
        $sql  = <<<SQL
DELETE FROM db_sysarqcamp WHERE codarq = 1915 AND codcam IN (1015577,1015578,1015579);
DELETE FROM db_syscampo WHERE codcam IN (1015577,1015578,1015579);

SQL;
            DB::connection()->getPdo()->exec($sql);
    }
}
