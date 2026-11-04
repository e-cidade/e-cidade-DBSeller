<?php

namespace App\Domain\Patrimonial\Ouvidoria\Services;

use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use Illuminate\Support\Facades\DB;

class FornecedorService
{


    public static function empenhos($cpf_cnpj)
    {
        $cgms = Cgm::where("z01_cgccpf", $cpf_cnpj)->get();

        if ($cgms->isEmpty()) {
            throw new \Exception("CGM não encontrado no cadastro!");
        }

        $numerosCgm = [];
        foreach ($cgms as $cgm) {
            $numerosCgm[] = $cgm->z01_numcgm;
        }

        $sql = "
     SELECT
        DISTINCT e60_numemp as empenho_codigo,
        e60_codemp as empenho_numero,
        e60_anousu as empenho_exercicio,
        e60_emiss data_emissao,
        z01_cgccpf AS cpf_cnpj,
        e60_vlremp as valor,
        e60_vlranu as valor_anulado,
        e60_vlrliq as valor_liquidado ,
        e60_vlrpag as valor_pago,
        round(e60_vlrliq-e60_vlrpag, 2)::float8 AS saldo_liquidado,
        round(e60_vlremp-e60_vlranu-e60_vlrpag, 2)::float8 AS saldo,
        e60_resumo AS observacao,
   COALESCE(
    (
         SELECT
             CONCAT(
                 trim(licitacao.cflicita.l03_descr),
                 ' - ',
                 liclicita.l20_numero,
                 '/',
                 liclicita.l20_anousu
            ) as licitacao
        FROM
            empautitem
        INNER  JOIN empautitempcprocitem on
            empautitempcprocitem.e73_sequen = empautitem.e55_sequen
            and empautitempcprocitem.e73_autori = empautitem.e55_autori
        INNER  JOIN liclicitem on
            liclicitem.l21_codpcprocitem = empautitempcprocitem.e73_pcprocitem
        INNER  JOIN liclicita on
            liclicitem.l21_codliclicita = liclicita.l20_codigo
        INNER  JOIN cflicita on
            liclicita.l20_codtipocom = cflicita.l03_codigo
        left join empautoriza on
            empautoriza.e54_autori = empautitem.e55_autori
        left join empenho.empempaut on
            empenho.empempaut.e61_autori = empautoriza.e54_autori
        where
               e61_numemp = empenho.empempenho.e60_numemp
               limit 1
    ),
    (
          SELECT
            DISTINCT
                              CONCAT(trim(licitacao.cflicita.l03_descr),
            ' - ',
            liclicita.l20_numero,
            '/',
            liclicita.l20_anousu ) as licitacao
        FROM
            acordo
        left join acordoempautoriza on
            acordo.ac16_sequencial = acordoempautoriza.ac45_acordo
        left join empautoriza on
            acordoempautoriza.ac45_empautoriza = empautoriza.e54_autori
        left join empempaut on
            empautoriza.e54_autori = empempaut.e61_autori
        INNER  JOIN empempenhocontrato on
            acordo.ac16_sequencial = empempenhocontrato.e100_acordo
        INNER  JOIN acordoposicao on
            acordoposicao.ac26_acordo = acordo.ac16_sequencial
        INNER  JOIN acordoitem on
            acordoitem.ac20_acordoposicao = acordoposicao.ac26_sequencial
        INNER  JOIN acordoliclicitem on
            acordoliclicitem.ac24_acordoitem = acordoitem.ac20_sequencial
        INNER  JOIN liclicitem on
            liclicitem.l21_codigo = acordoliclicitem.ac24_liclicitem
        INNER  JOIN liclicita on
            liclicita.l20_codigo = liclicitem.l21_codliclicita
        INNER  JOIN cflicita on
            liclicita.l20_codtipocom = cflicita.l03_codigo
        where
            e100_numemp = empenho.empempenho.e60_numemp
            limit 1
	)
)  AS licitacao,
                 (
                     SELECT
                    DISTINCT
                    CONCAT(ac16_numero,'/',ac16_anousu) as contrato
                    FROM acordo
                    left join acordoempautoriza on acordo.ac16_sequencial = acordoempautoriza.ac45_acordo
                    left join empautoriza       on acordoempautoriza.ac45_empautoriza = empautoriza.e54_autori
                    left join empempaut         on empautoriza.e54_autori = empempaut.e61_autori
                    INNER  JOIN empempenhocontrato on acordo.ac16_sequencial = empempenhocontrato.e100_acordo
                    where e100_numemp = empempenho.e60_numemp
                 ) as contrato
            FROM
                empempenho
            INNER  JOIN cgm ON
                cgm.z01_numcgm = empempenho.e60_numcgm
            INNER  JOIN db_config ON
                db_config.codigo = empempenho.e60_instit
            INNER  JOIN orcdotacao ON
                orcdotacao.o58_anousu = empempenho.e60_anousu
                AND orcdotacao.o58_coddot = empempenho.e60_coddot
            INNER  JOIN pctipocompra ON
                pctipocompra.pc50_codcom = empempenho.e60_codcom
            INNER  JOIN emptipo ON
                emptipo.e41_codtipo = empempenho.e60_codtipo
            INNER  JOIN concarpeculiar ON
                concarpeculiar.c58_sequencial = empempenho.e60_concarpeculiar
            INNER  JOIN db_config AS a ON
                a.codigo = orcdotacao.o58_instit
            INNER  JOIN orctiporec ON
                orctiporec.o15_codigo = orcdotacao.o58_codigo
            INNER  JOIN orcfuncao ON
                orcfuncao.o52_funcao = orcdotacao.o58_funcao
            INNER  JOIN orcsubfuncao ON
                orcsubfuncao.o53_subfuncao = orcdotacao.o58_subfuncao
            INNER  JOIN orcprograma ON
                orcprograma.o54_anousu = orcdotacao.o58_anousu
                AND orcprograma.o54_programa = orcdotacao.o58_programa
            INNER  JOIN orcelemento ON
                orcelemento.o56_codele = orcdotacao.o58_codele
                AND orcelemento.o56_anousu = orcdotacao.o58_anousu
            INNER  JOIN orcprojativ ON
                orcprojativ.o55_anousu = orcdotacao.o58_anousu
                AND orcprojativ.o55_projativ = orcdotacao.o58_projativ
            INNER  JOIN orcorgao ON
                orcorgao.o40_anousu = orcdotacao.o58_anousu
                AND orcorgao.o40_orgao = orcdotacao.o58_orgao
            INNER  JOIN orcunidade ON
                orcunidade.o41_anousu = orcdotacao.o58_anousu
                AND orcunidade.o41_orgao = orcdotacao.o58_orgao
                AND orcunidade.o41_unidade = orcdotacao.o58_unidade
            WHERE
                 e60_numcgm  IN (" . join(",", $numerosCgm) . ")
            ORDER BY
                e60_numemp DESC;
";
        return DB::select($sql);
    }

    public static function notasByCpfCnpj($cpf_cnpj)
    {
        $cgms = Cgm::where("z01_cgccpf", $cpf_cnpj)->get();

        if ($cgms->isEmpty()) {
            throw new \Exception("CGM não encontrado no cadastro!");
        }

        $numerosCgm = [];
        foreach ($cgms as $cgm) {
            $numerosCgm[] = $cgm->z01_numcgm;
        }

        $sql = "SELECT
                empenho.empempenho.e60_numemp as empenho_codigo,
                empenho.empempenho.e60_codemp as empenho_numero,
                empenho.empnota.e69_codnota as codigo,
                empenho.empnota.e69_numero as numero,
                empenho.empnota.e69_dtnota as data,
                empenho.empnota.e69_dtinclusao as data_inclusao,
                empenho.empnota.e69_dtservidor as data_servidor,
                empenho.empnotaele.e70_valor as valor,
                empenho.empnotaele.e70_vlrliq as valor_liquidado,
                empenho.empnotaele.e70_vlranu as valor_anulado,
                empenho.pagordemele.e53_vlrpag as valor_pago,
                empenho.pagordemnota.e71_anulado as anulado,
                empenho.empnota.e69_anousu  as exercicio,
                (
                SELECT sum(empenho.retencaoreceitas.e23_valorretencao) FROM
                        empenho.retencaopagordem
                    INNER  JOIN  empenho.retencaoreceitas
                        on  empenho.retencaoreceitas.e23_retencaopagordem  =  empenho.retencaopagordem.e20_sequencial
                    where empenho.retencaopagordem.e20_pagordem = empenho.pagordem.e50_codord
    and  empenho.retencaoreceitas.e23_ativo is true
                ) as retencao,
            e50_codord as ordem_pagamento
            FROM
                empenho.empnota
            INNER  JOIN empenho.empempenho on
                empenho.empempenho.e60_numemp = empenho.empnota.e69_numemp
            INNER  JOIN empenho.empnotaele on
                empenho.empnotaele.e70_codnota = empenho.empnota.e69_codnota
            INNER  JOIN empenho.pagordemnota on
                empenho.pagordemnota.e71_codnota = empenho.empnota.e69_codnota
            INNER  JOIN empenho.pagordem on
                empenho.pagordem.e50_codord = empenho.pagordemnota.e71_codord
            left join protocolo.cgm on
                protocolo.cgm.z01_numcgm = empenho.empempenho.e60_numcgm
            left join empenho.pagordemele  on  empenho.pagordemele.e53_codord =  empenho.pagordemnota.e71_codord
            where
                empenho.empempenho.e60_numcgm  IN (" . join(",", $numerosCgm) . ")
            order by
                1 asc;
";
        return DB::select($sql);
    }

    public static function notasHistorico($codigoOrdemPagamento)
    {
        $sql = "
                SELECT
            c53_descr as descricao,
            c70_valor as valor,
            c80_data as data
        FROM
            pagordem
        INNER  JOIN conlancamord on
            c80_codord = pagordem.e50_codord
        INNER  JOIN conlancam on
            c70_codlan = conlancamord.c80_codlan
        INNER  JOIN conlancamdoc on
            c71_codlan = conlancam.c70_codlan
        INNER  JOIN conhistdoc on
            conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
        where
            pagordem.e50_codord = {$codigoOrdemPagamento}
        order by
            conhistdoc;
        ";
        return DB::select($sql);
    }
}
