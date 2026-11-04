<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M18884AdicionaMenusPncp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_sysmodulo values (91,'PNCP','Portal Nacional de Contratações Públicas','2022-11-14','t');
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228802 ,'PNCP' ,'PNCP' ,'' ,'2' ,'1' ,'Módulo Portal Nacional de Contratações Públicas' ,'true' );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228800 ,'Cadastros' ,'Cadastros' ,'' ,'1' ,'1' ,'Cadastros Portal Nacional de Contratações Públicas' ,'true' );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228803 ,'Consultas' ,'Consultas' ,'' ,'1' ,'1' ,'Consultas Portal Nacional de Contrações Públicas' ,'true' );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228804 ,'Procedimentos' ,'Procedimentos' ,'' ,'1' ,'1' ,'Procedimentos Portal Nacional de Contrações Públicas' ,'true' );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228805 ,'Parâmetros' ,'Parâmetros' ,'' ,'1' ,'1' ,'Parâmetros PNCP' ,'true' );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228806 ,'Integração PNCP' ,'Integração PNCP' ,'pncp4_integracaopncp001.php' ,'1' ,'1' ,'Parâmetro para fazer a integração com o PNCP' ,'true' );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228807 ,'Unidade Compradora' ,'Unidade Compradora' ,'pncp1_unidadecompradora001.php' ,'1' ,'1' ,'Unidade Compradora PNCP' ,'true' );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228809 ,'Unidade Compradora' ,'Unidade Compradora' ,'pncp3_unidadecompradora001.php' ,'1' ,'1' ,'Consulta de Unidade Compradora' ,'true' );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228816 ,'Compra/Edital/Aviso' ,'Compra/Edital/Aviso' ,'' ,'1' ,'1' ,'Compra/Edital/Aviso PNCP' ,'true' );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228817 ,'Inclusão' ,'Inclusão' ,'pncp1_compraeditalaviso001.php' ,'1' ,'1' ,'Inclusão de Compra/Edital/Aviso PNCP' ,'true' );
insert into db_modulos( id_item ,nome_modulo ,descr_modulo ,imagem ,temexerc ) values ( 228802 ,'PNCP' ,'PNCP' ,'pncp.png' ,'true' );
insert into atendcadareamod ( at26_sequencia ,at26_codarea ,at26_id_item ) values ( 85 ,4 ,228802 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228802 ,228800 ,1 ,228802 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228802 ,228803 ,2 ,228802 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228802 ,228804 ,3 ,228802 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228800 ,228807 ,1 ,228802 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228807 ,228808 ,1 ,228802 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228803 ,228809 ,1 ,228802 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228805 ,228806 ,1 ,228802 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228804 ,228805 ,1 ,228802 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228804 ,228816 ,1 ,228802 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228816 ,228817 ,1 ,228802 );
update db_itensmenu set id_item = 228816 , descricao = 'Compra/Edital/Aviso' , help = 'Compra/Edital/Aviso' , itemativo = '1' , manutencao = '1' , desctec = 'Compra/Edital/Aviso PNCP' , libcliente = 'true' where id_item = 228816;
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
delete from db_menu where id_item_filho = 228816 AND modulo = 228802;
delete from db_menu where id_item_filho = 228817 AND modulo = 228802;
delete from db_menu where id_item_filho = 228800 AND modulo = 228802;
delete from db_menu where id_item_filho = 228803 AND modulo = 228802;
delete from db_menu where id_item_filho = 228804 AND modulo = 228802;
delete from db_menu where id_item_filho = 228805 AND modulo = 228802;
delete from db_menu where id_item_filho = 228806 AND modulo = 228802;
delete from db_menu where id_item_filho = 228807 AND modulo = 228802;
delete from db_menu where id_item_filho = 228808 AND modulo = 228802;
delete from db_menu where id_item_filho = 228809 AND modulo = 228802;
delete from db_itensmenu where id_item = 228802;
delete from db_itensmenu where id_item = 228800;
delete from db_itensmenu where id_item = 228803;
delete from db_itensmenu where id_item = 228804;
delete from db_itensmenu where id_item = 228805;
delete from db_itensmenu where id_item = 228806;
delete from db_itensmenu where id_item = 228807;
delete from db_itensmenu where id_item = 228809;
delete from db_itensmenu where id_item = 228816;
delete from db_itensmenu where id_item = 228817;
delete from atendcadareamod where at26_sequencia = 85;
delete from db_modulos where id_item = 228802;
delete from db_sysmodulo where codmod = 91;
SQL
        );
    }
}
