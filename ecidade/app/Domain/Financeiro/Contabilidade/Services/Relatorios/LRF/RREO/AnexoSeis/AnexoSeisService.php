<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoSeis;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Contabilidade\Factories\TemplateFactory;
use App\Domain\Financeiro\Contabilidade\Relatorios\LRF\RREO\XlsAnexoSeis;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\AnexosService;
use PhpOffice\PhpWord\Settings;
use NcJoes\OfficeConverter\OfficeConverter;
use DBDate;

class AnexoSeisService extends AnexosService
{
    protected $sections = [
        'receita_1' => [1, 54],  // RECEITAS PRIMÁRIAS
        'despesa_1' => [40, 55], // DESPESAS PRIMÁRIAS
        'baldesp_1' => [58, 59], // JUROS NOMINAIS
        'baldesp_2' => [62, 69], // CÁLCULO DO RESULTADO NOMINAL
        'baldesp_3' => [71, 77], // AJUSTE METODOLÓGICO
    ];

    protected $linhasProcessarRpsDespesaPrimaria = [
        40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54
    ];

    protected $linhasNaoProcessar = [

    ];

    protected $totalizarSoma = [
        1 => [3, 4, 5, 6, 7, 8, 10, 11, 13, 14, 15, 16, 17, 18, 19, 20, 22, 23],
        2 => [3, 4, 5, 6, 7],
        9 => [10, 11],
        12 => [13, 14, 15, 16, 17, 18, 19, 20],
        21 => [22, 23],
        25 => [26, 27, 29, 30, 31, 33, 34, 36, 37],
        28 => [29, 30, 31],
        32 => [33, 34],
        35 => [36, 37],
        40 => [41, 42, 43],
        45 => [46, 48, 49, 50, 51, 52],
        47 => [48, 49, 50, 51],
    ];

    protected $totalizarSubtracao = [
        24 => [1, 10, 22],
        38 => [25, 26, 27, 29, 30, 36],
        44 => [40, 42],
        53 => [45, 48, 49, 50, 52],
    ];

    /**
     * Valor manual informado na linha:
     * 57 - Meta fixada no Anexo de Metas Fiscais da LDO para o exercício de referência
     * do quadro: META FISCAL PARA O RESULTADO PRIMÁRIO
     * @var float
     */
    protected $valorManual1;
    /**
     * Valor manual informado na linha:
     * 61 - Meta fixada no Anexo de Metas Fiscais da LDO para o exercício de referência
     * do quadro: META FISCAL PARA O RESULTADO NOMINAL
     * @var float
     */
    protected $valorManual2;

    /**
     * Quadro - INFORMAÇÕES ADICIONAIS
     * Valor da linha 81 - Recursos Arrecadados em Exercícios Anteriores - RPPS
     * @var float
     */
    protected $valorPrevisaoAtualizada;
    /**
     * Quadro - INFORMAÇÕES ADICIONAIS
     * Valor da linha 82 - Superávit Financeiro Utilizado para Abertura e Reabertura de Créditos Adicionais
     * @var float
     */
    protected $valorSaldoFinalAcumulado;
    /**
     * Quadro - INFORMAÇÕES ADICIONAIS
     * Valor da linha 83 - RESERVA ORÇAMENTÁRIA DO RPPS
     * @var float
     */
    protected $valorTotalCreditos;

    /**
     * @throws \Exception
     */
    public function __construct($filtros)
    {
        $this->exercicio = $filtros['DB_anousu'];

        $this->emissor = \InstituicaoRepository::getInstituicaoByCodigo($filtros['DB_instit']);
        $template = TemplateFactory::getTemplate($filtros['codigo_relatorio'], $filtros['periodo']);

        $this->constructAssinaturas($filtros['DB_instit']);
        $this->constructInstituicoes(DBConfig::whereIn('codigo', $filtros['instituicoes'])->get());
        $this->constructPeriodo($filtros['periodo']);
        $this->constructRelatorio($filtros['codigo_relatorio']);
        $this->processaEnteFederativo();
        $this->parser = new XlsAnexoSeis($template);
    }


    public function emitir()
    {
        $this->processar();

        foreach ($this->linhasOrganizadas as $section => $linhas) {
            $this->parser->addCollection($section, $linhas);
        }

        $this->addParserVariaveisFixas();

        $filename = $this->parser->gerar();
        
        Settings::setTempDir('tmp/');
        $filePdf = basename($filename, ".xlsx").".pdf";
        $converter = new OfficeConverter($filename);
        $converter->convertTo($filePdf);

        return [
            'xls' => $filename,
            'xlsLinkExterno' => ECIDADE_REQUEST_PATH . $filename,
            'outros' => [
                [
                  'documento' => Settings::getTempDir().$filePdf,
                  'nome' => 'Anexo VI - Demonstrativo dos Resultados Primário e Nominal (PDF)'
                ]
            ]
        ];
    }

    /**
     *
     */
    protected function processar()
    {
        $this->processaLinhas($this->linhas);

        $this->processarRpsDespesaPrimaria();
        $this->processaReceita($this->getBalanceteReceitaExercicioAnterior(), $this->linhas[81]);

        $this->criaProriedadesValor();

        $this->linhas[66]->saldo_anterior_acumulado -= $this->linhas[84]->inscricao_rp_processado;
        $this->linhas[66]->saldo_final_acumulado -= $this->linhas[84]->saldo_rp_processado;
        $this->organizaLinhas();
        $this->totalizarLinhas();

        $this->valorManual1 = $this->linhas[57]->valor;
        $this->valorManual2 = $this->linhas[61]->valor;

        $this->valorPrevisaoAtualizada = $this->linhas[81]->previsao_atualizada;
        $this->valorSaldoFinalAcumulado = $this->linhas[82]->saldo_final_acumulado;
        $this->valorSaldoInicial = $this->linhas[83]->total_creditos;
    }

    public function getSimplificado()
    {
        $this->processar();

        $primario = $this->getObjetoSimplificado('Resultado Primário - Acima da Linha');
        $nominal = $this->getObjetoSimplificado('Resultado Nominal - Acima da Linha');

        $primario = $this->calculaSimplificado($primario, 56, 57);
        $nominal = $this->calculaSimplificado($nominal, 60, 61);

        return [$primario, $nominal];
    }

    /**
     *
     */
    protected function processarRpsDespesaPrimaria()
    {
        foreach ($this->linhasProcessarRpsDespesaPrimaria as $linha) {
            $this->processaRestoPagar($this->getDadosRestosPagar(), $this->linhas[$linha]);
        }
    }

    /**
     * Esse metodo deve ser chamado após o metoto organizar linhas
     */
    protected function totalizarLinhas()
    {
        $this->calcularSoma();
        $this->posTotalizar();
    }

    protected function posTotalizar()
    {
        $this->calcularSubtracao();

        $this->totalizaLinha39();
        $this->totalizaLinha55();
        $this->totalizaLinha56();
        $this->totalizaLinha60();
        $this->totalizaLinha69();
        $this->totalizaLinha70();
        $this->totalizaLinha71();
        $this->totalizaLinha78();
        $this->totalizaLinha79();
    }

    protected function totalizaLinha39()
    {
        $this->somarLinha(39, [24, 38]);
    }

    protected function totalizaLinha55()
    {
        $this->somarLinha(55, [44, 53, 54]);
    }

    /**
     * 56 - RESULTADO PRIMÁRIO - Acima da Linha (XXIV) = [XIIa - (XXIIIa +XXIIIb + XXIIIc)]
     */
    protected function totalizaLinha56()
    {
        $linha55 = $this->linhas[55];
        $despesa = $linha55->pago_acumulado + $linha55->pagamento_rp_processado + $linha55->pagamento_rp_nao_processado;
        $this->linhas[56]->valor = $this->linhas[39]->arrecadado_acumulado - $despesa;
    }

    /**
     *  RESULTADO NOMINAL - Acima da Linha (XXVII) = XXIV + (XXV - XXVI)
     */
    protected function totalizaLinha60()
    {
        $valorJuros = $this->linhas[58]->saldo_final_acumulado - $this->linhas[59]->saldo_final_acumulado;
        $this->linhas[60]->valor = $this->linhas[56]->valor + ($valorJuros);
    }

    /**
     * DÍVIDA CONSOLIDADA LÍQUIDA (XXXI) = (XXVIII - XXIX)
     */
    protected function totalizaLinha69()
    {
        $this->subtraiLinha(69, [62, 63]);
    }

    /**
     * RESULTADO NOMINAL - Abaixo da Linha (XXXII) = (XXXIa - XXXIb)
     */
    protected function totalizaLinha70()
    {
        $valor = $this->linhas[69]->saldo_anterior_acumulado - $this->linhas[69]->saldo_final_acumulado;
        $this->linhas[70]->valor = $valor;
    }

    /**
     * VARIAÇÃO SALDO RPP = (XXXIII) = (XXXa - XXXb)
     */
    protected function totalizaLinha71()
    {
        $valor = $this->linhas[66]->saldo_anterior_acumulado - $this->linhas[69]->saldo_final_acumulado;
        $this->linhas[71]->valor = $valor;
    }

    /**
     * RESULTADO NOMINAL AJUSTADO - Abaixo da Linha
     * (XXXIX) = (XXXII - XXXIII - IX + XXXIV + XXXV - XXXVI + XXXVII + XXXVIII)
     *      78 =     70 -     71 - 72 +    73 +   74 -    75 +     76 + 77
     */
    protected function totalizaLinha78()
    {
        $this->linhas[78]->valor = (
            $this->linhas[70]->valor -
            $this->linhas[71]->valor -
            $this->linhas[72]->valor +
            $this->linhas[73]->saldo_final_acumulado +
            $this->linhas[74]->saldo_final_acumulado -
            $this->linhas[75]->saldo_final_acumulado +
            $this->linhas[76]->saldo_final_acumulado +
            $this->linhas[77]->saldo_final_acumulado
        );
    }

    /**
     * RESULTADO PRIMÁRIO - Abaixo da Linha (XXXIX) = XXXVIII - (XXV - XXVI)
     */
    protected function totalizaLinha79()
    {
        $valor = $this->linhas[58]->saldo_final_acumulado - $this->linhas[59]->saldo_final_acumulado;
        $this->linhas[79]->valor = $this->linhas[78]->valor - ($valor);
    }

    protected function getObjetoSimplificado($string)
    {
        return (object)[
            "descricao" => $string,
            "resultado_apurado_ate_bimestre" => 0,
            "meta_fixada_anexo_metas_fiscais" => 0,
            "relacao_meta" => 0,
            "totalizar" => false,
            "nivel" => 0,
        ];
    }

    /**
     * @param \stdClass $std Objeto da linha em questão
     * @param integer $ordemResultadoApurado ordem da linha onde contém o valor
     * @param integer $ordemMeta ordem da linha onde contém o valor
     * @return \stdClass
     */
    protected function calculaSimplificado($std, $ordemResultadoApurado, $ordemMeta)
    {
        $std->resultado_apurado_ate_bimestre = $this->linhas[$ordemResultadoApurado]->valor;
        $std->meta_fixada_anexo_metas_fiscais = $this->linhas[$ordemMeta]->valor;

        if (!empty($std->meta_fixada_anexo_metas_fiscais)) {
            $std->relacao_meta = round(
                ($std->resultado_apurado_ate_bimestre / $std->meta_fixada_anexo_metas_fiscais) * 100,
                2
            );
        }
        return $std;
    }

    /**
     * @return void
     * @throws \ParameterException
     * @throws Exception
     */
    protected function addParserVariaveisFixas()
    {
        $this->parser->setVariavel('valor_manual1', $this->valorManual1);
        $this->parser->setVariavel('valor_manual2', $this->valorManual2);
        $this->parser->setVariavel('vlr_previsao_atualizada', $this->valorPrevisaoAtualizada);
        $this->parser->setVariavel('vlr_saldo_anterior_acumulado', $this->valorSaldoFinalAcumulado);
        $this->parser->setVariavel('vlr_total_creditos', $this->valorSaldoInicial);

        $mesesPeriodo = sprintf(
            '%s - %s',
            DBDate::getMesExtenso($this->periodo->getMesInicial()),
            DBDate::getMesExtenso($this->periodo->getMesFinal())
        );

        $this->parser->setEnteFederativo($this->enteFederativo);
        $this->parser->setEmissor($this->emissor);
        $this->parser->setPeriodo($this->periodo->getDescricao());
        $this->parser->setExercicio($this->exercicio);
        $this->parser->setExercicioAnterior($this->exercicio - 1);
        $this->parser->setMesesPeriodo($mesesPeriodo);
        $this->parser->setNotaExplicativa($this->getNotaExplicativa());
        $this->parser->setNomePrefeito($this->assinatura->assinaturaPrefeito());
        $this->parser->setNomeContador($this->assinatura->assinaturaContador());
        $this->parser->setNomeOrdenador($this->assinatura->assinaturaSecretarioFazenda());
    }
    
    public function getLinhasProcessadas()
    {
        $this->processar();
        return $this->linhas;
    }
}
