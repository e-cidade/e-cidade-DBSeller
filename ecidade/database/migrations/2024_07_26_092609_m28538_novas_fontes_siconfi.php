<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28538NovasFontesSiconfi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL

insert into orcamento.fontesiconfi 
select '545',
       'Recursos de Precatórios do FUNDEB (2007- 2020)',
       3,
       'Controle dos recursos decorrentes do recebimento de precatórios derivados de ações judiciais associadas aos repasses ao Fundo de Manutenção e Desenvolvimento da Educação Básica e de Valorização dos Profissionais da Educação (Fundeb) 2007-2020, para atendimento ao previsto no artigo 47-A da Lei nº 14.113, de 25 de dezembro de 2020.';


insert into orcamento.fontesiconfi 
select '747',
       'Outras vinculações de transferências da União',
       6,
       'Controle dos recursos de outras transferências vinculadas da União, não enquadrados nas especificações anteriores.';

insert into orcamento.fontesiconfi 
select '748',
       'Outras vinculações de transferências dos Estados',
       6,
       'Controle dos recursos de outras transferências vinculadas dos Estados, não enquadrados nas especificações anteriores.';

insert into orcamento.fontesiconfi
select '804',
       'Demais Recursos Previdenciários',
       8,
       'Controle de demais recursos vinculados a benefícios previdenciários, como os benefícios mantidos sob responsabilidade financeira direta do Tesouro do ente Federativo, concedidos em atendimento a legislações específicas e que não foram incorporados ao RPPS.';        





SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL


delete from orcamento.fontesiconfi where codigo_siconfi in ('545','747','748','804'); 


SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
