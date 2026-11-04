<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M26189AdicionarParametroMostraAreaIrregularCtm extends Migration
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
        alter table cadastro.cfiptu add column j18_mostraareairregular boolean not null default false;
        select configuracoes.fc_auditoria_cria_funcao('cadastro.cfiptu');
SQL;
        $this->executeQuery($sql);
    }

    private function upDicionario()
    {
        $sql = <<<SQL
        insert into db_syscampo values(1015575,'j18_mostraareairregular','bool','Mostra áreas irregulares do imóvel no Cadastro Técnico Municipal.','f', 'Mostra área irregular no CTM',1,'f','f','f',5,'text','Mostra área irregular no CTM');
        insert into db_sysarqcamp values(153,1015575,40,0);
SQL;
        $this->executeQuery($sql);
    }

    private function downEstrutura()
    {
        $sql = <<<SQL
            alter table cadastro.cfiptu drop column j18_mostraareairregular;
SQL;
        $this->executeQuery($sql);
    }

    private function downDicionario()
    {
        $sql = <<<SQL
        delete from db_sysarqcamp where codarq = 153 and codcam = 1015575;
        delete from db_syscampo where codcam = 1015575 and nomecam = 'j18_mostraareairregular';
SQL;
        $this->executeQuery($sql);
    }

    private function executeQuery($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
