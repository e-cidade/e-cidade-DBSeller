<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28198AjusteNomenclaturaEstadosEmpenhosDiaria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update empprestaitemdiaria set e446_estadodestino = upper(trim(e446_estadodestino));
update empprestaitemdiaria set e446_estadodestino = 'PR' where e446_estadodestino = 'FOZ DE IGUAÇU';
update empprestaitemdiaria set e446_estadodestino = 'DF' where e446_estadodestino in('BRASILIA', 'BRASÍLIA', 'BRASÍLIA - DF', 'DISTRITO FEDERAL');
update empprestaitemdiaria set e446_estadodestino = 'MA' where e446_estadodestino = 'MARANHÃO';
update empprestaitemdiaria set e446_estadodestino = 'MG' where e446_estadodestino in ('MINAS GERAIS', 'MINAS', 'RMG');
update empprestaitemdiaria set e446_estadodestino = 'MG' where e446_estadodestino in ('PARANA', 'PARANÁ');
update empprestaitemdiaria set e446_estadodestino = 'RS' where e446_estadodestino in ('PORTO ALEGRE', 'RIO GRANDE DO SUL');
update empprestaitemdiaria set e446_estadodestino = 'RJ' where e446_estadodestino in ('RIO DE JANEIRO','RJ/MG', 'RIO DE JNAEIRO', 'RIO DE JANERO', 'RJ/SP/MG', 'RJ/MG/SP', 'SP/RJ/MG', 'ERJ', 'RJ/SP'); 
update empprestaitemdiaria set e446_estadodestino = 'SC' where e446_estadodestino = 'SANTA CATARINA';
update empprestaitemdiaria set e446_estadodestino = 'SP' where e446_estadodestino in ('SAO PAULO', 'SÃO PAULO', 'SP/RJ');
update empprestaitemdiaria set e446_estadodestino = 'SE' where e446_estadodestino in ('SERGIPE');
update empprestaitemdiaria set e446_estadodestino = 'RJ' where e446_estadodestino = 'BR' and trim(upper(e446_destino)) = 'PIRAI';
update empprestaitemdiaria set e446_estadodestino = (select uf from db_config limit 1) where e446_estadodestino is null and e446_tipodiaria = 'dentroestado';

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
        //
    }
}
