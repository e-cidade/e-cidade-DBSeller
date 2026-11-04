<?php

namespace ECidade\Tributario\Caixa\Entity;

use recibo as ReciboLegacy;
use Exception;
use DateTime;
use ECidade\Tributario\Caixa\Entity\Debito;
use ECidade\Tributario\Caixa\Enum\ArretipoEnum;
use ECidade\Tributario\Juridico\Inicial\Repository\Inicial as InicialRepository;
use ECidade\Tributario\Juridico\Inicial\Inicial;
use ECidade\Tributario\Library\Entity;
use ECidade\Tributario\Caixa\Entity\Collection\DebitoCollection;
use ECidade\Tributario\Divida\Termo\Repository\Termo as TermoRepository;

final class Recibo extends Entity
{
    private $numpre;

    /**
     * @var DateTime
     */
    private $vencimento;

    private $origem;

    private $tipo;

    private $desconto;

    private $debitos;

    private $terceiroDigito;

    private $nossoNumero;

    private $codigoBarras;

    private $linhaDigitavel;

    /**
     * O objeto que foi usado para buscar os débitos do recibo.
     * Essencialmente, é ou o Cgm, ou a Inscrição, ou a Matrícula usada
     * na hora de pesquisar alguém na geral financeira.
     *
     * @var CgmBase|Issbase|Iptubase
     */
    private $identificacao;

    /**
     * Sequencia de strings com o historico dos débitos deste recibo.
     * A chave é o código da inicial, e o valor é seu código de processo do foro
     *
     * @var array
     */
    private $itensHistorico = array();

    private $processosForo = array();

    public function __construct()
    {
        $this->debitos = new DebitoCollection();
    }

    public function setNumpre($numpre)
    {
        $this->numpre = $numpre;
    }

    public function setVencimento(DateTime $vencimento)
    {
        $this->vencimento = $vencimento;
    }

    public function setOrigem($origem)
    {
        $this->origem = $origem;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public function setDesconto($desconto)
    {
        $this->desconto = $desconto;
    }

    public function addDebito(Debito $debito)
    {
        $this->geraItemHistorico($debito);
        $this->debitos->add($debito);
    }

    public function setDebitos(DebitoCollection $debitos)
    {
        $this->debitos = $debitos;
    }

    public function setTerceiroDigito($terceiroDigito)
    {
        $this->terceiroDigito = $terceiroDigito;
    }

    public function setNossoNumero($nossoNumero)
    {
        $this->nossoNumero = $nossoNumero;
    }

    public function setCodigoBarras($codigoBarras)
    {
        $this->codigoBarras = $codigoBarras;
    }

    public function setLinhaDigitavel($linhaDigitavel)
    {
        $this->linhaDigitavel = $linhaDigitavel;
    }

    public function getNumpre()
    {
        return $this->numpre;
    }

    /**
     * @return DateTime
     */
    public function getVencimento()
    {
        return $this->vencimento;
    }

    public function getOrigem()
    {
        return $this->origem;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function getDesconto()
    {
        return $this->desconto;
    }

    public function getDebitos()
    {
        return $this->debitos;
    }

    public function getTerceiroDigito()
    {
        return $this->terceiroDigito;
    }

    public function getNossoNumero()
    {
        return $this->nossoNumero;
    }

    public function getCodigoBarras()
    {
        return $this->codigoBarras;
    }

    public function getLinhaDigitavel()
    {
        return $this->linhaDigitavel;
    }

    private function geraItemHistorico(Debito $debito)
    {
        switch ($debito->getTipo()) {
            case ArretipoEnum::PARCELAMENTO_FORO:
                return $this->addItemHistoricoParcelamentoForo($debito);
                break;

            case ArretipoEnum::INICIAL_FORO:
                $inicial = InicialRepository::getInstance()
                    ->scopeNumpre($debito->getNumpre())
                    ->withCodigoForo()
                    ->first();
                return $this->addItemHistoricoInicialForo($inicial->getCodigo());
                break;
        }
    }

    private function addItemHistoricoParcelamentoForo(Debito $debito)
    {
        $termoRepository = TermoRepository::getInstance();

        $chave = "PARCEL_FORO:{$debito->getNumpre()}";

        $termo = $termoRepository
            ->scopeNumpre($debito->getNumpre())
            ->first();

        $parcelas = array();
        foreach ($debito->getParcelas() as $parcela) {
            $parcelas[] = "{$parcela->getNumero()}/{$termo->getTotalParcelas()}";
        }
        $parcelas = implode(', ', $parcelas);

        $itemHistorico = "Parcelamento {$termo->getCodigo()} - {$parcelas}";

        $origens = $termoRepository->getOrigem($termo);

        $this->itensHistorico[$chave] = $itemHistorico;
        foreach ($origens as $origem) {
            $this->addItemHistoricoInicialForo($origem->getCodigo());
        }

        // Quebra de linha
        $this->itensHistorico[] = '';
    }

    private function addItemHistoricoInicialForo($codigoInicial)
    {
        $chave = "INICIAL:{$codigoInicial}";
        // Utilizando o Repository para padroniar iniciais e parcelamentos
        $inicialRepository = InicialRepository::getInstance();
        $inicial = $inicialRepository->withCodigoForo()->scopeInicial($codigoInicial)->first();

        $exercicios = Inicial::getExercicios($inicial->getCodigo(), ArretipoEnum::INICIAL_FORO);

        $itemHistorico = "Inicial: {$inicial->getCodigo()} - Processo do Foro: {$inicial->getCodigoForo()}";
        $itemHistorico .= " - Exercício(s): " . implode(", ", $exercicios);

        $this->processosForo[$inicial->getCodigo()] = $inicial->getCodigoForo();
        $this->itensHistorico[$chave] = $itemHistorico;
    }

    /**
     * @return  CgmBase|Issbase|Iptubase
     */
    public function getIdentificacao()
    {
        return $this->identificacao;
    }

    /**
     * @param   CgmBase|Issbase|Iptubase
     */
    public function setIdentificacao($identificacao)
    {
        $this->identificacao = $identificacao;
    }

    /**
     * Retorna regra de desconto referente ao numpre e numpar de um débito
     *
     * @deprecated Compatibilidade com código legado
     * @todo isolar método em um service separado
     *
     * @param integer $numpre - Numpre do débito
     * @param integer $numpar - Parcela do débito
     * @param integer $tipo - Tipo de débito(arretipo)
     * @param string $whereloteador - Filtro quando houver loteador
     * @param integer $totalregistrospassados - Total de parcelas selecionadas na CGF
     * @param integer $totregistros - Variável da CGF "$totregistros"
     * @param string $vencimentoRecibo Data de vencimento do recibo no padrão 'Y-m-d'
     * @return integer $regraDesconto - Inteiro indicando a regra de desconto que deve ser aplicada ao débito e parcela
     *                                            declarada.
     */
    public function reciboDesconto(
        $numpre,
        $numpar,
        $tipo,
        $whereloteador,
        $totalregistrospassados,
        $totregistros,
        $vencimentoRecibo
    ) {
        global $k40_codigo, $k40_todasmarc, $cadtipoparc;

        $cadtipoparc = 0;

        $sqlvenc = "select k00_dtvenc
                from arrecad
               where k00_numpre = $numpre
                 and k00_numpar = $numpar";
        $resultvenc = db_query($sqlvenc) or die($sqlvenc);
        if (pg_num_rows($resultvenc) == 0) {
            return 0;
        }
        db_fieldsmemory($resultvenc, 0);


        $dDataUsu = date("Y-m-d", db_getsession("DB_datausu"));

        $sqltipoparc = "select k40_codigo,
                         k40_todasmarc,
                         cadtipoparc
                    from tipoparc
                         inner join cadtipoparc    on cadtipoparc     = k40_codigo
                         inner join cadtipoparcdeb on k41_cadtipoparc = cadtipoparc
                   where maxparc = 1
                     and '{$dDataUsu}' >= k40_dtini
                     and '{$dDataUsu}' <= k40_dtfim
                     and k41_arretipo   = $tipo
                     $whereloteador
                     and '$vencimentoRecibo' >= k41_vencini
                     and '$vencimentoRecibo' <= k41_vencfim ";

        $resulttipoparc = db_query($sqltipoparc);
        if (pg_num_rows($resulttipoparc) > 0) {
            db_fieldsmemory($resulttipoparc, 0);
        } else {
            $sqltipoparc = "select k40_codigo,
                           k40_todasmarc,
                           cadtipoparc
                      from tipoparc
                           inner join cadtipoparc on cadtipoparc = k40_codigo
                           inner join cadtipoparcdeb on k41_cadtipoparc = cadtipoparc
                     where maxparc = 1
                       and k41_arretipo = $tipo
                       and '{$dDataUsu}' >= k40_dtini
                       and '{$dDataUsu}' <= k40_dtfim
                       $whereloteador
                       and '$vencimentoRecibo' >= k41_vencini
                       and '$vencimentoRecibo' <= k41_vencfim ";

            $resulttipoparc = db_query($sqltipoparc);

            if (pg_num_rows($resulttipoparc) == 1) {
                db_fieldsmemory($resulttipoparc, 0);
            } else {
                $k40_todasmarc = false;
            }
        }

        $sqltipoparcdeb = "SELECT * FROM cadtipoparcdeb LIMIT 1";
        $resulttipoparcdeb = db_query($sqltipoparcdeb);
        $passar = false;

        if (pg_num_rows($resulttipoparcdeb) == 0) {
            $passar = true;
        } else {
            $sqltipoparcdeb = "select k40_codigo, k40_todasmarc
                         from cadtipoparcdeb
                              inner join cadtipoparc on k40_codigo = k41_cadtipoparc
                        where k41_cadtipoparc = $cadtipoparc and
                       k41_arretipo = $tipo $whereloteador and
                       '$vencimentoRecibo' >= k41_vencini and
                       '$vencimentoRecibo' <= k41_vencfim ";
            $resulttipoparcdeb = db_query($sqltipoparcdeb) or die($sqltipoparcdeb);
            if (pg_num_rows($resulttipoparcdeb) > 0) {
                $passar = true;
            }
        }

        if (pg_num_rows($resulttipoparc) == 0
            || ($k40_todasmarc == 't' ? $totalregistrospassados <> $totregistros : false)
            || $passar == false
        ) {
            $desconto = 0;
        } else {
            $desconto = $k40_codigo;
        }

        return $desconto;
    }

    /**
     * @return array
     */
    public function getItensHistorico()
    {
        return $this->itensHistorico;
    }

    /**
     * @param array $itensHistorico
     */
    public function setItensHistorico($itensHistorico)
    {
        $this->itensHistorico = $itensHistorico;
    }

    /**
     * @return array
     */
    public function getProcessosForo()
    {
        return $this->processosForo;
    }

    /**
     * @param array $processosForo
     */
    public function setProcessosForo($processosForo)
    {
        $this->processosForo = $processosForo;
    }
}
