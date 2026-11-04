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

namespace ECidade\Tributario\Arrecadacao\Custas\Partilha;

use ECidade\Tributario\Arrecadacao\Custas\Interfaces\Calculo;
use ECidade\Tributario\Arrecadacao\Custas\Calculo\Valor;
use ECidade\Tributario\Arrecadacao\Repository;
use \Taxa;
use ECidade\Tools\DbSqlRepository as DbSql;
use ECidade\Tributario\Juridico\Inicial\Repository\InicialNumpreRepository;
use ECidade\Tributario\Juridico\ProcessoForo\Repository\ProcessoForoInicial;
use ECidade\Tributario\Juridico\ProcessoForo\Repository\ProcessoForo;

abstract class Partilha
{
    private $calculo;

    private $codigoProcessoForo;

    protected $iniciais;

    /** @var Repository\Taxa */
    protected $taxaRepository;

    public function __construct(Calculo $calculo)
    {
        $this->calculo = $calculo;
        $this->taxaRepository = Repository\Taxa::getInstance();
    }

    abstract protected function getPartilhasIsentas();
    abstract protected function getPartilhasPagas();
    abstract protected function getTaxasEmissao();
    abstract public function processaRemocaoTaxa(array $taxas, array $partilhas);

    public function processaValorCalculo()
    {
        return $this->calculo->calcular();
    }

    private function calculaValorTaxaJurosMulta($valorCorrigido, $valorJuros = 0, $valorMulta = 0, $valorDesconto = 0)
    {
        return $valorCorrigido + $valorJuros + $valorMulta - $valorDesconto;
    }

    public function setCodigoProcessoForo($codigoProcessoForo)
    {
        $this->codigoProcessoForo = $codigoProcessoForo;
    }

    public function setCodigoIniciais($iniciais)
    {
        $this->iniciais = $iniciais;
    }

    public function calculaValorTaxa(Taxa $taxa, Valor $valor)
    {
        $valorCustas = $taxa->getValor();

        $valorDebito = $valor->getValorCorrigido() - $valor->getValorDesconto();

        if ($taxa->isAplicaJuroMulta()) {
            $valorDebito = $this->calculaValorTaxaJurosMulta(
                $valor->getValorCorrigido(),
                $valor->getValorJuros(),
                $valor->getValorMulta(),
                $valor->getValorDesconto()
            );
        }

        if ($taxa->isPorcentagem()) {
            if (!empty($this->codigoProcessoForo)) {
                $iCodigoProcessoForo = $this->codigoProcessoForo->getCodigo();

                $processoForo = ProcessoForo::getInstance();
                $isParcelamento = $processoForo->isParcelamento($iCodigoProcessoForo);
                $nValorProcessoForoDebitos = $this->getValorProcessoForoDebitos($iCodigoProcessoForo);
                if (!empty($nValorProcessoForoDebitos)) {
                    $valorDebito = $nValorProcessoForoDebitos;
                } else {
                    if ($isParcelamento) {
                        $valorDebito = $this->getValorNumpresInicialParcelamento();
                    } else {
                        $valorDebito = $this->getValorTotalIniciaisProcesso();
                    }
                }
            }

            $valorCustas = ($valorDebito * ($taxa->getPercentual() / 100));
            
            if ($valorCustas < $taxa->getValorMinimo()) {
                $valorCustas = $taxa->getValorMinimo();
            } elseif ($valorCustas > $taxa->getValorMaximo()) {
                $valorCustas = $taxa->getValorMaximo();
            }
        }

        return round($valorCustas, 2);
    }

    public function processaRemocaoTaxaPaga(array $taxas, array $partilhas)
    {
        $taxasRemover = array();

        foreach ($partilhas as $inicialPartilha) {
            foreach ($inicialPartilha->getCustas() as $inicialPartilhaCusta) {
                $taxa = $inicialPartilhaCusta->getTaxa();
                $taxasRemover[] = $taxa->getCodigoTaxa();
            }
        }

        $taxasRemover = array_unique($taxasRemover);

        foreach ($taxas as $i => $taxa) {
            if ($taxa->isFixo() && in_array($taxa->getCodigoTaxa(), $taxasRemover)) {
                unset($taxas[$i]);
            }
        }

        return $taxas;
    }

    /**
     * Método para pegar o valor somado de todas as iniciais vinculadas ao processo
     * @return float
     */
    private function getValorTotalIniciaisProcesso()
    {
        $valorTotalIniciaisProcesso = 0;

        $aIniciais = $this->verificaIniciaisVinculadasProcesso();

        $inicialNumpreRepository = new InicialNumpreRepository();

        foreach ($aIniciais as $inicial) {
            $numpres = array();
            $inicialNumpreRepository->scopeInicial($inicial->v71_inicial);

            $aNumpres = $inicialNumpreRepository->get();

            foreach ($aNumpres as $numpre) {
                $numpres[] = $numpre->getNumpre();
            }

            $valorTotalIniciaisProcesso += $this->getValorTotalInicial(implode(",", $numpres));
        }

        return $valorTotalIniciaisProcesso;
    }

    /**
     * Método para retornar as iniciais vinculadas ao processo que não estejam anuladas
     * @return array
     */
    protected function verificaIniciaisVinculadasProcesso()
    {
        $where = "v71_processoforo = {$this->codigoProcessoForo} AND v71_anulado = 'f' and v50_situacao = 1 ";

        return ProcessoForoInicial::getInstance()->find($where, "processoforoinicial.v71_inicial");
    }

    /**
     * Método que retorna o valor total por inicial para ser usado no cálculo da partilha
     * @return integer
     */
    private function getValorTotalInicial($numpres)
    {
        $anousu = db_getsession("DB_anousu");
        $dataCorrente = date("Y-m-d");

        $sql = "
        SELECT sum(q.total)::double precision as total
                  FROM (SELECT SUM((substr(fc_calcula,15,13)::float8+
                                    substr(fc_calcula,28,13)::float8+
                                    substr(fc_calcula,41,13)::float8-
                                    substr(fc_calcula,54,13)::float8)) AS total
                          FROM (SELECT fc_calcula(a.k00_numpre,
                                                  a.k00_numpar,
                                                  a.k00_receit,
                                                  '{$dataCorrente}',
                                                  '{$dataCorrente}',
                                                  {$anousu}
                                                )
                                  FROM arrecad a 
                                 WHERE k00_numpre IN ({$numpres})) x

                            UNION ALL
                
                        SELECT SUM(k00_valor)::double precision AS total
                          FROM arrepaga 
                         WHERE k00_numpre IN ({$numpres})
                           AND k00_receit NOT IN (SELECT ar36_receita 
                                                    FROM taxa)) q ";
        $result = db_query($sql);

        return \db_utils::fieldsMemory($result, 0)->total;
    }

    /**
     *  Verificar debitos da processoForoDebitos
     * @param $codigoProcessoForo
     */
    private function getValorProcessoForoDebitos($codigoProcessoForo)
    {

        $anousu = db_getsession("DB_anousu");
        $dataCorrente = date("Y-m-d");

        $sql = "select
                    sum(
                        fc_corre(
                            v91_receit,
                            v91_dtoper,
                            v91_vlrhist,
                            '{$dataCorrente}',
                            {$anousu},
                            v91_dtvenc
                        )
                    ) + sum(
                        round(
                            (
                                fc_corre(
                                    v91_receit,
                                    v91_dtoper,
                                    v91_vlrhist,
                                    '{$dataCorrente}',
                                    {$anousu},
                                    v91_dtvenc
                                )
                            ) * fc_juros(
                                v91_receit,
                                v91_dtvenc,
                                '{$dataCorrente}',
                                v91_dtoper,
                                false,
                                {$anousu}
                            ) :: numeric(20, 10),
                            2
                        )
                    ) + sum(
                        round(
                            (
                                fc_corre(
                                    v91_receit,
                                    v91_dtoper,
                                    v91_vlrhist,
                                    '{$dataCorrente}',
                                    {$anousu},
                                    v91_dtvenc
                                )
                            ) * fc_multa(
                                v91_receit,
                                v91_dtvenc,
                                '{$dataCorrente}',
                                v91_dtoper,
                                {$anousu}
                            ) :: numeric(20, 10),
                            2
                        )
                    ) as total
                from
                processoforodebitos
                where
                    v91_processoforo = {$codigoProcessoForo}
                    and not exists (select 1   
                                        from processoforoinicial 
                                    where v71_processoforo = v91_processoforo 
                                      and v71_inicial = v91_inicial 
                                      and v71_anulado is true)
                    ";
        
        $result = db_query($sql);
        return \db_utils::fieldsMemory($result, 0)->total;
    }

    /**
     * Método que retorna o valor da iniciais quando tiver em parcelamento
     * @return integer
     */
    
    private function getValorInicialOld($numpres)
    {

        $dataCorrecao = date("Y-m-d");
        $dataCorrecao = new \DBDate($dataCorrecao);

        $sql = "
        SELECT sum(total.total)::double precision as total
                  FROM (SELECT SUM((substr(fc_calculaold,15,13)::float8+
                                    substr(fc_calculaold,28,13)::float8+
                                    substr(fc_calculaold,41,13)::float8-
                                    substr(fc_calculaold,54,13)::float8)) AS total
                          FROM (SELECT fc_calculaold(a.k00_numpre,
                                                  a.k00_numpar,
                                                  a.k00_receit,
                                                  '{$dataCorrecao->getDate()}',
                                                  '{$dataCorrecao->getDate()}',
                                                  {$dataCorrecao->getAno()}
                                                )
                                  FROM arreold a
                                 WHERE k00_numpre IN ({$numpres})) x ) as total";

        $result = db_query($sql);

        if (!$result) {
            throw new \Exception("Erro ao buscar dados atualizados da divida.\n 
                                  Não encontrou vinculo da inicial com o débito");
        }

        return \db_utils::fieldsMemory($result, 0)->total;
    }



    /**
     * método para buscar o valor do parcelamento pela inicial
     *
     */
    private function getValorNumpresInicialParcelamento()
    {

        $valorTotalParcelamento = 0;
        $aIniciais = $this->verificaIniciaisVinculadasProcesso();
        $numpres = array();
        
        $inicialNumpreRepository = new InicialNumpreRepository();

        foreach ($aIniciais as $inicial) {
            $numpres = array();
            $inicialNumpreRepository->scopeInicial($inicial->v71_inicial);

            $aNumpres = $inicialNumpreRepository->get();

            foreach ($aNumpres as $numpre) {
                $numpres[] = $numpre->getNumpre();
            }

            $valorTotalParcelamento += $this->getValorInicialOld(implode(",", $numpres));
        }

        $nValorSemParcelamentoIniciais = $this->getValorTotalIniciaisProcesso();
        if (!empty($nValorSemParcelamentoIniciais)) {
            $valorTotalParcelamento = ($valorTotalParcelamento + $nValorSemParcelamentoIniciais);
        }

        return $valorTotalParcelamento;
    }

    /**
     * Verifica se existe um recibo valido gerado para o processo selecionado
     * @return bool
     */
    protected function verificaReciboValido()
    {
        $dataCorrente = date("Y-m-d");
        $iniciaiSelecionadas = implode(",", $this->iniciais);

        $sql = "SELECT COUNT(distinct(k00_numpre)) as quantidadeRecibo
                  FROM recibopaga rec
                 INNER JOIN processoforopartilhacusta
                    ON k00_numnov = v77_numnov
                 INNER JOIN processoforopartilha
                    ON v76_sequencial = v77_processoforopartilha
                 INNER JOIN processoforo
                    ON v70_sequencial = v76_processoforo
                 WHERE v76_processoforo = {$this->processoForo}
                   AND k00_dtpaga >= '{$dataCorrente}'
                   AND (SELECT COUNT(DISTINCT(k00_numpre))
                          FROM recibopaga
                         INNER JOIN processoforopartilhacusta
                            ON k00_numnov = rec.k00_numnov
                         INNER JOIN processoforopartilha
                            ON v76_sequencial = v77_processoforopartilha
                         INNER JOIN processoforo
                            ON v70_sequencial = v76_processoforo
                         WHERE v76_processoforo = {$this->processoForo}
                           AND k00_dtpaga >= '{$dataCorrente}'
                        ) = 1
                   AND k00_receit IN (SELECT ar36_receita
                                        FROM taxa
                                       WHERE ar36_honorario = 't'
                                         AND ar36_debitoscomprocesso = 't')
                   AND k00_numpre IN (SELECT DISTINCT(v59_numpre)
                                        FROM inicialnumpre
                                       INNER JOIN processoforoinicial
                                          ON v71_inicial = v59_inicial
                                         AND v71_processoforo = v76_processoforo
                                       WHERE v59_inicial NOT IN ({$iniciaiSelecionadas}))";

        $result = db_query($sql);
        $quantidadeRecibo = \db_utils::fieldsMemory($result, 0)->quantidaderecibo;

        if ($quantidadeRecibo > 0) {
            return true;
        }

        return false;
    }
}
