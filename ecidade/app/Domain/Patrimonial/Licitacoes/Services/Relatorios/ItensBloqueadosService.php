<?php

namespace App\Domain\Patrimonial\Licitacoes\Services\Relatorios;

use Exception;
use DBDate;
use Illuminate\Support\Facades\DB;

class ItensBloqueadosService
{
    public function buscarRegistrosDePreco()
    {
        $dtDia = DBDate::now();
        $sql = "select
                    distinct l21_codliclicita,
                    pc54_solicita,
                    l20_tipojulg,
                    liclicita.l20_edital,
                    l20_anousu,
                    liclicita.l20_codtipocom,
                    pctipocompra.pc50_descr,
                    liclicita.l20_numero,
                    liclicita.l20_id_usucria,
                    liclicita.l20_datacria,
                    liclicita.l20_horacria,
                    liclicita.l20_dataaber,
                    liclicita.l20_dtpublic,
                    liclicita.l20_horaaber,
                    liclicita.l20_local,
                    liclicita.l20_objeto,
                    l44_sigla,
                    liclicita.l20_licsituacao,
                    licsituacao.l08_descr
                from solicitaregistropreco
                    inner join solicita       on pc54_solicita    = pc10_numero
                    inner join solicitem      on pc10_numero      = pc11_numero
                    inner join pcprocitem     on pc81_solicitem   = pc11_codigo
                    inner join liclicitem     on pc81_codprocitem = l21_codpcprocitem
                    inner join liclicita      on l21_codliclicita = l20_codigo
                    inner join pcorcamitemlic on pc26_liclicitem  = l21_codigo
                    inner join pcorcamitem    on pc26_orcamitem   = pc22_orcamitem
                    inner join cflicita       on cflicita.l03_codigo = liclicita.l20_codtipocom
                    inner join pctipocompratribunal
                    on pctipocompratribunal.l44_sequencial = cflicita.l03_pctipocompratribunal
                    inner join pctipocompra on pctipocompra.pc50_codcom = cflicita.l03_codcom
                    inner join licsituacao  on licsituacao.l08_sequencial = liclicita.l20_licsituacao
                    where cast('{$dtDia}' as date) between pc54_datainicio and pc54_datatermino
                        and l20_licsituacao in (1, 6, 7)
                        order by l21_codliclicita";
        return DB::select($sql);
    }

    public function buscarDadosLicitacao($licitacao)
    {
        $solicita = $this->buscarSolicitacao($licitacao);
        $sql = "SELECT
                    distinct pc11_numero as solicitacao,
                     pc66_justificativa as justificativa,
                     tipomovimentacaoregistropreco.l33_descricao as movimentacao,
                     liclicita.l20_codigo as licitacao,
                     pctipocompra.pc50_descr as modalidade
                FROM registroprecomovimentacaoitens
                        INNER JOIN liclicita
                        ON liclicita.l20_codigo = {$licitacao}
                        INNER JOIN pctipocompra
                        ON pctipocompra.pc50_codcom = liclicita.l20_codtipocom
                        INNER JOIN solicitem
                        ON registroprecomovimentacaoitens.pc66_solicitem = solicitem.pc11_codigo
                        INNER JOIN solicitempcmater
                        ON registroprecomovimentacaoitens.pc66_solicitem = solicitempcmater.pc16_solicitem
                        INNER JOIN pcmater
                        ON solicitempcmater.pc16_codmater = pcmater.pc01_codmater
                        INNER JOIN solicitemunid
                        ON registroprecomovimentacaoitens.pc66_solicitem = solicitemunid.pc17_codigo
                        INNER JOIN matunid
                        ON solicitemunid.pc17_unid = matunid.m61_codmatunid
                        INNER JOIN tipomovimentacaoregistropreco
                        ON tipomovimentacaoregistropreco.l33_sequencial = pc66_tipomovimentacao
                WHERE pc66_registroprecomovimentacao =
                    (
                        SELECT pc58_sequencial
                            FROM registroprecomovimentacao
                        WHERE pc58_solicita = {$solicita}
                        ORDER BY pc58_sequencial DESC
                        LIMIT 1
                    )";

        return DB::select($sql);
    }

    public function buscarSolicitacao($licitacao)
    {
        $sql = "select
                distinct pc54_solicita
            from solicitaregistropreco
                inner join solicita       on pc54_solicita    = pc10_numero
                inner join solicitem      on pc10_numero      = pc11_numero
                inner join pcprocitem     on pc81_solicitem   = pc11_codigo
                inner join liclicitem     on pc81_codprocitem = l21_codpcprocitem
                inner join liclicita      on l21_codliclicita = l20_codigo
                inner join pcorcamitemlic on pc26_liclicitem  = l21_codigo
                inner join pcorcamitem    on pc26_orcamitem   = pc22_orcamitem
                inner join cflicita       on cflicita.l03_codigo = liclicita.l20_codtipocom
                inner join pctipocompratribunal
                on pctipocompratribunal.l44_sequencial = cflicita.l03_pctipocompratribunal
                inner join pctipocompra on pctipocompra.pc50_codcom = cflicita.l03_codcom
                where l21_codliclicita = {$licitacao}";
        $solicita = pg_fetch_assoc(pg_query($sql));
        return $solicita['pc54_solicita'];
    }

    public function buscarItensBloqueados($solicitacao)
    {
        $ultimosMovimentos = array();

        $ultimosMovimentos = DB::table('registroprecomovimentacao')
            ->where('pc58_solicita', '=', "{$solicitacao}")
            ->where('pc58_situacao', '=', 1)
            ->pluck('pc58_sequencial')->each(function ($dados) {
                $retorno = array();
                $retorno[] = $dados;
                return $retorno;
            });
        if (empty($ultimosMovimentos->all())) {
            return;
        }
        // $codigos = implode(",", $ultimosMovimentos->all());
        // dump($ultimosMovimentos);
        // exit;
        $retorno = [];

        foreach ($ultimosMovimentos->all() as $codigo) {
            $sql = "SELECT
                        pcmater.pc01_codmater as codigoMaterial,
                        pcmater.pc01_descrmater as descricaoMaterial,
                        pc66_justificativa as justificativa,
                        solicitem.pc11_quant as quantidadeMaterial,
                        solicitem.pc11_resum as resumoMaterial,
                        matunid.m61_descr as unidadeMaterial,
                        pc66_datainicial as dataInicial,
                        pc66_datafinal as dataFinal,
                        pc66_registroprecomovimentacao as movimentacao
                        FROM registroprecomovimentacaoitens
                        INNER JOIN solicitem
                        ON registroprecomovimentacaoitens.pc66_solicitem = solicitem.pc11_codigo
                        INNER JOIN solicitempcmater
                        ON registroprecomovimentacaoitens.pc66_solicitem = solicitempcmater.pc16_solicitem
                        INNER JOIN pcmater
                        ON solicitempcmater.pc16_codmater = pcmater.pc01_codmater
                        INNER JOIN solicitemunid
                        ON registroprecomovimentacaoitens.pc66_solicitem = solicitemunid.pc17_codigo
                        INNER JOIN matunid
                        ON solicitemunid.pc17_unid = matunid.m61_codmatunid
                        WHERE pc66_registroprecomovimentacao = {$codigo}";

            $resp = array_map(function ($resp) {
                return get_object_vars($resp);
            }, DB::select($sql));
            $retorno[] = $resp;
        }

        return $retorno;
    }

    public function buscarDadosHeadersLicitacao($licitacao)
    {
        $sql = "select
                    distinct pc54_solicita as solicitacao,
                    l20_codigo as licitacao,
                    pc50_descr as modalidade
                from solicitaregistropreco
                    inner join solicita	   	on solicita.pc10_numero 		= solicitaregistropreco.pc54_solicita
                    inner join solicitem   	on solicitem.pc11_numero 		= solicita.pc10_numero
                    inner join pcprocitem  	on pcprocitem.pc81_solicitem   	= solicitem.pc11_codigo
                    inner join liclicitem  	on liclicitem.l21_codpcprocitem = pcprocitem.pc81_codprocitem
                    inner join liclicita   	on liclicita.l20_codigo 		= liclicitem.l21_codliclicita
                    inner join pctipocompra on pctipocompra.pc50_codcom 	= liclicita.l20_codtipocom
                where liclicita.l20_codigo = {$licitacao}";

        return DB::select($sql);
    }
}
