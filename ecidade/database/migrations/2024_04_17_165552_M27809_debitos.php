<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27809Debitos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
        $this->upTabelas();
    }

    public function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            update db_itensmenu set descricao = 'Relatório Exercício x Procedência - Cobrança Administrativa',
                                    desctec = 'Relatório Exercício x Procedência - Cobrança Administrativa'
            where id_item = 228328;
SQL
        );
    }

    public function upTabelas()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            with cte_datadebitos as (
                    select k115_data,
                           ('debitos_'||to_char(k115_data, 'YYYYMMDD')||'_'||to_char(k115_instit, 'FM0'))::varchar
                               as tabela,
                           k115_instit
                    from datadebitos
                 ),

                 cte_debitos as (
                    select k115_data,
                           k115_instit,
                           tabela,
                           case when table_name is not null then true
                                else false
                           end as tem_debitos
                    from cte_datadebitos
                         left join information_schema.tables on table_schema = 'caixa'
                                                            and table_name = tabela
                 )

            delete from datadebitos
               using cte_debitos
               where datadebitos.k115_instit = cte_debitos.k115_instit
                 and datadebitos.k115_data = cte_debitos.k115_data
                 and cte_debitos.tem_debitos is false;
SQL
        );

        DB::connection()->getPdo()->exec(<<<SQL

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            alter table if exists caixa.datadebitos add column if not exists k115_completa boolean default true;
            select setval('datadebitos_k115_sequencial_seq', (select max(k115_sequencial) from datadebitos));
            alter table if exists caixa.datadebitos alter column k115_sequencial
                set default nextval('datadebitos_k115_sequencial_seq');

            SELECT fc_atualiza_dicionario_apartir_comentario('table column',
                   'caixa.datadebitos.k115_completa',
                   '{ "descricao": "Se existe uma lista para essa data, o campo é falso",
                      "rotulo": "Tabela completa",
                      "rotulorel": "Tabela completa",
                      "maiusculo": false,
                      "autocompl": false,
                      "aceitatipo": 5,
                      "tamanho": 1,
                      "tipoobj": "text"
                    }') ;

            select configuracoes.fc_auditoria_cria_funcao('caixa.datadebitos');

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
        $this->downTabelas();
    }

    public function downTabelas()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            alter table if exists caixa.datadebitos drop column if exists k115_completa;
            select configuracoes.fc_auditoria_remove_funcao('caixa.datadebitos');

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL
        );
    }
}
