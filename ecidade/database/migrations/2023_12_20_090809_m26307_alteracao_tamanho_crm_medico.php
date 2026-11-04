<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26307AlteracaoTamanhoCrmMedico extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upEstrutura();
        $this->upDicionario();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downEstrutura();
        $this->downDicionario();
    }

    private function upEstrutura()
    {
        $sql = <<<SQL
            ALTER TABLE recursoshumanos.monitoramentosaude ALTER COLUMN h26_crmmedico TYPE varchar(10);
            ALTER TABLE recursoshumanos.monitoramentosaude ALTER COLUMN h26_crmresponsavel TYPE varchar(10);
SQL;
        $this->executeQuery($sql);
    }

    private function upDicionario()
    {
        $sql = <<<SQL
        update db_syscampo set nomecam = 'h26_crmmedico', conteudo = 'varchar(10)', descricao = 'Número de inscrição do médico emitente do ASO no Conselho Regional de Medicina - CRM.', valorinicial = '', rotulo = 'CRM', nulo = 't', tamanho = 10, maiusculo = 't', autocompl = 'f', aceitatipo = 0, tipoobj = 'text', rotulorel = 'CRM' where codcam = 1013616;
        delete from db_syscampodep where codcam = 1013616;
        delete from db_syscampodef where codcam = 1013616;
        update db_syscampo set nomecam = 'h26_crmresponsavel', conteudo = 'varchar(10)', descricao = 'Número de inscrição do médico responsável/coordenador do PCMSO no CRM.', valorinicial = '', rotulo = 'CRM do responsável', nulo = 't', tamanho = 10, maiusculo = 't', autocompl = 'f', aceitatipo = 0, tipoobj = 'text', rotulorel = 'CRM do responsável' where codcam = 1013620;
        delete from db_syscampodep where codcam = 1013620;
        delete from db_syscampodef where codcam = 1013620;
SQL;
        $this->executeQuery($sql);
    }

    private function downEstrutura()
    {

        $sql = <<<SQL
            ALTER TABLE recursoshumanos.monitoramentosaude ALTER COLUMN h26_crmmedico TYPE varchar(8);
            ALTER TABLE recursoshumanos.monitoramentosaude ALTER COLUMN h26_crmresponsavel TYPE char(8);
SQL;
        $this->executeQuery($sql);
    }

    private function downDicionario()
    {
        $sql = <<<SQL
        update db_syscampo set nomecam = 'h26_crmmedico', conteudo = 'varchar(8)', descricao = 'Número de inscrição do médico emitente do ASO no Conselho Regional de Medicina - CRM.', valorinicial = '', rotulo = 'CRM', nulo = 't', tamanho = 10, maiusculo = 't', autocompl = 'f', aceitatipo = 0, tipoobj = 'text', rotulorel = 'CRM' where codcam = 1013616;
        delete from db_syscampodep where codcam = 1013616;
        delete from db_syscampodef where codcam = 1013616;
        update db_syscampo set nomecam = 'h26_crmresponsavel', conteudo = 'char(8)', descricao = 'Número de inscrição do médico responsável/coordenador do PCMSO no CRM.', valorinicial = '', rotulo = 'CRM do responsável', nulo = 't', tamanho = 10, maiusculo = 't', autocompl = 'f', aceitatipo = 0, tipoobj = 'text', rotulorel = 'CRM do responsável' where codcam = 1013620;
        delete from db_syscampodep where codcam = 1013620;
        delete from db_syscampodef where codcam = 1013620;
SQL;
        $this->executeQuery($sql);
    }

    private function executeQuery($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
