<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class M29010CriaDicionarioOcupacoesAluno extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        $this->alteraTrigger();
        $this->comentaCampos();
        $this->upDicionario();
        $this->upDicionario();
        $this->retornaTrigger();
    }

    public function comentaCampos()
    {
      DB::unprepared(<<<SQL

COMMENT ON COLUMN escola.aluno.ed47_i_ocupacaopai IS '{
  "descricao": "Ocupação do Pai",
  "rotulo": "Ocupação Pai",
  "rotulorel": "Ocupação do Pai",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 0,
  "tamanho": 10,
  "tipoobj": "text"
}';


COMMENT ON COLUMN escola.aluno.ed47_i_ocupacaomae IS '{
  "descricao": "Ocupação da Mãe",
  "rotulo": "Ocupação Mãe",
  "rotulorel": "Ocupação da Mãe",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 0,
  "tamanho": 10,
  "tipoobj": "text"
}'; 

COMMENT ON COLUMN escola.aluno.ed47_i_ocupacaoresp IS '{
  "descricao": "Ocupação do Responsáveis",
  "rotulo": "Ocupação Responsável",
  "rotulorel": "Ocupação dos Responsáveis",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 0,
  "tamanho": 10,
  "tipoobj": "text"
}';

SQL
    );


    }


    public function upDicionario()
    {
      DB::unprepared(<<<SQL
select fc_gera_dicionario_apartir_tabela('escola', 'aluno');
SQL
    );
    }

    public function alteraTrigger()
    {
      DB::unprepared(<<<SQL
ALTER TABLE db_sysarquivo DISABLE TRIGGER ALL;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
SQL
    );
    }   

    public function retornaTrigger()
    {
      DB::unprepared(<<<SQL
ALTER TABLE db_sysarquivo ENABLE TRIGGER ALL;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL
    );
    } 
    
    

    public function down()
    {

    }
}
