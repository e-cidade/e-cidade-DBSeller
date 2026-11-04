<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23215AjustarTiposEmpresa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
            INSERT INTO tipoempresa VALUES (2100,'SOCIEDADE MERCANTIL DE CAPITAL E INDÚSTRIA');
            INSERT INTO tipoempresa VALUES (2356,upper('Investidor Não Residente'));
            INSERT INTO tipoempresa VALUES (3328,upper('Plano de Benefícios de Previdência Complementar Fechada'));

            UPDATE
                tipoempresa
            SET db98_descricao =
            CASE
                WHEN db98_sequencial = 1139 THEN upper('Fundação Pública de Direito Público Federal')
                WHEN db98_sequencial = 1147 THEN upper('Fundação Pública de Direito Público Estadual ou do Distrito Federal')
                WHEN db98_sequencial = 1155 THEN upper('Fundação Pública de Direito Público Municipal')
                WHEN db98_sequencial = 1210 THEN upper('Consórcio Público de Direito Público (Associação Pública)')
                WHEN db98_sequencial = 1252 THEN upper('Fundação Pública de Direito Privado Federal')
                WHEN db98_sequencial = 1260 THEN upper('Fundação Pública de Direito Privado Estadual ou do Distrito Federal')
                WHEN db98_sequencial = 1279 THEN upper('Fundação Pública de Direito Privado Municipal')
            ELSE db98_descricao END
SQL;
        DB::connection()->getPdo()->exec($sql);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL
            UPDATE tipoempresa
            SET db98_descricao =
            CASE
                WHEN db98_sequencial = 1139 THEN upper('FUNDAÇÃOPÚBLICA DE DIREITO PÚBLICOFEDERAL')
                WHEN db98_sequencial = 1147 THEN upper('FUNDAÇÃOPÚBLICA DE DIREITO PÚBLICOESTADUAL OU DO DISTRITO FEDERAL')
                WHEN db98_sequencial = 1155 THEN upper('FUNDAÇÃOPÚBLICA DE DIREITO PÚBLICOMUNICIPAL')
                WHEN db98_sequencial = 1210 THEN upper('CONSÓRCIO PÚBLICO DE DIREITO PÚBLICO(ASSOCIAÇÃO PÚBLICA)')
                WHEN db98_sequencial = 1252 THEN upper('CONSÓRCIO PÚBLICO DE DIREITO PRIVADO FEDERAL')
                WHEN db98_sequencial = 1260 THEN upper('CONSÓRCIO PÚBLICO DE DIREITO PRIVADO ESTADUAL OU DO DISTRITO FEDERAL')
                WHEN db98_sequencial = 1279 THEN upper('CONSÓRCIO PÚBLICO DE DIREITO PRIVADO MUNICIPAL')
            ELSE db98_descricao END;

            DELETE FROM tipoempresa where db98_sequencial IN (2100,2356,3328);
SQL;

        DB::connection()->getPdo()->exec($sql);
    }
}
