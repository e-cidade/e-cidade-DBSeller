<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28710 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
select fc_startsession() as SESSAO_INICIADA,
       fc_putsession('DB_instit'::varchar, (select codigo
                                     from configuracoes.db_config
                                    WHERE prefeitura is true
                                  )::varchar
                    ) as DB_instit_setado,
       fc_putsession('DB_datausu'::varchar, current_date::varchar) as datausu_setado,
       fc_putsession('DB_anousu'::varchar, extract(year from current_date)::varchar) as anousu_setado,
       fc_putsession('DB_id_usuario'::varchar, '1'::varchar) as usuario,
       fc_putsession('DB_login'::varchar, 'dbseller'::varchar) as login;

delete from planejamento.detalhamentoiniciativa where pl20_esferaorcamentaria = 0;
update orcdotacao set o58_esferaorcamentaria = 10 where o58_anousu = 2024 and o58_esferaorcamentaria = 0;

ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

select fc_gera_dicionario_apartir_tabela('planejamento', 'basecalculoprojecao');

ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;

select configuracoes.fc_auditoria_cria_funcao('planejamento.basecalculoprojecao');
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
select configuracoes.fc_auditoria_remove_funcao('planejamento.basecalculoprojecao');
SQL
        );
    }
}
