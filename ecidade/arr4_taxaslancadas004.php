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

use ECidade\Tributario\Arrecadacao\Model\DiversosTaxa;
use ECidade\Tributario\Arrecadacao\Repository\TaxasLancadasRepository;
use ECidade\Tributario\Arrecadacao\Model\TaxasLancadasRecibo;
use ECidade\Tributario\Arrecadacao\Repository\DiversosTaxaRepository;
use ECidade\Tributario\Arrecadacao\Repository\TaxasLancadasReciboRepository;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));

$taxasLancadasRepository = TaxasLancadasRepository::getInstance();
$taxasLancadasRecibo = new TaxasLancadasRecibo();
$taxasLancadasReciboRepository = TaxasLancadasReciboRepository::getInstance();

db_postmemory($_GET);
db_postmemory($_POST);

$dataVencimento = date('Y-m-d', strtotime(str_replace("/", "-", $dataVencimento)));
$dataCorrente = date("Y-m-d");
$anoCorrente = date("Y");
$instituicao = (!empty(db_getsession("DB_instit")) ? db_getsession("DB_instit") : 1);
$usuario = (!empty(db_getsession("DB_id_usuario")) ? db_getsession("DB_id_usuario") : 1);
$DB_DATACALC = db_getsession("DB_datausu");
$db_datausu = date("Y-m-d", db_getsession("DB_datausu"));

db_inicio_transacao();

if (!empty($j01_matric)) {
    $sqlIptubase = "SELECT *
                        FROM cadastro.iptubase
                        WHERE j01_matric = {$j01_matric};";

    $rsIptubase = db_query($sqlIptubase);

    if (!$rsIptubase) {
        throw new \Exception("Erro ao buscar os dados da matricula.");
    }

    $oIptubase = \db_utils::fieldsMemory($rsIptubase, 0);

    $z01_numcgm = $oIptubase->j01_numcgm;
} else {
    if (!empty($q02_inscr)) {
        $sqlIssbase = "SELECT *
                            FROM issqn.issbase
                        WHERE q02_inscr = {$q02_inscr};";

        $rsIssbase = db_query($sqlIssbase);

        if (!$rsIssbase) {
            throw new \Exception("Erro ao buscar os dados da inscrição.");
        }

        $oIssbase = db_utils::fieldsMemory($rsIssbase, 0);

        $z01_numcgm = $oIssbase->q02_numcgm;
    }
}

$isRequestAjax = false;

$oTaxa = $taxasLancadasRepository->getTaxa($taxa);

$aCamposDinamicos = JSON::create()->parse(str_replace("\\", "", $campoDinamicos));
$aTaxasVinculadas = JSON::create()->parse(str_replace("\\", "", $taxasVinculadas));

$numTaxasSelecionadas = 0;
foreach ($aTaxasVinculadas as $oTaxaVinculada) {
    if ($oTaxaVinculada->ativa) {
        $numTaxasSelecionadas++;
    }
}
if ($numTaxasSelecionadas == 0) {
    throw new \Exception('Erro, Selecione uma taxa.');
}

$historico = isset($historico) ? $historico : '';
$historicoTaxas = "";

if (isset($processo) && !empty($processo)) {
    $historico .= "PROCESSO: $processo";
}

foreach ($aTaxasVinculadas as $oTaxaVinculada) {
    if (!$oTaxaVinculada->ativa) {
        continue;
    }
    $valorTaxaFormatado = number_format($oTaxaVinculada->valor, 2, ',', '');
    $historicoTaxas .= "{$oTaxaVinculada->sequencial} - {$oTaxaVinculada->descricao}: {$valorTaxaFormatado} | ";
}
$historicoTaxas = trim($historicoTaxas, "| ");

if (!empty($historicoTaxas)) {
    $historico .= "\nTaxas:" . $historicoTaxas;
}

if (!empty($y27_codtipo)) {
    $cltipofiscaliza = new cl_tipofiscaliza;

    $resultFiscalizacao = $cltipofiscaliza->sql_record($cltipofiscaliza->sql_query_file($y27_codtipo));

    if (!$resultFiscalizacao) {
        throw new \Exception('Erro ao buscar o tipo de fiscalização.');
    }

    $oFiscalizacao = db_utils::fieldsMemory($resultFiscalizacao, 0);

    $historico .= "\nTipo de Fiscalização: {$oFiscalizacao->y27_codtipo} - {$oFiscalizacao->y27_descr}";
}

$sqlNumpre = "SELECT nextval('numpref_k03_numpre_seq') AS iNumpre;";
$rsNumpre = db_query($sqlNumpre);
$iNumpre = db_utils::fieldsMemory($rsNumpre, 0)->inumpre;

foreach ($aCamposDinamicos as $oCampo) {
    if ($oCampo->valor != "") {
        $historico .= "\n{$oCampo->label}: $oCampo->valor";

        $sqlInsertValorCamposDinamicos = "INSERT INTO arrecadacao.taxaslancadasdinamicosvalor
                                                    (
                                                        ar48_codcam,
                                                        ar48_conteudo,
                                                        ar48_numnov
                                                    )
                                                VALUES
                                                    (
                                                        {$oCampo->codcam},
                                                        '{$oCampo->valor}',
                                                        {$iNumpre}
                                                    );";

        $resultCamosDinamicosValor = db_query($sqlInsertValorCamposDinamicos);

        if (!$resultCamosDinamicosValor) {
            throw new \Exception('Erro ao inserir na tabela taxaslancadasdinamicosvalor.');
        }
    }
}

if ($oTaxa->geraDebito) {
    $sqlProcdiver = "SELECT dv09_tipo,
                            dv09_hist,
                            dv09_receit
                        FROM diversos.procdiver
                        WHERE dv09_procdiver = {$oTaxa->ar44_procedencia};";

    $rsProcdiver = db_query($sqlProcdiver);

    if (!$rsProcdiver) {
        throw new \Exception('Erro ao buscar o tipo da procedência.');
    }

    $oProcdiver = db_utils::fieldsMemory($rsProcdiver, 0);

    $codHistorico = $oProcdiver->dv09_hist;
    $receita = $oProcdiver->dv09_receit;
    $tipo = $oProcdiver->dv09_tipo;
} else {
    $codHistorico = 502;
    $receita = $oTaxa->ar44_receita;
    $tipo = 11;

    $cltabrecarretipo = new \cl_tabrecarretipo();

    $sqlTabrecarretipo = $cltabrecarretipo->sql_query_file(null, "k79_arretipo", null, " k79_receit = {$receita}");
    $rTabrecarretipo = db_query($sqlTabrecarretipo);

    if (!$rTabrecarretipo) {
        throw new \Exception('Erro ao buscar o tipo da receita na tabela tabrecarretipo.');
    }

    if (pg_numrows($rTabrecarretipo) > 0) {
        $tipo = db_utils::fieldsMemory($rTabrecarretipo, 0)->k79_arretipo;
    }
}

$cltaborc = new \cl_taborc;
$resultRecursoReceita = db_query($cltaborc->sql_query( db_getsession("DB_anousu"), $receita, "orctiporec.o15_codigo, orctiporec.o15_complemento"));

if (!$resultRecursoReceita) {
    throw new \Exception('Erro ao consultar recurso da receita.');
}
db_fieldsmemory($resultRecursoReceita,0);

$recurso = RecursoRepository::getRecursoPorCodigo($o15_codigo);

$cl_arretipo = new \cl_arretipo();
$result = $cl_arretipo->sql_record($cl_arretipo->sql_query($tipo));

if (!$result) {
    throw new \Exception('Erro ao consultar na tabela arretipo.');
}

$rsNovoSequencialDiversos = db_query("select nextval('diversos_dv05_coddiver_seq')");
$novoSequencialDiversos   = db_utils::fieldsMemory($rsNovoSequencialDiversos, 0)->nextval;

$oArretipo = db_utils::fieldsMemory($result, 0);

$departamentoLogado = db_getsession("DB_coddepto");

if ($oTaxa->geraDebito || $grupoTaxasHabilitado == 'true') {
    $procedencia = (isset($procedencia) && !empty($procedencia)) ? $procedencia : $oTaxa->ar44_procedencia;

    $sqlDiversos = "INSERT INTO diversos.diversos
                        (
                            dv05_coddiver,
                            dv05_numcgm,
                            dv05_dtinsc,
                            dv05_exerc,
                            dv05_numpre,
                            dv05_vlrhis,
                            dv05_procdiver,
                            dv05_numtot,
                            dv05_privenc,
                            dv05_provenc,
                            dv05_diaprox,
                            dv05_oper,
                            dv05_valor,
                            dv05_obs,
                            dv05_instit,
                            dv05_login,
                            dv05_depto
                        )
                    VALUES
                        (
                            {$novoSequencialDiversos},
                            {$z01_numcgm},
                            '{$dataCorrente}',
                            {$anoCorrente},
                            {$iNumpre},
                            {$valorFinal},
                            {$procedencia},
                            1,
                            '{$dataVencimento}',
                            '{$dataVencimento}',
                            0,
                            '{$dataCorrente}',
                            {$valorFinal},
                            '{$historico}',
                            {$instituicao},
                            {$usuario},
                            {$departamentoLogado}
                        );";

    $rsDiversos = db_query($sqlDiversos);

    if (!$rsDiversos) {
        throw new \DBException('Erro ao inserir na tabela diversos.');
    }

    foreach($aTaxasVinculadas as $oTaxaVinculada) {
        if (!$oTaxaVinculada->ativa) {
            continue;
        }

        $diversosTaxaRepository = new DiversosTaxaRepository;

        $diversosTaxa = new DiversosTaxa();
        $diversosTaxa->setSequencial(null);
        $diversosTaxa->setCodigoDiversos($novoSequencialDiversos);
        $diversosTaxa->setCodigoTaxa($oTaxaVinculada->sequencial);
        $diversosTaxa->setIsPrincipal($oTaxaVinculada->principal);
        $diversosTaxa->setValor($oTaxaVinculada->valor);

        $diversosTaxaRepository->persist($diversosTaxa);
    }

    $sqlArrecad = "SELECT fc_geraarrecad({$tipo}, {$iNumpre}, true, 1, false);";

    $rsArrecad = db_query($sqlArrecad);

    if (!$rsArrecad) {
        throw new \Exception('Erro ao inserir na tabela arrecad.');
    }

    if (!empty($j01_matric)) {
        $sqlArrematric = "INSERT INTO caixa.arrematric
                              (
                                  k00_numpre,
                                  k00_matric
                              )
                          VALUES
                              (
                                  {$iNumpre},
                                  {$j01_matric}
                              );";

        $rsArrematric = db_query($sqlArrematric);

        if (!$rsArrematric) {
            throw new \Exception('Erro ao inserir na tabela arrematric.');
        }
    } else {
        if (!empty($q02_inscr)) {
            $sqlArreinscr = "INSERT INTO caixa.arreinscr
                                  (
                                      k00_numpre,
                                      k00_inscr
                                  )
                              VALUES
                                  (
                                      {$iNumpre},
                                      {$q02_inscr}
                                  );";

            $rsArreinscr = db_query($sqlArreinscr);

            if (!$rsArreinscr) {
                throw new \Exception('Erro ao inserir na tabela arreinscr.');
            }
        }
    }

    $sqlIdhist = "SELECT nextval('arrehist_k00_idhist_seq') AS idHist;";
    $rsIdhist = db_query($sqlIdhist);
    $iIdHist = db_utils::fieldsMemory($rsIdhist, 0)->idhist;

    $sqlArrehist = "INSERT INTO caixa.arrehist
                        (
                            k00_numpre,
                            k00_numpar,
                            k00_hist,
                            k00_dtoper,
                            k00_hora,
                            k00_id_usuario,
                            k00_histtxt,
                            k00_limithist,
                            k00_idhist
                        )
                    VALUES
                        (
                            {$iNumpre},
                            1,
                            {$codHistorico},
                            '{$dataCorrente}',
                            '{$horaCorrente}',
                            {$usuario},
                            '{$historico}',
                            null,
                            {$iIdHist}
                        );";

    $rsArrehist = db_query($sqlArrehist);

    if (!$rsArrehist) {
        throw new \Exception('Erro ao inserir na tabela arrehist.');
    }

    $sqlArrehistip = "INSERT INTO caixa.arrehistip
                        (
                            k45_idhist,
                            k45_ip,
                            k45_obs
                        )
                    VALUES
                        (
                            {$iIdHist},
                            '192.168.10.01',
                            ''
                        );";

    $rsArrehistip = db_query($sqlArrehistip);

    if (!$rsArrehistip) {
        throw new \Exception('Erro ao inserir na tabela arrehistip.');
    }

    $taxasLancadasRecibo->setSequencial(null);
    $taxasLancadasRecibo->setTaxaslancadas($taxa);
    $taxasLancadasRecibo->setNumnov($iNumpre);
    $taxasLancadasRecibo->setTipoemissao(0);
    $taxasLancadasRecibo->setDepartamento(db_getsession("DB_coddepto"));
    $taxasLancadasReciboRepository->persist($taxasLancadasRecibo);

    $oParam = new stdClass();
    $oParam2 = new stdClass();
    $oParam->exec = "geraRecibo_Carne";
    $oParam->lNovoRecibo = "true";

    $oParam2->H_ANOUSU = "{$anoCorrente}";
    $oParam2->H_DATAUSU = "{$db_datausu}";
    $oParam2->certidao = "";
    $oParam2->tipo_debito = "{$tipo}";
    $oParam2->k03_tipo = $oArretipo->k03_tipo;
    $oParam2->perfil_procuradoria = "1";
    $oParam2->k03_parcelamento = $oArretipo->k03_parcelamento;
    $oParam2->k03_permparc = $oArretipo->k03_permparc;
    $oParam2->k00_formemissao = $oArretipo->k00_formemissao;
    $oParam2->numpre_unica = "";
    $oParam2->txtNumpreUnicaSelecionados = "";
    $oParam2->DadosUnicas = "";
    $oParam2->totregistros = "1";
    $oParam2->marcartodas = "true";
    $oParam2->marcarvencidas = "false";
    $oParam2->CHECK0 = "N{$iNumpre}P1R0";
    $oParam2->_VALORES0 = "{$valorFinal}";
    $oParam2->forcarvencimento = "false";
    $oParam2->processarDescontoRecibo = "true";
    $oParam2->k00_dtoper = "{$dataCorrente}";

    $oParam->oDadosForm = $oParam2;

    $_POST["json"] = str_replace('"', '\\"', json_encode($oParam));

    include_once(modification("cai3_emitecarne.RPC.php"));

    $iNumnov = array_unique($aRecibopaga_numnov)[0];

    $_GET["sessao"] = "RequestRecibo0";
    $_GET["reemite_recibo"] = true;
    $_GET["forcarvencimento"] = false;
    $_GET["k03_numpre"] = $iNumnov;
    $_GET["k03_numnov"] = $iNumnov;

    if (!empty($oTaxa->ar44_receitaxaexpediente)) {
        $sqlInsertRecibopaga = "INSERT INTO recibopaga
                                        SELECT k00_numcgm,
                                            k00_dtoper,
                                            {$oTaxa->ar44_receitaxaexpediente},
                                            11505,
                                            {$oTaxa->valorTaxaExpediente},
                                            k00_dtvenc,
                                            k00_numpre,
                                            k00_numpar,
                                            k00_numtot,
                                            k00_numdig,
                                            k00_conta,
                                            k00_dtpaga,
                                            k00_numnov
                                        FROM recibopaga
                                        WHERE k00_numnov = {$iNumnov};";

        $rsInsertRecibopaga = db_query($sqlInsertRecibopaga);

        if (!$rsInsertRecibopaga) {
            throw new \Exception('Erro ao inserir na tabela reciboPaga.');
        }
    }

    include_once(modification("cai3_gerfinanc003.php"));
} else {
    $codrece = $receita;
    $vlrrece = $valorFinal;
    $fonte = $recurso->getFonteRecurso(db_getsession("DB_anousu"));

    $codcpca = "000";
    $codrecu = $fonte->gestao;
    $codcomplemento = $o15_complemento;

    if ($oTaxa->ar44_receitaxaexpediente != "" AND $oTaxa->valorTaxaExpediente != "") {
        $codrece .= "YY{$oTaxa->ar44_receitaxaexpediente}";
        $vlrrece .= "YY{$oTaxa->valorTaxaExpediente}";

        $codcpca .= "YY000";
        $codrecu .= "YY{$fonte->gestao}";
        $codcomplemento .= "YY{$o15_complemento}";
    }

    $forcaCodhistTaxaExpedienteTaxa = "11505_{$oTaxa->ar44_receitaxaexpediente}";

    $db_datausu = $dataVencimento;

    $_POST["CHECK10"]          = "";
    $_POST["k03_perparc"]      = "f";
    $_POST["numpre"]           = $iNumpre;
    $_POST["iNumpre"]          = $iNumpre;
    $_POST["k03_numpre"]        = $iNumpre;
    $_POST["k03_tipo"]         = $oArretipo->k03_tipo;
    $_POST["tipo_debito"]      = $tipo;
    $_POST["tipo"]             = $tipo;
    $_POST["arretipo"]         = $tipo;
    $_POST["k00_histtxt"]      = $historico;
    $_POST["k03_parcelamento"] = $oArretipo->k03_parcelamento;
    $_POST["emrec"]            = "t";
    $_POST["reemite_recibo"]   = "1";

    $lForcar = true;

    $taxasLancadasRecibo->setSequencial(null);
    $taxasLancadasRecibo->setTaxaslancadas($taxa);
    $taxasLancadasRecibo->setNumnov($iNumpre);
    $taxasLancadasRecibo->setTipoemissao(0);
    $taxasLancadasRecibo->setDepartamento(db_getsession("DB_coddepto"));
    $taxasLancadasReciboRepository->persist($taxasLancadasRecibo);

    require_once(modification("cai4_recibo003.php"));
}

db_fim_transacao();
