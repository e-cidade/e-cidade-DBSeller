<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23233CriacaoDeTabelaAcordoprocECampos extends Migration
{
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

    private function upDicionario() {
        DB::connection()->getPdo()->exec(<<<SQL
--create table acordoproc
insert into db_sysarquivo values (1011088, 'acordoproc', 'Processos dos Acordos', 'ac62', '2023-05-25', 'Processos dos Acordos', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (69,1011088);
--create campos
insert into db_syscampo values(1015104,'ac62_sequencial','int4','Sequencial','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');
insert into db_syscampo values(1015106,'ac62_protprocesso','int4','Processo do Acordo','0', 'Processo do Acordo',10,'f','f','f',1,'text','Processo do Acordo');
insert into db_syscampodep values(1015106,'2454');
insert into db_syscampo values(1015107,'ac62_acordo','int4','Acordo','0', 'Acordo',10,'f','f','f',1,'text','Acordo');
insert into db_syscampodep values(1015107,'16116');
insert into db_syscampo values(1015108,'ac62_numeroprocesso','varchar(60)','Numero do Processo do acordo','', 'Numero do Processo',60,'t','t','f',0,'text','Numero do Processo');
--organizar campos / vincular campos na tabela
insert into db_sysarqcamp values(1011088,1015104,1,0);
insert into db_sysarqcamp values(1011088,1015107,2,0);
insert into db_sysarqcamp values(1011088,1015106,3,0);
insert into db_sysarqcamp values(1011088,1015108,4,0);
--chave primaria
insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011088,1015104,1,1015108);
--chave estrangeira
insert into db_sysforkey values(1011088,1015107,1,2828,0);
insert into db_sysforkey values(1011088,1015106,1,403,0);
--cadastrar indices
insert into db_sysindices values(1008861,'acordoproc_protprocesso_in',1011088,'0');
insert into db_syscadind values(1008861,1015106,1);
insert into db_sysindices values(1008862,'acordoproc_acordo_in',1011088,'0');
insert into db_syscadind values(1008862,1015107,1);
insert into db_sysindices values(1008863,'acordoproc_protprocesso_acordo_in',1011088,'1');
insert into db_syscadind values(1008863,1015108,1);
insert into db_syscadind values(1008863,1015107,2);
--cadastrar sequencia
insert into db_syssequencia values(1001128, 'acordoproc_ac62_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
update db_sysarqcamp set codsequencia = 1001128 where codarq = 1011088 and codcam = 1015104;
SQL
        );
    }

    private function downDicionario() {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_syscadind where codind in (1008861, 1008862,1008863);
delete from db_sysindices where codind in (1008861, 1008862, 1008863);
delete from db_syssequencia where codsequencia = 1001128;
delete from db_sysprikey where codarq = 1011088 and codcam = 1015104;
delete from db_sysforkey where codarq = 1011088 and codcam in (1015107, 1015106);
delete from db_sysarqcamp where codarq = 1011088 and codcam in (1015104, 1015107, 1015106, 1015108);
delete from db_syscampodep where codcam in (1015106, 1015107);
delete from db_syscampo where codcam in (1015104, 1015106, 1015107, 1015108);
delete from db_sysarqmod where codmod = 69 and codarq = 1011088;
delete from db_sysarquivo where codarq = 1011088;
SQL
        );
    }

    private function upEstrutura() {
        DB::connection()->getPdo()->exec(<<<SQL
CREATE SEQUENCE acordos.acordoproc_ac62_sequencial_seq
    INCREMENT 1
        MINVALUE 1
        MAXVALUE 9223372036854775807
        START 1
        CACHE 1;

CREATE TABLE acordos.acordoproc (
    ac62_sequencial SERIAL PRIMARY KEY,
    ac62_protprocesso INTEGER NOT NULL,
    ac62_acordo INTEGER NOT NULL,
    ac62_numeroprocesso VARCHAR(60),
    CONSTRAINT acordoproc_ac62_protprocesso_fk
        FOREIGN KEY (ac62_protprocesso)
            REFERENCES protprocesso (p58_codproc),
    CONSTRAINT acordoproc_ac62_acordo_fk
        FOREIGN KEY (ac62_acordo)
            REFERENCES acordo (ac16_sequencial)
);

CREATE INDEX acordoproc_protprocesso_in ON acordos.acordoproc(ac62_protprocesso);
CREATE INDEX acordoproc_acordo_in ON acordos.acordoproc(ac62_acordo);
CREATE UNIQUE INDEX acordoproc_protprocesso_acordo_in ON acordos.acordoproc(ac62_protprocesso, ac62_acordo);
SQL
        );
    }

    private function downEstrutura() {
        DB::connection()->getPdo()->exec(<<<SQL
DROP SEQUENCE acordoproc_ac62_sequencial_seq;
DROP TABLE acordoproc;
SQL
        );
    }
}
