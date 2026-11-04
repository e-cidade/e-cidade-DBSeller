<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M25742AdicionarCamposIsscalcant extends Migration
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

    public function upDicionario()
    {
        $sql = <<<SQL
        insert into db_syscampo values(1015478,'q15_data','date','Data de inclusão do registro','null', 'Data',10,'t','f','f',0,'text','Data');
        insert into db_syscampo values(1015479,'q15_usuario','float4','Usuário que realizou a inclusão do registro','0', 'Usuário',8,'f','f','f',1,'text','Usuário');
        insert into db_sysarqcamp values(62,1015479,9,0);
        insert into db_sysarqcamp values(62,1015478,10,0);
        insert into db_sysforkey values(62,1015479,1,109,0);
SQL;
        $this->execute($sql);
    }

    public function upEstrutura()
    {
        $sql = <<<SQL
        alter table isscalcant add column q15_data timestamp with time zone;
        alter table isscalcant add column q15_usuario integer;
        alter table isscalcant add constraint isscalcant_db_usuarios_fk foreign key (q15_usuario) references db_usuarios(id_usuario);
SQL;
        $this->execute($sql);
    }

    public function downDicionario()
    {
        $sql = <<<SQL
        delete from db_sysforkey where codarq = 62 and codcam = 1015479;
        delete from db_sysarqcamp where codarq = 62 and codcam = 1015479;
        delete from db_sysarqcamp where codarq = 62 and codcam = 1015478;
        delete from db_syscampo where codcam = 1015478 and nomecam = 'q15_data';
        delete from db_syscampo where codcam = 1015479 and nomecam = 'q15_usuario';
SQL;
        $this->execute($sql);
    }

    public function downEstrutura()
    {
        $sql = <<<SQL
        alter table isscalcant drop constraint isscalcant_db_usuarios_fk;
        alter table isscalcant drop column q15_data;
        alter table isscalcant drop column q15_usuario;
SQL;
        $this->execute($sql);
    }

    private function execute($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
