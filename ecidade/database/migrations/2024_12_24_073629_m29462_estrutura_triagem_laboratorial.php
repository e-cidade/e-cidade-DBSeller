<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class M29462EstruturaTriagemLaboratorial extends Migration
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
        $this->upDados();
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


-- Tabela motivosrejeicaoamostra
CREATE TABLE laboratorio.motivosrejeicaoamostra(
  la73_sequencial serial primary key,
  la73_descricao varchar(100)
);

select configuracoes.fc_auditoria_cria_funcao('laboratorio.motivosrejeicaoamostra');

-- Tabela lab_requiitem
Alter table laboratorio.lab_requiitem add column la21_motivosrejeicaoamostra integer;
Alter table laboratorio.lab_requiitem add column la21_outromotivorejeicao text;
Alter table laboratorio.lab_requiitem add constraint lab_requiitem_motivosrejeicaoamostra_fk foreign key (la21_motivosrejeicaoamostra) references  laboratorio.motivosrejeicaoamostra(la73_sequencial); 

-- Tabela lab_parametros
Alter table laboratorio.lab_parametros ADD COLUMN la49_habilitartriagemlaboratorial boolean not null default false;
Alter table laboratorio.lab_parametros ADD COLUMN la49_unidadesdispensadastriagem varchar(255);

-- Tabela lab_triagemitem

CREATE TABLE laboratorio.lab_triagemitem (
    la74_sequencial serial primary key,
    la74_usuario integer not null,
    la74_requiitem integer not null,    
    created_at timestamp,
    updated_at timestamp,
    constraint lab_triagemitem_requiitem_fk foreign key (la74_requiitem) references lab_requiitem(la21_i_codigo),
    constraint lab_triagemitem_usuario_fk foreign key (la74_usuario) references db_usuarios(id_usuario)
);

select configuracoes.fc_auditoria_cria_funcao('laboratorio.lab_triagemitem');

SQL
    );        
    }  

    public function upDicionario()
    {
        DB::unprepared(<<<SQL

ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;


-- TABELA lab_parametros
COMMENT ON COLUMN laboratorio.lab_parametros.la49_identificaprioridade IS '{
  "descricao": "Indica se o tipo de pioridade deve ser exibida na etiqueta.",
  "rotulo": "Habilitar Identificação Prioridade de Amostra.",
  "rotulorel": "Exibir Prioridade.",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 5,
  "tamanho": 10,
  "tipoobj": "text"
}';

COMMENT ON COLUMN laboratorio.lab_parametros.la49_habilitartriagemlaboratorial IS '{
  "descricao": "Indica se a triagem laboratorial está habilitada ou não.",
  "rotulo": "Habilitar Triagem Taboratorial",
  "rotulorel": "Habilitar Triagem Laboratorial",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 5,
  "tamanho": 10,
  "tipoobj": "text"
}';


COMMENT ON COLUMN laboratorio.lab_parametros.la49_unidadesdispensadastriagem IS '{
  "descricao": "Unidades que serão dispensadas da triagem laboratorial",
  "rotulo": "Unidades Dispensadas da Triagem",
  "rotulorel": "Unidades Dispensadas da Triagem",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 0,
  "tamanho": 10,
  "tipoobj": "text"
}';



SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'lab_parametros');
SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'lab_parametros');


-- TABELA motivosrejeicaoamostra

COMMENT ON TABLE laboratorio.motivosrejeicaoamostra IS '{
  "descricao": "Motivos de rejeição da amostra",
  "sigla": "la73",
  "dataincl": "2024-12-18",
  "rotulo": "Motivos Rejeição Amostra",
  "tipotabela": 1,
  "naolibclass": false,
  "naolibfunc": false,
  "naolibprog": false,
  "naolibform": false
}';


COMMENT ON COLUMN laboratorio.motivosrejeicaoamostra.la73_sequencial IS '{
  "descricao": "Sequencial dos motivos de rejeição da amostra",
  "rotulo": "Sequencial",
  "rotulorel": "Sequencial",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 1,
  "tamanho": "10",
  "tipoobj": "text"
}';

COMMENT ON COLUMN laboratorio.motivosrejeicaoamostra.la73_descricao IS '{
  "descricao": "Descrição dos motivos de rejeição da amostra",
  "rotulo": "Descrição",
  "rotulorel": "Descrição",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 0,
  "tamanho": 10,
  "tipoobj": "text"
}';


SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'motivosrejeicaoamostra');
SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'motivosrejeicaoamostra');


-- Tabela lab_triagemitem

COMMENT ON TABLE laboratorio.lab_triagemitem IS '{
  "descricao": "Item da triagem",
  "sigla": "la74",
  "dataincl": "2024-12-24",
  "rotulo": "Item Triagem",
  "tipotabela": 0,
  "naolibclass": false,
  "naolibfunc": false,
  "naolibprog": false,
  "naolibform": false
}';

COMMENT ON COLUMN laboratorio.lab_triagemitem.la74_sequencial IS '{
  "descricao": "Sequencial do  item da triagem",
  "rotulo": "Sequencial",
  "rotulorel": "Sequencial",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

COMMENT ON COLUMN laboratorio.lab_triagemitem.la74_usuario IS '{
  "descricao": "Usuário solicitante do  item da triagem",
  "rotulo": "Usuário",
  "rotulorel": "Usuário",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';


COMMENT ON COLUMN laboratorio.lab_triagemitem.la74_requiitem IS '{
  "descricao": "Item da requisição da triagem",
  "rotulo": "Item Requisição",
  "rotulorel": "Item Requisição",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';


COMMENT ON COLUMN laboratorio.lab_triagemitem.created_at IS '{
  "descricao": "Data da criação",
  "rotulo": "Data Criação",
  "rotulorel": "Data Criação",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 0,
  "tamanho": 10,
  "tipoobj": "text"
}';

COMMENT ON COLUMN laboratorio.lab_triagemitem.updated_at IS '{
  "descricao": "Data da atualização",
  "rotulo": "Data Atualização",
  "rotulorel": "Data Atualização",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 0,
  "tamanho": 10,
  "tipoobj": "text"
}';

SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'lab_triagemitem');
SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'lab_triagemitem');

ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
      
SQL
    );  
    }

    public function upDados()
    {
        DB::unprepared(<<<SQL

INSERT INTO laboratorio.motivosrejeicaoamostra(la73_descricao)
VALUES 
('Amostra Coagulada'),
('Amostra Hemolisada'),
('Amostra Lipêmica'),
('Amostra Não Veio'),
('Estabilidade Excedida'),
('Quantidade Inadequada'),
('Tubo Incorreto'),
('Outros Motivos');
SQL
    );  
    }

    public function upItensMenu()
    {
        DB::unprepared(<<<SQL


-- Rotina da triagem laboratorial
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229376 ,'Triagem Laboratorial' ,'Triagem Laboratorial' ,'lab4_triagemlaboratorial001.php' ,'1' ,'1' ,'Triagem Laboratorial' ,'true' ,'false' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 8273 ,229376 ,4 ,8167 );

SQL
    );  
    }       
    public function downEstrutura()
    {
        DB::unprepared(<<<SQL

Alter table laboratorio.lab_requiitem drop column la21_motivosrejeicaoamostra;
Alter table laboratorio.lab_requiitem drop column la21_outromotivorejeicao;
Alter table laboratorio.lab_parametros drop column la49_habilitartriagemlaboratorial;
Alter table laboratorio.lab_parametros drop COLUMN la49_unidadesdispensadastriagem;
DROP TABLE laboratorio.motivosrejeicaoamostra;
DROP TABLE laboratorio.lab_triagemitem;

SQL
    );
    }
    
    public function downItensMenu()
    {
        DB::unprepared(<<<SQL
delete from db_menu where id_item_filho = 229376;
delete from db_itensmenu where id_item = 229376;
SQL
    );  
    }    

}
