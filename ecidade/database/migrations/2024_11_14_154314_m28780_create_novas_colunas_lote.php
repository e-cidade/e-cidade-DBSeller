<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28780CreateNovasColunasLote extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
        alter table cadastro.lote add column if not exists j34_areaprivativa float;
        alter table cadastro.lote add column if not exists j34_areausocomum float;
        alter table cadastro.lote add column if not exists j34_zonadesurb integer;

        alter table cadastro.lote add constraint fk_lote_zonasdesenvolvimento foreign key (j34_zonadesurb) references cadastro.zonasdesenvolvimento (j225_sequencial);

        insert into db_syscampo values(1015681,'j34_areaprivativa','float4','Área Privativa do lote em metros quadrados','0', 'Área Privativa m2 do lote',10,'f','f','f',0,'text','Área Privativa m2 do lote');
        insert into db_syscampo values(1015682,'j34_areausocomum','float4','Área Uso Comum em metros quadrados','0', 'Área uso comum m2',10,'t','f','f',0,'text','Área uso comum m2');
        insert into db_syscampo values(1015683,'j34_zonadesurb','int4','Zona de Desenvolvimento Urbano','0', 'Zona Des. Urbano',10,'t','f','f',0,'text','Zona Des. Urbano');

        insert into db_sysarqcamp values(19,1015682,12,0);
        insert into db_sysarqcamp values(19,1015681,13,0);
        insert into db_sysarqcamp values(19,1015683,14,0);

        update db_syscampo set rotulo = 'Área Privativa da Matricula', 
                               rotulorel = 'Área Privativa da Matricula', 
                               descricao = 'Área privativa da Matricula para condôminios'
                               where codcam = 1014504;

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
        update db_syscampo set rotulo = 'Area Privativa do Lote', 
                               rotulorel = 'Area Privativa do Lote', 
                               descricao = 'Área privativa do lote para condôminios'
                               where codcam = 1014504;

        alter table cadastro.lote drop constraint fk_lote_zonasdesenvolvimento;
        alter table cadastro.lote drop column j34_zonadesurb;
        alter table cadastro.lote drop column j34_areausocomum;
        alter table cadastro.lote drop column j34_areaprivativa;
        delete from db_sysarqcamp where codcam in (1015681, 1015682, 1015683);
        delete from db_syscampo where codcam in (1015681, 1015682, 1015683);

SQL
        );
    }
}