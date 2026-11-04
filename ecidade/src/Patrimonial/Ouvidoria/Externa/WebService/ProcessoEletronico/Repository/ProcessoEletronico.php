<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2020  DBSeller Servicos de Informatica
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
namespace ECidade\Patrimonial\Ouvidoria\Externa\WebService\ProcessoEletronico\Repository;

use ECidade\Patrimonial\Ouvidoria\Externa\WebService\ProcessoEletronico\Model\ProcessoEletronico
    as ProcessoEletronicoModel;
use ECidade\Patrimonial\Ouvidoria\Externa\WebService\ProcessoEletronico\Model\Requerente;

use \cl_cidadao;
use \cl_ouvidoriaatendimento;
use \cl_ouvidoriaatendimentocidadao;
use \cl_ouvidoriaatendimentoprocessoeletronico;
use \db_utils;
use \Exception;
use Illuminate\Support\Facades\Log;
use \JSON;

class ProcessoEletronico
{
    const TIPO_SEQUENCIAL = 1;
    const TIPO_SEQUENCIAL_ANO = 2;
    /**
     * @var PreProcesso
     */
    private static $instancia;

    /**
     * @var PreProcessoCollection
     */
    private $collection;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return PreProcesso
     */
    public static function getInstancia()
    {
        if (static::$instancia === null) {
            static::$instancia = new ProcessoEletronico();
        }

        return static::$instancia;
    }

    public function salvar(ProcessoEletronicoModel $processoEletronicoModel)
    {
        $response = (object)[
            'sucesso' => false,
            'status' => null,
            'atendimento' => null
        ];

        try {
            $requerente = $processoEletronicoModel->getRequerente();
            $solicitacaoJSON = $processoEletronicoModel->getSolicitacaoJSON();

            $atendimento = $this->salvarAtendimento($processoEletronicoModel);

            if ($requerente->getNome() != Requerente::ANONIMO && $requerente->getCpf() != null) {
                $cidadao = $this->salvarCidadao($requerente);
                $this->salvarAtendimentoCidadao($cidadao, $atendimento);
            }

            $this->salvarJSONAtendimento(
                $solicitacaoJSON,
                $atendimento,
                $processoEletronicoModel->getClientAPPAtendimentoID(),
                $processoEletronicoModel->getCodigoAtendimentoAnterior()
            );

            $response->sucesso = true;
            $response->atendimento = (object)[
                'sequencial' => $atendimento->ov01_sequencial
                , 'numero' => $atendimento->ov01_numero
                , 'ano' => $atendimento->ov01_anousu
            ];
        } catch (\Exception $e) {
            Log::debug($e->getMessage()." FILE: ".$e->getFile()." Line:".$e->getLine());
            $response->status = utf8_encode(urldecode(($e->getMessage())));
        }

        return $response;
    }

    /**
     * @param ProcessoEletronicoModel $processoEletronicoModel
     * @return cl_ouvidoriaatendimento
     * @throws Exception
     */
    protected function salvarAtendimento(ProcessoEletronicoModel $processoEletronicoModel)
    {
        $tipoProcesso = $processoEletronicoModel->getTipoProcesso();
        $usuario = $processoEletronicoModel->getUsuario();
        $departamento = $processoEletronicoModel->getDepartamento();
        $instituicao = $processoEletronicoModel->getInstituicao();
        $requerente = $processoEletronicoModel->getRequerente()->getNome();
        $anoAtendimento = date('Y');
        $dataAtendimento = date('Y-m-d');
        $horaAtendimento = date('H:i');
        $formaReclamacao = ProcessoEletronicoModel::FORMA_RECLAMACAO_PROCESSO_ELETRONICO;

        $numeroAtendimento = $this->getNumeroAtendimento($instituicao->getCodigo(), $anoAtendimento);

        $daoOuvidoriaAtendimento = new cl_ouvidoriaatendimento();
        $daoOuvidoriaAtendimento->ov01_situacaoouvidoriaatendimento = 1;
        $daoOuvidoriaAtendimento->ov01_tipoprocesso = $tipoProcesso->getCodigo();
        $daoOuvidoriaAtendimento->ov01_formareclamacao = $formaReclamacao;
        $daoOuvidoriaAtendimento->ov01_tipoidentificacao = 2;
        $daoOuvidoriaAtendimento->ov01_usuario = $usuario->getCodigo();
        $daoOuvidoriaAtendimento->ov01_depart = $departamento->getCodigo();
        $daoOuvidoriaAtendimento->ov01_instit = $instituicao->getCodigo();
        $daoOuvidoriaAtendimento->ov01_numero = $numeroAtendimento;
        $daoOuvidoriaAtendimento->ov01_anousu = $anoAtendimento;
        $daoOuvidoriaAtendimento->ov01_dataatend = $dataAtendimento;
        $daoOuvidoriaAtendimento->ov01_horaatend = $horaAtendimento;
        $daoOuvidoriaAtendimento->ov01_requerente = $requerente;
        $daoOuvidoriaAtendimento->ov01_solicitacao = "Solicitação aberta via processo eletrônico";
        $daoOuvidoriaAtendimento->ov01_executado = "";

        $daoOuvidoriaAtendimento->incluir(null);

        if ($daoOuvidoriaAtendimento->erro_status == "0") {
            $msg = "Ocorreu algo inesperado ao salvar os dados da solicitação, tente mais tarde.";
            $msg .= "Persistindo, contate o administrador do sistema. \n";
            $msg .= "-- metodo: salvarAtendimento() \n";
            $msg .= "Erro técnico: " . $daoOuvidoriaAtendimento->erro_msg;
            $msg.=  " - sql" . $daoOuvidoriaAtendimento->erro_sql;

            throw new Exception($msg);
        }

        return $daoOuvidoriaAtendimento;
    }

    /**
     * @return cl_cidadao|false|object
     * @throws Exception
     */
    protected function salvarCidadao($requerente)
    {

        /**
         * VERIFICA SE O CIDADÃO JÁ EXISTE CASO CONTRARIO CADASTRA CIDADÃO
         */
        $daoCidadao = new cl_cidadao();

        $sqlCidadao = $daoCidadao->sql_query_file(
            null,
            null,
            "*",
            null,
            "ov02_cnpjcpf='{$requerente->getCpf()}'"
        );

        $rsCidadao = $daoCidadao->sql_record($sqlCidadao);

        if ($rsCidadao) {
            return pg_fetch_object($rsCidadao);
        }

        $daoCidadao->ov02_seq = ProcessoEletronicoModel::SEQ;
        $daoCidadao->ov02_nome = $requerente->getNome();
        $daoCidadao->ov02_cnpjcpf = $requerente->getCpf();
        $daoCidadao->ov02_situacaocidadao = 1;
        $daoCidadao->ov02_ativo = 't';
        $daoCidadao->ov02_data = date('Y-m-d');

        $daoCidadao->incluir(null, ProcessoEletronicoModel::SEQ);

        if ($daoCidadao->erro_status == "0") {
            $msg = "Ocorreu algo inesperado ao salvar os dados do requerente, tente mais tarde.";
            $msg .= "Persistindo, contate o administrador do sistema. \n";
            $msg .= "-- metodo: salvarCidadao() \n";
            $msg .= "Erro técnico: " . $daoCidadao->erro_msg;

            throw new Exception($msg);
        }

        return $daoCidadao;
    }

    /**
     * @throws Exception
     */
    protected function salvarAtendimentoCidadao($cidadao, $atendimento)
    {
        $daoOuvidoriaAtendimentoCidadao = new cl_ouvidoriaatendimentocidadao();
        $daoOuvidoriaAtendimentoCidadao->ov10_seq = ProcessoEletronicoModel::SEQ;
        $daoOuvidoriaAtendimentoCidadao->ov10_cidadao = $cidadao->ov02_sequencial;
        $daoOuvidoriaAtendimentoCidadao->ov10_ouvidoriaatendimento = $atendimento->ov01_sequencial;

        $daoOuvidoriaAtendimentoCidadao->incluir(null);

        if ($daoOuvidoriaAtendimentoCidadao->erro_status == "0") {
            $msg = "Ocorreu algo inesperado ao salvar o vínculo de atendimento e cidadão.";
            $msg .= "Persistindo, contate o administrador do sistema. \n";
            $msg .= "-- metodo: salvarAtendimentoCidadao() \n";
            $msg .= "Erro técnico: " . $daoOuvidoriaAtendimentoCidadao->erro_msg;

            throw new Exception($msg);
        }
    }

    /**
     * @return cl_ouvidoriaatendimentoprocessoeletronico
     * @throws Exception
     */
    protected function salvarJSONAtendimento(
        $solicitacaoJSON,
        $atendimento,
        $clientAPPAtendimentoID,
        $codigoAtendimentoAnterior = null
    ) {
        $daoOuvidoriaAtendimentoProcesso = new cl_ouvidoriaatendimentoprocessoeletronico();
        $daoOuvidoriaAtendimentoProcesso->ov33_ouvidoriaatendimento = $atendimento->ov01_sequencial;
        $daoOuvidoriaAtendimentoProcesso->ov33_informacoesprocesso = pg_escape_string($solicitacaoJSON);
        $daoOuvidoriaAtendimentoProcesso->ov33_client_atendimento_id = $clientAPPAtendimentoID;
        if (!empty($codigoAtendimentoAnterior)) {
            $daoOuvidoriaAtendimentoProcesso->ov33_ouvidoriaatendimento_anterior = $codigoAtendimentoAnterior;
        }

        $daoOuvidoriaAtendimentoProcesso->incluir(null);

        if ($daoOuvidoriaAtendimentoProcesso->erro_status == "0") {
            $msg = "Ocorreu algo inesperado ao salvar o JSON de dados da solicitação de atendimento.";
            $msg .= "Persistindo, contate o administrador do sistema. \n";
            $msg .= "-- metodo: salvarJSONAtendimento() \n";
            $msg .= "Erro técnico: " . $daoOuvidoriaAtendimentoProcesso->erro_msg;
            $msg .= " - sql" . $daoOuvidoriaAtendimentoProcesso->erro_sql;
            throw new Exception($msg);
        }

        return $daoOuvidoriaAtendimentoProcesso;
    }

    private function getNumeroAtendimento($instituicaoId, $anoUsu)
    {
        $daoOuvidoriaParametro = db_utils::getDao("ouvidoriaparametro");
        $sqlParametro = $daoOuvidoriaParametro->sql_query_file(
            $instituicaoId,
            $anoUsu,
            "ov06_tiponumprocesso"
        );
        $rsParametro = $daoOuvidoriaParametro->sql_record($sqlParametro);

        $numero = 1;
        $tipoControleNumeracao = 1;
        if ($rsParametro && pg_num_rows($rsParametro) >= 1) {
            $tipoControleNumeracao = 2;
        }

        // Consulta Numero do Atendimento
        switch ($tipoControleNumeracao) {
            case self::TIPO_SEQUENCIAL:
                $sqlNumeroAtendimento = "  SELECT
                      COALESCE(max(ov01_numero),0) + 1 as seq
                  FROM ouvidoriaatendimento
                    where
                         ov01_instit = {$instituicaoId}
                ";
                $rsNumeroAtendimento = db_query($sqlNumeroAtendimento);

                if ($rsNumeroAtendimento) {
                    $numeroAtendimento = db_utils::fieldsMemory($rsNumeroAtendimento, 0);

                    return $numeroAtendimento->seq;
                }
                break;

            case self::TIPO_SEQUENCIAL_ANO:
                $sqlAnoAtendimento = "SELECT 1 FROM ouvidoriaatendimento WHERE ov01_anousu = " . $anoUsu;
                $rsAnoAtendimento = db_query($sqlAnoAtendimento);
                if ($rsAnoAtendimento && pg_num_rows($rsAnoAtendimento) > 0) { //Sequencial por ano
                    $sqlProximoNumero = "SELECT COALESCE(max(ov01_numero),0) + 1 as seq
                                          FROM ouvidoriaatendimento
                                         WHERE ov01_anousu = ";
                    $sqlProximoNumero .= db_getsession("DB_anousu");
                    $rsProximoNumero = db_query($sqlProximoNumero);
                    $numeroAtendimento = db_utils::fieldsMemory($rsProximoNumero, 0);

                    return $numeroAtendimento->seq;
                }
                break;
        }

        return $numero;
    }
}
