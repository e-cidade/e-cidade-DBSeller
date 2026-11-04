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
use ECidade\Pdf\Pdf;

define('MENSAGENS_EDU2_FICHAALUNO001', 'educacao.escola.edu2_fichaaluno002.');

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("model/educacao/ArredondamentoNota.model.php"));
require_once(modification("model/educacao/DBEducacaoTermo.model.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_libdocumento.php"));
require_once(modification("libs/db_libparagrafo.php"));

$oGet = db_utils::postMemory($_GET);
$oDaoMatricula = new cl_matricula();
$oDaoMatriculamov = new cl_matriculamov();
$oDaoAluno = new cl_aluno();
$oDaoAlunoprimat = new cl_alunoprimat();
$oDaoAlunonecessidade = new cl_alunonecessidade();
$oDaoProcResultado = new cl_procresultado();
$oDaoHistmpsdisc = new cl_histmpsdisc();
$oDaoHistmpsdiscfora = new cl_histmpsdiscfora();
$oDaoTipoSanguineo = new cl_tiposanguineo();
$oErro = new stdClass();

//Checar parametros
$clsec_parametros = new cl_sec_parametros;
$sSqlSecParametros = $clsec_parametros->sql_query_file('1', 'ed290_habilitarcamposmatriculainicial');
$resultQueryParametros = db_query($sSqlSecParametros);
$matriculaInicial = db_utils::fieldsMemory($resultQueryParametros, 0)->ed290_habilitarcamposmatriculainicial;
$matriculaInicial = ($matriculaInicial == 't') ? true : false;

$calendario = $_GET['calendario'];

try {
    $resultedu = eduparametros(db_getsession("DB_coddepto"));
    $permitenotaembranco = VerParametroNota(db_getsession("DB_coddepto"));

    $oDaoAluno->rotulo->label();
    $clrotulo = new rotulocampo;
    $clrotulo->label("ed76_i_escola");
    $clrotulo->label("ed76_d_data");

    $escola = db_getsession("DB_coddepto");// variável $escola não está sendo usada na classe @matheus;
    //$ano    = date(Y);
    $ano = $calendario;

    if (!isset($sCampos)) {
        $sCampos = '';
    }

    $sCampos .= "SELECT ed47_v_nome AS nomealuno, ";
    $sCampos .= "ed60_matricula AS matricula, ";
    $sCampos .= "ed47_d_nasc AS datanasc, ";
    $sCampos .= "ed60_d_datamatricula AS datamatr, ";
    $sCampos .= "ed60_c_situacao AS situacao, ";
    $sCampos .= "ed60_d_datasaida AS datasaida, ";
    $sCampos .= "ed47_v_mae AS nomemae, ";
    $sCampos .= "ed47_v_pai AS nomepai, ";
    $sCampos .= "ed47_v_sexo as sexo, ";
    $sCampos .= "ed47_v_cpf as cpf, ";
    $sCampos .= "ed47_c_nomeresp as responsavel, ";
    $sCampos .= "ed57_c_descr AS turma, ";
    $sCampos .= "ed52_c_descr AS calendario, ";
    $sCampos .= "ed47_v_cep AS cep, ";
    $sCampos .= "ed47_i_nacion AS nacionalidade, ";
    $sCampos .= "municnat.ed261_c_nome AS municnat, ";
    $sCampos .= "ufnat.ed260_c_sigla AS ufnat, ";
    $sCampos .= "ed228_c_descr AS pais, ";
    $sCampos .= "ed47_v_bairro AS bairro, ";
    $sCampos .= "ed47_c_numero AS numero, ";
    $sCampos .= "ed47_v_compl AS complemento, ";
    $sCampos .= "ed47_v_ender AS endereco, ";
    $sCampos .= "municend.ed261_c_nome AS municend, ";
    $sCampos .= "ufend.ed260_c_sigla AS ufend, ";
    $sCampos .= "ed47_o_oid, ";
    $sCampos .= "ed47_c_foto, ";
    $sCampos .= "ed11_c_descr as serie, ";
    $sCampos .= "ocupacaopai.mo304_titulo as ocupacaopai, ";
    $sCampos .= "ocupacaomae.mo304_titulo as ocupacaomae, ";
    $sCampos .= "ocupacaoresp.mo304_titulo as ocupacaoresp, ";
    $sCampos .= "matriculaescolaaluno.ed365_matricula as matricula_escola, ";
    $sCampos .= "matriculaescolaaluno.ed365_datamatriculainicial as matricula_inicial ";
    $sCampos .= "FROM matricula ";
    $sCampos .= "INNER JOIN aluno ON (ed60_i_aluno = ed47_i_codigo) ";
    $sCampos .= "INNER JOIN turma ON (ed60_i_turma = ed57_i_codigo) ";
    $sCampos .= "INNER JOIN calendario ON (ed52_i_codigo = ed57_i_calendario) ";
    $sCampos .= "INNER JOIN matriculaserie ON (ed221_i_matricula = ed60_i_codigo) ";
    $sCampos .= "INNER JOIN serie ON (ed11_i_codigo = ed221_i_serie ) ";
    $sCampos .= "INNER JOIN serieregimemat ON (ed223_i_serie = serie.ed11_i_codigo) ";
    $sCampos .= "LEFT JOIN censouf AS ufident ON (ed47_i_censoufident = ufident.ed260_i_codigo) ";
    $sCampos .= "LEFT JOIN censouf AS ufnat ON (ed47_i_censoufnat = ufnat.ed260_i_codigo) ";
    $sCampos .= "LEFT JOIN censouf AS ufcert ON (ed47_i_censoufcert = ufcert.ed260_i_codigo) ";
    $sCampos .= "LEFT JOIN censouf AS ufend ON (ed47_i_censoufend = ufend.ed260_i_codigo) ";
    $sCampos .= "LEFT JOIN censomunic AS municnat ON (ed47_i_censomunicnat = municnat.ed261_i_codigo) ";
    $sCampos .= "LEFT JOIN censomunic AS municcert ON (ed47_i_censomuniccert = municcert.ed261_i_codigo) ";
    $sCampos .= "LEFT JOIN censomunic AS municend ON (ed47_i_censomunicend = municend.ed261_i_codigo) ";
    $sCampos .= "LEFT JOIN censoorgemissrg ON (ed47_i_censoorgemissrg = ed132_i_codigo) ";
    $sCampos .= "LEFT JOIN pais ON (ed47_i_pais = ed228_i_codigo) ";
    $sCampos .= "LEFT JOIN escola.cboocupacao AS ocupacaopai ";
    $sCampos .= "          ON (ocupacaopai.mo304_codigo = aluno.ed47_i_ocupacaopai) ";
    $sCampos .= "LEFT JOIN escola.cboocupacao AS ocupacaomae";
    $sCampos .= "          ON (ocupacaomae.mo304_codigo = aluno.ed47_i_ocupacaomae) ";
    $sCampos .= "LEFT JOIN escola.cboocupacao AS ocupacaoresp";
    $sCampos .= "          ON (ocupacaoresp.mo304_codigo = aluno.ed47_i_ocupacaoresp) ";
    $sCampos .= "LEFT JOIN escola.matriculaescolaaluno ";
    $sCampos .= "          ON ( escola.matriculaescolaaluno.ed365_aluno = aluno.ed47_i_codigo ";
    $sCampos .= "          and escola.matriculaescolaaluno.ed365_escola = ed57_i_escola) ";
    $sCampos .= "WHERE date_part('year', ed60_d_datamatricula) = $calendario ";
    $sCampos .= "  AND ed57_i_escola = $escola ";
    $sCampos .= "  AND ed60_c_situacao NOT IN ('CANCELADO', 'TROCA DE TURMA') ";
    $sCampos .= "  AND ed221_c_origem = 'S' ";
    $sCampos .= "ORDER BY ed52_c_descr, ed57_c_descr, ed47_v_nome ";
    $rsResult = db_query($sCampos);

    if (!$rsResult) {
        throw new BusinessException(_M(MENSAGENS_EDU2_FICHAALUNO001 . "nenhum_registro_encontrado"));
    }

    if (!is_resource($rsResult)) {
        $oErro->sErro = pg_last_error();
        throw new DBException(_M(MENSAGENS_EDU2_FICHAALUNO001 . "erro_buscar_dados_aluno", $oErro));
    }

    $iLinhasAluno = pg_num_rows($rsResult);
    if ($iLinhasAluno == 0) {
        throw new BusinessException(_M(MENSAGENS_EDU2_FICHAALUNO001 . "nenhum_registro_encontrado"));
    }

    $oPdf = new Pdf();
    $oPdf->init(false);
    $oPdf->exibeHeader(true, \Fpdf\Pdf::HEADER_ESCOLA);
    $oPdf->AliasNbPages();
    $oPdf->setfillcolor(223);
    $oPdf->SetAutoPageBreak(false, 10);
    $oPdf->addTitulo("LIVRO DE MATRÍCULA");
    $oPdf->addTitulo("$ano");
    $i = 0;
    $height = 0;

    for ($iCont = 0; $iCont < $iLinhasAluno; $iCont++) {
        if ($i == 0) {
            $oPdf->AddPage('P');
            $oPdf->setfillcolor(223);
            $height = $oPdf->GetY();
        }
        $i++;

        $height = $oPdf->GetY() + ($responsavel ? 47 : 39);

        db_fieldsmemory($rsResult, $iCont);

        /** DADOS PESSOAIS */
        $oPdf->setfont('arial', 'b', 7);

        $sTurma = strpos(strtolower($turma), 'mult') === false
            ? "Turma: {$turma} Etapa: {$serie} ($calendario) "
            : "Turma/Etapa: {$turma} ($calendario) ";
        $nTurmaWidth = $oPdf->GetStringWidth($sTurma);
        $nMatriculaWidth = (194 - $nTurmaWidth - 2);

        $oPdf->cell($nMatriculaWidth > 40 ? $nMatriculaWidth : 44, 4, "   MATRICULA " . ($iCont + 1), 'LTB', 0, "L", 1);

        $oPdf->cell($nTurmaWidth, 4, $sTurma, "TB", 0, "C", 1);
        $oPdf->cell(2, 4, '', 'TRB', 1, "L", 1); //2

        $oPdf->cell(194, 2, "", "LR", 1, "C", 0);
        //$oPdf->cell(200, 4, "Conteudo", 'TB', 0, "C", 1);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(20, 4, "Nome: ", 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(90, 4, $nomealuno, 0, 0, "L", 0);

        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(20, 4, "CPF: ", 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(60, 4, $cpf, 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);
        $oPdf->cell(3, 4, "", "L", 0, "C", 0);

        /**
         * Gerenciar Matricula com parametro "Matricula Inicial"
         * Caso seja verdadeiro, ele vai alterar o número de matrícula
         * caso ele tenha alteração perante a escola, e a Data da Matrícula
         * de acordo com este mesmo registro
         */
        if ($matriculaInicial) {
            if (!empty($matricula_escola)) {
                $oPdf->setfont('arial', '', 7);
                $oPdf->cell(20, 4, "Matrícula: ", 0, 0, "L", 0);
                $oPdf->setfont('arial', 'b', 7);
                $oPdf->cell(50, 4, $matricula_escola, 0, 0, "L", 0);
            } else {
                $oPdf->setfont('arial', '', 7);
                $oPdf->cell(20, 4, "Matrícula: ", 0, 0, "L", 0);
                $oPdf->setfont('arial', 'b', 7);
                $oPdf->cell(50, 4, ($iCont + 1), 0, 0, "L", 0);
            }
        } else {
            $oPdf->setfont('arial', '', 7);
            $oPdf->cell(20, 4, "Matrícula: ", 0, 0, "L", 0);
            $oPdf->setfont('arial', 'b', 7);
            $oPdf->cell(50, 4, $matricula, 0, 0, "L", 0);
        }

        if ($matriculaInicial == true) {
            if (!empty($matricula_escola)) {
                $oPdf->setfont('arial', '', 7);
                $oPdf->cell(12, 4, "Dt. Mat.: ", 0, 0, "L", 0);
                $oPdf->setfont('arial', 'b', 7);
                $oPdf->cell(28, 4, join('/', array_reverse(explode('-', $matricula_inicial))), 0, 0, "L", 0);
            } else {
                $oPdf->setfont('arial', '', 7);
                $oPdf->cell(12, 4, "Dt. Mat.: ", 0, 0, "L", 0);
                $oPdf->setfont('arial', 'b', 7);
                $oPdf->cell(28, 4, " - ", 0, 0, "L", 0);
            }
        } else {
            $oPdf->setfont('arial', '', 7);
            $oPdf->cell(12, 4, "Dt. Mat.: ", 0, 0, "L", 0);
            $oPdf->setfont('arial', 'b', 7);
            $datamatricula = date('d/m/Y', strtotime($datamatr));
            $oPdf->cell(28, 4, $datamatricula, 0, 0, "L", 0);
        }

        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(20, 4, "Sexo: ", 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(60, 4, $sexo == 'M' ? 'MASCULINO' : 'FEMININO', 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);
        $oPdf->cell(3, 4, "", "L", 0, "C", 0);

        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(20, 4, "Filiação 1: ", 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(90, 4, $nomemae, 0, 0, "L", 0);

        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(20, 4, "Dt. Nasc: ", 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $nascimento = date('d/m/Y', strtotime($datanasc));
        $oPdf->cell(60, 4, $nascimento, 0, 0, "L", 0);

        $oPdf->cell(1, 4, "", "R", 1, "C", 0);
        $oPdf->cell(3, 4, "", "L", 0, "C", 0);

        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(20, 4, "Profissão 1: ", 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(90, 4, $ocupacaomae ? strtoupper($ocupacaomae) : '-', 0, 0, "L", 0);

        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(20, 4, "Situação: ", 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(60, 4, $situacao, 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);
        $oPdf->cell(3, 4, "", "L", 0, "C", 0);

        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(20, 4, "Filiação 2: ", 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(90, 4, $nomepai, 0, 0, "L", 0);

        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(20, 4, "Dt. Saída: ", 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        if (!empty($datasaida)) {
            $saida = date('d/m/Y', strtotime($datasaida));
        } else {
            $saida = " - ";
        }
        $oPdf->cell(60, 4, $saida, 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);

        if (!empty($numero)) {
            $numero = ",  " . $numero;
        } else {
            $numero = "";
        }

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(20, 4, "Profissão 2: ", 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(90, 4, $ocupacaopai ? strtoupper($ocupacaopai) : '-', 0, 0, "L", 0);

        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(20, 4, "CEP: ", 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $cep = substr($cep, 0, 5) . '-' . substr($cep, -3);
        $oPdf->cell(60, 4, $cep, 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);
        $oPdf->cell(3, 4, "", "L", 0, "C", 0);

        if ($responsavel) {
            $oPdf->setfont('arial', '', 7);
            $oPdf->cell(20, 4, "Resp. Legal: ", 0, 0, "L", 0);
            $oPdf->setfont('arial', 'b', 7);
            $oPdf->cell(90, 4, $responsavel, 0, 0, "L", 0);
        } else {
            $oPdf->setfont('arial', '', 7);
            $oPdf->cell(20, 4, "Endereço: ", 0, 0, "L", 0);
            $oPdf->setfont('arial', 'b', 7);
            $oPdf->cell(90, 4, $endereco . $numero . "   " . $complemento, 0, 0, "L", 0);
        }

        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(20, 4, "País: ", 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(60, 4, $pais, 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);
        $oPdf->cell(3, 4, "", "L", 0, "C", 0);

        if ($responsavel) {
            $oPdf->setfont('arial', '', 7);
            $oPdf->cell(20, 4, "Profissão Resp.: ", 0, 0, "L", 0);
            $oPdf->setfont('arial', 'b', 7);
            $oPdf->cell(90, 4, $ocupacaoresp ? strtoupper($ocupacaoresp) : '-', 0, 0, "L", 0);
        } else {
            $oPdf->setfont('arial', '', 7);
            $oPdf->cell(20, 4, "", 0, 0, "L", 0);
            $oPdf->setfont('arial', 'b', 7);
            $oPdf->cell(90, 4, strtoupper($bairro) . "   -   " . $municend . " - " . $ufend, 0, 0, "L", 0);
        }

        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(20, 4, "Nacionalidade: ", 0, 0, "L", 0);

        if ($nacionalidade == 1) {
            $nacionalidade = "BRASILEIRA";
        } elseif ($nacionalidade == 2) {
            $nacionalidade = "BRASILEIRA NO EXTERIOR OU NATURALIZADO";
        } elseif ($nacionalidade == 3) {
            $nacionalidade = "ESTRANGEIRA";
        }

        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(60, 4, $nacionalidade, 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);

        if ($responsavel) {
            $oPdf->cell(3, 4, "", "L", 0, "C", 0);
            $oPdf->setfont('arial', '', 7);
            $oPdf->cell(20, 4, 'Endereço: ', 0, 0, 'L', 0);
            $oPdf->setfont('arial', 'b', 7);
            $oPdf->cell(90, 4, $endereco . $numero . "   " . $complemento, 0, 0, 'L', 0);

            $oPdf->setfont('arial', '', 7);
            $oPdf->cell(20, 4, "Naturalidade: ", 0, 0, "L", 0);
            $oPdf->setfont('arial', 'b', 7);
            $oPdf->cell(60, 4, $municnat . " - " . $ufnat, 0, 0, "L", 0);
            $oPdf->cell(1, 4, "", "R", 1, "C", 0);
            $oPdf->cell(3, 4, "", "L", 0, "C", 0);

            $oPdf->setfont('arial', '', 7);
            $oPdf->cell(20, 4, "", 0, 0, "L", 0);
            $oPdf->setfont('arial', 'b', 7);
            $oPdf->cell(90, 4, strtoupper($bairro) . " - " . $municend . " - " . $ufend, 0, 0, "L", 0);

            $oPdf->cell(80, 4, " ", 0, 0, "L", 0);
            $oPdf->cell(1, 4, "", "R", 1, "C", 0);
        } else {
            $oPdf->cell(113, 4, "", "L", 0, "C", 0);

            $oPdf->setfont('arial', '', 7);
            $oPdf->cell(20, 4, "Naturalidade: ", 0, 0, "L", 0);
            $oPdf->setfont('arial', 'b', 7);
            $oPdf->cell(60, 4, $municnat . " - " . $ufnat, 0, 0, "L", 0);
            $oPdf->cell(1, 4, "", "R", 1, "C", 0);
        }

        if ($i == 7 || $height >= 240) {
            $i = 0;
            $oPdf->cell(194, 1, "", "LBR", 1, "C", 0);
        } else {
            if ($iCont + 1 == $iLinhasAluno) {
                $oPdf->cell(194, 1, "", "LBR", 1, "C", 0);
            } else {
                $oPdf->cell(194, 1, "", "LR", 1, "C", 0);
            }
        }
    }
    $oPdf->Output();
} catch (Exception $oErro) {
    db_redireciona('db_erros.php?fechar=true&db_erro=' . $oErro->getMessage());
}
