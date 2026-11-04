<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24478CroandoTabelaZonaResidencia extends Migration
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
        Schema::create('secretariadeeducacao.zonas_residencia', function (Blueprint $table) {
            $table->increments('ed194_id');
            $table->text('ed194_descriicao');
        });

        $tabelas = [[mb_convert_encoding('Zonas Residencia', 'UTF-8'), 'ed194', '2023-11-13', mb_convert_encoding('Zonas Residencia', 'UTF-8'), 0, false, false, false, false]];
        $tabela = [];
        foreach ($tabelas as $tab) {
            $tabela = [
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

        $campos = [
            'ed194_id' => ['ID','ID','ID',false,false,1,11,'text'],
            'ed194_descriicao' => ['Descricao','Descricao','Descricao',false,false,1, 200,'text']
        ];

        $json = mb_convert_encoding(json_encode($tabela), 'ISO-8859-1');
        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('secretariadeeducacao.zonas_residencia');");
        DB::statement("COMMENT ON TABLE secretariadeeducacao.zonas_residencia IS '{$json}'");
        DB::statement("SELECT fc_gera_dicionario_apartir_tabela('secretariadeeducacao', 'zonas_residencia')");

        $c = [];
        foreach ($campos as $nomeCampo => $valoresCampo) {
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
            $sql = "COMMENT ON COLUMN zonas_residencia.{$nomeCampo} IS '{$cm}'";
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
        DB::statement("SELECT fc_remove_dicionario_tabela('secretariadeeducacao', 'zonas_residencia');");
        Schema::dropIfExists('secretariadeeducacao.zonas_residencia');
    }

    public function insertZonas()
    {
        $zonas = [
            ["ed194_descriicao" => "Urbana"],
            ["ed194_descriicao" => "Rural"],
            ["ed194_descriicao" => "Urbana/Norte"],
            ["ed194_descriicao" => "Urbana/Sul"],
            ["ed194_descriicao" => "Urbana/Leste"],
            ["ed194_descriicao" => "Urbana/Oeste"]
        ];

        DB::table('secretariadeeducacao.zonas_residencia')->insert($zonas);
    }
}
