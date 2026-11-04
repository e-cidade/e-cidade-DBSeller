<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27275Conplanoexeconciliarp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(<<<SQL
create table contabilidade.conplanoexeconciliarp (
    c162_codigo serial primary key,
    c162_exercicio integer not null,
    c162_reduzido integer not null,
    c162_ai integer not null,
    c162_funcao integer not null  references orcamento.orcfuncao,
    c162_subfuncao integer not null references orcamento.orcsubfuncao,
    c162_elemento integer not null,
    c162_recurso integer not null references orcamento.orctiporec,
    c162_valor numeric(17,2) not null,
    c162_natureza char(1),
    foreign key (c162_reduzido, c162_exercicio) references contabilidade.conplanoreduz,
    foreign key (c162_elemento, c162_ai) references orcamento.orcelemento
);
SQL
        );
        $this->dicionario();
        $this->menu();

        DB::statement("select configuracoes.fc_auditoria_cria_funcao('contabilidade.conplanoexeconciliarp');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getPdo()->exec(<<<SQL
select configuracoes.fc_auditoria_remove_funcao('contabilidade.conplanoexeconciliarp');
drop table contabilidade.conplanoexeconciliarp;
SQL
        );

        DB::connection()->getPdo()->exec(<<<SQL
update db_itensmenu set descricao = 'Mapeamento Saldo de RP por empenho' , help = 'Mapeamento Saldo de RP por empenho', desctec = 'Mapeamento EmpenhoRP Conta' where id_item = 229023;

delete from db_menu where id_item_filho in (229260, 229023, 229261) AND modulo = 209;
delete from db_itensmenu where id_item in (229260, 229261);

insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 9954 ,229023 ,246 ,209)
SQL
        );
    }

    private function dicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;
SQL
        );

        DB::connection()->getPdo()->exec(<<<SQL
COMMENT ON TABLE contabilidade.conplanoexeconciliarp IS
  '{
      "descricao": "Saldo inicial dos reduzidos de RP não vinculados a empenhos",
      "sigla": "c162",
      "dataincl": "2024-04-15",
      "rotulo": "Saldo inicial dos reduzidos de RP",
      "tipotabela": 0,
      "naolibclass": false,
      "naolibfunc": false,
      "naolibprog": false,
      "naolibform": false
   }';

COMMENT ON COLUMN contabilidade.conplanoexeconciliarp.c162_codigo is '{
      "descricao": "Chave primária da tabela",
      "rotulo": "Código",
      "rotulorel": "Código",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.conplanoexeconciliarp.c162_exercicio is '{
      "descricao": "Exercício Reduzido",
      "rotulo": "Exercício Reduzido",
      "rotulorel": "Exercício Reduzido",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.conplanoexeconciliarp.c162_reduzido is '{
      "descricao": "Reduzido",
      "rotulo": "Reduzido",
      "rotulorel": "Reduzido",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.conplanoexeconciliarp.c162_ai is '{
      "descricao": "Exercício do empenho",
      "rotulo": "Exercício do empenho",
      "rotulorel": "Exercício do empenho",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.conplanoexeconciliarp.c162_funcao is '{
      "descricao": "Função",
      "rotulo": "Função",
      "rotulorel": "Função",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.conplanoexeconciliarp.c162_subfuncao is '{
      "descricao": "Subfunção",
      "rotulo": "Subfunção",
      "rotulorel": "Subfunção",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.conplanoexeconciliarp.c162_elemento is '{
      "descricao": "Código",
      "rotulo": "Código",
      "rotulorel": "Código",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.conplanoexeconciliarp.c162_recurso is '{
      "descricao": "Recurso",
      "rotulo": "Recurso",
      "rotulorel": "Recurso",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.conplanoexeconciliarp.c162_valor is '{
      "descricao": "Valor",
      "rotulo": "Valor",
      "rotulorel": "Valor",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.conplanoexeconciliarp.c162_natureza is '{
      "descricao": "Natureza",
      "rotulo": "Natureza",
      "rotulorel": "Natureza",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
SQL
        );

        DB::connection()->getPdo()->exec(<<<SQL
SELECT fc_gera_dicionario_apartir_tabela('contabilidade', 'conplanoexeconciliarp');

ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL
        );
    }

    private function menu()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente )
values ( 229260 ,'Mapeamento Saldo de RP' ,'Mapeamento Saldo de RP' ,'' ,'1' ,'1' ,'Mapeamento Saldo de RP' ,'true'),
       ( 229261 ,'Manual por Planilha' ,'Manual por Planilha' ,'web/financeiro/contabilidade/procedimento/mapeamento-empenho-rp-manual' ,'1' ,'1' ,'Manual por Planilha' ,'true' );


update db_itensmenu set descricao = 'Por empenho' , help = 'Por empenho', desctec = 'Mapeamento EmpenhoRP Conta' where id_item = 229023;

delete from db_menu where id_item_filho = 229023 AND modulo = 209;

insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo )
values ( 9954 , 229260 ,248 ,209 ),
       ( 229260 ,229023 ,1 ,209 ),
       ( 229260 ,229261 ,2 ,209 );
SQL
        );
    }
}
