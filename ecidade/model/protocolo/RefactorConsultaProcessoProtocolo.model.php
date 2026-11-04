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

/**
 * Refatoração da consulta de processo do protocolo
 *
 * @package protocolo
 * @author Vinicius Leivas <vinicius.leivas@dbseller.com.br>
 */
class RefactorConsultaProcessoProtocolo
{

    private $iCodigoProcesso;
    private $usuarioLogado;
    private $aMovimentacoes;
    private $ordem;

    /**
     * @throws Exception
     */
    public function __construct($parametros)
    {
        $this->ordem = !empty($parametros->ordem) ? strtoupper($parametros->ordem) : 'ASC';
        $this->iCodigoProcesso = $parametros->iCodigoProcesso;
        $this->usuarioLogado = db_getsession("DB_id_usuario");
        $this->processarMovimentacoes();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function processarMovimentacoes()
    {
        $codigoProcesso = $this->iCodigoProcesso;
        $transferencias = $this->buscarTransferencias($codigoProcesso);

        if (empty($transferencias)) {
            return;
        }

        $movimentacoes = [];
        $indexTramiteInicial = $this->buscarIndexTramiteInicial($transferencias);
        foreach ($transferencias as $key => $transferencia) {
            $isTramiteInicial = $key === $indexTramiteInicial;

            $movimentacoes[$key] = $this->montarDadosMovimentacao($transferencia, $isTramiteInicial);
        }

        $this->adicionarMovimentacoes($movimentacoes);
    }

    /**
     * @param $codigoProcesso
     * @return array|false
     */
    public function buscarTransferencias($codigoProcesso)
    {
        $camposTransferencia = "
            *,
            atual.descrdepto AS departamento_atual,
            atual.instit AS instituicao_atual,
            instiatual.nomeinstabrev AS instituicao_atual_descricao,
            usu_atual.nome AS nome_usuario_atual,
            usu_destino.login as usuario_destino_login,
            destino.coddepto as departamento_destino,
            destino.descrdepto as departamento_destino_descricao,
            depto_andam.descrdepto as departamento_andamento_descricao,
            instit_andam.nomeinstabrev as instituicao_andamento_descricao,
            instit_andam.codigo as instituicao_andamento,
            usu_andam.nome AS usuario_andamento_descricao
        ";

        $ordem = $this->ordem;
        $clproctransfer = new cl_proctransfer();
        $sqlTransferencias = $clproctransfer->sql_query_movimentacoes(
            null,
            $camposTransferencia,
            "
                p62_dttran $ordem,
                p62_hora $ordem,
                p62_codtran $ordem,
                p61_dtandam $ordem,
                p61_hora $ordem,
                p61_codandam $ordem
            ",
            "
                p63_codproc = $codigoProcesso
                AND
                    CASE
                        WHEN p61_codandam IS NOT NULL
                        THEN p61_codproc = $codigoProcesso
                        ELSE TRUE
                    END
            "
        );

        $transferencias = $clproctransfer->sql_record($sqlTransferencias);
        return pg_fetch_all($transferencias) ?: [];
    }

    /**
     * @param $transferencias
     * @return int|string|null
     */
    private function buscarIndexTramiteInicial($transferencias)
    {
        reset($transferencias);
        if ($this->ordem === 'ASC') {
            return key($transferencias);
        }

        end($transferencias);
        return key($transferencias);
    }

    /**
     * @param $transferencia
     * @param $isTramiteInicial
     * @return void
     */
    private function montarDadosMovimentacao($transferencia, $isTramiteInicial)
    {
        $dadosMovimentacao = [];
        $dadosMovimentacao['transferencia'] = $this->montarDadosTransferencia($transferencia, $isTramiteInicial);
        $dadosMovimentacao['andamento'] = $this->montarDadosAndamento($transferencia);
        $dadosMovimentacao['despachos'] = $this->montarDadosDespacho($transferencia);
        $dadosMovimentacao['transferenciasInternas'] = $this->montarDadosMovimentacoesInternas($transferencia);

        if ($this->ordem === 'DESC') {
            $dadosMovimentacao = array_reverse($dadosMovimentacao);
        }

        return $dadosMovimentacao;
    }

    /**
     * @param array $transferencia
     * @return RefactorDadosMovimentacaoProcessoProtocolo|bool
     */
    private function montarDadosTransferencia($transferencia, $isTramiteInicial = false)
    {
        // Omite transferência quando arquivado/desarquivado
        if (!empty($transferencia['p69_arquivado'])) {
            return false;
        }

        $dadosMovimentacao = new RefactorDadosMovimentacaoProcessoProtocolo();
        $dadosMovimentacao->sData = db_formatar($transferencia['p62_dttran'], 'd');
        $dadosMovimentacao->sHora = $transferencia['p62_hora'];
        $dadosMovimentacao->iDepartamento = $transferencia['p62_coddepto'];
        $dadosMovimentacao->sDepartamento = $transferencia['departamento_atual'];
        $dadosMovimentacao->iInstituicao = $transferencia['instituicao_atual'];
        $dadosMovimentacao->sInstituicao = $transferencia['instituicao_atual_descricao'];
        $dadosMovimentacao->sLogin = $transferencia['nome_usuario_atual'];
        $dadosMovimentacao->sOrgao = $transferencia['o40_orgao'] . ' - ' . $transferencia['o40_descr'];
        $dadosMovimentacao->sDespacho = '';
        $dadosMovimentacao->sObservacoes = $this->montarObservacaoTransferencia(
            $transferencia,
            $isTramiteInicial
        );

        return $dadosMovimentacao;
    }

    /**
     * @param array $transferencia
     * @return string
     */
    private function montarObservacaoTransferencia($transferencia, $isTramiteInicial)
    {
        $usuarioDestino = $transferencia['p62_id_usorec'];
        $observacao = 'Transferência ' . $transferencia['p62_codtran'];
        $observacao .= ' p/ o Departamento: ' . $transferencia['departamento_destino'];
        $observacao .= ' - ' . $transferencia['departamento_destino_descricao'];

        if ($isTramiteInicial) {
            $observacao = "Tramite Inicial " . $transferencia['p62_codtran'] . ' p/ Departamento: ';
            $observacao .= $transferencia['departamento_destino'] . " - ";
            $observacao .= $transferencia['departamento_destino_descricao'];
        }

        if (!empty($usuarioDestino)) {
            $observacao .= " - usuário especificado: $usuarioDestino - " . $transferencia['usuario_destino_login'];
        } else {
            $observacao .= ' (sem usuário especificado)';
        }

        return $observacao;
    }

    /**
     * @param $transferencia
     * @return false|RefactorDadosMovimentacaoProcessoProtocolo
     */
    private function montarDadosAndamento($transferencia)
    {
        if (empty($transferencia['p61_codandam'])) {
            return false;
        }

        $dadosAndamento = new RefactorDadosMovimentacaoProcessoProtocolo();
        $dadosAndamento->sData = db_formatar($transferencia['p61_dtandam'], 'd');
        $dadosAndamento->sHora = $transferencia['p61_hora'];
        $dadosAndamento->iDepartamento = $transferencia['p61_coddepto'];
        $dadosAndamento->sDepartamento = $transferencia['departamento_andamento_descricao'];
        $dadosAndamento->iInstituicao = $transferencia['instituicao_andamento'];
        $dadosAndamento->sInstituicao = $transferencia['instituicao_andamento_descricao'];
        $dadosAndamento->sLogin = $transferencia['usuario_andamento_descricao'];
        $dadosAndamento->sOrgao = $transferencia['o40_orgao'] . ' - ' . $transferencia['o40_descr'];
        $dadosAndamento->sDespacho = $transferencia['p61_despacho'];
        $dadosAndamento->sObservacoes = $this->montarObservacaoAndamento($transferencia);

        return $dadosAndamento;
    }

    /**
     * @param $transferencia
     * @return string
     */
    private function montarObservacaoAndamento($transferencia)
    {
        $observacao = "Recebeu Transferência - " . $transferencia['p62_codtran'];
        if (!empty($transferencia['p69_codarquiv'])) {
            $observacao = $transferencia['p69_arquivado'] === 't' ? 'Processo Arquivado' : 'Desarquivamento';
        }

        return $observacao;
    }

    /**
     * @param $transferencia
     * @return array|false
     */
    private function montarDadosDespacho($transferencia)
    {
        if (empty($transferencia['p61_codandam'])) {
            return false;
        }

        $despachos = $this->buscarDespachos($transferencia['p61_codandam']);
        $tiposDespacho = [1 => 'Interno', 2 => ''];

        $dadosDespacho = [];
        foreach ($despachos as $key => $despacho) {
            if ($despacho['p78_transint'] === 't') {
                continue;
            }

            $dadosDespacho[$key] = new RefactorDadosMovimentacaoProcessoProtocolo();
            $dadosDespacho[$key]->sData = db_formatar($despacho['p78_data'], 'd');
            $dadosDespacho[$key]->sHora = $despacho['p78_hora'];
            $dadosDespacho[$key]->iDepartamento = $despacho['departamento_andamento'];
            $dadosDespacho[$key]->sDepartamento = $despacho['departamento_andamento_descricao'];
            $dadosDespacho[$key]->iAndamentoInterno = $despacho['p78_sequencial'];
            $dadosDespacho[$key]->iInstituicao = $despacho['instituicao_andamento'];
            $dadosDespacho[$key]->sInstituicao = $despacho['instituicao_andamento_descricao'];
            $dadosDespacho[$key]->sLogin = $despacho['usuario_andamento_descricao'];
            $dadosDespacho[$key]->sOrgao = $transferencia['o40_orgao'] . ' - ' . $transferencia['o40_descr'];
            $dadosDespacho[$key]->sDespacho = $despacho['p78_despacho'];
            $dadosDespacho[$key]->sObservacoes = $despacho['tipo_despacho_descricao'] . " ";
            $dadosDespacho[$key]->sObservacoes .= $tiposDespacho[$despacho['tipo_despacho']];

            if ($despacho['p78_publico'] === 't' || $despacho['p78_usuario'] == $this->usuarioLogado) {
                $dadosDespacho[$key]->lImprimir = true;
            }

            if ($despacho['possui_documento'] === 't') {
                $dadosDespacho[$key]->lAnexos = true;
            }
        }


        return $dadosDespacho;
    }

    /**
     * @param int $codigoAndamento
     * @return array
     */
    public function buscarDespachos($codigoAndamento)
    {
        $ordem = $this->ordem;
        $camposDespacho = "
            procandamint.*,
            db_usuarios.id_usuario AS usuario_andamento,
            db_usuarios.nome AS usuario_andamento_descricao,
            db_depart.coddepto AS departamento_andamento,
            db_depart.descrdepto AS departamento_andamento_descricao,
            db_config.codigo AS instituicao_andamento,
            db_config.nomeinstabrev AS instituicao_andamento_descricao,
            coalesce(p100_descricao,'Despacho') as tipo_despacho_descricao,
            coalesce(p100_sequencial, 1) as tipo_despacho,
            EXISTS (
                SELECT 1
                FROM protprocessodocumento
                WHERE p01_procandamint = p78_sequencial
            ) AS possui_documento
        ";


        $clproctransfer = new cl_proctransfer();
        $sqlDespachos = $clproctransfer->sql_query_despachos(
            $codigoAndamento,
            $camposDespacho,
            "
                p78_data $ordem,
                p78_hora $ordem,
                p78_sequencial $ordem
            "
        );
        $despachos = $clproctransfer->sql_record($sqlDespachos);

        return pg_fetch_all($despachos) ?: [];
    }

    /**
     * @param $transferencia
     * @return array
     */
    private function montarDadosMovimentacoesInternas($transferencia)
    {
        $transferenciasInternas = $this->buscarTransferenciasInternas($transferencia['p61_codandam']);
        $andamentosInternos = $this->buscarAndamentosInternos($transferencia['p61_codandam']);
        $andamentosCriados = false;

        $movimentacoes = [];
        foreach ($transferenciasInternas as $key => $transferenciaInterna) {
            $movimentacoes[$key]['transferencias'] = $this->montarDadosTransferenciaInterna($transferenciaInterna);

            if ($andamentosCriados) {
                continue;
            }

            $andamentosCriados = true;
            $movimentacoes[$key]['andamentos'] = $this->montarDadosAndamentosInternos(
                $transferenciaInterna,
                $andamentosInternos
            );
        }

        return $movimentacoes;
    }

    /**
     * @param $codigoAndamento
     * @return array|false
     */
    public function buscarTransferenciasInternas($codigoAndamento)
    {
        if (empty($codigoAndamento)) {
            return [];
        }

        $ordem = $this->ordem;
        $campos = "
            proctransferint.*,
            procandam.p61_coddepto,
            db_usuarios.id_usuario AS codigo_usuario_andamento,
            db_usuarios.nome AS nome_usuario_andamento,
            db_depart.coddepto AS departamento_andamento,
            db_depart.descrdepto AS departamento_andamento_descricao,
            db_config.codigo AS instituicao_andamento,
            db_config.nomeinstabrev AS instituicao_andamento_descricao,
            usuario_destino.nome AS nome_usuario_destino,
            usuario_destino.id_usuario AS codigo_usuario_destino
        ";

        $clproctransfer = new cl_proctransfer();
        $sqlTransferenciasInternas = $clproctransfer->sql_query_transferencias_internas(
            $codigoAndamento,
            $campos,
            "p88_codigo $ordem"
        );
        $transferenciasInternas = $clproctransfer->sql_record($sqlTransferenciasInternas);

        return pg_fetch_all($transferenciasInternas) ?: [];
    }

    /**
     * @param $codigoAndamento
     * @return array|false
     */
    public function buscarAndamentosInternos($codigoAndamento)
    {
        if (empty($codigoAndamento)) {
            return [];
        }

        $ordem = $this->ordem;
        $clproctransfer = new cl_proctransfer();
        $sqlQueryAndamentosInternos = $clproctransfer->sql_query_andamentos_internos(
            $codigoAndamento,
            "
                procandamint.*,
                db_usuarios.nome AS nome_usuario_recebimento
            ",
            "p78_sequencial $ordem"
        );
        $andamentosInternos = $clproctransfer->sql_record($sqlQueryAndamentosInternos);

        return pg_fetch_all($andamentosInternos) ?: [];
    }

    /**
     * @param $transferencia
     * @return false|RefactorDadosMovimentacaoProcessoProtocolo
     */
    private function montarDadosTransferenciaInterna($transferencia)
    {
        if (empty($transferencia['p88_codigo'])) {
            return false;
        }

        $dadosMovimentacao = new RefactorDadosMovimentacaoProcessoProtocolo();
        $dadosMovimentacao->sData = db_formatar($transferencia['p88_data'], 'd');
        $dadosMovimentacao->sHora = $transferencia['p88_hora'];
        $dadosMovimentacao->iInstituicao = $transferencia['instituicao_andamento'];
        $dadosMovimentacao->sInstituicao = $transferencia['instituicao_andamento_descricao'];
        $dadosMovimentacao->iDepartamento = $transferencia['p61_coddepto'];
        $dadosMovimentacao->sDepartamento = $transferencia['departamento_andamento_descricao'];
        $dadosMovimentacao->sLogin = $transferencia['nome_usuario_andamento'];
        $dadosMovimentacao->sOrgao = $transferencia['o40_orgao'] . ' - ' . $transferencia['o40_descr'];
        $dadosMovimentacao->sDespacho = $transferencia['p88_despacho'];
        $dadosMovimentacao->sObservacoes = 'Transferência Interna - ' . $transferencia['p88_codigo'];
        $dadosMovimentacao->sObservacoes .= ' para: ' . $transferencia['codigo_usuario_destino'] . ' ';
        $dadosMovimentacao->sObservacoes .= ' - ' . $transferencia['nome_usuario_destino'];

        return $dadosMovimentacao;
    }

    /**
     * @param $transferencia
     * @return false|RefactorDadosMovimentacaoProcessoProtocolo
     */
    private function montarDadosAndamentosInternos($transferencia, $andamentos)
    {
        $dadosAndamentosInternos = [];
        foreach ($andamentos as $key => $andamento) {
            if (empty($andamento['p78_sequencial'])) {
                continue;
            }

            if ($andamento['p78_transint'] === 'f') {
                continue;
            }

            $dadosAndamentosInternos[$key] = new RefactorDadosMovimentacaoProcessoProtocolo();
            $dadosAndamentosInternos[$key]->sData = db_formatar($andamento['p78_data'], 'd');
            $dadosAndamentosInternos[$key]->sHora = $andamento['p78_hora'];
            $dadosAndamentosInternos[$key]->sDepartamento = $transferencia['departamento_andamento_descricao'];
            $dadosAndamentosInternos[$key]->iDepartamento = $transferencia['p61_coddepto'];
            $dadosAndamentosInternos[$key]->iInstituicao = $transferencia['instituicao_andamento'];
            $dadosAndamentosInternos[$key]->sInstituicao = $transferencia['instituicao_andamento_descricao'];
            $dadosAndamentosInternos[$key]->sLogin = $andamento['nome_usuario_recebimento'];
            $dadosAndamentosInternos[$key]->sOrgao = $transferencia['o40_orgao'] . ' - ' . $transferencia['o40_descr'];
            $dadosAndamentosInternos[$key]->sDespacho = $andamento['p78_despacho'];
            $dadosAndamentosInternos[$key]->sObservacoes = "Recebeu Transferência Interna";
            $dadosAndamentosInternos[$key]->iAndamentoInterno = $andamento['p78_sequencial'];
        }

        return $dadosAndamentosInternos;
    }

    /**
     * @param $movimentacoes
     * @return void
     */
    public function adicionarMovimentacoes($movimentacoes)
    {
        foreach ($movimentacoes as $movimentacao) {
            if (is_array($movimentacao)) {
                $this->adicionarMovimentacoes($movimentacao);
                continue;
            }

            if (!empty($movimentacao)) {
                $this->aMovimentacoes[] = $movimentacao;
            }
        }
    }

    /**
     * @return mixed
     */
    public function getMovimentacoes()
    {
        return $this->aMovimentacoes;
    }
}

/**
 * Refactor com dados da movimentacao do processo
 *
 * @package protocolo
 * @author Jeferson Belmiro <jeferson.belmiro@dbseller.com.br>
 */
class RefactorDadosMovimentacaoProcessoProtocolo
{

    public $sData;
    public $sHora;
    public $iDepartamento;
    public $sDepartamento;
    public $sOrgao;
    public $iInstituicao;
    public $sInstituicao;
    public $sLogin;
    public $sObservacoes;
    public $sDespacho;
    public $iAndamentoInterno;
    public $lImprimir = false;
    public $lAnexos = false;

    /**
     * Valida antes de declarar propriedades do refactor
     * - nao permite usar propriedades nao declaradas
     *
     * @param string $sVariavel
     * @param mixed $mValor
     * @access public
     * @exception - variavel nao declarada
     * @return void
     */
    public function __set($sVariavel, $mValor)
    {

        if (!property_exists($this, $sVariavel)) {
            throw new Exception(__CLASS__ . ": Propriedade {$sVariavel} não encontrada.");
        }

        $this->{$sVariavel} = $mValor;
    }
}
