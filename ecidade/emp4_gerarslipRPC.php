<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification(Modification::getFile('model/agendaPagamento.model.php')));
require_once(modification("model/slip.model.php"));
require_once(modification("model/caixa/slip/TransferenciaFactory.model.php"));
require_once(modification("model/caixa/slip/Transferencia.model.php"));
require_once(modification("model/caixa/slip/Transferencia.model.php"));
require_once(modification("model/contabilidade/lancamento/LancamentoAuxiliarSlip.model.php"));
require_once(modification("interfaces/ILancamentoAuxiliar.interface.php"));
require_once(modification("interfaces/IRegraLancamentoContabil.interface.php"));
use ECidade\Financeiro\Orcamento\Recurso\Recurso;

db_app::import("CgmFactory");
db_app::import("MaterialCompras");
db_app::import("configuracao.*");
db_app::import("contabilidade.*");
db_app::import("contabilidade.lancamento.*");
db_app::import("Dotacao");
db_app::import("empenho.*");
db_app::import("exceptions.*");

$oJson = new services_json();
$oParam = $oJson->decode(str_replace("\\", "", $_POST["json"]));
$oRetorno = new stdClass();
$oRetorno->status = 1;
$oRetorno->message = "";
$exercicio = db_getsession("DB_anousu");

/**
 * Criado em carater de expedite, para resolver o problema do cliente.
 * Quando resolvermos o problema real, essa função é única que deve existir.
 *
 * @param $oParam
 * @param $exercicio
 * @param $instituicao
 * @param $parametroCaixa
 * @return stdClass[]
 */
function getRetencoesComApropriacao($oParam, $exercicio, $instituicao, $parametroCaixa)
{
    $sJoin = "";
    $sWhere = "";

    if ($oParam->isFolha) {
        $sJoin = " inner join rhempenhofolhaempenho    on rh76_numemp         = e60_numemp      ";
        $sJoin .= " inner join rhempenhofolha           on rh76_rhempenhofolha = rh72_sequencial ";

        $sWhere = " and rh72_mesusu      = {$oParam->paramFolha->iMesFolha}";
        $sWhere .= " and rh72_anousu      = {$oParam->paramFolha->iAnoFolha}";
        $sWhere .= " and rh72_tipoempenho = 1";
        $sWhere .= " and rh72_siglaarq    = '{$oParam->paramFolha->sSigla}'";
        if ($oParam->paramFolha->sSigla <> 'r20') {
            $sWhere .= " and rh72_seqcompl    = {$oParam->paramFolha->sSemestre}";
        }
        $sWhere .= " and e21_retencaotiporecgrupo  = 2";
    } else {
        $sWhere .= " and e21_retencaotiporecgrupo  = 1";
        if ($oParam->dtIni != "" && $oParam->dtFim == "") {
            $sWhere .= " and corrente.k12_data >= '" . implode("-", array_reverse(explode("/", $oParam->dtIni))) . "'";
        } elseif ($oParam->dtIni != "" && $oParam->dtFim != "") {
            $dtDataIni = implode("-", array_reverse(explode("/", $oParam->dtIni)));
            $dtDataFim = implode("-", array_reverse(explode("/", $oParam->dtFim)));
            $sWhere .= " and corrente.k12_data between '{$dtDataIni}' and '{$dtDataFim}'";
        } elseif ($oParam->dtIni == "" && $oParam->dtFim != "") {
            $dtDataFim = implode("-", array_reverse(explode("/", $oParam->dtFim)));
            $sWhere .= " and corrente.k12_data <= '{$dtDataFim}'";
        }
    }

    /**
     * Busca CGM vinculada a retenção
     */
    if ($oParam->iNumCgm != '') {
        $sWhere .= " and cgm_credor.z01_numcgm = {$oParam->iNumCgm} ";
    }

    if (!empty($oParam->iRecurso)) {
        $sWhere .= " and k00_recurso = {$oParam->iRecurso}";
    }

    if (!empty($oParam->idsRecursos)) {
        $sWhere .= " and o15_codigo in ($oParam->idsRecursos)";
    }

    if ($oParam->iReceita != "") {
        $sWhere .= " and tabrec.k02_codigo = {$oParam->iReceita}";
    }

    if (!empty($oParam->ordemPag)) {
        $sWhere .= " and pagordem.e50_codord = {$oParam->ordemPag}";
    }

    /**
     * Adicionado essa condição para resolver um problema na estrutura de retenção onde não temos como saber
     * qual movimento pertence à receita da retenção (retencaoreceitas).
     * Apenas com apropriação ligada a tabela empagemovslips grava no campo k107_retencao o id da retencaoreceitas.
     *
     * @todo vou manter esse if dentro dessa função pois quando o problema de registrar a tabela empagemovslips
     * for resolvido, essa função deve existir exatamente dessa forma para clientes com ou sem o parâmetro de
     * apropriação.
     */
    if (APROPRIACAO_RETENCAO) {
        $sWhere .= " and e23_sequencial = k107_retencao";
    }

    /* [Extensão] Filtro da Despesa */

    $sSqlArrecadacoesExtra = "DROP TABLE IF EXISTS tmp_gerar_slip; ";
    $sSqlArrecadacoesExtra .= "CREATE TEMPORARY TABLE tmp_gerar_slip as ";
    $sSqlArrecadacoesExtra .= "
                SELECT cornump.k12_numpre, corrente.k12_valor,
                       k13_conta AS credito,
                       cc.c60_descr AS descrcredito,
                       k02_reduz AS debito,
                       cd.c60_descr AS descrdebito,
                       e60_numemp,
                       o15_codigo as k00_recurso,
                       gestao,
                       o15_recurso,
                       o15_complemento,
                       cgm_credor.z01_nome,
                       cgm_credor.z01_numcgm,
                       e50_codord,
                       tabrec.k02_codigo,
                       e23_sequencial,
                       k107_sequencial,
                       k02_drecei
                from retencaoreceitas
                join retencaocorgrupocorrente on e47_retencaoreceita = e23_sequencial
                join corgrupocorrente on k105_sequencial = e47_corgrupocorrente
                join corrente on k105_data = corrente.k12_data
                     and k105_id = corrente.k12_id
                     and k105_autent = k12_autent
                join cornump on corrente.k12_data = cornump.k12_data
                     and corrente.k12_id = cornump.k12_id
                     and corrente.k12_autent = cornump.k12_autent
                join retencaotiporec on e23_retencaotiporec = e21_sequencial
                join retencaotiporeccgm on retencaotiporeccgm.e48_retencaotiporec = retencaotiporec.e21_sequencial
                join cgm as cgm_credor on cgm_credor.z01_numcgm = retencaotiporeccgm.e48_cgm
                join tabrec on e21_receita = tabrec.k02_codigo
                join tabplan on tabrec.k02_codigo = tabplan.k02_codigo
                     and tabplan.k02_anousu = {$exercicio}
                join retencaopagordem on e20_sequencial = e23_retencaopagordem
                join pagordem on e50_codord = e20_pagordem
                left join pagordemconta on e50_codord = e49_codord
                join empempenho on e50_numemp = e60_numemp
                join cgm on cgm.z01_numcgm = e60_numcgm
                left join conplanoreduz rd on rd.c61_reduz = k02_reduz
                     and tabplan.k02_anousu = rd.c61_anousu
                left join conplano cd on cd.c60_codcon = rd.c61_codcon
                     and cd.c60_anousu = rd.c61_anousu
                join orcdotacao on e60_coddot = o58_coddot
                     and e60_anousu = o58_anousu
                join empord on e82_codord = e50_codord
                join caixa.empagemovslips on empagemovslips.k107_empagemov = empord.e82_codmov
                join empagemov on e81_codmov = k107_empagemov
                join empagepag on e85_codmov = empagemov.e81_codmov
                join empagetipo on empagetipo.e83_codtipo = empagepag.e85_codtipo
                join saltes on saltes.k13_conta = empagetipo.e83_conta
                join conplanoreduz rc on rc.c61_anousu = tabplan.k02_anousu
                     and rc.c61_reduz = saltes.k13_reduz
                join conplano cc on cc.c60_codcon = rc.c61_codcon
                     and cc.c60_anousu = rc.c61_anousu
                join orctiporec on orctiporec.o15_codigo = rc.c61_codigo
                join fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
                     and fonterecurso.exercicio = rc.c61_anousu
                {$sJoin}
              where e23_recolhido is true
                and k02_tipo = 'E'
                and corrente.k12_instit = {$instituicao}
                and not exists(
                  select *
                    from slipcorrente where k112_data = corrente.k12_data
                     and k112_id = corrente.k12_id
                     and k112_autent = corrente.k12_autent
                     and k112_ativo is true
                )
                and (k12_estorn is false)
                and (e23_ativo is true)
               {$sWhere}
               order by e21_receita, k00_recurso;
            ";

    $sSqlArrecadacoesExtra .= " ALTER TABLE tmp_gerar_slip ADD COLUMN k13_conta VARCHAR; ";
    $sSqlArrecadacoesExtra .= " ALTER TABLE tmp_gerar_slip ADD COLUMN k13_descr VARCHAR; ";
    $sSqlArrecadacoesExtra .= " SELECT * FROM tmp_gerar_slip";
    $rsArrecadacaoExtra = db_query($sSqlArrecadacoesExtra);


    $sql = "SELECT * FROM tmp_gerar_slip ";
    $res = db_query($sql);
    $arrecadacoes = db_utils::getCollectionByRecord($res, false, false, true);

    /**
     * Se o sistema estiver configurado para usar a conta extra devemos buscar a conta extra da conta pagadora
     * Parâmetros: Utiliza Conta Extra Orçamentária: SIM
     */
    if ($parametroCaixa->k29_utilizarcontaextra == 't') {
        foreach ($arrecadacoes as $index => $arrecadacao) {
            $sql = "
                    select k109_contaextra AS credito,
                           cc.c60_descr AS descrcredito,
                           o15_codigo as k00_recurso,
                           gestao,
                           o15_recurso,
                           o15_complemento
                      from saltesextra
                      join conplanoreduz rc on rc.c61_anousu = {$exercicio}
                              and rc.c61_reduz = saltesextra.k109_contaextra
                      join conplano cc on cc.c60_codcon = rc.c61_codcon
                              and cc.c60_anousu = rc.c61_anousu
                      join orctiporec on orctiporec.o15_codigo = rc.c61_codigo
                      join fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
                              and fonterecurso.exercicio = rc.c61_anousu
                    where saltesextra.k109_saltes = {$arrecadacao->credito}
                    ";

            $rs = db_query($sql);

            if (pg_num_rows($rs) === 0) {
                unset($arrecadacoes[$index]);
                continue;
            }
            $contaExtra = db_utils::fieldsMemory($rs, 0);

            $arrecadacao->credito = $contaExtra->credito;
            $arrecadacao->descrcredito = $contaExtra->descrcredito;
            $arrecadacao->k00_recurso = $contaExtra->k00_recurso;
            $arrecadacao->gestao = $contaExtra->gestao;
            $arrecadacao->o15_recurso = $contaExtra->o15_recurso;
            $arrecadacao->o15_complemento = $contaExtra->o15_complemento;
        }
    }
    return $arrecadacoes;
}


/**
 * @todo função criada para não termos que realizar uma migração das retenções já lançadas no sistema.
 * @todo essa função deve ser removida do sistema em 2024/2025
 *
 * @param $oParam
 * @param $exercicio
 * @param $instituicao
 * @param $parametroCaixa
 * @return stdClass[]
 */
function getRetencoesSemApropriacao($oParam, $exercicio, $instituicao, $parametroCaixa)
{
    $sJoin = "";
    $sWhere = "";

    if ($oParam->isFolha) {
        $sJoin = " inner join rhempenhofolhaempenho    on rh76_numemp         = e60_numemp      ";
        $sJoin .= " inner join rhempenhofolha           on rh76_rhempenhofolha = rh72_sequencial ";

        $sWhere = " and rh72_mesusu      = {$oParam->paramFolha->iMesFolha}";
        $sWhere .= " and rh72_anousu      = {$oParam->paramFolha->iAnoFolha}";
        $sWhere .= " and rh72_tipoempenho = 1";
        $sWhere .= " and rh72_siglaarq    = '{$oParam->paramFolha->sSigla}'";
        if ($oParam->paramFolha->sSigla <> 'r20') {
            $sWhere .= " and rh72_seqcompl    = {$oParam->paramFolha->sSemestre}";
        }
        $sWhere .= " and e21_retencaotiporecgrupo  = 2";
    } else {
        $sWhere .= " and e21_retencaotiporecgrupo  = 1";
        if ($oParam->dtIni != "" && $oParam->dtFim == "") {
            $sWhere .= " and corrente.k12_data >= '" . implode("-", array_reverse(explode("/", $oParam->dtIni))) . "'";
        } elseif ($oParam->dtIni != "" && $oParam->dtFim != "") {
            $dtDataIni = implode("-", array_reverse(explode("/", $oParam->dtIni)));
            $dtDataFim = implode("-", array_reverse(explode("/", $oParam->dtFim)));
            $sWhere .= " and corrente.k12_data between '{$dtDataIni}' and '{$dtDataFim}'";
        } elseif ($oParam->dtIni == "" && $oParam->dtFim != "") {
            $dtDataFim = implode("-", array_reverse(explode("/", $oParam->dtFim)));
            $sWhere .= " and corrente.k12_data <= '{$dtDataFim}'";
        }
    }

    /**
     * Busca CGM vinculada a retenção
     */
    if ($oParam->iNumCgm != '') {
        $sWhere .= " and cgm_credor.z01_numcgm = {$oParam->iNumCgm} ";
    }

    if (!empty($oParam->iRecurso)) {
        $sWhere .= " and k00_recurso = {$oParam->iRecurso}";
    }

    if (!empty($oParam->idsRecursos)) {
        $sWhere .= " and o15_codigo in ($oParam->idsRecursos)";
    }

    if ($oParam->iReceita != "") {
        $sWhere .= " and tabrec.k02_codigo = {$oParam->iReceita}";
    }

    if (!empty($oParam->ordemPag)) {
        $sWhere .= " and pagordem.e50_codord = {$oParam->ordemPag}";
    }

    /* [Extensão] Filtro da Despesa */

    $sSqlArrecadacoesExtra = "DROP TABLE IF EXISTS tmp_gerar_slip; ";
    $sSqlArrecadacoesExtra .= "CREATE TEMPORARY TABLE tmp_gerar_slip as ";
    $sSqlArrecadacoesExtra .= "
                SELECT cornump.k12_numpre, corrente.k12_valor,
                       k13_conta AS credito,
                       cc.c60_descr AS descrcredito,
                       k02_reduz AS debito,
                       cd.c60_descr AS descrdebito,
                       e60_numemp,
                       o15_codigo as k00_recurso,
                       gestao,
                       o15_recurso,
                       o15_complemento,
                       cgm_credor.z01_nome,
                       cgm_credor.z01_numcgm,
                       e50_codord,
                       tabrec.k02_codigo,
                       e23_sequencial,
                       k107_sequencial,
                       k02_drecei
                from retencaoreceitas
                join retencaocorgrupocorrente on e47_retencaoreceita = e23_sequencial
                join corgrupocorrente on k105_sequencial = e47_corgrupocorrente
                join corrente on k105_data = corrente.k12_data
                     and k105_id = corrente.k12_id
                     and k105_autent = k12_autent
                join cornump on corrente.k12_data = cornump.k12_data
                     and corrente.k12_id = cornump.k12_id
                     and corrente.k12_autent = cornump.k12_autent
                join retencaotiporec on e23_retencaotiporec = e21_sequencial
                left join caixa.empagemovslips on e23_sequencial = k107_retencao
                join retencaotiporeccgm on retencaotiporeccgm.e48_retencaotiporec = retencaotiporec.e21_sequencial
                join cgm as cgm_credor on cgm_credor.z01_numcgm = retencaotiporeccgm.e48_cgm
                join tabrec on e21_receita = tabrec.k02_codigo
                join tabplan on tabrec.k02_codigo = tabplan.k02_codigo
                     and tabplan.k02_anousu = {$exercicio}
                join retencaopagordem on e20_sequencial = e23_retencaopagordem
                join pagordem on e50_codord = e20_pagordem
                left join pagordemconta on e50_codord = e49_codord
                join empempenho on e50_numemp = e60_numemp
                join cgm on cgm.z01_numcgm = e60_numcgm
                left join conplanoreduz rd on rd.c61_reduz = k02_reduz
                     and tabplan.k02_anousu = rd.c61_anousu
                left join conplano cd on cd.c60_codcon = rd.c61_codcon
                     and cd.c60_anousu = rd.c61_anousu
                join orcdotacao on e60_coddot = o58_coddot
                     and e60_anousu = o58_anousu

                join empord on e82_codord = e50_codord
                join empagemov on e81_codmov = e82_codmov
                join empagepag on e85_codmov = empagemov.e81_codmov
                join empagetipo on empagetipo.e83_codtipo = empagepag.e85_codtipo
                join saltes on saltes.k13_conta = empagetipo.e83_conta
                join conplanoreduz rc on rc.c61_anousu = tabplan.k02_anousu
                     and rc.c61_reduz = saltes.k13_reduz
                join conplano cc on cc.c60_codcon = rc.c61_codcon
                     and cc.c60_anousu = rc.c61_anousu
                join orctiporec on orctiporec.o15_codigo = rc.c61_codigo
                join fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
                     and fonterecurso.exercicio = rc.c61_anousu
                {$sJoin}
              where e23_recolhido is true
                and k02_tipo = 'E' and corrente.k12_instit = {$instituicao}
                and not exists(
                      select *
                        from slipcorrente where k112_data = corrente.k12_data
                         and k112_id = corrente.k12_id
                         and k112_autent = corrente.k12_autent
                         and k112_ativo is true
                )
                and (k12_estorn is false)
                and (e23_ativo is true)
               {$sWhere}
               order by e21_receita, k00_recurso;
            ";

    $sSqlArrecadacoesExtra .= " ALTER TABLE tmp_gerar_slip ADD COLUMN k13_conta VARCHAR; ";
    $sSqlArrecadacoesExtra .= " ALTER TABLE tmp_gerar_slip ADD COLUMN k13_descr VARCHAR; ";
    $sSqlArrecadacoesExtra .= " SELECT * FROM tmp_gerar_slip";
    $rsArrecadacaoExtra = db_query($sSqlArrecadacoesExtra);

    $sql = "SELECT * FROM tmp_gerar_slip ";
    $res = db_query($sql);
    $arrecadacoes = db_utils::getCollectionByRecord($res, false, false, true);

    /**
     * Se o sistema estiver configurado para usar a conta extra devemos buscar a conta extra da conta pagadora
     * Parâmetros: Utiliza Conta Extra Orçamentária: SIM
     */
    if ($parametroCaixa->k29_utilizarcontaextra == 't') {
        foreach ($arrecadacoes as $index => $arrecadacao) {
            $sql = "
                    select k109_contaextra AS credito,
                           cc.c60_descr AS descrcredito,
                           o15_codigo as k00_recurso,
                           gestao,
                           o15_recurso,
                           o15_complemento
                      from saltesextra
                      join conplanoreduz rc on rc.c61_anousu = {$exercicio}
                              and rc.c61_reduz = saltesextra.k109_contaextra
                      join conplano cc on cc.c60_codcon = rc.c61_codcon
                              and cc.c60_anousu = rc.c61_anousu
                      join orctiporec on orctiporec.o15_codigo = rc.c61_codigo
                      join fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
                              and fonterecurso.exercicio = rc.c61_anousu
                    where saltesextra.k109_saltes = {$arrecadacao->credito}
                    ";

            $rs = db_query($sql);

            if (pg_num_rows($rs) === 0) {
                unset($arrecadacoes[$index]);
                continue;
            }
            $contaExtra = db_utils::fieldsMemory($rs, 0);

            $arrecadacao->credito = $contaExtra->credito;
            $arrecadacao->descrcredito = $contaExtra->descrcredito;
            $arrecadacao->k00_recurso = $contaExtra->k00_recurso;
            $arrecadacao->gestao = $contaExtra->gestao;
            $arrecadacao->o15_recurso = $contaExtra->o15_recurso;
            $arrecadacao->o15_complemento = $contaExtra->o15_complemento;
        }
    }
    return $arrecadacoes;
}

try {
    $instituicao = db_getsession("DB_instit");
    $dataSessao = date("Y-m-d", db_getsession("DB_datausu"));

    $dao = new cl_caiparametro();
    $sql = $dao->sql_query_file($instituicao, 'k29_utilizarcontaextra');
    $rs = db_query($sql);

    if (!$rs || pg_num_rows($rs) == 0) {
        $mgs = "Acesse: DB:FINANCEIRO > Tesouraria > Procedimentos > Parâmetros > Financeiro ";
        $mgs .= "e configure os parâmetros";
        throw new Exception($mgs);
    }

    $parametroCaixa = db_utils::fieldsMemory($rs, 0);

    switch ($oParam->exec) {
        case 'getContas':
            $dao = new cl_saltes();
            $campos = "distinct k13_conta as conta, k13_descr as nome";
            if ($parametroCaixa->k29_utilizarcontaextra == 't') {
                $dao = new cl_saltesextra();
                $campos = "distinct k109_contaextra AS conta, c60_descr AS nome";
            }

            $where = [
                "c61_instit = {$instituicao}",
                "c61_anousu = {$exercicio}",
                "c60_codsis in (5,6) ",
                "(k13_limite is null or k13_limite > '{$dataSessao}')",
            ];

            $sqlContas = $dao->sqlContas(
                $campos,
                $where
            );

            $rsContas = db_query($sqlContas);
            if (!$rsContas || pg_num_rows($rsContas) === 0) {
                throw new Exception("Não foi encontrado contas para essa instituição.");
            }

            $oRetorno->contas = db_utils::getCollectionByRecord($rsContas, false, false, true);
            echo $oJson->encode($oRetorno);

            break;

        case "getMovimentos":
            $oAgendaPagamento = new agendaPagamento();
            $oAgendaPagamento->setUrlEncode(true);
            $oRetorno = new stdClass();
            $oRetorno->status = 1;
            $oRetorno->message = "";
            $aSlips = $oAgendaPagamento->getMovimentosSlip(
                $oParam->options->dtIni,
                $oParam->options->dtFim,
                $oParam->options->agrupar,
                $oParam->options->codigoordem
            );
            if (count($aSlips) > 0) {
                $oRetorno->aSlips = $aSlips;
                $oRetorno->agrupar = $oParam->options->agrupar;
            } else {
                $oRetorno->status = 2;
                $oRetorno->message = urlencode("Não foi encontrados slips");
            }
            echo $oJson->encode($oRetorno);
            break;

        case "gerarSlips":
            db_inicio_transacao();
            $oRetorno = new stdClass();
            $oRetorno->status = 1;
            $oRetorno->message = "";
            $aSlipsRetorno = array();
            try {
                $oAgendaPagamento = new agendaPagamento();
                $oAgendaPagamento->setUrlEncode(true);
                if (count($oParam->options->aSlips) > 0) {
                    foreach ($oParam->options->aSlips as $oSlip) {
                        $iSlip = $oAgendaPagamento->gerarSlip(
                            $oSlip->iCtaDebito,
                            $oSlip->iCtaCredito,
                            $oSlip->nValor,
                            $oParam->options->dtIni,
                            $oParam->options->dtFim,
                            false,
                            $oParam->options->agrupar,
                            $oSlip->iCodigoOrdem
                        );
                        $aSlipsRetorno[] = $iSlip;
                    }
                }

                /**
                 * Vinculamos o slip gerado ao tipo Transferencia Financeira Recebimento
                 */
                if (USE_PCASP) {
                    $tipoOperacao = 5;
                    if (!empty($oParam->tipoOperacao)) {
                        $tipoOperacao = $oParam->tipoOperacao;
                    }

                    foreach ($aSlipsRetorno as $iCodigoSlip) {
                        $oDaoTipoOperacaoVinculo = new cl_sliptipooperacaovinculo;
                        $oDaoTipoOperacaoVinculo->k153_slip = $iCodigoSlip;
                        $oDaoTipoOperacaoVinculo->k153_slipoperacaotipo = $tipoOperacao;
                        $oDaoTipoOperacaoVinculo->incluir($iCodigoSlip);

                        if ($oDaoTipoOperacaoVinculo->erro_status == 0) {
                            $sMensagemErro = "Não foi possível víncular o tipo de slip ao slip.\n\n";
                            $sMensagemErro .= "Erro Técnico: {$oDaoTipoOperacaoVinculo->erro_msg}";
                            throw new Exception($sMensagemErro);
                        }
                    }
                }
                $oRetorno->aSlipsRetorno = $aSlipsRetorno;
                db_fim_transacao(false);
            } catch (Exception $eErro) {
                $oRetorno->status = 2;
                $oRetorno->message = urlencode($eErro->getMessage());
                db_fim_transacao(true);
            }
            echo $oJson->encode($oRetorno);
            break;

        case "getArrecExtra":

            /**
             * @todo esse if foi criado como uma gambiarra para resolver um expedite dos clientes.
             * identificamos um bug no sistema onde não grava registros na tabela empagemovslips se a conta não tem
             * contrapartida.
             */
            if (APROPRIACAO_RETENCAO) {
                $arrecadacoes = getRetencoesComApropriacao($oParam, $exercicio, $instituicao, $parametroCaixa);

            } else {
                $arrecadacoes = getRetencoesSemApropriacao($oParam, $exercicio, $instituicao, $parametroCaixa);
            }

            $oRetorno->itens = array_values($arrecadacoes);
            echo $oJson->encode($oRetorno);
            break;

        case "gerarSlipsExtra":
            /**
             * Percorremos as arrecadacoes e  agrupamos por cta credito, ctadebito, recurso
             * cada grupo ira compor um slip
             */
            db_inicio_transacao();
            try {
                $oDaoOPAuxiliar = new cl_empageordem();
                $oDaoOPAuxiliar->e42_dtpagamento = $dataSessao;
                $oDaoOPAuxiliar->incluir(null);
                $aSlips = array();
                require_once(modification("model/slip.model.php"));
                foreach ($oParam->aSlips as $oArrecadacao) {

                    $notaLiquidacao = NotaLiquidacao::getInstancePorCodOrd($oArrecadacao->iOrdem);
                    $sIndex = $oArrecadacao->iCtaCredito . $oArrecadacao->iCtaDebito . $oArrecadacao->iRecurso;
                    $sIndex .= $oArrecadacao->iCGM;

                    $rec = new Recurso($oArrecadacao->iRecurso);
                    $gestaoRecurso = $rec->getFonteRecurso($exercicio)->gestao;

                    if (isset($aSlips[$sIndex])) {
                        $aSlips[$sIndex]->addRecurso($oArrecadacao->iRecurso, $oArrecadacao->nValor);
                        $aSlips[$sIndex]->setValor($aSlips[$sIndex]->getValor() + $oArrecadacao->nValor);
                        if (!empty($oArrecadacao->iRetencao)) {
                            $aSlips[$sIndex]->adicionarRetencao($oArrecadacao->iRetencao);
                        }
                        $aSlips[$sIndex]->addArrecadacao($oArrecadacao->iArrecadacao);
                    } else {
                        $aSlips[$sIndex] = new slip();

                        $iContaCredito = $oArrecadacao->iCtaCredito;

                        $aSlips[$sIndex]->addRecurso($oArrecadacao->iRecurso, $oArrecadacao->nValor);
                        $aSlips[$sIndex]->setContaCredito($iContaCredito);
                        $aSlips[$sIndex]->setCaracteristicaPeculiarCredito("000");
                        $aSlips[$sIndex]->setContaDebito($oArrecadacao->iCtaDebito);
                        $aSlips[$sIndex]->setCaracteristicaPeculiarDebito("000");
                        $aSlips[$sIndex]->setValor($oArrecadacao->nValor);
                        $aSlips[$sIndex]->setTipoPagamento(2);
                        $aSlips[$sIndex]->setSituacao(1);
                        $aSlips[$sIndex]->addArrecadacao($oArrecadacao->iArrecadacao);
                        if (!empty($oArrecadacao->iRetencao)) {
                            $aSlips[$sIndex]->adicionarRetencao($oArrecadacao->iRetencao);
                        }
                        $aSlips[$sIndex]->setHistorico(9017);
                        $aSlips[$sIndex]->setNumCgm($oArrecadacao->iCGM);
                        $oDaoNotaOrdem = new cl_empagenotasordem();
                        $empenho = $notaLiquidacao->getEmpenho();

                        $sObservacao = "";
                        $observacaoEmpenho = " Empenho: {$empenho->getCodigo()}/{$empenho->getAno()}";
                        if ($oParam->isFolha) {
                            $sObservacao .= "Referente as consignações da folha de ";
                            switch ($oParam->paramFolha->sSigla) {
                                case "r14":
                                    $sObservacao .= "Salário ";
                                    break;

                                case "r48":
                                    $sObservacao .= "Complementar {$oParam->paramFolha->sSemestre} ";
                                    break;

                                case "r35":
                                    $sObservacao .= "13o. Salário ";
                                    break;

                                case "r20":
                                    $sObservacao .= "Rescisão ";
                                    break;

                                case "r22":
                                    $sObservacao .= "Adiantamento ";
                                    break;
                            }
                            $sObservacao .= "da competência {$oParam->paramFolha->iMesFolha}/{$oParam->paramFolha->iAnoFolha} 0 ";

                        } else {
                            $sObservacao = "Referente ao pagamento das retenções geradas para o ";
                            $observacaoEmpenho .= " Nota Fiscal: {$notaLiquidacao->getNumeroNota()}";
                        }

                        $sObservacao .= "recurso {$gestaoRecurso}";
                        $sObservacao .= ", cujo pagamento será agendado na OP auxiliar nº {$oDaoOPAuxiliar->e42_sequencial}";
                        $sObservacao .= "\nOP: {$oArrecadacao->iOrdem}";
                        $sObservacao .= $observacaoEmpenho;

                        $aSlips[$sIndex]->setObservacoes($sObservacao);
                    }
                }

                /**
                 * incluimos os slips gerados na base
                 */
                foreach ($aSlips as $oSlip) {
                    $oSlip->save();
                    /**
                     * Incluimos o slip na base de dados
                     */
                    $oDaoNotaOrdem->e43_ordempagamento = $oDaoOPAuxiliar->e42_sequencial;
                    $oDaoNotaOrdem->e43_empagemov = $oSlip->getMovimento();
                    $oDaoNotaOrdem->e43_autorizado = "true";
                    $oDaoNotaOrdem->e43_valor = $oSlip->getValor();
                    $oDaoNotaOrdem->incluir(null);

                    if (isset($oArrecadacao->iRetencaoReceitas) && !empty($oArrecadacao->iRetencaoReceitas)) {
                        $oSlip->vincularSlipReceitaRetencao($oSlip->getSlip(), $oArrecadacao->iRetencaoReceitas);
                    }

                    $oRetorno->aSlipsRetorno[] = $oSlip->getSlip();
                }

                /**
                 * Vinculamos o slip gerado ao tipo Depósito de Diversos - Pagamento
                 */
                if (USE_PCASP) {
                    foreach ($oRetorno->aSlipsRetorno as $iCodigoSlip) {
                        $oDaoTipoOperacaoVinculo = new cl_sliptipooperacaovinculo;
                        $oDaoTipoOperacaoVinculo->k153_slip = $iCodigoSlip;
                        $oDaoTipoOperacaoVinculo->k153_slipoperacaotipo = 13;
                        $oDaoTipoOperacaoVinculo->incluir($iCodigoSlip);

                        if ($oDaoTipoOperacaoVinculo->erro_status == 0) {
                            $sMensagemErro = "Não foi possível víncular o tipo de slip ao slip.\n\n";
                            $sMensagemErro .= "Erro Técnico: {$oDaoTipoOperacaoVinculo->erro_msg}";
                            throw new Exception($sMensagemErro);
                        }
                    }
                }
                db_fim_transacao(false);
            } catch (Exception $eErro) {
                db_fim_transacao(true);
                $oRetorno->message = $eErro->getMessage() . "\nTrace:\n" . $eErro->getTraceAsString();
                $oRetorno->status = 2;
            }
            echo $oJson->encode($oRetorno);
            break;

        /** [AutorizacaoRepasse] - Inicio */

        /** [CancelamentoRepasse] - Inicio */

        /** [DevolucaoRepasse] - Inicio */
    }
} catch (Exception $e) {
    db_fim_transacao(true);
    $oRetorno->message = $e->getMessage();
    $oRetorno->status = 2;
}
