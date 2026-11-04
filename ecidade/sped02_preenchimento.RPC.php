<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                    www.dbseller.com.br
 *                 e-cidade@dbseller.com.br
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
require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_usuariosonline.php');
require_once modification('libs/db_app.utils.php');
require_once modification('libs/db_utils.php');
require_once modification('libs/JSON.php');
require_once modification('dbforms/db_funcoes.php');

use ECidade\Integracao\Sped\Common\Avaliacao\AvaliacaoSped;
use ECidade\Integracao\Sped\Common\Avaliacao\AvaliacaoSpedAdapter;
use ECidade\Integracao\Sped\Common\Configuracao\ConfiguracaoFactory;
use ECidade\Integracao\Sped\Common\Evento\EventoFactory;
use ECidade\RecursosHumanos\ESocial\Factory\SugestaoPreenchimento;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ECidade\RecursosHumanos\ESocial\Transformer\Sugestao;
use App\Domain\Integracoes\EFDReinf\Repository\UnidadeResponsavelRepository;
use App\Domain\Integracoes\EFDReinf\Services\ConfiguracaoService;

$retorno = new stdClass();
$retorno->erro = false;
$retorno->mensagem = '';

db_inicio_transacao();

try {
    $parametros = JSON::requestParameters();
    $instituicaoSessao = InstituicaoRepository::getInstituicaoSessao();

    switch ($parametros->acao) {
        case 'inicializar':
            if ($parametros->integracao == Tipo::EFD_REINF) {
                $efdConfig = ConfiguracaoService::getInstance($instituicaoSessao->getCodigo());

                if ($efdConfig->filtraOrgaoUnidade()) {
                    $unidaderesp = new UnidadeResponsavelRepository;
                    $retorno->contribuinte = $unidaderesp->getAll($instituicaoSessao->getCodigo());
                } else {
                    $contribuinte = [$instituicaoSessao->toArray()];
                    $contribuinte[0]['cgm'] = $instituicaoSessao->getCgm()->getCodigo();
                    $retorno->contribuinte = $contribuinte;
                }
            }

            if ($parametros->integracao == Tipo::ESOCIAL) {
                $where = array(
                  "r70_ativo IS TRUE",
                  "r70_instit = {$instituicaoSessao->getCodigo()}"
                );

                $campos = array(
                  "z01_numcgm AS cgm",
                  "z01_cgccpf AS cnpj",
                  "z01_nome AS nome",
                );

                $dao = new cl_rhlota();
                $sql = $dao->sql_query_lota_cgm(
                  null,
                  'DISTINCT ' . implode(', ', $campos),
                  'z01_numcgm',
                  implode(' AND ', $where)
                );
                $rs = db_query($sql);

                if (!$rs) {
                    throw new DBException("Não foi possível buscar os empregadores da instituição.\nContate o suporte.");
                }

                if (pg_num_rows($rs) === 0) {
                    throw new Exception("Não há empregadores registrados.");
                }

                $retorno->empregadores = pg_fetch_all($rs);
            }
            $reenvio = false;
            if (isset($parametros->reenvio) && $parametros->reenvio == 'true') {
                $reenvio = true;
            }
            $exclusao = false;
            if (!empty($parametros->exclusaoLote)) {
                $exclusao = true;
            }
            $retorno->exibeCompetencia = Tipo::getExibeCompetencias($reenvio, $exclusao);
            $retorno->exibeMatricula = Tipo::getExibeMatricula($reenvio, $exclusao);
            $retorno->exibeDataPreenchimento = Tipo::getExibeDataDePreenchimento($reenvio);
            $retorno->exibeIndicativoPeriodoApuracao = Tipo::getExibeIndicativoPeriodoApuracao($reenvio);
            $retorno->exibeForcarMatricula = Tipo::getExibeForcarMatricula($reenvio);
            $retorno->exibePeriodoData = Tipo::getExibePeriodoData($reenvio, $exclusao);
            $retorno->exibeSelecao = Tipo::getExibeSelecao($reenvio, $exclusao);
            $retorno->exibeFiltro = Tipo::getExibeFiltro($reenvio, $exclusao);
            $retorno->exibeTipoDataPagamento = Tipo::getExibeTipoDataPagamento();
            $retorno->exibeRubrica = Tipo::getExibeRubrica($reenvio);
            $retorno->exibeCaixa = Tipo::getExibeCaixa($reenvio, $exclusao);

            break;
        case 'buscar':
            if (empty($parametros->formularioTipo) || !Tipo::existe($parametros->formularioTipo)) {
                throw new BusinessException("É necessário informar um tipo de formulário para preenchimento.\nContate o suporte.");
            }

            if (empty($parametros->cgm)) {
                throw new BusinessException('É necessário informar a instituição.');
            }

            $configuracao = ConfiguracaoFactory::getInstance((int)$parametros->integracao);
            $codigoFormulario = $configuracao->getFormulario($parametros->formularioTipo);

            $trazerSugestoes = false;

            if (!empty($parametros->trazerSugestoes)) {
                $trazerSugestoes = $parametros->trazerSugestoes;
            }

            $cgm = CgmRepository::getByCodigo($parametros->cgm);

            $evento = EventoFactory::getInstance($parametros->formularioTipo);
            $evento->setCgm($cgm);

            $avaliacaoAdapter = new AvaliacaoSpedAdapter(
              AvaliacaoRepository::getAvaliacaoByCodigo($codigoFormulario),
              $evento
            );
            $avaliacaoAdapter->setUsaSugestoes($trazerSugestoes);

            empty($parametros->preenchimento) ?
              $avaliacaoAdapter->definirCodigoGrupoResposta($parametros) :
              $avaliacaoAdapter->setCodigoGrupoResposta($parametros->preenchimento);

            $retorno->preenchimento = $avaliacaoAdapter->getCodigoGrupoResposta();
            $retorno->formulario = $avaliacaoAdapter->getObject();

            if (!empty($parametros->somenteLeitura)) {
                $retorno->somenteLeitura = (array) JSON::create()->parse($parametros->somenteLeitura);
            }

            if (empty($retorno->preenchimento)) {
                $sugestaoPreenchimento = new SugestaoPreenchimento();
                $sugestaoPreenchimento->setCgmResponsavel($cgm);
                $sugestaoPreenchimento->setParametros($parametros);
                $sugestao = $sugestaoPreenchimento->porTipo($parametros->formularioTipo);

                $sugestaoParametros = array();
                if (isset($parametros->sugestao)) {
                    $sugestaoParametros = (array) JSON::create()->parse($parametros->sugestao);
                }

                $sugestaoEstrutura = array();
                if ($sugestao instanceof Sugestao) {
                    $sugestaoEstrutura = $sugestao->parse();
                }

                if (!empty($sugestaoParametros) || !empty($sugestaoEstrutura)) {
                    $retorno->sugestao = array_merge($sugestaoParametros, $sugestaoEstrutura);
                }
            }

            break;
        case 'salvar':
            if (empty($parametros->formularioTipo) || !Tipo::existe($parametros->formularioTipo)) {
                throw new BusinessException("É necessário informar um tipo de formulário para preenchimento.\nContate o suporte.");
            }

            if (empty($parametros->cgm)) {
                throw new BusinessException('É necessário informar a instituição.');
            }

            if (empty($parametros->perguntasRespostas)) {
                throw new ParameterException('Não foram enviadas respostas para salvar.');
            }

            $perguntasRespostas = JSON::create()->parse($parametros->perguntasRespostas);

            $avaliacao = AvaliacaoRepository::getAvaliacaoByCodigo($parametros->codigoAvaliacao);
            $avaliacaoSped = new AvaliacaoSped($avaliacao);
            $avaliacaoSped->setCgm(CgmRepository::getByCodigo($parametros->cgm));

            if (!empty($parametros->preenchimento)) {
                $avaliacao->setAvaliacaoGrupo($parametros->preenchimento);
                $avaliacaoSped->setPreenchimento($parametros->preenchimento);
            } else {
                $avaliacao->setAvaliacaoGrupo();
            }

            if (!empty($parametros->codigoGrupoPerguntas)) {
                $avaliacaoSped->setCodigoGrupoPerguntas($parametros->codigoGrupoPerguntas);
            }

            $retorno->preenchimento = $avaliacaoSped->salvar(
              $perguntasRespostas,
              $parametros->formularioTipo,
              (array)$parametros
            );

            $retorno->mensagem = 'Preenchimento salvo com sucesso.';
            break;
    }
} catch (Exception $exception) {
    $retorno->erro = true;
    $retorno->mensagem = $exception->getMessage();
}

db_fim_transacao($retorno->erro);

echo JSON::create()->stringify($retorno);
