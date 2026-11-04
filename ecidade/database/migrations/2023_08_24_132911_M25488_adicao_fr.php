<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25488AdicaoFr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into orcamento.fontesiconfi
(codigo_siconfi, descricao, classificacaofr_id, finalidade)
values (
'605',
'Assistência financeira da União destinada à complementação ao pagamento dos pisos salariais para profissionais da enfermagem.',
4,
'Controle dos recursos transferidos pela União, a título de assistência financeira complementar, para o cumprimento dos pisos salariais profissionais nacionais para o enfermeiro, o técnico de enfermagem, o auxiliar de enfermagem e a parteira, conforme estabelecido pela CF/88, art. 198, §§12 a 15.'
);
SQL
        );

        DB::connection()->getPdo()->exec(<<<SQL
update orcamento.fontesiconfi set descricao= 'Royalties e Participação Especial de Petróleo e Gás Natural vinculados à Saúde - Lei nº 12.858/2013'
where codigo_siconfi = '635';
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
delete from orcamento.fontesiconfi where codigo_siconfi = '605';
SQL
        );
    }
}
