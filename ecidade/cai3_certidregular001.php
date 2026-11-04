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

use ECidade\Tributario\Arrecadacao\Repository\CertidaoTemplateRepository;
use ECidade\Tributario\Arrecadacao\Model\CertidaoTemplateModel;

use ECidade\Tributario\Arrecadacao\Repository\CertidaoTemplateCgmRepository;
use ECidade\Tributario\Arrecadacao\Model\CertidaoTemplateCgmModel;

use ECidade\Tributario\Arrecadacao\Repository\CertidaoTemplateMatriculaRepository;
use ECidade\Tributario\Arrecadacao\Model\CertidaoTemplateMatriculaModel;

use ECidade\Tributario\Arrecadacao\Repository\CertidaoTemplateInscricaoRepository;
use ECidade\Tributario\Arrecadacao\Model\CertidaoTemplateInscricaoModel;

require_once(modification("libs/db_sql.php"));
require_once(modification("libs/db_libsys.php"));
require_once(modification("fpdf151/pdf1.php"));
require_once(modification("libs/db_libtributario.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("dbagata/classes/core/AgataAPI.class"));
require_once(modification("model/documentoTemplate.model.php"));
require_once(modification("std/DBLargeObject.php"));
require_once("std/db_stdClass.php");
require_once(modification("dbforms/db_funcoes.php"));

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

try {
    // Objeto com informações do arquivo/caminho
    $oInfoArquivoPdf = buscoDadosArquivoPdf($sTipoCertidao, $titulo, $origem, $codproc, $tipo);

    // Objeto com informações do header do arquivo
    $oQueryHeader = buscaDadosCabecalhoPdf($titulo, $origem, $tipo, $textarea, $tipocertidao, $codproc);

    // Objeto com informações do body do arquivo
    $oQueryBody = buscaDadosCorpoPdf($tipo, $titulo);

    getByPdf(57, $oInfoArquivoPdf, $oQueryHeader, $oQueryBody);

    salvaCertidao($oInfoArquivoPdf);

    exit;

} catch (Exception $e) {
    db_redireciona("db_erros.php?fechar=true&db_erro=Nenhum Registro Encontrado!");
}

function buscoDadosArquivoPdf($sTipoCertidao, $titulo, $origem, $codproc, $tipo)
{
    $aParam = array();

    $sCampo = getCamposTemplate($tipo, $titulo)->templateCertidao;

    // Busca o template da certidão a ser impresso
    $certidaoRepository = CertidaoTemplateRepository::getInstance();
    // Busca o ID do template cadastrado, ou NULL se não houver
    $template = $certidaoRepository->getByDadosCertidao($sCampo);

    $variaveisSessao = getVariaveisSessao();

    // Seta os dados a serem utilizados no PDF
    $sDescrDoc        = date("YmdHis").$variaveisSessao->id_usuario;
    $sNomeRelatorio   = "tmp/certidao{$sTipoCertidao}.pdf";
    $sCaminhoSalvoSxw = "tmp/certidao{$sTipoCertidao}_{$sDescrDoc}.sxw";
    $sAgt             = "arrecadacao/certidaoPadrao.agt";

    // Formata a data por extenso
    $iMes        = date('m');
    $iAno        = date('Y');
    $iDia        = date('d');
    $sMes        = DBDate::getMesExtenso($iMes);
    $dataExtenso = ' ' . $iDia .' de ' . $sMes . ' de '.$iAno;

    $aParam['instituicao']    = $variaveisSessao->instit;
    $aParam['dataextensosql'] = $dataExtenso;
    $aParam['codigo_verificacao'] = DBTributario::emitirCodigoCerticao(date("Y-m-d"), 
                                                                      date("H:i"), 
                                                                      date("s")
                                                                     );

    if ($codproc != "") {
        if (strpos($codproc, "/") > 0) {
            $codproc   =explode("/", $codproc);
            $exercicio = $codproc[1];
            $codproc   = $codproc[0];
        } else {
            $codproc   = $codproc;
            $exercicio = $variaveisSessao->anousu;
        }
    } else {
        $codproc   = "";
        $exercicio = 0;
    }

    return (object) array(
        'sNomeRelatorio'    => $sNomeRelatorio,
        'titulo'            => $titulo,
        'origem'            => $origem,
        'tipo'              => $tipo,
        'codproc'           => $codproc,
        'sTipoCertidao'     => $sTipoCertidao,
        'sCaminhoSalvoSxw'  => $sCaminhoSalvoSxw,
        'sCaminhoAgt'       => $sAgt,
        'sTemplate'         => (integer) $template,
        'sInstituicao'      => $variaveisSessao->instit,
        'oParametros'       => (object) $aParam,
        'iExercicio'        => $exercicio
    );
}

function buscaDadosCabecalhoPdf($titulo, $origem, $tipo, $textarea = "", $tipocertidao, $codproc)
{
    $aQuery          = array();
    $sCampo          = getCamposTemplate($tipo, $titulo)->diasCertidao;
    $variaveisSessao = getVariaveisSessao();

    // Seta os campos a serem exibidos no topo do PDF
    $campos = array(
        "cgm.z01_nome as proprietario_nome",
        "cgm.z01_nomecomple as proprietario_nome_completo",
        "cgm.z01_numcgm as proprietario_cgm",
        "cgm.z01_cgccpf as proprietario_cpfcnpj",
        "cgm.z01_ident as proprietario_identidade",
        "cgm.z01_nasc as proprietario_nascimento",
        "cgm.z01_ender as proprietario_rua",
        "cgm.z01_numero as proprietario_numero",
        "cgm.z01_compl as proprietario_complemento",
        "cgm.z01_bairro as proprietario_bairro",
        "cgm.z01_munic as proprietario_municipio",
        "cgm.z01_uf as proprietario_uf",
        "cgm.z01_cep as proprietario_cep",
        "cgm.z01_telef as proprietario_telefone",
        "(select {$sCampo} from numpref where k03_instit = {$variaveisSessao->instit} and k03_anousu = {$variaveisSessao->anousu}) as dias_validade",
        "'{$variaveisSessao->base}' as base_dados",
        "'{$variaveisSessao->login}' as usuario_geracao",
        "'".date('d/m/Y')."' as data_sessao",
        "fc_dataextenso('".date('d/m/Y')."') as data_sessao_extenso",
        "'".date('H:i:s')."' as hora_sessao",
        "'{$textarea}' as observacao",
        "'{$codproc}'  as processo "
    );

    /**
     * Busca a informação de acordo com o parâmetro passado (CGM, Inscrição ou Matrícula)
     */
    if (trim($titulo) == "CGM") {
	    $sFrom   = "cgm";
        $sWhere  = "cgm.z01_numcgm = {$origem}";
    } else {
        if (trim($titulo) == "MATRICULA") {
            $campos[] = 'iptubase.j01_matric as matricula_imovel'; 
            $campos[] = 'proprietario.j34_setor as matricula_setor';
            $campos[] = 'proprietario.proprietario as nome_proprietario_view';
            $campos[] = 'proprietario.j34_quadra as matricula_quadra';
            $campos[] = 'proprietario.j34_lote as matricula_lote';
            $campos[] = 'proprietario.j13_descr as matricula_bairro';
            $campos[] = 'proprietario.j34_zona as matricula_zona';
            $campos[] = 'proprietario.j06_setorloc as matricula_setor_localizaca';
            $campos[] = 'proprietario.j06_quadraloc as matricula_quadra_localizacao';
            $campos[] = 'proprietario.pql_localizacao as matricula_lote_localizacao';
            $campos[] = "proprietario.tipopri || '. ' || proprietario.nomepri as matricula_rua";
            $campos[] = 'proprietario.j39_numero as matricula_numero';
	        $campos[] = 'proprietario.j39_compl as matricula_complemento';
            $campos[] = 'proprietario.j40_refant as inscricao_imobiliaria';
            $sFrom = "cgm inner join iptubase on iptubase.j01_numcgm = cgm.z01_numcgm 
                      inner join proprietario on proprietario.j01_matric = iptubase.j01_matric";

            if ($tipocertidao == 1) {
                $sFrom = "iptubase inner join proprietario on proprietario.j01_matric = iptubase.j01_matric inner join cgm on cgm.z01_numcgm = proprietario.z01_cgmpri";
            }

            $sWhere = "iptubase.j01_matric = {$origem}";
        } else {
            if (trim($titulo) == "INSCRICAO") {
                $campos[] = 'empresa.q02_inscr as inscricao';
                $campos[] = 'empresa.q03_descr as inscricao_atividade';
                $campos[] = 'empresa.z01_ender as inscricao_endereco';
                $campos[] = 'empresa.z01_numero as inscricao_numero';
                $campos[] = 'empresa.z01_compl as inscricao_complemento';
                $campos[] = 'empresa.z01_cep as inscricao_cep';
                $campos[] = 'empresa.z01_bairro as inscricao_bairro';
                $campos[] = 'empresa.z01_munic as inscricao_municipio';
                $campos[] = 'empresa.razao as inscricao_razao_social';
		        $campos[] = "to_char(issbase.q02_dtbaix, 'DD/MM/YYYY') as inscricao_baixa_alvara";
                $campos[] = "empresa.q02_inscmu as inscricao_anterior";
                $campos[] = "(  select
                                    string_agg(q03_descr, ', ')
                                from
                                    tabativ
                                    join ativid ON ativid.q03_ativ = tabativ.q07_ativ
                                where
                                    q07_inscr = empresa.q02_inscr
                                    and q07_datafi is null
                                    and q07_databx is null
                            ) as inscricao_atividades";
                
                $sFrom  = "empresa inner join cgm on cgm.z01_numcgm = empresa.q02_numcgm 
                           left join issbase on issbase.q02_inscr = empresa.q02_inscr";
                $sWhere = "empresa.q02_inscr = {$origem} and empresa.q88_tipo = 'P'";
            }
        }
    }
    
    $aQuery["Select"] = implode(',', $campos);
    $aQuery["From"]   = $sFrom;
    $aQuery["Where"]  = $sWhere;
    
    return (object) $aQuery;
}

function buscaDadosCorpoPdf($tipo, $titulo) 
{
    $aQuery = array();

    $sCampo          = getCamposTemplate($tipo, $titulo)->diasCertidao;
    $variaveisSessao = getVariaveisSessao();
    
    // Busca a informação de acordo com o parâmetro da sessão (Instituição e Ano Corrente)
    $sWhere  = "k03_instit = {$variaveisSessao->instit} and k03_anousu = {$variaveisSessao->anousu}";

    $aQuery["Select"] = "numpref.{$sCampo} as dias_validade";
    $aQuery["From"]   = "numpref";
    $aQuery["Where"]  = $sWhere;

    return (object) $aQuery;
}

function getByPdf($tipoDoc, $oDadosArquivo, $oQueryHeader, $oQueryBody)
{
    $clagata = new cl_dbagata($oDadosArquivo->sCaminhoAgt);

    $api = $clagata->api;

    $api->setOutputPath($oDadosArquivo->sCaminhoSalvoSxw);
    foreach ($oDadosArquivo->oParametros as $key => $value) {
        $api->setParameter($key, trim($value));
    }

    $xml = $api->getReport();
    
    foreach ($oQueryHeader as $key => $value){

        $xml["Report"]["DataSet"]["Query"][$key] = $value;
        $xml["Report"]["OpenOffice"]["Details"]["detail1"]["DataSet"]["Query"][$key] = $value;
        $xml["Report"]["OpenOffice"]["Details"]["detail3"]["DataSet"]["Query"][$key] = $value;
    }
    
    foreach ($oQueryBody as $key => $value){

        $xml["Report"]["OpenOffice"]["Details"]["detail2"]["DataSet"]["Query"][$key] = $value;
    }
    
    $api->setReport($xml);
    try {
        $oDocumentoTemplate = new documentoTemplate($tipoDoc, $oDadosArquivo->sTemplate);
    } catch (Exception $eException) {
        $sErroMsg = $eException->getMessage();
        db_redireciona("db_erros.php?fechar=true&db_erro={$sErroMsg}");
    }

    /**
     * Abrimos a conexão com o e-cidade novamente
     */
    include(modification('libs/db_conecta.php'));

    // Parse do documento .xml para .swx
    $lProcessado = $api->parseOpenOffice($oDocumentoTemplate->getArquivoTemplate());

    if ($lProcessado) {
        // Parse do documento .sxw para .pdf
        db_stdClass::ex_oo2pdf($oDadosArquivo->sCaminhoSalvoSxw, $oDadosArquivo->sNomeRelatorio);
    } else {
        db_redireciona("db_erros.php?fechar=true&db_erro=Falha ao gerar relatório!!!");
    }
}

function salvaCertidao($oParamsArq)
{
    global $conn;

    /**
     * Geramos um Blob vazio e gravamos o arquivo no banco
     */
    db_query($conn, "begin;");
    
    $iOid = DBLargeObject::criaOID( true );
    DBLargeObject::escrita( $oParamsArq->sNomeRelatorio, $iOid );

    if (empty($iOid)) {
        $sErroMensagem = "Erro ao criar oid do arquivo da Certidão.";
        throw new DBException($sErroMensagem);
    }

    if (isset($oParamsArq->titulo) && $oParamsArq->titulo == 'CGM') {
        $iNumcgm = $oParamsArq->origem;
    } else {
        if (isset ($oParamsArq->titulo) && $oParamsArq->titulo == 'MATRICULA') {
            $iMatricula = $oParamsArq->origem;
        } else {
            if (isset($oParamsArq->titulo) && $oParamsArq->titulo == 'INSCRICAO') {
                $iInscricao = $oParamsArq->origem;
            }
        }
    }

    //INSTITUIÇÃO E ANO DA SESSÃO
    $variaveisSessao = getVariaveisSessao();

    //  TIPO
    if ($oParamsArq->tipo == 1) {
        $p50_tipo = "p";
    } else {
        if ($oParamsArq->tipo == 0) {
            $p50_tipo = "r";
        } else {
            $p50_tipo = "n";
        }
    }

    $coluna = getCamposTemplate($oParamsArq->tipo, $oParamsArq->titulo)->diasCertidao;

    //  PROCESSO
    if ($oParamsArq->codproc != "") {
        if (strpos($oParamsArq->codproc, "/") > 0) {
            $codproc =explode("/", $oParamsArq->codproc);
            $exercicio = $codproc[1];
            $codproc = $codproc[0];
        } else {
            $codproc = $oParamsArq->codproc;
            $exercicio = $variaveisSessao->anousu;
        }
    }
    else {
        $codproc = "";
        $exercicio = 0;
    }

    //  HISTÓRICO
    if (isset($oParamsArq->textarea) && $oParamsArq->textarea != "") {
        $historico = $oParamsArq->textarea;
    }

    if (isset($historico) && $historico != "") {
        $p50_hist = $historico. ($codproc != '' ? ", processo N".chr(176).": ".$codproc : '');
    } else {
        $p50_hist = " ". ($codproc != '' ? "Processo N".chr(176).": ".$codproc : '');
    }

    try {
        $certidaoRepository = CertidaoTemplateRepository::getInstance();
        $certidaoModel      = new CertidaoTemplateModel();

        $k03_tipocodcert = $certidaoRepository->getByDadosCertidao("k03_tipocodcert");
        if ($k03_tipocodcert != 0) {
            if ($k03_tipocodcert == 5) {
                $codimpresso = $codproc . "/" . $exercicio;
            } else {
                $codimpresso = pg_result(db_query("select fc_numerocertidao({$variaveisSessao->instit}, {$k03_tipocodcert}, '{$p50_tipo}', false)"), 0);
            }
        }

        $dias_validade = $certidaoRepository->getByDadosCertidao($coluna);
        $certidaoModel->setIdUsuario($variaveisSessao->id_usuario);
        $certidaoModel->setTipo($p50_tipo);
        $certidaoModel->setData(date("Y-m-d", $variaveisSessao->datausu));
        $certidaoModel->sethora(db_hora());
        $certidaoModel->setIp($variaveisSessao->ip);
        $certidaoModel->setHistorico($p50_hist);
        $certidaoModel->setWeb("false");
        $certidaoModel->setCodigoProcesso($codproc);
        $certidaoModel->setExercicio($exercicio);
        $certidaoModel->setCodigoImpresso($codimpresso);
        $certidaoModel->setInstituicao($variaveisSessao->instit);
        $certidaoModel->setArquivo($iOid);
        $certidaoModel->setDiasValidade($dias_validade);
        $certidaoModel->setDiasValidade($dias_validade);
        
        $nomeServico         = isset($_GET['nomeServico'])?$_GET['nomeServico']:null;
        $resultadoWebservice = isset($_GET['resultadoWebservice'])?$_GET['resultadoWebservice']:null;
        $dataHoraConsulta    = isset($_GET['dataHoraConsulta'])?$_GET['dataHoraConsulta']:null;
        $certidaoModel->setNomeServico($nomeServico);
        $certidaoModel->setResultadoWebservice($resultadoWebservice);
        $certidaoModel->setDataHoraConsulta($dataHoraConsulta);

        $certidaoRepository->persist($certidaoModel);
        
        $certidao = $certidaoModel->getSequencial();
        
        //$datavenc   = null;
        $sqlvenc    = "select ('{$certidaoModel->getData()}'::date + '{$certidaoModel->getDiasValidade()} days'::interval)::date as datavenc";
        
        $resultvenc = db_query($sqlvenc);
          
        if (!$resultvenc) {
          throw new Exception("Erro ao cadastrar certidão para validação online: " . $sqlvenc);
        }
          
        $datavenc = db_utils::fieldsmemory($resultvenc, 0)->datavenc;
        
        
        if (isset($iInscricao)) {

            $sSqlContribuinte  = "select trim(cgm.z01_nome) as nomecontribuinte
                             from empresa
                            inner join cgm on cgm.z01_numcgm = empresa.q02_numcgm
                            where q02_inscr = $iInscricao";
        }
        else if (isset ($iMatricula)) {
          
            $sSqlContribuinte = "select trim(z01_nome) as nomecontribuinte
                             from proprietario
                            where j01_matric = $iMatricula";
          
        } else {
          
            $sSqlContribuinte    = "select trim(z01_nome) as nomecontribuinte
                                      from cgm
                                     where z01_numcgm = $iNumcgm";
        }
        
        $rsContribuinte = db_query($sSqlContribuinte);
        if(!$rsContribuinte || pg_numrows($rsContribuinte) == 0) {

          throw new Exception("Não foi possível buscar contribuinte");
        }
        
        $nomeContibuinte = db_utils::fieldsMemory($rsContribuinte, 0)->nomecontribuinte;
        
        $clcertidaoweb               = new cl_db_certidaoweb;
        $clcertidaoweb->codcert      = $certidao;
        $clcertidaoweb->tipocer      = $oParamsArq->tipo;
        $clcertidaoweb->cerdtemite   = $certidaoModel->getData();
        $clcertidaoweb->cerhora      = $certidaoModel->gethora() . ":" . date('s');
        $clcertidaoweb->cerdtvenc    = $datavenc;
        $clcertidaoweb->cerip        = db_getsession('DB_ip');
        $clcertidaoweb->ceracesso    = $oParamsArq->oParametros->codigo_verificacao;
        $clcertidaoweb->cercertidao  = $iOid;
        $clcertidaoweb->cernomecontr = addslashes($nomeContibuinte);
        $clcertidaoweb->cerhtml      = "x";
        $clcertidaoweb->cerweb       = 'true';
          
        if (!$clcertidaoweb->incluir() && $clcertidaoweb->erro_status == 0) {
          throw new DBException("Erro ao cadastrar certidão para validação online: " . $clcertidaoweb->erro_msg);
        }
        
        $certidao = $certidaoModel->getSequencial();

    // CertidaoCgm
    if (isset ($iNumcgm)) {
        $certidaoCgmRepository = CertidaoTemplateCgmRepository::getInstance();
        $certidaoCgmModel      = new CertidaoTemplateCgmModel();
        $certidaoCgmModel->setSequencial($certidao);
        $certidaoCgmModel->setNumcgm($iNumcgm);
        $certidaoCgmRepository->persist($certidaoCgmModel);
    }
    else {
        // CertidaoMatricula
        if (isset ($iMatricula)) {
            $certidaoMatricRepository = CertidaoTemplateMatriculaRepository::getInstance();
            $certidaoMatricModel      = new CertidaoTemplateMatriculaModel();
            $certidaoMatricModel->setSequencial($certidao);
            $certidaoMatricModel->setMatric($iMatricula);
            $certidaoMatricRepository->persist($certidaoMatricModel);
        } else {
            // CertidaoInscricao
            if (isset($iInscricao)) {
                $certidaoInscrRepository = CertidaoTemplateInscricaoRepository::getInstance();
                $certidaoInscrModel      = new CertidaoTemplateInscricaoModel();
                $certidaoInscrModel->setSequencial($certidao);
                $certidaoInscrModel->setInscr($iInscricao);
                $certidaoInscrRepository->persist($certidaoInscrModel);
            } else {
                throw new \DBException("Erro ao inserir certidão");
            }
        }
    }

    if ($k03_tipocodcert != 0) {

        $certidaoModel->setSequencial($certidao);
        $certidaoModel->setCodigoProcesso($codproc);
        $certidaoRepository->persist($certidaoModel);
    }

    db_query($conn, "commit;");

    db_redireciona($oParamsArq->sNomeRelatorio);

    } catch (Exception $e) {
        
        db_query($conn, "rollback;");
        $sErroMsg = $e->getMessage();
        db_redireciona("db_erros.php?fechar=true&db_erro={$sErroMsg}");
    }
}

function getCamposTemplate($tipo, $titulo)
{
    /**
     * Seta a coluna de acordo com o tipo de certidão
     * 0 - Regular
     * 1 - Positiva
     * Qualquer diferente - Negativa
    */
    if ($tipo == 1) {
        if ($titulo == "CGM") {
            $sCampoTemplateCertidao = "k03_templatecertidaopositiva_cgm";
            $sCampoDiasCertidao     = "k03_diascertidpositiva_cgm";
        } else {
            if ($titulo == "MATRICULA") {
                $sCampoTemplateCertidao = "k03_templatecertidaopositiva_matric";
                $sCampoDiasCertidao     = "k03_diascertidpositiva_matric";
            } else {
                $sCampoTemplateCertidao = "k03_templatecertidaopositiva_inscr";
                $sCampoDiasCertidao     = "k03_diascertidpositiva_inscr";
            }
        }
    } else {
        if ($tipo == 0) {
            if ($titulo == "CGM") {
                $sCampoTemplateCertidao = "k03_templatecertidao_cgm";
                $sCampoDiasCertidao     = "k03_diascertidregular_cgm";
            } else {
                if ($titulo == "MATRICULA") {
                    $sCampoTemplateCertidao = "k03_templatecertidao_matric";
                    $sCampoDiasCertidao     = "k03_diascertidregular_matric";
                } else {
                    $sCampoTemplateCertidao = "k03_templatecertidao_inscr";
                    $sCampoDiasCertidao     = "k03_diascertidregular_inscr";
                }
            }
        } else {
            if ($titulo == "CGM") {
                $sCampoTemplateCertidao = "k03_templatecertidaonegativa_cgm";
                $sCampoDiasCertidao     = "k03_diascertidnegativa_cgm";
            } else {
                if ($titulo == "MATRICULA") {
                    $sCampoTemplateCertidao = "k03_templatecertidaonegativa_matric";
                    $sCampoDiasCertidao     = "k03_diascertidnegativa_matric";
                } else {
                    $sCampoTemplateCertidao = "k03_templatecertidaonegativa_inscr";
                    $sCampoDiasCertidao     = "k03_diascertidnegativa_inscr";
                }
            }
        }
    }

    return (object) array(
        "templateCertidao" => $sCampoTemplateCertidao,
        "diasCertidao"     => $sCampoDiasCertidao
    );
}

function getVariaveisSessao()
{
    return (object) array(
        "instit"     => db_getsession('DB_instit'),
        "anousu"     => db_getsession('DB_anousu'),
        "id_usuario" => db_getsession('DB_id_usuario'),
        "datausu"    => db_getsession('DB_datausu'),
        "ip"         => db_getsession('DB_ip'),
        "base"       => db_getsession('DB_base'),
        "login"      => db_getsession('DB_login')
    );
}
