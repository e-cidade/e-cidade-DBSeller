<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CriacaoTabelaGerfsad13 extends Migration
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

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function upDicionario()
    {
        $sql = <<<SQL
            insert into db_sysarquivo values (1011073, 'gerfsad13', 'Tabela que fica armazenado os valores referentes ao Cálculo do Adiantamento do 13º Salário', 'r95', '2023-05-09', 'r95', 0, 'f', 'f', 'f', 'f' );
            insert into db_sysarqmod values (28,1011073);
            insert into db_syscampo values(1015045,'r95_anousu','int4','Ano da Competência do Cálculo','0', 'Ano da Competência do Cálculo',10,'f','f','f',1,'text','Ano da Competência do Cálculo');
            insert into db_syscampo values(1015046,'r95_mesusu','int4','Mês da Competência do Cálculo','0', 'Mês da Competência do Cálculo',10,'f','f','f',1,'text','Mês da Competência do Cálculo');
            insert into db_syscampo values(1015047,'r95_regist','int4','Código da Matrícula do Servidor','0', 'Código da Matrícula do Servidor',10,'f','f','f',1,'text','Código da Matrícula do Servidor');
            insert into db_syscampo values(1015048,'r95_rubric','varchar(4)','Código da Rubrica','', 'Código da Rubrica',4,'f','t','f',0,'text','Código da Rubrica');
            insert into db_syscampo values(1015049,'r95_valor','float4','Valor da Rubrica','0', 'Valor da Rubrica',10,'t','f','f',4,'text','Valor da Rubrica');
            insert into db_syscampo values(1015050,'r95_pd','int4','Tipo da Rubrica','0', 'Tipo da Rubrica',10,'t','f','f',1,'text','Tipo da Rubrica');
            insert into db_syscampo values(1015051,'r95_quant','float4','Quantidade da Rubrica','0', 'Quantidade da Rubrica',10,'t','f','f',4,'text','Quantidade da Rubrica');
            insert into db_syscampo values(1015052,'r95_lotac','varchar(4)','Lotação do Servidor','', 'Lotação do Servidor',4,'f','t','f',0,'text','Lotação do Servidor');
            insert into db_syscampo values(1015053,'r95_semest','int4','Semestre da Rubrica','0', 'Semestre da Rubrica',10,'t','f','f',1,'text','Semestre da Rubrica');
            insert into db_syscampo values(1015054,'r95_tpp','varchar(1)','tpp','', 'tpp',1,'f','t','f',0,'text','tpp');
            insert into db_syscampo values(1015055,'r95_instit','int4','Instituição do Cálculo','0', 'Instituição do Cálculo',10,'f','f','f',1,'text','Instituição do Cálculo');
            insert into db_sysarqcamp values(1011073,1015045,1,0);
            insert into db_sysarqcamp values(1011073,1015046,2,0);
            insert into db_sysarqcamp values(1011073,1015047,3,0);
            insert into db_sysarqcamp values(1011073,1015048,4,0);
            insert into db_sysarqcamp values(1011073,1015049,5,0);
            insert into db_sysarqcamp values(1011073,1015050,6,0);
            insert into db_sysarqcamp values(1011073,1015051,7,0);
            insert into db_sysarqcamp values(1011073,1015052,8,0);
            insert into db_sysarqcamp values(1011073,1015053,9,0);
            insert into db_sysarqcamp values(1011073,1015054,10,0);
            insert into db_sysarqcamp values(1011073,1015055,11,0);
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    private function upEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL

create table pessoal.gerfsad13(
    r95_anousu integer,
    r95_mesusu integer,
    r95_regist integer,
    r95_rubric varchar(4),
    r95_valor double precision,
    r95_pd integer,
    r95_quant double precision,
    r95_lotac varchar(4),
    r95_semest integer,
    r95_tpp varchar (1),
    r95_instit integer,

    PRIMARY KEY (r95_anousu, r95_mesusu, r95_regist, r95_rubric, r95_pd),

    FOREIGN KEY (r95_instit) references configuracoes.db_config (codigo) on DELETE CASCADE
);

CREATE INDEX gerfsad13_r95_anousu_r95_mesusu_r95_regist_r95_rubric_in ON pessoal.gerfsad13(r95_anousu,r95_mesusu,r95_regist,r95_rubric);

SELECT configuracoes.fc_auditoria_cria_funcao('pessoal.gerfsad13');

SQL
);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function downDicionario()
    {
        $sql = <<<SQL
            delete from db_sysarqcamp where codarq = 1011073;
            delete from db_syscampo where codcam in (1015045,1015046,1015047,1015048,1015049,1015050,1015051,1015052,1015053,1015054,1015055);
            delete from db_sysarqmod where codmod = 28 and codarq = 1011073;
            delete from db_sysarquivo where codarq = 1011073;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    private function downEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            drop table IF EXISTS pessoal.gerfsad13 CASCADE;
SQL
        );
    }
}
