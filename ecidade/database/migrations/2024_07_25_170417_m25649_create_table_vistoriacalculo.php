<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25649CreateTableVistoriacalculo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upTable();
        $this->upDicionario();
    }

    public function upTable()
    {
        DB::statement("
            create table fiscal.vistoriacalculo (
                y123_codvist integer references fiscal.vistorias(y70_codvist),
                y123_numpre integer,
                y123_tipcalc integer references issqn.tipcalc(q81_codigo),
                y123_ativ integer references issqn.ativid(q03_ativ),
                y123_valor numeric(17,2),
                y123_valorbase numeric(17,2),
                y123_valorinflator numeric(17,2),
                y123_valorisencao numeric(17,2),
                y123_databasecalculo date,
                y123_datacalculo date,
                y123_anousu integer,
                y123_recei integer references caixa.tabrec(k02_codigo),
                primary key(y123_codvist,y123_numpre,y123_tipcalc,y123_ativ,y123_anousu)
            )
        ");
        DB::statement("select configuracoes.fc_auditoria_cria_funcao('fiscal.vistoriacalculo');");
    }

    public function upDicionario()
    {
        DB::unprepared("
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;
        ");

        DB::unprepared(<<<SQL
        COMMENT ON TABLE fiscal.vistoriacalculo IS
        '{ "descricao": "Tabela de calculo da vistoria",
            "sigla": "y123",
            "dataincl": "2024-07-25",
            "rotulo": "vistoriacalculo",
            "tipotabela": 0,
            "naolibclass": false,
            "naolibfunc": false,
            "naolibprog": false,
            "naolibform": false
        }';

        COMMENT ON COLUMN fiscal.vistoriacalculo.y123_codvist IS
        '{ "descricao": "Codigo da vistoria",
            "rotulo": "Cod. da vistoria",
            "rotulorel": "Codigo",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 1,
            "tamanho": 10,
            "tipoobj": "text"
        }';

        COMMENT ON COLUMN fiscal.vistoriacalculo.y123_numpre IS
        '{ "descricao": "Codigo do débito",
            "rotulo": "Numpre",
            "rotulorel": "Numpre",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 1,
            "tamanho": 10,
            "tipoobj": "text"
        }';

        COMMENT ON COLUMN fiscal.vistoriacalculo.y123_tipcalc IS
        '{ "descricao": "Tipo de calculo",
            "rotulo": "Tipcalc",
            "rotulorel": "Tipcalc",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 1,
            "tamanho": 10,
            "tipoobj": "text"
        }';

        COMMENT ON COLUMN fiscal.vistoriacalculo.y123_ativ IS
        '{ "descricao": "Atividade",
            "rotulo": "Atividade",
            "rotulorel": "Atividade",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 1,
            "tamanho": 10,
            "tipoobj": "text"
        }';

        COMMENT ON COLUMN fiscal.vistoriacalculo.y123_valor IS
        '{ "descricao": "Valor",
            "rotulo": "Valor",
            "rotulorel": "Valor",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 4,
            "tamanho": 10,
            "tipoobj": "text"
        }';

        COMMENT ON COLUMN fiscal.vistoriacalculo.y123_valorbase IS
        '{ "descricao": "Valor base",
            "rotulo": "Valor base",
            "rotulorel": "Valor base",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 4,
            "tamanho": 10,
            "tipoobj": "text"
        }';

        COMMENT ON COLUMN fiscal.vistoriacalculo.y123_valorinflator IS
        '{ "descricao": "Valor inflator",
            "rotulo": "Valor inflator",
            "rotulorel": "Valor inflator",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 4,
            "tamanho": 10,
            "tipoobj": "text"
        }';

        COMMENT ON COLUMN fiscal.vistoriacalculo.y123_valorisencao IS
        '{ "descricao": "Valor isençao",
            "rotulo": "Valor isençao",
            "rotulorel": "Valor isençao",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 4,
            "tamanho": 10,
            "tipoobj": "text"
        }';

        COMMENT ON COLUMN fiscal.vistoriacalculo.y123_databasecalculo IS
        '{ "descricao": "Data Base calculo",
            "rotulo": "Data Base",
            "rotulorel": "Data Base",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 3,
            "tamanho": 1,
            "tipoobj": "text"
        }';

        COMMENT ON COLUMN fiscal.vistoriacalculo.y123_datacalculo IS
        '{ "descricao": "Data calculo",
            "rotulo": "Data calculo",
            "rotulorel": "Data calculo",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 3,
            "tamanho": 10,
            "tipoobj": "text"
        }';

        COMMENT ON COLUMN fiscal.vistoriacalculo.y123_anousu IS
        '{ "descricao": "Ano de calculo",
            "rotulo": "Ano de calculo",
            "rotulorel": "Ano de calculo",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 1,
            "tamanho": "10",
            "tipoobj": "text"
        }';

        COMMENT ON COLUMN fiscal.vistoriacalculo.y123_recei IS
        '{ "descricao": "Receita",
            "rotulo": "Receita",
            "rotulorel": "Receita",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 1,
            "tamanho": "10",
            "tipoobj": "text"
        }'
SQL
        );

        DB::statement("select fc_gera_dicionario_apartir_tabela('fiscal', 'vistoriacalculo');");
        DB::unprepared("
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("drop table fiscal.vistoriacalculo");
    }
}
