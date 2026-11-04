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
require_once(modification("libs/db_sql.php"));
require_once(modification("libs/db_utils.php"));


parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

$iEscola = db_getsession("DB_coddepto");

if(empty($ano)){
    $ano = db_getsession("DB_anousu");
}

$head2 = "RELATÓRIO DE PROFESSORES";
$head3 = "E OUTROS PROFISSIONAIS - $ano";

if ($formato == "PDF") {
    $oPdf = new PDF();
    $oPdf->Open();
    $oPdf->AliasNbPages();
    $oPdf->setfillcolor(235);
    $logo = db_query($conn,"SELECT logo FROM db_config WHERE codigo = ".db_getsession("DB_instit"));
    $oPdf->Image('imagens/files/'.pg_result($logo,0,"logo"),7,3,26,28);
}

$sSql = "SELECT DISTINCT
TRIM(ed18_c_nome)  AS ESCOLA,
TRIM(ed57_c_descr) AS TURMA,
TRIM(ed11_c_descr) AS etapa,
CASE
     WHEN ed20_i_tiposervidor = 1 THEN TRIM(cgmrh.z01_nome)
     WHEN ed20_i_tiposervidor = 2 THEN  TRIM(cgmcgm.z01_nome)
     ELSE 'Turma não possui outros profissionais vinculados'
END AS nome,
CASE
     WHEN ed01_c_descr IS NULL THEN ' - '
     ELSE TRIM(ed01_c_descr)
END AS funcao,
'' AS disciplina,
2 as ordem
FROM turma
LEFT JOIN turmaoutrosprofissionais ON ed57_i_codigo          = ed347_turma
      LEFT JOIN rechumano          ON ed20_i_codigo          = ed347_rechumano
      LEFT JOIN rechumanoescola    ON ed75_i_rechumano       = ed20_i_codigo
      LEFT JOIN rechumanoativ      ON ed22_i_rechumanoescola = ed75_i_codigo
      LEFT JOIN rechumanocgm       ON ed285_i_rechumano      = ed20_i_codigo
      LEFT JOIN rechumanopessoal   ON ed284_i_rechumano      = ed20_i_codigo
      LEFT JOIN rhpessoal          ON ed284_i_rhpessoal      = rh01_regist
      LEFT JOIN cgm AS cgmcgm      ON cgmcgm.z01_numcgm      = rechumanocgm.ed285_i_cgm
      LEFT JOIN cgm AS cgmrh       ON cgmrh.z01_numcgm       = rhpessoal.rh01_numcgm
      LEFT JOIN atividaderh        ON ed01_i_codigo          = ed22_i_atividade
      LEFT JOIN rhregime           ON rh30_codreg            = ed20_i_rhregime
     INNER JOIN escola             ON ed18_i_codigo          = ed57_i_escola
     INNER JOIN calendario         ON ed52_i_codigo          = ed57_i_calendario
     INNER JOIN regencia           ON ed59_i_turma           = ed57_i_codigo
     INNER JOIN serie              ON ed11_i_codigo          = ed59_i_serie
WHERE ed18_i_codigo = {$iEscola}
  AND ed18_i_codigo = ed75_i_escola
  AND ed52_i_ano = {$ano}
  AND ed22_ativo = true
  AND ed22_datafim IS NULL
 UNION ALL
SELECT 
      ed18_c_nome AS escola,
      ed57_c_descr AS turma,
      ed11_c_descr AS etapa,
      CASE
        WHEN ed20_i_tiposervidor = 1 THEN cgmrh.z01_nome
        WHEN ed20_i_tiposervidor = 2 THEN cgmcgm.z01_nome
        ELSE 'Turma não possui professores vinculados'
      END AS nome,
      'PROFESSOR' AS funcao,
      ARRAY_TO_STRING(ARRAY_ACCUM(DISTINCT TRIM(ed232_c_abrev)),' / ') AS disciplina,
      1 as ordem
FROM escola
INNER JOIN calendarioescola ON ed38_i_escola = ed18_i_codigo
INNER JOIN calendario       ON ed52_i_codigo = ed38_i_calendario
INNER JOIN turma            ON ed57_i_calendario = ed52_i_codigo
INNER JOIN regencia         ON ed59_i_turma = ed57_i_codigo
INNER JOIN disciplina       ON ed12_i_codigo = ed59_i_disciplina
INNER JOIN caddisciplina    ON ed232_i_codigo = ed12_i_caddisciplina
 LEFT JOIN regenciahorario  ON ed58_i_regencia = ed59_i_codigo AND ed58_ativo = 't'
INNER JOIN serie            ON ed11_i_codigo = ed59_i_serie
 LEFT JOIN rechumano        ON ed20_i_codigo = ed58_i_rechumano
 LEFT JOIN rechumanoescola  ON ed75_i_rechumano = ed20_i_codigo
 LEFT JOIN rechumanocgm     ON ed285_i_rechumano = ed20_i_codigo
 LEFT JOIN cgm AS cgmcgm    ON cgmcgm.z01_numcgm = rechumanocgm.ed285_i_cgm
 LEFT JOIN rechumanopessoal ON rechumanopessoal.ed284_i_rechumano = rechumano.ed20_i_codigo
 LEFT JOIN rhpessoal        ON rhpessoal.rh01_regist = rechumanopessoal.ed284_i_rhpessoal
 LEFT JOIN cgm AS cgmrh     ON cgmrh.z01_numcgm = rhpessoal.rh01_numcgm
 WHERE ed18_i_codigo = {$iEscola}
  AND  ed52_i_ano = {$ano}
  AND  ed18_i_codigo = ed75_i_escola
GROUP BY 1,2,3,4,5
ORDER BY 1,2,3,7,5,4";

$rsDados = db_query($sSql);
$oDados =  db_utils::fieldsMemory($rsDados, 0);
$head6 = $iEscola.' - '.$oDados->escola;

$iLinhas = pg_num_rows($rsDados);

if (!is_null($tipo)){
    if ($tipo == 0 || $tipo == 1) {
        $iLinhas = pg_num_rows($rsDados);
    }
}

if ($iLinhas == 0) {
    db_redireciona("db_erros.php?fechar=true&db_erro=Nenhum registro encontrado para a escola.");
} 

if($iLinhas>0) {
    $nomeEscola = '';
    $nomeTurma = '';
    $nomeFuncao = '';
    $nomeEtapa = '';
    $outrosProfissionais = false;
    $iCont = 1;
    for ($iInd = 0; $iInd < $iLinhas; $iInd ++) {
        
        $oDados =  db_utils::fieldsMemory($rsDados, $iInd);

        if ($formato == "PDF") {
            
            $logo = db_query($conn,"SELECT logo FROM db_config WHERE codigo = ".db_getsession("DB_instit"));
            $oPdf->Image('imagens/files/'.pg_result($logo,0,"logo"),7,3,26,28);
            if ( $oPdf->gety() > $oPdf->h - 0 || $iInd == 0) {
                $oPdf->addpage();
                $oPdf->ln(1);
            }
            if($nomeEscola != $oDados->escola) {
                $nomeEscola = $oDados->escola;
            }
            if($nomeTurma != $oDados->turma) {
                if(!empty($nomeTurma)) { $oPdf->ln(5); }
                $nomeTurma = $oDados->turma;
                $nomeEtapa = '';
                $iCont = 1;
                $oPdf->setfont('arial','b',12);
                $oPdf->cell(192,5,"TURMA: " . $oDados->turma,"B",1,"L",0);
                $oPdf->ln(1);
            }

            if($nomeEtapa != $oDados->etapa) {
                if(!empty($nomeEtapa)) { $oPdf->ln(5); }
                $outrosProfissionais = false;
                $nomeEtapa = $oDados->etapa;
                $iCont = 1;
                $oPdf->setfont('arial','b',8);
                $oPdf->cell(192,5,"ETAPA: " . $oDados->etapa,"B",1,"L",0);
                $oPdf->ln(1);
            }

            if($nomeFuncao != $oDados->funcao) {
                $nomeFuncao = $oDados->funcao;
                $iCont = 1;
                $oPdf->setfont('arial','b',8);
                if($nomeFuncao == 'PROFESSOR') {
                    $oPdf->cell(81,5,"FUNÇÃO: " . $oDados->funcao,"B",0,"L",0);
                    $oPdf->cell(111,5,"DISCIPLINAS","B",1,"L",0);
                    $oPdf->ln(1);
                } else {
                    if(!$outrosProfissionais) {
                        $outrosProfissionais = true;
                        $oPdf->cell(81,5,"OUTROS PROFISSIONAIS","B",0,"L",0);
                        $oPdf->cell(111,5,"FUNÇÃO","B",1,"L",0);
                        $oPdf->ln(1);
                    }
                }
            }
            escreveDadosPassagem($oPdf, $oDados, ++$iCont%2);
        }
    }
}

function escreveDadosPassagem($oPdf, $oDados, $i) {
    $oPdf->setfont('arial','',7);
    $oPdf->cell(81,5,$oDados->nome,0,0,"L",$i);
    if($oDados->funcao == "PROFESSOR") {
        $oPdf->cell(111,5,$oDados->disciplina,0,1,"L",$i);
    } else {
        $oPdf->cell(111,5,$oDados->funcao,0,1,"L",$i);
    }
}

if ($formato == "PDF") { 
    $oPdf->Output();
}
?>
