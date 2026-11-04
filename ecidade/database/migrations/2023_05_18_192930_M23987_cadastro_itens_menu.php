<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23987CadastroItensMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) 
                 values ( 228920 ,
                          'Alunos com Certidões Inválidas' ,
                          'Alunos com Certidões Inválidas' ,
                          'edu2_alunosmatriculacertidaoinvalida001.php' ,
                          '1' ,
                          '1' ,
                          'Alunos com Certidões Inválidas' ,
                          'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) 
            values ( 6871 ,228920 ,6 ,7159 );
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
        delete from db_menu where id_item_filho = 228920;
        delete from db_itensmenu where id_item = 228920;
SQL;
        
        DB::connection()->getPdo()->exec($sql);
    }
}
