<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class M29423EstruturaUnidadesOrigem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upEstrutura();
        $this->upDicionario();
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

CREATE TABLE ambulatorial.unidadesorigem (
  sd113_sequencial SERIAL NOT NULL PRIMARY KEY,
  sd113_descricao VARCHAR(255),
  sd113_ativo  BOOLEAN DEFAULT TRUE
);
select configuracoes.fc_auditoria_cria_funcao('ambulatorial.unidadesorigem');

create table ambulatorial.cgs_undunidadesorigem(
  sd114_sequencial SERIAL NOT NULL PRIMARY KEY,
  sd114_cgs_und INT,
  sd114_unidadesorigem INT,
  CONSTRAINT cgs_undunidadesorigem_cgs_fk FOREIGN KEY(sd114_cgs_und) REFERENCES ambulatorial.cgs_und(z01_i_cgsund),
  CONSTRAINT cgs_undunidadesorigem_unidades_fk FOREIGN KEY (sd114_unidadesorigem) REFERENCES ambulatorial.unidadesorigem(sd113_sequencial)
);

select configuracoes.fc_auditoria_cria_funcao('ambulatorial.cgs_undunidadesorigem');
SQL
        );    
    }

    public function downEstrutura()
    {
DB::unprepared(<<<SQL
DROP TABLE ambulatorial.cgs_undunidadesorigem;
DROP TABLE ambulatorial.unidadesorigem;
SQL
        ); 
    }
    
    public function upDicionario()
    {
DB::unprepared(<<<SQL

ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

COMMENT ON TABLE ambulatorial.unidadesorigem IS '{
  "descricao": "Unidades de origem",
  "sigla": "sd113",
  "dataincl": "2024-11-11",
  "rotulo": "Unidades Origem",
  "tipotabela": 0,
  "naolibclass": false,
  "naolibfunc": false,
  "naolibprog": false,
  "naolibform": false
}';

COMMENT ON COLUMN ambulatorial.unidadesorigem.sd113_sequencial IS '{
  "descricao": "Sequencial da unidade de origem",
  "rotulo": "Sequencial",
  "rotulorel": "Sequencial",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 0,
  "tamanho": 10,
  "tipoobj": "text"
}';

COMMENT ON COLUMN ambulatorial.unidadesorigem.sd113_descricao IS '{
  "descricao": "Descrição da unidade de origem",
  "rotulo": "Descrição",
  "rotulorel": "Descrição",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 0,
  "tamanho": 10,
  "tipoobj": "text"
}';

COMMENT ON COLUMN ambulatorial.unidadesorigem.sd113_ativo IS '{
  "descricao": "Verifica se a unidade origem está ativa.",
  "rotulo": "Ativo",
  "rotulorel": "Ativo",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 0,
  "tamanho": 10,
  "tipoobj": "text"
}';

SELECT fc_gera_dicionario_apartir_tabela('ambulatorial', 'unidadesorigem');
SELECT fc_gera_dicionario_apartir_tabela('ambulatorial', 'unidadesorigem');



COMMENT ON TABLE ambulatorial.cgs_undunidadesorigem IS '{
  "descricao": "Unidade de origem do CGS",
  "sigla": "sd114",
  "dataincl": "2024-11-11",
  "rotulo": "Unidade Origem CGS",
  "tipotabela": 0,
  "naolibclass": false,
  "naolibfunc": false,
  "naolibprog": false,
  "naolibform": false
}';


COMMENT ON COLUMN ambulatorial.cgs_undunidadesorigem.sd114_sequencial IS '{
  "descricao": "Sequencial da unidade de origem do CGS",
  "rotulo": "Sequencial",
  "rotulorel": "Sequencial",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 0,
  "tamanho": 10,
  "tipoobj": "text"
}';

COMMENT ON COLUMN ambulatorial.cgs_undunidadesorigem.sd114_cgs_und IS '{
  "descricao": "Cgs",
  "rotulo": "Cgs",
  "rotulorel": "Cgs",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 0,
  "tamanho": 10,
  "tipoobj": "text"
}';


COMMENT ON COLUMN ambulatorial.cgs_undunidadesorigem.sd114_unidadesorigem IS '{
  "descricao": "Unidade de origem do cgs",
  "rotulo": "Unidade Origem",
  "rotulorel": "Unidade Origem",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 0,
  "tamanho": 10,
  "tipoobj": "text"
}';


SELECT fc_gera_dicionario_apartir_tabela('ambulatorial', 'cgs_undunidadesorigem');
SELECT fc_gera_dicionario_apartir_tabela('ambulatorial', 'cgs_undunidadesorigem');

ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL
        );         
    }

    public function upItensMenu()
    {
DB::unprepared(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229340 ,'Unidade Origem' ,'Unidade Origem' ,'web/saude/ambulatorial/cadastros/unidade-origem' ,'1' ,'1' ,'Unidade de origem' ,'true' ,'false' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 3470 ,229340 ,53 ,1000004 );
SQL
        );
    }

    public function downItensMenu()
    {
DB::unprepared(<<<SQL
delete from db_menu where id_item_filho = 229340 AND modulo = 1000004;
delete from db_itensmenu where id_item = 229340;
SQL
        );
    }
}
