<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M24801AdicionarParametroSeparacaoJurEMulRegraParcelamento extends Migration
{
    public function up()
    {
        $this->upDicionario();
        $this->upEstrutura();
    }

    public function down()
    {
        $this->downDicionario();
        $this->downEstrutura();
    }

    private function upDicionario()
    {
        $sql = "
            insert into db_syscampo values(1015201,'k40_separajuremul','bool','Controla separação de juros e multas.','f', 'Separar jur e mul no parcelamento',1,'f','f','f',5,'text','Separa de juros e multas.');
            insert into db_sysarqcamp values(1257,1015201,27,0);
        ";

        DB::connection()->getPdo()->exec($sql);
    }

    private function upEstrutura()
    {
        $sql = "
            alter table cadtipoparc add k40_separajuremul boolean default false;
        ";

        DB::connection()->getPdo()->exec($sql);
    }

    private function downDicionario()
    {
        $sql = "
            delete from db_sysarqcamp where codarq = 1257 and codcam = 1015201 and seqarq = 27;
            delete from db_syscampo where codcam = 1015201 and nomecam = 'k40_separajuremul';
        ";

        DB::connection()->getPdo()->exec($sql);
    }

    private function downEstrutura()
    {
        $sql = "
            alter table cadtipoparc drop column k40_separajuremul;
        ";

        DB::connection()->getPdo()->exec($sql);
    }
}
