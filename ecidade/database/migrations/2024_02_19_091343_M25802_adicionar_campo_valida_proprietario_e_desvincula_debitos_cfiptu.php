<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M25802AdicionarCampoValidaProprietarioEDesvinculaDebitosCfiptu extends Migration
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
        insert into db_syscampo values(1015622,'j18_validaproprietario','bool','Quando setado NÃO, sistema deve permitir incluir averbação para a matrícula, informando o CGM do proprietário já cadastrado na matrícula para que seja incluído outros proprietários;','f', 'Valida proprietário',1,'f','f','f',5,'text','Valida proprietário');
        insert into db_syscampo values(1015623,'j18_desvinculadebitosaverbacao','bool','Quando setado SIM, Ao processar averbação, sistema deve mostrar mensagem notificando usuário que existem débitos de Inicial do foro lançado e se deseja desvincular da matrícula e vincular apenas ao CGM do transmitente da averbação;','f', 'Desvincula débitos com averbação',1,'f','f','f',5,'text','Desvincula débitos com averbação');
        insert into db_sysarqcamp values(153,1015623,42,0);
        insert into db_sysarqcamp values(153,1015622,43,0);
SQL;
        $this->executeQuery($sql);
    }

    public function upEstrutura()
    {
        $sql = <<<SQL
        alter table cadastro.cfiptu add column j18_validaproprietario boolean default true;
        alter table cadastro.cfiptu add column j18_desvinculadebitosaverbacao boolean default false;
        select configuracoes.fc_auditoria_cria_funcao('cadastro.cfiptu');
SQL;
        $this->executeQuery($sql);
    }

    public function downDicionario()
    {
        $sql = <<<SQL
        delete from db_sysarqcamp where codarq = 153 and codcam in (1015623,1015622);
        delete from db_syscampo where codcam in (1015623,1015622) and nomecam in ('j18_validaproprietario','j18_desvinculadebitosaverbacao');
SQL;
        $this->executeQuery($sql);
    }

    public function downEstrutura()
    {
        $sql = <<<SQL
        alter table cadastro.cfiptu drop column j18_validaproprietario;
        alter table cadastro.cfiptu drop column j18_desvinculadebitosaverbacao;
SQL;
        $this->executeQuery($sql);
    }

    private function executeQuery($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
