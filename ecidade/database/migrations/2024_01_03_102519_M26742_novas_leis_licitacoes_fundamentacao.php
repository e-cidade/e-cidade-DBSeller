<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26742NovasLeisLicitacoesFundamentacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->getCodigoFundamentacao();
        $this->inserirNovasOpcoes();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->getCodigoFundamentacao();
        $this->excluirNovasOpcoes();
    }
    private $codigoFundamentacao = 18;

    private function getCodigoFundamentacao()
    {
        $fundamentacaoCodigo = DB::table('db_cadattdinamicoatributos')
            ->select('db109_sequencial')
            ->where('db109_nome', 'codigofundamentacao')
            ->first();

        $this->codigoFundamentacao = $fundamentacaoCodigo->db109_sequencial;
    }

    private function inserirNovasOpcoes()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A75XVII', 'Art. 75, inc. XVII, da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A75XVIII', 'Art. 75, inc. XVIII, da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A76IA', 'Art. 76, inc. I, alínea "a" da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A76IB', 'Art. 76, inc. I, alínea "b" da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A76IC', 'Art. 76, inc. I, alínea "c" da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A76ID', 'Art. 76, inc. I, alínea "d" da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A76IE', 'Art. 76, inc. I, alínea "e" da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A76IF', 'Art. 76, inc. I, alínea "f" da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A76IG', 'Art. 76, inc. I, alínea "g" da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A76IIA', 'Art. 76, inc. II, alínea "a" da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A76IIB', 'Art. 76, inc. II, alínea "b" da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A76IIC', 'Art. 76, inc. II, alínea "c" da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A76IID', 'Art. 76, inc. II, alínea "d" da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A76IIE', 'Art. 76, inc. II, alínea "e" da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A76IIF', 'Art. 76, inc. II, alínea "f" da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, '14628A4', 'Art. 4 da Lei 14.628/23');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A79I', 'Art. 79, inc. I, da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A79II', 'Art. 79, inc. II, da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A79III', 'Art. 79, inc. III, da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, '11947A14P1', 'Art. 14, § 1o, da Lei no 11.947/2009');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, '11947A21', 'Art. 21 da Lei no 11.947/2009');
SQL
        );
    }

    private function excluirNovasOpcoes()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_cadattdinamicoatributosopcoes where db18_opcao in (
    'A75XVII',
    'A75XVIII',
    'A76IA',
    'A76IB',
    'A76IC',
    'A76ID',
    'A76IE',
    'A76IF',
    'A76IG',
    'A76IIA',
    'A76IIB',
    'A76IIC',
    'A76IID',
    'A76IIE',
    'A76IIF',
    '14628A4',
    'A79I',
    'A79II',
    'A79III',
    '11947A14P1',
    '11947A21'
) and db18_cadattdinamicoatributos = {$this->codigoFundamentacao};
SQL
        );
    }
}
