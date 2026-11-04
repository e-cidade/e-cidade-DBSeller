<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24420CriarTabelaCancelamentoviagemTfd extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tfd.cancelamentoviagem', function (Blueprint $table) {
            $table->increments('tf41_codigo');
            $table->integer('tf41_usuario');
            $table->dateTime('tf41_datahora');
            $table->integer('tf41_veiculodestino');
            $table->string('tf41_motivocancelamento');

            $table->foreign('tf41_veiculodestino')->references('tf18_i_codigo')->on('tfd.tfd_veiculodestino');
            $table->foreign('tf41_usuario')->references('id_usuario')->on('db_usuarios');
        });

        DB::connection()->getPdo()->exec(<<<SQL

            insert into db_sysarquivo values (1011090, 'cancelamentoviagem', 'Refere-se ao cancelamento da viagem(tfd_veiculodestino).', 'tf41', '2023-05-29', 'Cancelamento Viagem', 0, 't', 't', 't', 't' );
            insert into db_sysarqmod values (70,1011090);
            insert into db_syscampo values(1015119,'tf41_codigo','int4','Chave primária da tabela cancelamentoviagem','0', 'Código',10,'f','f','f',1,'text','Código');
            insert into db_syscampo values(1015120,'tf41_usuario','int4','Chave estrangeira da tabela db_usuario.','0', 'Usuário',10,'f','f','f',1,'text','Usuário');
            insert into db_syscampo values(1015121,'tf41_datahora','date','Campo referente a data e hora da efetivação do cancelamento da viagem.','null', 'Data Hora',10,'f','f','f',3,'text','Data Hora');
            insert into db_syscampo values(1015122,'tf41_veiculodestino','int4','Chave estrangeira da tabela tfd_veiculodestino.','0', 'Veículo Destino',10,'f','f','f',1,'text','Veículo Destino');
            insert into db_syscampo values(1015123,'tf41_motivocancelamento','varchar(120)','O motivo descritivo daquela viagem ter sido cancelada.','', 'Motivo Cancelamento',120,'f','t','f',0,'text','Motivo Cancelamento');
            insert into db_sysarqcamp values(1011090,1015119,1,0);
            insert into db_sysarqcamp values(1011090,1015120,2,0);
            insert into db_sysarqcamp values(1011090,1015121,3,0);
            insert into db_sysarqcamp values(1011090,1015122,4,0);
            insert into db_sysarqcamp values(1011090,1015123,5,0);
            insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011090,1015119,1,1015119);
            insert into db_sysforkey values(1011090,1015120,1,109,0);
            insert into db_sysforkey values(1011090,1015122,1,2874,0);

SQL
        );

        DB::statement(<<<SQL

CREATE OR REPLACE FUNCTION public.fc_verificaexclusaosaidatfd()
    RETURNS trigger
    LANGUAGE plpgsql
AS $$
DECLARE
    iNumLinhasAfetadas INT;
    pVerificaVeiculoDestino CURSOR FOR
        SELECT tf18_i_codigo
        FROM tfd_veiculodestino
                 INNER JOIN tfd_passageiroveiculo on tfd_passageiroveiculo.tf19_i_veiculodestino =
                                                     tfd_veiculodestino.tf18_i_codigo AND
                                                     tfd_passageiroveiculo.tf19_i_valido = 1
        WHERE tf19_i_pedidotfd = OLD.tf17_i_pedidotfd
          AND NOT EXISTS (SELECT 1 FROM cancelamentoviagem where tf41_veiculodestino = tf18_i_codigo);
    rsDados            RECORD;

BEGIN
    -- Não posso permitir deletar um registro da tfd_agendasaida se já foi indicado um veiculo para o pedido de TFD da da saída (tf17_i_pedidotfd)
    IF TG_OP = 'DELETE' OR TG_OP = 'UPDATE' THEN
        OPEN pVerificaVeiculoDestino;
        FETCH pVerificaVeiculoDestino INTO rsDados;
        IF NOT FOUND THEN -- Significa que não foi indicado veículo para ninguém do pedido de TFD, então, pode excluir
            IF TG_OP = 'DELETE' THEN
                CLOSE pVerificaVeiculoDestino;
                RETURN OLD;
            ELSE
                CLOSE pVerificaVeiculoDestino;
                RETURN NEW;
            END IF;
        ELSE
            RAISE EXCEPTION 'O pedido de TFD já possui paciente ou acompanhantes lançados na rotina Indique Veículos.';
            IF TG_OP = 'DELETE' THEN
                RETURN NEW;
            ELSE
                RETURN OLD;
            END IF;
        END IF;
    END IF;
END;
$$

SQL
        );

        DB::statement("select configuracoes.fc_auditoria_cria_funcao('tfd.cancelamentoviagem');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tfd.cancelamentoviagem');

        DB::connection()->getPdo()->exec(<<<SQL

            delete from db_sysarqcamp where codarq = 1011090;
            delete from db_sysprikey where codarq = 1011090;
            delete from db_sysforkey where codarq = 1011090;
            delete from db_sysforkey where codarq = 1011090;
            delete from db_syscampo where codcam in (1015119, 1015120, 1015121, 1015122, 1015123);
            delete from db_sysarqmod where codarq = 1011090;
            delete from db_sysarquivo where codarq = 1011090;

SQL
        );

        DB::statement(<<<SQL

CREATE OR REPLACE FUNCTION public.fc_verificaexclusaosaidatfd()
    RETURNS trigger
    LANGUAGE plpgsql
AS $$
DECLARE
    iNumLinhasAfetadas INT;
    pVerificaVeiculoDestino CURSOR FOR
        SELECT tf18_i_codigo
        FROM tfd_veiculodestino
                 INNER JOIN tfd_passageiroveiculo on tfd_passageiroveiculo.tf19_i_veiculodestino =
                                                     tfd_veiculodestino.tf18_i_codigo AND
                                                     tfd_passageiroveiculo.tf19_i_valido = 1
        WHERE tf19_i_pedidotfd = OLD.tf17_i_pedidotfd;
    rsDados            RECORD;

BEGIN
    -- Não posso permitir deletar um registro da tfd_agendasaida se já foi indicado um veiculo para o pedido de TFD da da saída (tf17_i_pedidotfd)
    IF TG_OP = 'DELETE' OR TG_OP = 'UPDATE' THEN
        OPEN pVerificaVeiculoDestino;
        FETCH pVerificaVeiculoDestino INTO rsDados;
        IF NOT FOUND THEN -- Significa que não foi indicado veículo para ninguém do pedido de TFD, então, pode excluir
            IF TG_OP = 'DELETE' THEN
                CLOSE pVerificaVeiculoDestino;
                RETURN OLD;
            ELSE
                CLOSE pVerificaVeiculoDestino;
                RETURN NEW;
            END IF;
        ELSE
            RAISE EXCEPTION 'O pedido de TFD já possui paciente ou acompanhantes lançados na rotina Indique Veículos.';
            IF TG_OP = 'DELETE' THEN
                RETURN NEW;
            ELSE
                RETURN OLD;
            END IF;
        END IF;
    END IF;
END;
$$

SQL
        );
    }
}
