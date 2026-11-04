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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));

use ECidade\Tributario\Cadastro\Iptu\Recadastramento\Processamento;
use ECidade\Tributario\Cadastro\Iptu\Recadastramento\Arquivo\Civitas\Civitas;

$oJson = new services_json();
$oParam = JSON::create()->parse(str_replace("\\", "", $_POST["json"]));
$oRetorno = new stdClass();
$oRetorno->iStatus = 1;
$oRetorno->sMessage = '';

$iAnoAtual = db_getsession("DB_anousu");

$iDepartamento = db_getsession("DB_coddepto");

const ARQUIVO_LOTES = 1;

const ARQUIVO_EDIFICACOES = 2;

const ARQUIVO_TESTADAS = 3;

const GRUPO_CARACTERISTICA_DA_CONSTRUCAO = 16;

try {

    db_inicio_transacao();
    $lErroTransacao = false;

    switch ($oParam->exec) {

        case "importar":

            if (empty($oParam->aArquivos)) {
                throw new ParameterException("Nenhum arquivo informado.");
            }

            if (empty($oParam->sDataArquivo)) {
                throw new ParameterException("Data do arquivo não informada.");
            }

            $oDataArquivo = new DBDate($oParam->sDataArquivo);
            $sDataNomeSchema = $oDataArquivo->getDia() . $oDataArquivo->getMes() . $oDataArquivo->getAno();
            $sNomeSchema = "importacao_" . $sDataNomeSchema;

            $aDescricaoArquivoImportado = array();
            $oCivitas = new Civitas($sNomeSchema);
            $oFiles = db_utils::postMemory($_FILES);

            foreach ($oParam->aArquivos as $oArquivo) {

                $sData = $oArquivo->sData;
                switch ($oArquivo->iTipoArquivo) {
                    case 1:

                        $sDestino = "tmp/" . $oFiles->arquivoLotes["name"];
                        move_uploaded_file($oFiles->arquivoLotes["tmp_name"], $sDestino);
                        $oCivitas->setArquivoLote($sDestino);
                        $aDescricaoArquivoImportado[] = "ARQUIVO DE LOTES";
                        break;

                    case 2:

                        $sDestino = "tmp/" . $oFiles->arquivoEdificacoes["name"];
                        move_uploaded_file($oFiles->arquivoEdificacoes["tmp_name"], $sDestino);
                        $oCivitas->setArquivoConstrucao($sDestino);
                        $aDescricaoArquivoImportado[] = "ARQUIVO DE EDIFICAÇÕES";
                        break;
                }
            }

            $oProcessamento = new Processamento($sNomeSchema);
            $oProcessamento->setArquivosImportados($aDescricaoArquivoImportado);
            $oProcessamento->setDataArquivo($oDataArquivo);
            $oProcessamento->processar();
            $oCivitas->processar();
            $oProcessamento->calcularIptu($oCivitas->getMatriculasImportadas(), $iAnoAtual);
            $oProcessamento->incluirMatriculasImportadas();
            $oRetorno->sMessage = "Arquivos importados com sucesso!";
            break;

        case 'buscarFiltros':

            $oDao = new cl_atualizacaoiptuschema();
            $rs = db_query($oDao->sql_query_file(null, "*", "j142_dataarquivo"));

            if (!$rs) {
                throw new Exception('Não foi possível buscar schemas.');
            }

            $aItens = array_map(function ($oItem) {
                $oDate = new DateTime($oItem->j142_dataarquivo);
                $oItem->sDescricao = "Importação - {$oDate->format('d/m/Y')}";
                return $oItem;
            }, db_utils::getCollectionByRecord($rs));

            $oRetorno->aSchemas = $aItens;
            break;

        case 'buscarSetores':

            if (empty($oParam->iSchema)) {
                throw new ParameterException('Importação não informada.');
            }

            $oDaoSchemaMatricula = new cl_atualizacaoiptuschemamatricula();
            $sCampos = "j30_codi, j30_descr";
            $sWhere = "j144_atualizacaoiptuschema = {$oParam->iSchema}";
            $sGroup = "j30_codi, j30_descr";
            $sOrder = "j30_descr";
            $sSqlSchemaMatricula = $oDaoSchemaMatricula->buscaSetoresQuadras($sCampos, $sWhere, $sGroup, $sOrder,
                $oParam->sSchema);
            $rsSchemaMatricula = db_query($sSqlSchemaMatricula);

            if (!$rsSchemaMatricula) {
                throw new DBException("Erro ao buscar os lotes das matrículas da importação.");
            }

            $oRetorno->aSetores = db_utils::getCollectionByRecord($rsSchemaMatricula);
            break;

        case 'buscarQuadras':

            if (empty($oParam->iSchema)) {
                throw new ParameterException('Importação não informada.');
            }

            if (empty($oParam->sSetor)) {
                throw new ParameterException('Setor não informado.');
            }

            $oDaoSchemaMatricula = new cl_atualizacaoiptuschemamatricula();
            $sCampos = "j34_quadra";
            $sWhere = "j144_atualizacaoiptuschema = {$oParam->iSchema} AND j34_setor = '{$oParam->sSetor}'";
            $sGroup = "j34_quadra";
            $sOrder = "j34_quadra";
            $sSqlSchemaMatricula = $oDaoSchemaMatricula->buscaSetoresQuadras($sCampos, $sWhere, $sGroup, $sOrder,
                $oParam->sSchema);
            $rsSchemaMatricula = db_query($sSqlSchemaMatricula);

            if (!$rsSchemaMatricula) {
                throw new DBException("Erro ao buscar os lotes das matrículas da importação.");
            }

            $oRetorno->aQuadras = db_utils::getCollectionByRecord($rsSchemaMatricula);
            break;

        case 'buscarLotes':

            if (empty($oParam->iSchema)) {
                throw new ParameterException('Importação não informada.');
            }

            if (empty($oParam->sSetor)) {
                throw new ParameterException('Setor não informado.');
            }

            if (empty($oParam->sQuadra)) {
                throw new ParameterException('Quadra não informada.');
            }

            $oDaoSchemaMatricula = new cl_atualizacaoiptuschemamatricula();
            $sCampos = "j34_lote";
            $sWhere  = "j144_atualizacaoiptuschema = {$oParam->iSchema}";
            $sWhere .= " AND j34_setor  = '{$oParam->sSetor}'";
            $sWhere .= " AND j34_quadra = '{$oParam->sQuadra}'";
            $sGroup  = "j34_quadra";
            $sGroup  = "j34_lote";
            $sOrder  = "j34_lote";
            $sSqlSchemaMatricula = $oDaoSchemaMatricula->buscaSetoresQuadrasLotes($sCampos, $sWhere, $sGroup, $sOrder, $oParam->sSchema);
            $rsSchemaMatricula = db_query($sSqlSchemaMatricula);

            if (!$rsSchemaMatricula) {
                throw new DBException("Erro ao buscar os lotes das matrículas da importação.");
            }

            $oRetorno->aLotes = db_utils::getCollectionByRecord($rsSchemaMatricula);
            break;

        case 'buscarMatriculas':

            db_fim_transacao(true);
            if (empty($oParam->sSchema)) {
                throw new Exception('Informe o schema.');
            }

            if (empty($oParam->iSetor)) {
                throw new Exception('Informe o setor.');
            }

            /**
             * MATRICULA_PENDENTE   = 0;
             * MATRICULA_NOVA       = 1;
             * MATRICULA_APROVADA   = 2;
             * MATRICULA_REJEITADA  = 3;
             * MATRICULA_PROCESSADA = 4;
             */
            $sBuscaValorEcidade  = " coalesce((select sum(j21_valor) ";
            $sBuscaValorEcidade .= "    from cadastro.iptucalv ";
            $sBuscaValorEcidade .= "   where j21_anousu = {$iAnoAtual} and j21_matric = j144_matricula ), 0) as valor";

            $sBuscaValorCivita  = "  coalesce((select sum(j21_valor) ";
            $sBuscaValorCivita .= "     from {$oParam->sSchema}.iptucalv ";
            $sBuscaValorCivita .= "    where j21_anousu = {$iAnoAtual} and j21_matric = j144_matricula ), 0) as valor_civita";

            $sBuscaAreaEdificada  = " coalesce((SELECT sum(j39_area)                                        ";
            $sBuscaAreaEdificada .= "             FROM {$oParam->sSchema}.iptuconstr                       ";
            $sBuscaAreaEdificada .= "            WHERE j39_matric = j144_matricula), 0) AS area_edificada   ";

            $sBuscaCaracteristicaConstrucao  =  "   ( SELECT j31_descr ";
            $sBuscaCaracteristicaConstrucao .=  "       FROM caracter ";
            $sBuscaCaracteristicaConstrucao .=  " INNER JOIN carconstr ";
            $sBuscaCaracteristicaConstrucao .=  "         ON j48_caract = j31_codigo AND j48_matric = j144_matricula ";
            $sBuscaCaracteristicaConstrucao .=  "      WHERE j31_grupo = ". GRUPO_CARACTERISTICA_DA_CONSTRUCAO ."   limit 1 ) AS caracteristica_construcao ";

            $sBuscaEndereco =  "SELECT 
                                       ( rua 
                                         ||(CASE WHEN numero IS NOT NULL THEN ', '||numero ELSE '' END) 
                                         ||(CASE WHEN (complemento IS NOT NULL AND trim(complemento) <> '') THEN '/'||complemento ELSE '' END) 
                                         ||' Bairro: '||bairro
                                       ) AS endereco_completo
                                FROM (
                                            SELECT  j88_sigla||' '||j14_nome AS rua ,
                                                    j39_numero AS numero ,
                                                    j39_compl AS complemento ,
                                                    (     SELECT j13_descr
                                                            FROM {$oParam->sSchema}.iptubase
                                                      INNER JOIN {$oParam->sSchema}.lote ON j34_idbql = j01_idbql
                                                      INNER JOIN {$oParam->sSchema}.bairro ON j13_codi = j34_bairro
                                                           WHERE j01_matric = j39_matric
                                                    ) AS bairro ,
                                                    1 AS endereco_principal ,
                                                   'construcao' AS endereco_de
                                              FROM {$oParam->sSchema}.iptuconstr
                                        INNER JOIN {$oParam->sSchema}.ruas ON j14_codigo = j39_codigo
                                        INNER JOIN {$oParam->sSchema}.ruastipo ON j88_codigo = j14_tipo
                                             WHERE j39_matric = j144_matricula
                                   
                                        UNION ALL 
                                   
                                            SELECT  j88_sigla||' '||j14_nome AS rua ,
                                                    NULL AS numero ,
                                                    '' AS complemento ,
                                                    j13_descr AS bairro ,
                                                    0 AS endereco_principal ,
                                                    'lote' AS endereco_de
                                              FROM {$oParam->sSchema}.iptubase
                                        INNER JOIN {$oParam->sSchema}.lote ON j34_idbql = j01_idbql
                                        INNER JOIN {$oParam->sSchema}.bairro ON j13_codi = j34_bairro
                                         LEFT JOIN {$oParam->sSchema}.lotedist ON j54_idbql = j34_idbql
                                         LEFT JOIN {$oParam->sSchema}.ruas ON j14_codigo = j54_codigo
                                         LEFT JOIN {$oParam->sSchema}.ruastipo ON j88_codigo = j14_tipo
                                             WHERE j01_matric = j144_matricula
                                   
                                        UNION ALL

                                            SELECT  j88_sigla||' '||j14_nome AS rua ,
                                                    NULL AS numero ,
                                                    '' AS complemento ,
                                                    j13_descr AS bairro ,
                                                    0 AS endereco_principal ,
                                                    'testada' AS endereco_de
                                              FROM {$oParam->sSchema}.testada
                                        INNER JOIN {$oParam->sSchema}.iptubase ON j01_idbql = j36_idbql
                                        INNER JOIN {$oParam->sSchema}.lote ON j34_idbql = j01_idbql
                                        INNER JOIN {$oParam->sSchema}.bairro ON j13_codi = j34_bairro
                                        INNER JOIN {$oParam->sSchema}.ruas ON j14_codigo = j36_codigo
                                        INNER JOIN {$oParam->sSchema}.ruastipo ON j88_codigo = j14_tipo
                                             WHERE j01_matric = j144_matricula
                                     ) AS enderecos 
                                ORDER BY endereco_principal DESC 
                                LIMIT 1
            ";

            $sSql = " SELECT 
                      j144_matricula, 
                      z01_nome,
                      j144_situacao,
                      j144_processado::int ,
                      valor,
                      valor_civita,
                      area_edificada,
                      caracteristica_construcao,
                      endereco, 
                      j146_motivo_rejeicao,
                      j34_quadra,
                      j34_lote,
                      j06_quadraloc,
                      j06_lote,
                      matric_nova,
                      nova_matricula                     
                       FROM (select ". implode(", ", array(
                    "j144_matricula",
                    "z01_nome",
                    "j144_situacao",
                    "j144_processado::int",
                    "{$sBuscaValorEcidade}",
                    "{$sBuscaValorCivita}",
                    "{$sBuscaAreaEdificada}",
                    "{$sBuscaCaracteristicaConstrucao}",
                    "({$sBuscaEndereco}) as endereco",
                    "j146_motivo_rejeicao",
                    "j34_quadra",
                    "j34_lote",
                    "j06_quadraloc",
                    "j06_lote",
                    "padraoiptubase.j01_matric as matric_nova",
                    "civitasinfoscomplementar.nova_matricula"
                ));

            $sSql .= "   from cadastro.atualizacaoiptuschema ";
            $sSql .= "   join cadastro.atualizacaoiptuschemamatricula on j144_atualizacaoiptuschema = j142_sequencial ";
            $sSql .= "   left join cadastro.atualizacaoiptuschemamotivorejeicao on j146_atualizacaoiptuschemamatricula = j144_sequencial ";
            $sSql .= "   join {$oParam->sSchema}.iptubase on j01_matric = j144_matricula ";
            $sSql .= "   join {$oParam->sSchema}.lote on j34_idbql = j01_idbql  ";
            $sSql .= "   left join {$oParam->sSchema}.cgm      on z01_numcgm = j01_numcgm ";
            $sSql .= "   left join {$oParam->sSchema}.loteloc on j06_idbql = j01_idbql  ";
            $sSql .= "   left join cadastro.iptubase as padraoiptubase on padraoiptubase.j01_matric = j144_matricula ";
            $sSql .= "   left join cadastro.civitasinfoscomplementar on civitasinfoscomplementar.matricula = j144_matricula ";

            $sSql .= "  where j142_schema = '{$oParam->sSchema}' ";

            if (!empty($oParam->iSetor)) {
                $sWhereSql[] = "{$oParam->sSchema}.lote.j34_setor = '{$oParam->iSetor}'";
            }

            if (!empty($oParam->sQuadra)) {
                $sWhereSql[] = "{$oParam->sSchema}.lote.j34_quadra = '{$oParam->sQuadra}'";
            }

            if (!empty($oParam->sLote)) {
                $sWhereSql[] = "{$oParam->sSchema}.lote.j34_lote = '{$oParam->sLote}'";
            }

            if (isset($oParam->iSituacao)) {

                switch ($oParam->iSituacao) {

                    case 0: // Pendente/Nova
                        $iSituacao = '0,1';
                        break;
                    case 2: // Aprovada
                        $iSituacao = $oParam->iSituacao;
                        break;
                    case 3: // Rejeitada
                        $iSituacao = $oParam->iSituacao;
                        break;
                    case 4: // processada
                        $iSituacao = $oParam->iSituacao;
                        break;
                    default:
                        $iSituacao = null;
                        break;
                }

                if (!is_null($iSituacao)) {
                    if ($iSituacao == 4) {
                        $sWhereSql[] = "atualizacaoiptuschemamatricula.j144_processado = 'true'";
                    } else {
                        $sWhereSql[] = "atualizacaoiptuschemamatricula.j144_situacao IN ($iSituacao)  ";
                    }

                } else {

                    if($oParam->iSituacao == 3) {
                        $sWhereSql[] = "atualizacaoiptuschemamatricula.j144_situacao NOT IN ($oParam->iSituacao)";
                        $sWhereSql[] = "atualizacaoiptuschemamatricula.j144_processado is true";
                    }
                }
            }

            if (!empty($oParam->iMatricula)) {
                $sWhereSql[] = "atualizacaoiptuschemamatricula.j144_matricula  = {$oParam->iMatricula}";
            }

            $sSql .= !empty($sWhereSql) ? ' AND '. implode(' AND ', $sWhereSql) : '';
            $sSql .= "  order by 2) AS tab ";

            if (!empty($oParam->sLogradouro)) {
                $sSql .=  " WHERE endereco ilike '%{$oParam->sLogradouro}%'";
            }

            $rs = db_query($sSql);

            if (!$rs) {
                throw new Exception('Nenhum resultado encontrado para os filtros aplicados .');
            }

            $oRetorno->aMatriculas = db_utils::makeCollectionFromRecord($rs,
                function ($oDados) use ($oParam, $iAnoAtual) {

                    db_inicio_transacao();
                    db_query("select fc_putsession('DB_Gerafinanc','0')");
                    //Busca dados atualizados da matricula
                    $sSqlCalculo = "select fc_calculoiptu({$oDados->j144_matricula}::integer,{$iAnoAtual}::integer,true::boolean,false::boolean,false::boolean,false::boolean,false::boolean,array['0','0','0'])";
                    $rsCalculo = db_query($sSqlCalculo);

                    /**
                     * forcar rollback
                     */

                    if (!$rsCalculo) {
                        db_fim_transacao(true);
                    }

                    $sCamposIptuCalv = " coalesce(sum(j21_valor),0) as j21_valor";
                    $sWhere = " j21_anousu = {$iAnoAtual} and j21_matric = {$oDados->j144_matricula} ";
                    db_fim_transacao(true);
                    $oDaoIptuCalv = new cl_iptucalv();
                    $sSqlIptuCalv = $oDaoIptuCalv->sql_query_file(null, $sCamposIptuCalv, null, $sWhere);

                    $rsIptuCalvAtualizado = db_query($sSqlIptuCalv);

                    if (!$rsIptuCalvAtualizado || pg_num_rows($rsIptuCalvAtualizado) == 0) {
                        throw new DBException("Erro ao buscar valores atualizados do IPTU da matrícula {$oDados->j144_matricula}.");
                    }

                    $oDados->valor = db_utils::fieldsMemory($rsIptuCalvAtualizado, 0)->j21_valor;

                    if ($oParam->iFiltro == 1 && $oDados->valor_civita <= $oDados->valor) {
                        return;
                    }

                    if ($oParam->iFiltro == 2 && $oDados->valor_civita >= $oDados->valor) {
                        return;
                    }


                    $oMatricula = new stdClass();
                    $oMatricula->iNovaMatricula = (empty($oDados->matric_nova) ? 1 : 0);
                    $oMatricula->iMatricula     =  (empty($oDados->nova_matricula) ? $oDados->j144_matricula : $oDados->nova_matricula);
                    $oMatricula->sRazao       = $oDados->z01_nome;
                    $oMatricula->nValorAtual  = $oDados->valor;
                    $oMatricula->nValorNovo   = $oDados->valor_civita;
                    $oMatricula->iSituacao    = $oDados->j144_situacao;
                    $oMatricula->lProcessado  = (boolean)$oDados->j144_processado;

                    $oMatricula->sSetor                        = $oParam->iSetor;
                    $oMatricula->sQuadra                       = !empty($oParam->sQuadra) ? $oParam->sQuadra : $oDados->j34_quadra;
                    $oMatricula->sLote                         = !empty($oParam->sLote) ? $oParam->sLote : $oDados->j34_lote;
                    $oMatricula->sQuadraLocalizacao            = !empty($oDados->j06_quadraloc) ? $oDados->j06_quadraloc : '';
                    $oMatricula->sLoteLocalizacao              = !empty($oDados->j06_lote) ? $oDados->j06_lote : '';
                    $oMatricula->sEndereoCompleto              = !empty($oDados->endereco) ? $oDados->endereco : '';
                    $oMatricula->sCaracteristicaConstrucao     = !empty($oDados->caracteristica_construcao) ? $oDados->caracteristica_construcao : '';
                    $oMatricula->sAeu                          = $oDados->area_edificada;
                    $oMatricula->sMotivoRejeicao               = !empty($oDados->j146_motivo_rejeicao) ? $oDados->j146_motivo_rejeicao : '';

                    return $oMatricula;

                });


            if (!empty($oParam->ret) && $oParam->ret == 'json') {

                file_put_contents('tmp/cad4recadastramentomatriculas.json',json_encode($oRetorno->aMatriculas));
                exit(json_encode(array('')));
            }

            if (count($oRetorno->aMatriculas) == 0) {
                throw new Exception("Nenhuma matrícula encontrada para os filtros selecionados.");
            }

            $lErroTransacao = true;
            break;

        case 'rejeitar':

            if (!(is_array($oParam->aMatriculas)) || empty($oParam->aMatriculas)) {
                throw new \ParameterException('Nenhuma matrícula informada.');
            }

            $oProcessamento = new Processamento($oParam->sNomeImportacao);
            $oProcessamento->setCodigoSchema($oParam->iCodigoImportacao);
            $oProcessamento->setMotivoRejeicao($oParam->sMotivoRejeicao);

            if (!empty($oParam->outrasMatriculas) && is_array($oParam->outrasMatriculas)) {
                $oParam->aMatriculas =  $oParam->outrasMatriculas;

            }

            foreach ($oParam->aMatriculas as $iMatricula) {
                $oProcessamento->atualizaSituacaoMatricula ($iMatricula, $oParam->iSituacao);
            }

            $oRetorno->sMessage = "Matrículas rejeitadas.";
            break;

        case 'atualizar':

            if (!(is_array($oParam->aMatriculas)) || empty($oParam->aMatriculas)) {
                throw new \ParameterException('Nenhuma matrícula informada.');
            }

            $oProcessamento = new Processamento($oParam->sNomeImportacao);
            $oProcessamento->setCodigoSchema($oParam->iCodigoImportacao);

            foreach ($oParam->aMatriculas as $iCodigoMatricula) {
                $oProcessamento->atualizaSituacaoMatricula ($iCodigoMatricula, $oParam->iSituacao);
            }

            $oRetorno->sMessage = "Matrículas aprovadas.";

            break;

        case 'processar':

            if (!(is_array($oParam->aMatriculas)) || empty($oParam->aMatriculas)) {
                throw new \ParameterException('Nenhuma matrícula informada.');
            }

            $oProcessamento = new Processamento($oParam->sNomeImportacao);
            $oProcessamento->setCodigoSchema($oParam->iCodigoImportacao);
            $aMatriculasCriadas = array();

            foreach ($oParam->aMatriculas as $oMatricula) {

                $iCodigoMatricula =  $oMatricula->iMatricula;
                $iSituacao        = $oMatricula->iSituacao;
                $iCodigo          = null;

                switch ($iSituacao) {

                    case Processamento::MATRICULA_APROVADA:

                        $iCodigo = $oProcessamento->atualizarMatricula($iCodigoMatricula);

                        if (!empty($iCodigo)) {
                            $aMatriculasCriadas[$iCodigo] = $iCodigo;
                        }

                        break;

                    case Processamento::MATRICULA_REJEITADA:
                        $oProcessamento->atualizaMatriculaRejeitada(
                            $iCodigoMatricula,
                            db_getsession('DB_instit'),
                            db_getsession('DB_id_usuario')
                        );

                        break;
                }
            }

            $sMensagemPadrao  = "Matrícula(s) processada(s) com sucesso.";

            if (count($aMatriculasCriadas) > 1) {

                $sMensagemPadrao .= "\nPara essa importação foram criadas as matrículas (". implode(', ', $aMatriculasCriadas) .").";

            } elseif (count($aMatriculasCriadas) > 0) {

                $sMensagemPadrao .= "\nPara essa importação foi criado a matrícula ". key($aMatriculasCriadas) .".";
            }

            $oRetorno->sMessage = $sMensagemPadrao;

            break;

        case 'buscaRua':
            $sBuscaEndereco =  "SELECT  DISTINCT ON (rua)
                                       numero as cod,
                                       rua as label
                                FROM (
                                            SELECT  j88_sigla||' '||j14_nome AS rua ,
                                                    j39_numero AS numero ,
                                                    j39_compl AS complemento ,
                                                    (     SELECT j13_descr
                                                            FROM {$oParam->sSchema}.iptubase
                                                      INNER JOIN {$oParam->sSchema}.lote ON j34_idbql = j01_idbql
                                                      INNER JOIN {$oParam->sSchema}.bairro ON j13_codi = j34_bairro
                                                           WHERE j01_matric = j39_matric
                                                    ) AS bairro ,
                                                    1 AS endereco_principal ,
                                                   'construcao' AS endereco_de
                                              FROM {$oParam->sSchema}.iptuconstr
                                        INNER JOIN {$oParam->sSchema}.ruas ON j14_codigo = j39_codigo
                                        INNER JOIN {$oParam->sSchema}.ruastipo ON j88_codigo = j14_tipo
                                        UNION ALL 
                                   
                                            SELECT  j88_sigla||' '||j14_nome AS rua ,
                                                    NULL AS numero ,
                                                    '' AS complemento ,
                                                    j13_descr AS bairro ,
                                                    0 AS endereco_principal ,
                                                    'lote' AS endereco_de
                                              FROM {$oParam->sSchema}.iptubase
                                        INNER JOIN {$oParam->sSchema}.lote ON j34_idbql = j01_idbql
                                        INNER JOIN {$oParam->sSchema}.bairro ON j13_codi = j34_bairro
                                         LEFT JOIN {$oParam->sSchema}.lotedist ON j54_idbql = j34_idbql
                                         LEFT JOIN {$oParam->sSchema}.ruas ON j14_codigo = j54_codigo
                                         LEFT JOIN {$oParam->sSchema}.ruastipo ON j88_codigo = j14_tipo
                                            
                                        UNION ALL

                                            SELECT  j88_sigla||' '||j14_nome AS rua ,
                                                    NULL AS numero ,
                                                    '' AS complemento ,
                                                    j13_descr AS bairro ,
                                                    0 AS endereco_principal ,
                                                    'testada' AS endereco_de
                                              FROM {$oParam->sSchema}.testada
                                        INNER JOIN {$oParam->sSchema}.iptubase ON j01_idbql = j36_idbql
                                        INNER JOIN {$oParam->sSchema}.lote ON j34_idbql = j01_idbql
                                        INNER JOIN {$oParam->sSchema}.bairro ON j13_codi = j34_bairro
                                        INNER JOIN {$oParam->sSchema}.ruas ON j14_codigo = j36_codigo
                                        INNER JOIN {$oParam->sSchema}.ruastipo ON j88_codigo = j14_tipo
                                            
                                     ) AS enderecos  where enderecos.rua ILIKE '%{$oParam->sLogradouro}%'
                                 
                                LIMIT 20
            ";


            $rsBuscaEndereco = db_query($sBuscaEndereco);
            $ret  = db_utils::getCollectionByRecord($rsBuscaEndereco);
            die(json_encode($ret));
            // exit(json_encode());


            break;
    }
    db_fim_transacao($lErroTransacao);
} catch (Exception $eErro) {

    db_fim_transacao(true);
    $oRetorno->iStatus = 2;
    $oRetorno->sMessage = $eErro->getMessage();
}

$oRetorno->erro = $oRetorno->iStatus == 2;
echo JSON::create()->stringify($oRetorno);
