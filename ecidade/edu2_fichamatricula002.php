<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2014  DBSeller Servicos de Informatica
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
define( 'MENSAGENS_EDU2_FICHAALUNO001', 'educacao.escola.edu2_fichaaluno002.' );

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_utils.php");
require_once modification("libs/db_stdlibwebseller.php");
require_once modification("model/educacao/ArredondamentoNota.model.php");
require_once modification("model/educacao/DBEducacaoTermo.model.php");
require_once modification("dbforms/db_funcoes.php");
require_once modification("libs/db_libdocumento.php");
require_once modification("libs/db_libparagrafo.php");

$oGet                 = db_utils::postMemory($_GET);
$oDaoMatricula        = new cl_matricula();
$oDaoMatriculamov     = new cl_matriculamov();
$oDaoAluno            = new cl_aluno();
$oDaoAlunoprimat      = new cl_alunoprimat();
$oDaoAlunonecessidade = new cl_alunonecessidade();
$oDaoProcResultado    = new cl_procresultado();
$oDaoHistmpsdisc      = new cl_histmpsdisc();
$oDaoHistmpsdiscfora  = new cl_histmpsdiscfora();
$oDaoTipoSanguineo    = new cl_tiposanguineo();
$oDaoDiretor          = new cl_escoladiretor();
$oDaoEscola           = new cl_escola();
$oErro                = new stdClass();

try {
    $resultedu            = eduparametros(db_getsession("DB_coddepto"));
    $permitenotaembranco  = VerParametroNota(db_getsession("DB_coddepto"));

    $oDaoAluno->rotulo->label();
    $clrotulo = new rotulocampo;
    $clrotulo->label("ed76_i_escola");
    $clrotulo->label("ed76_d_data");

    $escola = db_getsession("DB_coddepto");// variável $escola não está sendo usada na classe @matheus;

    $sCampos  = " aluno.*,   ";
    $sCampos .= " censoufident.ed260_c_nome as ufident,  ";
    $sCampos .= " censoufnat.ed260_c_nome as ufnat,   ";
    $sCampos .= " censoufcert.ed260_c_nome as ufcert,   ";
    $sCampos .= " censoufend.ed260_c_nome as ufend,   ";
    $sCampos .= " censomunicnat.ed261_c_nome as municnat,   ";
    $sCampos .= " censomuniccert.ed261_c_nome as municcert,   ";
    $sCampos .= " censomunicend.ed261_c_nome as municend,   ";
    $sCampos .= " censoorgemissrg.ed132_c_descr as orgemissrg,   ";
    $sCampos .= " pais.ed228_c_descr, ";
    $sCampos .= " censocartorio.ed291_c_nome";
    $sSql     = $oDaoAluno->sql_query_ficha_matricula("",  $sCampos, "ed47_v_nome", " ed60_i_codigo IN ($oGet->alunos) ");

    $rsResult = db_query($sSql);
    if( !is_resource( $rsResult ) ) {

        $oErro->sErro = pg_last_error();
        throw new DBException( _M( MENSAGENS_EDU2_FICHAALUNO001 . 'erro_buscar_dados_aluno', $oErro ) );
    }

    $iLinhasAluno = pg_num_rows( $rsResult );

    if( $iLinhasAluno == 0 ) {
        throw new BusinessException(_M(MENSAGENS_EDU2_FICHAALUNO001 . 'nenhum_registro_encontrado'));
    }

    $sSqlTipoSanguineo = $oDaoTipoSanguineo->sql_query_file("", "*", "sd100_sequencial", "");
    $rsTipoSanguineo   = db_query($sSqlTipoSanguineo);

    if( !is_resource( $rsTipoSanguineo ) ) {

        $oErro->sErro = pg_last_error();
        throw new DBException( _M( MENSAGENS_EDU2_FICHAALUNO001 . 'erro_buscar_tipo_sanguineo', $oErro ) );
    }

    $iLinhas           = pg_num_rows( $rsTipoSanguineo );
    $aTiposSanguineos  = array();

    if ( $iLinhas > 0) {

        for ( $iContador = 0; $iContador < $iLinhas; $iContador++ ) {

            $oDados = db_utils::fieldsMemory($rsTipoSanguineo, $iContador);
            $aTiposSanguineos[$oDados->sd100_sequencial] = $oDados->sd100_tipo;
        }
    }

    $oPdf = new ECidade\Pdf\Pdf();
    $oPdf->init(false);
    $oPdf->AliasNbPages();
    $oPdf->setfillcolor(223);
    $oPdf->SetAutoPageBreak(true, 10);

    function imprimeRematricula($endEscola, $nomeResponsavel, $nomeDiretor)
    {
        global $oPdf, $oGet;
        $oPdf->setfont('arial', '', 7);
        $altAssRem = $oPdf->GetY();
        $oPdf->cell(194, 25, "", 1, 1, "C", 0);
        $oPdf->SetY($altAssRem + 2);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(40, 4, " Solicito a rematrícula no curso: ", 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);

        $eixoX = $oPdf->GetX();
        $oPdf->setXY($eixoX, $altAssRem + 3);
        $oPdf->cell(2, 2, "", 1, 0, "C", 0);
        $oPdf->setXY($eixoX + 2, $altAssRem + 2);
        $oPdf->cell(30, 4, " EDUCAÇÃO INFANTIL", 0, 0, "L", 0);


        $eixoX = $oPdf->GetX();
        $oPdf->setXY($eixoX, $altAssRem + 3);
        $oPdf->cell(2, 2, "", 1, 0, "C", 0);
        $oPdf->setXY($eixoX + 2, $altAssRem + 2);
        $oPdf->cell(23, 4, " FUNDAMENTAL", 0, 0, "L", 0);

        $eixoX = $oPdf->GetX();
        $oPdf->setXY($eixoX, $altAssRem + 3);
        $oPdf->cell(2, 2, "", 1, 0, "C", 0);
        $oPdf->setXY($eixoX + 2, $altAssRem + 2);
        $oPdf->cell(8, 4, " EJA", 0, 0, "L", 0);

        $eixoX = $oPdf->GetX();
        $oPdf->setXY($eixoX, $altAssRem + 3);
        $oPdf->cell(2, 2, "", 1, 0, "C", 0);
        $oPdf->setXY($eixoX + 2, $altAssRem + 2);
        $oPdf->cell(28, 4, " OUTRO ____________", 0, 0, "L", 0);

        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(30, 4, " Etapa: _________________________________", 0, 1, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(194, 4, "", 0, 1, "C", 0);
        // Assinatura Responsável
        $oPdf->cell(50, 4, " $endEscola,  ___/___/____ ", 0, 0, "C", 0);
        $height = $oPdf->getY();
        $oPdf->cell(47, 4, " _____________________________________", 0, 1, "C", 0);
        $oPdf->cell(50, 1, "", 0, 0, "C", 0);
        $oPdf->setfont('arial', 'b', 6);
        $oPdf->cell(47, 4, $oGet->lExibeAssinaturaResponsavel == "true" ? $nomeResponsavel : "", 0, 1, "C", 0);
        $oPdf->cell(50, 1, "", 0, 0, "C", 0);
        $oPdf->cell(47, 4, "Assinatura do Responsável", 0, 1, "C", 0);
        // Assinatura Diretor
        $oPdf->setfont('arial', '', 7);
        $oPdf->setY($height);
        $oPdf->cell(100, 1, "", 0, 0, "C", 0);
        $oPdf->cell(37, 4, " Deferido em: ___/___/____", 0, 0, "R", 0);
        $oPdf->cell(57, 4, "_______________________________________", 0, 1, "L", 0);
        $oPdf->setfont('arial', 'b', 6);
        $oPdf->cell(137, 1, "", 0, 0, "C", 0);
        $oPdf->cell(57, 4, $oGet->lExibeAssinaturaDiretor == "true" ? $nomeDiretor : "", 0, 1, "C", 0);
        $oPdf->cell(137, 1, "", 0, 0, "C", 0);
        $oPdf->cell(57, 4, "Assinatura da Direção", 0, 1, "C", 0);
        $oPdf->cell(194, 5, "", 0, 1, "C", 0);
    }

    for ($iCont = 0; $iCont < $iLinhasAluno; $iCont++) {
        db_fieldsmemory($rsResult, $iCont);
        $oPdf->addTitulo("FICHA DE MATRÍCULA", 1);
        $oPdf->addTitulo("$ed47_i_codigo - $ed47_v_nome", 2);
        $oPdf->addpage('P');

        /** DADOS PESSOAIS */

        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(160, 4, "   DADOS PESSOAIS", "LBT", 0, "L", 1);
        $oPdf->cell(34, 4, "   FOTO", 1, 1, "L", 1);
        $oPdf->cell(160, 2, "", "LR", 0, "C", 0);
        $oPdf->cell(34, 2, "", "LR", 1, "C", 0);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(35, 4, strip_tags($Led47_v_nome), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(120, 4, $ed47_v_nome, 0, 0, "L", 0);
        $oPdf->cell(2, 4, "", "R", 1, "C", 0);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(35, 4, strip_tags($Led47_i_codigo), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(20, 4, $ed47_i_codigo, 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_c_codigoinep), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(20, 4, $ed47_c_codigoinep, 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(25, 4, strip_tags($Led47_c_nis), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(25, 4, $ed47_c_nis, 0, 0, "L", 0);
        $oPdf->cell(2, 4, "", "R", 1, "C", 0);

        if ($ed47_o_oid != 0) {

            $pArquivo = "tmp/" . $ed47_c_foto;

            db_inicio_transacao();
            $lResultExport = pg_lo_export($ed47_o_oid, $pArquivo, $conn);
            db_fim_transacao();

            $oPdf->Image($pArquivo, 177, 43, 20);
        }

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(35, 4, strip_tags($Led47_d_nasc), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(20, 4, db_formatar($ed47_d_nasc, 'd'), 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_v_sexo), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(20, 4, $ed47_v_sexo == "M" ? "MASCULINO" : "FEMININO", 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(25, 4, strip_tags($Led47_i_estciv), 0, 0, "R", 0);

        if ($ed47_i_estciv == 1) {
            $ed47_i_estciv = "SOLTEIRO";
        } else if ($ed47_i_estciv == 2) {
            $ed47_i_estciv = "CASADO";
        } else if ($ed47_i_estciv == 3) {
            $ed47_i_estciv = "VIÚVO";
        } else {
            $ed47_i_estciv = "DIVORCIADO";
        }

        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(25, 4, $ed47_i_estciv, 0, 0, "L", 0);
        $oPdf->cell(2, 4, "", "R", 1, "C", 0);
        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(35, 4, strip_tags($Led47_tiposanguineo), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(65, 4, $ed47_tiposanguineo == "" ? "NÃO INFORMADO" : $aTiposSanguineos[$ed47_tiposanguineo], 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_c_raca), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(25, 4, $ed47_c_raca, 0, 0, "L", 0);
        $oPdf->cell(2, 4, "", "R", 1, "C", 0);
        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(35, 4, strip_tags($Led47_i_filiacao), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(120, 4, $ed47_i_filiacao == "0" ? "NÃO DECLARADO / IGNORADO" : "PAI E/OU MÃE", 0, 0, "L", 0);
        $oPdf->cell(2, 4, "", "R", 1, "C", 0);
        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);


        $oPdf->cell(35, 4, strip_tags($Led47_v_mae), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(120, 4, $ed47_v_mae, 0, 0, "L", 0);
        $oPdf->cell(2, 4, "", "R", 1, "C", 0);
        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(35, 4, strip_tags($Led47_v_pai), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(120, 4, $ed47_v_pai, 0, 0, "L", 0);
        $oPdf->cell(2, 4, "", "R", 1, "C", 0);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(35, 4, strip_tags($Led47_c_nomeresp), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(120, 4, $ed47_c_nomeresp, 0, 0, "L", 0);
        $oPdf->cell(2, 4, "", "R", 1, "C", 0);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(35, 4, strip_tags($Led47_c_emailresp), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(60, 4, $ed47_c_emailresp, 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(35, 4, strip_tags($Led47_celularresponsavel), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(26, 4, $ed47_celularresponsavel, 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 0, "C", 0);
        $oPdf->Ln();
        $oPdf->line(204, 35, 204, 80);

        //////////////////////////////////////////////////////////

        $oPdf->cell(160, 2, "", "LR", 0, "C", 0);
        $oPdf->cell(34, 2, "", "LR", 1, "C", 0);
        $oPdf->cell(194, 4, "ENDEREÇO / CONTATOS", 1, 1, "L", 1);
        $oPdf->cell(194, 2, "", "LR", 1, "C", 0);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_v_ender), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(40, 4, substr($ed47_v_ender, 0, 37), 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_c_numero), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(30, 4, $ed47_c_numero, 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(25, 4, strip_tags($Led47_v_compl), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(35, 4, $ed47_v_compl, 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_i_censoufend), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(40, 4, $ufend, 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_i_censomunicend), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(30, 4, $municend, 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(25, 4, strip_tags($Led47_v_bairro), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(35, 4, substr($ed47_v_bairro, 0, 23), 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_c_zona), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(40, 4, $ed47_c_zona, 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_v_cep), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(90, 4, $ed47_v_cep, 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_v_email), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(40, 4, $ed47_v_email, 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_v_telef), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(30, 4, $ed47_v_telef, 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(25, 4, strip_tags($Led47_v_telcel), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(35, 4, $ed47_v_telcel, 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);

        //////////////////////////////////////////////////////////

        $oPdf->cell(194, 2, "", "LR", 1, "C", 0);
        $oPdf->cell(194, 4, "OUTRAS INFORMAÇÕES", 1, 1, "L", 1);
        $oPdf->cell(194, 2, "", "LR", 1, "C", 0);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_i_nacion), 0, 0, "L", 0);

        if ($ed47_i_nacion == 1) {
            $ed47_i_nacion = "BRASILEIRA";
        } else if ($ed47_i_nacion == 2) {
            $ed47_i_nacion = "BRASILEIRA NO EXTERIOR OU NATURALIZADO";
        } else if ($ed47_i_nacion == 3) {
            $ed47_i_nacion = "ESTRANGEIRA";
        }

        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(40, 4, $ed47_i_nacion, 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_i_pais), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(90, 4, $ed228_c_descr, 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_i_censoufnat), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(40, 4, $ufnat, 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_i_censomunicnat), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(90, 4, $municnat, 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_i_transpublico), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(40, 4, $ed47_i_transpublico == "0" ? "NÃO UTILIZA" : "UTILIZA", 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_c_transporte), 0, 0, "R", 0);

        if ($ed47_c_transporte == 1) {
            $ed47_c_transporte = "ESTADUAL";
        } else if ($ed47_c_transporte == 2) {
            $ed47_c_transporte = "MUNICIPAL";
        } else {
            $ed47_c_transporte = "";
        }

        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(90, 4, $ed47_c_transporte, 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_c_bolsafamilia), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(23, 4, $ed47_c_bolsafamilia == 'N' ? 'NÃO' : 'SIM', 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(47, 4, strip_tags($Led47_c_atenddifer), 0, 0, "L", 0);

        if ($ed47_c_atenddifer == 1) {
            $ed47_c_atenddifer = "EM HOSPITAL";
        } else if ($ed47_c_atenddifer == 2) {
            $ed47_c_atenddifer = "EM DOMICÍLIO";
        } else if ($ed47_c_atenddifer == 3) {
            $ed47_c_atenddifer = "NÃO RECEBE";
        }

        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(30, 4, $ed47_c_atenddifer, 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(25, 4, strip_tags($Led47_v_profis), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(35, 4, substr($ed47_v_profis, 0, 23), 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);

        $sCampos1 = " ed76_d_data,  ";
        $sCampos1 .= " case when ed76_c_tipo = 'M' ";
        $sCampos1 .= " then ed18_c_nome else ed82_c_nome end as nomeescola ";
        $sSql11 = $oDaoAlunoprimat->sql_query("", $sCampos1, "", " ed76_i_aluno = $ed47_i_codigo");
        $rsResult11 = db_query($sSql11);

        if (!is_resource($rsResult11)) {
            $oErro->sErro = pg_last_error();
            throw new DBException(_M(MENSAGENS_EDU2_FICHAALUNO001 . 'erro_buscar_escola_aluno', $oErro));
        }

        if (pg_num_rows($rsResult11) > 0) {
            db_fieldsmemory($rsResult11, 0);
        } else {
            $ed76_d_data = "";
            $nomeescola = "";
        }

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led76_d_data), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(40, 4, db_formatar($ed76_d_data, 'd'), 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led76_i_escola), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(90, 4, substr(trim($nomeescola), 0, 60), 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);

        //////////////////////////////////////////////////////////

        $oPdf->cell(194, 2, "", "LR", 1, "C", 0);
        $oPdf->cell(194, 4, " DOCUMENTOS", 1, 1, "L", 1);
        $oPdf->cell(194, 2, "", "LR", 1, "C", 0);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_c_certidaotipo), 0, 0, "L", 0);

        if ($ed47_c_certidaotipo == "N") {
            $ed47_c_certidaotipo = "NASCIMENTO";
        } else if ($ed47_c_certidaotipo == "C") {
            $ed47_c_certidaotipo = "CASAMENTO";
        } else {
            $ed47_c_certidaotipo = "";
        }

        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(40, 4, $ed47_c_certidaotipo, 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_c_certidaonum), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(90, 4, $ed47_c_certidaonum, 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_c_certidaofolha), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(40, 4, $ed47_c_certidaofolha, 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_c_certidaolivro), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(30, 4, $ed47_c_certidaolivro, 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(25, 4, strip_tags($Led47_c_certidaodata), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(35, 4, db_formatar($ed47_c_certidaodata, 'd'), 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_i_censoufcert), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(40, 4, $ufcert, 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_i_censomuniccert), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(90, 4, $municcert, 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_c_certidaocart), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(160, 4, substr($ed291_c_nome, 0, 90), 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->cell(188, 0.5, "", 1, 0, "C", 1);
        $oPdf->cell(3, 4, "", "R", 1, "C", 0);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_v_ident), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(40, 4, $ed47_v_ident, 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_v_identcompl), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(30, 4, $ed47_v_identcompl, 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(25, 4, strip_tags($Led47_i_censoufident), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(35, 4, $ufident, 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_i_censoorgemissrg), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(100, 4, $orgemissrg, 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(25, 4, strip_tags($Led47_d_identdtexp), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(35, 4, db_formatar($ed47_d_identdtexp, 'd'), 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->cell(188, 0.5, "", 1, 0, "C", 1);
        $oPdf->cell(3, 4, "", "R", 1, "C", 0);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_v_cnh), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(40, 4, $ed47_v_cnh, 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_v_categoria), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(90, 4, $ed47_v_categoria, 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_d_dtemissao), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(40, 4, db_formatar($ed47_d_dtemissao, 'd'), 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_d_dthabilitacao), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(30, 4, db_formatar($ed47_d_dthabilitacao, 'd'), 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(25, 4, strip_tags($Led47_d_dtvencimento), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(35, 4, db_formatar($ed47_d_dtvencimento, 'd'), 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->cell(188, 0.5, "", 1, 0, "C", 1);
        $oPdf->cell(3, 4, "", "R", 1, "C", 0);

        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_v_cpf), 0, 0, "L", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(40, 4, $ed47_v_cpf, 0, 0, "L", 0);
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(30, 4, strip_tags($Led47_c_passaporte), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(35, 4, $ed47_c_passaporte, 0, 0, "L", 0);

        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(20, 4, strip_tags($Led47_cartaosus), 0, 0, "R", 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(35, 4, $ed47_cartaosus, 0, 0, "L", 0);
        $oPdf->cell(1, 4, "", "R", 1, "C", 0);

        //////////////////////////////////////////////////////////

        $oPdf->cell(194, 2, "", "LR", 1, "C", 0);
        $oPdf->cell(194, 4, "   NECESSIDADES ESPECIAIS", 1, 1, "L", 1);

        $sSql22 = $oDaoAlunonecessidade->sql_query("", "*", "ed48_c_descr LIMIT 2", " ed214_i_aluno = $ed47_i_codigo");
        $rsResult22 = db_query($sSql22);

        if (!is_resource($rsResult22)) {
            $oErro->sErro = pg_last_error();
            throw new DBException(_M(MENSAGENS_EDU2_FICHAALUNO001 . 'erro_buscar_necessidade_aluno', $oErro));
        }

        $iContNec = 0;
        $iLinhasNecessidades = pg_num_rows($rsResult22);

        if ($iLinhasNecessidades > 0) {

            $oPdf->cell(3, 4, "", "L", 0, "C", 0);
            $oPdf->setfont('arial', 'b', 7);
            $oPdf->cell(70, 4, "Descrição:", 0, 0, "L", 0);
            $oPdf->cell(120, 4, "Necessidade Maior:", 0, 0, "L", 0);
            $oPdf->cell(1, 4, "", "R", 1, "C", 0);

            for ($iContNec = 0; $iContNec < $iLinhasNecessidades; $iContNec++) {
                db_fieldsmemory($rsResult22, $iContNec);
                $oPdf->cell(3, 4, "", "L", 0, "C", 0);
                $oPdf->setfont('arial', '', 7);
                $oPdf->cell(70, 4, $ed48_c_descr, 0, 0, "L", 0);
                $oPdf->cell(120, 4, $ed214_c_principal, 0, 0, "L", 0);
                $oPdf->cell(1, 4, "", "R", 1, "C", 0);
            }
        } else {
            $oPdf->cell(3, 4, "", "L", 0, "C", 0);
            $oPdf->setfont('arial', '', 7);
            $oPdf->cell(190, 4, "Nenhum registro.", 0, 0, "L", 0);
            $oPdf->cell(1, 4, "", "R", 1, "C", 0);
            $iContNec++;
        }

        for ($iFor = $iContNec; $iFor < 2; $iFor++) {

            $oPdf->cell(3, 4, "", "L", 0, "C", 0);
            $oPdf->cell(190, 4, "", 0, 0, "C", 0);
            $oPdf->cell(1, 4, "", "R", 1, "C", 0);
        }

        //////////////////////////////////////////////////////////
        $sSqlDadosEtapa = "  select ed52_i_ano,
                                ed11_c_descr,
                                ed10_c_descr,
                                ed15_c_nome
                           from matricula
                                inner join turma          on turma.ed57_i_codigo              = matricula.ed60_i_turma
                                inner join turno          on turno.ed15_i_codigo                        = turma.ed57_i_turno
                                inner join calendario     on calendario.ed52_i_codigo         = turma.ed57_i_calendario
                                inner join base           on base.ed31_i_codigo               = turma.ed57_i_base
                                inner join cursoedu       on cursoedu.ed29_i_codigo           = base.ed31_i_curso
                                inner join ensino         on ensino.ed10_i_codigo             = cursoedu.ed29_i_ensino
                                inner join matriculaserie on matriculaserie.ed221_i_matricula = matricula.ed60_i_codigo
                                inner join serie          on serie.ed11_i_codigo              = matriculaserie.ed221_i_serie
                          where matriculaserie.ed221_c_origem = 'S'
                            AND ed60_i_aluno = {$ed47_i_codigo}
                          order by ed60_d_datamatricula desc";
        $rsDadosEtapa = db_query($sSqlDadosEtapa);
        $oDadosEtapa = db_utils::fieldsMemory($rsDadosEtapa, 0);
        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(194, 4, "   INFORMAÇÕES DA MATRICULA (ATUAL)", 1, 1, "L", 1);
        $oPdf->setfont('arial', "", 7);
        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
        $sInformacoesMatriculaEtapa = " Aluno matriculado para o ano {$oDadosEtapa->ed52_i_ano}";
        $sInformacoesMatriculaEtapa .= " na etapa {$oDadosEtapa->ed11_c_descr}";
        $sInformacoesMatriculaEtapa .= " do curso {$oDadosEtapa->ed10_c_descr}";
        $sInformacoesMatriculaEtapa .= " no turno {$oDadosEtapa->ed15_c_nome}";

        $oPdf->cell(191, 4, $sInformacoesMatriculaEtapa, "R", 1, "L", 0);

        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(194, 2, "", "LR", 1, "C", 0);
        $oPdf->cell(194, 4, "   OBSERVAÇÕES GERAIS", 1, 1, "L", 1);
        $obsDecode = utf8_decode($oGet->sObs);
        $campoObs = trim($ed47_t_obs) == "" && $obsDecode == "" ? "Nenhum registro." : substr(trim($ed47_t_obs), 0, 800);
        if (trim($ed47_t_obs) !== "" && trim($obsDecode) !== "") {
            $campoObs .= PHP_EOL;
            $campoObs .= substr(trim($obsDecode), 0, 800);
        } else {
            $campoObs = substr(trim($obsDecode), 0, 800);
        }
        $campoObs = str_replace('<br>', "\n", $campoObs);
        $alt_obs = $oPdf->getY();
        $oPdf->setfont('arial', '', 7);
        $oPdf->cell(3, 42, "", "L", 0, "C", 0);
        $oPdf->multicell(188, 4, $campoObs, 0, "J", 0, 0);
        $oPdf->setXY(201, $alt_obs);
        $oPdf->cell(3, 42, "", "R", 1, "C", 0);
        $oPdf->cell(194, 2, "", "LBR", 1, "C", 0);

        if ($oGet->lExibeAssinaturaDiretor == "true" || $oGet->lExibeAssinaturaResponsavel == "true") {
            $oPdf->setfont('arial', 'b', 7);
            $oPdf->cell(194, 4, "   ASSINATURAS DE MATRÍCULA", 1, 1, "L", 1);
            $oPdf->setfont('arial', '', 7);
        }

        $dataDeferimento = !empty($oGet->iDataEmissao) ? "Data de deferimento: " . $oGet->iDataEmissao : "Data de deferimento: ___/___/_____.";

        $oPdf->cell(194, 1, "", 0, 1, "C", 0);
        $oPdf->cell(97, 4, $dataDeferimento, 0, 0, "C", 0);
        $oPdf->cell(97, 4, "Aceito e concordo com as normas da Unidade de Ensino e solicito a matricula.", 0, 1, "C", 0);
        $oPdf->cell(194, 5, "", 0, 1, "C", 0);

        $oPdf->cell(97, 4, "_________________________________________________________", 0, 0, "C", 0);
        $oPdf->cell(97, 4, "_________________________________________________________", 0, 1, "C", 0);

        $oPdf->setfont('arial', 'b', 7);
        $oPdf->cell(97, 4, $oGet->lExibeAssinaturaDiretor == "true" ? $oGet->diretor : "", 0, 0, "C", 0);
        $oPdf->cell(97, 4, $oGet->lExibeAssinaturaResponsavel == "true" ? $ed47_c_nomeresp : "", 0, 1, "C", 0);
        $oPdf->cell(97, 4, "Assinatura da Direção", 0, 0, "C", 0);
        $oPdf->cell(97, 4, "Assinatura do Responsável", 0, 1, "C", 0);
        $oPdf->setfont('arial', '', 7);

        if ($oGet->lImprimeRematricula == "true") {
            $sSqlDadosEscola = $oDaoEscola->sql_query($escola, '*', '', '');
            $rsResultEscola = db_query($sSqlDadosEscola);

            if (pg_num_rows($rsResultEscola) > 0) {
                db_fieldsmemory($rsResultEscola, 0);
                $endEscola = $munic . ' - ' . $uf;
            }

            $endEscola = !empty($endEscola) ? $endEscola : "________________ - ____";

            $oPdf->AddPage('P');
            $oPdf->setfont('arial', 'b', 7);
            $oPdf->cell(194, 4, "   ASSINATURAS DE REMATRÍCULA", 1, 1, "L", 1);

            while ($oPdf->getY() + 40 < $oPdf->getH()) {
                imprimeRematricula($endEscola, $ed47_c_nomeresp, $oGet->diretor);
            }
        }
    }
    $oPdf->Output('I');
} catch ( Exception $oErro ) {
    db_redireciona('db_erros.php?fechar=true&db_erro=' . $oErro->getMessage());
}
