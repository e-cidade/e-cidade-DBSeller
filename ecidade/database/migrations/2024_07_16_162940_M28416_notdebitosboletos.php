<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M28416Notdebitosboletos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;
        
        CREATE TABLE caixa.notdebitosboletos (
            k218_numpre int4 NOT NULL,
            k218_notifica int4 NOT NULL,
            k218_dtvenc date NOT NULL,
            k218_dtemissao date NOT NULL,
            k218_processa_desconto bool DEFAULT false NOT NULL,
            CONSTRAINT caixa_notdebitosboletos_k218_notifica_foreign FOREIGN KEY (k218_notifica) REFERENCES caixa.notificacao(k50_notifica)
        );

        COMMENT ON TABLE caixa.notdebitosboletos IS
          '{
              "descricao": "Tabela responsavel pelo parametro de emissуo de notificaчуo por lista",
              "sigla": "k218",
              "dataincl": "2024-07-25",
              "rotulo": "Notificaчуo debitos boletos",
              "tipotabela": 0,
              "naolibclass": false,
              "naolibfunc": false,
              "naolibprog": false,
              "naolibform": false
           }';

        COMMENT ON COLUMN caixa.notdebitosboletos.k218_numpre IS
          '{
              "descricao": "Numpre",
              "rotulo": "Numpre",
              "rotulorel": "Numpre",
              "maiusculo": false,
              "autocompl": false,
              "aceitatipo": 1,
              "tamanho": 10,
              "tipoobj": "text"
           }';

        COMMENT ON COLUMN caixa.notdebitosboletos.k218_notifica IS
          '{
              "descricao": "Numero da Notificaчуo",
              "rotulo": "Numero da Notificaчуo",
              "rotulorel": "Numero da Notificaчуo",
              "maiusculo": false,
              "autocompl": false,
              "aceitatipo": 1,
              "tamanho": 10,
              "tipoobj": "text"
           }';

        COMMENT ON COLUMN caixa.notdebitosboletos.k218_dtvenc IS
          '{
              "descricao": "Data da vencimento",
              "rotulo": "Data da vencimento",
              "rotulorel": "Data da vencimento",
              "maiusculo": false,
              "autocompl": false,
              "aceitatipo": 0,
              "tamanho": 10,
              "tipoobj": "text"
           }';

        COMMENT ON COLUMN caixa.notdebitosboletos.k218_dtemissao IS
          '{
              "descricao": "Data da emissуo",
              "rotulo": "Data da emissуo",
              "rotulorel": "Data da emissуo",
              "maiusculo": false,
              "autocompl": false,
              "aceitatipo": 0,
              "tamanho": 10,
              "tipoobj": "text"
           }';

        COMMENT ON COLUMN caixa.notdebitosboletos.k218_processa_desconto IS
          '{
              "descricao": "Se processou desconto com desconto",
              "rotulo": "Processar desconto",
              "rotulorel": "Processar desconto",
              "maiusculo": false,
              "autocompl": false,
              "aceitatipo": 5,
              "tamanho": 10,
              "tipoobj": "text"
           }';

        SELECT fc_gera_dicionario_apartir_tabela('caixa', 'notdebitosboletos');

        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
        SELECT configuracoes.fc_auditoria_cria_funcao('caixa.notdebitosboletos');
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
        DB::unprepared(<<<SQL
            select configuracoes.fc_auditoria_remove_funcao('caixa.notdebitosboletos ');
            DROP TABLE caixa.notdebitosboletos;
SQL
        );
    }
}
