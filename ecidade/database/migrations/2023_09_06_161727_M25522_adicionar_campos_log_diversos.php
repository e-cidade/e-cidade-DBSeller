<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M25522AdicionarCamposLogDiversos extends Migration
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
        $sql = <<<SQL
        insert into db_syscampo values(1015381,'dv05_depto','int4','Departamento do usuário que realizou a inclusão','0', 'Departamento',10,'f','f','f',1,'text','Departamento');
        insert into db_syscampo values(1015380,'dv05_login','int4','Usuário que realizou a inclusão','0', 'Usuário',10,'f','f','f',1,'text','Usuário');
        insert into db_sysarqcamp values(372,1015380,16,0);
        insert into db_sysarqcamp values(372,1015381,17,0);
        insert into db_sysforkey values(372,1015380,1,109,0);
        insert into db_sysforkey values(372,1015381,1,154,0);
SQL;

        $this->execute($sql);
    }

    private function upEstrutura()
    {
        $sql = <<<SQL
        alter table diversos add column dv05_login integer;
        alter table diversos add column dv05_depto integer;
        alter table diversos add constraint diversos_db_usuarios_fk foreign key (dv05_login) references db_usuarios(id_usuario);
        alter table diversos add constraint diversos_db_depart_fk foreign key (dv05_depto) references db_depart(coddepto);
SQL;

        $this->execute($sql);
    }

    private function downDicionario()
    {
        $sql = <<<SQL
        delete from db_sysforkey where codarq = 372 and codcam in (1015380,1015381);
        delete from db_sysarqcamp where codarq = 372 and codcam in (1015380, 1015381);
        delete from db_syscampo where codcam in (1015380, 1015381) and nomecam in ('dv05_login', 'dv05_depto');
SQL;

        $this->execute($sql);
    }

    private function downEstrutura()
    {
        $sql = <<<SQL
        alter table diversos drop constraint diversos_db_usuarios_fk;
        alter table diversos drop constraint diversos_db_depart_fk;
        alter table diversos drop column dv05_login;
        alter table diversos drop column dv05_depto;
SQL;

        $this->execute($sql);
    }

    private function execute($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
