<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class M28069UnificacaoCGS extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upEstrutura();
        $this->upItensMenu();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downEstrutura();
        $this->downItensMenu();
    }

    public function upEstrutura()
    {
DB::unprepared(<<<SQL
Alter table sau_cgscorreto add column s127_data_proc date;
Alter table sau_cgscorreto add column s127_hora_proc VARCHAR(5);
Alter table sau_cgscorreto add column s127_login_proc integer;
Alter table sau_cgscorreto add constraint sau_cgscorreto_login_proc_fk foreign key(s127_login_proc) references db_usuarios(id_usuario);
SQL
        );        
    }
    public function downEstrutura()
    {
DB::unprepared(<<<SQL
ALTER TABLE sau_cgscorreto DROP CONSTRAINT sau_cgscorreto_login_proc_fk;
ALTER TABLE sau_cgscorreto DROP COLUMN s127_data_proc,DROP COLUMN s127_hora_proc,DROP COLUMN s127_login_proc;
SQL
        );        
    }   

    public function upItensMenu(){
DB::unprepared(<<<SQL
update db_itensmenu set funcao = 'web/saude/ambulatorial/procedimentos/unificacao-cgs',descricao ='Unificar CGSs',help = 'Unificar CGSs',desctec='Unificar CGSs' where id_item = 8071;
update db_itensmenu set libcliente = 'f' where id_item in (8072,8073);
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229302 ,'CGSs Unificados' ,'CGSs Unificados' ,'web/saude/ambulatorial/consultas/consulta-cgs-unificado' ,'1' ,'1' ,'CGSs Unificados' ,'true' ,'false' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 31 ,229302 ,197 ,1000004 );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229303 ,'Processar Unificação' ,'Processar Unificação' ,'' ,'1' ,'1' ,'Processar Unificação' ,'true' ,'false' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 32 ,229303 ,586 ,1 );
SQL
        ); 
    } 

    public function downItensMenu(){
DB::unprepared(<<<SQL
update db_itensmenu set funcao = '',descricao ='CGS Duplos',help = 'CGS Duplos',desctec = 'Procedimentos -> CGS Duplos' where id_item = 8071;
update db_itensmenu set libcliente = 't' where id_item in (8072,8073);

delete from db_menu where id_item_filho in (229302,229303);
delete from db_itensmenu where id_item in (229302,229303);

SQL
        ); 
    }     
}