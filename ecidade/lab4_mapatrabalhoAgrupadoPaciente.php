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

require_once(modification("fpdf151/pdf.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("model/laboratorio/Exame.model.php"));

function novoTitulo($oPdf, $dIni, $dFim)
{
    $lCor = false;
    $oPdf->setfont('arial', 'B', 11);

    if ($dIni != $dFim) {
        $sSep = ' a ';
    } else {
        $sSep = '';
        $dFim = '';
    }

    $oPdf->cell(190, 8, 'LISTA GERAL: ' . $dIni . $sSep . $dFim, 0, 1, 'L', $lCor);
}

function novoPaciente(PDF $oPdf, $sNome, $iCgs, $iIdade, $sMedico, $dData, $aCodigoExames, $iRequisicao)
{
    $limiteAtributosLinha = 8;
    $atributosImpressos = 0;
    $tamanhoBlocoRequisicaoExames = 30;
    $tamanhoQuadroAtributos = 22;
    $posicaoYAtual = $oPdf->getY();
    $alturaMaxima = 275;

    if ($posicaoYAtual + $tamanhoBlocoRequisicaoExames > $alturaMaxima) {
        $oPdf->AddPage();
    }

    $oPdf->setfont('arial', '', 7);
    dadosRequisicao($oPdf, $iRequisicao, $dData, $sNome, $iIdade, $iCgs, $sMedico);

    foreach ($aCodigoExames as $codigoExames) {
        $exames = new Exame($codigoExames);

        foreach ($exames->getAtributos() as $atributoExame) {
            if ($atributoExame->getCodigoReferencia() == "") {
                continue;
            }

            if ($atributosImpressos == $limiteAtributosLinha) {
                $atributosImpressos = 0;

                $oPdf->ln($tamanhoQuadroAtributos);
                $y = $oPdf->getY();

                if ($y > $alturaMaxima || $tamanhoQuadroAtributos + $y > $alturaMaxima) {
                    $oPdf->addPage();
                    dadosRequisicao($oPdf, $iRequisicao, $dData, $sNome, $iIdade, $iCgs, $sMedico);
                }
            }

            $x = $oPdf->getX();
            $y = $oPdf->getY();

            $oPdf->multiCell($tamanhoQuadroAtributos, 6, $exames->getSigla() . "(" . substr($atributoExame->getNome(), 0, 5) . ")", 1, 'C',
              1);
            $oPdf->setX($x);
            $oPdf->multicell($tamanhoQuadroAtributos, 14, "", 1, 'C', 0);
            $x += $tamanhoQuadroAtributos;
            $oPdf->setXY($x, $y);
            $atributosImpressos++;
        }
    }

    $oPdf->ln(24);
}

function dadosRequisicao(PDF $oPdf, $iRequisicao, $dData, $sNome, $iIdade, $iCgs, $sMedico)
{
    $lCor = false;
    $pipe = " | ";

    $sString = "Requisição: $iRequisicao $pipe Data: $dData $pipe Paciente: $sNome $pipe Idade: $iIdade ano(s) $pipe CGS: $iCgs";
    $oPdf->setfont('arial', 'b', 7);
    $oPdf->multicell(0, 4, $sString, 0, 1, 'L', $lCor);
    $oPdf->ln(1);
    $oPdf->setfont('arial', '', 7);
    $oPdf->multicell(0, 4, "Médico: $sMedico", 0, 1, 'L', $lCor);
}

function formataData($dData, $iTipo = 1)
{
    if (empty($dData)) {
        return '';
    }

    if ($iTipo == 1) {
        $dData = explode('/', $dData);
        $dData = $dData[2] . '-' . $dData[1] . '-' . $dData[0];

        return $dData;
    }

    $dData = explode('-', $dData);
    $dData = $dData[2] . '/' . $dData[1] . '/' . $dData[0];

    return $dData;
}

$oDaolab_requisicao = new cl_lab_requisicao();
$dData_inicio = "";
$dData_fim = "";

if (!empty($datas)) {
    $datas = explode(',', $datas);
    $dData_inicio = formataData($datas[0]);
    $dData_fim = formataData($datas[1]);
}

/**
 * Emisso POR AGENDAMENTO
 * - Ser emitido o mapa de trabalho, de todas as requisies onde os exames esto AUTORIZADOS no perodo informado;
 * - Ordem por DATA:
 * -   ordenar as requisies por data de agendamento, no caso de empate, ver a requisio
 * - Ordem por REQUISIO:
 * -   ordenar as requisies por cdigo
 *
 * Emisso POR COLETA
 * - Ser emitido o mapa de trabalho, de todas as requisies onde os exames esto COLETADOS no perodo informado;
 * - Ordem por DATA:
 * -   ordenar as requisies por data e hora de coleta, no caso de empate, ver a requisio
 * - Ordem por REQUISIO:
 * -   ordenar as requisies por cdigo;
 */

$dDataAtual = date('Y-m-d', db_getsession('DB_datausu'));

$sCampos = " la21_d_data, ";
$sCampos .= " z01_i_cgsund, ";
$sCampos .= " trim(z01_v_nome) as z01_v_nome, ";
$sCampos .= " case ";
$sCampos .= "      when z01_d_nasc is null ";
$sCampos .= "           then null ";
$sCampos .= "      else fc_idade(z01_d_nasc, '$dDataAtual') ";
$sCampos .= "  end as idade, ";
$sCampos .= " trim(z01_nome) as z01_nome, ";
$sCampos .= " trim(la22_c_medico) as la22_c_medico, ";
$sCampos .= " la22_i_codigo, ";
$sCampos .= " la02_c_descr, ";
$sCampos .= " array_to_string(array_accum(la08_i_codigo), ',')  as la08_i_codigo  ";

$sGroupBy = " group by la21_d_data, z01_i_cgsund, z01_v_nome, idade, z01_nome, la22_c_medico, la22_i_codigo, la02_c_descr";
$sOrdem = " la22_i_codigo, la21_d_data";

if (!empty($iOrdemData) && $filtrarRelatorio == 1) {
    $sOrdem = " la21_d_data, la22_i_codigo ";
}

if (!empty($iOrdemRequisicao) && $filtrarRelatorio == 1) {
    $sOrdem = " la22_i_codigo ";
}

$sWhere = "  la21_c_situacao = '20 - Autorizado' ";

if (!empty($dData_inicio) && !empty($dData_fim)) {
    $sWhere .= " and la21_d_data between '{$dData_inicio}' and '{$dData_fim}' ";
}

if ($filtrarRelatorio == 2) {
    $sWhere = " la21_c_situacao = '30 - Coletado' ";

    if (!empty($dData_inicio) && !empty($dData_fim)) {
        $sWhere .= " and la32_d_data between '{$dData_inicio}' and '{$dData_fim}' ";
    }

    $sCampos .= " ,la32_d_data ";
    $sOrdem = " la32_d_data, la32_c_hora ";
    $sGroupBy .= " ,la32_d_data, la32_c_hora ";

    if (!empty($iOrdemRequisicao)) {
        $sOrdem = " la22_i_codigo ";
    }
}

if (!empty($iRequisicao)) {
    $sWhere .= " and la22_i_codigo = {$iRequisicao} ";
}

if (!empty($exame)) {
    $sWhere .= " and la08_i_codigo = {$exame} ";
}
if (!empty($labsetor)) {
    $sWhere .= " and la24_i_setor = {$labsetor} ";
}

if (!empty($laboratorio)) {
    $sWhere .= " and la02_i_codigo = {$laboratorio} ";
}

GLOBAL $iLinhas;
$sSql = $oDaolab_requisicao->sql_query_requiitem(null, $sCampos, $sOrdem, $sWhere . $sGroupBy);
$rs = $oDaolab_requisicao->sql_record($sSql);
$iLinhas = $oDaolab_requisicao->numrows;

if ($iLinhas == 0) {
    ?>
  <table width='100%'>
    <tr>
      <td align='center'>
        <font color='#FF0000' face='arial'>
          <b>Nenhum registro encontrado.<br>
            <input type='button' value='Fechar' onclick='window.close()'>
          </b>
        </font>
      </td>
    </tr>
  </table>
    <?php
    exit;
}

$oPdf = new PDF();
$oPdf->Open();
$oPdf->AliasNbPages();

$oDadosExame   = db_utils::fieldsMemory($rs, 0);

$head1 = "\nMapa de trabalho Agrupado por Paciente\n\n";
$head2 = "Laboratório: " . $oDadosExame->la02_c_descr;
$head3 = "Setor: {$nomesetor}";
$head4 = 'Perodo:';

if ($dData_inicio != $dData_fim) {
    $head4 = 'Perodo: ' . formataData($dData_inicio, 2) . ' a ' . formataData($dData_fim, 2);
} else {
    $head4 = 'Perodo: ' . formataData($dData_inicio, 2);
}

$head5 = $filtrarRelatorio == 1 ? "Por Agendamento" : "Por Coleta";

$oPdf->Addpage('P');

if ($iAtributo == 1) {
    $alt = $oPdf->getY();
    $oPdf->rect(10, 61, 190, 220, 'D');
}

$lCor = false;
$oPdf->setfillcolor(223);
$oPdf->SetAutoPageBreak(false);
$oPdf->setfont('arial', '', 8);

for ($iCont = 0; $iCont < $iLinhas; $iCont++) {
    $oDados = db_utils::fieldsMemory($rs, $iCont);

    $sCamposExame = " trim(la08_c_sigla) as la08_c_sigla, la08_i_codigo, la42_i_atributo ";
    $sOrdemExame = " trim(la08_c_sigla) ";
    $sWhereExame = " la08_i_codigo in ($oDados->la08_i_codigo) ";
    $oDaoLabExame = new cl_lab_exame();
    $sSqlExames = $oDaoLabExame->sql_query_exame_atributo(null, $sCamposExame, $sOrdemExame, $sWhereExame);
    $rsExames = db_query($sSqlExames);

    $aNomeExames = array();
    $aCodigoExames = array();
    $aCodigoAtributos = array();

    if ($rsExames && pg_num_rows($rsExames) > 0) {
        $iLinhasExame = pg_num_rows($rsExames);

        for ($i = 0; $i < $iLinhasExame; $i++) {
            $oDadosExame = db_utils::fieldsMemory($rsExames, $i);
            $aNomeExames[] = $oDadosExame->la08_c_sigla;
            $aCodigoExames[] = $oDadosExame->la08_i_codigo;
            $aCodigoAtributos[] = $oDadosExame->la42_i_atributo;
        }
    }

    $sMedico = empty($oDados->z01_nome) ? $oDados->la22_c_medico : $oDados->z01_nome;

    novoPaciente($oPdf, $oDados->z01_v_nome, $oDados->z01_i_cgsund, $oDados->idade, $sMedico,
      formataData($oDados->la21_d_data, 2), $aCodigoExames, $oDados->la22_i_codigo);
}

$oPdf->Output();
