<?php

namespace IntegracaoExterna\Infisc\IntegraDebitos;

use IntegracaoExterna\Infisc\Enums\StatusProcessamentoEnum;
use IntegracaoExterna\Infisc\Managers\QueryManager;

abstract class IntegraDebitosBaseService
{
    /**
     * @var QueryManager
     */
    protected $origemManager;

    /**
     * @var QueryManager
     */
    protected $destinoManager;

    /**
     * @var stdClass
     */
    protected $dados;

    /**
     * @var string
     */
    private $nomeTabelaPrincipal;

    public function __construct(QueryManager $origemManager, QueryManager $destinoManager)
    {
        $this->origemManager = $origemManager;
        $this->destinoManager = $destinoManager;
    }

    /**
     * @param $dados
     * @return $this
     */
    public function setDados($dados)
    {
        $this->dados = $dados;

        return $this;
    }

    /**
     * @param string $nomeTabelaPrincipal
     */
    public function setNomeTabelaPrincipal($nomeTabelaPrincipal)
    {
        $this->nomeTabelaPrincipal = $nomeTabelaPrincipal;
    }

    /**
     * @throws \Exception
     */
    public function atualizaStatusProcessamento($statusProcessamento, $motivo = null)
    {
        if (!StatusProcessamentoEnum::isValid($statusProcessamento)) {
            throw new \Exception("Status inválido.");
        }

        $camposAtualizar = ["status_processamento = '{$statusProcessamento}'"];

        if ($statusProcessamento == StatusProcessamentoEnum::PROCESSADO) {
            $motivo = "NULL";
        } else {
            $motivo = "'{$motivo}'";
        }

        if ($motivo) {
            $camposAtualizar[] = "motivo_status = ".utf8_encode($motivo);
        }

        $updated = $this->destinoManager->update(
            $this->nomeTabelaPrincipal,
            $camposAtualizar,
            ["sequencial = {$this->dados->sequencial}"]
        );

        if (!$updated) {
            throw new \Exception("Não foi possível atualizar o status [erro: {$this->destinoManager->getError()}].");
        }
    }

    /**
     * @throws \Exception
     */
    protected function getIssVarInfo(
        $tipoDebito,
        $codigoInscricao,
        $anoCompetencia,
        $mesCompetencia,
        $codigoIntegraDebitos = null,
        $numpre = null,
        $numpar = null,
        $codigoCgm = null
    ) {
        $sqlBuscaIssVar = "SELECT q05_codigo, q05_numpre, q05_numpar, k00_receit, k00_tipo
                             FROM issvar
                       LEFT JOIN arrecad
                               ON arrecad.k00_numpre = issvar.q05_numpre
                               AND arrecad.k00_numpar = issvar.q05_numpar
                       LEFT JOIN arreinscr
                               ON arreinscr.k00_numpre = issvar.q05_numpre
                       LEFT JOIN arrenumcgm
                               ON arrenumcgm.k00_numpre = issvar.q05_numpre
                        LEFT JOIN issvar_infisc_integra_debitos
                               ON issvar_infisc_integra_debitos.q196_issvar = issvar.q05_codigo";

        if ($numpre && $numpar) {
            $sqlBuscaIssVar .= " WHERE issvar.q05_numpre = {$numpre} AND issvar.q05_numpar = {$numpar}";
        } elseif ($codigoIntegraDebitos) {
            $sqlBuscaIssVar .= " WHERE issvar_infisc_integra_debitos.q196_integra_debitos = {$codigoIntegraDebitos}";
        } else {
            $sqlBuscaIssVar .= "
                WHERE arrecad.k00_tipo = {$tipoDebito}
                  AND issvar.q05_ano = {$anoCompetencia}
                  AND issvar.q05_mes = {$mesCompetencia}";

            if ($codigoInscricao) {
                $sqlBuscaIssVar .=  " AND arreinscr.k00_inscr = {$codigoInscricao} ";
            } elseif ($codigoCgm) {
                $sqlBuscaIssVar .=  " AND arrenumcgm.k00_numcgm = {$codigoCgm} ";
            } else {
                throw new \Exception("Informe a inscrição ou o CGM.");
            }
        }

        return $this->origemManager->runRawQuery($sqlBuscaIssVar)->get();
    }

    /**
     * @throws \Exception
     */
    protected function buscarCodigoInscricao($codigoIntegraEmpresas, $cnpj, $numpre = null)
    {
        if ($codigoIntegraEmpresas) {
            $dadosEmpresa = $this->destinoManager->query(
                "integra_infisc.integra_empresas",
                "inscricao",
                ["sequencial = {$codigoIntegraEmpresas}"]
            )->get();

            if ($dadosEmpresa) {
                return $dadosEmpresa->inscricao;
            }
        }

        if ($cnpj) {
            $sqlInscricaoPorCnpj = "SELECT q02_inscr AS inscricao
                                      FROM cgm
                                INNER JOIN issbase
                                        ON q02_numcgm = z01_numcgm
                                     WHERE z01_cgccpf = '{$cnpj}';";

            $dadosEmpresa = $this->origemManager->runRawQuery($sqlInscricaoPorCnpj)->get();

            if ($dadosEmpresa) {
                return $dadosEmpresa->inscricao;
            }
        }

        if ($numpre) {
            $dadosEmpresa = $this->origemManager->query(
                "caixa.arreinscr",
                "k00_inscr",
                ["k00_numpre = {$numpre}"]
            )->get();

            if ($dadosEmpresa) {
                return $dadosEmpresa->k00_inscr;
            }
        }

        return null;
    }
}
