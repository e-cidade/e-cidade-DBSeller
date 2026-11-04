<?php


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28780CreateMenuZoneamentodoPlanodeDesenvolvimentoUrbano extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            <<<SQL
                insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229347 ,'Zoneamento do Plano Diretor' ,'Zoneamento do Plano Diretor' ,'' ,'1' ,'1' ,'Zoneamento do Plano Diretor' ,'true' ,'false' );
                insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 29 ,229347 ,320 ,578 );
                insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229348 ,'Macrozonas' ,'Macrozonas' ,'' ,'1' ,'1' ,'Macrozonas' ,'true' ,'false' );
                insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 229347 ,229348 ,1 ,578 );
                insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229349 ,'Zonas' ,'Zonas' ,'' ,'1' ,'1' ,'Zonas' ,'true' ,'false' );
                insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 229347 ,229349 ,2 ,578 );
                insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229350 ,'Inclusуo' ,'Inclusуo de Macrozonas' ,'web/tributario/cadastro/procedimentos/zoneamento/cadastro_macrozonas' ,'1' ,'1' ,'Inclusуo de Macrozonas' ,'true' ,'false' );
                insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 229348 ,229350 ,1 ,578 );
                insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229351 ,'Inclusуo' ,'Inclusуo de Zonas' ,'web/tributario/cadastro/procedimentos/zoneamento/cadastro_zonas' ,'1' ,'1' ,'Inclusуo de Zonas' ,'true' ,'false' );
                insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 229349 ,229351 ,1 ,578 );
                insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229352 ,'Alteraчуo' ,'Alteraчуo de Macrozonas' ,'web/tributario/cadastro/procedimentos/zoneamento/alteracao_macrozonas' ,'1' ,'1' ,'Alteraчуo de Macrozonas' ,'true' ,'false' );
                insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 229348 ,229352 ,2 ,578 );
                insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229353 ,'Alteraчуo' ,'Alteraчуo de Zonas' ,'web/tributario/cadastro/procedimentos/zoneamento/alteracao_zonas' ,'1' ,'1' ,'Alteraчуo de Zonas' ,'true' ,'false' );
                insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 229349 ,229353 ,2 ,578 );



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
            delete from db_menu where id_item_filho in (229347,229348,229349, 229350, 229351, 229352, 229353) and modulo = 578;
            delete from db_itensmenu where id_item in (229347,229348,229349, 229350, 229351, 229352, 229353);
SQL
        );
    }
}
