<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M26189AdicionaParametroCodigosAreaIrregular extends Migration
{
    public function up()
    {
        $this->upEstrutura();
        $this->upDicionario();
    }

    public function down()
    {
        $this->downEstrutura();
        $this->downDicionario();
    }

    private function upEstrutura()
    {
        $sql = <<<SQL
        alter table cadastro.cfiptu add column j18_caracterirregular varchar(40) not null default '';
        select configuracoes.fc_auditoria_cria_funcao('cadastro.cfiptu');
SQL;
        $this->executeQuery($sql);
    }

    private function upDicionario()
    {
        $sql = <<<SQL
        insert into db_syscampo values(1015576,'j18_caracterirregular','varchar(40)','Códigos das características de construção irregular.','', 'Características de área irregular',40,'f','f','f',0,'text','Características de área irregular');
        insert into db_sysarqcamp values(153,1015576,41,0);
SQL;
        $this->executeQuery($sql);
    }

    private function downEstrutura()
    {
        $sql = <<<SQL
            alter table cadastro.cfiptu drop column j18_caracterirregular;
SQL;
        $this->executeQuery($sql);
    }

    private function downDicionario()
    {
        $sql = <<<SQL
        delete from db_sysarqcamp where codarq = 153 and codcam = 1015576;
        delete from db_syscampo where codcam = 1015576 and nomecam = 'j18_caracterirregular';
SQL;
        $this->executeQuery($sql);
    }

    private function executeQuery($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
