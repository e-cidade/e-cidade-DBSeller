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
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

use ECidade\Saude\Laboratorio\Exame\Model\ConferenciaResultado as ConferenciaResultadoModel;
use ECidade\Saude\Laboratorio\Exame\Repository\ConferenciaResultado as ConferenciaResultadoRepository;
use ECidade\Saude\Laboratorio\Exame\Validator\ConferenciaResultado as ConferenciaResultadoValidator;
use ECidade\Saude\Laboratorio\Exame\Repository\RequisicaoExame as RequisicaoExameRepository;
use ECidade\Saude\Laboratorio\Repository\RequisicaoLaboratorialRepository;
use ECidade\Saude\Laboratorio\Service\RequisicaoLaboratorialService;

define("MSG_LAB_CONFERENCIARPC", "saude.laboratorio.lab_conferenciarpc.");

$oJson = new services_json();
$oParam = $oJson->decode(str_replace("\\", "", $_POST["json"]));
$oRetorno = new stdClass();
$oRetorno->iStatus = 1;
$oRetorno->sMensagem = '';

try {
    db_inicio_transacao();

    switch ($oParam->exec) {
        /**
         * ************************************************************
         * Busca os dados dos exames vinculados a Requisição Laboratorial
         * Retorna a seguinte estrutura:
         * stdClass Object
         * (
         *     [iExame] =>
         *     [sExame] =>
         *     [iCidConferido] =>
         *     [sNomeCidConferido] =>
         *     [sEstruturalCidConferido] =>
         *     [sProcedimentoEstrutural] =>
         *     [sProcedimento] =>
         *     [aCID] => Array
         *         (
         *             [0] => stdClass Object
         *                 (
         *                     [iCodigo] =>
         *                     [sNome] =>
         *                     [lPrincipal] =>
         *                 )
         *         )
         *
         * )
         * @param  integer  $oParam  ->iCodigo
         * ************************************************************
         */
        case 'getExamesRequisicao':

            $oRetorno->aExames = array();
            $oRequisicaoLaboratorial = new RequisicaoLaboratorial($oParam->iCodigo);
            $aRequisicoesExames = $oRequisicaoLaboratorial->getRequisicoesDeExames();
            $usuarioSessao = db_getsession('DB_id_usuario');
            $oDaoLabResp = new cl_lab_labresp();

            if (is_array($aRequisicoesExames)) {
                $oRetorno->laboratorios = "";
                foreach ($aRequisicoesExames as $oRequisicaoExame) {
                    if ($oRequisicaoExame->getSituacao() != RequisicaoExame::LANCADO
                        && $oRequisicaoExame->getSituacao() != RequisicaoExame::CONFERIDO
                    ) {
                        continue;
                    }
                    $whereData = " (la06_d_fim >= '".date('Y-m-d')."' or (la06_d_fim is null))";
                    $whereUsuarioSessao = " and db_usuacgm.id_usuario = ".$usuarioSessao;
                    $whereLaboratorio = " and la06_i_laboratorio= ".$oRequisicaoExame->getLaboratorio();
                    $where = $whereData.$whereUsuarioSessao.$whereLaboratorio;
                    $sql = $oDaoLabResp->sql_query_setor(null, "la06_i_laboratorio", null, $where);

                    $rsPermissaoLaboratorio = db_query($sql);

                    $iLinhasPermissaoLaboratorio = pg_num_rows($rsPermissaoLaboratorio);

                    if ($iLinhasPermissaoLaboratorio == 0) {
                        $oDaoLaboratorio = new cl_lab_laboratorio();
                        $oDaoLaboratorio->la08_i_codigo = $oRequisicaoExame->getLaboratorio();
                        $sql = $oDaoLaboratorio->sql_query_file($oRequisicaoExame->getLaboratorio());
                        $rsLaboratorio = db_query($sql);
                        $oDadosLaboratorio = db_utils::fieldsMemory($rsLaboratorio, 0);

                        if ($oRetorno->laboratorios == "") {
                            $oRetorno->laboratorios = urlencode(
                                "* ".$oRequisicaoExame->getExame()->getCodigo()." - "
                                .$oRequisicaoExame->getExame()->getNome(
                                )." - ".$oDadosLaboratorio->la02_i_codigo." - ".$oDadosLaboratorio->la02_c_descr
                            );
                        } else {
                            $oRetorno->laboratorios .= urlencode(
                                "\n* ".$oRequisicaoExame->getExame()->getCodigo()." - "
                                .$oRequisicaoExame->getExame()->getNome(
                                )." - ".$oDadosLaboratorio->la02_i_codigo." - ".$oDadosLaboratorio->la02_c_descr
                            );
                        }
                        continue;
                    }


                    $oDadosExame = new stdClass();
                    $oExame = $oRequisicaoExame->getExame();
                    $oDadosExame->iExame = $oRequisicaoExame->getCodigo();
                    $oDadosExame->sExame = urlencode($oExame->getNome());
                    $oDadosExame->aCID = array();

                    $oDadosExame->iProcedimento = '';
                    $oDadosExame->sProcedimentoEstrutural = '';
                    $oDadosExame->sProcedimento = '';
                    $oDadosExame->sSituacao = $oRequisicaoExame->getSituacao();
                    $oDadosExame->lConferido = $oRequisicaoExame->getSituacao() == RequisicaoExame::CONFERIDO;
                    $oDadosExame->liberadoPor = urlencode("");

                    if ($oRequisicaoExame->getConferenciaResultado() !== null) {
                        $oDadosExame->liberadoPor = urlencode(
                            $oRequisicaoExame->getConferenciaResultado()->getUsuarioSistema()->getNome()
                        );
                    }

                    $oDadosExame->iCidConferido = null;
                    $oDadosExame->sNomeCidConferido = '';
                    $oDadosExame->sEstruturalCidConferido = '';

                    $oCID = $oRequisicaoExame->getCID();

                    if (!empty($oCID)) {
                        $oDadosExame->iCidConferido = $oCID->getCodigo();
                        $oDadosExame->sNomeCidConferido = urlencode($oCID->getNome());
                        $oDadosExame->sEstruturalCidConferido = urlencode($oCID->getCID());
                    }

                    $oProcedimento = $oExame->getProcedimento();

                    if (!empty($oProcedimento)) {
                        $oDadosExame->iProcedimento = $oProcedimento->getCodigo();
                        $oDadosExame->sProcedimentoEstrutural = $oProcedimento->getEstrutural();
                        $oDadosExame->sProcedimento = urlencode($oProcedimento->getDescricao());

                        $aCIDProcedimento = $oProcedimento->getCID();

                        foreach ($aCIDProcedimento as $oCIDProcedimento) {
                            $oCID = new stdClass();
                            $oCID->iCodigo = $oCIDProcedimento->getCID()->getCodigo();
                            $oCID->sCID = $oCIDProcedimento->getCID()->getCID();
                            $oCID->sNome = urlencode($oCIDProcedimento->getCID()->getNome());
                            $oCID->lPrincipal = $oCIDProcedimento->cidPrincipal();
                            $oDadosExame->aCID[] = $oCID;
                        }
                    }
                    $oRetorno->aExames[] = $oDadosExame;
                }
            }

            break;

        case 'salvarConferencia':

            /**
             * OBSERVAÇÂO
             * A variável consideração deve pode ser salva em : lab_conferencia ou lab_resultado
             * O local é definido pelo parâmetro : $oParam->lConferido
             * ... false: devemos salvar em lab_conferencia
             * ... true: devemos salvar em lab_resultado
             */

            $oDaoConferencia = new cl_lab_conferencia();

            db_inicio_transacao();

            $oDaoConferencia->la47_d_data = date('Y-m-d', db_getsession("DB_datausu"));
            $oDaoConferencia->la47_c_hora = db_hora();
            $oDaoConferencia->la47_i_login = db_getsession("DB_id_usuario");

            foreach ($oParam->aExames as $oExame) {
                $sSituacao = RequisicaoExame::CONFERIDO;
                $oDaoConferencia->la47_i_requiitem = $oExame->iCodigoRequisicaoExame;
                $oDaoConferencia->la47_i_cid = $oExame->iCodigoCID;
                $oDaoConferencia->la47_i_resultado = 1;
                $oDaoConferencia->la47_i_procedimento = $oExame->iProcedimento;

                if (!$oParam->lConferido) {
                    $sSituacao = RequisicaoExame::COLETADO;
                    $oDaoConferencia->la47_i_resultado = 2;
                }

                $oDaoConferencia->incluir(null);

                if ($oDaoConferencia->erro_status == "0") {
                    throw new DBException(
                        _M(MSG_LAB_CONFERENCIARPC."erro_salvar_conferencia")."\n {$oDaoConferencia->erro_msg}"
                    );
                }

                $oItemRequisicao = new RequisicaoExame($oExame->iCodigoRequisicaoExame);
                $oItemRequisicao->setSituacao($sSituacao);
                $oItemRequisicao->salvar();
                $oRetorno->sMensagem = urlencode(_M(MSG_LAB_CONFERENCIARPC."sucesso_conferencia"));
            }

            db_fim_transacao();

            break;

        case 'salvarConferenciaLote':
            db_inicio_transacao();

            $requisicoesExame = getItensRequisicaosFiltrados($oParam);

            $conferenciaModel = new ConferenciaResultadoModel();
            $conferenciaModel->setData(DBDate::create(date('Y-m-d')));
            $conferenciaModel->setHora(date('H:i'));
            $conferenciaModel->setResultado(1);
            $conferenciaModel->setUsuarioSistema(UsuarioSistemaRepository::getUsuarioSessao());

            $conferenciaRepository = ConferenciaResultadoRepository::getInstance();
            $conferenciaValidator = ConferenciaResultadoValidator::getInstance();

            foreach ($requisicoesExame as $requisicaoExame) {
                $conferenciaModel->setRequisicaoExame($requisicaoExame);

                if ($requisicaoExame->getExame()->getProcedimento() !== null) {
                    $conferenciaModel->setProcedimento($requisicaoExame->getExame()->getProcedimento());
                }

                $validou = $conferenciaValidator->validar($conferenciaModel);

                if ($validou === false) {
                    continue;
                };

                $conferenciaRepository->salvar($conferenciaModel);

                $requisicaoExame->setSituacao(RequisicaoExame::CONFERIDO);
                $requisicaoExame->salvar();
            }

            db_fim_transacao();

            $mensagem = 'Conferência(s) concluída(s) com sucesso.';

            if ($conferenciaValidator->temInconsistencias()) {
                $mensagem .= ' Porém, os seguintes exames não tiveram a conferência salva, em suas respectivas requisições';
                $mensagem .= ', por não possuírem procedimento configurado:';

                foreach ($conferenciaValidator->getInconstencias() as $tipoInconsistencia) {
                    foreach ($tipoInconsistencia as $exame) {
                        $mensagem .= "\n - [{$exame->getSigla()}] {$exame->getNome()}";
                    }
                }

                $mensagem .= "\n\nPara configuração do procedimento, acesse:";
                $mensagem .= "\n - DB:SAÚDE > Laboratório > Cadastros > Exame > Alteração > aba Procedimentos";
            }

            $oRetorno->sMensagem = urlencode($mensagem);

            break;

        case 'getRequisicoesExames':
            $itensRequisicoes = getItensRequisicaosFiltrados($oParam, true);
            $idsItensRequisicao = [];

            foreach ($itensRequisicoes as $itemRequisicao) {
                if (!in_array($itemRequisicao->la21_i_codigo, $idsItensRequisicao)) {
                    $idsItensRequisicao[] = $itemRequisicao->la21_i_codigo;
                }
            }
            $itensRequisicoes = null;

            $resultados = (new RequisicaoLaboratorialService(
                new RequisicaoLaboratorialRepository(new cl_lab_requisicao())
            ))
                ->getRelacaoRequisicaoExames($idsItensRequisicao, $oParam->limit, $oParam->offset);

            $registros = [];
            $requiAtual = null;
            $examesPorRequisicao = examesDaRequisicao($resultados);

            foreach ($resultados as $resultado) {
                if ($requiAtual !== $resultado->requisicao) {
                    $registros[] = (object)[
                        'requisicao' => $resultado->requisicao,
                        'nomePaciente' => urlencode($resultado->nome_paciente),
                        'nomeLaboratorio' => urlencode($resultado->lab_descr),
                        'dataRequisicao' => DateTime::createFromFormat('Y-m-d', $resultado->data_requisicao)
                            ->format('d/m/Y'),
                        'listaExames' => urlencode($examesPorRequisicao[$resultado->requisicao])
                    ];
                }
                $requiAtual = $resultado->requisicao;
            }

            $oRetorno->total = count($idsItensRequisicao);
            $oRetorno->rows = $registros;

            break;
    }
} catch (Exception $oErro) {
    db_fim_transacao(true);

    $oRetorno->iStatus = 2;
    $oRetorno->sMensagem = urlencode(str_replace("\\n", "\n", $oErro->getMessage()));
}

echo $oJson->encode($oRetorno);

/**
 * @param $oParam
 * @return RequisicaoExame[]|\stdClass[]
 * @throws Exception
 */
function getItensRequisicaosFiltrados($oParam, $apenasIds = false)
{
    if (empty($oParam->dataInicio) || empty($oParam->dataFim)) {
        throw new Exception('Período não informado.');
    }

    $requisicaoRepository = RequisicaoExameRepository::getInstance();
    $requisicaoRepository->setSituacao(RequisicaoExame::LANCADO);
    $requisicaoRepository->setPeriodo(DBDate::create($oParam->dataInicio), DBDate::create($oParam->dataFim));

    if (!empty($oParam->codigoLaboratorio)) {
        $requisicaoRepository->setLaboratorio($oParam->codigoLaboratorio);
    }

    if (!empty($oParam->codigoSetor)) {
        $requisicaoRepository->setSetor($oParam->codigoSetor);
    }

    if (!empty($oParam->codigoExame)) {
        $requisicaoRepository->setExame($oParam->codigoExame);
    }

    if (!empty($oParam->grupoLiberacao)) {
        $requisicaoRepository->setExamesByGrupoLiberacao($oParam->grupoLiberacao);
    }

    return $requisicaoRepository->getRequisicoesExameComResultado($apenasIds);
}

/**
 * Organiza os exames vinculados a respectiva requisição.
 * @param  array  $dadosRequisicao
 * @return array
 */
function examesDaRequisicao($dadosRequisicao)
{
    $requisicoesPresentes = [];
    $listaExames = [];

    foreach ($dadosRequisicao as $resultado) {
        if (!in_array($resultado->requisicao, $requisicoesPresentes)) {
            $requisicoesPresentes[] = $resultado->requisicao;
        }
    }

    foreach ($requisicoesPresentes as $idRequisicao) {
        $primeiroExameJafoi = false;
        $exames = '';

        foreach ($dadosRequisicao as $resultado) {
            if ($resultado->requisicao == $idRequisicao) {
                if ($primeiroExameJafoi) {
                    $exames .= " - ";
                }

                $exames .= "{$resultado->exame}";
                $primeiroExameJafoi = true;
            }
        }

        $listaExames[$idRequisicao] = $exames;
    }
    return $listaExames;
}
