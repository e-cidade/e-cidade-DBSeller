<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24510CriacaoEstruturaNovaTabelaPlacaixausu extends Migration
{
    private function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_sysarquivo values (1011099, 'placaixausu', 'Possui a informação do usuário que inseriu a planilha no sistema.', 'k214', '2023-06-15', 'placaixausu', 0, 'f', 't', 't', 't' );
insert into db_sysarqmod values (5,1011099);
insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015171 ,'k214_sequencial' ,'int4' ,'Sequencial' ,'' ,'Sequencial' ,6 ,'false' ,'false' ,'false' ,1 ,'text' ,'Sequencial' );
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 1011099 ,1015171 ,1 ,0 );
insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015172 ,'k214_placaixa' ,'int4' ,'Código da Planilha' ,'' ,'Código da Planilha' ,6 ,'false' ,'false' ,'false' ,1 ,'text' ,'Código da Planilha' );
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 1011099 ,1015172 ,2 ,0 );
insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015173 ,'k214_db_usuario' ,'int4' ,'Código do Usuário' ,'' ,'Código Usuário' ,6 ,'false' ,'false' ,'false' ,1 ,'text' ,'Código Usuário' );
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 1011099 ,1015173 ,3 ,0 );
insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015174 ,'k214_db_depart' ,'int4' ,'Código do Departamento' ,'' ,'Código Departamento' ,6 ,'false' ,'false' ,'false' ,1 ,'text' ,'Código Departamento' );
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 1011099 ,1015174 ,4 ,0 );
insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011099,1015171,1,1015171);
insert into db_sysforkey values(1011099,1015172,1,1023,0);
insert into db_sysforkey values(1011099,1015173,1,109,0);
insert into db_sysforkey values(1011099,1015174,1,154,0);
insert into db_sysindices values(1008878,'placaixausu_k214_placaixa_unique',1011099,'1');
insert into db_syscadind values(1008878,1015172,1);
insert into db_syssequencia values(1001132, 'placaixausu_k214_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
update db_sysarqcamp set codsequencia = 1001132 where codarq = 1011099 and codcam = 1015171;
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228929 ,'Manutenção vínculo Planilha/Departamento' ,'Manutenção vínculo Planilha/Departamento' ,'cai4_manutencaoplanilhadepartamento001.php' ,'1' ,'1' ,'Realização da manutenção do vínculo entre a planilha e o departamento' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 9419 ,228929 ,6 ,39 );
SQL
        );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_sysforkey where codarq = 1011099;
delete from db_syscampodef where codcam = 1015171;
delete from db_sysarqcamp where codcam = 1015171;
delete from db_syscampo where codcam = 1015171;
delete from db_syssequencia where codsequencia = 1001132;
delete from db_syscampodef where codcam = 1015172;
delete from db_sysarqcamp where codcam = 1015172;
delete from db_syscampo where codcam = 1015172;
delete from db_syscampodef where codcam = 1015173;
delete from db_sysarqcamp where codcam = 1015173;
delete from db_syscampo where codcam = 1015173;
delete from db_syscampodef where codcam = 1015174;
delete from db_sysarqcamp where codcam = 1015174;
delete from db_syscampo where codcam = 1015174;
delete from db_sysprikey where codarq = 1011099;
delete from db_sysarqmod where codarq = 1011099;
delete from db_sysarquivo where codarq = 1011099;
delete from db_sysindices where codind = 1008878;
delete from db_syscadind where codind = 1008878 and codcam = 1015172;
delete from db_menu where id_item_filho = 228929 AND modulo = 39;
delete from db_itensmenu where id_item = 228929;
SQL
        );
    }
    
    private function upEstrutura()
    {
DB::connection()->getPdo()->exec(<<<SQL
CREATE SEQUENCE placaixausu_k214_sequencial_seq
INCREMENT 1
MINVALUE 1
MAXVALUE 9223372036854775807
START 1
CACHE 1;
CREATE TABLE caixa.placaixausu (
k214_sequencial int4 not null default nextval('placaixausu_k214_sequencial_seq'),
k214_placaixa int4 not null references placaixa(k80_codpla),
k214_db_usuario int4 not null references db_usuarios(id_usuario),
k214_db_depart int4 not null references db_depart(coddepto),
CONSTRAINT placaixausu_k214_sequencial_pk PRIMARY KEY (k214_sequencial)
);
CREATE UNIQUE INDEX placaixausu_k214_placaixa_unique ON caixa.placaixausu(k214_placaixa);
SQL
        );
    }

    private function downEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
DROP TABLE caixa.placaixausu;
DROP SEQUENCE placaixausu_k214_sequencial_seq;        
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
