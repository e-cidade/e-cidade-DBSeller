<?php

use Illuminate\Database\Migrations\Migration;

class M5492CriacaoCamposTabelaExemplar extends Migration
{
    private function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015651 ,'bi23_isbn' ,'char(30)' ,'Classificação ISBN' ,'' ,'I.S.B.N' ,30 ,'true' ,'true' ,'false' ,0 ,'text' ,'I.S.B.N' );
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 1010151 ,1015651 ,10 ,0 );
insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015652 ,'bi23_paginacao' ,'varchar(50)' ,'Quantidade de páginas do exemplar.' ,'' ,'Paginação' ,50 ,'true' ,'true' ,'false' ,0 ,'text' ,'Paginação' );
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 1010151 ,1015652 ,11 ,0 );
insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015653 ,'bi23_volume' ,'varchar(50)' ,'Volume do Exemplar' ,'' ,'Volume' ,50 ,'true' ,'true' ,'false' ,0 ,'text' ,'Volume' );
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 1010151 ,1015653 ,12 ,0 );
insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015654 ,'bi23_tomo' ,'varchar(50)' ,'Tomo do exemplar.' ,'' ,'Tomo' ,50 ,'true' ,'true' ,'false' ,0 ,'text' ,'Tomo' );
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 1010151 ,1015654 ,13 ,0 );
update db_syscampo set rotulo = 'Ano Edição' where codcam = 1010408;
update db_syscampo set rotulo = 'Dt. Aquisição' where codcam = 1008944;
update db_syscampo set rotulo = 'Forma Aquisição' where codcam = 1008102;
SQL
        );
    }

    private function upEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
alter table biblioteca.exemplar add column bi23_isbn character(30);
alter table biblioteca.exemplar add column bi23_paginacao varchar(50);
alter table biblioteca.exemplar add column bi23_volume varchar(50) default 0;
alter table biblioteca.exemplar add column bi23_tomo varchar(50);
SQL
        );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_sysarqcamp where codarq = 1010151 and codcam in (1015651, 1015652, 1015653, 1015654);
delete from db_syscampo where codcam in (1015651, 1015652, 1015653, 1015654);
update db_syscampo set rotulo = 'Ano da Edição' where codcam = 1010408;
update db_syscampo set rotulo = 'Data de Aquisição' where codcam = 1008944;
update db_syscampo set rotulo = 'Forma de Aquisição' where codcam = 1008102;
SQL
        );
    }

    private function downEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
alter table biblioteca.exemplar drop column bi23_isbn;
alter table biblioteca.exemplar drop column bi23_paginacao;
alter table biblioteca.exemplar drop column bi23_volume;
alter table biblioteca.exemplar drop column bi23_tomo;
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
