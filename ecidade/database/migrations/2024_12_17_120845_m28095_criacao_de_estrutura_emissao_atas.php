<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28095CriacaoDeEstruturaEmissaoAtas extends Migration
{
    private function upDicionario()
    {
        DB::unprepared(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229367 ,'Emissão de Atas' ,'Emissão de Atas' ,'web/educacao/secretaria/relatorios/turmas/emissao-atas' ,'1' ,'1' ,'Emissão de Atas' ,'true' ,'false' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 1101110 ,229367 ,42 ,7159 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 1101110 ,229367 ,43 ,1100747 );

insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015686 ,'ed217_exibir_assinatura_secretario' ,'bool' ,'Exibir Assinatura Secretário' ,'t' ,'Exibir Assinatura Secretário' ,1 ,'false' ,'false' ,'false' ,5 ,'text' ,'Exibir Assinatura Secretário' );
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 2571 ,1015686 ,22 ,0 );

insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015687 ,'ed217_exibir_assinatura_adicional' ,'bool' ,'Exibir Assinatura Adicional' ,'t' ,'Exibir Assinatura Adicional' ,1 ,'false' ,'false' ,'false' ,5 ,'text' ,'Exibir Assinatura Adicional' );
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 2571 ,1015687 ,23 ,0 );

insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015688 ,'ed217_exibir_grade_alunos' ,'bool' ,'Exibir Grade Alunos' ,'t' ,'Exibir Grade Alunos' ,1 ,'false' ,'false' ,'false' ,5 ,'text' ,'Exibir Grade Alunos' );
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 2571 ,1015688 ,24 ,0 );

insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015689 ,'ed217_exibir_coluna_disciplinas' ,'bool' ,'Exibir Coluna Disciplinas' ,'t' ,'Exibir Coluna Disciplinas' ,1 ,'false' ,'false' ,'false' ,5 ,'text' ,'Exibir Coluna Disciplinas' );
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 2571 ,1015689 ,25 ,0 );

insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015690 ,'ed217_exibir_coluna_resultado_final' ,'bool' ,'Exibir Coluna Resultado Final' ,'t' ,'Exibir Coluna Resultado Final' ,1 ,'false' ,'false' ,'false' ,5 ,'text' ,'Exibir Coluna Resultado Final' );
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 2571 ,1015690 ,26 ,0 );

insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015691 ,'ed217_exibir_brasao' ,'bool' ,'Exibir Brasão' ,'t' ,'Exibir Brasão' ,1 ,'false' ,'false' ,'false' ,5 ,'text' ,'Exibir Brasão' );
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 2571 ,1015691 ,27 ,0 );

insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015692 ,'ed217_disposicao_brasao' ,'char(20)' ,'Disposição Brasão' ,'LADO' ,'Disposição Brasão' ,20 ,'false' ,'true' ,'false' ,0 ,'text' ,'Disposição Brasão' );
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 2571 ,1015692 ,28 ,0 );

insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015693 ,'ed217_disposicao_cabecalho' ,'char(20)' ,'Disposição Cabeçalho' ,'ESQUERDA' ,'Disposição Cabeçalho' ,20 ,'false' ,'true' ,'false' ,0 ,'text' ,'Disposição Cabeçalho' );
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 2571 ,1015693 ,29 ,0 );
SQL
        );
    }

    private function downDicionario()
    {
        DB::unprepared(<<<SQL
delete from db_menu where id_item_filho = 229367 AND modulo = 1100747;
delete from db_menu where id_item_filho = 229367 AND modulo = 7159;
delete from db_itensmenu where id_item = 229367;

delete from db_syscampodef where codcam = 1015686;  
delete from db_sysarqcamp where codarq = 2571 and codcam = 1015686;
delete from db_syscampo where codcam = 1015686;

delete from db_syscampodef where codcam = 1015687;
delete from db_sysarqcamp where codarq = 2571 and codcam = 1015687;
delete from db_syscampo where codcam = 1015687;

delete from db_syscampodef where codcam = 1015688;
delete from db_sysarqcamp where codarq = 2571 and codcam = 1015688;
delete from db_syscampo where codcam = 1015688;

delete from db_syscampodef where codcam = 1015689;
delete from db_sysarqcamp where codarq = 2571 and codcam = 1015689;
delete from db_syscampo where codcam = 1015689;

delete from db_syscampodef where codcam = 1015690;
delete from db_sysarqcamp where codarq = 2571 and codcam = 1015690;
delete from db_syscampo where codcam = 1015690;

delete from db_syscampodef where codcam = 1015691;
delete from db_sysarqcamp where codarq = 2571 and codcam = 1015691;
delete from db_syscampo where codcam = 1015691;

delete from db_syscampodef where codcam = 1015692;
delete from db_sysarqcamp where codarq = 2571 and codcam = 1015692;
delete from db_syscampo where codcam = 1015692;

delete from db_syscampodef where codcam = 1015693;
delete from db_sysarqcamp where codarq = 2571 and codcam = 1015693;
delete from db_syscampo where codcam = 1015693;
SQL
        );
    }

    private function upEstrutura()
    {
        DB::unprepared(<<<SQL
alter table secretariadeeducacao.edu_relatmodel add column ed217_exibir_assinatura_secretario boolean default true;
alter table secretariadeeducacao.edu_relatmodel add column ed217_exibir_assinatura_adicional boolean default true;
alter table secretariadeeducacao.edu_relatmodel add column ed217_exibir_grade_alunos boolean default true;
alter table secretariadeeducacao.edu_relatmodel add column ed217_exibir_coluna_disciplinas boolean default true;
alter table secretariadeeducacao.edu_relatmodel add column ed217_exibir_coluna_resultado_final boolean default true;
alter table secretariadeeducacao.edu_relatmodel add column ed217_exibir_brasao boolean default true;
alter table secretariadeeducacao.edu_relatmodel add column ed217_disposicao_brasao varchar(20) default 'LADO';
alter table secretariadeeducacao.edu_relatmodel add column ed217_disposicao_cabecalho varchar(20) default 'ESQUERDA';     
SQL
        );
    }

    private function downEstrutura()
    {
        DB::unprepared(<<<SQL
alter table secretariadeeducacao.edu_relatmodel drop column ed217_exibir_assinatura_secretario;
alter table secretariadeeducacao.edu_relatmodel drop column ed217_exibir_assinatura_adicional;
alter table secretariadeeducacao.edu_relatmodel drop column ed217_exibir_grade_alunos;
alter table secretariadeeducacao.edu_relatmodel drop column ed217_exibir_coluna_disciplinas;
alter table secretariadeeducacao.edu_relatmodel drop column ed217_exibir_coluna_resultado_final;
alter table secretariadeeducacao.edu_relatmodel drop column ed217_exibir_brasao;
alter table secretariadeeducacao.edu_relatmodel drop column ed217_disposicao_brasao;
alter table secretariadeeducacao.edu_relatmodel drop column ed217_disposicao_cabecalho;
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
