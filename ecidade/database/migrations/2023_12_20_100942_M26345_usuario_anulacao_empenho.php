<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26345UsuarioAnulacaoEmpenho extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
alter table empenho.empanulado add column e94_usuario integer default null;
alter table empenho.empanulado add FOREIGN KEY(e94_usuario) references configuracoes.db_usuarios(id_usuario);

ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

COMMENT ON COLUMN empenho.empanulado.e94_usuario IS  '{
  "descricao": "Usuário que realizou a anulação.",
  "rotulo": "Usuário",
  "rotulorel": "Usuário",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

SELECT fc_gera_dicionario_apartir_tabela('empenho', 'empanulado');

ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;

select configuracoes.fc_auditoria_cria_funcao('empenho.empanulado');
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
alter table empenho.empanulado drop column e94_usuario;
select configuracoes.fc_auditoria_remove_funcao('empenho.empanulado');
SQL
        );
    }
}
