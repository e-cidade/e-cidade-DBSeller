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
use \ECidade\Pdf\Pdf;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));

/**
 * Cria uma célula no PDF, e ajusta o tamanho da fonte para que o texo caiba
 */
function cellFitFont(PDF $pdf, $tamanhoFonte, $w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = 0, $link = '', $preenc = '')
{
    $largura = $pdf->GetStringWidth($txt);
    // Começamos com a fonte em 100%
    $porcentagemFonte = 1;
    while ($largura > $w) {
        // E vamos diminuindo em passos de 5%...
        $porcentagemFonte -= 0.05;
        $pdf->SetFontSize($tamanhoFonte * $porcentagemFonte);
        $largura = $pdf->GetStringWidth($txt);
    }

    $pdf->Cell($w, $h, $txt, $border, $ln, $align, $fill);
    $pdf->SetFontSize($tamanhoFonte);
}

$oDaoMatricula       = new cl_matricula();
$oDaoCalendario      = new cl_calendario();
$oDaoEduParametros   = new cl_edu_parametros();
$oDaoRegenteConselho = new cl_regenteconselho();
$oDaoTurma           = new cl_turma();
$oDaoEscola          = new cl_escola();
$oDaoTipoSanguineo   = new cl_tiposanguineo();
$iEscola             = db_getsession("DB_coddepto");
$cabecalho           = utf8_decode(base64_decode($cabecalho));
$campos              = base64_decode($campos);

if (isset($_GET["idadeInicial"])) {
    $idadeInicial= $_GET["idadeInicial"];
} else {
    $idadeInicial="";
}
if (isset($_GET["idadeFinal"])) {
    $idadeFinal= $_GET["idadeFinal"];
} else {
    $idadeFinal="";
}

$sCampos  = "distinct                                                              \n";
$sCampos .= "ed57_i_codigo, ed57_c_descr, ed29_i_codigo,                           \n";
$sCampos .= "ed29_c_descr, ed52_c_descr, ed11_c_descr, ed15_c_nome, ed223_i_serie  \n";

$sSqlTurmaSerie = $oDaoTurma->sql_query_turmaserie("", $sCampos, "ed57_c_descr", " ed220_i_codigo in ($turmas)");
$rsTurmaSerie   = $oDaoTurma->sql_record($sSqlTurmaSerie);

if ($oDaoTurma->numrows == 0) { ?>
  <table width='100%'>
    <tr>
      <td align='center'>
        <font color='#FF0000' face='arial'>
          <b>Nenhuma turma para o curso selecionado<br>
          <input type='button' value='Fechar' onclick='window.close()'></b>
        </font>
      </td>
    </tr>
  </table>

    <?php
    exit;
}

$sSqlCalendario   = $oDaoCalendario->sql_query("", "ed52_i_ano as ano_calendario", "", "ed52_i_codigo = {$codcalendario}");
$rsCalendario     = $oDaoCalendario->sql_record($sSqlCalendario);
$oDadosCalendario = db_utils::fieldsmemory($rsCalendario, 0);

$dtSistema = date('Y-m-d', db_getsession("DB_datausu"));

$campos = str_replace(chr(92), "", $campos);
$campos = str_replace("fc_idade()", "fc_idade(ed47_d_nasc,'$dtSistema'::date)", $campos);
$campos = str_replace("fc_idade_mes()", "fc_idade_anomesdia(ed47_d_nasc,'$dtSistema')", $campos);
$campos = str_replace("fc_idade_dia()", "fc_idade_anomesdia(ed47_d_nasc,'$dtSistema')", $campos);

$oPdf = new ECidade\Pdf\Pdf();
$oPdf->init(false);
$oPdf->exibeHeader(true, \Fpdf\Pdf::HEADER_ESCOLA);
$oPdf->setExibeBrasao(true);
$oPdf->AliasNbPages();

$head1 = $titulorel == "" ? "LISTA OFICIAL DAS TURMAS" : base64_decode($titulorel);
$oPdf->addTitulo($head1);
$oPdf->AddPage($orientacao);

$larguraUtil = ($oPdf->getW() - $oPdf->getRightMargin() - $oPdf->getLeftMargin());

$aMeses           = array("JAN", "FEV", "MAR", "ABR", "MAI", "JUN", "JUL", "AGO", "SET", "OUT", "NOV", "DEZ");
$aCamposCabecalho = explode("|", $cabecalho);
// Largura dos campos é enviada pelo cliente
$aCamposLargura   = explode("|", $colunas);
$aCamposAlinha    = explode("|", $alinhamento);
$aCamposImpressao = explode("__", $campos);
$campos  = implode(", ", $aCamposImpressao);
$iLinhas = $oDaoTurma->numrows;

$iLarguraMaxima      = $orientacao == "P" ? 195 : 280;
$aCamposTexto        = array( "Nome do Aluno", "Endereço/Bairro", "Email", "Filiação 1", "Filiação 2", "CPF Filiação 1", "CPF Filiação 2", "CPF Responsável" );
$aCamposData         = array( "ed47_d_nasc", "ed60_d_datamatricula", "ed60_d_datasaida", "ed76_d_data" );
$iSomaColunas        = array_sum($aCamposLargura);
$aCabecalhosTexto    = array_intersect($aCamposCabecalho, $aCamposTexto);
$iTamanhoIncrementar = floor(( $iLarguraMaxima - $iSomaColunas ) / count($aCabecalhosTexto));
$aLarguraCorrigida   = array();
$aCamposConcatenados = array(
                              "Endereço/Bairro",
                              "Telefones",
                              "Naturalidade",
                              "Transporte Escolar",
                              "Bolsa Família",
                              "Rep",
                              "Certidão",
                              "Local de Procedência",
                              "Assinatura 1",
                              "Assinatura 2",
                              "Assinatura 3",
                              "Meses",
                              "Idade",
                              "Meses da Idade",
                              "Dias da Idade",
                              "Foto"
                            );

for ($iContFor = 0; $iContFor < $iLinhas; $iContFor++) {
    $oDadosTurmaSerie    = db_utils::fieldsmemory($rsTurmaSerie, $iContFor);

    $sSqlRegenteConselho = $oDaoRegenteConselho->sql_query(
        "",
        "case when ed20_i_tiposervidor = 1 then cgmrh.z01_nome
                                                          else cgmcgm.z01_nome end as regente",
        "",
        " ed235_i_turma = $oDadosTurmaSerie->ed57_i_codigo "
    );
    $rsRegenteConselho   = $oDaoRegenteConselho->sql_record($sSqlRegenteConselho);

    $regente = "";
    if ($oDaoRegenteConselho->numrows > 0) {
        $regente = db_utils::fieldsMemory($rsRegenteConselho, 0)->regente;
    }

    $labelRegente = "";
    if ($nomeregente == "S") {
        $labelRegente = " Regente: $regente";
    }

    $turma = "Turma: ".$oDadosTurmaSerie->ed57_c_descr;
    $curso = "Curso: ".$oDadosTurmaSerie->ed29_i_codigo." - ".$oDadosTurmaSerie->ed29_c_descr;
    $calendario = "Calendário: ".$oDadosTurmaSerie->ed52_c_descr;
    $etapa = "Etapa: ".$oDadosTurmaSerie->ed11_c_descr;
    $turno = "Turno: ".$oDadosTurmaSerie->ed15_c_nome;

    $oPdf->setfont('arial', 'b', $tamfonte);
    $oPdf->cell($larguraUtil, 4, $turma." - ".$curso." - ".$calendario, 0, 0, "L", 0);
    $oPdf->ln();
    $oPdf->cell($larguraUtil, 4, $etapa." - ".$turno." - ".$labelRegente, 0, 0, "L", 0);
    $oPdf->ln();

    $oPdf->ln(5);
    $somacampos = 0;

    for ($iContFor1 = 0; $iContFor1 < count($aCamposCabecalho); $iContFor1++) {
        $next = 0;
        if ($iContFor1 == (count($aCamposCabecalho)-1)) {
            $next = 1;
        }

        $aLarguraCorrigida[$iContFor1] = $aCamposLargura[$iContFor1];

        if (in_array($aCamposCabecalho[$iContFor1], $aCabecalhosTexto)) {
            $aLarguraCorrigida[$iContFor1] = $aCamposLargura[$iContFor1] + $iTamanhoIncrementar;
        }

        if (trim($aCamposCabecalho[$iContFor1]) == "Meses") {
            for ($iContFor2 = 0; $iContFor2 < 12; $iContFor2++) {
                $next_mes = $next;
                if ($iContFor2 < 11) {
                    $next_mes = 0;
                }

                $oPdf->cell($aLarguraCorrigida[$iContFor1]/12, 4, $aMeses[$iContFor2], 1, $next_mes, "C", 0);
            }
        } else {
            $oPdf->cell($aLarguraCorrigida[$iContFor1], 4, $aCamposCabecalho[$iContFor1], 1, $next, "C", 0);
        }

        $somacampos += $aLarguraCorrigida[$iContFor1];
    }

    $condicao = "";
    if ($active == "SIM") {
        $condicao=" AND ed60_c_situacao = 'MATRICULADO' ";
    }

    if ($trocaTurma == 1) {
        $condicao .= " AND ed60_c_situacao != 'TROCA DE TURMA' ";
    }

    $whereIdade ="";

    if ($idadeFinal == "" && $idadeInicial == "") {
    } elseif ($idadeFinal == "" && $idadeInicial != "") {
        $whereIdade = " AND ed47_d_nasc = '$idadeInicial'";
    } elseif ($idadeInicial == "" && $idadeFinal != "") {
        $whereIdade = " AND ed47_d_nasc='$idadeFinal'";
    } else {
        if ($idadeInicial > $idadeFinal) {
            $whereIdade  = " AND (ed47_d_nasc >= '$idadeFinal' AND ed47_d_nasc <= '$idadeInicial')";
        } elseif ($idadeInicial < $idadeFinal) {
            $whereIdade  = " AND (ed47_d_nasc >= '$idadeInicial' AND ed47_d_nasc <= '$idadeFinal')";
        } else {
            $whereIdade  = " AND ed47_d_nasc='$idadeInicial'";
        }
    }

    $sGroup = null;
    $lFiliacao = strstr($campos, "filiacao1") || strstr($campos, "filiacao2");

        $sGroup = "ed47_v_nome,
                  ed60_i_numaluno,
                  ed47_v_cep,
                  ed47_d_nasc,
                  ed60_matricula,
                  ed60_c_situacao,
                  ed47_v_sexo,
                  ed47_v_email,
                  ed47_v_pai,
                  ed47_v_mae,
                  ed47_v_mae,
                  ed47_v_pai,
                  ed47_i_codigo,
                  ed60_d_datamatricula,
                  ed60_d_datasaida,
                  ed47_certidaomatricula,
                  ed76_d_data,
                  ed47_v_ident,
                  ed47_v_cpf,
                  ed47_v_cnh,
                  ed47_c_codigoinep,
                  ed47_c_nis,
                  ed47_cartaosus,
                  ed60_i_numaluno,
                  ed60_c_ativa,
                  cidadao.ov02_cnpjcpf,
                  ed261_c_nome,
                  ed47_tiposanguineo,
                  ed76_c_tipo,
                  escolaprimat.ed18_c_nome,
                  escolaproc.ed82_c_nome,
                  ed228_c_descr,
                  ed60_i_codigo,
                  alunonecessidade.ed214_i_aluno "
                  ;
        $sGroup .= ", ". $ordenacao;

        $campos .= ", ed47_v_nomesocial, array_agg(cidadao2.ov02_cnpjcpf)::text[] as cpf_filiacao ";

    $sOrdenacao = $ordenacao.", ed60_c_ativa";
    $sWhereMatricula  = "    ed60_i_turma = {$oDadosTurmaSerie->ed57_i_codigo}";
    $sWhereMatricula .= " AND ed221_i_serie = {$oDadosTurmaSerie->ed223_i_serie} {$condicao}";
    $sWhereMatricula .= " $whereIdade";
    $sSqlMatricula    = $oDaoMatricula->sql_query_naturalidade_aluno("", $campos, $sOrdenacao, $sWhereMatricula, $sGroup);
    $rsMatricula      = $oDaoMatricula->sql_record($sSqlMatricula);
    $iLinha2          = $oDaoMatricula->numrows;

    if ($iLinha2 == 0) {
        $oPdf->cell(195, 4, "Turma não possui nenhum aluno matriculado.", "", $next, "C", 0);
        continue;
    }

    $limite = $orientacao == "P" ? 52 : 32;
    $cont   = 0;

    for ($iContFor3 = 0; $iContFor3 < $iLinha2; $iContFor3++) {
        $oDadosAluno = db_utils::fieldsMemory($rsMatricula, $iContFor3);
        $oDadosAluno->ed47_v_nome = !empty($oDadosAluno->ed47_v_nomesocial) ?
        $oDadosAluno->ed47_v_nomesocial : $oDadosAluno->ed47_v_nome;

        if ($lFiliacao) {
            $cpfFiliacao = str_replace("{", "", $oDadosAluno->cpf_filiacao);
            $cpfFiliacao = str_replace("}", "", $cpfFiliacao);
            $cpfsFiliacao = explode(',', $cpfFiliacao);
            $cpfsFiliacao1 = implode("','", $cpfsFiliacao);

            $daoCidadao = new cl_cidadao();
            $sqlFiliacao = $daoCidadao->sql_query_file(null, null, '*', '', "ov02_cnpjcpf in ('$cpfsFiliacao1')");
            $rsFiliacao = db_query($sqlFiliacao);
            $arrayFiliacao = [];
            while ($filiacao = pg_fetch_array($rsFiliacao)) {
                $key = str_replace(' ', '', $filiacao['ov02_nome']);
                $arrayFiliacao[$key] = $filiacao['ov02_cnpjcpf'];
            }

            $oDadosAluno->cpf_filiacao1 = '';
            if (strstr($campos, "filiacao1")) {
                $key1 = str_replace(' ', '', $oDadosAluno->filiacao1);
                if (array_key_exists($key1, $arrayFiliacao)) {
                    $oDadosAluno->cpf_filiacao1 = $arrayFiliacao[$key1];
                }
            }

            $oDadosAluno->cpf_filiacao2 = '';
            if (strstr($campos, "filiacao2")) {
                $key2 = str_replace(' ', '', $oDadosAluno->filiacao2);
                if (array_key_exists($key2, $arrayFiliacao)) {
                    $oDadosAluno->cpf_filiacao2 = $arrayFiliacao[$key2];
                }
            }
        }
        for ($iContFor1 = 0; $iContFor1 < count($aCamposCabecalho); $iContFor1++) {
            $next = 0;
            if ($iContFor1 == (count($aCamposCabecalho) -1)) {
                $next = 1;
            }

            if (trim($aCamposCabecalho[$iContFor1]) == "Meses") {
                for ($iContFor2 = 1; $iContFor2 <= 12; $iContFor2++) {
                    $next_mes = $next;
                    if ($iContFor2 < 12) {
                        $next_mes = 0;
                    }
                    $oPdf->cell($aLarguraCorrigida[$iContFor1]/12, 4, "", 1, $next_mes, "C", 0);
                }
            } elseif (pg_field_name($rsMatricula, $iContFor1) == "ed47_certidaomatricula") {
                $iMatricula = pg_result($rsMatricula, $iContFor3, $iContFor1);
                $sMatricula = substr($iMatricula, 0, 6)." ".substr($iMatricula, 6, 2)." ".
                      substr($iMatricula, 8, 2)." ".substr($iMatricula, 10, 4)." ".
                      substr($iMatricula, 14, 1)." ".substr($iMatricula, 15, 5)." ".
                      substr($iMatricula, 20, 3)." ".substr($iMatricula, 23, 7)." ".
                      substr($iMatricula, 30, 2);
                $oPdf->cell($aLarguraCorrigida[$iContFor1], 4, $sMatricula, 1, $next, $aCamposAlinha[$iContFor1], 0);
            } elseif (pg_field_name($rsMatricula, $iContFor1) == "anomes") {
                $sMes = pg_result($rsMatricula, $iContFor3, $iContFor1);
                $aMes = explode(",", $sMes);
                $iMes = str_replace("meses", " ", $aMes[1]);
                $oPdf->cell($aLarguraCorrigida[$iContFor1], 4, $iMes, 1, $next, $aCamposAlinha[$iContFor1], 0);
            } elseif (pg_field_name($rsMatricula, $iContFor1) == "idadedia") {
                $sDia = pg_result($rsMatricula, $iContFor3, $iContFor1);
                $aDia = explode(",", $sDia);
                $iDia = str_replace("dias", " ", $aDia[2]);
                $oPdf->cell($aLarguraCorrigida[$iContFor1], 4, $iDia, 1, $next, $aCamposAlinha[$iContFor1], 0);
            } elseif (pg_field_name($rsMatricula, $iContFor1) == "ed47_tiposanguineo") {
                $sTipoSanguineo = "Não informado";
                $iTipoSanguineo = pg_result($rsMatricula, $iContFor3, $iContFor1);

                if (!empty($iTipoSanguineo)) {
                    $sSqlTipoSanguineo = $oDaoTipoSanguineo->sql_query_file(null, "sd100_tipo", null, " sd100_sequencial = {$iTipoSanguineo}");
                    $rsTipoSanguineo   = $oDaoTipoSanguineo->sql_record($sSqlTipoSanguineo);
                    $sTipoSanguineo    = db_utils::fieldsMemory($rsTipoSanguineo, 0)->sd100_tipo;
                }

                $oPdf->cell($aLarguraCorrigida[$iContFor1], 4, $sTipoSanguineo, 1, $next, $aCamposAlinha[$iContFor1], 0);
            } elseif (pg_field_name($rsMatricula, $iContFor1) == "ed47_localizacaodiferenciada") {
                $localizacoes = array (
                    '' => "",
                    1 => "Área de assentamento",
                    2 => "Terra indígena",
                    3 => "Comunidade quilombola",
                    7 => "Não está em área de localização diferenciada"
                );
                $localizacaoAluno = $localizacoes[$oDadosAluno->ed47_localizacaodiferenciada];
                $oPdf->cell($aLarguraCorrigida[$iContFor1], 4, $localizacaoAluno, 1, $next, $aCamposAlinha[$iContFor1], 0);
            } else if (pg_field_name($rsMatricula, $iContFor1) == "ed214_i_aluno") {
                $sNecessidadeEspecial = pg_result($rsMatricula, $iContFor3, $iContFor1);
                if ($sNecessidadeEspecial != null) {
                    $sNecessidadeEspecial = "*";
                } else {
                    $sNecessidadeEspecial = '';
                }
                $oPdf->cell($aLarguraCorrigida[$iContFor1], 4, $sNecessidadeEspecial, 1, $next, $aCamposAlinha[$iContFor1], 0);
            } else {
                $sValor = "";

                if (in_array($aCamposCabecalho[$iContFor1], $aCamposConcatenados)) {
                    $sValor = pg_result($rsMatricula, $iContFor3, $iContFor1);
                } elseif (in_array($aCamposImpressao[$iContFor1], $aCamposData)) {
                    $sValor = pg_result($rsMatricula, $iContFor3, $iContFor1);
                    if (!empty($sValor)) {
                        $sValor = db_formatar($sValor, 'd');
                    }
                } else {
                    $sValor = isset($oDadosAluno->{$aCamposImpressao[$iContFor1]}) ?
                    $oDadosAluno->{$aCamposImpressao[$iContFor1]} :
                    "";

                    if ($aCamposImpressao[$iContFor1] == "ed47_v_mae as filiacao1") {
                        $sValor = $oDadosAluno->cpf_filiacao1;
                    }
                    if ($aCamposImpressao[$iContFor1] == "ed47_v_pai as filiacao2") {
                        $sValor = $oDadosAluno->cpf_filiacao2;
                    }
                    if ($aCamposImpressao[$iContFor1] == "cidadao.ov02_cnpjcpf") {
                        $sValor = $oDadosAluno->ov02_cnpjcpf;
                    }
                }
                cellFitFont($oPdf, $tamfonte, $aLarguraCorrigida[$iContFor1], 4, $sValor, 1, $next, $aCamposAlinha[$iContFor1], 0);
            }
        }
        if ($limite == $cont) {
            $oPdf->cell($somacampos, 4, "* Aluno repetindo a Etapa", 1, 1, "L", 0);
            $oPdf->line(10, 44, $somacampos + 10, 44);
            $oPdf->addpage($orientacao);
            $oPdf->ln(5);
            $oPdf->setfont('arial', 'b', $tamfonte);

            for ($iContFor1 = 0; $iContFor1 < count($aCamposCabecalho); $iContFor1++) {
                $next = 0;
                if ($iContFor1 == (count($aCamposCabecalho)-1)) {
                    $next = 1;
                }

                if (trim($aCamposCabecalho[$iContFor1]) == "Meses") {
                    for ($iContFor2 = 0; $iContFor2 < 12; $iContFor2++) {
                        $next_mes = $next;
                        if ($iContFor2 < 11) {
                            $next_mes = 0;
                        }

                        $oPdf->cell($aLarguraCorrigida[$iContFor1]/12, 4, $aMeses[$iContFor2], 1, $next_mes, "C", 0);
                    }
                } else {
                    $oPdf->cell($aLarguraCorrigida[$iContFor1], 4, $aCamposCabecalho[$iContFor1], 1, $next, "C", 0);
                }
            }

            $cont = -1;
        }

        $cont++;
    }

    $comeco = $cont-1;

    for ($iContFor3 = $comeco; $iContFor3 < $limite; $iContFor3++) {
        for ($iContFor1 = 0; $iContFor1 < count($aCamposCabecalho); $iContFor1++) {
            $next = 0;
            if ($iContFor1 == (count($aCamposCabecalho)-1)) {
                $next = 1;
            }

            if (trim($aCamposCabecalho[$iContFor1]) == "Meses") {
                for ($iContFor2 = 1; $iContFor2 <= 12; $iContFor2++) {
                    $next_mes = $next;
                    if ($iContFor2 < 12) {
                        $next_mes = 0;
                    }

                    $oPdf->cell($aLarguraCorrigida[$iContFor1]/12, 4, "", "LR", $next_mes, "C", 0);
                }
            } else {
                $oPdf->cell($aLarguraCorrigida[$iContFor1], 4, "", "LR", $next, "C", 0);
            }
        }
    }

    $oPdf->cell($somacampos, 5, "* Aluno repetindo a Etapa", 1, 1, "L", 0);
    $oPdf->line(10, 44, $somacampos + 10, 44);

    if ($oPdf->getY() > $oPdf->getH() - 50 && ($iContFor+1) < $iLinhas) {
        $oPdf->AddPage($orientacao);
    }
}
$dtRelatorio = date('d-m-Y', db_getsession("DB_datausu"));
$oPdf->Output('I', 'Relatório - Lista das turmas - ' . $dtRelatorio);
