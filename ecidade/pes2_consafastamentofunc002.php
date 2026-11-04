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
use ECidade\Pdf\Pdf;

require_once("libs/db_stdlib.php");
require_once("libs/db_conecta.php");

db_postmemory($_POST);
parse_str($_SERVER["QUERY_STRING"]);

$clafasta = new cl_afasta();
$clrhpessoalmov = new cl_rhpessoalmov();

$sWhere = "rh02_anousu = {$ano} and rh02_mesusu = {$mes} and rh02_regist = {$matricula}";
$sSql = $clrhpessoalmov->sql_query_matricula_cgm(null,
                                                 db_getsession("DB_instit"),
                                                 "z01_nome, rh01_admiss",
                                                 null,
                                                 $sWhere);
$rsDadosServidor = $clrhpessoalmov->sql_record($sSql);
$oDadosServidor = db_utils::fieldsMemory($rsDadosServidor,0);

$sSql = $clafasta->sql_query_file(null,
                                  "case r45_situac
                                        when '2' then 'Sem Remuneração'
                                        when '3' then 'Acidente de trabalho'
                                        when '4' then 'Serviço Militar'
                                        when '5' then 'Licença Gestante'
                                        when '6' then 'Doença'
                                        when '7' then 'Sem Vencimentos/Sem Ônus'
                                        when '8' then 'Doença'
                                        when '9' then 'Prorrogação Licença Maternidade'
                                        when '10' then 'Licença para cuidar de Familiar'
                                        when '11' then 'Licença Prêmio'
                                   end as r45_situac,
                                   r45_dtafas,
                                   r45_dtreto,
                                   r45_dtlanc,
                                   r45_obs,
                                   r45_dtreto - r45_dtafas + 1 as dias",
                                   "r45_dtafas",
                                   "      r45_regist = {$matricula}
                                      and r45_anousu = {$ano}
                                      and r45_mesusu = {$mes}");
$rsDados = $clafasta->sql_record($sSql);
$iLinhas = $clafasta->numrows;

if ($iLinhas == 0){
    db_redireciona('db_erros.php?fechar=true&db_erro=Não existem Códigos cadastrados no período de '.$mes.' / '.$ano);
}

$pdf = new Pdf();
$pdf->init(false);
$pdf->AliasNbPages();
$pdf->setfont('arial','b',8);

$pdf->addTitulo("AFASTAMENTOS CADASTRADOS", 2);
$pdf->addTitulo("PERÍODO : ".db_formatar($oDadosServidor->rh01_admiss,'d')." até ".date('d/m/Y',db_getsession("DB_datausu")),4);
$pdf->addTitulo("NOME: ".$oDadosServidor->z01_nome,6);

$alt = 4;
$pre = 1;

for ($iInd = 0; $iInd < $iLinhas; $iInd++) {
    
    $oDados = db_utils::fieldsMemory($rsDados,$iInd);
    
    if ($pdf->getY() > $pdf->getH() - 30 || $iInd == 0) {
        
        $pdf->addPage('L');
        $pdf->setfont('arial','b',8);
        $pdf->cell(70,$alt,'Situação do afastamento',1,0,"C",1);
        $pdf->cell(25,$alt,'Inicio',1,0,"C",1);
        $pdf->cell(25,$alt,'Fim',1,0,"C",1);
        $pdf->cell(15,$alt,'Qtd dias',1,0,"C",1);
        $pdf->cell(25,$alt,'Data lançamento',1,0,"C",1);
        $pdf->cell(120,$alt,'Observações',1,1,"C",1);
        
    }
    
    if ($pre == 1) {
        $pre = 0;
    } else {
        $pre = 1;
    }
    
    $pdf->setfont('arial','',7);
    $pdf->cell(70,$alt,$oDados->r45_situac,0,0,"C",$pre);
    $pdf->cell(25,$alt,db_formatar( $oDados->r45_dtafas, "d"),0,0,"C",$pre);
    $pdf->cell(25,$alt,db_formatar( $oDados->r45_dtreto, "d"),0,0,"C",$pre);
    $pdf->cell(15,$alt,$oDados->dias,0,0,"C",$pre);
    $pdf->cell(25,$alt,db_formatar( $oDados->r45_dtlanc, "d"),0,0,"C",$pre);
    $pdf->multicell(120,$alt,$oDados->r45_obs,0,"L",$pre);

}

$pdf->Output();
