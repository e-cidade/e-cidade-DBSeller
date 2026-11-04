<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25897NivelamentoNovoCampoInstituicaoTabelaProtelac extends Migration
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

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function upDicionario()
    {
        $sql = <<<SQL
INSERT INTO db_syscampo (codcam, nomecam, conteudo, descricao, valorinicial, rotulo, tamanho, nulo, maiusculo, autocompl, aceitatipo, tipoobj, rotulorel)
SELECT 1015299, 'h19_instit', 'int4', 'Código da Instituição', '0', 'Instituição', 4, 'f', 'f', 'f', 1, 'text', 'Instituição'
WHERE NOT EXISTS (
    SELECT 1 FROM db_syscampo WHERE codcam = 1015299
);

INSERT INTO db_sysarqcamp (codarq, codcam, seqarq, codsequencia)
SELECT 584, 1015299, 35, 0
WHERE NOT EXISTS (
    SELECT 1 FROM db_sysarqcamp WHERE codarq = 584 AND codcam = 1015299
);

INSERT INTO db_sysprikey (codarq, codcam, sequen, camiden)
SELECT 584, 1015299, 2, 1015299
WHERE NOT EXISTS (
    SELECT 1 FROM db_sysprikey WHERE codarq = 584 AND codcam = 1015299
);


SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    private function upEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL

ALTER TABLE recursoshumanos.protelac ADD COLUMN IF NOT EXISTS h19_instit integer;
ALTER TABLE recursoshumanos.protelac DROP CONSTRAINT IF EXISTS protelac_ass_tip_pk;
ALTER TABLE recursoshumanos.protelac ADD CONSTRAINT protelac_ass_tip_pk PRIMARY KEY (h19_assent, h19_tipo, h19_instit);

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
        $sql = <<<SQL
            DELETE FROM db_sysarqcamp WHERE codarq = 584 AND codcam = 1015299;
            DELETE FROM db_syscampo WHERE codcam = 1015299;
            DELETE FROM db_sysprikey WHERE codarq = 584 AND codcam = 1015299;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    private function downEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            
ALTER TABLE recursoshumanos.protelac DROP COLUMN IF EXISTS h19_instit;
ALTER TABLE recursoshumanos.protelac DROP CONSTRAINT IF EXISTS protelac_ass_tip_pk;
ALTER TABLE recursoshumanos.protelac ADD CONSTRAINT protelac_ass_tip_pk PRIMARY KEY (h19_assent, h19_tipo);

SQL
        );
    }
}