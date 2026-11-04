<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24478CroandoRendaFamiliar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;");
        Schema::create('matriculaonline.renda_familiar', function (Blueprint $table) {
            $table->increments('mo25_id');
            $table->text('mo25_faixa');
        });
        Schema::create('matriculaonline.base_necessidade_subdivisao', function (Blueprint $table) {
            $table->increments('mo26_id');
            $table->text('mo26_base_necessidade');
            $table->text('mo26_subdivisao');
        });

        $tabelas = [
            [mb_convert_encoding('Renda Familiar', 'UTF-8'), 'mo25', '2023-11-13', mb_convert_encoding('Renda Familiar', 'UTF-8'), 0, false, false, false, false],
            [mb_convert_encoding('Base Necessidade Subdivisao', 'UTF-8'), 'mo26', '2023-11-13', mb_convert_encoding('Base Necessidade Subdivisao', 'UTF-8'), 0, false, false, false, false]
        ];
        $tabela = [];
        foreach ($tabelas as $tab) {
            $tabela[] = [
                "descricao" => $tab[0],
                "sigla" => $tab[1],
                "dataincl" => $tab[2],
                "rotulo" => $tab[3],
                "tipotabela" => $tab[4],
                "naolibclass" => $tab[5],
                "naolibfunc" => $tab[6],
                "naolibprog" => $tab[7],
                "naolibform" => $tab[8]
            ];
        }

        $campos1 = [
            'mo25_id' => ['ID','ID','ID',false,false,1,11,'text'],
            'mo25_faixa' => ['Faixa Renda','Faixa Renda','Faixa Renda',false,false,1, 200,'text']
        ];

        $json = mb_convert_encoding(json_encode($tabela[0]), 'ISO-8859-1');
        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('matriculaonline.renda_familiar');");
        DB::statement("COMMENT ON TABLE matriculaonline.renda_familiar IS '{$json}'");
        DB::statement("SELECT fc_gera_dicionario_apartir_tabela('matriculaonline', 'renda_familiar')");

        $json = mb_convert_encoding(json_encode($tabela[1]), 'ISO-8859-1');
        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('matriculaonline.base_necessidade_subdivisao');");
        DB::statement("COMMENT ON TABLE matriculaonline.base_necessidade_subdivisao IS '{$json}'");
        DB::statement("SELECT fc_gera_dicionario_apartir_tabela('matriculaonline', 'base_necessidade_subdivisao')");

        $c = [];
        foreach ($campos1 as $nomeCampo => $valoresCampo) {
            $c[$nomeCampo] = [
                "descricao" => $valoresCampo[0],
                "rotulo" => $valoresCampo[1],
                "rotulorel" => $valoresCampo[2],
                "maiusculo" => $valoresCampo[3],
                "autocompl" => $valoresCampo[4],
                "aceitatipo" => $valoresCampo[5],
                "tamanho" => $valoresCampo[6],
                "tipoobj" => $valoresCampo[7]
            ];
        }

        foreach ($c as $nomeCampo => $omentario) {
            $cm = mb_convert_encoding(json_encode($omentario), 'ISO-8859-1');
            $sql = "COMMENT ON COLUMN renda_familiar.{$nomeCampo} IS '{$cm}'";
            DB::statement($sql);
        }
        $campos1 = [
            'mo26_id' => ['ID','ID','ID',false,false,1,11,'text'],
            'mo26_base_necessidade' => ['Base Necessidade','Base Necessidade','Base Necessidade',false,false,1, 11,'text'],
            'mo26_subdivisao' => ['Subdivisao','Subdivisao','Subdivisao',false,false,1, 11,'text']
        ];

        $c = [];
        foreach ($campos1 as $nomeCampo => $valoresCampo) {
            $c[$nomeCampo] = [
                "descricao" => $valoresCampo[0],
                "rotulo" => $valoresCampo[1],
                "rotulorel" => $valoresCampo[2],
                "maiusculo" => $valoresCampo[3],
                "autocompl" => $valoresCampo[4],
                "aceitatipo" => $valoresCampo[5],
                "tamanho" => $valoresCampo[6],
                "tipoobj" => $valoresCampo[7]
            ];
        }
        foreach ($c as $nomeCampo => $omentario) {
            $cm = mb_convert_encoding(json_encode($omentario), 'ISO-8859-1');
            $sql = "COMMENT ON COLUMN base_necessidade_subdivisao.{$nomeCampo} IS '{$cm}'";
            DB::statement($sql);
        }

        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;");

        $this->insertZonas();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("SELECT fc_remove_dicionario_tabela('matriculaonline', 'renda_familiar');");
        Schema::dropIfExists('matriculaonline.renda_familiar');
    }

    public function insertZonas()
    {
        $zonas = [
            ["mo25_faixa" => "Nenhuma renda"],
            ["mo25_faixa" => "Até um salário mínimo"],
            ["mo25_faixa" => "Até 2 salários mínimos"],
            ["mo25_faixa" => "Até 3 salários mínimos"],
            ["mo25_faixa" => "Acima de 3 salários mínimos"]
        ];

        DB::table('matriculaonline.renda_familiar')->insert($zonas);
    }
}
