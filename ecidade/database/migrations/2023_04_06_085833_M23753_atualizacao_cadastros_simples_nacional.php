<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M23753AtualizacaoCadastrosSimplesNacional extends Migration
{
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
                DB::connection()->getPdo()->exec(
                        <<<SQL
        CREATE TABLE parissqnfrequenciaatualizacaocadastros(
                q70_sequencial SERIAL,
                q70_descricao VARCHAR(30),
                CONSTRAINT parissqnfrequenciaatualizacaocadastros_sequencial_pk PRIMARY KEY (q70_sequencial)
        );
        INSERT INTO parissqnfrequenciaatualizacaocadastros (q70_descricao) VALUES ('Diário'), ('Semanal'), ('Mensal');
        ALTER TABLE parissqn ADD COLUMN q60_frequenciaatualizacaosimplesnacional INTEGER;
        ALTER TABLE parissqn ADD COLUMN q60_diasemanalatualizacaosimplesnacional INTEGER;
        ALTER TABLE parissqn ADD COLUMN q60_diamensalatualizacaosimplesnacional INTEGER;
        ALTER TABLE parissqn ADD COLUMN q60_horaatualizacaosimplesnacional TIME;
        ALTER TABLE parissqn ADD CONSTRAINT parissqn_parissqnfrequenciaatualizacaocadastros_fk FOREIGN KEY (q60_frequenciaatualizacaosimplesnacional ) REFERENCES parissqnfrequenciaatualizacaocadastros (q70_sequencial);
        INSERT INTO issmotivobaixa (q42_sequencial, q42_descr) VALUES (3,'INTEGRAÇÃO API');
        ALTER TABLE isscadsimples ADD COLUMN q38_observacao VARCHAR(60);

        CREATE TABLE isscadsimplesatualizacoes(
                q186_sequencial SERIAL,
                q186_data DATE NOT NULL,
                CONSTRAINT isscadsimplesatualizacoes_sequencial_pk PRIMARY KEY (q186_sequencial)
        );

        CREATE TABLE isscadsimplesatualizacoesinclusao(
                q187_sequencial SERIAL,
                q187_isscadsimplesatualizacoes INTEGER NOT NULL,
                q187_isscadsimples INTEGER NOT NULL,
                CONSTRAINT isscadsimplesatualizacoesinclusao_sequencial_pk PRIMARY KEY (q187_sequencial),
                CONSTRAINT isscadsimplesatualizacoesinclusao_isscadsimplesatualizacoes_fk FOREIGN KEY (q187_isscadsimplesatualizacoes) REFERENCES isscadsimplesatualizacoes (q186_sequencial),
                CONSTRAINT isscadsimplesatualizacoes_isscadsimples_fk FOREIGN KEY (q187_isscadsimples) REFERENCES isscadsimples (q38_sequencial)
        );

        CREATE TABLE isscadsimplesatualizacoesbaixa(
                q188_sequencial SERIAL,
                q188_isscadsimplesatualizacoes INTEGER NOT NULL,
                q188_isscadsimplesbaixa INTEGER NOT NULL,
                CONSTRAINT isscadsimplesatualizacoesbaixa_sequencial_pk PRIMARY KEY (q188_sequencial),
                CONSTRAINT isscadsimplesatualizacoesbaixa_isscadsimplesatualizacoes_fk FOREIGN KEY (q188_isscadsimplesatualizacoes) REFERENCES isscadsimplesatualizacoes (q186_sequencial),
                CONSTRAINT isscadsimplesatualizacoes_isscadsimplesbaixa_fk FOREIGN KEY (q188_isscadsimplesbaixa) REFERENCES isscadsimplesbaixa (q39_sequencial)
        );
SQL
                );

                DB::connection()->getPdo()->exec(
                        <<<SQL
        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228908 ,'Agendamento de atualização Simples Nacional' ,'Agendamento de atualização Simples Nacional' ,'web/tributario/issqn/procedimentos/empresas-optantes-do-simples/atualizacao-de-cadastros' ,'1' ,'1' ,'Rotina para configuração do período da sincronização periódica dos cadastros de optantes do Simples Nacional com a api da Receita Federal.' ,'true' );
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 608574 ,228908 ,5 ,40 );
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
                DB::connection()->getPdo()->exec(
                        <<<SQL
                drop table if exists inclusoestestes;
                create temp table inclusoestestes as select isscadsimples.q38_sequencial  from isscadsimplesatualizacoesinclusao join issqn.isscadsimples ON isscadsimples.q38_sequencial = isscadsimplesatualizacoesinclusao.q187_isscadsimples;
        
                drop table if exists issbaixastestes;
                create temp table issbaixastestes as select isscadsimplesatualizacoesbaixa.q188_isscadsimplesbaixa  from isscadsimplesatualizacoesbaixa join issqn.isscadsimplesbaixa ON isscadsimplesbaixa.q39_sequencial = isscadsimplesatualizacoesbaixa.q188_isscadsimplesbaixa join issqn.isscadsimples ON isscadsimples.q38_sequencial = isscadsimplesbaixa.q39_isscadsimples;

                delete from isscadsimplesatualizacoesbaixa;
                delete from isscadsimplesatualizacoesinclusao;
                delete from isscadsimplesatualizacoes;

                delete from isscadsimplesbaixa where q39_sequencial in (select * from issbaixastestes);
                delete from isscadsimples where q38_sequencial in (select * from inclusoestestes);

                DELETE FROM issmotivobaixa where q42_sequencial = 3;
                ALTER TABLE parissqn DROP COLUMN q60_frequenciaatualizacaosimplesnacional;
                ALTER TABLE parissqn DROP COLUMN q60_diasemanalatualizacaosimplesnacional;
                ALTER TABLE parissqn DROP COLUMN q60_diamensalatualizacaosimplesnacional;
                ALTER TABLE parissqn DROP COLUMN q60_horaatualizacaosimplesnacional;
                ALTER TABLE isscadsimples DROP COLUMN q38_observacao;
                DROP TABLE parissqnfrequenciaatualizacaocadastros CASCADE;
                DROP TABLE isscadsimplesatualizacoes CASCADE;
                DROP TABLE isscadsimplesatualizacoesinclusao CASCADE;
                DROP TABLE isscadsimplesatualizacoesbaixa CASCADE;
SQL
                );

                DB::connection()->getPdo()->exec(
                        <<<SQL
        delete from db_menu where id_item = 608574 and id_item_filho = 228908 AND modulo = 40;
        delete from db_itensmenu where id_item = 228908;
SQL
                );
        }
}
