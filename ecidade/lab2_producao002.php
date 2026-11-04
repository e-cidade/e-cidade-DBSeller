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

include(modification("classes/db_lab_requiitem_classe.php"));
include(modification("dbforms/db_funcoes.php"));
require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");

function avisoNenhumRegistro()
{
  die("
    <table width='100%'>
      <tr>
      <td align='center'>
        <font color='#FF0000' face='arial'>
        <b>Nenhum registro encontrado<br>
        <input type='button' value='Fechar' onclick='window.close()'></b>
        </font>
      </td>
      </tr>
    </table>
  ");
}

function buscarRegistros()
{
  global $_GET;
  $get = \db_utils::postMemory($_GET);
  $cllab_requiitem = new \cl_lab_requiitem;

  $sAdd    = "";
  $sCampos = "";
  if ($get->iTipo == 2) {

    $sAdd = "||'__'||(coalesce(sd63_f_sa,0)+coalesce(sd63_f_sp,0))";

    $sCampos .= " la21_d_data,";
    $sCampos .= " z01_i_cgsund,";
    $sCampos .= " z01_v_nome,";
    $sCampos .= " la08_c_sigla,";

  }
  if ($get->sLaboratorios == 'TODOS') {
    $sCampos .= " '0' as la02_i_codigo, ";
    $sCampos .= " 'TODOS' as la02_c_descr, ";
  } else {
    $sCampos .= " la02_i_codigo, ";
    $sCampos .= " la02_c_descr, ";
  }
  $sCampos .= " (select sd63_c_nome||'__'||sd63_c_procedimento{$sAdd} from lab_conferencia
                inner join sau_procedimento on sd63_i_codigo = la47_i_procedimento
                inner join db_usuarios on id_usuario = la47_i_login
                where la47_i_requiitem=la21_i_codigo
                order by la47_d_data desc,la47_c_hora desc limit 1) as conferencia, ";
  $sCampos .= " la08_c_descr";

  $sWhere   = "exists (select * from lab_conferencia
                      inner join sau_procedimento on sd63_i_codigo = la47_i_procedimento
                      inner join db_usuarios on id_usuario = la47_i_login
                      where la47_i_requiitem=la21_i_codigo and
                      la47_d_data between '{$get->dInicio}' and '{$get->dFim}'
                      order by la47_d_data desc,la47_c_hora desc limit 1)";
  if ($get->sLaboratorios != 'TODOS' && $get->sLaboratorios !== '') {
    $sWhere  .= " and la02_i_codigo in ({$get->sLaboratorios}) ";
  }

  if ($get->setores !== '') {
      $setores = getSetoresFiltrados($get->setores);
      $setores = implode(', ', $setores['id']);
      $sWhere  .= " and la24_i_setor in ({$setores}) ";
  }
  if ($get->exames !== '') {
      $sWhere  .= " and la08_i_codigo in ({$get->exames}) ";
  }
  if ($get->iTipo == 1) {
    $sCampos .= " , la21_i_codigo ";
    $order   = " la02_i_codigo ";
  }	else {
    $order   = " la21_d_data asc,z01_v_nome,la02_c_descr ";
  }

  $sSql = $cllab_requiitem->sql_query2("",$sCampos,$order,$sWhere);

  if ($get->iTipo == 1) {
    $sSql = "SELECT
        la02_i_codigo,
        la02_c_descr,
        conferencia,
        la08_c_descr,
        count(la21_i_codigo) as total
      FROM ({$sSql}) as x
      GROUP BY la02_i_codigo,la02_c_descr,la08_c_descr,conferencia
      ORDER BY la02_i_codigo";
  }

  $result = $cllab_requiitem->sql_record($sSql);

  if (!$result) {
    avisoNenhumRegistro();
  }

  return \db_utils::getCollectionByRecord($result);
}

function buscarTotaisAnalitico($dados)
{
  $totalExames = [];
  $totalColetas = 0;
  foreach ($dados as $dado) {
      $totalExames[] = $dado->la08_c_descr;
      $totalColetas++;
  }

  return (object)['exames' => count(array_unique($totalExames)), 'coletas' => $totalColetas];
}

function imprimiCabecalhoSintetico($pdf, $dado)
{
  $pdf->ln(5);
  $pdf->addpage('P');
  $pdf->setfont('arial', 'b', 10);
  $pdf->cell(190, 4, "Laboratorio: {$dado->la02_c_descr}", 1, 1, "C", 0);
  $pdf->cell(30, 4, "Procedimento", 0, 0, "C", 0);
  $pdf->cell(100, 4, "Descrição", 0, 0, "C", 0);
  $pdf->cell(50, 4, "Exame", 0, 0, "C", 0);
  $pdf->cell(10, 4, "Total", 0, 1, "C", 0);
}

function imprimiLinhaSintetico($pdf, $dado)
{  
  $pdf->setfont('arial', '', 7);
  $aProc = explode("__", $dado->conferencia);
  $procedimento = $aProc[1];

  $pdf->cell(30, 4, $procedimento, 0, 0, "L", 0);  
  $posY = $pdf->getY();
  $pdf->Multicell(100, 4, $aProc[0], 0,"L",0);
  $posFinalDescricao = $pdf->getY();
  $posX = $pdf->getX();  
  $pdf->setXY($posX+130,$posY);
  
  $pdf->Multicell(50,4,"{$dado->la08_c_descr}",0,"C",0);  
  $posFinalExame = $pdf->getY();
  $pdf->setXY($posX+180,$posY);
  $pdf->cell(10, 4, "{$dado->total}", 0, 1, "R", 0);
  
  if($posFinalDescricao > $posFinalExame){
    $pdf->setY($posFinalDescricao+4);
  } else {
    $pdf->setY($posFinalExame+4);
  }
  
  return $dado->total;
}

function imprimiTotalSintetico($pdf, $total)
{
  $pdf->setfont('arial', 'b', 10);
  $pdf->cell(190, 4, "TOTAL DE EXAMES LIBERADOS: {$total}" , 1, 1, "R", 0);
}

function imprimirDadosSintetico($pdf, $dados, $setoresFiltrados)
{
    $lab = '';
    $total = 0;

    foreach ($dados as $dado) {
        if ($dado->la02_i_codigo != $lab) {
            if ($lab != '') {
                imprimiTotalSintetico($pdf, $total);
                $total = 0;
            }
            $totalSinteticos = buscarTotaisSintetico($dados, $dado->la02_i_codigo);
            $pdf->addTitulo("", 3);
            $pdf->addTitulo("TOTAL DE EXAMES: $totalSinteticos[exames]", 4);
            $pdf->addTitulo("TOTAL DE EXAMES LIBERADOS: $totalSinteticos[coleta]", 5);
            $pdf->addTitulo("LABORATÓRIO: {$dado->la02_c_descr}", 6);
            imprimiSetoresFiltrados($pdf, $setoresFiltrados, 7);
            imprimiCabecalhoSintetico($pdf, $dado);
            $lab = $dado->la02_i_codigo;
        }

    if ($pdf->getAvailHeight() < 8) {
      imprimiCabecalhoSintetico($pdf, $dado);
    }

    imprimiLinhaSintetico($pdf, $dado);
    $total += $dado->total;
  }
  imprimiTotalSintetico($pdf, $total);
}

function imprimiCabecalhoAnalitico($pdf, $dado)
{
  $data = db_formatar($dado->la21_d_data, 'd');

  $pdf->setfont('arial', 'b', 8);
  $pdf->cell(20, 4, $data, "T", 0, "L", 0);
  $pdf->cell(30, 4, $dado->z01_i_cgsund, "T" ,0, "L", 0);
  $pdf->cell(70, 4, $dado->z01_v_nome, "T", 0, "L", 0);
  $pdf->cell(25, 4, 'Código', "T", 0, "C", 0);
  $pdf->cell(45, 4, 'Valor Procedimento', "T", 1, "R", 0);
}

function imprimiInfoLaboratorio($pdf, $dado)
{
  $pdf->cell(20, 4, '', 0, 0, "L", 0);
  $pdf->cell(30, 4, '', 0, 0, "L", 0);
  $pdf->cell(70, 4, $dado->la02_c_descr, 0, 1, "L", 0);
}

function imprimiLinhaAnalitico($pdf, $dado)
{
  $aProc = explode("__", $dado->conferencia);
  $procedimento = $aProc[1];
  $valor = $aProc[2];

  $pdf->setfont('arial', '', 8);
  $pdf->cell(20, 4, '', 0, 0, "L", 0);
  $pdf->cell(30, 4, $dado->la08_c_sigla, 0, 0, "L", 0);
  $posY = $pdf->getY();
  $pdf->Multicell(70, 4, $dado->la08_c_descr, 0,"L",0);
  $posFinalExame = $pdf->getY();
  $posX = $pdf->getX();
  $pdf->setXY($posX+120,$posY);      
  $pdf->cell(25, 4, $procedimento, 0, 0, "C", 0);
  $pdf->cell(45, 4, "R$ " . number_format($valor, 2, ',', ''), 0, 1, "R", 0);
  
  $pdf->setY($posFinalExame+4);
}

function imprimiTotalAnalitico($pdf, $total)
{
  $pdf->setfont('arial', 'b', 8);
  $pdf->cell(20, 4, '', 0, 0, "L", 0);
  $pdf->cell(30, 4, '', 0, 0, "L", 0);
  $pdf->cell(70, 4, '', 0, 0, "L", 0);
  $pdf->cell(25, 4, '', 0, 0, "C", 0);
  $pdf->cell(45, 4, "R$ " . number_format($total, 2, ',', ''), "T", 1, "R", 0);
}

function imprimirDadosAnalitico($pdf, $dados)
{
  $lab = 0;
  $cgs = 0;
  $total = 0;
  $somaTotal = 0;
  $first = true;

  foreach ($dados as $dado) {
    if (($cgs != $dado->z01_i_cgsund || $dado->la02_i_codigo != $lab) && $cgs != 0 && $lab != 0) {
      imprimiTotalAnalitico($pdf, $total);
      $total = 0;
    }

    if ($pdf->getAvailHeight() < 16 || $first) {
      $pdf->ln(5);
      $pdf->addpage('P');
      imprimiCabecalhoAnalitico($pdf, $dado);
      imprimiInfoLaboratorio($pdf, $dado);
      $cgs = $dado->z01_i_cgsund;
      $lab = $dado->la02_i_codigo;
    }

    if ($cgs != $dado->z01_i_cgsund) {
      imprimiCabecalhoAnalitico($pdf, $dado);
      $cgs = $dado->z01_i_cgsund;
      $lab = 0;
    }

    if ($dado->la02_i_codigo != $lab || $cgs != $dado->z01_i_cgsund) {
      imprimiInfoLaboratorio($pdf, $dado);
      $lab = $dado->la02_i_codigo;
    }
    $first = false;

    imprimiLinhaAnalitico($pdf, $dado);
    $total += explode("__", $dado->conferencia)[2];
    $somaTotal += explode("__", $dado->conferencia)[2];
  }
  imprimiTotalAnalitico($pdf, $total);
  imprimiSomaTotalAnalitico($pdf, $somaTotal);
}

function buscarTotaisSintetico($dados, $laboratorio)
{
    $totais = [];
    $contadorExames = 0;
    $contadorColeta = 0;

    foreach ($dados as $dado) {
        if ($dado->la02_i_codigo === $laboratorio) {
            $contadorExames++;
            $contadorColeta += $dado->total;
        }
    }
    $totais['exames'] = $contadorExames;
    $totais['coleta'] = $contadorColeta;

    return $totais;
}

function getSetoresFiltrados($setores) {
    $arraySetores = explode(',', $setores);
    $dadosSetores = [];

    if (empty($setores)) {
        return false;
    }

    foreach ($arraySetores as $setor) {
        if (is_numeric($setor)) {
            $dadosSetores['id'][] = $setor;
            continue;
        }
        $dadosSetores['descricao'][] = trim($setor);
    }
    return $dadosSetores;
}

function setoresFiltradosCabecalho($setores) {
    $dadoSetor = [];

    if (!$setores) {
        return false;
    }

    if (count($setores['id']) <= 5) {
        foreach (array_combine($setores['id'], $setores['descricao']) as $key => $setor) {
            $dadoSetor[] = "{$key} - {$setor}";
        }
        return $dadoSetor;
    }
    return $setores['id'];
}

function imprimiSetoresFiltrados($pdf, $setoresFiltrados, $chaveTitulo) {
    if ($setoresFiltrados) {
        $strSetores = mb_convert_encoding(implode(', ', $setoresFiltrados), 'ISO-8859-1', 'UTF-8');
        $pdf->addTitulo("SETOR: {$strSetores}", $chaveTitulo);
    }
}

function imprimiSomaTotalAnalitico($pdf, $somaTotal)
{
    $pdf->setfont('arial', 'b', 10);
    $somaTotal = number_format($somaTotal, 2, ',', '');
    $pdf->cell(190, 4, "VALOR TOTAL DE EXAMES LIBERADOS: R$ {$somaTotal}" , 1, 1, "R", 0);
}

$dados = buscarRegistros();
$setoresFiltrados = setoresFiltradosCabecalho(getSetoresFiltrados($_GET['setores']));

$pdf = new ECidade\Pdf\Pdf();
$pdf->init(false);
$pdf->AliasNbPages();
$pdf->addTitulo("RELATÓRIO DE PRODUTIVIDADE", 1);
$pdf->addTitulo("PERIODO: {$_GET['dInicio']} até {$_GET['dFim']}", 2);

if ($_GET['iTipo'] == 1) {
  imprimirDadosSintetico($pdf, $dados, $setoresFiltrados);
} else {
  $totais = buscarTotaisAnalitico($dados);
  $pdf->addTitulo("TOTAL DE EXAMES: {$totais->exames}", 3);
  $pdf->addTitulo("TOTAL DE EXAMES LIBERADOS: {$totais->coletas}", 4);
  imprimiSetoresFiltrados($pdf, $setoresFiltrados, 5);
  imprimirDadosAnalitico($pdf, $dados);
}

$pdf->output('I');
?>
