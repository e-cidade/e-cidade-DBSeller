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

function novoTitulo($oPdf, $dIni, $dFim) {

  $lCor = false;
  $oPdf->setfont('arial','B',11);

  if($dIni != $dFim) {
    $sSep = ' a ';
  } else {

    $sSep = '';
    $dFim = '';
  }

  $oPdf->cell(190, 8, 'LISTA GERAL: ' . $dIni.$sSep.$dFim, 0, 1, 'L', $lCor);
}

function novoExame(PDF $oPdf, $iNum, $sNome, $iCgs, $iIdade, $sMedico, $exames, $dData, $iCont, $iAtributo, $iRequisicao, $dData_inicio, $dData_fim ) {
                                                                    
  $lCor = false;
  
  $oPdf->setfont('arial', '', 7);  

  foreach ($exames as $exame) {    
    $y = $oPdf->getY();
    if ($y > 225) {
      $oPdf->addPage();
    }
    $oPdf->setfont('arial', 'b', 7);
    $oPdf->cell(15, 12, "Exame: " . $exame->tipoExame, 0, 0, 'L', $lCor );

    $oPdf->setfont('arial', '', 7);
    $oPdf->ln(8);
    $quebra = false;
    
    foreach($exame->pacientes as $paciente) {
      $quebra = false;
      $x = $oPdf->getX();
      $y = $oPdf->getY();      
      $oPdf->multicell(34, 7, "Req: $paciente->requisicao\n" . "CGS:" . $paciente->cgsund . " " . $paciente->idade . " A - " . $paciente->sexo, 1, 'C', 1);
      $oPdf->setX($x);
      $oPdf->multicell(34, 15, "", 1, 'C', 0);
      $x += 34;

      $oPdf->setXY($x, $y);
      
      if ($x >= 180) {
        $quebra = true;
        $oPdf->ln(32);
        if ($y > 220) {
          $oPdf->addPage();
          $oPdf->setfont('arial', 'b', 7);
          $oPdf->cell(15, 8, "Exame: " . $exame->tipoExame, 0, 0, 'L', $lCor );
          $oPdf->setfont('arial', '', 0);
          $oPdf->ln();
        }
      }
    }   

    if (!$quebra) {
      $oPdf->ln(26);
    }
  }
}  

function formataData($dData, $iTipo = 1) {

  if (empty($dData)) {
    return '';
  }

  if($iTipo == 1) {

    $dData = explode('/',$dData);
    $dData = $dData[2].'-'.$dData[1].'-'.$dData[0];
    return $dData;
  }

 $dData = explode('-',$dData);
 $dData = $dData[2].'/'.$dData[1].'/'.$dData[0];
 return $dData;
}

$oDaolab_requisicao = new cl_lab_requisicao();

$dData_inicio       = "";
$dData_fim          = "";

if (!empty($datas)) {

  $datas              = explode(',',$datas);
  $dData_inicio       = formataData($datas[0]);
  $dData_fim          = formataData($datas[1]);
}

/**
 * Emissão POR AGENDAMENTO
 * - Será emitido o mapa de trabalho, de todas as requisições onde os exames estão AUTORIZADOS no período informado;
 * - Ordem por DATA:
 * -   ordenará as requisições por data de agendamento, no caso de empate, verá a requisição
 * - Ordem por REQUISIÇÃO:
 * -   ordenará as requisições por código
 *
 * Emissão POR COLETA
 * - Será emitido o mapa de trabalho, de todas as requisições onde os exames estão COLETADOS no período informado;
 * - Ordem por DATA:
 * -   ordenará as requisições por data e hora de coleta, no caso de empate, verá a requisição
 * - Ordem por REQUISIÇÃO:
 * -   ordenará as requisições por código;
 */

$dDataAtual = date('Y-m-d', db_getsession('DB_datausu'));

$sCampos  = " la21_d_data, ";
$sCampos .= " z01_i_cgsund, ";
$sCampos .= " trim(z01_v_nome) as z01_v_nome, ";
$sCampos .= " case ";
$sCampos .= "      when z01_d_nasc is null ";
$sCampos .= "           then null ";
$sCampos .= "      else fc_idade(z01_d_nasc, '$dDataAtual') ";
$sCampos .= "  end as idade, ";
$sCampos .= " trim(z01_nome) as z01_nome, ";
$sCampos .= " trim(la22_c_medico) as la22_c_medico, ";
$sCampos .= " la08_i_codigo, ";
$sCampos .= " la08_c_descr, ";
$sCampos .= " z01_v_sexo, ";
$sCampos .= " la02_c_descr, ";
$sCampos .= " array_to_string(array_accum(la22_i_codigo), ',')  as la22_i_codigo ";
$sGroupBy = " group by la21_d_data, z01_i_cgsund, z01_v_nome, la22_c_medico, la08_i_codigo, la08_c_descr, z01_v_sexo, la02_c_descr, z01_nome ";
$sOrdem   = " la08_c_descr ";

if (!empty($iOrdemData) && $filtrarRelatorio == 1) {
  $sOrdem = " la08_c_descr ";
}

if (!empty($iOrdemRequisicao) && $filtrarRelatorio == 1) {
  $sOrdem = " la08_c_descr ";
}

$sWhere = "  la21_c_situacao = '20 - Autorizado' ";

if ( !empty($dData_inicio) && !empty($dData_fim))  {
  $sWhere .= " and la21_d_data between '{$dData_inicio}' and '{$dData_fim}' ";
}

if ($filtrarRelatorio == 2 ) {

  $sWhere    = " la21_c_situacao = '30 - Coletado' ";
  if ( !empty($dData_inicio) && !empty($dData_fim))  {
    $sWhere .= " and la32_d_data between '{$dData_inicio}' and '{$dData_fim}' ";
  }


  $sCampos  .= " ,la32_d_data ";
  $sOrdem    = " la32_d_data, la32_c_hora ";
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
$sSql     = $oDaolab_requisicao->sql_query_requiitem(null, $sCampos, $sOrdem, $sWhere . $sGroupBy);
$rs       = $oDaolab_requisicao->sql_record($sSql);
$iLinhas  = $oDaolab_requisicao->numrows;

if($iLinhas == 0) {
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

$head1 = "\nMapa de trabalho Agrupado por Exame\n\n";
$head2 = "Laboratório: ". $oDadosExame->la02_c_descr;
$head3 = "Setor: {$nomesetor}";

$head4 = 'Período:';
if($dData_inicio != $dData_fim) {
  $head4 = 'Período: '.formataData($dData_inicio, 2).' a '.formataData($dData_fim, 2);
} else {
  $head4 = 'Período: '.formataData($dData_inicio, 2);
}
$head5 = $filtrarRelatorio == 1 ? "Por Agendamento" : "Por Coleta";

$oPdf->Addpage('P');
if ($iAtributo==1) {

 $alt = $oPdf->getY();
 $oPdf->rect(10,61,190,220,'D');
}

$lCor = false;
$oPdf->setfillcolor(223);
$oPdf->setfont('arial','',8);

// novoTitulo($oPdf, formataData($dData_inicio, 2), formataData($dData_fim, 2));

$exames = array();
for ($iCont = 0; $iCont < $iLinhas; $iCont ++) {

  $oDadosExame   = db_utils::fieldsMemory($rs, $iCont);
  
  $paciente = (object)array(
    'nome'  => $oDadosExame->z01_v_nome,
    'idade' => $oDadosExame->idade,    
    'sexo' =>  $oDadosExame->z01_v_sexo,
    'cgsund' => $oDadosExame->z01_i_cgsund,
    'requisicao' => $oDadosExame->la22_i_codigo
  );
  
  if (empty($exames[$oDadosExame->la08_i_codigo]->pacientes)) {
    $exames[$oDadosExame->la08_i_codigo] = (object)array(
      'codigo'    => $oDadosExame->la08_i_codigo,
      'tipoExame' => $oDadosExame->la08_c_descr,
      'pacientes' => array($paciente),
    );
  } else {
    $exames[$oDadosExame->la08_i_codigo]->pacientes[] = $paciente;
  }
}

$sMedico = empty($oDadosExame->z01_nome) ? $oDadosExame->la22_c_medico : $oDadosExame->z01_nome;

novoExame($oPdf, $iCont + 1, $oDadosExame->z01_v_nome, $oDadosExame->z01_i_cgsund, $oDadosExame->idade, $sMedico,
          $exames, formataData($oDadosExame->la21_d_data, 2), $iCont +1, $iAtributo, $oDadosExame->la22_i_codigo, $dData_inicio, $dData_fim);

$oPdf->Output();
