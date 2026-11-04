<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28121AtualizacaoRecursos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
insert into orcamento.fontesiconfi values ('503', 'Apoio financeiro da Uniуo em decorrъncia de estado de calamidade pњblica', 2, 'Controle dos recursos transferidos pela Uniуo a tэtulo de apoio financeiro com o objetivo de enfrentar situaчѕes de calamidade pњblica e suas consequъncias sociais e econєmicas, como o apoio financeiro decorrente da Medida Provisѓria nК 1.222, de 21 de maio de 2024.');

update orcamento.fontesiconfi set
  descricao = 'Transferъncias da Uniуo Referentes рs participaчѕes na exploraчуo de Petrѓleo e Gсs Natural destinadas ao FEP - Lei 9.478/1997',
  finalidade = 'Transferъncias da Uniуo referentes рs participaчѕes na exploraчуo de petrѓleo, gсs natural e outros hidrocarbonetos fluidos, destinadas ao Fundo Especial - FEP, conforme estabelece o art. 50-F da Lei 9.478/97, exceto os recursos obrigatѓrios para educaчуo e saњde de que trata a Lei 12.858/2013.'
where codigo_siconfi = '720';


update orcamento.fontesiconfi set
  descricao = 'Transferъncias da Uniуo Referentes a Cessуo Onerosa de Petrѓleo ? Lei nК 13.885/2019',
  finalidade = 'Controle dos recursos transferidos pela Uniуo, provenientes da cessуo onerosa р Petrѓleo Brasileiro S.A. - PETROBRAS, do exercэcio das atividades de pesquisa e lavra de petrѓleo, gсs natural e outros hidrocarbonetos fluidos, originсrios dos leilѕes dos volumes excedentes ao limite a que se refere o Ї 2К do art. 1К da Lei nК 12.276, conforme estabelecido na Lei nК 13.885/2019.'
where codigo_siconfi = '721';
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
delete from orcamento.fontesiconfi where codigo_siconfi = '503';
SQL
        );
    }
}
