<?php


namespace ECidade\Financeiro\Contabilidade\Relatorio\Razao\PorConta;

abstract class Relatorio
{

    private $instituicao;
    private $dataInicial;
    private $dataFinal;
    private $anoUsu;
    private $contas;
    private $estrutural;
    private $documentos;
    private $quebraPaginaPorConta;
    private $saldoPorDia;
    private $relatorio;
    private $contasSemMovimento;
    private $recursos;

    /**
     * @return mixed
     */
    public function getInstituicao()
    {
        return $this->instituicao;
    }

    /**
     * @param mixed $instituicao
     */
    public function setInstituicao($instituicao)
    {
        $this->instituicao = $instituicao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDataInicial()
    {
        if (empty($this->dataInicial)) {
            $this->setDataInicial($this->getAnoUsu() . "-01-01");
        }
        return $this->dataInicial;
    }

    /**
     * @param mixed $dataInicial
     */
    public function setDataInicial($dataInicial)
    {
        $this->dataInicial = $dataInicial;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDataFinal()
    {
        if (empty($this->dataFinal)) {
            $this->setDataFinal($this->getAnoUsu() . "-12-31");
        }
        return $this->dataFinal;
    }

    /**
     * @param mixed $dataFinal
     */
    public function setDataFinal($dataFinal)
    {
        $this->dataFinal = $dataFinal;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAnoUsu()
    {
        return $this->anoUsu;
    }

    /**
     * @param mixed $anoUsu
     */
    public function setAnoUsu($anoUsu)
    {
        $this->anoUsu = $anoUsu;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContas()
    {
        return $this->contas;
    }

    /**
     * @param mixed $contas
     */
    public function setContas($contas)
    {
        $this->contas = $contas;
        return $this;
    }

    /**
     * @return array
     */
    public function getEstrutural()
    {
        return $this->estrutural;
    }

    /**
     * @param mixed $estrutural
     */
    public function setEstrutural(array $estrutural)
    {
        $this->estrutural = $estrutural;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDocumentos()
    {
        return $this->documentos;
    }

    /**
     * @param mixed $documentos
     */
    public function setDocumentos($documentos)
    {
        $this->documentos = $documentos;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuebraPaginaPorConta()
    {
        return $this->quebraPaginaPorConta;
    }

    /**
     * @param mixed $quebraPaginaPorConta
     */
    public function setQuebraPaginaPorConta($quebraPaginaPorConta)
    {
        $this->quebraPaginaPorConta = $quebraPaginaPorConta;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSaldoPorDia()
    {
        return $this->saldoPorDia;
    }

    /**
     * @param mixed $saldoPorDia
     */
    public function setSaldoPorDia($saldoPorDia)
    {
        $this->saldoPorDia = $saldoPorDia;
    }

    /**
     * @return mixed
     */
    public function getRelatorio()
    {
        return $this->relatorio;
    }

    /**
     * @param mixed $relatorio
     */
    public function setRelatorio($relatorio)
    {
        $this->relatorio = $relatorio;
    }

    /**
     * @return mixed
     */
    public function getContasSemMovimento()
    {
        return $this->contasSemMovimento;
    }

    /**
     * @param mixed $contasSemMovimento
     */
    public function setContasSemMovimento($contasSemMovimento)
    {
        $this->contasSemMovimento = $contasSemMovimento;
    }

    /**
     * @return array
     */
    public function getRecursos()
    {
        return $this->recursos;
    }

    /**
     * @param mixed $recursos
     */
    public function setRecursos($recursos)
    {
        $this->recursos = $recursos;
        return $this;
    }


    public function __construct()
    {
    }


    public function getComplanoPorReduzido($reduzido)
    {
        $sql = "select
                conplanoreduz.c61_codcon,
                conplanoreduz.c61_reduz,
                conplano.c60_estrut,
                conplano.c60_descr
            from conplanoreduz
            inner join conplano    
            on c60_codcon = conplanoreduz.c61_codcon and c60_anousu=conplanoreduz.c61_anousu
            where conplanoreduz.c61_anousu = {$this->getAnoUsu()}
            and c61_reduz = {$reduzido} order by conplano.c60_estrut";
        $rs = db_query($sql);
        return pg_fetch_object($rs);
    }


    public function getComplanoAnalitico()
    {

        $where = "conplanoreduz.c61_instit in ({$this->getInstituicao()})";

        if (!empty($this->getContas())) {
            $where .= " AND conplanoreduz.c61_reduz in ({$this->getContas()})";
        }
        
        
        if (!empty($this->getEstrutural())) {
            $estruturais = [];
            foreach ($this->getEstrutural() as $estrutural) {
                $estruturais[] = " conplano.c60_estrut like '".trim($estrutural)."%' ";
            }
            $where .= " AND (" . implode(" OR ", $estruturais) . ")";
        }

        $sql = "
           SELECT
                conplanoreduz.c61_codcon,
                conplanoreduz.c61_reduz,
                conplano.c60_estrut,
                conplano.c60_descr,
                codigo,
                nomeinst
            FROM conplanoreduz
            INNER JOIN conplano ON c60_codcon = conplanoreduz.c61_codcon AND c60_anousu=conplanoreduz.c61_anousu
            inner join db_config on codigo = c61_instit
            WHERE conplanoreduz.c61_anousu = {$this->getAnoUsu()} AND {$where}  ORDER BY conplano.c60_estrut
        ";
        $rs = db_query($sql);
        
        return pg_fetch_all($rs);
    }

    public function getDadosGerais($reduzido)
    {

        $where = "conplanoreduz.c61_instit in ({$this->getInstituicao()})";

        if (!empty($this->getContas())) {
            $where .= " AND conplanoreduz.c61_reduz in ({$this->getContas()})";
        }

        if (!empty($this->getEstrutural())) {
            $estruturais = [];
            foreach ($this->getEstrutural() as $estrutural) {
                $estruturais[] = " conplano.c60_estrut like '".trim($estrutural)."%' ";
            }
            $where .= " AND (" . implode(" OR ", $estruturais) . ")";
        }

        if (!empty($this->getRecursos())) {
            $where .= "AND EXISTS ( 
                                    SELECT 1 FROM  conlancamrecurso 
                                    WHERE conplanoreduz.c61_reduz = conlancamrecurso.c130_conta
                                        AND conlancamrecurso.c130_conlancam = c69_codlan
                                        AND conlancamrecurso.c130_orctiporec in ({$this->getRecursos()}))";
        }

        if (!empty($this->getDocumentos())) {
            $where .= " and c53_coddoc in ({$this->getDocumentos()})";
        }

        $where .= " and conplanoreduz.c61_reduz = {$reduzido}  
        and conplanoreduz.c61_instit in ({$this->getInstituicao()})";

        $where .= " and c69_data between '{$this->getDataInicial()}' 
        and '{$this->getDataFinal()}'  and conplanoreduz.c61_instit in ({$this->getInstituicao()})";

        $sql = "
                select conplanoreduz.c61_codcon,
                conplanoreduz.c61_reduz,
                conplano.c60_estrut,
                conplano.c60_descr as conta_descr,
                c69_codlan,
                c69_sequen,
                c69_data,
                c69_codhist,
                c53_coddoc,
                c53_descr,
                c69_debito,
                debplano.c60_descr as debito_descr,
                debplano.c60_estrut as debito_estrut,
                c69_credito,
                credplano.c60_descr as credito_descr,
                credplano.c60_estrut as credito_estrut,
                c69_valor,
                case when c69_debito = conplanoreduz.c61_reduz
                then 'D'
                else 'C'
                end  as tipo,
                c50_codhist,
                c50_descr,
                c74_codrec,
                c79_codsup,
                c75_numemp,
                e60_codemp,
                e60_resumo,
                e60_anousu,
                c73_coddot,
                c76_numcgm,
                c78_chave,
                c72_complem ,
                z01_numcgm,
                z01_nome,
                ( select k81_codpla
                from conlancamcorrente
                inner join corplacaixa on k82_data = c86_data and k82_id = c86_id and k82_autent = c86_autent
                inner join placaixarec on k81_seqpla = k82_seqpla
                where c86_conlancam = c69_codlan ) as planilha,
                ( select distinct k12_codigo
                from conlancamcorrente
                inner join corlanc on c86_id     = k12_id
                and c86_data   = k12_data
                and c86_autent = k12_autent
                where c86_conlancam = c69_codlan) as slip,
                (
                    select
                           e82_codmov 
                    from empord 
                        inner join conlancamord on conlancamord.c80_codord = empord.e82_codord 
                    where 
                          c80_codlan = conlancamval.c69_codlan limit 1
                ) as codigo_movimento
                from conplanoreduz
                inner join conlancamval on  c69_anousu=conplanoreduz.c61_anousu 
                and ( c69_debito=conplanoreduz.c61_reduz or c69_credito = conplanoreduz.c61_reduz)
                inner join conplano     on c60_codcon = conplanoreduz.c61_codcon
                and c60_anousu=conplanoreduz.c61_anousu
                inner join conplanoreduz debval on debval.c61_anousu = conlancamval.c69_anousu and
                debval.c61_reduz  = conlancamval.c69_debito
                inner join conplano  debplano  on debplano.c60_anousu = debval.c61_anousu and
                debplano.c60_codcon = debval.c61_codcon
                inner join conplanoreduz credval on credval.c61_anousu = conlancamval.c69_anousu and
                credval.c61_reduz  = conlancamval.c69_credito
                inner join conplano  credplano  on credplano.c60_anousu = credval.c61_anousu and
                credplano.c60_codcon = credval.c61_codcon
                
                left join conhist          on c50_codhist = c69_codhist
                left join conlancamdoc on c71_codlan  = c69_codlan
                left join conhistdoc   on c53_coddoc  = conlancamdoc.c71_coddoc
                left join conlancamrec on c74_codlan = c69_codlan
                and c74_anousu = c69_anousu
                left join conlancamsup on c79_codlan = c69_codlan
                
                left join conlancamemp on c75_codlan = c69_codlan
                left join empempenho   on  e60_numemp = conlancamemp.c75_numemp
                
                left join conlancamdot on c73_codlan = c69_codlan
                and c73_anousu = c69_anousu
                left join conlancamcgm on c76_codlan = c69_codlan
                left join cgm on z01_numcgm = c76_numcgm
                left join conlancamdig on c78_codlan = c69_codlan
                left join conlancamcompl on c72_codlan = c69_codlan
                where conplanoreduz.c61_anousu = {$this->getAnoUsu()} and {$where}
                order by conplano.c60_estrut, c69_data,c69_codlan,c69_sequen
      ";
        $rs = db_query($sql);

        if (!$rs) {
             new \BusinessException("Erro");
        }

        return pg_fetch_all($rs);
    }

    public function calculaSaldo($valorDebito, $valorCredito, $abs = false)
    {
        return $abs ? abs($valorDebito - $valorCredito) : $valorDebito - $valorCredito;
    }

    public function retornaSinal($valorDebito, $valorCredito)
    {
        return $valorDebito > $valorCredito ? "D" : "C";
    }

    abstract public function run();
}
