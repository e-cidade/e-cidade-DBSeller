<?php

use Illuminate\Database\Migrations\Migration;

class M24208Decendio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
create table caixa.decendio (
  k230_codigo serial primary key,
  k230_slip int references caixa.slip,
  created_at timestamp,
  updated_at timestamp
);

create table caixa.decendiolancamento (
  k231_codigo serial primary key,
  k231_decendio int references caixa.decendio,
  k231_lancamento int references contabilidade.conlancam,
  k231_desdobramento int references caixa.tipodesdobramento,
  created_at timestamp,
  updated_at timestamp
);
SQL
        );

        $this->dicionario();

        DB::unprepared(<<<SQL
select configuracoes.fc_auditoria_cria_funcao('caixa.decendio');
select configuracoes.fc_auditoria_cria_funcao('caixa.decendiolancamento');
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
select configuracoes.fc_auditoria_remove_funcao('caixa.decendio');
select configuracoes.fc_auditoria_remove_funcao('caixa.decendiolancamento');

drop table caixa.decendiolancamento;
drop table caixa.decendio;
SQL
        );
    }

    private function dicionario()
    {
        DB::unprepared(<<<SQL
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;
SQL
        );

        DB::unprepared(<<<SQL
COMMENT ON TABLE caixa.decendio IS
'{
      "descricao": "Armazena os slips gerados para o decêndio",
      "sigla": "k230",
      "dataincl": "2024-04-26",
      "rotulo": "Decendio",
      "tipotabela": 0,
      "naolibclass": false,
      "naolibfunc": false,
      "naolibprog": false,
      "naolibform": false
}';

COMMENT ON TABLE caixa.decendiolancamento IS
'{
      "descricao": "Armazena os lançamentos presente no slip",
      "sigla": "k231",
      "dataincl": "2024-04-26",
      "rotulo": "Lancanentos presente no Decendio",
      "tipotabela": 0,
      "naolibclass": false,
      "naolibfunc": false,
      "naolibprog": false,
      "naolibform": false
}';

COMMENT ON COLUMN caixa.decendio.k230_codigo is '{
      "descricao": "Chave primária da tabela",
      "rotulo": "Código",
      "rotulorel": "Código",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN caixa.decendio.k230_slip is '{
      "descricao": "Código do slip",
      "rotulo": "Slip",
      "rotulorel": "Slip",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';

COMMENT ON COLUMN caixa.decendiolancamento.k231_codigo is '{
      "descricao": "Chave primária da tabela",
      "rotulo": "Código",
      "rotulorel": "Código",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN caixa.decendiolancamento.k231_decendio is '{
      "descricao": "FK com decendio",
      "rotulo": "Decêndio",
      "rotulorel": "Decêndio",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN caixa.decendiolancamento.k231_lancamento is '{
      "descricao": "Lançamento",
      "rotulo": "Lançamento",
      "rotulorel": "Lançamento",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN caixa.decendiolancamento.k231_desdobramento is '{
      "descricao": "Desdobramento",
      "rotulo": "Desdobramento",
      "rotulorel": "Desdobramento",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
SQL
        );

        DB::unprepared(<<<SQL
SELECT fc_gera_dicionario_apartir_tabela('caixa', 'decendiolancamento');
SELECT fc_gera_dicionario_apartir_tabela('caixa', 'decendio');
SQL
        );

        DB::unprepared(<<<SQL
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL
        );
    }
}
