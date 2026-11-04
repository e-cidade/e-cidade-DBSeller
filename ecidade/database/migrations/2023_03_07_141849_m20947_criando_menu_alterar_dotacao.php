<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M20947CriandoMenuAlterarDotacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
        insert into configuracoes.db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228865 ,'Alteração de Dotação' ,'Alteração de Dotação' ,'web/patrimonial/contratos/alterar-dotacao' ,'1' ,'1' ,'Alteração de Dotação' ,'true' );
        insert into configuracoes.db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 8568 ,228865 ,9 ,8251 );
        alter table acordoitemdotacao alter column ac22_sequencial SET DEFAULT nextval('acordoitemdotacao_ac22_sequencial_seq'::regclass);
SQL;
        DB::connection()->getPdo()->exec($sql);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL
            DELETE FROM configuracoes.db_menu WHERE id_item_filho = 228865;
            DELETE FROM configuracoes.db_itensmenu WHERE id_item = 228865;
            alter table acordoitemdotacao alter column ac22_sequencial SET DEFAULT 0;
SQL;
        DB::connection()->getPdo()->exec($sql);

    }
}
