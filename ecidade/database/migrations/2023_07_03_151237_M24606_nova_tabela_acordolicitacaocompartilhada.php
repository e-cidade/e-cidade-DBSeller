<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24606NovaTabelaAcordolicitacaocompartilhada extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->criaTabela();
        $this->upDicionario();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->deletaTabela();
        $this->downDicionario();
    }
    private function criaTabela()
    {
        Schema::create("acordos.acordolicitacaocompartilhada", function (Blueprint $table) {
            $table->bigIncrements("ac63_sequencial");
            $table->integer("ac63_acordo");
            $table->integer("ac63_licitacaocompartilhada");
            $table->integer("ac63_numcgm");
            $table->integer("ac63_modalidade");
            $table->integer("ac63_numero");
            $table->integer("ac63_ano");
            $table->foreign('ac63_acordo')->references('ac16_sequencial')->on('acordo');
            DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('acordo.acordolicitacaocompartilhada');");
        });
    }

    private function deletaTabela()
    {
        Schema::drop('acordos.acordolicitacaocompartilhada');
    }

    private function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_sysarquivo values (1011113, 'acordolicitacaocompartilhada', 'Acordo Licitacao Compartilhada', 'ac63', '2023-07-03', 'Acordo Licitacao Compartilhada', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (69,1011113);
insert into db_syscampo values(1015223,'ac63_sequencial','int4','Sequencial','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');
insert into db_syscampo values(1015224,'ac63_acordo','int4','Acordo','0', 'Acordo',10,'f','f','f',1,'text','Acordo');
insert into db_syscampo values(1015225,'ac63_licitacaocompartilhada','int4','Licitação Compartilhada','0', 'Licitação Compartilhada',10,'f','f','f',1,'text','Licitação Compartilhada');
insert into db_syscampo values(1015226,'ac63_numcgm','int4','Número do Cgm','0', 'Número do Cgm',10,'f','f','f',1,'text','Número do Cgm');
insert into db_syscampo values(1015228,'ac63_modalidade','int4','Modalidade Licitação','0', 'Modalidade Licitação',10,'f','f','f',1,'text','Modalidade Licitação');
insert into db_syscampo values(1015229,'ac63_ano','int4','Ano Licitação','0', 'Ano Licitação',10,'f','f','f',1,'text','Ano Licitação');
insert into db_syscampo values(1015230,'ac63_numero','int4','Numero da Licitação','0', 'Numero da Licitação',10,'f','f','f',1,'text','Numero da Licitação');
insert into db_sysarqcamp values(1011113,1015226,1,0);
insert into db_sysarqcamp values(1011113,1015225,2,0);
insert into db_sysarqcamp values(1011113,1015224,3,0);
insert into db_sysarqcamp values(1011113,1015223,4,1001142);
insert into db_sysarqcamp values(1011113,1015230,5,0);
insert into db_sysarqcamp values(1011113,1015229,6,0);
insert into db_sysarqcamp values(1011113,1015228,7,0);
insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011113,1015223,1,1015223);
insert into db_sysforkey values(1011113,1015224,1,2828,0);
insert into db_sysforkey values(1011113,1015228,1,430,0);
insert into db_syssequencia values(1001142, 'acordolicitacaocompartilhada_ac63_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
update db_sysarqcamp set codsequencia = 1001142 where codarq = 1011113 and codcam = 1015223;
SQL
        );
    }

    public function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        delete from db_syssequencia where codsequencia = 1001142;
        delete from db_sysforkey where codarq = 1011113;
        delete from db_sysprikey where codarq = 1011113;
        delete from db_sysarqcamp where codarq = 1011113;
        delete from db_syscampo where codcam in (1015223, 1015224, 1015225, 1015226, 1015228, 1015229, 1015230);
        delete from db_sysarqmod where codarq = 1011113;
        delete from db_sysarquivo where codarq = 1011113;
SQL
        );
    }
}
