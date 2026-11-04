<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29037TabelasMsc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
create table if not exists contabilidade.complanoexe_despesa_ajuste_saldo_msc (
    c156_codigo serial primary key,
    c156_reduzido integer not null,
    c156_exercicio integer not null,
    c156_funcao integer default null,
    c156_subfuncao integer default null,
    c156_elemento integer default null,
    c156_recurso integer default null,
    c156_valor numeric(17,2),
    c156_natureza char(1),
    FOREIGN KEY(c156_reduzido, c156_exercicio) REFERENCES contabilidade.conplanoreduz (c61_reduz, c61_anousu),
    FOREIGN KEY(c156_funcao) REFERENCES orcamento.orcfuncao (o52_funcao),
    FOREIGN KEY(c156_subfuncao) REFERENCES orcamento.orcsubfuncao (o53_subfuncao),
    FOREIGN KEY(c156_elemento, c156_exercicio) REFERENCES orcamento.orcelemento (o56_codele, o56_anousu),
    FOREIGN KEY(c156_recurso) REFERENCES orcamento.orctiporec (o15_codigo)
);


create table if not exists contabilidade.complanoexe_receita_ajuste_saldo_msc (
    c157_codigo serial primary key,
    c157_reduzido integer not null,
    c157_exercicio integer not null,
    c157_receita integer default null,
    c157_recurso integer default null,
    c157_valor numeric(17,2),
    c157_natureza char(1),
    FOREIGN KEY(c157_reduzido, c157_exercicio) REFERENCES contabilidade.conplanoreduz (c61_reduz, c61_anousu),
    FOREIGN KEY(c157_receita, c157_exercicio) REFERENCES orcamento.orcfontes (o57_codfon, o57_anousu),
    FOREIGN KEY(c157_recurso) REFERENCES orcamento.orctiporec (o15_codigo)
);
SQL
        );

        $this->dicionario();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared(<<<SQL
drop table if exists contabilidade.complanoexe_despesa_ajuste_saldo_msc;
drop table if exists contabilidade.complanoexe_receita_ajuste_saldo_msc;
SQL
        );
    }

    private function dicionario()
    {
        DB::unprepared(<<<SQL
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

COMMENT ON TABLE contabilidade.complanoexe_despesa_ajuste_saldo_msc IS
  '{
      "descricao": "Tabela criada para realizar ajuste de saldo inicial da despesa",
      "sigla": "c156",
      "dataincl": "2024-09-25",
      "rotulo": "saldo inicial despesa",
      "tipotabela": 0,
      "naolibclass": false,
      "naolibfunc": false,
      "naolibprog": false,
      "naolibform": false
   }';

COMMENT ON COLUMN contabilidade.complanoexe_despesa_ajuste_saldo_msc.c156_codigo is '{
      "descricao": "Chave primária da tabela",
      "rotulo": "Código",
      "rotulorel": "Código",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.complanoexe_despesa_ajuste_saldo_msc.c156_reduzido is '{
      "descricao": "Reduzido pcasp",
      "rotulo": "Reduzido pcasp",
      "rotulorel": "Reduzido pcasp",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.complanoexe_despesa_ajuste_saldo_msc.c156_exercicio is '{
      "descricao": "exercício",
      "rotulo": "exercício",
      "rotulorel": "exercício",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.complanoexe_despesa_ajuste_saldo_msc.c156_funcao is '{
      "descricao": "Função",
      "rotulo": "Função",
      "rotulorel": "Função",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.complanoexe_despesa_ajuste_saldo_msc.c156_subfuncao is '{
      "descricao": "Subfunção",
      "rotulo": "Subfunção",
      "rotulorel": "Subfunção",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.complanoexe_despesa_ajuste_saldo_msc.c156_elemento is '{
      "descricao": "Elemento",
      "rotulo": "Elemento",
      "rotulorel": "Elemento",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.complanoexe_despesa_ajuste_saldo_msc.c156_recurso is '{
      "descricao": "Recurso",
      "rotulo": "Recurso",
      "rotulorel": "Recurso",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.complanoexe_despesa_ajuste_saldo_msc.c156_valor is '{
      "descricao": "Valor",
      "rotulo": "Valor",
      "rotulorel": "Valor",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 4,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.complanoexe_despesa_ajuste_saldo_msc.c156_natureza is '{
      "descricao": "Natureza",
      "rotulo": "Natureza",
      "rotulorel": "Natureza",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 2,
      "tamanho": 10,
      "tipoobj": "text"
}';

COMMENT ON TABLE contabilidade.complanoexe_receita_ajuste_saldo_msc IS
  '{
      "descricao": "Tabela criada para realizar ajuste de saldo inicial da receita",
      "sigla": "c157",
      "dataincl": "2024-09-25",
      "rotulo": "saldo inicial receita",
      "tipotabela": 0,
      "naolibclass": false,
      "naolibfunc": false,
      "naolibprog": false,
      "naolibform": false
   }';

COMMENT ON COLUMN contabilidade.complanoexe_receita_ajuste_saldo_msc.c157_codigo is '{
      "descricao": "Chave primária da tabela",
      "rotulo": "Código",
      "rotulorel": "Código",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.complanoexe_receita_ajuste_saldo_msc.c157_reduzido is '{
      "descricao": "Reduzido pcasp",
      "rotulo": "Reduzido pcasp",
      "rotulorel": "Reduzido pcasp",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.complanoexe_receita_ajuste_saldo_msc.c157_exercicio is '{
      "descricao": "exercício",
      "rotulo": "exercício",
      "rotulorel": "exercício",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.complanoexe_receita_ajuste_saldo_msc.c157_receita is '{
      "descricao": "Receita",
      "rotulo": "Receita",
      "rotulorel": "Receita",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.complanoexe_receita_ajuste_saldo_msc.c157_recurso is '{
      "descricao": "Recurso",
      "rotulo": "Recurso",
      "rotulorel": "Recurso",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.complanoexe_receita_ajuste_saldo_msc.c157_valor is '{
      "descricao": "Valor",
      "rotulo": "Valor",
      "rotulorel": "Valor",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 4,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN contabilidade.complanoexe_receita_ajuste_saldo_msc.c157_natureza is '{
      "descricao": "Natureza",
      "rotulo": "Natureza",
      "rotulorel": "Natureza",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 2,
      "tamanho": 10,
      "tipoobj": "text"
}';

SELECT fc_gera_dicionario_apartir_tabela('contabilidade', 'complanoexe_despesa_ajuste_saldo_msc');
SELECT fc_gera_dicionario_apartir_tabela('contabilidade', 'complanoexe_receita_ajuste_saldo_msc');

ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL
        );
    }
}
