<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CriarTabelaArrelanc extends Migration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE if not exists caixa.arrelanc (
    k00_numpre integer NOT NULL,
    k00_codlanc integer NOT NULL,
    CONSTRAINT arrelanc_lanc_numpre_pk PRIMARY KEY (k00_codlanc, k00_numpre)
);


ALTER TABLE caixa.arrelanc OWNER TO ecidade;

COMMENT ON TABLE caixa.arrelanc IS '{ "descricao": "Tabela com o numpre do lancamento fiscal",
    "sigla": "k00",
    "dataincl": "2021-05-25",
    "rotulo": "arrelanc",
    "tipotabela": "0",
    "naolibclass": "false",
    "naolibfunc": "false",
    "naolibprog": "false",
    "naolibform": "false"
}';

COMMENT ON COLUMN caixa.arrelanc.k00_numpre IS '{ "descricao": "Numero de arrecadacao",
    "rotulo": "Numero de arrecadacao",
    "rotulorel": "Numero de arrecadacao",
    "maiusculo": false,
    "autocompl": false,
    "aceitatipo": 1,
    "tamanho": 10,
    "tipoobj": "text"
}';

COMMENT ON COLUMN caixa.arrelanc.k00_codlanc IS '{ "descricao": "Codigo do lancamento fiscal",
    "rotulo": "Codigo do lancamento fiscal",
    "rotulorel": "Codigo do lancamento fiscal",
    "maiusculo": false,
    "autocompl": false,
    "aceitatipo": 1,
    "tamanho": 10,
    "tipoobj": "text"
}';

GRANT SELECT ON TABLE caixa.arrelanc TO plugin;

SQL;
        $this->execute($sql);
    }

    public function down()
    {
        $sql = <<<SQL
drop table caixa.arrelanc;
SQL;
        $this->execute($sql);
    }

    private function execute($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
