<?php

use Illuminate\Database\Migrations\Migration;

class M24208Tabelas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
create table caixa.tipodesdobramento (
    k222_codigo int primary key,
    k222_descricao varchar(20) not null,
    k222_percentual int default 0 not null,
    created_at timestamp,
    updated_at timestamp
);

create table caixa.orctiporectipodesdobramento (
    k223_codigo serial primary key,
    k223_tiposdesdobramento int not null references caixa.tipodesdobramento,
    k223_orctiporec int not null references orcamento.orctiporec
);

create table caixa.receitadescendio (
    k224_codigo serial primary key,
    k224_tipodesdobramento int not null references caixa.tipodesdobramento,
    k224_reduz int not null,
    k224_anousu int not null,
    foreign key (k224_anousu, k224_reduz) REFERENCES orcamento.orcreceita
);
SQL
        );

        $this->dicionario();

        DB::connection()->getPdo()->exec(<<<SQL
select configuracoes.fc_auditoria_cria_funcao('caixa.tipodesdobramento');
select configuracoes.fc_auditoria_cria_funcao('caixa.orctiporectipodesdobramento');
select configuracoes.fc_auditoria_cria_funcao('caixa.receitadescendio');
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
select configuracoes.fc_auditoria_remove_funcao('caixa.receitadescendio');
select configuracoes.fc_auditoria_remove_funcao('caixa.orctiporectipodesdobramento');
select configuracoes.fc_auditoria_remove_funcao('caixa.tipodesdobramento');

drop table caixa.orctiporectipodesdobramento;
drop table caixa.receitadescendio;
drop table caixa.tipodesdobramento;
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
COMMENT ON TABLE caixa.tipodesdobramento IS
  '{
      "descricao": "Tipo Desdobramento",
      "sigla": "k222",
      "dataincl": "2024-04-15",
      "rotulo": "Tipo Desdobramento",
      "tipotabela": 0,
      "naolibclass": false,
      "naolibfunc": false,
      "naolibprog": false,
      "naolibform": false
   }';

COMMENT ON TABLE caixa.orctiporectipodesdobramento IS
  '{
      "descricao": "Recursos do tipo desdobramento",
      "sigla": "k223",
      "dataincl": "2024-04-15",
      "rotulo": "Recursos do tipo desdobramento",
      "tipotabela": 0,
      "naolibclass": false,
      "naolibfunc": false,
      "naolibprog": false,
      "naolibform": false
   }';

COMMENT ON TABLE caixa.receitadescendio IS
  '{
      "descricao": "Receita usadas no Decêndio",
      "sigla": "k224",
      "dataincl": "2024-04-15",
      "rotulo": "Receita decendio",
      "tipotabela": 0,
      "naolibclass": false,
      "naolibfunc": false,
      "naolibprog": false,
      "naolibform": false
   }';

COMMENT ON COLUMN caixa.tipodesdobramento.k222_codigo is '{
      "descricao": "Chave primária da tabela",
      "rotulo": "Código",
      "rotulorel": "Código",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN caixa.tipodesdobramento.k222_descricao is '{
      "descricao": "Desdobramento",
      "rotulo": "Desdobramento",
      "rotulorel": "Desdobramento",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 0,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN caixa.tipodesdobramento.k222_percentual is '{
      "descricao": "Percentual",
      "rotulo": "Percentual",
      "rotulorel": "Percentual",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';

COMMENT ON COLUMN caixa.orctiporectipodesdobramento.k223_codigo is '{
      "descricao": "Chave primária da tabela",
      "rotulo": "Código",
      "rotulorel": "Código",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN caixa.orctiporectipodesdobramento.k223_tiposdesdobramento is '{
      "descricao": "Tipo Desdobramento",
      "rotulo": "Tipo Desdobramento",
      "rotulorel": "Tipo Desdobramento",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';

COMMENT ON COLUMN caixa.orctiporectipodesdobramento.k223_orctiporec is '{
      "descricao": "Recurso",
      "rotulo": "Recurso",
      "rotulorel": "Recurso",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';

COMMENT ON COLUMN caixa.receitadescendio.k224_codigo is '{
      "descricao": "Chave primária da tabela",
      "rotulo": "Código",
      "rotulorel": "Código",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';

COMMENT ON COLUMN caixa.receitadescendio.k224_tipodesdobramento is '{
      "descricao": "Tipo Desdobramento",
      "rotulo": "Tipo Desdobramento",
      "rotulorel": "Tipo Desdobramento",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';

COMMENT ON COLUMN caixa.receitadescendio.k224_reduz is '{
      "descricao": "Reduzido Receita",
      "rotulo": "Reduzido Receita",
      "rotulorel": "Reduzido Receita",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';

COMMENT ON COLUMN caixa.receitadescendio.k224_anousu is  '{
      "descricao": "Exercício",
      "rotulo": "Exercício",
      "rotulorel": "Exercício",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
SQL
        );

        DB::connection()->getPdo()->exec(<<<SQL
SELECT fc_gera_dicionario_apartir_tabela('caixa', 'tipodesdobramento');
SELECT fc_gera_dicionario_apartir_tabela('caixa', 'orctiporectipodesdobramento');
SELECT fc_gera_dicionario_apartir_tabela('caixa', 'receitadescendio');
SQL
        );

        DB::connection()->getPdo()->exec(<<<SQL
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL
        );
    }

}
