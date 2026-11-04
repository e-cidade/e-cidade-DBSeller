<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27892RemocaoCamposTabelaAcervo extends Migration
{
    private function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_syscampodef where codcam in (1008116,1008118,1010588,1010587);
delete from db_sysarqcamp where codcam in (1008116,1008118,1010588,1010587);
delete from db_syscampo WHERE codcam in (1008116,1008118,1010588,1010587);
SQL
        );
    }

    private function upEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
alter table biblioteca.acervo drop column bi06_isbn;
alter table biblioteca.acervo drop column bi06_paginacao;
alter table biblioteca.acervo drop column bi06_volume;
alter table biblioteca.acervo drop column bi06_tomo;
select configuracoes.fc_auditoria_cria_funcao('biblioteca.acervo');
select configuracoes.fc_auditoria_cria_funcao('biblioteca.exemplar');
SQL
        );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_syscampo (codcam, nomecam, conteudo, descricao, valorinicial, rotulo, tamanho, nulo, maiusculo, autocompl, aceitatipo, tipoobj, rotulorel) values (1008116, 'bi06_isbn', 'char(30)', 'Classificação ISBN', '', 'ISBN', 30, 'true', 'true', 'false', 0, 'text', 'I.S.B.N');
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 1008014 ,1008116 ,7 ,0 );
insert into db_syscampo (codcam, nomecam, conteudo, descricao, valorinicial, rotulo, tamanho, nulo, maiusculo, autocompl, aceitatipo, tipoobj, rotulorel) values (1008118, 'bi06_volume', 'varchar(50)', 'Volume do Acervo', '', 'Volume', 50, 'true', 'true', 'false', 0, 'text', 'Volume');
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 1008014 ,1008118 ,8 ,0 );
insert into db_syscampo (codcam, nomecam, conteudo, descricao, valorinicial, rotulo, tamanho, nulo, maiusculo, autocompl, aceitatipo, tipoobj, rotulorel) values (1010587, 'bi06_paginacao', 'varchar(50)', 'Quantidade de páginas do acervo.', '', 'Paginação', 50, 'true', 'true', 'false', 0, 'text', 'Paginação');
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 1008014 ,1010587 ,19 ,0 );
insert into db_syscampo (codcam, nomecam, conteudo, descricao, valorinicial, rotulo, tamanho, nulo, maiusculo, autocompl, aceitatipo, tipoobj, rotulorel) values (1010588, 'bi06_tomo', 'varchar(50)', 'Tomo do acervo', '', 'Tomo', 50, 'true', 'true', 'false', 0, 'text', 'Tomo');
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 1008014 ,1010588 ,18 ,0 );
SQL
        );
    }

    private function downEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
alter table biblioteca.acervo add column bi06_isbn character(30);
alter table biblioteca.acervo add column bi06_paginacao varchar(50);
alter table biblioteca.acervo add column bi06_volume varchar(50) default 0;
alter table biblioteca.acervo add column bi06_tomo varchar(50);
select configuracoes.fc_auditoria_cria_funcao('biblioteca.acervo');
SQL
        );
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
        $this->upEstrutura();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
        $this->downEstrutura();
    }
}
