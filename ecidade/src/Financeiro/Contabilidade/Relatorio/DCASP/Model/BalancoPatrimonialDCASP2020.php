<?php

namespace ECidade\Financeiro\Contabilidade\Relatorio\DCASP\Model;

class BalancoPatrimonialDCASP2020 extends BalancoPatrimonialDCASP2019
{
    /**
     * @var integer
     */
    const CODIGO_RELATORIO = 243;

    const QUADRO_PRINCIPAL_INICIAL = 1;
    const QUADRO_PRINCIPAL_INICIO_PASSIVOS = 17;
    const QUADRO_PRINCIPAL_FINAL = 45;

    const QUADRO_ATIVOS_PASSIVOS_INICIAL = 46;
    const QUADRO_ATIVOS_PASSIVOS_FINAL = 62;

    const QUADRO_CONTAS_COMPENSACAO_INICIAL = 63;
    const QUADRO_CONTAS_COMPENSACAO_FINAL = 74;

    /**
     * Linhas que não devem serem impressa no pdf
     * @var int[]
     */
    protected $linhasOcultarImpressao = [52, 53, 54, 55, 56, 57, 59, 60];

    /**
     * @var string
     */
    private $fonteApresentar;


    public function __construct($iAnoUsu, $iCodigoRelatorio, $iCodigoPeriodo)
    {
        parent::__construct($iAnoUsu, $iCodigoRelatorio, $iCodigoPeriodo);
        $linhas = [8, 15, 16, 25, 34, 44, 45, 49, 61, 62, 68, 74];
        $this->aLinhasTotalizadoras = $linhas;
        $this->linhas = array(
            'balanco_patrimonial' => array(
                'inicio' => static::QUADRO_ATIVOS_PASSIVOS_INICIAL,
                'final' => static::QUADRO_ATIVOS_PASSIVOS_FINAL
            ),
            'contas_compensacao' => array(
                'inicio' => static::QUADRO_CONTAS_COMPENSACAO_INICIAL,
                'final' => static::QUADRO_CONTAS_COMPENSACAO_FINAL,
            )
        );
    }

    public function emitir()
    {
        $this->preparaCabecalhos();

        $this->aDados = $this->getDados();
        $this->processarQuadros();

        $this->aQuadroSuperavitDeficit = $this->getSuperavitDeficit();

        $this->configurarPdf();

        $this->processarFormasDasLinhas(array(58, 51, 50, 61, 62, 63, 68, 69, 74));

        $quadros = array(
            (object)array(
                "nome" => 'QUADRO PRINCIPAL',
                "coluna" => "ATIVO",
                "quadro" => $this->aQuadroPrincipal
            ),
            (object)array(
                "nome" => "QUADRO DE ATIVOS E PASSIVOS FINANCEIROS E PERMANENTES\n(Lei nº 4.320/1964)",
                "coluna" => "",
                "quadro" => $this->aQuadroAtivosPassivos
            ),
            (object)array(
                "nome" => "QUADRO DE CONTAS DE COMPENSAÇÃO\n(Lei nº 4.320/1964)",
                "coluna" => "",
                "quadro" => $this->aQuadroContasCompensacao
            ),
            (object)array(
                "nome" => "QUADRO DE SUPERÁVIT/DÉFICIT FINANCEIRO\n(Lei nº 4.320/1964)",
                "coluna" => "FONTES DE RECURSOS",
                "quadro" => $this->aQuadroSuperavitDeficit
            )
        );

        foreach ($quadros as $stdQuadro) {
            $this->emitirQuadro($stdQuadro->nome, $stdQuadro->coluna, $stdQuadro->quadro);
        }

        $this->escreveAssinatura("", null);
        $this->oPdf->showPDF("2020_BalancoPatrimonialDCASP_" . time());
    }

    public function fonteRecursoApresentar($fonteApresentar)
    {
        $this->fonteApresentar = $fonteApresentar;
    }

    /**
     * Retorna os valores da DDR. Contas 8211101 e 8211102
     * @return array
     */
    protected function getSuperavitDeficit()
    {
        if (!$this->exibirQuadroRelatorio(self::QUADRO_SUPERAVIT)) {
            return false;
        }
        $instit = str_replace("-", ",", $this->sListaInstit);
        $where = "c61_instit in ($instit) and c61_anousu = {$this->iAnoUsu} and c60_estrut like '82111%'";

        $sqlFiltraReduzidos = "
        select c61_reduz
          from contabilidade.conplano
          join contabilidade.conplanoreduz on (c61_codcon, c61_anousu) = (c60_codcon, c60_anousu)
         where {$where}
        ";

        $sqlReduz = "(select array_agg(c61_reduz) from ({$sqlFiltraReduzidos}) as x )::int[]";
        $datIni = $this->getDataInicial()->getDate();
        $datFim = $this->getDataFinal()->getDate();
        $encerramento = 'false';

        $pl = "contabilidade.balancete_verificacao_por_recurso";
        $sqlPL = "{$pl}($this->iAnoUsu, '{$datIni}', '$datFim', $encerramento, $sqlReduz)";

        $campos = "siconfi, complemento, fs.descricao";
        $group = 'group by 1,2,3';
        $join = 'join fontesiconfi fs on fs.codigo_siconfi = substring(siconfi, 2, 3)';
        if ($this->fonteApresentar === 'fonteRecurso') {
            $campos = "gestao, complemento, o15_descr as descricao";
            $group = 'group by 1,2,3';
            $join = 'join orctiporec rec on rec.o15_codigo = id_recurso';
        }
        if ($this->fonteApresentar === 'depara') {
            $campos = "gestao, subrecurso, complemento, o15_descr as descricao";
            $group = 'group by 1, 2, 3, 4';
            $join = 'join orctiporec rec on rec.o15_codigo = id_recurso';
        }

        $sql = "
            select {$campos},
                   sum(saldo_anterior) as saldo_anterior,
                   sum(saldo_debito) as saldo_debito,
                   sum(saldo_credito) as saldo_credito
              from {$sqlPL}
            {$join}
            {$group}
            order by 1,2
        ";

        $rs = db_query($sql);
        $dadosDDR = \db_utils::getCollectionByRecord($rs);

        $totalExercicioAtual = 0;
        $totalExercicioAnterior = 0;
        $retorno = [];
        foreach ($dadosDDR as $dado) {
            $codigo = $this->buildCodigoDDR($dado);
            $descricao = "{$codigo} - {$dado->descricao}";

            if (strlen($descricao) > 111) {
                $descricao = substr($descricao, 0, 108) . '...';
            }

            $saldoAtual = ($dado->saldo_anterior + $dado->saldo_debito + $dado->saldo_credito) * -1;
            $dado->saldo_anterior = $dado->saldo_anterior * -1;
            /**
             * OBSERVAÇÃO
             * Para padronização, a PL balancete_verificacao_por_recurso retorna valores credores sempre negativo
             * Como a natureza das contas 82111 são credoras, esse valor deve apresentar de forma positiva quando credor
             * e negativa como devedora.
             */
            $retorno[] =  (object)[
                "codigo" => $codigo,
                "descricao" => $descricao,
                "vlrexatual" => $saldoAtual,
                "vlrexanter" => $dado->saldo_anterior,
                "totalizar" => 0,
                "totalizadorFinal" => false,
                "ultimaLinhaQuadro" => false,
                "ordem" => 0,
                "nivel" => 1
            ];
            $totalExercicioAtual +=$saldoAtual;
            $totalExercicioAnterior += $dado->saldo_anterior;
        }

        $retorno[] =  (object)[
            "codigo" => null,
            "descricao" => 'Total das Fontes de Recurso',
            "vlrexatual" => $totalExercicioAtual,
            "vlrexanter" => $totalExercicioAnterior,
            "totalizar" => 1,
            "totalizadorFinal" => true,
            "ultimaLinhaQuadro" => true,
            "ordem" => 0,
            "nivel" => 1
        ];
        return $retorno;
    }

    public function buildCodigoDDR($dado)
    {
        $complemento = str_pad($dado->complemento, 4, '0', STR_PAD_LEFT);
        $codigo = "{$dado->siconfi} - {$complemento}";
        if ($this->fonteApresentar === 'fonteRecurso') {
            $codigo = "{$dado->gestao} - {$complemento}";
        }
        if ($this->fonteApresentar === 'depara') {
            $codigo = "{$dado->gestao} - {$dado->subrecurso} - {$complemento}";
        }
        return $codigo;
    }
}
