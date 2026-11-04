<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M20866ContagemConcessao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recursoshumanos.process_concessao_count', function (Blueprint $table) {
            $table->bigIncrements('rh510_sequencial');
            $table->integer('rh510_total');
            $table->integer('rh510_quantidade');
        });
        $this->upDicionario();
    }

    protected function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_sysarquivo values (1011016, 'process_concessao_count', 'Gerenciador processos do jobs de concessões ', 'rh510', '2023-01-18', 'process_concessao_count', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (29,1011016);
insert into db_sysarquivo values (1011017, 'concessao_direitos_erros', 'Erros do processamento das concessões ', 'rh509', '2023-01-18', 'concessao_direitos_erros', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (29,1011017);
insert into db_syscampo values(1014700,'rh509_sequencial','int8','Sequencial da tabela','0', 'rh509_sequencial',10,'f','f','f',1,'text','rh509_sequencial');
insert into db_syscampo values(1014701,'rh509_matricula','int8','Registro do servidor','0', 'rh509_matricula',10,'f','f','f',1,'text','rh509_matricula');
insert into db_syscampo values(1014702,'rh509_erro','text','Mensagem de erro ocasionada no processamento ','', 'rh509_erro',1,'f','t','f',0,'text','rh509_erro');
insert into db_syscampo values(1014703,'rh510_sequencial','int8','Sequencial da tabela','0', 'rh510_sequencial',10,'f','f','f',1,'text','rh510_sequencial');
insert into db_syscampo values(1014704,'rh510_total','int8','Valor total de itens no processamento','0', 'rh510_total',10,'f','f','f',1,'text','rh510_total');
insert into db_syscampo values(1014705,'rh510_quantidade','int8','Quantidade de itens processados','0', 'rh510_quantidade',10,'f','f','f',1,'text','rh510_quantidade');
delete from db_sysarqcamp where codarq = 1011016;
insert into db_sysarqcamp values(1011016,1014705,1,0);
insert into db_sysarqcamp values(1011016,1014704,2,0);
insert into db_sysarqcamp values(1011016,1014703,3,0);
delete from db_sysprikey where codarq = 1011016;
insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011016,1014703,1,1014703);
delete from db_sysarqcamp where codarq = 1011017;
insert into db_sysarqcamp values(1011017,1014702,1,0);
insert into db_sysarqcamp values(1011017,1014701,2,0);
insert into db_sysarqcamp values(1011017,1014700,3,0);
delete from db_sysprikey where codarq = 1011017;
insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011017,1014700,1,1014702);
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
        Schema::drop('recursoshumanos.process_concessao_count');
        $this->downDicionario();
    }

    protected function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_sysprikey where codarq = 1011016;
delete from db_sysprikey where codarq = 1011017;

delete from db_sysarqcamp where codarq = 1011016;
delete from db_sysarqcamp where codarq = 1011017;

delete from db_syscampo where codcam = 1014700;
delete from db_syscampo where codcam = 1014701;
delete from db_syscampo where codcam = 1014702;
delete from db_syscampo where codcam = 1014703;
delete from db_syscampo where codcam = 1014704;
delete from db_syscampo where codcam = 1014705;

delete from db_sysarqmod where codarq = 1011016;
delete from db_sysarqmod where codarq = 1011017;

delete from db_sysarquivo where codarq = 1011016;
delete from db_sysarquivo where codarq = 1011017;

SQL
        );
    }
}
