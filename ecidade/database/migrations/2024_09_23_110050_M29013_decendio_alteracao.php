<?php

use App\Domain\Financeiro\Tesouraria\Models\TipoDesdobramento;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29013DecendioAlteracao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
truncate table caixa.tipodesdobramento cascade;


alter table caixa.decendiolancamento drop constraint IF EXISTS decendiolancamento_k231_desdobramento_fkey;
alter table caixa.orctiporectipodesdobramento drop constraint IF EXISTS  orctiporectipodesdobramento_k223_tiposdesdobramento_fkey;
alter table caixa.receitadescendio drop constraint IF EXISTS receitadescendio_k224_tipodesdobramento_fkey;


drop table caixa.tipodesdobramento;

create table caixa.tipodesdobramento (
    k222_codigo int primary key,
    k222_origem int not null,
    k222_descricao varchar(20) not null,
    k222_percentual int default 0 not null,
    created_at timestamp,
    updated_at timestamp
);

alter table caixa.decendiolancamento add FOREIGN KEY (k231_desdobramento) references caixa.tipodesdobramento(k222_codigo);
alter table caixa.orctiporectipodesdobramento add FOREIGN KEY (k223_tiposdesdobramento) references caixa.tipodesdobramento(k222_codigo);
alter table caixa.receitadescendio add FOREIGN KEY (k224_tipodesdobramento) references caixa.tipodesdobramento(k222_codigo);

select configuracoes.fc_auditoria_cria_funcao('caixa.tipodesdobramento');
SQL
        );


        TipoDesdobramento::create(['k222_codigo' => 1, 'k222_origem' => 1, 'k222_descricao' => 'MDE']);
        TipoDesdobramento::create(['k222_codigo' => 2, 'k222_origem' => 1, 'k222_descricao' => 'ASPS']);
        TipoDesdobramento::create(['k222_codigo' => 3, 'k222_origem' => 1, 'k222_descricao' => 'FUNDEB']);
        TipoDesdobramento::create(['k222_codigo' => 4, 'k222_origem' => 2, 'k222_descricao' => 'MDE']);
        TipoDesdobramento::create(['k222_codigo' => 5, 'k222_origem' => 2, 'k222_descricao' => 'ASPS']);
        TipoDesdobramento::create(['k222_codigo' => 6, 'k222_origem' => 2, 'k222_descricao' => 'FUNDEB']);


        $this->dicionario();
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
COMMENT ON COLUMN caixa.tipodesdobramento.k222_origem is '{
      "descricao": "Origem se é 1 - Impostos ou 2 - Transferências",
      "rotulo": "Origem",
      "rotulorel": "Origem",
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
SQL
        );
        DB::connection()->getPdo()->exec(<<<SQL
SELECT fc_gera_dicionario_apartir_tabela('caixa', 'tipodesdobramento');
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
        DB::connection()->getPdo()->exec(<<<SQL
delete from caixa.tipodesdobramento where k222_origem = 2;
alter table caixa.tipodesdobramento drop column k222_origem;
SQL
        );
    }
}
