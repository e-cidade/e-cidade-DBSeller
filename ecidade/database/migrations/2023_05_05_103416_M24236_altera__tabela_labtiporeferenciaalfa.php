<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24236AlteraTabelaLabtiporeferenciaalfa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update lab_tiporeferenciaalfa set la29_i_fixo = null;
delete from lab_tiporeferenciaalfa where not exists (select 1 from lab_valorrefselgrupo where la51_i_referencia = la29_i_codigo);
update db_syscampo set nomecam = 'la29_v_fixo', conteudo = 'varchar(30)', descricao = 'valor referencial fixo do exame', valorinicial = '', rotulo = 'Valor Referencial Fixo', nulo = 't', tamanho = 30, maiusculo = 'f', autocompl = 'f', aceitatipo = 0, tipoobj = 'text', rotulorel = 'Valor Referencial Fixo' where codcam = 16502;
alter table lab_tiporeferenciaalfa alter column la29_i_fixo type varchar(30),
alter column la29_i_fixo set default null;
alter table lab_tiporeferenciaalfa rename column la29_i_fixo to la29_v_fixo;
SQL
        );
        DB::statement("select configuracoes.fc_auditoria_remove_funcao('laboratorio.lab_tiporeferenciaalfa');");
    }

    /**q
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getPdo()->exec(<<<SQL

update db_syscampo set nomecam = 'la29_i_fixo', conteudo = 'int4(10)', descricao = 'valor referencial fixo do exame', valorinicial = '0', rotulo = 'Valor Referencial Fixo', nulo = 't', tamanho = 10, maiusculo = 't', autocompl = 'f', aceitatipo = 0, tipoobj = 'text', rotulorel = 'Valor Referencial Fixo' where codcam = 16502;
alter table lab_tiporeferenciaalfa
    alter column la29_v_fixo drop default,
    alter column la29_v_fixo type integer USING la29_v_fixo::integer,
    alter column la29_v_fixo set default 0;
alter table lab_tiporeferenciaalfa rename column la29_v_fixo to la29_i_fixo;
SQL
        );
        DB::statement("select configuracoes.fc_auditoria_cria_funcao('laboratorio.lab_tiporeferenciaalfa');");

    }
}
