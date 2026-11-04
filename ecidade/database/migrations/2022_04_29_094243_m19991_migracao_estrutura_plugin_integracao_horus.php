<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M19991MigracaoEstruturaPluginIntegracaoHorus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->criarEstrutura();

        if ($this->existePluginHorus()) {
            $this->migrarEstrutura();
        }

        $this->upParametro();
        $this->upDicionario();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
        $this->downParametro();
        $this->downEstrutura();
    }

    private function criarEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            CREATE TABLE farmacia.tipomovimentacaobnafar (
                fa68_codigo serial primary key not null,
                fa68_descricao varchar(200) not null,
                fa68_tipo varchar(1) not null
            );

            CREATE TABLE farmacia.estoquemovimentacaobnafar (
                fa69_codigo serial primary key not null,
                fa69_matestoqueini integer not null,
                fa69_tipomovimentacao integer,
                fa69_cgm integer,
                fa69_unidade integer,
                CONSTRAINT matestoqueini_fk FOREIGN KEY(fa69_matestoqueini) REFERENCES material.matestoqueini(m80_codigo),
                CONSTRAINT tipomovimentacaobnafar_fk FOREIGN KEY(fa69_tipomovimentacao) REFERENCES farmacia.tipomovimentacaobnafar(fa68_codigo)
            );
SQL
        );
        $this->seedTiposMovimentacoes();
    }

    private function existePluginHorus()
    {
        $encontrou = DB::select("SELECT table_name from information_schema.tables where table_name ilike 'horus_distribuidor' and table_schema ilike 'plugins';");
        if ($encontrou) {
            return true;
        }
        return false;
    }

    private function migrarEstrutura()
    {
        $this->atualizarColunas();
        DB::connection()->getPdo()->exec(<<<SQL
            INSERT INTO farmacia.estoquemovimentacaobnafar
                SELECT sequencial, matestoqueini, tipo - 1, cgm, unidade
                FROM plugins.matestoqueinitipo
                INNER JOIN material.matestoqueini ON matestoqueini.m80_codigo = matestoqueini
                WHERE tipo != 1;

            SELECT setval('estoquemovimentacaobnafar_fa69_codigo_seq', (SELECT max(fa69_codigo) FROM farmacia.estoquemovimentacaobnafar), true);
SQL
        );
    }

    private function atualizarColunas()
    {
        $colunas = DB::select("SELECT column_name from information_schema.columns where table_name ilike 'matestoqueinitipo';");

        if (count($colunas) == 9) {
            return;
        }

        Schema::table('plugins.matestoqueinitipo', function (Blueprint $table) {
            if (!Schema::hasColumn('matestoqueinitipo', 'unidade')) {
                $table->integer('unidade')->nullable()->default(null);
            }
        });
    }

    private function seedTiposMovimentacoes()
    {
        $tipos = [
            ['fa68_codigo' => 1, 'fa68_descricao' => 'Doação', 'fa68_tipo' => 'A'],
            ['fa68_codigo' => 2, 'fa68_descricao' => 'Perda', 'fa68_tipo' => 'S'],
            ['fa68_codigo' => 3, 'fa68_descricao' => 'Ajuste de estoque', 'fa68_tipo' => 'A'],
            ['fa68_codigo' => 4, 'fa68_descricao' => 'Eventual', 'fa68_tipo' => 'E'],
            ['fa68_codigo' => 5, 'fa68_descricao' => 'Ordinária', 'fa68_tipo' => 'E'],
            ['fa68_codigo' => 6, 'fa68_descricao' => 'Permuta', 'fa68_tipo' => 'E'],
            ['fa68_codigo' => 7, 'fa68_descricao' => 'Transferência / Remanejamento', 'fa68_tipo' => 'A'],
            ['fa68_codigo' => 8, 'fa68_descricao' => 'Saldo de implantação', 'fa68_tipo' => 'E'],
            ['fa68_codigo' => 9, 'fa68_descricao' => 'Validade vencida', 'fa68_tipo' => 'S'],
            ['fa68_codigo' => 10, 'fa68_descricao' => 'Distribuição', 'fa68_tipo' => 'S'],
            ['fa68_codigo' => 11, 'fa68_descricao' => 'Devolução de empréstimo', 'fa68_tipo' => 'S'],
            ['fa68_codigo' => 12, 'fa68_descricao' => 'Empréstimo', 'fa68_tipo' => 'S'],
            ['fa68_codigo' => 13, 'fa68_descricao' => 'Apreensão sanitária', 'fa68_tipo' => 'S'],
            ['fa68_codigo' => 14, 'fa68_descricao' => 'Amostra, exposição e análise', 'fa68_tipo' => 'S'],
            ['fa68_codigo' => 15, 'fa68_descricao' => 'Devolução de entrada de produto', 'fa68_tipo' => 'S']
        ];
        DB::table('farmacia.tipomovimentacaobnafar')->insert($tipos);
    }

    private function upParametro()
    {
        Schema::table('farmacia.far_parametros', function (Blueprint $table) {
            $table->boolean('fa02_utiliza_bnafar')->default(false);
        });
    }

    private function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            insert into db_sysarquivo values (1010908, 'tipomovimentacaobnafar', 'Tipos de movimentação do estoque.', 'fa68', '2022-05-02', 'Tipos de Movimentações', 1, 'f', 'f', 't', 't' );
            insert into db_sysarqmod values (52,1010908);
            insert into db_sysarquivo values (1010909, 'estoquemovimentacaobnafar', 'Dados da movimentações do estoque para a integração BNAFAR.', 'fa69', '2022-05-02', 'Movimentações do Estoque', 0, 'f', 'f', 't', 't' );
            insert into db_sysarqmod values (52,1010909);

            insert into db_syscampo values(1014040,'fa68_codigo','int4','Chave primária.','0', 'Código',10,'f','f','f',1,'text','Código');
            insert into db_syscampo values(1014041,'fa68_descricao','varchar(200)','Descrição da movimentação.','', 'Descrição',200,'f','f','f',0,'text','Descrição');
            insert into db_syscampo values(1014042,'fa68_tipo','char(1)','Tipo da movimentação. A: Ambos; S: Saída; E: Entrada;','', 'Tipo',1,'f','t','f',0,'text','Tipo');
            insert into db_syscampo values(1014043,'fa69_codigo','int4','Chave primária.','0', 'Código',10,'f','f','f',1,'text','Código');
            insert into db_syscampo values(1014044,'fa69_matestoqueini','int4','Chave estrangeira da tabela matestoqueini.','0', 'Estoque Inicial',10,'f','f','f',1,'text','Estoque Inicial');
            insert into db_syscampo values(1014045,'fa69_tipomovimentacao','int4','Chave estrangeira da tabela tipomovimentacao.','0', 'Tipo de Movimentação',10,'f','f','f',1,'text','Tipo de Movimentação');
            insert into db_syscampo values(1014046,'fa69_cgm','int4','Referência um CGM destino ou CGM origem/Distribuidor.','0', 'CGM',10,'t','f','f',1,'text','CGM');
            insert into db_syscampo values(1014047,'fa69_unidade','int4','Guarda a unidade de destino ou unidade origem/distribuidor.','0', 'Unidade',10,'t','f','f',1,'text','Unidade');
            insert into db_syscampo values(1014051,'m105_matestoqueitem','int4','Chave estrangeira da matestoqueitem.','0', 'Estoque Item',10,'f','f','f',1,'text','Estoque Item');
            insert into db_syscampo values(1014052,'m105_cgm','int4','Chave estrangeira do cgm.','0', 'CGM',10,'f','f','f',1,'text','CGM');

            insert into db_sysarqcamp values(1010908,1014040,1,0);
            insert into db_sysarqcamp values(1010908,1014041,2,0);
            insert into db_sysarqcamp values(1010908,1014042,3,0);
            insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1010908,1014040,1,1014040);

            insert into db_sysarqcamp values(1010909,1014043,1,0);
            insert into db_sysarqcamp values(1010909,1014044,2,0);
            insert into db_sysarqcamp values(1010909,1014045,3,0);
            insert into db_sysarqcamp values(1010909,1014046,4,0);
            insert into db_sysarqcamp values(1010909,1014047,5,0);
            insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1010909,1014043,1,1014043);
            insert into db_sysforkey values(1010909,1014044,1,1133,0);
            insert into db_sysforkey values(1010909,1014045,1,1010908,0);

            insert into db_syscampo values(1014053,'fa02_utiliza_bnafar','bool','Informa se o cliente utiliza integração com o sistema BNAFAR.','f', 'Utiliza Integração BNAFAR',1,'f','f','f',5,'text','Utiliza Integração BNAFAR');
            insert into db_sysarqcamp values(2103,1014053,21,0);

            insert into db_syssequencia values(1001094, 'tipomovimentacaobnafar_fa68_codigo_seq', 1, 1, 9223372036854775807, 1, 1);
            update db_sysarqcamp set codsequencia = 1001094 where codarq = 1010908 and codcam = 1014040;
            insert into db_syssequencia values(1001095, 'estoquemovimentacaobnafar_fa69_codigo_seq', 1, 1, 9223372036854775807, 1, 1);
            update db_sysarqcamp set codsequencia = 1001095 where codarq = 1010909 and codcam = 1014043;
SQL
        );
    }

    private function downEstrutura()
    {
        Schema::drop('farmacia.estoquemovimentacaobnafar');
        Schema::drop('farmacia.tipomovimentacaobnafar');
    }

    private function downParametro()
    {
        Schema::table('farmacia.far_parametros', function (Blueprint $table) {
            $table->dropColumn(['fa02_utiliza_bnafar']);
        });
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            delete from db_syssequencia where codsequencia in (1001094, 1001095);
            delete from db_sysforkey where codarq = 1010909;
            delete from db_sysprikey where codarq in (1010909, 1010908);
            delete from db_sysarqcamp where codcam in (1014040,1014041,1014042,1014043,1014044,1014045,1014046,1014047,1014051,1014052,1014053);
            delete from db_syscampo where codcam in (1014040,1014041,1014042,1014043,1014044,1014045,1014046,1014047,1014051,1014052,1014053);
            delete from db_sysarqmod where codarq in (1010909, 1010908);
            delete from db_sysarquivo where codarq in (1010909, 1010908);
SQL
        );
    }
}
