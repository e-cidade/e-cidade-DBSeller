<?php

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_utils.php');
require_once modification('dbforms/db_funcoes.php');
include(modification("libs/db_libsys.php"));
require_once(modification("libs/JSON.php"));
include(modification("dbagata/classes/core/AgataAPI.class"));
include(modification("classes/db_db_relatorio_classe.php"));
include(modification("classes/db_db_geradorrelatoriotemplate_classe.php"));
include(modification("model/dbColunaRelatorio.php"));
include(modification("model/dbFiltroRelatorio.php"));
include(modification("model/dbVariaveisRelatorio.php"));
include(modification("model/dbGeradorRelatorio.model.php"));
include(modification("model/dbOrdemRelatorio.model.php"));
include(modification("model/dbPropriedadeRelatorio.php"));
require_once(modification("model/configuracao/DocumentConverter.model.php"));
require_once(modification("std/DBLargeObject.php"));
require_once(modification('src/RecursosHumanos/RH/Assinatura/AssentamentoPortaria/AssinaturaPortaria.php'));

use App\Domain\Patrimonial\Protocolo\Services\PortariaDocumentoService;
use ECidade\RecursosHumanos\RH\Assinatura\AssetamentoPortaria\AssinaturaPortaria;
use ECidade\Lib\File\FileEstorage;
use ECidade\Lib\Request\Storage\Curl\Autenticacao;
use ECidade\Lib\Request\Storage\Curl\Post;
use ECidade\Lib\Request\Storage\File as FilePostStorage;
use ECidade\V3\Extension\Registry;
use App\Domain\Configuracao\Helpers\StorageHelper;
use ECidade\Lib\Request\Storage\Curl\Delete;
use ECidade\Lib\Request\Storage\Curl\Put;
use ECidade\Lib\Request\Storage\Curl\Get;

$get = (array)filter_input_array(INPUT_GET);
$post = (array)filter_input_array(INPUT_POST);
$parametros = (object)array_merge($get, $post);
$oJson   = new Services_JSON();

try {

    $retorno = new stdClass();
    $retorno->erro = false;

    switch ($parametros->exec) {

        case 'getInformacoesUsuario':

            $idUsuario  = db_getsession("DB_id_usuario");
            $usuario    = UsuarioSistemaRepository::getPorCodigo($idUsuario);
            $cgmUsuario = $usuario->getCGM();

            if($cgmUsuario === false) {
                throw new Exception('Não foi possível obter os dados do usuário');
            }

            if($cgmUsuario instanceof CgmJuridico) {
                throw new Exception('Usuário logado deve ser pessoa física');
            }

            $retorno->usuario = (object) array(
                'id'    => $idUsuario,
                'nome'  => $cgmUsuario->getNomeCompleto(),
                'cpf'   => $cgmUsuario->getCpf()
            );

            break;

        case 'gerarArquivo':
            /**
             * Verificar se o arquivo já foi gerado
             */
            $clDocumentoPortaria = new \cl_documentoportaria();
            $sSqlDocumentoPortaria = $clDocumentoPortaria->sql_query_file(
                null, "rh235_portaria", null, "rh235_portaria = {$parametros->iCodigoPortaria}"
            );

            $rsDocumentoPortaria = pg_fetch_assoc(db_query($sSqlDocumentoPortaria));

            if (!empty($rsDocumentoPortaria) && empty($parametros->flagAlteracao)) {
                throw new Exception('Documento para esta portaria já existe.');
            }

            try {
                $retorno = gerarArquivo($parametros);
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }

            break;

        case 'getArquivoEstorage':

            if(empty($parametros->idestorage)) {
                throw new ParameterException('Informe o ID do arquivo que deseja visualizar');
            }

            $fileEstorage   = new FileEstorage();
            $retorno->path  = $fileEstorage->getPath($parametros->idestorage);
            $retorno->path  = preg_replace('/.*?(tmp\/.*...*)$/', "$1", $retorno->path);

            break;

        // Onde busca portarias
        case 'buscaPortarias':
            $idsEstorageAssinar = array();
            $whereIdsEstorageAssinados = '';
            $idUsuario  = db_getsession("DB_id_usuario");
            $usuario    = UsuarioSistemaRepository::getPorCodigo($idUsuario);
            $cgmUsuario = $usuario->getCGM();
            $cpf_cnpj = null;

            if (!empty($parametros->filter)) {
                $filter = json_decode($parametros->filter);

                if (!empty($filter->cSituacao) && $filter->cSituacao == 'A') {

                    // Busca o que tem no estorage para o CPF/CNPJ
                    if ($cgmUsuario) {

                        $cpf_cnpj = '';
                        if ($cgmUsuario instanceof CgmFisico) {
                            $cpf_cnpj = $cgmUsuario->getCpf();
                        } else if ($cgmUsuario instanceof CgmJuridico) {
                            $cpf_cnpj = $cgmUsuario->getCnpj();
                        }

                        if ($cpf_cnpj) {
                            $storageResponse = new Get(Autenticacao::getInstance()->execute());
                            $storageResponse->setRoute("/files/sign?cpf_cnpj={$cpf_cnpj}");
                            $storageResponse->execute();

                            $infoRequest = $storageResponse->getInfo();
                            $response = $storageResponse->getResponse();


                            if ($infoRequest['http_code'] == 200) {
                                $dados = json_decode($response);
                                foreach($dados->data as $documento) {
                                    $idsEstorageAssinar[] = $documento->id;
                                }
                            }
                        }
                    }
                }
            }

            $aWhere         = array(" rh01_instit = ". db_getsession("DB_instit"));
            $aWhereSituacao = array(" total_assinaturas > 0 ");

            if (!empty($filter->sAno)) {

                $aWhere[] = "  h31_anousu = {$filter->sAno}";

            } else {

                $data = new DBDate(date('Y-m-d'));
                $data->modificarIntervalo('-6 months');

                $aWhere[] = " h31_dtportaria >= '". $data->getDate() ."'";
            }

            if (!empty($filter->sTipoPortaria)) {
                $aWhere[] = "  h31_portariatipo = {$filter->sTipoPortaria}";
            }


            if (!empty($filter->sPortariafinal) && !empty($filter->sPortariainicial)) {

                if ($filter->sPortariafinal == $filter->sPortariainicial) {
                    $aWhere[] = " h31_numero = '$filter->sPortariainicial' ";
                } else {

                    if ($filter->sPortariafinal < $filter->sPortariainicial) {
                        $numeroAuxiliar = $filter->sPortariafinal;
                        $filter->sPortariafinal   = $filter->sPortariainicial;
                        $filter->sPortariainicial = $numeroAuxiliar;
                    }

                    $aWhere[] = " cast(h31_numero as integer) between {$filter->sPortariainicial} and {$filter->sPortariafinal} ";
                }
            }

            if (!empty($filter->sPortariainicial) && empty($filter->sPortariafinal)) {
                $aWhere[] = " h31_numero = '$filter->sPortariainicial' ";
            }

            if (!empty($filter->cSituacao)) {

                $aWhereSituacao[] = "  situacao = '{$filter->cSituacao}'";

                if($filter->cSituacao == 'A' && db_getsession("DB_id_usuario") != 1) {
                    $aWhere[] = " db59_usuario = ". db_getsession("DB_id_usuario");
                }
            }

            $sfilter         = implode('  AND  ', $aWhere);
            $sfilterSituacao = implode('  AND  ', $aWhereSituacao);

            $sSql = "
                SELECT
                  *
                FROM (
                    SELECT
                        h31_sequencial as codigo,
                        h31_dtportaria as dataportaria,
                        h31_numero as portaria,
                        h16_regist as matricula,
                        h16_histor as informacoes,
                        z01_nome as nomeservidor,
                        h31_anousu as anoportaria,
                        h12_descr as tipoportaria
                        ,coalesce((      SELECT rh236_situacao
                                          FROM portariaassentasituacao
                                    INNER JOIN portariaassenta ON portariaassenta.h33_sequencial = portariaassentasituacao.rh236_portariaassenta
                                         WHERE portariaassenta.h33_portaria = portaria.h31_sequencial
                                      ORDER BY rh236_momento DESC
                                         LIMIT 1
                        ), 'C') as situacao
                        ,coalesce(documentosassinaturas.status, false)::int as status_assinatura
                        ,documentosassinaturas.assinaturas
                        ,documentosassinaturas.idestorage
                        ,documentosassinaturas.url
                        ,coalesce((SELECT
                                      count(distinct ad.db59_usuario)
                                    FROM
                                      portariatipodocindividual as ptdi
                                    INNER JOIN portariatipo as pt ON pt.h30_sequencial = ptdi.h37_portariatipo
                                    INNER JOIN tipoasse as tp ON tp.h12_codigo = pt.h30_tipoasse
                                    INNER JOIN db_relatorio as dbr ON dbr.db63_sequencial = ptdi.h37_modportariaindividual
                                    INNER JOIN assinaturadocumentodesignacao as ad ON ad.db59_relatorio = dbr.db63_sequencial
                                    WHERE
                                      pt.h30_sequencial = portaria.h31_portariatipo
                        ), 0) as total_assinaturas
                    FROM
                        portaria
                    INNER JOIN portariatipo on h31_portariatipo = h30_sequencial
                    INNER JOIN tipoasse ON tipoasse.h12_codigo = portariatipo.h30_tipoasse
                    INNER JOIN portariaassenta on h31_sequencial = h33_portaria
                    INNER JOIN assenta on h16_codigo = h33_assenta
                    INNER JOIN rhpessoal on rh01_regist = h16_regist
                    INNER JOIN cgm on z01_numcgm = rh01_numcgm
                    LEFT  JOIN (
                              SELECT
                                  portaria,
                                  case when count(DISTINCT idestorage) > 0
                                       then (  SELECT array_agg(xx.db177_idestorage)
                                                 FROM (    SELECT DISTINCT
                                                               arquivoestorage.db177_idestorage
                                                              ,arquivoestorage.db177_datadocumento
                                                             FROM arquivoestorage
                                                            WHERE arquivoestorage.db177_idestorage = any(array_agg(x.idestorage))
                                                         ORDER BY
                                                             arquivoestorage.db177_datadocumento DESC
                                                            ,arquivoestorage.db177_idestorage DESC
                                                 ) as xx
                                             )
                                       else null
                                   end as idestorage,
                                  case when count(DISTINCT db177_url) > 0
                                       then (  SELECT array_agg(xx.db177_url)
                                                 FROM (    SELECT DISTINCT
                                                               arquivoestorage.db177_url
                                                              ,arquivoestorage.db177_idestorage
                                                              ,arquivoestorage.db177_datadocumento
                                                             FROM arquivoestorage
                                                            WHERE arquivoestorage.db177_idestorage = any(array_agg(x.idestorage))
                                                         ORDER BY
                                                             arquivoestorage.db177_datadocumento DESC
                                                            ,arquivoestorage.db177_idestorage DESC
                                                 ) as xx
                                             )
                                       else null
                                   end as url,
                                  case when count(DISTINCT cpf) > 0
                                       then true
                                       else false
                                   end AS status,
                                  case when count(DISTINCT cpf) > 0
                                       then array_agg(DISTINCT nome||':'||cpf)
                                       else null
                                  end as assinaturas
                              FROM
                                  (
                                  SELECT
                                      dp.rh235_portaria as portaria,
                                      a.db177_idestorage as idestorage,
                                      a.db177_descricao,
                                      a.db177_datadocumento,
                                      a.db177_url,
                                      a.db177_idestorage_arquivoanterior AS db177_idestorage_arquivoanterior,
                                      ass.db178_cpf as cpf,
                                      ass.db178_nome as nome
                                  FROM
                                      arquivoestorage AS a
                                      INNER JOIN documentoportaria as dp ON dp.rh235_documento = a.db177_idestorage
                                      LEFT JOIN arquivoestorage AS af ON af.db177_idestorage = a.db177_idestorage_arquivoanterior
                                      LEFT JOIN arquivoestorageassinaturas AS aa ON aa.db179_arquivo = a.db177_idestorage
                                      LEFT JOIN assinaturasdocumento AS ass ON ass.db178_sequencial = aa.db179_assinatura
                                  ORDER BY
                                      db177_datadocumento DESC,
                                      db177_idestorage_arquivoanterior DESC,
                                      idestorage DESC
                                  ) as x
                              GROUP BY
                                  portaria
                              ) as documentosassinaturas ON documentosassinaturas.portaria = portaria.h31_sequencial
                    LEFT JOIN portariatipodocindividual ON h37_portariatipo = h31_portariatipo
                    LEFT JOIN db_relatorio ON db63_sequencial = h37_modportariaindividual
                    LEFT JOIN assinaturadocumentodesignacao ON db59_relatorio = db63_sequencial
                    WHERE  {$sfilter}
                    GROUP BY
                         codigo
                        ,dataportaria
                        ,portaria
                        ,matricula
                        ,informacoes
                        ,nomeservidor
                        ,anoportaria
                        ,tipoportaria
                        ,situacao
                        ,status_assinatura
                        ,assinaturas
                        ,idestorage
                        ,url
                        ,total_assinaturas
                ) as x
                WHERE
                    {$sfilterSituacao}
                ORDER BY
                  anoportaria DESC,
                  portaria::int DESC
                LIMIT 50
            ";

            $assinante = buscaAssinante();

            $contador = 0;

            $retorno->portarias = db_utils::makeCollectionFromRecord($rsSql = db_query($sSql), function($item) use (
                $idsEstorageAssinar,
                $idUsuario,
                $filter,
                $assinante,
                $cpf_cnpj,
                $usuario,
                &$contador
            ){

                // verificador se foi assinado pelo usuário
                if( $filter->cSituacao == 'A' ) {
                    if(!empty($item->assinaturas)) {
                        $assinaturas = str_replace(array('{', '}', "'", '"'), "", $item->assinaturas);
                        $assinaturas = explode(',', $assinaturas);

                        if(is_array($assinaturas) && count($assinaturas) > 0) {

                            $assinaturasDOC = array();

                            do {
                                $assinatura = current($assinaturas);
                                if(!(empty($assinatura) || $assinatura == "NULL")) {
                                    $assinaturasDOC[] = (object)array_combine(array('nome', 'doc'), explode(":", $assinatura));
                                }

                            } while (next($assinaturas));

                            foreach($assinaturasDOC as $key => $value) {

                                if($cpf_cnpj === $value->doc) {
                                    return null;
                                }

                            }
                        }

                    }
                }


                if (!empty($item->idestorage)) {
                    $item->idestorage = str_replace(array('{', '}'), '', $item->idestorage);
                    $item->idestorage = explode(',', $item->idestorage);

                    if (
                        (empty($idsEstorageAssinar) || !in_array((int) $item->idestorage[0], $idsEstorageAssinar))
                        && $idUsuario != 1
                        && $filter->cSituacao == 'A'
                    ) {
                        return null;
                    }
                }

                $item->dataportaria      = db_formatar($item->dataportaria, 'd');
                $item->status_assinatura = (boolean)$item->status_assinatura;

                $oServidor   = ServidorRepository::getInstanciaByCodigo($item->matricula);
                $dadosCargo  = $oServidor->getDadosCargo();

                $item->cargoservidor = trim($dadosCargo->rh37_descr);


                if (!empty($item->assinaturas)) {

                    $item->assinaturas = str_replace(array('{', '}', "'", '"'), "", $item->assinaturas);
                    $item->assinaturas = explode(',', $item->assinaturas);

                    if(is_array($item->assinaturas) && count($item->assinaturas) > 0) {

                        $assinaturasDocumento = array();

                        do {
                            $assinatura = current($item->assinaturas);
                            if(!(empty($assinatura) || $assinatura == "NULL")) {
                                $assinaturasDocumento[] = (object)array_combine(array('nome', 'cpf'), explode(":", $assinatura));
                            }

                        } while (next($item->assinaturas));

                        $item->assinaturas = $assinaturasDocumento;
                    }
                }

                if (!empty($item->url)) {

                    $item->url = str_replace(array('{"', '"}', '{', '}'), '', $item->url);
                    $item->url = explode(',', $item->url);

                    $configApi = (object)Registry::get('app.config')->get('app.api');

                    if (empty($configApi) || empty($configApi->estorage)) {
                        $msg  = "Erro ao buscar as credencias do e-Storage";
                        $msg .= "\nVerifique o arquivo de configuração (application).";
                        throw new ParameterException($msg);
                    }

                    $configStorage = (object)$configApi->estorage;

                    do {
                        $url  = current($item->url);
                        $url  = $configStorage->url . DS . $url;
                        $item->url[key($item->url)] = $url;
                    } while (next($item->url));
                }

                $item->assinante = $assinante;

                return $item;
            });

            break;

        case 'salvarDocumentoAssinado':
            db_inicio_transacao();

            $json         = json_decode($parametros->json);
            $file         = current($json->files);
            $path         = "";
            $father_id    = "";
            $signers_need = [];
            $usuarioInstancia = \UsuarioSistemaRepository::getPorCodigo(db_getsession("DB_id_usuario"));
            $assinante = "";

            if( $json->isB64 ) {
                $data = base64_decode($json->b64file);
                $filename = uniqid() . time() . ".pdf";
                $mountPath = ECIDADE_PATH . "tmp" . DS . $filename;
                file_put_contents($mountPath, $data);
                $path         = $mountPath;
                $father_id = $json->oldIdEstorage;
                $signers_need_obj = $json->signers_need;
                $signers_need = [];
                $assinante = $usuarioInstancia->getNome() . ":" . $usuarioInstancia->getCGM()->getCpf();

                foreach($signers_need_obj as $value) {
                    $tmp = [
                        'name' => $value->nome,
                        'cpf' => $value->cpf_cnpj
                    ];
                    array_push($signers_need, $tmp);
                }

            }else{
                $path         = preg_replace('/.*\/(tmp\/.*?\..*)$/', "$1", $file->filename);
                $assinante = $json->assinante;
            }



            $nroPortaria  = $json->portaria->sPortaria;
            $nroPortaria .= '/';
            $nroPortaria .= $json->portaria->sAno;

            try {
                salvarDocumentoEstorage($path, $nroPortaria, $json->portaria->sId, $assinante, $signers_need, null, $father_id);
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }

            db_fim_transacao();

            $retorno->mensagem = "Documento assinado e gravado com sucesso.";
            break;

        case 'emitirPortariaAssinada':

            db_inicio_transacao();

            $oAssinaturaPortaria = new AssinaturaPortaria();
            $oAssinaturaPortaria->setParamsPortaria(array(
                'portaria' => $parametros->numero_portaria
            ));

            $file = $oAssinaturaPortaria->getPortariaAssinada();
            $retorno->file = $file;

            db_fim_transacao();

            break;

        case 'alterarSituacoesPortarias':

            $portarias = json_decode($parametros->portarias);

            db_inicio_transacao();

            alterarSituacoesPortarias($portarias, $parametros->alterarParaSituacao);

            db_fim_transacao();

            $retorno->mensagem = 'Situação alterada com sucesso.';
            $retorno->data = $portarias;

            break;

        case 'atualizarStatusArquivos':

            $arquivoComTodasAssinaturas = [];
            $arquivos = $parametros->arquivo;

            if (!empty($arquivos)) {
                $multipartParams = array_map(function ($arqId) {
                    return array(
                        "name" => 'files[]',
                        'contents' => $arqId
                    );
                }, $arquivos);

                $storageResponse = new Post(Autenticacao::getInstance()->execute());
                $response = $storageResponse->run("/files/to-sign", $multipartParams);

                foreach($response->data as $documento) {
                    $file_id = !empty($documento->parent) ? $documento->parent : $documento->file_id;

                    if (empty($documento->signers)) {
                        $arquivoComTodasAssinaturas[$file_id] = $file_id;
                    }

                    $novaVersaoArquivoAssinado[$file_id] = (object) array(
                        'id' => $documento->file_id,
                        'url' => !empty($documento->url) ? $documento->url : null
                    );
                }

                db_inicio_transacao();

                if (!empty($arquivoComTodasAssinaturas)) {
                    $documentos = implode(", ", $arquivoComTodasAssinaturas);

                    $rs = db_query("
                        SELECT *
                        FROM documentoportaria
                        WHERE rh235_documento IN ({$documentos})
                    ");

                    if (!$rs) {
                        throw new Exception("Erro ao obter a portaria do arquivo assinado. ({$documentos})");
                    }

                    $portariaAtualizarStatus = db_utils::makeCollectionFromRecord($rs, function ($documentoportaria) {
                        return $documentoportaria->rh235_portaria;
                    });
                    $portarias[] = $portariaAtualizarStatus;

                    alterarSituacoesPortarias($portarias, 'S');
                }

                foreach ($novaVersaoArquivoAssinado as $arquivoPai => $arquivoAssinado) {

                    $rsVerificaExisteArquivo = db_query("
                        SELECT 1
                        FROM arquivoestorage
                        WHERE db177_idestorage = {$arquivoAssinado->id}
                    ");

                    if (!$rsVerificaExisteArquivo) {
                        throw new Exception('Ocorreu um erro ao verificar a existencia do arquivo ({$arquivoAssinado->id).');
                    }

                    if (pg_num_rows($rsVerificaExisteArquivo) > 0) {
                        unset($novaVersaoArquivoAssinado[$arquivoPai]);
                        continue;
                    }

                    if (!empty($arquivoPai)) {
                        $rsDocumentoPortaria = db_query("
                            SELECT
                                dp.rh235_portaria,
                                a.db177_descricao
                            FROM documentoportaria as dp
                            INNER JOIN arquivoestorage as a
                                    ON a.db177_idestorage = dp.rh235_documento
                            WHERE a.db177_idestorage = {$arquivoPai}
                        ");

                        if (!$rsDocumentoPortaria) {
                            throw new Exception('Erro o incluir vnculo de novas versões dos arquivos de portarias.');
                        }

                        $documentoAnterior = db_utils::makeFromRecord($rsDocumentoPortaria, function ($item) {
                            return $item;
                        }, 0);

                        $arquivoestorage = new cl_arquivoestorage();
                        $arquivoestorage->db177_idestorage                 = $arquivoAssinado->id;
                        $arquivoestorage->db177_descricao                  = $documentoAnterior->db177_descricao;
                        $arquivoestorage->db177_datadocumento              = date('Y-m-d');
                        $arquivoestorage->db177_url                        = $arquivoAssinado->url;
                        $arquivoestorage->db177_idestorage_arquivoanterior = $arquivoPai != $arquivoAssinado->id ? $arquivoPai : null;

                        if(!$arquivoestorage->incluir()) {
                            throw new DBException($arquivoestorage->erro_msg);
                        }

                        $documentoportaria = new cl_documentoportaria();
                        $documentoportaria->rh235_portaria   = $documentoAnterior->rh235_portaria;
                        $documentoportaria->rh235_documento  = $arquivoAssinado->id;

                        if(!$documentoportaria->incluir()) {
                            throw new DBException($documentoportaria->erro_msg);
                        }
                    }
                }

                db_fim_transacao();
            }

            break;

        case 'salvarSituacao':

            db_inicio_transacao();

            if (empty($parametros->sCodigoPortaria)) {
                throw new ParameterException('Informe o código da portaria');
            }

            if (empty($parametros->cSituacao)) {
                throw new ParameterException('Informe a situação qual deseja alterar');
            }

            $portariaassentasituacao = new cl_portariaassentasituacao();

            $portariaassentasituacao->rh236_portariaassenta = "(SELECT h33_sequencial FROM portariaassenta WHERE h33_portaria = {$parametros->sCodigoPortaria})";
            $portariaassentasituacao->rh236_situacao        = $parametros->cSituacao;
            $portariaassentasituacao->rh236_momento         = time();

            if(!$portariaassentasituacao->incluir()) {
                throw new DBException($portariaassentasituacao->erro_msg);
            }

            $configStorage = StorageHelper::getStorageConfig();

            // Troca de situaçãõ, de conferido para aguardando assinatura e salva no estorage quem deve assinar
            if ($parametros->cSituacao === 'A' && $configStorage->url) {
                // Busca id do estorage
                $clArquivoEstorage = new \cl_arquivoestorage();

                $rsArquivoEstorage = pg_fetch_assoc(
                    db_query($clArquivoEstorage->sql_query_estorage_por_portaria((int) $sCodigoPortaria))
                );

                $idEstorage = $rsArquivoEstorage['db177_idestorage'] ? $rsArquivoEstorage['db177_idestorage'] : null;

                $clPortaria = new \cl_portaria();

                $sSqlTipoPortaria = $clPortaria->sql_query_file(null, 'h31_portariatipo, h31_numero, h31_anousu', null, "h31_sequencial = {$parametros->sCodigoPortaria}");
                $rsTipoPortaria = pg_fetch_assoc(db_query($sSqlTipoPortaria));

                $codigoTipoPortaria = 0;
                if (!empty($rsTipoPortaria)) {
                    $codigoTipoPortaria = $rsTipoPortaria['h31_portariatipo'];
                }

                // Busca usuários que devem assinar
                $clAssinaturaDocumentoDesignacao = new \cl_assinaturadocumentodesignacao();
                $postgresObjectAssinantes = db_query(
                    $clAssinaturaDocumentoDesignacao->sql_query_assinantes_tipo_portaria((int) $codigoTipoPortaria)
                );


                if (!pg_num_rows($postgresObjectAssinantes)) {
                    $sSqlDescricaoTipo = "SELECT h12_descr FROM tipoasse WHERE h12_codigo = {$codigoTipoPortaria}";
                    $rsDescricaoTipo = db_query($sSqlDescricaoTipo);

                    $aux = pg_fetch_assoc($rsDescricaoTipo);
                    $decricaoTipo = $aux ? $aux['h12_descr'] : null;

                    throw new Exception("Por favor cadastre quem deve assinar os documentos para o tipo de portaria {$decricaoTipo} (código {$codigoTipoPortaria})");
                }

                $signers = [];
                while ($row = pg_fetch_object($postgresObjectAssinantes)) {
                    $oAssinante = new stdClass();

                    $oAssinante->name = $row->nome;
                    $oAssinante->cpf_cnpj = $row->cpf_cnpj;

                    // Isso  necessrio porque o Post faz encode pra json e o Put no
                    $signers[] = !$idEstorage ? $oAssinante : json_encode($oAssinante);
                }

                $atualizarAtributos = [
                    'sign_required' => true,
                    'signers' => $signers
                ];

                // Gera arquivo caso ainda não tenha sido gerado
                if (empty($idEstorage)) {

                    $parametrosGeraArquivo = new stdClass();

                    $parametrosGeraArquivo->aParametros =
                        '[{"sNome":"$portaria","sValor":"'. $rsTipoPortaria['h31_numero'] .'"},{"sNome":"$ano","sValor":"'. $rsTipoPortaria['h31_anousu'] .'"}]'
                    ;
                    $parametrosGeraArquivo->iCodRelatorio = "1000058";
                    $parametrosGeraArquivo->iCodigoPortaria = $sCodigoPortaria;

                    try {
                        $responseGeraArquivo = gerarArquivo($parametrosGeraArquivo, $signers);
                    } catch (Exception $e) {
                        throw new Exception($e->getMessage());
                    }

                    $idEstorage = $responseGeraArquivo->idestorage;

                    if (empty($idEstorage)) {
                        throw new Exception('Erro ao gerar arquivo.');
                    }

                } else {

                    $put = new Put(Autenticacao::getInstance());
                    $put->setFileId($idEstorage);

                    try {
                        $response = $put->update($atualizarAtributos);
                    } catch (Exception $e) {
                        throw new Exception($e->getMessage());
                    }
                }
            }

            db_fim_transacao();

            $retorno->mensagem = 'Situação alterada com sucesso';

            break;

    }

} catch (Exception $exception) {

    if(db_utils::inTransaction()) {
        db_fim_transacao(true);
    }
    $retorno->mensagem = $exception->getMessage();
    $retorno->erro = true;
}

echo JSON::create()->stringify($retorno);

function gerarArquivo($parametros, $signers = null)
{
    /**
     * @todo refatorar isto, fazer requisicao corretamente para isto
     */
    ob_start();
    $_POST = $parametros;
    require 'sys4_processarelatorioRPC.php';
    $processamentoRelatorio = ob_get_clean();
    $_POST = null;

    $processamentoRelatorio = JSON::create()->parse(urldecode($processamentoRelatorio));

    if ($processamentoRelatorio->erro) {
        throw new BusinessException($processamentoRelatorio->sMsg);
    }

    db_inicio_transacao();

    $path = $processamentoRelatorio->sMsg;

    $parametros->aParametros = JSON::create()->parse(urldecode($parametros->aParametros));
    $parametrosPortaria = current($parametros->aParametros);
    $nroPortaria = $parametrosPortaria->sValor;
    $parametrosPortaria = next($parametros->aParametros);
    $nroPortaria .= '/' . $parametrosPortaria->sValor;
    $idEstorage = $parametros->idEstorage;

    try {
        $response = salvarDocumentoEstorage(
            $path,
            $nroPortaria,
            $parametros->iCodigoPortaria,
            null,
            $signers,
            $idEstorage
        );
    } catch (Exception $e) {
        throw new Exception($e->getMessage());
    }

    db_fim_transacao();

    $configStorage = StorageHelper::getStorageConfig();

    $retorno->idestorage = $response->data->id;
    $retorno->url = $configStorage->url;
    $retorno->url .= '/' . $response->data->url;
    $retorno->path = preg_replace('/.*?(tmp\/.*...*)$/', "$1", $path);

    return $retorno;
}

/**
 * O parmetro $assinante  para efetuar a assinatura.
 * O paramtro $signers indica quem deve assinar o documento (informao gravada no estorage)
 * Parmetro $idEstorage  passado na alterao de uma portaria.
 */

function salvarDocumentoEstorage(
    $path,
    $nroPortaria,
    $iCodigoPortaria,
    $assinante = null,
    $signers = null,
    $idEstorage = null,
    $father_id = null
) {
    $file = new FilePostStorage();
    $file->realPath($path);
    $file->clientOriginalName(preg_replace('/.*\/(.*?\..*)$/', "$1", $path));
    $file->visibility('public');



    $arrayPortaria = explode('/', $nroPortaria);

    $clPortaria = new cl_portaria;
    $rsAssentamento = pg_fetch_assoc(db_query($clPortaria->sql_query_assentamento_servidor($iCodigoPortaria)));

    $metadata = new stdClass();

    $metadata->tipo_documento = 'portaria';
    $metadata->numero = "{$arrayPortaria[0]}/{$arrayPortaria[1]}";
    $metadata->matricula_servidor = $rsAssentamento['h16_regist'];
    $metadata->id_assentamento = $rsAssentamento['h16_codigo'];

    $file->metadata($metadata);

    if (!empty($signers)) {
        $file->signersRequired(true);
        $file->signers($signers);
    }

    if( $father_id != null ){
        $file->fileFather($father_id);
    }

    // if( !empty($signer) ) {
    //     $file->signersSigned($signer);
    // }

    $post = new Post(Autenticacao::getInstance());

    try {
        $response = $post->execute($file);
    } catch (Exception $e) {
        throw new Exception($e->getMessage());
    }

    $arquivoestorage = new cl_arquivoestorage();
    $arquivoestorage->db177_idestorage                 = $response->data->id;
    $arquivoestorage->db177_descricao                  = "Portaria número: {$nroPortaria}";
    $arquivoestorage->db177_datadocumento              = date('Y-m-d');
    $arquivoestorage->db177_url                        = $response->data->url;
    $arquivoestorage->db177_idestorage_arquivoanterior = null;

    if(!$arquivoestorage->incluir()) {
        throw new DBException($arquivoestorage->erro_msg);
    }

    $documentoportaria = new cl_documentoportaria();
    $documentoportaria->rh235_portaria   = $iCodigoPortaria;
    $documentoportaria->rh235_documento  = $response->data->id;

    if(!$documentoportaria->incluir()) {
        throw new DBException($documentoportaria->erro_msg);
    }

    // Deleta versão antiga do arquivo
    if ($idEstorage) {
        $rsDocumentoPortaria = pg_fetch_assoc(db_query("SELECT rh235_sequencial FROM documentoportaria WHERE rh235_documento = {$idEstorage}"));

        $documentoPortariaAntigo = new cl_documentoportaria();
        $excluiuDocumentoPortaria = $documentoPortariaAntigo->excluir($rsDocumentoPortaria['rh235_sequencial']);

        if (!$excluiuDocumentoPortaria) {
            throw new DBException($documentoPortariaAntigo->erro_msg);
        }

        $arquivoEstorageAntigo = new cl_arquivoestorage($idEstorage);
        $excluiuArquivoEstorage = $arquivoEstorageAntigo->excluir($idEstorage);

        if (!$excluiuArquivoEstorage) {
            throw new DBException($arquivoEstorageAntigo->erro_msg);
        }

        /**
         * Em caso de erro ao desvincular arquivo antigo com portaria.
         * Se faz necessrio remover o arquivo novo que est de acordo com a alterao da portaria.
         *  */
        if (!$excluiuArquivoEstorage || !$excluiuDocumentoPortaria) {
            try {
                $delete = new Delete(Autenticacao::getInstance());

                $delete->delete($response->data->id);
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }

            throw new Exception('Erro ao remover vinculo entre portaria e arquivo do estorage antigo.');
        }

        try {
            $delete = new Delete(Autenticacao::getInstance());

            $delete->execute($idEstorage);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    if(!empty($assinante)) {

        $nomeAssinante = preg_replace('/^(.*?):.*/', "$1", $assinante);
        $cpfAssinante  = preg_replace(array(
            '/.*?:(.*)$/',
            '/\D/'
        ),
            array(
                "$1",
                ""
            ),
            $assinante
        );

        // $cpfAssinante = substr($cpfAssinante, 0, 11);

        $assinaturasdocumento = new cl_assinaturasdocumento();

        $aWhereAssinantes = array(
            "db178_cpf = '{$cpfAssinante}'"
        );
        $whereAssinantes  = implode(" AND ", $aWhereAssinantes);
        $sqlAssinantes    = $assinaturasdocumento->sql_query_file(null, "db178_sequencial", null, $whereAssinantes);
        $rsAssinantes     = db_query($sqlAssinantes);

        if(!$rsAssinantes) {
            throw new DBException("Ocorreu um erro ao consultar assinantes.\n". pg_last_error());
        }

        $idAssinante = db_utils::fieldsMemory($rsAssinantes, 0)->db178_sequencial;

        if(empty($idAssinante)) {

            $assinaturasdocumento->db178_nome      = $nomeAssinante;
            $assinaturasdocumento->db178_cpf       = $cpfAssinante;
            $assinaturasdocumento->db178_imagem    = null;
            $assinaturasdocumento->db178_metadados = 'null';

            if(!empty($cpfAssinante)) {

                $servidor = ServidorRepository::getByCPF($cpfAssinante);

                if(!empty($servidor)) {

                    $metadadosAssinante = (object)array(
                        'matricula' => $servidor->getMatricula(),
                        'cgm'       => $servidor->getCgm()->getCodigo()
                    );
                    $assinaturasdocumento->db178_metadados = JSON::create()->stringify($metadadosAssinante);
                }
            }

            if(!$assinaturasdocumento->incluir()) {
                throw new DBException($assinaturasdocumento->erro_msg);
            }

            $idAssinante = $assinaturasdocumento->db178_sequencial;
        }

        $arquivoestorageassinaturas = new cl_arquivoestorageassinaturas();

        $arquivoestorageassinaturas->db179_arquivo         = $response->data->id;
        $arquivoestorageassinaturas->db179_assinatura      = $idAssinante;
        $arquivoestorageassinaturas->db179_dataassinatura  = date('Y-m-d');

        if(!$arquivoestorageassinaturas->incluir()) {
            throw new DBException($arquivoestorageassinaturas->erro_msg);
        }

        $assinaturasarquivo = new cl_arquivoestorageassinaturas();
        $aWhereAssinaturasarquivo = array("h31_sequencial = {$iCodigoPortaria}");
        $sqlAssinaturasarquivo    = $assinaturasarquivo->sql_query_limite_assinaturas(
            null
            ,array(
                "portaria.h31_sequencial"
            ,"portaria.h31_numero"
            ,"portaria.h31_anousu"
            ,"count(distinct arquivoestorageassinaturas.db179_assinatura) as totalAssinaturas"
            ,"count(distinct assinaturadocumentodesignacao.db59_usuario) as limiteAssinaturasDocumento"
            )
            ,null
            ,$aWhereAssinaturasarquivo
        );
        $rsAssinaturasarquivo     = db_query($sqlAssinaturasarquivo);

        if(!$rsAssinaturasarquivo) {
            throw new DBException("Ocorreu um erro ao consultar quem assina os documentos.\n". pg_last_error());
        }

        if(pg_num_rows($rsAssinaturasarquivo) > 0) {

            $assinaturasdocumentoportaria = db_utils::fieldsMemory($rsAssinaturasarquivo, 0);

            $totalAssinaturas           = $assinaturasdocumentoportaria->totalassinaturas;
            $limiteAssinaturasDocumento = $assinaturasdocumentoportaria->limiteassinaturasdocumento;

            if($totalAssinaturas == $limiteAssinaturasDocumento) {

                $portariaassentasituacao = new cl_portariaassentasituacao();

                $portariaassentasituacao->rh236_portariaassenta = "(SELECT h33_sequencial
                                                                      FROM portariaassenta
                                                                     WHERE h33_portaria = {$iCodigoPortaria})";
                $portariaassentasituacao->rh236_situacao        = 'S';
                $portariaassentasituacao->rh236_momento         = time();

                if(!$portariaassentasituacao->incluir()) {
                    throw new DBException($portariaassentasituacao->erro_msg);
                }
            }
        }
    }

    return $response;
}

function alterarSituacoesPortarias($portarias, $sSituacaoAlterar)
{
    $sNumerosPortarias = implode("', '", $portarias);
    $sSql = "
        SELECT
            h31_sequencial
        FROM
            portaria
        WHERE
            CONCAT(h31_numero, '/', h31_anousu) IN ('{$sNumerosPortarias}')
    ";

    $rsPortarias = db_query($sSql);

    while ($row = pg_fetch_assoc($rsPortarias)) {
        $portariaassentasituacao = new cl_portariaassentasituacao();
        $portariaassentasituacao->rh236_portariaassenta = "(
            SELECT h33_sequencial
            FROM portariaassenta
            WHERE h33_portaria = {$row['h31_sequencial']})";
        $portariaassentasituacao->rh236_situacao        = $sSituacaoAlterar;
        $portariaassentasituacao->rh236_momento         = time();

        if(!$portariaassentasituacao->incluir()) {
            throw new DBException($portariaassentasituacao->erro_msg);
        }
    }
}


function buscaAssinante()
{
    $instituicao = db_getsession('DB_instit');
    $idUsuario = db_getsession('DB_id_usuario');

    $sql = "
    select trim(z01_nome) || ' (' ||lpad(db_usuarios.id_usuario,10,'0') || ')' as nome_assinar,
           'Matricula:' || lpad(rh01_regist,10,'0') || '-CPF:' || z01_cgccpf || '-' ||case when rh04_descr is null then rh37_descr else rh04_descr end::varchar as papel,
           z01_cgccpf as cpf,
           z01_nome as nome
       from db_depusu
            inner join db_usuarios on db_usuarios.id_usuario = db_depusu.id_usuario
            inner join db_usuacgm on db_usuacgm.id_usuario = db_usuarios.id_usuario
            inner join cgm on z01_numcgm = cgmlogin
            inner join rhpessoal on rh01_numcgm = cgmlogin
            inner join rhpessoalmov on rh02_regist = rh01_regist and rh02_anousu = fc_anofolha({$instituicao}) and rh02_mesusu = fc_mesfolha({$instituicao})
            left join rhpesrescisao on rh05_seqpes = rh02_seqpes

            left join rhfuncao on (rh02_funcao, rh02_instit) = (rh37_funcao, rh37_instit)
            left join rhpescargo on rh20_seqpes = rh02_seqpes
            left join rhcargo on (rh20_cargo, rh20_instit) = (rh04_codigo, rh04_instit)
        where db_usuarios.id_usuario = {$idUsuario}
        and rh05_seqpes is null
        limit 1;
        ";

    $rs = db_query($sql);
    if (pg_numrows($rs) > 0) {
        return pg_fetch_object($rs, 0);
    }

    return null;
}
