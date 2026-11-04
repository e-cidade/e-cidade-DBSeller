<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26962AdicionaNovosTiposSiconf extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL


insert into fontesiconfi select '720', 
                                'Transferência da União referentes às participações na exploração de Petróleo e Gás Natural destinadas ao FEP - Lei 9.478/1997',
                                6,
                                'Controle das transferências da União referentes às participações na exploração de petróleo, gás natural e outros hidrocarbonetos fluidos, destinadas ao Fundo Especial - FEP, conforme estabelece o art. 50-F da Lei 9.478/97, exceto os recursos obrigatórios para educação e saúde de que trata a Lei 12.858/2013.';
                                
insert into fontesiconfi select '721',
                                'Transferências da União referentes a Cessão Onerosa de Petróleo  Lei nº 13.885/2019',
                                6,
                                'Controle dos recursos transferidos pela União, provenientes da cessão onerosa à Petróleo Brasileiro S.A. - PETROBRAS, do exercício das atividades de pesquisa e lavra de petróleo, gás natural e outros hidrocarbonetos fluidos, originários dos leilões dos volumes excedentes ao limite a que se refere o § 2º do art. 1º da Lei nº 12.276/2010, conforme estabelecido na Lei nº 13.885/2019.';
                                
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

          delete from fontesiconfi where codigo_siconfi in ('720', '721');
SQL;

        DB::connection()->getPdo()->exec($sql);
        
    }
}
