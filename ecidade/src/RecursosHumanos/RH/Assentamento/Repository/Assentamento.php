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

namespace ECidade\RecursosHumanos\RH\Assentamento\Repository;

use cl_afastamentosesocial;
use ECidade\RecursosHumanos\ESocial\Integracao\ESocial;
use ECidade\RecursosHumanos\ESocial\Integracao\Recurso;
use ECidade\RecursosHumanos\RH\Assentamento\AssentamentoHoraExtraManual as AHEM;
use ECidade\V3\Extension\Registry;
use Exception;

/**
 * Class Assentamento
 * @package Ecidade\RecursosHumanos\RH\Assentamento\Repository
 */
class Assentamento extends \BaseClassRepository
{
    /**
     * Sobrescrista da variável oInstante da classe BaseClassRepository
     * @var Assentamento
     */
    protected static $oInstance;

    private $serviceApiEsocial = null;


    /**
     * Retorna o serviço para realizar requisções à API do e-social
     * Ex.: $recurso = '/evento/recibo'
     * @param $recurso
     */
    private function getServiceApiEsocialByRecurso($recurso)
    {
        if (empty($recurso)) {
            throw new \ParameterException("Recurso para requisição da API não informado");
        }

        $this->serviceApiEsocial = new ESocial(Registry::get('app.config'), $recurso);
        return $this->serviceApiEsocial;
    }

    /**
     * Valida a data final do ultimo protocolo de tipo S-2230 de um servidor.
     *
     * @param $matriculaServidor
     * @param $dataAfastamento
     * @return bool
     * @throws \ParameterException
     */
    public function validarUltimoProtocoloAfastamentoByServidor(
        $matriculaServidor,
        $dataAfastamento,
        $tipoAfastamento,
        $codigoAssentamento
    ) {

        $rhPessoal = new \cl_rhpessoal();
        $empregador = new \stdClass();

        if (!$this->possuiConfiguracaoApi()) {
            return true;
        }

        if (!$this->isTipoAfastamentoEsocial($tipoAfastamento)) {
            return true;
        }

        $sqlCnpjEmpregador = $rhPessoal->sql_queryInsntituicoesServidoresVinculo(
            null,
            null,
            " z01_cgccpf ",
            " rh01_regist = {$matriculaServidor} ",
            " z01_cgccpf ",
            "  z01_cgccpf ",
            true
        );

        $rsCnpjEmpregador = \db_query($sqlCnpjEmpregador);

        $empregador->inscricaoEmpregador = \db_utils::fieldsMemory($rsCnpjEmpregador, 0)->z01_cgccpf;
        $empregador->idEvento = "S-2230";
        $empregador->eventojson = json_encode(array('matricula' => $matriculaServidor));

        $serviceApi = $this->getServiceApiEsocialByRecurso(Recurso::CONSULTA_RECIBO);
        $serviceApi->setDados($empregador);

        $responseAfastamento = $serviceApi->request('GET');

        foreach ($responseAfastamento as $key => $afastamento) {
            $evento = json_decode($afastamento->evento);

            if (empty($evento->fimafastamento->dttermafast)) {
                return false;
            }

            if (empty($codigoAssentamento)) {
                if (strtotime($evento->fimafastamento->dttermafast) >= strtotime($dataAfastamento)) {
                    return false;
                }
            } else {
                if (strtotime($evento->fimafastamento->dttermafast) >= strtotime($dataAfastamento)) {
                    if ($afastamento->referencia != $codigoAssentamento) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Verifica se há protocolo(recibo) para o afastamento informado
     * @param int $codigoAssentamento
     * @return bool
     */
    public function possuiProtocoloByAfastamento($codigoAssentamento)
    {
        $where = array(
            "r70_ativo IS TRUE",
            "r70_instit = " . db_getsession("DB_instit")
        );

        $campos = array(
            "z01_numcgm AS cgm",
            "z01_cgccpf AS cnpj",
            "z01_nome AS nome",
        );

        $dao = new \cl_rhlota();
        $sql = $dao->sql_query_lota_cgm(
            null,
            'DISTINCT ' . implode(', ', $campos),
            'z01_numcgm',
            implode(' AND ', $where)
        );
        $rs = db_query($sql);

        if (!$rs) {
            throw new \DBException("Não foi possível buscar os empregadores da instituição.\nContate o suporte.");
        }

        if (pg_num_rows($rs) === 0) {
            throw new Exception("Não há empregadores registrados.");
        }

        $empregadores = pg_fetch_all($rs);
        $encontrouApi = false;
        $temRecibo = true;
        foreach ($empregadores as $empregador) {
            $assentamento = new \stdClass();
            $assentamento->idReferencia = $codigoAssentamento;
            $assentamento->idEvento = "S-2230";
    
            $serviceApi = $this->getServiceApiEsocialByRecurso(Recurso::CONSULTA_RECIBO);
            $serviceApi->setDados($assentamento);

            if ($encontrouApi == true) {
                continue;
            }
            $assentamento->inscricaoEmpregador = $empregador['cnpj'];
            $response = $serviceApi->request('GET');
            
            // Caso nao tenha retorno, repetimos a consulta com o evento S-2220
            if (empty($response)) {
                $assentamento->idEvento = "S-2220";
                $serviceApi->setDados($assentamento);
                $response = $serviceApi->request('GET');
            } else {
                $encontrouApi = true;
            }
            if (!empty($response)) {
                foreach ($response as $evento) {
                    if (empty($evento->recibo)) {
                        $temRecibo = false;
                    } else {
                        foreach ($evento->recibo as $recibo) {
                            if ($recibo->ultimoRecibo == true && $recibo->excluido == true) {
                                $temRecibo = false;
                            }
                        }
                    }
                }
            }
        }
        if ($encontrouApi == false) {
            $temRecibo = false;
        }

        return $temRecibo;
        return !empty($response);
    }

    /**
     * Verifica se há configuração de conexão com a API
     * @return bool
     */
    public function possuiConfiguracaoApi()
    {
        $dadosAPI = Registry::get('app.config')->get('app.api');

        if (empty($dadosAPI['esocial']['url']) ||
            empty($dadosAPI['esocial']['login']) ||
            empty($dadosAPI['esocial']['password'])) {
            return false;
        }
        return true;
    }

    /**
     * Verifica se um assentamento é do tipo afastamento do esocial (S-2230)
     * @param string $tipo
     * @return bool
     */
    public function isTipoAfastamentoEsocial($tipo)
    {
        $afastamentoEsocial = new cl_afastamentosesocial();
        $sqlTipoAfastamento = $afastamentoEsocial->sql_query_tipo_assentamento("h12_assent", "h12_assent = '{$tipo}'");
        $rsTipoAfastamento = \db_query($sqlTipoAfastamento);

        if (pg_num_rows($rsTipoAfastamento) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param integer|null $codigo
     * @return \Assentamento
     * @throws \BusinessException
     * @throws \DBException
     */
    public function getByCodigo($codigo = null)
    {
        if (!empty($codigo)) {
            return $this->getAssentamentoCompleto($codigo);
        }
        return new \Assentamento();
    }

    /**
     * @param integer $codigoAssentamento
     * @return \Assentamento
     * @throws \BusinessException
     * @throws \DBException
     */
    private function getAssentamentoCompleto($codigoAssentamento)
    {
        $assentamentoCompleto = new \Assentamento($codigoAssentamento);
        $assentamentoCompleto->getAtributosDinamicos();
        return $assentamentoCompleto;
    }

    /**
     * @param $matricula
     * @param $codigoAssentamento
     * @param $dataInicial
     * @param null $dataFinal
     * @param bool $funcional
     * @param bool $vinculaPeriodoAquisitivo
     * @return bool
     */
    public static function naoPossuiAfastamento(
        $matricula,
        $codigoTipoAssentamento,
        $dataInicial,
        $dataFinal = null,
        $funcional = false,
        $vinculaPeriodoAquisitivo = false,
        $codigoAssentamento = null
    ) {
        /**
         * Campo data final no formulario vazia
         * - procura afastamentos com data inicial maior ou igual e com data final menor ou igual
         * - ou com data final vazia(afastamento em aberto)
         * - ou com data inicial do formulario menor ou igual a do banco (afastamento com data posterior
         *      ja cadastrado)
         */
        $whereDatas = "
            ('{$dataInicial}'::date >= h16_dtconc and '{$dataInicial}'::date <= h16_dtterm )
            or (h16_dtterm is null)
            or ( '{$dataInicial}'::date <= h16_dtconc )";

        if (!empty($dataFinal)) {
            /**
             * Caso campo com data final nao estiver vazio procura afastamento entre data inicial e final
             * ou com data final vazia(afastamento em aberto)
             */
            $whereDatas = "
                (h16_dtconc, case when h16_dtterm is null then '3000-12-31'::date else h16_dtterm+1 end)
                    overlaps ('{$dataInicial}'::date, '{$dataFinal}'::date)";
        }

        $where = "
            case
                when exists
                    (select
                        1
                    from
                        tipoasse
                    where
                        h12_codigo = '{$codigoTipoAssentamento}'
                         and h12_tipo = 'A')
                then
                    (h16_regist  = {$matricula}
                    and h12_tipo = 'A' and ({$whereDatas}))
                 else false
             end
        ";

        if ($funcional) {
            $whereTmp = "";
            if (!empty($codigoAssentamento)) {
                $whereTmp = " where rh193_assentamento_funcional != {$codigoAssentamento} ";
            }
            $where .= "
                and h16_codigo in (
                    select
                        rh193_assentamento_funcional
                    from
                        assentamentofuncional
                        {$whereTmp}
                 )";
        } else {
            if (!empty($codigoAssentamento)) {
                $where .= " AND h16_codigo != {$codigoAssentamento} ";
            }
        }

        $dao = new \cl_assenta();
        $sql = $dao->sql_query(null, '*', 'h16_dtconc', $where);
        $rs = $dao->sql_record($sql);
        /**
         * Encontrou afastamento para o periodo informado no formulario
         * retorna erro
         */
        if ($dao->numrows > 0 && !$vinculaPeriodoAquisitivo) {
            $mensagem = "Servidor já possui assentamento cadastrado para este período.";
            $assentamentos = \db_utils::getCollectionByRecord($rs);

            /**
             * Percorre assentamentos encontrados para montar mensagem de erro
             */
            foreach ($assentamentos as $assentamento) {
                /**
                 * Encontrou afastamento em aberto
                 */
                if ($assentamento->h16_dtterm == '') {
                    $mensagem = "Servidor com afastamento em aberto.";
                }

                $dataInicial = new \DBDate($assentamento->h16_dtconc);
                $dataInicial = $dataInicial->getDate(\DBDate::DATA_PTBR);

                $sDataFinal = null;

                if (!empty($assentamento->h16_dtterm)) {
                    $dataFinal = new \DBDate($assentamento->h16_dtterm);
                    $sDataFinal = $dataFinal->getDate(\DBDate::DATA_PTBR);
                }

                $mensagem .= "\n\nAfastamento encontrado: {$assentamento->h12_assent}";
                $mensagem .= "\nData inicial: {$dataInicial}";
                $mensagem .= "\nData final  : {$sDataFinal}";
            }
            throw new Exception($mensagem);
        }
    }

    public static function verificaAssentamento(\Assentamento $assentamento)
    {
        switch ($assentamento->getInstanciaTipoAssentamento()->getNatureza()) {
            case \Assentamento::NATUREZA_AUTORIZA_HORA_EXTRA:
                $dao = new \cl_assenta();
                $whereAssentamento = "";
                $codigoAssentamento = $assentamento->getCodigo();
                if (!empty($codigoAssentamento)) {
                    $whereAssentamento = " AND h16_codigo != {$codigoAssentamento} ";
                }
                /**
                 * Verifica se já existe um assentamento de autorização para a matrícula na data informada
                 */
                $sql = $dao->sql_query_tipo(
                    null,
                    '*',
                    null,
                    "h16_regist={$assentamento->getServidor()->getMatricula()}"
                        ."AND h16_dtconc='{$assentamento->getDataConcessao()->getDate()}'"
                        ."AND h12_natureza="
                    . \Assentamento::NATUREZA_AUTORIZA_HORA_EXTRA . " {$whereAssentamento} "
                );
                $rs = db_query($sql);
                if (!$rs) {
                    $mensagem = "Erro ao verificar o assentamento do servidor "
                        . $assentamento->getServidor()->getMatricula() . " na data "
                        . $assentamento->getDataConcessao()->getDate(\DBDate::DATA_PTBR) . ".";
                    throw new \DBException($mensagem);
                }
                return true;
                break;
            case \AssentamentoSubstituicao::CODIGO_NATUREZA:
                /**
                 * Valida se data final está vazia
                 */
                if (empty($assentamento->getDataTermino())) {
                    throw new Exception("A data final deve ser preenchida.");
                }

                /**
                 * Valida se data final é menor que data inicial
                 */
                if ($assentamento->getDataTermino()->getTimeStamp()
                    < $assentamento->getDataConcessao()->getTimeStamp()) {
                    throw new Exception("A data final não pode ser menor que a data inicial.");
                }

                /**
                 * Busca o mês e ano das data inicial e final para compará-las
                 */

                if ($assentamento->getDataConcessao()->getMes() != $assentamento->getDataTermino()->getMes()) {
                    throw new Exception("A data final deve estar no mesmo mês e ano da data inicial.");
                }
                return true;
                break;
        }
        return true;
    }

    /**
     * @param \Assentamento $assentamento
     * @return bool
     * @throws Exception
     */
    public static function verificaAssentamentoJustificativa(\Assentamento $assentamento)
    {
        if ($assentamento->getInstanciaTipoAssentamento()->getNatureza() == \Assentamento::NATUREZA_JUSTIFICATIVA) {
            $existeJustificativa = false;
            $assentamentoJustificativa = \AssentamentoRepository::getAssentamentoJustificativaPorTipoServidorPeriodo(
                $assentamento->getInstanciaTipoAssentamento()->getSequencial(),
                $assentamento->getServidor()->getMatricula(),
                $assentamento->getDataConcessao(),
                $assentamento->getDataTermino(),
                $assentamento->getCodigo()
            );
            if (!empty($assentamentoJustificativa)) {
                $existeJustificativa = true;
            }

            if (!$existeJustificativa) {
                $assentamentoJustificativa = \AssentamentoRepository::getAssentamentosServidorPorTipoENatureza(
                    $assentamento->getServidor(),
                    'S',
                    $assentamento->getDataConcessao(),
                    \Assentamento::NATUREZA_JUSTIFICATIVA,
                    false,
                    $assentamento->getCodigo()
                );
                if (!empty($assentamentoJustificativa)) {
                    $existeJustificativa = true;
                }
            }
            if (!$existeJustificativa) {
                $dataFim = $assentamento->getDataTermino();
                if (!empty($dataFim)) {
                    $assentamentoJustificativa = \AssentamentoRepository::getAssentamentosServidorPorTipoENatureza(
                        $assentamento->getServidor(),
                        'S',
                        $assentamento->getDataTermino(),
                        \Assentamento::NATUREZA_JUSTIFICATIVA,
                        false,
                        $assentamento->getCodigo()
                    );
                }
                if (!empty($assentamentoJustificativa)) {
                    $existeJustificativa = true;
                }
            }
            if ($existeJustificativa) {
                $mensagem = "Já existe um assentamento da natureza justificativa para este servidor na data "
                    . "informada.\nRealize a alteração no assentamento existente.";
                throw new \Exception($mensagem);
            }
        }
        return true;
    }

    public static function verficaHoraExtraManual(\Assentamento $assentamento, $isFuncional = false)
    {
        if (empty($assentamento->getDataTermino())) {
            $mensagem = 'Tipos de assentamentos de natureza "HE - Manual" devem possuir data final. '
                . 'Informe a data final do assentamento.';
            throw new \BusinessException($mensagem);
        }

        if ($isFuncional) {
            $existeHoraExtraManual = AHEM::existeAssentamentoHoraExtraHistoricoFuncionalNaData(
                $assentamento->getDataConcessao(),
                $assentamento->getServidor()
            );
        } else {
            $existeHoraExtraManual = AHEM::existeAssentamentoHoraExtraEfetividadeNaData(
                new DBDate($assentamento->getDataConcessao()),
                ServidorRepository::getInstanciaByCodigo($assentamento->getServidor()->getMatricula())
            );
        }

        if ($existeHoraExtraManual) {
            throw new DBException("Já existe assentamento para o servidor na data informada.");
        }
        return true;
    }

    public static function verficaAutorizacaoHoraExtra(\Assentamento $assentamento)
    {
        $dao = new \cl_assenta();
        if (empty($assentamento->getDataTermino())) {
            $mensagem = 'Tipos de assentamentos de natureza "Autorização de Hora Extra" devem possuir data final. '
                . 'Informe a data final do assentamento.';
            throw new \BusinessException($mensagem);
        }

        /**
         * Verifica se já existe um assentamento de autorização para a matrícula na data
         * informada não sendo o assentamento atual
         */
        $where = "h16_regist={$assentamento->getServidor()->getMatricula()}
            AND h16_dtconc='{$assentamento->getDataConcessao()->getDate()}'
            AND h12_natureza=" . \Assentamento::NATUREZA_AUTORIZA_HORA_EXTRA;
        if (!empty($assentamento->getCodigo())) {
            $where = "h16_codigo != {$assentamento->getCodigo()}
            AND h16_regist={$assentamento->getServidor()->getMatricula()}
            AND h16_dtconc='{$assentamento->getDataConcessao()->getDate()}'
            AND h12_natureza=" . \Assentamento::NATUREZA_AUTORIZA_HORA_EXTRA;
        }
        $sql = $dao->sql_query_tipo(null, '*', null, $where);
        $rs = \db_query($sql);

        if (!$rs) {
            $mensagem = "Não foi possoivel verificar se existe um assentamento de autorização de hora extra para este
                servidor na data informada.
                Realize a alteração no assentamento existente.";
            throw new Exception($mensagem);
        }

        if (pg_num_rows($rs) > 0) {
            $mensagem = "Já existe um assentamento de autorização de hora extra para este servidor na data informada.
                Realize a alteração no assentamento existente.";
            throw new Exception($mensagem);
        }
        return true;
    }

    /**
     * @param integer $matricula
     * @return bool
     * @throws \BusinessException
     */
    public static function verificaReajuste($matricula)
    {
        $dao = new \cl_assenta();
        /**
         * Verifica se já foi lançado outro assentamento com o campo tipo de rescisão informado no tipoasse
         */
        $where = " h12_tiporeajuste is not null and h12_tiporeajuste <> 0 and h16_regist = {$matricula}";

        $dao->sql_record($dao->sql_query(null, "h16_codigo", null, $where));

        if ($dao->numrows > 0) {
            throw new \BusinessException('Servidor já possui assentamento de reajuste salarial.');
        }
        return true;
    }

    public static function buscaAssentamentosPeriodo(
        $matricula,
        $codigoTipoAssentamento,
        $data
    ) {
        $dao = new \cl_assenta();

        $where = [];
        $where[] = "h16_regist = {$matricula}";
        $where[] = "h16_assent = {$codigoTipoAssentamento}";
        $where[] = "( ('$data' >= h16_dtconc)
                      and (case when h16_dtterm is not null then '$data' <= h16_dtterm else true end)) ";
        $rsAssentamentos = $dao->sql_record($dao->sql_query(
            null,
            "h16_codigo,h16_dtconc, h16_dtterm",
            null,
            implode(' and ', $where)
        ));
        if ($dao->numrows > 0) {
            return \db_utils::getCollectionByRecord($rsAssentamentos, true);
        }

        return [];
    }
}
