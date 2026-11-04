<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25236AtualizacoesLicitaconCampoTipoDeDisputaOpcoes extends Migration
{
    private $codigoTipoDisputa = 0;
    private function getCodigoTipoDisputa()
    {
        $tipoDisputa = DB::table('db_cadattdinamicoatributos')
            ->select('db109_sequencial')
            ->where('db109_nome', 'tipodisputa')
            ->first();

        $this->codigoTipoDisputa = $tipoDisputa->db109_sequencial;
    }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->getCodigoTipoDisputa();
        DB::connection()->getPdo()->exec(<<<SQL
update db_cadattdinamicoatributosopcoes set db18_valor = 'Aberto-Fechado' where db18_cadattdinamicoatributos = $this->codigoTipoDisputa and db18_opcao = 'C';
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), $this->codigoTipoDisputa, 'E', 'Fechado-Aberto');
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
        $this->getCodigoTipoDisputa();
        DB::connection()->getPdo()->exec(<<<SQL
update db_cadattdinamicoatributosopcoes set db18_valor = 'Combinado' where db18_cadattdinamicoatributos = $this->codigoTipoDisputa and db18_opcao = 'C';
delete from db_cadattdinamicoatributosopcoes where db18_cadattdinamicoatributos = $this->codigoTipoDisputa and db18_opcao = 'E';
SQL
        );
    }
}
