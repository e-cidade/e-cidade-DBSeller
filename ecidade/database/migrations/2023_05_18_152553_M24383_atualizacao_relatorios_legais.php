<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24383AtualizacaoRelatoriosLegais extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
UPDATE orcparamseq
   SET o69_labelrel = '( - ) Transferências obrigatórias da União relativas às emendas de bancada (art. 166, § 16, da CF) (VI) e ao vencimento dos agentes...'
 where o69_codparamrel = 270 and o69_codseq = 32;

UPDATE orcparamseq
   SET o69_labelrel = 'RESULTADO NOMINAL AJUSTADO (SEM RPPS) AJUSTADO - Abaixo da Linha (L) = [XLIII + (XLIV - XLV - XLVI + XLVII + XLVIII) +/- (XLXIX)]'
 where o69_codparamrel = 271 and o69_codseq = 87;
SQL
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getPdo()->exec(<<<SQL
UPDATE orcparamseq
   SET o69_labelrel = '( - ) Transferências obrigatórias da União relativas às emendas de bancada (art. 166, § 16, da CF) (VI)'
 where o69_codparamrel = 270 and o69_codseq = 32;

UPDATE orcparamseq
   SET o69_labelrel = 'RESULTADO NOMINAL AJUSTADO (SEM RPPS) AJUSTADO - Abaixo da Linha (L) = [XLIII + (XLIV - XLV + XLVI + XLVII + XLVIII) +/- (XLXIX)]'
 where o69_codparamrel = 271 and o69_codseq = 87;
SQL
        );
    }
}
