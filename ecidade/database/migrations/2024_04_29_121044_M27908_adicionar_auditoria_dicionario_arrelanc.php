<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M27908AdicionarAuditoriaDicionarioArrelanc extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

select fc_gera_dicionario_apartir_tabela('caixa', 'arrelanc');
select fc_gera_dicionario_apartir_tabela('caixa', 'arrelanc');

ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;

select configuracoes.fc_auditoria_cria_funcao('caixa.arrelanc');
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        $sql = <<<SQL
select configuracoes.fc_auditoria_remove_funcao('caixa.arrelanc');
SQL;
        $this->execute($sql);
    }

    private function execute($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
