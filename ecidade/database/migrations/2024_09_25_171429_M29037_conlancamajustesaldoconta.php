<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29037Conlancamajustesaldoconta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
create table contabilidade.conlancamajustesaldoconta (
	c155_codigo serial primary key,
	c155_conlancam integer not null,
	c155_exercicio integer default null,
	c155_funcao integer default null,
	c155_subfuncao integer default null,
    c155_elemento integer default null,
    c155_ai int default null,
    c155_receita int default null,
    FOREIGN KEY(c155_funcao) REFERENCES orcamento.orcfuncao (o52_funcao),
    FOREIGN KEY(c155_subfuncao) REFERENCES orcamento.orcsubfuncao (o53_subfuncao),
    FOREIGN KEY(c155_elemento, c155_exercicio) REFERENCES orcamento.orcelemento (o56_codele, o56_anousu),
    FOREIGN KEY(c155_receita, c155_exercicio) REFERENCES orcamento.orcfontes (o57_codfon, o57_anousu)
);
SQL
        );

        DB::unprepared(<<<SQL
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

COMMENT ON TABLE contabilidade.conlancamajustesaldoconta IS
  '{
      "descricao": "Tabela criada para realizar ajuste de saldo nas contas",
      "sigla": "c155",
      "dataincl": "2024-09-25",
      "rotulo": "Ajuste saldo conta",
      "tipotabela": 0,
      "naolibclass": false,
      "naolibfunc": false,
      "naolibprog": false,
      "naolibform": false
   }';

COMMENT ON COLUMN contabilidade.conlancamajustesaldoconta.c155_codigo is '{
      "descricao": "Chave primária da tabela",
      "rotulo": "Código",
      "rotulorel": "Código",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.conlancamajustesaldoconta.c155_conlancam is '{
      "descricao": "Lançamento",
      "rotulo": "Lançamento",
      "rotulorel": "Lançamento",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.conlancamajustesaldoconta.c155_exercicio is '{
      "descricao": "Exercício",
      "rotulo": "Exercício",
      "rotulorel": "Exercício",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.conlancamajustesaldoconta.c155_funcao is '{
      "descricao": "Função se for de despesa",
      "rotulo": "Função",
      "rotulorel": "Função",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.conlancamajustesaldoconta.c155_subfuncao is '{
      "descricao": "Subfunção se for de despesa",
      "rotulo": "Subfunção",
      "rotulorel": "Subfunção",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.conlancamajustesaldoconta.c155_elemento is '{
      "descricao": "Elemento se for de despesa",
      "rotulo": "Elemento",
      "rotulorel": "Elemento",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.conlancamajustesaldoconta.c155_ai is '{
      "descricao": "AI se for empenho de RP",
      "rotulo": "AI ",
      "rotulorel": "AI ",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.conlancamajustesaldoconta.c155_receita is '{
      "descricao": "Receita se for receita",
      "rotulo": "Receita ",
      "rotulorel": "Receita ",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
SQL
        );

        DB::connection()->getPdo()->exec(<<<SQL
SELECT fc_gera_dicionario_apartir_tabela('contabilidade', 'conlancamajustesaldoconta');
SQL
        );

        DB::connection()->getPdo()->exec(<<<SQL
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
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
drop table if exists contabilidade.conlancamajustesaldoconta;
SQL
        );
    }
}
