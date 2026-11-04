<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23172AtualizacaoPreQualificacaoEFundamentacaoLegal extends Migration
{
    private $codigoFundamentacao = 18;
    private $codigoPreQualificacao = 16;

    private function getCodigoFundamentacao()
    {
        $fundamentacao = DB::table('db_cadattdinamicoatributos')
            ->select('db109_sequencial')
            ->where('db109_nome', 'codigofundamentacao')
            ->first();

        if ($fundamentacao->db109_sequencial != 18) {
            $this->codigoFundamentacao = $fundamentacao->db109_sequencial;
        }
    }

    private function getCodigoPreQualificacao()
    {
        $prequalificacao = DB::table('db_cadattdinamicoatributos')
            ->select('db109_sequencial')
            ->where('db109_nome', 'prequalificacao')
            ->first();

        if ($prequalificacao->db109_sequencial != 16) {
            $this->codigoPreQualificacao = $prequalificacao->db109_sequencial;
        }
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->getCodigoFundamentacao();
        $this->getCodigoPreQualificacao();

        if ($this->codigoFundamentacao != 18) {
            $this->upFundamentacao();
            $this->upPreQualificacao();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->getCodigoFundamentacao();
        $this->getCodigoPreQualificacao();

        if ($this->codigoFundamentacao != 18) {
            $this->downFundamentacao();
            $this->downPreQualificacao();
        }
    }

    private function upFundamentacao()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_cadattdinamicoatributosopcoes where db18_opcao in (
    'A28IIINLL',
    'A28IINLL',
    'A28INLL',
    'A28IVNLL',
    'A75IVC',
    'A75IVD',
    'A75V',
    'A78I',
    'A78III',
    'A29',
    'A75IVB',
    'A75P7',
    'A24XXI'
) and db18_cadattdinamicoatributos = 18;

insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A28IIINLL', 'Art. 28, inc. III, da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A28IINLL', 'Art. 28, inc. II, da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A28INLL', 'Art. 28, inc. I, da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A28IVNLL', 'Art. 28, inc. IV, da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A75IVC', 'Art. 75, inc. IV, alínea "c" da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A75IVD', 'Art. 75, inc. IV, alínea "d" da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A75V', 'Art. 75, inc. V, da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A78I', 'Art. 78, inc. I, da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A78III', 'Art. 78, inc. III, da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A29', 'Art.29 da Lei no 13.019/14');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A75IVB', 'Art. 75, inc. IV, alínea "b" da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A75P7', 'Art. 75, § 7o, da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, 'A24XXI', 'Art. 24, inc. XXI, da Lei no 8.666/93');
SQL
        );
    }

    private function upPreQualificacao()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoPreQualificacao}, 'E', 'Específica');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoPreQualificacao}, 'G', 'Geral');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoPreQualificacao}, 'N', 'Não Realizada');
SQL
        );
    }

    private function downFundamentacao()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), 18, 'A28IIINLL', 'Art. 28, inc. III, da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), 18, 'A28IINLL', 'Art. 28, inc. II, da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), 18, 'A28INLL', 'Art. 28, inc. I, da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), 18, 'A28IVNLL', 'Art. 28, inc. IV, da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), 18, 'A75IVC', 'Art. 75, inc. IV, alínea "c" da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), 18, 'A75IVD', 'Art. 75, inc. IV, alínea "d" da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), 18, 'A75V', 'Art. 75, inc. V, da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), 18, 'A78I', 'Art. 78, inc. I, da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), 18, 'A78III', 'Art. 78, inc. III, da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), 18, 'A29', 'Art.29 da Lei no 13.019/14');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), 18, 'A75IVB', 'Art. 75, inc. IV, alínea "b" da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), 18, 'A75P7', 'Art. 75, § 7o, da Lei no 14.133/21');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), 18, 'A24XXI', 'Art. 24, inc. XXI, da Lei no 8.666/93');

delete from db_cadattdinamicoatributosopcoes where db18_opcao in (
    'A28IIINLL',
    'A28IINLL',
    'A28INLL',
    'A28IVNLL',
    'A75IVC',
    'A75IVD',
    'A75V',
    'A78I',
    'A78III',
    'A29',
    'A75IVB',
    'A75P7',
    'A24XXI'
) and db18_cadattdinamicoatributos = {$this->codigoFundamentacao};
SQL
        );
    }

    private function downPreQualificacao()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_cadattdinamicoatributosopcoes where db18_opcao in (
    'E',
    'G',
    'N'
) and db18_cadattdinamicoatributos = {$this->codigoPreQualificacao};
SQL
        );
    }
}
