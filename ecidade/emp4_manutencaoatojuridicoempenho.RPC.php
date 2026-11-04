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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("dbforms/db_funcoes.php"));

$parametros = JSON::requestParameters();

$oRetorno = new stdClass();
$oRetorno->erro = false;

$aDadosRetorno = [];

$instit = db_getsession("DB_instit");

try {
    db_inicio_transacao();
    switch ($parametros->acao) {
        /**
         * case para obter empenhos
         */
        case "getEmpenhoFiltroManutencao":
            $aWhere = [];

            $aWhere[] = " e60_instit = {$instit} ";

            $sqlEmpenhos  = "select e60_numemp, e60_codemp, e60_anousu, e60_emiss, e60_vlremp, e60_numcgm, z01_nome, ";
            $sqlEmpenhos .= "       e165_descricao, ";
            $sqlEmpenhos .= "       case ";
            $sqlEmpenhos .= "          when e60_codcom in ('1','2','3','4','10','12','14','16','17','19','20', ";
            $sqlEmpenhos .= "                              '21','22','23','24','25','26') ";
            $sqlEmpenhos .= "             then 'LICITAÇÃO' ";
            $sqlEmpenhos .= "          when e60_codcom in ('5','11','18','27') ";
            $sqlEmpenhos .= "             then 'DISPENSA' ";
            $sqlEmpenhos .= "          when e60_codcom in ('6','13') ";
            $sqlEmpenhos .= "             then 'INEXIGIBILIDADE' ";
            $sqlEmpenhos .= "          when e60_codcom in ('7','8','9','15') ";
            $sqlEmpenhos .= "             then 'JUSTIFICATIVA INEXISTENTE' ";
            $sqlEmpenhos .= "       end as tipo_instrumento,";
            $sqlEmpenhos .= "       e171_dados->>'ato_juridico' as ato_juridico,";
            $sqlEmpenhos .= "       e171_dados->>'ug_ato_juridico' as ug_ato_juridico,";
            $sqlEmpenhos .= "       e171_dados->>'instrumento_previo' as instrumento_previo,";
            $sqlEmpenhos .= "       e171_dados->>'ug_instrumento_previo' as ug_instrumento_previo";
            $sqlEmpenhos .= "  from empenho.empempenho ";
            $sqlEmpenhos .= "       inner join protocolo.cgm on cgm.z01_numcgm = empempenho.e60_numcgm ";
            $sqlEmpenhos .= "        left join empenho.empempenhooutrosdados on ";
            $sqlEmpenhos .= "                  empempenhooutrosdados.e171_numemp = empempenho.e60_numemp";
            $sqlEmpenhos .= "        inner join empenho.emptipoatojuridico on ";
            $sqlEmpenhos .= "                   emptipoatojuridico.e166_empempenho = empempenho.e60_numemp";
            $sqlEmpenhos .= "        left join empenho.tipoatojuridico on ";
            $sqlEmpenhos .= "                tipoatojuridico.e165_sequencial = emptipoatojuridico.e166_tipoatojuridico";

            if (!empty($parametros->numeroCgm)) {
                $aWhere[] = " e60_numcgm = {$parametros->numeroCgm}";
            }

            if (!empty($parametros->dataInicial) and !empty($parametros->dataFinal)) {
                $aWhere[] = " e60_emiss between '{$parametros->dataInicial}' and '{$parametros->dataFinal}'";
            }

            if ((!empty($parametros->empenhoInicial) or ($parametros->empenhoInicial) === "0")
            && (!empty($parametros->empenhoFinal) or ($parametros->empenhoInicial === "0"))) {
                $aWhere[] = " e60_codemp::Integer >= {$parametros->empenhoInicial}
                and e60_codemp::Integer <= {$parametros->empenhoFinal}";
            }

            if (!empty($aWhere)) {
                $sqlEmpenhos .= " where " . implode(" and ", $aWhere);
            }

            $sqlEmpenhos .= " order by e60_codemp";

            $empenhos = [];
            $resultEmpenhos = db_query($sqlEmpenhos);
            $numRowsEmpenhos = pg_num_rows($resultEmpenhos);
            if ($numRowsEmpenhos > 0) {
                $empenhos = db_utils::getCollectionByRecord($resultEmpenhos);
            }
            $oRetorno->empenhos = $empenhos;
            break;

        case "salvaOutrosDadosEmpenho":
            $empenhoOutrosDados = new cl_empempenhooutrosdados();

            $resultEmpenhoOutrosDados = $empenhoOutrosDados->sql_record($empenhoOutrosDados->sql_query_file(
                null,
                "e171_numdadosemp, e171_numemp, e171_dados",
                null,
                "e171_numemp = $parametros->empenho"
            ));
            if ($empenhoOutrosDados->numrows == 0) {
                $empenhoOutrosDados->e171_numemp = $parametros->empenho;
                $empenhoOutrosDados->e171_dados = $parametros->dados;
                $empenhoOutrosDados->incluir(null);
            } else {
                $outrosDadosEmpenho = db_utils::fieldsMemory($resultEmpenhoOutrosDados, 0);

                $empenhoOutrosDados->e171_numdadosemp = $outrosDadosEmpenho->e171_numdadosemp;
                $empenhoOutrosDados->e171_numemp = $outrosDadosEmpenho->e171_numemp;
                $empenhoOutrosDados->e171_dados = json_encode(
                    array_merge(
                        json_decode($outrosDadosEmpenho->e171_dados, true),
                        json_decode($parametros->dados, true)
                    )
                );
                $empenhoOutrosDados->alterar($outrosDadosEmpenho->e171_numdadosemp, $outrosDadosEmpenho->e171_numemp);
            }
            if ($empenhoOutrosDados->erro_status == 0) {
                throw new Exception($empenhoOutrosDados->erro_msg);
            }
            break;
    }
} catch (Exception $eErro) {
    $oRetorno->erro = true;
    $oRetorno->menssagem = $eErro->getMessage();
}
db_fim_transacao($oRetorno->erro);
echo JSON::create()->stringify($oRetorno);
