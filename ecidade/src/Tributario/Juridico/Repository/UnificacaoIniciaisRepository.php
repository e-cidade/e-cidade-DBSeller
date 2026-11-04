<?php

namespace ECidade\Tributario\Juridico\Repository;

class UnificacaoIniciaisRepository
{
    /**
     * @var integer
     */
    private $codigo;

    /**
     * @var integer
     * 1 = CGM
     * 2 = Matricula
     * 3 = Inscrição
     */
    private $origem;

    /**
     * @var integer
     * 1 = CGM
     * 2 = Matricula
     * 3 = Inscrição
     */
    private $agrupamento = 1;

    /**
     * @var integer
     */
    private $instituicao;

    /**
     * @var string
     */
    private $data;

    /**
     * @var integer
     */
    private $ano;

    /**
     * @var array
     */
    private $filtros = [];

    /**
     * UnificacaoIniciaisRepository constructor.
     * @param $iInstituicao
     * @param $sData
     * @param $iAno
     */
    public function __construct($iInstituicao, $sData, $iAno)
    {
        $this->instituicao = $iInstituicao;
        $this->data = $sData;
        $this->ano = $iAno;
    }

    /**
     * @param int $codigo
     * @return UnificacaoIniciaisRepository
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
        return $this;
    }

    /**
     * @param int $origem
     * @return UnificacaoIniciaisRepository
     */
    public function setOrigem($origem)
    {
        $this->origem = $origem;
        return $this;
    }

    /**
     * @param int $agrupamento
     * @return UnificacaoIniciaisRepository
     */
    public function setAgrupamento($agrupamento)
    {
        $this->agrupamento = $agrupamento;
        return $this;
    }

    /**
     * Monta os filtros da consulta
     * @throws \Exception
     */
    public function montaFiltros()
    {
        $this->filtros = [];

        switch ($this->origem) {
            case 1:
                $this->filtros[] = "arrenumcgm.k00_numcgm = {$this->codigo}";

                switch ($this->agrupamento) {
                    case 2:
                        $this->filtros[] = "arrematric.k00_matric IS NOT NULL";
                        break;
                    case 3:
                        $this->filtros[] = "arreinscr.k00_inscr IS NOT NULL";
                        break;
                }
                break;
            case 2:
                $this->filtros[] = "arrematric.k00_matric = {$this->codigo}";
                break;
            case 3:
                $this->filtros[] = "arreinscr.k00_inscr = {$this->codigo}";
                break;
        }

        if (count($this->filtros) == 0) {
            throw new \Exception("Selecione um filtro para consultar as iniciais.");
        }

        return $this;
    }

    /**
     * Faz a consulta na base
     * @return \stdClass[]
     * @throws \Exception
     */
    public function getIniciaisByOrigem()
    {
        $sWhere = implode(" AND ", $this->filtros);

        $sSql = /** @lang text */
                "SELECT DISTINCT
                        inicial.v50_inicial AS codigo_inicial,
                        certid.v13_certid AS codigo_certidao,
                        arretipo.k00_descr AS tipo_debito,
                        inicial.v50_data AS data_inicial,
                        (CASE WHEN termo.v07_parcel IS NOT NULL THEN TO_CHAR(termo.v07_dtlanc, 'YYYY')::integer
                              ELSE divida.v01_exerc
                              END) AS exercicio_divida,
                        (CASE WHEN termo.v07_parcel IS NOT NULL THEN 'PARCELAMENTO '||termo.v07_parcel
                              ELSE proced.v03_descr
                              END) AS nome_procedencia,
                        (CASE WHEN arrematric.k00_matric IS NOT NULL THEN 'M '||arrematric.k00_matric
                              WHEN arreinscr.k00_inscr IS NOT NULL THEN 'I '||arreinscr.k00_inscr
                              ELSE 'C '||arrenumcgm.k00_numcgm
                              END) AS origem,
                        (SELECT (substr(fc_calcula, 15, 13)::float8 +
                                 substr(fc_calcula, 28, 13)::float8 +
                                 substr(fc_calcula, 41, 13)::float8 -
                                 substr(fc_calcula, 54, 13)::float8)
                           FROM (SELECT fc_calcula(arrecad.k00_numpre,
                                                   arrecad.k00_numpar,
                                                   arrecad.k00_receit,
                                                  '{$this->data}', '{$this->data}', {$this->ano})
                                                 ) AS fc_calcula) as valor_total,
                        processoforo.v70_codforo as codigo_processo,
                        arrecad.k00_numpre as numpre
                   FROM arrecad
                  INNER JOIN arreinstit
                     ON arreinstit.k00_numpre = arrecad.k00_numpre
                    AND arreinstit.k00_instit = {$this->instituicao}
                  INNER JOIN arretipo
                     ON arretipo.k00_tipo = arrecad.k00_tipo
                   LEFT JOIN divida
                     ON divida.v01_numpre = arrecad.k00_numpre
                    AND divida.v01_numpar = arrecad.k00_numpar
                   LEFT JOIN proced
                     ON proced.v03_codigo = divida.v01_proced
                   LEFT JOIN termodiv
                     ON termodiv.coddiv = divida.v01_coddiv
                   LEFT JOIN termo
                     ON termo.v07_numpre = arrecad.k00_numpre
                   LEFT JOIN certdiv
                     ON certdiv.v14_coddiv = divida.v01_coddiv
                   LEFT JOIN certter
                     ON certter.v14_parcel = termo.v07_parcel
                   LEFT JOIN certid
                     ON (certid.v13_certid = certdiv.v14_certid OR certid.v13_certid = certter.v14_certid)
                  INNER JOIN inicialcert
                     ON inicialcert.v51_certidao = certid.v13_certid
                  INNER JOIN inicial
                     ON inicial.v50_inicial = inicialcert.v51_inicial
                  INNER JOIN tabrec
                     ON tabrec.k02_codigo = arrecad.k00_receit
                   LEFT JOIN arrenumcgm
                     ON arrenumcgm.k00_numpre = arrecad.k00_numpre
                   LEFT JOIN arrematric
                     ON arrematric.k00_numpre = arrecad.k00_numpre
                   LEFT JOIN arreinscr
                     ON arreinscr.k00_numpre = arrecad.k00_numpre
                   LEFT JOIN processoforoinicial
                     ON processoforoinicial.v71_inicial = inicial.v50_inicial
                    AND processoforoinicial.v71_anulado IS FALSE
                   LEFT JOIN processoforo
                     ON processoforo.v70_sequencial = processoforoinicial.v71_processoforo
                    AND processoforo.v70_anulado IS FALSE
                   LEFT JOIN divida.termoini
                     ON termoini.inicial = inicial.v50_inicial
                   LEFT JOIN termo termo_inicial
                     ON termo_inicial.v07_parcel = termoini.parcel
                  WHERE {$sWhere}
                    AND arretipo.k03_tipo = 18
                    AND inicial.v50_situacao = 1
                    AND (termo_inicial.v07_parcel IS NULL OR termo_inicial.v07_situacao = 2)
                  ORDER BY exercicio_divida DESC,
                           inicial.v50_inicial DESC";

        $rResult = db_query($sSql);

        if (!$rResult) {
            throw new \Exception("Erro ao buscar as iniciais. Erro: ".pg_last_error());
        }

        return \db_utils::getCollectionByRecord($rResult);
    }

    public function getByInicial($aIniciais)
    {
        $sIniciais = implode(",", $aIniciais);
        $this->filtros[] = " inicial.v50_inicial IN ({$sIniciais}) ";

        return $this->getIniciaisByOrigem();
    }

    public function getByCda($iCda)
    {
        $this->filtros = [];
        $this->filtros[] = " inicialcert.v51_certidao = {$iCda} ";

        return $this->getIniciaisByOrigem();
    }
}
