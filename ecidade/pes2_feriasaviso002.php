<?php

/**
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

include(modification("fpdf151/pdf.php"));
include(modification("libs/db_sql.php"));
include(modification("libs/db_libdocumento.php"));
require_once(modification("libs/JSON.php"));

define("TIPO_RELATORIO_GERAL", "0");
define("TIPO_RELATORIO_LOTACAO", "2");
define("TIPO_RELATORIO_MATRICULA", "3");

define("TIPO_FILTRO_GERAL", 0);
define("TIPO_FILTRO_INTERVALO", 1);
define("TIPO_FILTRO_SELECIONADOS", 2);

$oJson = new services_json();
$oGet = db_utils::postMemory($_GET);
$oParametros = $oJson->decode(str_replace("\\", "", $oGet->json));

$oInstituicao = InstituicaoRepository::getInstituicaoSessao();

$dtPagamento = explode("/", $oParametros->dtPagamento);
$dtDocumento = explode("/", $oParametros->dtDocumento);

$anoPagamento = $dtPagamento[2];

$mesPagamento = db_mes($dtPagamento[1],3);
$mesDocumento = db_mes($dtDocumento[1],3);

$descDataPagamento = $dtPagamento[0] .' de '.  $mesPagamento;

$iAnoFolha = $oParametros->ano;
$iMesFolha = $oParametros->mes;

$aWhere = array();

switch ($oParametros->iTipoRelatorio) {

    default:
        $sLabelTipoRelatorio = "Geral";
        $sCampoCondicaoTipoRelatorio = 1;
        $sCampoEstruturalTipoRelatorio = 1;
        $sCampoDescricaoTipoRelatorio = "'GERAL'";
        break;

    case TIPO_RELATORIO_LOTACAO:
        $sLabelTipoRelatorio = "Lotações:";
        $sCampoCondicaoTipoRelatorio = "r70_codigo";
        $sCampoEstruturalTipoRelatorio = "r70_estrut";
        $sCampoDescricaoTipoRelatorio = "r70_descr";
        break;

    case TIPO_RELATORIO_MATRICULA:
        $sLabelTipoRelatorio = "Matrículas:";
        $sCampoCondicaoTipoRelatorio = "rh02_regist";
        $sCampoEstruturalTipoRelatorio = "rh02_regist";
        $sCampoDescricaoTipoRelatorio = "z01_nome";
        break;
}

if ($oParametros->iTipoRelatorio <> TIPO_RELATORIO_GERAL) {

    switch ($oParametros->iTipoFiltro) {
        case TIPO_FILTRO_GERAL:
            //Sem Filtros
            break;
        case TIPO_FILTRO_INTERVALO:
            $aWhere['tipo_filtro' . $oParametros->iTipoRelatorio] = "{$sCampoCondicaoTipoRelatorio} between $oParametros->iIntervaloInicial and $oParametros->iIntervaloFinal";
            break;
        case TIPO_FILTRO_SELECIONADOS:
            $aWhere['tipo_filtro' . $oParametros->iTipoRelatorio] = "{$sCampoCondicaoTipoRelatorio} in (" . implode(", ", $oParametros->iRegistros) . ")";
            break;
    }
}

$sResponsavel = "";
if (!empty($oParametros->responsavel)) {
    $oResponsavel = new Servidor($oParametros->responsavel);
    $sResponsavel = $oResponsavel->getMatricula() .' - ' . $oResponsavel->getCgm()->getNome();
}

$aWhere [] = "((extract(year from r30_per1i) = {$iAnoFolha} and extract(month from r30_per1i) = {$iMesFolha})
                   or (extract(year from r30_per2i) = {$iAnoFolha} and extract(month from r30_per2i) = {$iMesFolha}))";

$dataInicial = "{$iAnoFolha}-{$iMesFolha}-01";
$dataFinal = "{$iAnoFolha}-{$iMesFolha}-" . cal_days_in_month(CAL_GREGORIAN, $iMesFolha,$iAnoFolha);

$sWhere = implode(' and ', $aWhere);
$sCampos = "distinct rh01_regist as matricula, ";
$sCampos .= "r30_perai as periodo_aquisitivo_inicial, ";
$sCampos .= "z01_cgccpf as cpf,";
$sCampos .= "r30_peraf as periodo_aquisitivo_final,";
$sCampos .= "rh55_codigo, rh55_instit,";
$sCampos .= "rh37_descr as cargo, rh37_funcao as codigocargo,";
$sCampos .= "z01_nome as nome, gradeshorarios.rh190_descricao  as escala,";
$sCampos .= "rh55_descr as local_trabalho, rh55_codigo as codigolocal,";
$sCampos .= " '  ' as faltas,";
$sCampos .= "   case when r30_per1i between '{$dataInicial}' and '{$dataFinal}' then r30_per1i else r30_per2i end  as periodogozoinicial,";
$sCampos .= "   case when r30_per1i between '{$dataInicial}' and '{$dataFinal}' then r30_per1f else r30_per2f end  as periodogozofinal";

$ordem = "";

switch ($oParametros->ordem) {
    case "alfabetica":
        $ordem= "rh55_descr";
        break;

    case "numerica":
        $ordem = "rh01_regist";
        break;

    default:
        $ordem = "rh55_descr";
        break;
}

try{

    $sSqlServidores = sql_query_servidoresferias($iMesFolha,
        $iAnoFolha,
        $oInstituicao->getCodigo(),
        $sCampos,
        $sWhere,
        $ordem,
        null);

    $rsServidores = db_query($sSqlServidores);

    if (!$rsServidores) {
        throw new DBException("Erro ao Buscar os Servidores pelos filtros selecionados. \n" . pg_last_error());
    }

    if (pg_num_rows($rsServidores) == 0) {
        throw new BusinessException("Nenhum Servidor encontrado nos Filtros Selecionados.");
    }

} catch (\Exception $eErro) {

    db_redireciona('db_erros.php?fechar=true&db_erro='. $eErro->getMessage());
    exit;
}

$aDadosRelatorios = db_utils::getCollectionByRecord($rsServidores);
$oDocumento = new libdocumento(92000);
$oDocumento->getParagrafos();
$ordemParagrafo = 1;

$pdf = new PDFDocument();
$pdf->Open();
$pdf->AliasNbPages();
$pdf->setfillcolor(235);

$oParagrafo = $oDocumento->aParagrafos[$ordemParagrafo];

$mesFolhaAtual = DBPessoal::getMesFolha();
$anoFolhaAtual = DBPessoal::getAnoFolha();

foreach ($aDadosRelatorios as $registro) {
    $lDiaUtil = false;
    $dataRetorno = date('Y-m-d', strtotime($registro->periodogozofinal. ''));

    // Enquanto não for dia útil
    while (!$lDiaUtil ) {
        $dataRetorno = date('Y-m-d', strtotime($dataRetorno. '+ 1 days'));
        // Verifica se a data de retorno não é final de semana
        $dataVerificar = new \DBDate($dataRetorno);
        $diaSemana = $dataVerificar->diaUtil();
        
        if ($diaSemana) {
            $codInstituicao = $oInstituicao->getCodigo();
            // Se não for, verifica se é feriado
            $sql = "select 
                        * 
                    from 
                        rhcadcalend 
                        inner join calendf on r62_calend = rh53_calend 
                    where 
                        rh53_instit = " . $codInstituicao . "
                        and r62_data = '" . $dataRetorno . "'";
            $rs = db_query($sql);
            if (!$rs) {
                $data = date('d/m/Y', $dataRetorno);
                throw new DBException("Ocorreu um erro ao buscar informações do calendário do dia " . $data . ".");                
            }
            $irs = pg_num_rows($rs);
            // não é feriado
            if ($irs == 0) {
                $lDiaUtil = true;
            }
        }
    }

    $periodoAquisitivo = db_formatar($registro->periodo_aquisitivo_inicial,'d') . ' à '. db_formatar($registro->periodo_aquisitivo_final,'d');
    $periodoGozo       = db_formatar($registro->periodogozoinicial,'d') . ' á '. db_formatar($registro->periodogozofinal,'d');
    $dataRetorno       = db_formatar($dataRetorno, 'd');

    $texto = str_replace(
        array('#periodoAquisitivo#', '#periodoGozo#','#dataPagamento#', '#dataVoltarTrabalho#'),
        array($periodoAquisitivo,$periodoGozo,$descDataPagamento, $dataRetorno),
        $oParagrafo->db02_texto
    );

    $pdf->AddPage();
    $pdf->setfont('arial','',10);
    $pdf->setfont('arial','b',14);
    $pdf->cell(190,8,"Aviso de Férias",'LTR',1,"C",0);

    $pdf->setfont('arial','',10);
    $pdf->cell(95,8,"Empresa: ".$oInstituicao->getDescricao(),'LTB', 0, "L", 0);
    $pdf->cell(95,8,"CNPJ: ".db_formatar($oInstituicao->getCNPJ(), 'cnpj'),'BTR', 1, "C", 0);

    $pos = $pdf->gety();

    $pdf->sety($pos + 4);

    $matricula = $registro->matricula;
    $nome      = $registro->nome;
    $cpf       = $registro->cpf;
    $cargo     = $registro->codigocargo. ' - '. $registro->cargo;
    $local     = $registro->local_trabalho;
    $escala    = $registro->escala;
    $alt       = 4;

    $pdf->Cell(110, $alt + 0.5, 'Servidor: '. $matricula .' - '. $nome, 0, 1, "L", 0);
    $pdf->Cell(80,  $alt + 0.5, 'CPF: '. substr($cpf,0,3).".".substr($cpf,3,3).".".substr($cpf,6,3)."-".substr($cpf,9,2), 0, 1, "d", 0);
    $pdf->Cell(110, $alt + 0.5, 'Escala: '. $escala, 0, 1, "L", 0);
    $pdf->Cell(80,  $alt + 0.5, 'Cargo: '. $cargo, 0, 1, "L", 0);
    $pdf->Cell(112, $alt + 0.5, 'Local: '. $registro->codigolocal.'   '.$local, 0, 1, "L", 0);

    $pos = $pdf->gety();

    $pdf->sety($pos + 4);

    $pdf->multicell(190, 4, '   '.$texto, 0);
    $pos = $pdf->gety();

    $pdf->sety($pos + 4);

    if (!empty($dtDocumento) && isset($dtDocumento[0]) && isset($dtDocumento[1]) && isset($dtDocumento[2])) {
        // Formatação original com a data.
        $descDataDocumento = $dtDocumento[0] . ' de ' . $mesDocumento . ' de ' . $dtDocumento[2];
        $pdf->Cell(188, 3, $oInstituicao->getMunicipio() . ", " . $descDataDocumento, 0, 1, "R", 0);
    } else {
        // Formatação sem data, com espaços maiores.
        $pdf->Cell(140, 3, $oInstituicao->getMunicipio() . ",", 0, 0, "R", 0);
        $pdf->Cell(5, 3, '   ', 0, 0, "C", 0); // Espaço inicial maior.
        $pdf->Cell(5, 3, 'de', 0, 0, "C", 0); // Primeiro "de".
        $pdf->Cell(20, 3, '   ', 0, 0, "C", 0); // Espaço entre os "de".
        $pdf->Cell(5, 3, 'de', 0, 1, "C", 0); // Segundo "de".
    }

    $linhaSe =  "________________________________________________"."\n\n". $nome;
    $linhaMu =  "_________________________________"."\n\n". $oInstituicao->getDescricao().
        "\n\n".$sResponsavel;

    $largura = 80;
    $pdf->ln(8);
    $pos = $pdf->gety();
    $pdf->multicell($largura ,3,$linhaMu,0,"C",0,0);

    $pdf->sety($pos);
    $pdf->setx(100);
    $pdf->multicell(100,3,$linhaSe,0,"C",0,0);
    $pdf->setxy(200,$pos);

    /** duplica linha */

    $pos = $pdf->gety();
    $pdf->sety($pos + 28);
    $pdf->setfont('arial','',10);
    $pdf->setfont('arial','b',14);
    $pdf->cell(190,8,"Aviso de Férias",'LTR', 1, "C", 0);

    $pdf->setfont('arial','',10);
    $pdf->cell(95,8,"Empresa: ".$oInstituicao->getDescricao(),'LTB', 0, "L", 0);
    $pdf->cell(95,8,"CNPJ: ".db_formatar($oInstituicao->getCNPJ(), "cnpj"),'BTR', 1, "C", 0);
    $pos = $pdf->gety();

    $pdf->sety($pos + 4);

    $matricula = $registro->matricula;
    $nome      = $registro->nome;
    $cpf       = $registro->cpf;
    $cargo     = $registro->codigocargo. ' - '. $registro->cargo;
    $local     = $registro->local_trabalho;
    $escala    = $registro->escala;
    $alt       = 4;

    $pdf->Cell(110, $alt + 0.5, 'Servidor: '. $matricula .' - '. $nome, 0, 1, "L", 0);
    $pdf->Cell(80,  $alt + 0.5, 'CPF: '. substr($cpf,0,3).".".substr($cpf,3,3).".".substr($cpf,6,3)."-".substr($cpf,9,2), 0, 1, "d", 0);
    $pdf->Cell(110, $alt + 0.5, 'Escala: '. $escala, 0, 1, "L", 0);
    $pdf->Cell(80,  $alt + 0.5, 'Cargo: '. $cargo, 0, 1, "L", 0);
    $pdf->Cell(112, $alt + 0.5, 'Local: '. $registro->codigolocal.'   '.$local, 0, 1, "L", 0);

    $pos = $pdf->gety();

    $pdf->sety($pos + 4);

    $pdf->multicell(190, 4, '   '.$texto, 0);
    $pos = $pdf->gety();

    $pdf->sety($pos + 4);

    if (!empty($dtDocumento) && isset($dtDocumento[0]) && isset($dtDocumento[1]) && isset($dtDocumento[2])) {
        // Formatação original com a data.
        $descDataDocumento = $dtDocumento[0] . ' de ' . $mesDocumento . ' de ' . $dtDocumento[2];
        $pdf->Cell(188, 3, $oInstituicao->getMunicipio() . ", " . $descDataDocumento, 0, 1, "R", 0);
    } else {
        // Formatação sem data, com espaços maiores.
        $pdf->Cell(140, 3, $oInstituicao->getMunicipio() . ",", 0, 0, "R", 0);
        $pdf->Cell(5, 3, '   ', 0, 0, "C", 0); // Espaço inicial maior.
        $pdf->Cell(5, 3, 'de', 0, 0, "C", 0); // Primeiro "de".
        $pdf->Cell(20, 3, '   ', 0, 0, "C", 0); // Espaço entre os "de".
        $pdf->Cell(5, 3, 'de', 0, 1, "C", 0); // Segundo "de".
    }

    $linhaSe =  "________________________________________________"."\n\n". $nome;
    $linhaMu =  "_________________________________"."\n\n". $oInstituicao->getDescricao().
        "\n\n".$sResponsavel;

    $largura = 80;
    $pdf->ln(8);
    $pos = $pdf->gety();
    $pdf->multicell($largura ,3,$linhaMu,0,"C",0,0);

    $pdf->sety($pos);
    $pdf->setx(100);
    $pdf->multicell(100,3,$linhaSe,0,"C",0,0);
    $pdf->setxy(200,$pos);
}

$pdf->Output();

function sql_query_servidoresferias($iMesFolha, $iAnoFolha, $iInstituicao, $sCampos = "", $sWhere = "", $sOrdem = "", $sAgrupamento = "")
{
    if (empty($sCampos)) {
        $sCampos = "*";
    }

    $mesFolhaAtual = DBPessoal::getMesFolha();
    $anoFolhaAtual = DBPessoal::getAnoFolha();

    $sSQLBase = "select {$sCampos}                                                                                              \n";
    $sSQLBase .= "  from cadferia ";
    $sSQLBase .= "      inner join rhpessoal  on r30_regist = rh01_regist ";
    $sSQLBase .= "                           and r30_anousu = $anoFolhaAtual ";
    $sSQLBase .= "                           and r30_mesusu = $mesFolhaAtual ";
    $sSQLBase .= "       inner join cgm             on cgm.z01_numcgm                = rhpessoal.rh01_numcgm                    \n";
    $sSQLBase .= "       inner join rhpessoalmov    on rhpessoalmov.rh02_regist      = rhpessoal.rh01_regist                    \n";
    $sSQLBase .= "       left  join rhpescargo      on rhpescargo.rh20_seqpes        = rhpessoalmov.rh02_seqpes                 \n";
    $sSQLBase .= "       left  join rhcargo         on rhcargo.rh04_codigo           = rhpescargo.rh20_cargo                    \n";
    $sSQLBase .= "                                 and rhcargo.rh04_instit           = rhpessoalmov.rh02_instit                 \n";
    $sSQLBase .= "       left  join rhfuncao        on rhfuncao.rh37_funcao          = rhpessoalmov.rh02_funcao                 \n";
    $sSQLBase .= "                                 and rhfuncao.rh37_instit          = rhpessoalmov.rh02_instit                 \n";
    $sSQLBase .= "       left  join rhlota          on rhlota.r70_codigo             = rhpessoalmov.rh02_lota                   \n";
    $sSQLBase .= "                                 and rhlota.r70_instit             = rhpessoalmov.rh02_instit                 \n";
    $sSQLBase .= "       left  join rhlotaexe       on rh26_codigo                   = r70_codigo                               \n";
    $sSQLBase .= "                                 and rh26_anousu                   = rh02_anousu                              \n";
    $sSQLBase .= "       left  join orcorgao        on o40_orgao                     = rh26_orgao                               \n";
    $sSQLBase .= "                                 and o40_anousu                    = rhpessoalmov.rh02_anousu                 \n";
    $sSQLBase .= "                                 and o40_instit                    = rhpessoalmov.rh02_instit                 \n";
    $sSQLBase .= "       left  join rhlotavinc      on rh25_codigo                   = r70_codigo                               \n";
    $sSQLBase .= "                                 and rh25_anousu                   = rhpessoalmov.rh02_anousu                 \n";
    $sSQLBase .= "       left  join orctiporec      on o15_codigo                    = rh25_recurso                             \n";
    $sSQLBase .= "       inner join rhregime        on rhregime.rh30_codreg          = rhpessoalmov.rh02_codreg                 \n";
    $sSQLBase .= "                                 and rhregime.rh30_instit          = rhpessoalmov.rh02_instit                 \n";
    $sSQLBase .= "       left  join rhpesrescisao   on rhpesrescisao.rh05_seqpes     = rhpessoalmov.rh02_seqpes                 \n";
    $sSQLBase .= "       left  join rhpespadrao     on rhpespadrao.rh03_seqpes       = rhpessoalmov.rh02_seqpes                 \n";
    $sSQLBase .= "                                 and rhpespadrao.rh03_anousu       = rhpessoalmov.rh02_anousu                 \n";
    $sSQLBase .= "                                 and rhpespadrao.rh03_mesusu       = rhpessoalmov.rh02_mesusu                 \n";
    $sSQLBase .= "       left  join padroes         on padroes.r02_anousu            = rhpespadrao.rh03_anousu                  \n";
    $sSQLBase .= "                                 and padroes.r02_mesusu            = rhpespadrao.rh03_mesusu                  \n";
    $sSQLBase .= "                                 and padroes.r02_regime            = rhpespadrao.rh03_regime                  \n";
    $sSQLBase .= "                                 and padroes.r02_codigo            = rhpespadrao.rh03_padrao                  \n";
    $sSQLBase .= "                                 and padroes.r02_instit            = rhpessoalmov.rh02_instit                 \n";
    $sSQLBase .= "       left  join rhpeslocaltrab  on rhpeslocaltrab.rh56_seqpes    = rhpessoalmov.rh02_seqpes                 \n";
    $sSQLBase .= "                                 and rhpeslocaltrab.rh56_princ     = 't'                                      \n";
    $sSQLBase .= "       left  join rhlocaltrab     on rhpeslocaltrab.rh56_localtrab = rhlocaltrab.rh55_codigo                  \n";
    $sSQLBase .= "       left  join escalaservidor  on escalaservidor.rh192_regist   = rhpessoalmov.rh02_regist and escalaservidor.rh192_instit = rhpessoalmov.rh02_instit \n";
    $sSQLBase .= "       left  join gradeshorarios  on gradeshorarios.rh190_sequencial  = escalaservidor.rh192_gradeshorarios  \n";
    
    $sSQLBase .= " where rh02_anousu = $anoFolhaAtual                                                                          \n";
    $sSQLBase .= "   and rh02_mesusu = $mesFolhaAtual                                                                          \n";
    $sSQLBase .= "   and rh02_instit = $iInstituicao                                                                           \n";

    if (!empty($sWhere)) {
        $sSQLBase .= "   and {$sWhere}                                                                                          \n";
    }

    if (!empty($sAgrupamento)) {
        $sSQLBase .= "group by {$sAgrupamento}";
    }

    if (!empty($sOrdem)) {
        $sSQLBase .= " order by {$sOrdem}                                                                                        \n";
    }
    return $sSQLBase;
}
