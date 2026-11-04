<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24007CriacaoItensmenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        $sql = <<<SQL

--
-- Itens de Menu
--
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228889 ,'TCE/RN' ,'TCE/RN' ,'' ,'1' ,'1' ,'Geração de arquivos de entrega para o TCE/RN' ,'true' );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228890 ,'Gerar Siai' ,'Gerar Siai' ,'con4_gerarSIAI001.php' ,'1' ,'1' ,'Gerar Siai' ,'true' );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228891 ,'Geração Arquivo LOA XML' ,'Geração Arquivo LOA XML' ,'con4_gerarSIAILOAXML001.php' ,'1' ,'1' ,'Geração Arquivo LOA XML' ,'true' );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228892 ,'Gerar SIAI XML' ,'Gerar SIAI XML' ,'con4_gerarSIAIXML001.php' ,'1' ,'1' ,'con4_gerarSIAIXML001.php' ,'true' );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228893 ,'Geração PPA XML' ,'Geração PPA XML' ,'con4_gerarSIAIPPAXML001.php' ,'1' ,'1' ,'Geração PPA XML' ,'true' );

insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 6819 ,228889 ,7 ,209 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228889 ,228890 ,1 ,209 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228889 ,228891 ,2 ,209 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228889 ,228892 ,3 ,209 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228889 ,228893 ,4 ,209 );

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
--
-- Itens de menu
--
delete from db_menu where id_item_filho in (228889, 228890, 228891, 228892, 228893);
delete from db_itensmenu where id_item in (228889, 228890, 228891, 228892, 228893);
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
