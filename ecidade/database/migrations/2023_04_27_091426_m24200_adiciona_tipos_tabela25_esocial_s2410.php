<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24200AdicionaTiposTabela25EsocialS2410 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
            insert into pessoal.rhtipoapos values('0801', 'Aposentadoria sem paridade concedida antes da obrigatoriedade de envio dos eventos não periódicos para entes públicos no eSocial');
            insert into pessoal.rhtipoapos values('0802', 'Aposentadoria com paridade concedida antes da obrigatoriedade de envio dos eventos não periódicos para entes públicos no eSocial');
            insert into pessoal.rhtipoapos values('0803', 'Aposentadoria por invalidez com paridade concedida antes da obrigatoriedade de envio dos eventos não periódicos para entes públicos no eSocial');
            insert into pessoal.rhtipoapos values('0804', 'Aposentadoria por invalidez sem paridade concedida antes da obrigatoriedade de envio dos eventos não periódicos para entes públicos no eSocial');
            insert into pessoal.rhtipoapos values('0805', 'Transferência para reserva concedida antes da obrigatoriedade de envio dos eventos não periódicos para entes públicos no eSocial');
            insert into pessoal.rhtipoapos values('0806', 'Reforma concedida antes da obrigatoriedade de envio dos eventos não periódicos para entes públicos no eSocial');
            insert into pessoal.rhtipoapos values('0807', 'Pensão por morte com paridade concedida antes da obrigatoriedade de envio dos eventos não periódicos para entes públicos no eSocial');
            insert into pessoal.rhtipoapos values('0808', 'Pensão por morte sem paridade concedida antes da obrigatoriedade de envio dos eventos não periódicos para entes públicos no eSocial');
            insert into pessoal.rhtipoapos values('0809', 'Outros benefícios previdenciários concedidos antes da obrigatoriedade de envio dos eventos não periódicos para entes públicos no eSocial');
            insert into pessoal.rhtipoapos values('0810', 'Aposentadoria de parlamentar - Plano próprio');
            insert into pessoal.rhtipoapos values('0811', 'Aposentadoria de servidor vinculado ao Poder Legislativo - Plano próprio');
            insert into pessoal.rhtipoapos values('0812', 'Pensão por morte - Plano próprio');
            insert into pessoal.rhtipoapos values('1002', 'Anistiado político (Lei 10.559/2002)');
            insert into pessoal.rhtipoapos values('1201', 'Pensão por morte ficta');
            insert into pessoal.rhtipoapos values('1202', 'Pensão especial');
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
            delete from pessoal.rhtipoapos where rh88_sequencial in (
                '0801',
                '0802',
                '0803',
                '0804',
                '0805',
                '0806',
                '0807',
                '0808',
                '0809',
                '0810',
                '0811',
                '0812',
                '1002',
                '1201',
                '1202');
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
