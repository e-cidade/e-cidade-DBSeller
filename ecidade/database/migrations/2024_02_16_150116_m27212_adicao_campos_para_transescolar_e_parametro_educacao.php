<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27212AdicaoCamposParaTransescolarEParametroEducacao extends Migration
{
    private function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015617 ,'ed47_v_sigla_concessionaria' ,'varchar(10)' ,'Sigla da concessionária de energia elétrica' ,'' ,'Sigla Concessionária' ,10 ,'true' ,'true' ,'false' ,0 ,'text' ,'Sigla Concessionária' );
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 1010051 ,1015617 ,84 ,0 );
insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015618 ,'ed47_v_codigo_energia' ,'varchar(20)' ,'Código do padrão de energia (poste)' ,'' ,'Código Energia' ,20 ,'true' ,'false' ,'false' ,0 ,'text' ,'Código Energia' );
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 1010051 ,1015618 ,85 ,0 );
insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015619 ,'ed18_v_sigla_concessionaria' ,'varchar(10)' ,'Sigla da concessionária de energia elétrica' ,'' ,'Sigla Concessionária' ,10 ,'true' ,'true' ,'false' ,0 ,'text' ,'Sigla Concessionária' );
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 1010031 ,1015619 ,41 ,0 );
insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015620 ,'ed18_v_codigo_energia' ,'varchar(20)' ,'Código do padrão de energia (poste)' ,'' ,'Código Energia' ,20 ,'true' ,'false' ,'false' ,0 ,'text' ,'Código Energia' );
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 1010031 ,1015620 ,42 ,0 );
insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015621 ,'ed290_habilitarcampostransescolar' ,'bool' ,'Habilitar campos para transescolar' ,'' ,'Habilitar Campos Transescolar' ,1 ,'false' ,'false' ,'false' ,5 ,'text' ,'Habilitar Campos Transescolar' );
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 3180 ,1015621 ,10 ,0 );
SQL
        );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_sysarqcamp where codcam = 1015617 and codarq = 1010051;
delete from db_syscampo where codcam = 1015617;
delete from db_sysarqcamp where codcam = 1015618 and codarq = 1010051;
delete from db_syscampo where codcam = 1015618;
delete from db_sysarqcamp where codcam = 1015619 and codarq = 1010031;
delete from db_syscampo where codcam = 1015619;
delete from db_sysarqcamp where codcam = 1015620 and codarq = 1010031;
delete from db_syscampo where codcam = 1015620;
delete from db_sysarqcamp where codcam = 1015621 and codarq = 3180;
delete from db_syscampo where codcam = 1015621;
SQL
        );
    }

    private function upEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
alter table escola.escola add column ed18_v_sigla_concessionaria varchar(10) default null;
alter table escola.escola add column ed18_v_codigo_energia varchar(20) default null;
alter table escola.aluno add column ed47_v_sigla_concessionaria varchar(10) default null;
alter table escola.aluno add column ed47_v_codigo_energia varchar(20) default null;
alter table secretariadeeducacao.sec_parametros add column ed290_habilitarcampostransescolar boolean default false;
SQL
        );
    }

    private function downEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
alter table escola.escola drop column ed18_v_sigla_concessionaria;
alter table escola.escola drop column ed18_v_codigo_energia;
alter table escola.aluno drop column ed47_v_sigla_concessionaria;
alter table escola.aluno drop column ed47_v_codigo_energia;
alter table secretariadeeducacao.sec_parametros drop column ed290_habilitarcampostransescolar;
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
