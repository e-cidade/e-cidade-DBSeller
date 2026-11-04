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



use ECidade\V3\Extension\Data as ExtensionData;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));

$cldb_usuarios = new cl_db_usuarios;

db_postmemory($_POST);

$dbwhereid_usuariospermis = "";

$id_usuarios_selecionados = "";
if (isset($usuariossel) && count($usuariossel) > 0) {
    $virgula = "";
    foreach ($usuariossel as $indexArray => $id_usuario) {
        $id_usuarios_selecionados .= $virgula . $id_usuario;
        $virgula = ",";
    }
    $dbwhereid_usuariospermis = " and db_usuarios.id_usuario in (" . $id_usuarios_selecionados . ")";
}

$sCampos = "db_usuarios.id_usuario, login, nome, email";
$sWhere = "usuext = 0 and usuarioativo = 1 ";
$sWhere .= $dbwhereid_usuariospermis;
$sSql = $cldb_usuarios->sql_query_usuarios_cgm(null, $sCampos, "nome", $sWhere);

$rsUsers = $cldb_usuarios->sql_record($sSql);

if ($cldb_usuarios->numrows == 0) {
    db_redireciona("db_erros.php?fechar=true&db_erro=Verifique os dados informados ou não existem usuários para o filtro selecionado.");
}

$users = db_utils::getCollectionByRecord($rsUsers);

$userV2 = [];
foreach ($users as $user) {
	$extensionV3 = \ECidade\V3\Extension\Data::restore('Desktop');

	if (!$extensionV3->exists()) {
		break;
    }

    if (!\ECidade\V3\Extension\Manager::isEnabled('Desktop', $user->login)) {
        $userV2[] = $user;
    }
}

$usersTotal = count($users);
$v2Users = count($userV2);

if ( $v2Users == 0) {
    db_redireciona("db_erros.php?fechar=true&db_erro=Não existem usuários na versão 2.");
}

$pdf = new ECidade\Pdf\Pdf();
$pdf->init(false);

$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(false, 15);
$alt = 4;

$pdf->addTitulo("Relatórios de usuários que estão na versão 2 do e-cidade");

cabecalho($pdf, $alt);
$bDestaca = false;
foreach ($userV2 as $user) {
    
    if ($pdf->GetY() >= ($pdf->getH() - 15)) {
        cabecalho($pdf, $alt);
    }

    $pdf->cell(15, $alt, $user->id_usuario, 0, 0, "R", $bDestaca);
    $pdf->cell(30, $alt, $user->login, 0, 0, "L", $bDestaca);
    $pdf->cell(80, $alt, $user->nome, 0, 0, "L", $bDestaca);
    $pdf->cell(65, $alt, $user->email, 0, 1, "L", $bDestaca);

    if ($bDestaca == false) {
        $bDestaca = true;
    } else {
        $bDestaca = false;
    }
}

$pdf->ln();
$pdf->setfont("arial","B",8);
$pdf->cell(80, 5, "Total de usuários ativos:", 1, 0, "L", 1);
$pdf->cell(30, 5, $usersTotal, 1, 1, "R", 1);
$pdf->cell(80, 5, "Usuários na versão 2 do e-cidade:", 1, 0, "L", 1);
$pdf->cell(30, 5, $v2Users, 1, 1, "R", 1);

$pdf->output('I');

function cabecalho($pdf, $alt)
{
    $pdf->addPage();
    $pdf->SetFont("arial", "B", 8);
    $pdf->Cell(15, $alt, "ID Usuário", 1, 0, "C", 1);
    $pdf->Cell(30, $alt, "Login", 1, 0, "C", 1);
    $pdf->Cell(80, $alt, "Nome", 1, 0, "L", 1);
    $pdf->Cell(65, $alt, "Email", 1, 1, "L", 1);
    $pdf->SetFont("arial", '', 7);
}
