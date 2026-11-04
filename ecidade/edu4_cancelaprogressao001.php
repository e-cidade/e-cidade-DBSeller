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

require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

$escola = db_getsession("DB_coddepto");

$cltrocaserie                   = new cl_trocaserie;
$clturmaserieregimemat          = new cl_turmaserieregimemat;
$clmatricula                    = new cl_matricula;
$clmatriculaserie               = new cl_matriculaserie;
$clamparo                       = new cl_amparo;
$cldiariofinal                  = new cl_diariofinal;
$clparecerresult                = new cl_parecerresult;
$cldiarioresultado              = new cl_diarioresultado;
$cldiarioresultadorecuperacao   = new cl_diarioresultadorecuperacao;
$clpareceraval                  = new cl_pareceraval;
$clabonofalta                   = new cl_abonofalta;
$cltransfaprov                  = new cl_transfaprov;
$cldiarioavaliacao              = new cl_diarioavaliacao;
$claprovconselho                = new cl_aprovconselho;
$cldiario                       = new cl_diario;
$clmatriculamov                 = new cl_matriculamov;
$clalunotransfturma             = new cl_alunotransfturma;
$cltransfescolarede             = new cl_transfescolarede;
$cltransfescolafora             = new cl_transfescolafora;
$cllogmatricula                 = new cl_logmatricula;
$clalunocurso                   = new cl_alunocurso;
$clavaliacaoclassificacao       = new cl_avaliacaoclassificacao;
$clhistmpsdisc                  = new cl_histmpsdisc;
$clhistoricomps                 = new cl_historicomps;
$clalunopossib                  = new cl_alunopossib;
$clregencia                     = new cl_regencia;
$clhistorico                    = new cl_historico;
$oDaoMatriculaTurnoReferente    = new cl_matriculaturnoreferente();
$oDaoDiarioAvaliacaoAlternativa = new cl_diarioavaliacaoalternativa();

if (isset($incluir)) {
    db_inicio_transacao();
    try {
        $sql_exc = "SELECT DISTINCT ed95_i_codigo AS coddiario
                FROM diarioavaliacao
                INNER JOIN diario ON ed95_i_codigo = ed72_i_diario
                WHERE ed95_i_aluno = {$codigoaluno}
                    AND ed95_i_regencia IN
                        (SELECT ed59_i_codigo
                        FROM regencia
                        WHERE ed59_i_turma = {$codturmadest}
                            AND ed59_i_serie = {$codseriedest})";

        $result_exc = db_query($sql_exc);

        if (!$result_exc) {
            throw new Exception('Erro na consulta ao banco de dados!');
        } else {
            $msgPadrao = 'Erro ao tentar realizar a transação no banco de dados! Se o problema persistir, entre em contato com o suporte! ';
            $linhas_exc = pg_num_rows($result_exc);
            if ($linhas_exc > 0) {
                for ($i = 0; $i < $linhas_exc; $i++) {
                    db_fieldsmemory($result_exc, $i);
                    /* TABELA amparo */
                    $resultAmparo = $clamparo->excluir("", "ed81_i_diario = {$coddiario}" );
                    if (!$resultAmparo) {
                        throw new Exception($msgPadrao . $clamparo->erro_msg);
                    }

                    /* TABELA diariofinal */
                    $resultDiarioFinal = $cldiariofinal->excluir("", "ed74_i_diario = {$coddiario}");
                    if (!$resultDiarioFinal) {
                        throw new Exception($msgPadrao . $cldiariofinal->erro_msg);
                    }

                    /* TABELA diarioresultado */
                    $resultDiarioResultado = db_query("select ed73_i_codigo from diarioresultado where ed73_i_diario = {$coddiario}");
                    if (!$resultDiarioResultado) {
                        throw new Exception($msgPadrao . 'SQL: Consulta a tabela diarioresultado!');
                    }

                    /* TABELA parecerresult */
                    $sWhereParecerResult = " ed63_i_diarioresultado in (select ed73_i_codigo from diarioresultado where ed73_i_diario = {$coddiario})";
                    $resultParecerResult =  $clparecerresult->excluir("", $sWhereParecerResult);
                    if (!$resultParecerResult) {
                        throw new Exception($msgPadrao . $clparecerresult->erro_msg);
                    }

                    /* TABELA diarioresultadorecuperacao */
                    $sWhereDiarioResultadoRecuperacao = "ed116_diarioresultado in (select ed73_i_codigo from diarioresultado where ed73_i_diario = {$coddiario})";
                    $resultDiarioResultadoRecuperacao = $cldiarioresultadorecuperacao->excluir( "", $sWhereDiarioResultadoRecuperacao);
                    if(!$resultDiarioResultadoRecuperacao){
                        throw new Exception($msgPadrao . $cldiarioresultadorecuperacao->erro_msg);
                    }

                    /* TABELA diarioresultado */
                    $resultDiarioResultado = $cldiarioresultado->excluir( "", "ed73_i_diario = {$coddiario}" );
                    if (!$resultDiarioResultado) {
                        throw new Exception($msgPadrao . $cldiarioresultado->erro_msg);
                    }

                    /* TABELA pareceraval */
                    $sWhereParecerAval = "ed93_i_diarioavaliacao in (select ed72_i_codigo from diarioavaliacao where ed72_i_diario = {$coddiario})";
                    $resultParecerAval = $clpareceraval->excluir( "", $sWhereParecerAval );
                    if (!$resultParecerAval) {
                        throw new Exception($msgPadrao . $clpareceraval->erro_msg);
                    }

                    /* TABELA abonofalta */
                    $sWhereAbonoFalta = "ed80_i_diarioavaliacao in (select ed72_i_codigo from diarioavaliacao where ed72_i_diario = {$coddiario})";
                    $resultAbonoFalta = $clabonofalta->excluir( "", $sWhereAbonoFalta );
                    if (!$resultAbonoFalta) {
                        throw new Exception($msgPadrao . $clpareceraval->erro_msg);
                    }

                    /* TABELA diarioavaliacao */
                    $sqlCodigosDiarioAvaliacao = $cldiarioavaliacao->sql_query_file(null, 'ed72_i_codigo', null, "ed72_i_diario = {$coddiario}");
                    $rsDiarioAvaliacao = db_query($sqlCodigosDiarioAvaliacao);
                    if (!$rsDiarioAvaliacao) {
                        throw new Exception($msgPadrao . $cldiarioavaliacao->erro_msg);
                    }

                    /* TABELA transfaprov */
                    $resultTransfAprov = $cltransfaprov->excluir("", "ed251_i_diariodestino in ({$sqlCodigosDiarioAvaliacao}) or ed251_i_diarioorigem in({$sqlCodigosDiarioAvaliacao})");
                    if (!$resultTransfAprov) {
                        throw new Exception($msgPadrao . $cltransfaprov->erro_msg);
                    }

                    /* TABELA diarioavaliacao */
                    $resultDiarioAvaliacao = $cldiarioavaliacao->excluir("", "ed72_i_diario = {$coddiario}");
                    if (!$resultDiarioAvaliacao) {
                        throw new Exception($msgPadrao . $cldiarioavaliacao->erro_msg);
                    }

                    /* TABELA aprovconselho */
                    $resultAprovConselho = $claprovconselho->excluir("", "ed253_i_diario = {$coddiario}");
                    if (!$resultAprovConselho) {
                        throw new Exception($msgPadrao . $claprovconselho->erro_msg);
                    }

                    /* TABELA diarioavaliacaoalternativa */
                    $resultDiarioAvaliacaoAlternativa = $oDaoDiarioAvaliacaoAlternativa->excluir(null, " ed136_diario = {$coddiario} ");
                    if (!$resultDiarioAvaliacaoAlternativa) {
                        throw new Exception($msgPadrao . $oDaoDiarioAvaliacaoAlternativa->erro_msg);
                    }

                    /* TABELA diario */
                    $resultDiario = $cldiario->excluir("", "ed95_i_codigo = {$coddiario}");
                    if (!$resultDiario) {
                        throw new Exception($msgPadrao . $cldiario->erro_msg);
                    }
                }
            }

            /* TABELA matriculamov */
            $resultMatriculaMov = $clmatriculamov->excluir("", "ed229_i_matricula = {$matriculadest}");
            if (!$resultMatriculaMov) {
                throw new Exception($msgPadrao . $clmatriculamov->erro_msg);
            }

            /* TABELA alunotransfturma */
            $resultAlunoTransfTurma = $clalunotransfturma->excluir("", "ed69_i_matricula = {$matriculadest}");
            if (!$resultAlunoTransfTurma) {
                throw new Exception($msgPadrao . $clalunotransfturma->erro_msg);
            }

            /* TABELA transfescolarede */
            $resultTransfEscolaRede = $cltransfescolarede->excluir("", "ed103_i_matricula = {$matriculadest}");
            if (!$resultTransfEscolaRede) {
                throw new Exception($msgPadrao . $cltransfescolarede->erro_msg);
            }

            /* TABELA transfescolafora */
            $resultTransfEscolaFora = $cltransfescolafora->excluir("", "ed104_i_matricula = {$matriculadest}");
            if (!$resultTransfEscolaFora) {
                throw new Exception($msgPadrao . $cltransfescolafora->erro_msg);
            }

            /* TABELA matriculaserie */
            $resultMatriculaSerie = $clmatriculaserie->excluir("", "ed221_i_matricula = {$matriculadest}");
            if (!$resultMatriculaSerie) {
                throw new Exception($msgPadrao . $clmatriculaserie->erro_msg);
            }

            /* TABELA matriculaturnoreferente */
            $resultMatriculaTurnoReferente = $oDaoMatriculaTurnoReferente->excluir(null, "ed337_matricula = {$matriculadest}");
            if (!$resultMatriculaTurnoReferente) {
                throw new Exception($msgPadrao . $oDaoMatriculaTurnoReferente->erro_msg);
            }

            /* TABELA matricula */
            $resultMatricula = $clmatricula->excluir($matriculadest);
            if (!$resultMatricula) {
                throw new Exception($msgPadrao . $clmatricula->erro_msg);
            }
            $sCamposMatricula = "ed221_i_serie as serieant, ed11_i_sequencia as seqant";
            $sSqlMatricula = $clmatricula->sql_query("", $sCamposMatricula, "", "ed60_i_codigo = {$matriculaorig}");
            $result_seqant = $clmatricula->sql_record($sSqlMatricula);
            if (!$result_seqant) {
                throw new Exception($msgPadrao . $clmatricula->erro_msg);
            }
            db_fieldsmemory($result_seqant, 0);

            /* TABELA alunocurso */
            $sCamposAlunoCurso = "ed56_i_codigo";
            $sSqlAlunoCurso = $clalunocurso->sql_query_file("", $sCamposAlunoCurso, "", "ed56_i_aluno = {$codigoaluno}");
            $resultAlunoCurso = $clalunocurso->sql_record($sSqlAlunoCurso);

            if (!$resultAlunoCurso) {
                throw new Exception($msgPadrao . $clalunocurso->erro_msg);
            }
            $nLinhas = pg_num_rows($resultAlunoCurso);

            if($nLinhas>0){
                db_fieldsmemory($resultAlunoCurso, 0);
                /* UPDATE TABELA alunocurso */
                $clalunocurso->ed56_i_escola = $codescolaorig;
                $clalunocurso->ed56_i_base = $codbaseorig;
                $clalunocurso->ed56_i_calendario = $codcalendarioorig;
                $clalunocurso->ed56_c_situacao = 'MATRICULADO';
                $clalunocurso->ed56_i_baseant = null;
                $clalunocurso->ed56_i_calendarioant = null;
                $clalunocurso->ed56_c_situacaoant = '';
                $clalunocurso->ed56_i_codigo = $ed56_i_codigo;

                $resultAlterarAlunoCurso = $clalunocurso->alterar($ed56_i_codigo);
                if (!$resultAlterarAlunoCurso) {
                    throw new Exception($msgPadrao . $clalunocurso->erro_msg);
                }

                /* UPDATE TABELA alunopossib */
                $sSqlAlterarAlunoPossib = "UPDATE alunopossib SET
                    ed79_i_serie    = {$serieant},
                    ed79_i_turno    = {$codturnoorig},
                    ed79_i_turmaant = null,
                    ed79_c_resulant = '',
                    ed79_c_situacao = 'A'
                        WHERE ed79_i_alunocurso = {$ed56_i_codigo}";

                $resultAlterarAlunoPossib = db_query($sSqlAlterarAlunoPossib);
                if (!$resultAlterarAlunoPossib) {
                    throw new Exception($msgPadrao . 'Houve um erro ao tentar alterar a tabela alunopossib!');
                }
            }

            /* SELECT TABELA matricula */
            $sCamposMatricula3 = "ed60_d_datamodifant as datamodifant, turma.ed57_i_tipoturma";
            $sSqlMatricula3 = $clmatricula->sql_query("", $sCamposMatricula3, "", "ed60_i_codigo = {$matriculaorig}");
            $result_modif = $clmatricula->sql_record($sSqlMatricula3);
            if (!$result_modif) {
                throw new Exception($msgPadrao . $clmatricula->erro_msg);
            }
            db_fieldsmemory( $result_modif, 0 );
            $sDataModificacaoAnterior = !empty($datamodifant) ? $datamodifant : 'null';

            /* UPDATE TABELA matricula */
            $clmatricula->ed60_c_situacao     = 'MATRICULADO';
            $clmatricula->ed60_c_concluida    = 'N';
            $clmatricula->ed60_c_ativa        = 'S';
            $clmatricula->ed60_t_obs          = '';
            $clmatricula->ed60_d_datamodif    = $sDataModificacaoAnterior;
            $clmatricula->ed60_d_datamodifant = null;
            $clmatricula->ed60_d_datasaida    = null;
            $clmatricula->ed60_i_codigo       = $matriculaorig;

            $resultAlterarMatricula = $clmatricula->alterar($matriculaorig);

            if (!$resultAlterarMatricula) {
                throw new Exception($msgPadrao . $clmatricula->erro_msg);
            }

            /* DELETE TABELA matriculamov */
            $sWhereMatriculaMov = "ed229_c_procedimento like 'PROGRESS%' and ed229_i_matricula = {$matriculaorig}";
            $resultDeleteMatriculaMov = $clmatriculamov->excluir('', $sWhereMatriculaMov);
            if (!$resultDeleteMatriculaMov) {
                throw new Exception($msgPadrao . $clmatriculamov->erro_msg);
            }

            /* DELETE TABELA avaliacaoclassificacao */
            $sWhereAvaliacaoClassificacao = "ed335_trocaserie = {$codigoprogressao}";
            $rstDeleteAvaliacaoClassificacao = $clavaliacaoclassificacao->excluir('', $sWhereAvaliacaoClassificacao);
            if (!$rstDeleteAvaliacaoClassificacao) {
                throw new Exception($msgPadrao . $clavaliacaoclassificacao->erro_msg);
            }

            /* DELETE TABELA trocaserie */
            $sWhereTrocaSerie = "ed101_i_codigo = {$codigoprogressao}";
            $rsDeleteTrocaSerie = $cltrocaserie->excluir('', $sWhereTrocaSerie);
            if (!$rsDeleteTrocaSerie) {
                throw new Exception($msgPadrao . $cltrocaserie->erro_msg);
            }

            /* UPDATE TABELA diario */
            $sSqlAlterarDiario = "UPDATE diario SET
                ed95_c_encerrado = 'N'
                WHERE ed95_i_aluno = {$codigoaluno}
                AND ed95_i_regencia IN
                    (SELECT ed59_i_codigo
                        FROM regencia
                        WHERE ed59_i_turma = {$codturmaorig}
                        AND ed59_i_serie = {$codserieorig})";

            $resultAlterarDiario = db_query($sSqlAlterarDiario);
            if (!$resultAlterarDiario) {
                throw new Exception($msgPadrao . "Houve um erro ao tentar alterar o diário do aluno!");
            }

            /* UPDATE TABELA  historico */
            $sSqlAlterarHistorico = "UPDATE historico SET
                ed61_t_obs = ''
                WHERE ed61_i_aluno = {$codigoaluno}
                AND ed61_i_curso = {$codcursoorig}";

            $resultAlterarHistorico = db_query($sSqlAlterarHistorico);
            if (!$resultAlterarHistorico) {
                throw new Exception($msgPadrao . 'Houve um erro ao tentar alterar o histórico do aluno!');
            }

            if ($ed57_i_tipoturma == 2) {
                $condicao = " AND ed11_i_sequencia >= {$seqant}";
            } else {
                $condicao = " AND ed11_i_sequencia = {$seqant}";
            }

            /* SELECT TABELA turmaserieregimemat */
            $sSqlTurmaSerieRegimeMat = $clturmaserieregimemat->sql_query(
                "",
                "ed223_i_serie",
                "ed223_i_ordenacao",
                "ed220_i_turma = {$codturmaorig} {$condicao}"
            );
            $resultEtapaAnterior = $clturmaserieregimemat->sql_record($sSqlTurmaSerieRegimeMat);
            if (!$resultEtapaAnterior) {
                throw new Exception($msgPadrao . $clturmaserieregimemat->erro_msg);
            }

            for ($i = 0; $i < $clturmaserieregimemat->numrows; $i++) {
                db_fieldsmemory($resultEtapaAnterior, $i);

                /* DELETE TABELA histmpsdisc */
                $sWhereHistMpsDisc = "ed65_i_codigo IN (SELECT ed65_i_codigo
                                        FROM histmpsdisc
                                             INNER JOIN historicomps ON ed62_i_codigo = ed65_i_historicomps
                                             INNER JOIN historico    ON ed61_i_codigo = ed62_i_historico
                                       WHERE ed62_i_serie  = {$ed223_i_serie}
                                         AND ed62_i_escola = {$codescolaorig}
                                         AND ed61_i_aluno  = {$codigoaluno}
                                         AND ed61_i_curso  = {$codcursoorig})";
                $rsDeleteHistMpsDisc = $clhistmpsdisc->excluir('', $sWhereHistMpsDisc);
                if (!$rsDeleteHistMpsDisc) {
                    throw new Exception($msgPadrao . $clhistmpsdisc->erro_msg);
                }

                /* DELETE TABELA historicomps */
                $sWhereHistoricoMps = "ed62_i_codigo IN (SELECT ed62_i_codigo
                                        FROM historicomps
                                             INNER JOIN historico ON ed61_i_codigo = ed62_i_historico
                                       WHERE ed62_i_serie  = {$ed223_i_serie}
                                         AND ed62_i_escola = {$codescolaorig}
                                         AND ed61_i_aluno  = {$codigoaluno}
                                         AND ed61_i_curso  = {$codcursoorig})";
                $rsDeleteHistoricoMps = $clhistoricomps->excluir('', $sWhereHistoricoMps);
                if (!$rsDeleteHistoricoMps) {
                    throw new Exception($msgPadrao . $clhistoricomps->erro_msg);
                }
            }

            $descr_origem  = "Matrícula n°: {$matriculadest}\nTurma: {$descrturmadest}\nEscola: ".db_getsession("DB_nomedepto")."";
            $descr_origem .= "\nCalendário: {$descrcalendariodest}";

            $cllogmatricula->ed248_i_usuario = db_getsession("DB_id_usuario");
            $cllogmatricula->ed248_i_motivo  = null;
            $cllogmatricula->ed248_i_aluno   = $codigoaluno;
            $cllogmatricula->ed248_t_origem  = $descr_origem;

            $sObservacao                     = "Cancelamento de Progressão ( Aluno retornado da turma {$descrturmadest} para";
            $sObservacao                    .= " a turma {$descrturmaorig} )";
            $cllogmatricula->ed248_t_obs     = $sObservacao;
            $cllogmatricula->ed248_d_data    = date("Y-m-d", db_getsession("DB_datausu"));
            $cllogmatricula->ed248_c_hora    = date("H:i" );
            $cllogmatricula->ed248_c_tipo    = "E";
            $cllogmatricula->incluir(null);

            db_fim_transacao();
            db_msgbox("Cancelamento efetuado com sucesso!");
            db_redireciona("edu4_cancelaprogressao001.php");
            exit;
        }
    } catch (Exception $error) {
        db_msgbox($error->getMessage());
        db_redireciona("edu4_cancelaprogressao001.php");
    }
}

db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor="#CCCCCC">
  <div class="container">
    <form name="form1" method="post" class="form-container">
      <?php MsgAviso(db_getsession("DB_coddepto"),"escola");?>
      <table>
        <tr>
          <td>
            <fieldset style="width:95%">
             <legend>Cancelar Progressão de Aluno</legend>
             <table>
               <tr>
                 <td>
                 <?php
                   $sCamposTrocaSerie = "ed101_i_codigo, ed101_d_data, ed101_c_tipo, ed47_i_codigo, ed47_v_nome";
                   $sWhereTrocaSerie  = "turmaorig.ed57_i_escola = $escola";
                   $sSqlTrocaSerie = $cltrocaserie->sql_query( "", $sCamposTrocaSerie, "", $sWhereTrocaSerie );
                   $result         = $cltrocaserie->sql_record( $sSqlTrocaSerie );
                 ?>
                   <b>Alunos Progredidos:</b>
                   <select name="aluno" style="font-size:9px;" onchange="js_pesquisa(this.value);">
                 <?php
                   if ( $cltrocaserie->numrows == 0 ) {
                     echo "<option value=''>Nenhum registro de progressão</option>";
                   } else {

                     echo "<option value=''></option>";
                     for ( $x = 0; $x < $cltrocaserie->numrows; $x++ ) {

                       db_fieldsmemory($result,$x);
                       $sSituacao = "CLASSIFICADO";
                       if ( $ed101_c_tipo == "R" ) {
                         $sSituacao = "RECLASSIFICADO";
                       } else if ( $ed101_c_tipo = "A" ) {
                         $sSituacao = "AVANÇADO";
                       }

                       echo "<option value='$ed101_i_codigo' ".($ed101_i_codigo==@$aluno?"selected":"").">$ed47_i_codigo - $ed47_v_nome ( ".$sSituacao." em ".db_formatar($ed101_d_data,'d')." )</option>";
                     }
                   }
                 ?>
                   </select>
                 </td>
               </tr>
               <?php
                 if ( isset( $aluno ) ) {

                   $sSituacao = "CLASSIFICADO";
                   if ( $ed101_c_tipo == "R" ) {
                     $sSituacao = "RECLASSIFICADO";
                   } else if ( $ed101_c_tipo = "A" ) {
                     $sSituacao = "AVANÇADO";
                   }

                 $campos = "turmaorig.ed57_i_codigo as codturmaorig,
                            turmaorig.ed57_c_descr as descrturmaorig,
                            baseorig.ed31_i_codigo as codbaseorig,
                            calendarioorig.ed52_i_codigo as codcalendarioorig,
                            escolaorig.ed18_i_codigo as codescolaorig,
                            turnoorig.ed15_i_codigo as codturnoorig,
                            aluno.ed47_i_codigo as codigoaluno,
                            turmadest.ed57_i_codigo as codturmadest,
                            turmadest.ed57_c_descr as descrturmadest,
                            basedest.ed31_i_codigo as codbasedest,
                            calendariodest.ed52_i_codigo as codcalendariodest,
                            calendariodest.ed52_c_descr as descrcalendariodest,
                            escoladest.ed18_i_codigo as codescoladest,
                            turnodest.ed15_i_codigo as codturnodest,
                            trocaserie.ed101_d_data,
                            trocaserie.ed101_c_tipo,
                            trocaserie.ed101_t_obs,
                            trocaserie.ed101_i_codigo";
                 $result1 = $cltrocaserie->sql_record( $cltrocaserie->sql_query( "", $campos, "", "ed101_i_codigo = {$aluno}" ) );
                 db_fieldsmemory( $result1, 0 );

                 $sCamposMatricula2  = "ed60_i_codigo as matriculaorig, ed60_c_situacao as situacaoorig";
                 $sCamposMatricula2 .= ", ed60_c_concluida as conclusaoorig, cursoedu.ed29_i_codigo as codcursoorig";
                 $sCamposMatricula2 .= ", ed11_c_descr as descrserieorig,ed221_i_serie as codserieorig";
                 $sWhereMatricula2   = "ed60_i_aluno = {$codigoaluno} AND ed60_i_turma = {$codturmaorig}";
                 $sWhereMatricula2  .= " AND (ed60_c_situacao = 'AVANÇADO' OR ed60_c_situacao = 'CLASSIFICADO'";
                 $sWhereMatricula2  .= " OR ed60_c_situacao = 'RECLASSIFICADO')";
                 $sSqlMatricula2     = $clmatricula->sql_query( "", $sCamposMatricula2, "", $sWhereMatricula2 );
                 $result2            = $clmatricula->sql_record( $sSqlMatricula2 );

                 if ( $clmatricula->numrows > 0 ) {

                   db_fieldsmemory( $result2, 0 );
                   $conclusaoorig = $conclusaoorig == "S" ? "SIM" : "NAO";
                 }

                 $sCamposMatricula3  = "ed60_i_codigo as matriculadest, ed60_c_situacao as situacaodest";
                 $sCamposMatricula3 .= ", ed60_c_concluida as conclusaodest, ed11_c_descr as descrseriedest";
                 $sCamposMatricula3 .= ", ed221_i_serie as codseriedest";
                 $sWhereMatricula3   = "ed60_i_aluno = {$codigoaluno} AND ed60_i_turma = {$codturmadest}";
                 $sWhereMatricula3  .= " AND ed60_c_situacao = 'MATRICULADO'";
                 $sSqlMatricula3     = $clmatricula->sql_query( "", $sCamposMatricula3, "", $sWhereMatricula3);
                 $result3            = $clmatricula->sql_record( $sSqlMatricula3 );

                 if ( $clmatricula->numrows > 0 ) {

                   db_fieldsmemory( $result3, 0 );
                   $conclusaodest = $conclusaodest == "S" ? "SIM" : "NAO";
                 }
               ?>
               <tr>
                 <td>
                   <fieldset style="width:95%;">
                     <legend>Dados de Origem</legend>
                     <table>
                       <tr>
                         <td>
                           <b>Matrícula:</b>
                         </td>
                         <td>
                         <?php
                           db_input( 'codescolaorig',     15, @$codescolaorig,     true, 'hidden', 3 );
                           db_input( 'codbaseorig',       15, @$codbaseorig,       true, 'hidden', 3 );
                           db_input( 'codcalendarioorig', 15, @$codcalendarioorig, true, 'hidden', 3 );
                           db_input( 'codturnoorig',      15, @$codturnoorig,      true, 'hidden', 3 );
                           db_input( 'codserieorig',      15, @$codserieorig,      true, 'hidden', 3 );
                           db_input( 'codcursoorig',      15, @$codcursoorig,      true, 'hidden', 3 );
                           db_input( 'matriculaorig',     15, @$matriculaorig,     true, 'text',   3 );
                         ?>
                         </td>
                       </tr>
                       <tr>
                         <td>
                           <b>Situação:</b>
                         </td>
                         <td>
                         <?php db_input( 'situacaoorig', 40, @$situacaoorig, true, 'text', 3 );?>
                         </td>
                       </tr>
                       <tr>
                         <td>
                           <b>Concluída:</b>
                         </td>
                         <td>
                         <?php db_input( 'conclusaoorig', 40, @$conclusaorig, true, 'text', 3 );?>
                         </td>
                       </tr>
                       <tr>
                         <td>
                           <b>Etapa:</b>
                         </td>
                         <td>
                         <?php
                           db_input( 'codserieorig',   15, @$codserieorig,   true, 'hidden', 3 );
                           db_input( 'descrserieorig', 40, @$descrserieorig, true, 'text',   3 );
                         ?>
                         </td>
                       </tr>
                       <tr>
                         <td>
                           <b>Turma:</b>
                         </td>
                         <td>
                          <?php
                            db_input( 'codturmaorig',   15, @$codturmaorig,   true, 'hidden', 3 );
                            db_input( 'descrturmaorig', 40, @$descrturmaorig, true, 'text',   3 );
                          ?>
                         </td>
                       </tr>
                     </table>
                   </fieldset>
                 </td>
                 <td>
                   <fieldset style="width:95%;"><legend><b>Dados de Destino</b></legend>
                     <table>
                       <tr>
                         <td>
                           <b>Matrícula:</b>
                         </td>
                         <td>
                         <?php
                           db_input( 'codescoladest',       15, @$codescoladest,       true, 'hidden', 3 );
                           db_input( 'codbasedest',         15, @$codbasedest,         true, 'hidden', 3 );
                           db_input( 'codcalendariodest',   15, @$codcalendariodest,   true, 'hidden', 3 );
                           db_input( 'descrcalendariodest', 20, @$descrcalendariodest, true, 'hidden', 3 );
                           db_input( 'codturnodest',        15, @$codturnodest,        true, 'hidden', 3 );
                           db_input( 'codseriedest',        15, @$codseriedest,        true, 'hidden', 3 );
                           db_input( 'matriculadest',       15, @$matriculadest,       true, 'text',   3 );
                         ?>
                         </td>
                       </tr>
                       <tr>
                         <td>
                           <b>Situação:</b>
                         </td>
                         <td>
                         <?php db_input( 'situacaodest', 40, @$situacaodest, true, 'text', 3 );?>
                         </td>
                       </tr>
                       <tr>
                         <td>
                           <b>Concluida:</b>
                         </td>
                         <td>
                         <?php db_input( 'conclusaodest', 40, @$conclusadest, true, 'text', 3 );?>
                         </td>
                       </tr>
                       <tr>
                         <td>
                           <b>Etapa:</b>
                         </td>
                         <td>
                         <?php
                           db_input( 'codseriedest',   15, @$codseriedest,   true, 'hidden', 3 );
                           db_input( 'descrseriedest', 40, @$descrseriedest, true, 'text',   3 );
                         ?>
                         </td>
                       </tr>
                       <tr>
                         <td>
                           <b>Turma:</b>
                         </td>
                         <td>
                         <?php
                           db_input( 'codturmadest',   15, @$codturmadest,   true, 'hidden', 3 );
                           db_input( 'descrturmadest', 40, @$descrturmadest, true, 'text',   3 );
                         ?>
                         </td>
                       </tr>
                     </table>
                   </fieldset>
                 </td>
               </tr>
               <tr>
                 <td colspan="2">
                   <b>
                    <?=$sSituacao?> em: <?=db_formatar($ed101_d_data,'d')?>
                    Obs.: <?=$ed101_t_obs?>
                   </b>
                 </td>
               </tr>
             </table>
            </fieldset>
            <script>
            sitorig = document.form1.situacaoorig.value.substr(0,4);
            sitdest = document.form1.situacaodest.value;
            var sMensagem = "";

            if ( sitdest != 'MATRICULADO' ) {

              if ( sitdest == "" ) {
                alert("Aluno não está mais matriculado na turma de destino. Cancelamento de Progressão não permitido!");
              } else {

                sMensagem  = "Aluno não está mais na situação de MATRICULADO na turma <?=$descrturmadest?>.";
                sMensagem += " Cancelamento de Progressão não permitido!";
                alert( sMensagem );
              }

              document.form1.incluir.disabled = true;
            }else if(sitorig != 'CLAS' && sitorig != 'AVAN' && sitorig != 'RECL') {

              if ( sitorig == "" ) {
                alert("Aluno não está mais matriculado na turma de origem. Cancelamento de Progressão não permitido!");
              } else {

                sMensagem  = "Aluno não está mais na situação de <?=$sSituacao?> na turma <?=$descrturmaorig?>.";
                sMensagem += " Cancelamento de Progressão não permitido!";
                alert( sMensagem );
              }
            } else if ( document.form1.conclusaodest.value == 'SIM' ) {

              sMensagem  = "Aluno já possui matrícula concluída na turma <?=$descrturmadest?>.";
              sMensagem += " Cancelamento de Progressão não permitido!";
              alert( sMensagem );
            }
            </script>
            <div class="center">
              <input type="hidden" name="codigoprogressao" value="<?=$ed101_i_codigo?>">
              <input type="hidden" name="codigoaluno"      value="<?=isset( $codigoaluno ) ? $codigoaluno : ''?>">
              <input type="submit" name="incluir"          value="Confirmar Cancelamento" onclick="return js_confirma();">
            </div>
            <?php } ?>
          </td>
        </tr>
      </table>
    </form>
  </div>
<?php db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));?>
</body>
</html>
<script>
function js_pesquisa() {

  if ( document.form1.aluno.value == "" ) {
    location.href = 'edu4_cancelaprogressao001.php';
  } else {
    location.href = 'edu4_cancelaprogressao001.php?aluno='+document.form1.aluno.value;
  }
}

function js_confirma() {

  if ( confirm( 'Confirmar cancelamento de progressão para este aluno?' ) ) {

    document.form1.incluir.style.visibility = "hidden";
    return true;
  } else {
    return false;
  }
}
</script>
