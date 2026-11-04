<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24561PlPlanoPadraoBalver extends Migration
{
    public function up()
    {
        $this->upPlUniao();
        $this->upPlUf();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

    private function upPlUniao()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop function if exists contabilidade.balancete_verificacao_plano_uniao(int, date, date, boolean);

--
-- Essa PL totaliza o resultado do balancete de verificacao por recurso usando o plano PCASP da UNIAO.
--
create or replace function contabilidade.balancete_verificacao_plano_uniao(
    f_ano integer,
    f_dataInicio date,
    f_dataFim date,
    f_encerramento boolean
)
    returns table (
       estrutural varchar(13),
       exercicio integer,
       classe integer,
       nome varchar,
       indicador_superavit char(1),
       instituicao integer,
       id_recurso integer,
       siconfi varchar(4),
       gestao varchar(4),
       subrecurso varchar(4),
       complemento integer,
       saldo_anterior numeric(17,2),
       saldo_debito numeric(17,2),
       saldo_credito numeric(17,2),
       saldo_final numeric(17,2),
       sinal_anterior char(1),
       sinal_final char(1),
       codigos_conplano int[],
       reduzidos int[]
    )
    language plpgsql
as $$
declare
    recordDados record;
begin

    for recordDados in (
      with plano_uniao as (
        select
            pcasp.conta,
            pcasp.nome,
            pcasp.indicador,
            b.exercicio,
            b.classe,
            b.instituicao,
            b.id_recurso,
            b.siconfi,
            b.gestao,
            b.subrecurso,
            b.complemento,
            array_agg(b.reduzido) as reduzidos,
            array_agg(b.codigo_conplano) as codigos_conplano,
            sum(b.saldo_anterior) as saldo_anterior,
            sum(b.saldo_debito) as saldo_debito,
            sum(b.saldo_credito) as saldo_credito
          from contabilidade.balancete_verificacao_por_recurso(f_ano, f_dataInicio, f_dataFim, f_encerramento) b
          join contabilidade.pcaspconplano on pcaspconplano.conplano_codigo = b.codigo_conplano
          join contabilidade.pcasp on pcasp.id = pcaspconplano.pcasp_id
          where uniao is true
          group by pcasp.conta, pcasp.nome, pcasp.indicador, b.exercicio, b.classe, b.instituicao, b.id_recurso,
                   b.siconfi, b.gestao, b.subrecurso, b.complemento
      ) select * from plano_uniao
    ) loop

        estrutural = recordDados.conta;
        exercicio = recordDados.exercicio;
        codigos_conplano = recordDados.codigos_conplano;
        indicador_superavit = recordDados.indicador;
        reduzidos = recordDados.reduzidos;
        classe = recordDados.classe;
        nome = recordDados.nome;
        instituicao = recordDados.instituicao;
        id_recurso = recordDados.id_recurso;
        siconfi = recordDados.siconfi;
        gestao = recordDados.gestao;
        subrecurso = recordDados.subrecurso;
        complemento = recordDados.complemento;
        saldo_anterior = recordDados.saldo_anterior;
        saldo_debito = recordDados.saldo_debito;
        saldo_credito = recordDados.saldo_credito;
        saldo_final = saldo_anterior + saldo_debito + saldo_credito;
        sinal_anterior = fc_sinal_saldo_conta(classe, saldo_anterior);
        sinal_final = fc_sinal_saldo_conta(classe, saldo_final);

    return next;
end loop;
    end;
$$;
SQL
        );
    }

    private function upPlUf()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop function if exists contabilidade.balancete_verificacao_plano_uf(int, date, date, boolean);

--
-- Essa PL totaliza o resultado do balancete de verificacao por recurso usando o plano PCASP do ESTADO.
--
create or replace function contabilidade.balancete_verificacao_plano_uf(
    f_ano integer,
    f_dataInicio date,
    f_dataFim date,
    f_encerramento boolean
)
    returns table (
       estrutural varchar(13),
       exercicio integer,
       classe integer,
       nome varchar,
       indicador_superavit char(1),
       instituicao integer,
       id_recurso integer,
       siconfi varchar(4),
       gestao varchar(4),
       subrecurso varchar(4),
       complemento integer,
       saldo_anterior numeric(17,2),
       saldo_debito numeric(17,2),
       saldo_credito numeric(17,2),
       saldo_final numeric(17,2),
       sinal_anterior char(1),
       sinal_final char(1),
       codigos_conplano int[],
       reduzidos int[]
    )
    language plpgsql
as $$
declare
    recordDados record;
begin

    for recordDados in (
      with plano_estadual as (
        select
            pcasp.conta,
            pcasp.nome,
            pcasp.indicador,
            b.exercicio,
            b.classe,
            b.instituicao,
            b.id_recurso,
            b.siconfi,
            b.gestao,
            b.subrecurso,
            b.complemento,
            array_agg(b.reduzido) as reduzidos,
            array_agg(b.codigo_conplano) as codigos_conplano,
            sum(b.saldo_anterior) as saldo_anterior,
            sum(b.saldo_debito) as saldo_debito,
            sum(b.saldo_credito) as saldo_credito
          from contabilidade.balancete_verificacao_por_recurso(f_ano, f_dataInicio, f_dataFim, f_encerramento) b
          join contabilidade.pcaspconplano on pcaspconplano.conplano_codigo = b.codigo_conplano
          join contabilidade.pcasp on pcasp.id = pcaspconplano.pcasp_id
          where uniao is false
          group by pcasp.conta, pcasp.nome, pcasp.indicador, b.exercicio, b.classe, b.instituicao, b.id_recurso,
                   b.siconfi, b.gestao, b.subrecurso, b.complemento
      ) select * from plano_estadual
    ) loop

        estrutural = recordDados.conta;
        exercicio = recordDados.exercicio;
        indicador_superavit = recordDados.indicador;
        codigos_conplano = recordDados.codigos_conplano;
        reduzidos = recordDados.reduzidos;
        classe = recordDados.classe;
        nome = recordDados.nome;
        instituicao = recordDados.instituicao;
        id_recurso = recordDados.id_recurso;
        siconfi = recordDados.siconfi;
        gestao = recordDados.gestao;
        subrecurso = recordDados.subrecurso;
        complemento = recordDados.complemento;
        saldo_anterior = recordDados.saldo_anterior;
        saldo_debito = recordDados.saldo_debito;
        saldo_credito = recordDados.saldo_credito;
        saldo_final = saldo_anterior + saldo_debito + saldo_credito;
        sinal_anterior = fc_sinal_saldo_conta(classe, saldo_anterior);
        sinal_final = fc_sinal_saldo_conta(classe, saldo_final);

    return next;
end loop;
    end;
$$;
SQL
        );
    }
}
