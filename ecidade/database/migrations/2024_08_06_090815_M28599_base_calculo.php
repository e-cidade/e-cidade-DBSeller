<?php

use Illuminate\Database\Migrations\Migration;

class M28599BaseCalculo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
create table planejamento.basecalculoprojecao (
	pl31_codigo serial primary key,
	pl31_planejamento_id int not null,
	pl31_origem int not null,
	pl31_basecalculo varchar(20) not null,
	created_at timestamp,
    updated_at timestamp,
	foreign key (pl31_planejamento_id) references planejamento.planejamento on delete cascade
);

ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

COMMENT ON TABLE planejamento.basecalculoprojecao IS
  '{
      "descricao": "Armazena a base de cálculo usada pelo cliente",
      "sigla": "pl31",
      "dataincl": "2024-08-06",
      "rotulo": "basecalculoprojecao",
      "tipotabela": 0,
      "naolibclass": false,
      "naolibfunc": false,
      "naolibprog": false,
      "naolibform": false
   }';

COMMENT ON COLUMN planejamento.basecalculoprojecao.pl31_codigo IS
'{
  "descricao": "Código",
  "rotulo": "Código",
  "rotulorel": "Código",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

COMMENT ON COLUMN planejamento.basecalculoprojecao.pl31_planejamento_id IS
'{
  "descricao": "Planejamento",
  "rotulo": "Planejamento",
  "rotulorel": "Planejamento",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

COMMENT ON COLUMN planejamento.basecalculoprojecao.pl31_origem IS
'{
  "descricao": "Origem Despesa/Receita",
  "rotulo": "Origem",
  "rotulorel": "Origem",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text",
  "syscampodef": [
      {
        "defcampo": "1",
        "defdescr": "Despesa"
      },
      {
        "defcampo": "2",
        "defdescr": "Receita"
      }
  ]
}';

COMMENT ON COLUMN planejamento.basecalculoprojecao.pl31_basecalculo IS
'{
  "descricao": "Base de Cálculo",
  "rotulo": "Base de Cálculo",
  "rotulorel": "Base de Cálculo",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 0,
  "tamanho": 10,
  "tipoobj": "text"
}';

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
drop table planejamento.basecalculoprojecao;
SQL
        );
    }
}
