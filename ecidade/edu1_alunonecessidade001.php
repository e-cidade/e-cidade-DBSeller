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
use App\Domain\Configuracao\Helpers\StorageHelper;

require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("classes/db_alunonecessidade_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));

db_postmemory($_POST);

$clAlunoNecessidade = new cl_alunonecessidade;
$clAlunoCadeirante = new cl_necessidadealunocadeirante;
$clAlunoBPC = new cl_necessidadealunobpc;
$clSubDivisaoAluno = new cl_necessidadesubdivisaoaluno;
$clTipoAtendimentoAluno = new cl_necessidadetipoatendimentoaluno;

$db_opcao = 1;
$db_botao = true;

if (isset($incluir)) {
    try {
        db_inicio_transacao();
        
        $clAlunoNecessidade->sql_record($clAlunoNecessidade->sql_query_file(
            null,
            "ed214_i_necessidade",
            "",
            " ed214_i_aluno = $ed214_i_aluno and ed214_i_necessidade = $ed214_i_necessidade"
        ));
        if ($clAlunoNecessidade->numrows > 0) {
            throw new Exception("Erro: Necessidade já se encontra cadastrada. Caso deseje atulizá-la, selecione a opção 'A' no menu abaixo.");
        }

        $clAlunoNecessidade->sql_record($clAlunoNecessidade->sql_query_file(
            "",
            "ed214_i_codigo",
            "",
            " ed214_i_aluno = $ed214_i_aluno"
        ));
        if ($clAlunoNecessidade->numrows == 0) {
            $ed214_c_principal = "SIM";
       
          /**
           * Incluimos um registro para o aluno na tabela alunorecursoavaliacaoinep, com a opcao NENHUM por padrao
           */
            $oDaoAlunoRecursosAvaliacao                              = new cl_alunorecursosavaliacaoinep();
            $oDaoAlunoRecursosAvaliacao->ed327_aluno                 = $ed214_i_aluno;
            $oDaoAlunoRecursosAvaliacao->ed327_recursosavaliacaoinep = 110;
            $oDaoAlunoRecursosAvaliacao->incluir(null);
            unset($oDaoAlunoRecursosAvaliacao);
        } else {
            $ed214_c_principal = "NÃO";
        }

        if (!empty($oid_arquivoLaudo)) {
            $idFileLaudo = StorageHelper::uploadArquivo($oid_arquivoLaudo, null, true);
            $clAlunoNecessidade->ed214_i_anexo_estorage = $idFileLaudo;
        }
       
        $clAlunoNecessidade->ed214_c_principal = $ed214_c_principal;
        $clAlunoNecessidade->ed214_d_data      = date("Y-m-d", db_getsession("DB_datausu"));
        $clAlunoNecessidade->ed214_i_escola    = db_getsession("DB_coddepto");
        $clAlunoNecessidade->incluir($ed214_i_codigo);
        if ($clAlunoNecessidade->erro_status == "0") {
            throw new Exception($clAlunoNecessidade->erro_msg);
        }

       /*
        * Cadeirante
        */
        $clAlunoCadeirante->excluir(null, "ed189_aluno = $ed214_i_aluno");
        if ($clAlunoCadeirante->erro_status == "0") {
            throw new Exception($clAlunoCadeirante->erro_msg);
        }
        if ($cadeirante == 't') {
            $clAlunoCadeirante->ed189_aluno = $ed214_i_aluno;
            $clAlunoCadeirante->ed189_necessidade = $ed214_i_necessidade;
            $clAlunoCadeirante->incluir(null);
            if ($clAlunoCadeirante->erro_status == "0") {
                 throw new Exception($clAlunoCadeirante->erro_msg);
            }
        }
       
       /*
        * BPC
        */
        $clAlunoBPC->excluir(null, "ed190_aluno = $ed214_i_aluno");
        if ($clAlunoBPC->erro_status == "0") {
            throw new Exception($clAlunoBPC->erro_msg);
        }
        if ($bpc == 't') {
            $clAlunoBPC->ed190_aluno = $ed214_i_aluno;
            $clAlunoBPC->ed190_necessidade = $ed214_i_necessidade;
            $clAlunoBPC->incluir(null);
            if ($clAlunoBPC->erro_status == "0") {
                throw new Exception($clAlunoBPC->erro_msg);
            }
        }
       
       
       /*
        * Subdivisao
        */
        $sWhere  = " ed187_aluno = {$ed214_i_aluno} ";
        $sWhere .= " and ed187_necessidadesubdivisao in (select ed185_sequencial 
                                                          from necessidadesubdivisao 
                                                         where ed185_necessidade = {$ed214_i_necessidade})";
        $clSubDivisaoAluno->excluir(null, $sWhere);
        if ($clSubDivisaoAluno->erro_status == "0") {
            throw new Exception($clSubDivisaoAluno->erro_msg);
        }
        foreach ($codsubdivisao as $value) {
            $clSubDivisaoAluno = new cl_necessidadesubdivisaoaluno;
            $clSubDivisaoAluno->ed187_necessidadesubdivisao = $value;
            $clSubDivisaoAluno->ed187_aluno = $ed214_i_aluno;
            $clSubDivisaoAluno->incluir(null);
            if ($clSubDivisaoAluno->erro_status == "0") {
                throw new Exception($clSubDivisaoAluno->erro_msg);
            }
        }
       
       /*
        * Tipo de Atendimento
        */
        $clTipoAtendimentoAluno->excluir(null, "ed188_aluno = {$ed214_i_aluno}");
        if ($clTipoAtendimentoAluno->erro_status == "0") {
            throw new exception($clTipoAtendimentoAluno->erro_msg);
        }
        foreach ($codtipoatendimento as $value) {
            $clTipoAtendimentoAluno = new cl_necessidadetipoatendimentoaluno;
            $clTipoAtendimentoAluno->ed188_necessidadetipoatendimento = $value;
            $clTipoAtendimentoAluno->ed188_aluno = $ed214_i_aluno;
            $clTipoAtendimentoAluno->incluir(null);
            if ($clTipoAtendimentoAluno->erro_status == "0") {
                 throw new Exception($clTipoAtendimentoAluno->erro_msg);
            }
        }
       
        db_fim_transacao();
       
        db_msgbox("Inclusão realizada com sucesso");
        db_redireciona("edu1_alunonecessidade001.php?ed214_i_aluno={$ed214_i_aluno}&ed47_v_nome={$ed47_v_nome}");
    } catch (Exception $oErro) {
        db_fim_transacao(true);
        db_msgbox($oErro->getMessage());
    }
}

if (isset($alterar)) {
    try {
        db_inicio_transacao();
      
        $rsLaudo = $clAlunoNecessidade->sql_record($clAlunoNecessidade->sql_query_file(
            $ed214_i_codigo,
            'ed214_i_anexo_estorage'
        ));
      
        $idLaudo = db_utils::fieldsMemory($rsLaudo, 0)->ed214_i_anexo_estorage;
        $arquivoLaudo = !empty($idLaudo) ? StorageHelper::downloadArquivo($idLaudo) : "";
      
        if (!empty($oid_arquivoLaudo) && $arquivoLaudo != $oid_arquivoLaudo) {
            $idFileLaudo = StorageHelper::uploadArquivo($oid_arquivoLaudo, null, true);
            $clAlunoNecessidade->ed214_i_anexo_estorage = $idFileLaudo;
        }
      
        $clAlunoNecessidade->ed214_d_data   = date("Y-m-d", db_getsession("DB_datausu"));
        $clAlunoNecessidade->ed214_i_escola = db_getsession("DB_coddepto");
        $clAlunoNecessidade->alterar($ed214_i_codigo);
        if ($clAlunoNecessidade->erro_status == "0") {
            throw new Exception($clAlunoNecessidade->erro_msg);
        }

       /*
        * Cadeirante
        */
        $clAlunoCadeirante->excluir(null, "ed189_aluno = $ed214_i_aluno");
        if ($clAlunoCadeirante->erro_status == "0") {
            throw new Exception($clAlunoCadeirante->erro_msg);
        }
        if ($cadeirante == 't') {
            $clAlunoCadeirante->ed189_aluno = $ed214_i_aluno;
            $clAlunoCadeirante->ed189_necessidade = $ed214_i_necessidade;
            $clAlunoCadeirante->incluir(null);
            if ($clAlunoCadeirante->erro_status == "0") {
                 throw new Exception($clAlunoCadeirante->erro_msg);
            }
        }
       
       /*
        * BPC
        */
        $clAlunoBPC->excluir(null, "ed190_aluno = $ed214_i_aluno");
        if ($clAlunoBPC->erro_status == "0") {
            throw new Exception($clAlunoBPC->erro_msg);
        }
        if ($bpc == 't') {
            $clAlunoBPC->ed190_aluno = $ed214_i_aluno;
            $clAlunoBPC->ed190_necessidade = $ed214_i_necessidade;
            $clAlunoBPC->incluir(null);
            if ($clAlunoBPC->erro_status == "0") {
                throw new Exception($clAlunoBPC->erro_msg);
            }
        }
       
       
       /*
        * Subdivisao
        */
        $sWhere  = " ed187_aluno = {$ed214_i_aluno} ";
        $sWhere .= " and ed187_necessidadesubdivisao in (select ed185_sequencial 
                                                          from necessidadesubdivisao 
                                                         where ed185_necessidade = {$ed214_i_necessidade})";
        $clSubDivisaoAluno->excluir(null, $sWhere);
        if ($clSubDivisaoAluno->erro_status == "0") {
            throw new Exception($clSubDivisaoAluno->erro_msg);
        }
        if (isset($codsubdivisao)) {
            foreach ($codsubdivisao as $value) {
                $clSubDivisaoAluno = new cl_necessidadesubdivisaoaluno;
                $clSubDivisaoAluno->ed187_necessidadesubdivisao = $value;
                $clSubDivisaoAluno->ed187_aluno = $ed214_i_aluno;
                $clSubDivisaoAluno->incluir(null);
                if ($clSubDivisaoAluno->erro_status == "0") {
                    throw new Exception($clSubDivisaoAluno->erro_msg);
                }
            }
        }
       
       /*
        * Tipo de Atendimento
        */
        $clTipoAtendimentoAluno->excluir(null, "ed188_aluno = {$ed214_i_aluno}");
        if ($clTipoAtendimentoAluno->erro_status == "0") {
            throw new exception($clTipoAtendimentoAluno->erro_msg);
        }
        if (isset($codtipoatendimento)) {
            foreach ($codtipoatendimento as $value) {
                $clTipoAtendimentoAluno = new cl_necessidadetipoatendimentoaluno;
                $clTipoAtendimentoAluno->ed188_necessidadetipoatendimento = $value;
                $clTipoAtendimentoAluno->ed188_aluno = $ed214_i_aluno;
                $clTipoAtendimentoAluno->incluir(null);
                if ($clTipoAtendimentoAluno->erro_status == "0") {
                    throw new Exception($clTipoAtendimentoAluno->erro_msg);
                }
            }
        }
      
        db_fim_transacao();
      
        db_msgbox("Alteração realizada com sucesso");
        db_redireciona("edu1_alunonecessidade001.php?ed214_i_aluno={$ed214_i_aluno}&ed47_v_nome={$ed47_v_nome}");
    } catch (Exception $oErro) {
        db_fim_transacao(true);
        db_msgbox($oErro->getMessage());
    }
}

if (isset($excluir)) {
    try {
        db_inicio_transacao();
       
        $db_opcao = 3;
       
        $clAlunoNecessidade->excluir($ed214_i_codigo);
        if ($clAlunoNecessidade->erro_status == "0") {
            throw new Exception($clAlunoNecessidade->erro_msg);
        }

       /*
        * Cadeirante
        */
        $clAlunoCadeirante->excluir(null, "ed189_aluno = $ed214_i_aluno");
        if ($clAlunoCadeirante->erro_status == "0") {
            throw new Exception($clAlunoCadeirante->erro_msg);
        }
       
       /*
        * BPC
        */
        $clAlunoBPC->excluir(null, "ed190_aluno = $ed214_i_aluno");
        if ($clAlunoBPC->erro_status == "0") {
            throw new Exception($clAlunoBPC->erro_msg);
        }
       
       /*
        * Subdivisao
        */
        $sWhere  = " ed187_aluno = {$ed214_i_aluno} ";
        $sWhere .= " and ed187_necessidadesubdivisao in (select ed185_sequencial
                                                          from necessidadesubdivisao
                                                         where ed185_necessidade = {$ed214_i_necessidade})";
        $clSubDivisaoAluno->excluir(null, $sWhere);
        if ($clSubDivisaoAluno->erro_status == "0") {
            throw new Exception($clSubDivisaoAluno->erro_msg);
        }
       
       /*
        * Tipo de Atendimento
        */
        $clTipoAtendimentoAluno->excluir(null, "ed188_aluno = {$ed214_i_aluno}");
        if ($clTipoAtendimentoAluno->erro_status == "0") {
            throw new exception($clTipoAtendimentoAluno->erro_msg);
        }
       
       
       /*
        * Ajuste na necessidade princial (maior)
        */
        $rsNecessidades = $clAlunoNecessidade->sql_record($clAlunoNecessidade->sql_query(
            null,
            "*",
            "ed48_c_descr",
            "ed214_i_aluno = {$ed214_i_aluno}"
        ));

        $oNecessidade = db_utils::fieldsMemory($rsNecessidades, 0);
        $iQtdNecessidades = $clAlunoNecessidade->numrows;

        $clAlunoNecessidade->ed214_i_codigo = $oNecessidade->ed214_i_codigo;
        $clAlunoNecessidade->ed214_i_necessidade = $oNecessidade->ed214_i_necessidade;
        $clAlunoNecessidade->ed214_i_apoio = $oNecessidade->ed214_i_apoio;
        $clAlunoNecessidade->ed214_d_data = $oNecessidade->ed214_d_data;
        $clAlunoNecessidade->ed214_i_tipo = $oNecessidade->ed214_i_tipo;
        $clAlunoNecessidade->ed214_i_escola = $oNecessidade->ed214_i_escola;
        $clAlunoNecessidade->ed214_i_anexo_estorage = $oNecessidade->ed214_i_anexo_estorage;
        $descricaoNecessidade = $oNecessidade->ed48_c_descr;

        if ($iQtdNecessidades==1) {
            $clAlunoNecessidade->ed214_c_principal = 'SIM';
           
            $clAlunoNecessidade->alterar($oNecessidade->ed214_i_codigo);
            if ($clAlunoNecessidade->erro_status == "0") {
                throw new Exception($clAlunoNecessidade->erro_msg);
            }
        } elseif ($iQtdNecessidades>1) {
            $necessidadePrincipal = "";
            for ($i=0; $i<$iQtdNecessidades; $i++) {
                $oNecessidadeMaior = db_utils::fieldsMemory($rsNecessidades, $i);
                if ($oNecessidadeMaior->ed214_c_principal=="SIM") {
                    $necessidadePrincipal = $oNecessidadeMaior->ed214_i_codigo;
                }
            }
           
            if (empty($necessidadePrincipal)) {
                $clAlunoNecessidade->ed214_c_principal = 'SIM';

                $clAlunoNecessidade->alterar($oNecessidade->ed214_i_codigo);
                if ($clAlunoNecessidade->erro_status == "0") {
                    throw new Exception($clAlunoNecessidade->erro_msg);
                }
            }
           
            db_msgbox("ATENÇÃO! A necessidade especial {$descricaoNecessidade}\\nficou definida como necessidade Maior deste aluno!");
        }
       
       /**
        * Apos excluir a necessidade especial do aluno, verificamos se ele possui alguma outra necessidade.
        * Caso nao tenha mais nenhuma necessidade, verificamos se o aluno possui recursos para avaliacao do INEP cadastrados,
        * removendo estes
        */
        $oAluno = AlunoRepository::getAlunoByCodigo($ed214_i_aluno);
       
        if (count($oAluno->getNecessidadesEspeciais()) == 0) {
            if (count($oAluno->getRecursosAvaliacao()) > 0) {
                $oDaoExclusaoAlunoRecursosAvaliacao = new cl_alunorecursosavaliacaoinep();
                $oDaoExclusaoAlunoRecursosAvaliacao->excluir(null, "ed327_aluno = {$oAluno->getCodigoAluno()}");
            }
        }
       
        AlunoRepository::removerAluno($oAluno);
       
        db_fim_transacao();
       
        db_msgbox("Exclusão realizada com sucesso!");
        db_redireciona("edu1_alunonecessidade001.php?ed214_i_aluno={$ed214_i_aluno}&ed47_v_nome={$ed47_v_nome}");
    } catch (Exception $oErro) {
        db_fim_transacao(true);
        db_msgbox($oErro->getMessage());
    }
}

if (isset($atualizar)) {
    try {
        db_inicio_transacao();
       
        $sqlNecessidadePrincipal = $clAlunoNecessidade->sql_query_file($principal);
        $rsNecessidadePrincipal = $clAlunoNecessidade->sql_record($sqlNecessidadePrincipal);
        if ($clAlunoNecessidade->numrows == 0) {
            throw new Exception("Erro ao buscar os dados da necessidade maior selecionada");
        }
       
        $dadosNecessidadePrincipal = db_utils::fieldsMemory($rsNecessidadePrincipal, 0);

        $clAlunoNecessidade->ed214_i_codigo         = $dadosNecessidadePrincipal->ed214_i_codigo;
        $clAlunoNecessidade->ed214_c_principal      = "SIM";
        $clAlunoNecessidade->ed214_i_necessidade    = $dadosNecessidadePrincipal->ed214_i_necessidade;
        $clAlunoNecessidade->ed214_i_aluno          = $dadosNecessidadePrincipal->ed214_i_aluno;
        $clAlunoNecessidade->ed214_i_necessidade    = $dadosNecessidadePrincipal->ed214_i_necessidade;
        $clAlunoNecessidade->ed214_i_apoio          = $dadosNecessidadePrincipal->ed214_i_apoio;
        $clAlunoNecessidade->ed214_d_data           = $dadosNecessidadePrincipal->ed214_d_data;
        $clAlunoNecessidade->ed214_i_tipo           = $dadosNecessidadePrincipal->ed214_i_tipo;
        $clAlunoNecessidade->ed214_i_escola         = $dadosNecessidadePrincipal->ed214_i_escola;
        $clAlunoNecessidade->ed214_i_anexo_estorage = $dadosNecessidadePrincipal->ed214_i_anexo_estorage;
        $clAlunoNecessidade->alterar($principal);
        if ($clAlunoNecessidade->erro_status == "0") {
            throw new Exception($clAlunoNecessidade->erro_msg);
        }

        $sqlNecessidades = $clAlunoNecessidade->sql_query_file(
            null,
            "*",
            "ed214_i_codigo",
            " ed214_i_aluno = {$ed214_i_aluno} and ed214_i_codigo <> {$principal}"
        );
        $rsNecessidades = $clAlunoNecessidade->sql_record($sqlNecessidades);
        $iQtdNecessidades = $clAlunoNecessidade->numrows;
        if ($iQtdNecessidades > 0) {
            for ($i = 0; $i<$iQtdNecessidades; $i++) {
                $oResult = db_utils::fieldsMemory($rsNecessidades, $i);
               
                $clAlunoNecessidade->ed214_i_codigo = $oResult->ed214_i_codigo;
                $clAlunoNecessidade->ed214_i_necessidade = $oResult->ed214_i_necessidade;
                $clAlunoNecessidade->ed214_i_apoio = $oResult->ed214_i_apoio;
                $clAlunoNecessidade->ed214_d_data = $oResult->ed214_d_data;
                $clAlunoNecessidade->ed214_i_tipo = $oResult->ed214_i_tipo;
                $clAlunoNecessidade->ed214_i_escola = $oResult->ed214_i_escola;
                $clAlunoNecessidade->ed214_i_anexo_estorage = $oResult->ed214_i_anexo_estorage;
                $clAlunoNecessidade->ed214_c_principal = "NÃO";
                $clAlunoNecessidade->alterar($oResult->ed214_i_codigo);
                if ($clAlunoNecessidade->erro_status == "0") {
                    throw new Exception($clAlunoNecessidade->erro_msg);
                }
            }
        }

        db_fim_transacao();
        db_msgbox("Atualização da maior necessidade realizada com sucesso!");
        db_redireciona("edu1_alunonecessidade001.php?ed214_i_aluno={$ed214_i_aluno}&ed47_v_nome={$ed47_v_nome}");
    } catch (Exception $oErro) {
        db_fim_transacao(true);
        db_msgbox($oErro->getMessage());
    }
}

if (isset($excluirlaudo)) {
    try {
        db_inicio_transacao();
      
        $clAlunoNecessidade->ed214_i_codigo = $ed214_i_codigo;
        $clAlunoNecessidade->ed214_i_anexo_estorage = null;
        $clAlunoNecessidade->alterar($ed214_i_codigo);
        if ($clAlunoNecessidade->erro_status == "0") {
            throw new Exception($clAlunoNecessidade->erro_msg);
        }
      
        db_fim_transacao();
        db_msgbox("Exclusão do laudo realizada com sucesso!");
        db_redireciona("edu1_alunonecessidade001.php?ed214_i_aluno=$ed214_i_aluno&ed47_v_nome=$ed47_v_nome");
    } catch (Exception $oErro) {
        db_fim_transacao(true);
        db_msgbox($oErro->getMessage());
    }
}

if (isset($cancelar)) {
    db_redireciona("edu1_alunonecessidade001.php?ed214_i_aluno=$ed214_i_aluno&ed47_v_nome=$ed47_v_nome");
}

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<?php
  db_app::load("scripts.js, prototype.js, strings.js, datagrid.widget.js, windowAux.widget.js, dbmessageBoard.widget.js");
  db_app::load('estilos.css, grid.style.css, arrays.js');
?>
</head>
<body>
 <?php include(modification("forms/db_frmalunonecessidade.php"));?>
</body>
</html>
<script>
js_tabulacaoforms("form1","ed214_i_necessidade",true,1,"ed214_i_necessidade",true);
</script>
