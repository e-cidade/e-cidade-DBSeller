<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M27392AdicionaCampoGerafinanceiro extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            ALTER TABLE cadastro.iptucalc ADD j23_gerafinanc bool DEFAULT true NULL;
            ALTER TABLE cadastro.iptucalcold ADD j223_gerafinanc bool DEFAULT true NULL;
            ALTER TABLE cadastro.iptucalclog ADD j27_geralfinanc bool DEFAULT true NULL;
            ALTER TABLE cadastro.iptucalclogmat ADD j28_gerafinanc bool DEFAULT true NULL;
            ALTER TABLE cadastro.iptucalv ADD j21_gerafinanc bool DEFAULT true NULL;
            ALTER TABLE cadastro.iptucalvold ADD j157_gerafinanc bool DEFAULT true NULL;
            ALTER TABLE cadastro.iptucale ADD j22_gerafinanc bool DEFAULT true NULL;
            ALTER TABLE cadastro.iptucaleold ADD j162_gerafinanc bool DEFAULT true NULL;

            COMMENT ON COLUMN cadastro.iptucalc.j23_gerafinanc IS
                '{
                    "descricao": "Foi Calculado o Financeiro",
                    "rotulo": "Gera debito financeiro",
                    "rotulorel": "Gera debito financeiro",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 5,
                    "tamanho": 10,
                    "tipoobj": "checkbox"
                }';
            COMMENT ON COLUMN cadastro.iptucalcold.j223_gerafinanc IS
                '{
                    "descricao": "Foi Calculado o Financeiro",
                    "rotulo": "Gera debito financeiro",
                    "rotulorel": "Gera debito financeiro",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 5,
                    "tamanho": 10,
                    "tipoobj": "checkbox"
                }';
            COMMENT ON COLUMN cadastro.iptucalclog.j27_geralfinanc IS
                '{
                    "descricao": "Foi Calculado o Financeiro",
                    "rotulo": "Gera debito financeiro",
                    "rotulorel": "Gera debito financeiro",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 5,
                    "tamanho": 10,
                    "tipoobj": "checkbox"
                }';
            COMMENT ON COLUMN cadastro.iptucalclogmat.j28_gerafinanc IS
                '{
                    "descricao": "Foi Calculado o Financeiro",
                    "rotulo": "Gera debito financeiro",
                    "rotulorel": "Gera debito financeiro",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 5,
                    "tamanho": 10,
                    "tipoobj": "checkbox"
                }';
            COMMENT ON COLUMN cadastro.iptucalv.j21_gerafinanc IS
                '{
                    "descricao": "Foi Calculado o Financeiro",
                    "rotulo": "Gera debito financeiro",
                    "rotulorel": "Gera debito financeiro",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 5,
                    "tamanho": 10,
                    "tipoobj": "checkbox"
                }';
            COMMENT ON COLUMN cadastro.iptucalvold.j157_gerafinanc IS
                '{
                    "descricao": "Foi Calculado o Financeiro",
                    "rotulo": "Gera debito financeiro",
                    "rotulorel": "Gera debito financeiro",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 5,
                    "tamanho": 10,
                    "tipoobj": "checkbox"
                }';
            COMMENT ON COLUMN cadastro.iptucale.j22_gerafinanc IS
                '{
                    "descricao": "Foi Calculado o Financeiro",
                    "rotulo": "Gera debito financeiro",
                    "rotulorel": "Gera debito financeiro",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 5,
                    "tamanho": 10,
                    "tipoobj": "checkbox"
                }';
            COMMENT ON COLUMN cadastro.iptucaleold.j162_gerafinanc IS
                '{
                    "descricao": "Foi Calculado o Financeiro",
                    "rotulo": "Gera debito financeiro",
                    "rotulorel": "Gera debito financeiro",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 5,
                    "tamanho": 10,
                    "tipoobj": "checkbox"
                }';
            SELECT fc_gera_dicionario_apartir_tabela('cadastro', 'iptucalc');
            SELECT fc_gera_dicionario_apartir_tabela('cadastro', 'iptucalcold');
            SELECT fc_gera_dicionario_apartir_tabela('cadastro', 'iptucalclog');
            SELECT fc_gera_dicionario_apartir_tabela('cadastro', 'iptucalclogmat');
            SELECT fc_gera_dicionario_apartir_tabela('cadastro', 'iptucalv');
            SELECT fc_gera_dicionario_apartir_tabela('cadastro', 'iptucalvold');
            SELECT fc_gera_dicionario_apartir_tabela('cadastro', 'iptucale');
            SELECT fc_gera_dicionario_apartir_tabela('cadastro', 'iptucaleold');
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
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
            ALTER TABLE cadastro.iptucalc DROP COLUMN j23_gerafinanc;
            ALTER TABLE cadastro.iptucalcold DROP COLUMN j223_gerafinanc;
            ALTER TABLE cadastro.iptucalclog DROP COLUMN j27_geralfinanc;
            ALTER TABLE cadastro.iptucalclogmat DROP COLUMN j28_gerafinanc;
            ALTER TABLE cadastro.iptucalv DROP COLUMN j21_gerafinanc;
            ALTER TABLE cadastro.iptucalvold DROP COLUMN j157_gerafinanc;
            ALTER TABLE cadastro.iptucale DROP COLUMN j22_gerafinanc;
            ALTER TABLE cadastro.iptucaleold DROP COLUMN j162_gerafinanc;
SQL
        );
    }
}
