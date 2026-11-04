<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF\RREO\AnexoUm;

use App\Domain\Financeiro\Contabilidade\Builder\RegraCalculoLinhaLrfBuilder;
use App\Domain\Financeiro\Contabilidade\Relatorios\LRF\RREO\XlsAnexoUm;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF\AnexosService;
use Exception;
use NcJoes\OfficeConverter\OfficeConverter;
use PhpOffice\PhpWord\Settings;
use stdClass;

class Versao280Service extends AnexosService
{
    /**
     * Ordem das linhas da receita
     * @var array
     */
    protected $linhasReceitas = [];
    /**
     * Ordem das linhas da despesa
     * @var array
     */
    private $linhasDespesas = [];

    protected $sections = [
        'receita_1' => [1, 78],
        'despesa_1' => [79, 103],
        'receita_2' => [104, 166],
        'despesa_2' => [167, 176],
    ];

    public function emitir()
    {
        $this->processar();
        foreach ($this->linhasOrganizadas as $section => $linhas) {
            $this->parser->addCollection($section, $linhas);
        }

        $this->parser->setEnteFederativo($this->enteFederativo);
        $this->parser->setEmissor($this->emissor);
        $this->parser->setPeriodo($this->periodo->getDescricao());
        $this->parser->setMesesPeriodo($this->mesesProcessadosPeriodo());

        $this->parser->setNotaExplicativa($this->getNotaExplicativa());
        $this->parser->setNomePrefeito($this->assinatura->assinaturaPrefeito());
        $this->parser->setNomeContador($this->assinatura->assinaturaContador());
        $this->parser->setNomeOrdenador($this->assinatura->assinaturaSecretarioFazenda());

        $filename = $this->parser->gerar();
        $filePdf = basename($filename, ".xlsx") . ".pdf";
        $pathPdf = "tmp/{$filePdf}";

        $converter = new OfficeConverter($filename, 'tmp');
        $converter->convertTo($filePdf);

        return [
            'xls' => $filename,
            'xlsLinkExterno' => url($filename),
            'pdf' => $pathPdf,
            'pdfLinkExterno' => url($pathPdf)
        ];
    }

    public function getNome()
    {
        return 'RREO - Anexo 1';
    }

    protected function processar()
    {
        $this->mapeiaLinhas();
        $this->processarFiltros();
        $this->calcularReceitas();
        $this->calcularDespesas();
        $this->alteraSinalContas();
        $this->calculaLinhasManuais();
        $this->totalizaColunasReceitas();
        $this->totalizaColunasDespesa();
        $this->totalizarSomaLinhas();
        $this->calculaPercentualReceitas();
        $this->calcularDefict();
        $this->calcularSuperavit();
        $this->calculaTotalComDeficit();
        $this->calculaTotalComSuperavit();
        $this->limpaValoresColunas();

        $this->organizaLinhas();
    }

    /**
     * @param stdClass $linha
     * @param RegraCalculoLinhaLrfBuilder $regra
     * @param $dadosMsc
     * @return void
     * @throws Exception
     */
    public function calculaColunasReceita(stdClass $linha, RegraCalculoLinhaLrfBuilder $regra, $dadosMsc)
    {
        // PREVISÃO INICIAL
        $this->calculaColunaStrPos(
            $linha,
            $regra->addPcasp(['5211'])->build(),
            $dadosMsc,
            'valor_inicial',
            self::CALCULAR_SALDO_FINAL
        );

        // PREVISÃO ATUALIZADA (a)
        $this->calculaColunaStrPos(
            $linha,
            $regra->addPcasp(['5211', '5212'])->build(),
            $dadosMsc,
            'previsao_atualizada',
            self::CALCULAR_SALDO_FINAL
        );

        // RECEITAS REALIZADAS No Bimestre (b)
        $this->calculaColunaStrPos(
            $linha,
            $regra->addPcasp(['6212', '6213'])->build(),
            $dadosMsc,
            'arrecadado_periodo',
            self::CALCULAR_PERIODO
        );

        // RECEITAS REALIZADAS Até o Bimestre (c)
        $this->calculaColunaStrPos(
            $linha,
            $regra->addPcasp(['6212', '6213'])->build(),
            $dadosMsc,
            'arrecadado_acumulado',
            self::CALCULAR_SALDO_FINAL
        );
    }

    /**
     * // realiza os cálculos das colunas
     * @return void
     */
    protected function totalizaColunasReceitas()
    {
        foreach ($this->linhas as $linha) {
            if ($linha->totalizadora || !in_array($linha->ordem, $this->linhasReceitas)) {
                continue;
            }

            $prevAtu = $linha->previsao_atualizada;
            if ($prevAtu != 0) {
                $linha->percentual_no_bimestre = $this->calculaPercentual($linha->arrecadado_periodo, $prevAtu);
                $linha->percentual_acumulado = $this->calculaPercentual($linha->arrecadado_acumulado, $prevAtu);
            }
            $linha->saldo = $prevAtu - $linha->arrecadado_acumulado;
        }
    }

    /**
     * realiza os cálculos das colunas
     * @return void
     */
    protected function totalizaColunasDespesa()
    {
        foreach ($this->linhas as $linha) {
            if ($linha->totalizadora || !in_array($linha->ordem, $this->linhasDespesas)) {
                continue;
            }

            $dotAtualizada = $linha->total_creditos;
            $linha->saldo_empenhado = $dotAtualizada - $linha->empenhado_liquido_acumulado;
            $linha->saldo_liquidado = $dotAtualizada - $linha->liquidado_acumulado;
        }
    }


    /**
     * @throws Exception
     */
    protected function carregarParserXls()
    {
        $template = $this->carregarTemplate();
        $this->parser = new XlsAnexoUm($template);
    }

    /**
     * @return void
     */
    protected function criaPropriedadesValores()
    {
        foreach ($this->linhas as $linha) {
            foreach ($linha->colunas as $coluna) {
                $linha->{$coluna->coluna} = 0;
            }
        }
    }

    /**
     * Executa o calculo dos quadros das receitas.
     * @return void
     * @throws Exception
     */
    protected function calcularReceitas()
    {
        $dadosMsc = $this->executarMsc(['5211', '5212', '6212', '6213']);

        // Linha 4 e 107 - Impostos
        $this->calcularLinhaReceita($this->linhas[4], $dadosMsc, ['111']);
        $this->calcularLinhaReceita($this->linhas[107], $dadosMsc, ['711']);
        // Linha 5 e 108 - Taxas
        $this->calcularLinhaReceita($this->linhas[5], $dadosMsc, ['112']);
        $this->calcularLinhaReceita($this->linhas[108], $dadosMsc, ['712']);
        // Linha 6 e 109 - Contribuição de Melhoria
        $this->calcularLinhaReceita($this->linhas[6], $dadosMsc, ['113']);
        $this->calcularLinhaReceita($this->linhas[109], $dadosMsc, ['713']);
        // Linha 8 e 111 - Contribuições Sociais
        $this->calcularLinhaReceita($this->linhas[8], $dadosMsc, ['121']);
        $this->calcularLinhaReceita($this->linhas[111], $dadosMsc, ['721']);
        // Linha 9 e 112 - Contribuições Econômicas
        $this->calcularLinhaReceita($this->linhas[9], $dadosMsc, ['122']);
        $this->calcularLinhaReceita($this->linhas[112], $dadosMsc, ['722']);
        // Linha 10 e 113 - Contribuições para Entidades Privadas de Serviço Social e de Formação Profissional
        $this->calcularLinhaReceita($this->linhas[10], $dadosMsc, ['123']);
        $this->calcularLinhaReceita($this->linhas[113], $dadosMsc, ['723']);
        // Linha 11 e 114 - Contribuição para o Custeio do Serviço de Iluminação Pública
        $this->calcularLinhaReceita($this->linhas[11], $dadosMsc, ['124']);
        $this->calcularLinhaReceita($this->linhas[114], $dadosMsc, ['724']);
        // Linha 13 e 116 - Exploração do Patrimônio Imobiliário do Estado
        $this->calcularLinhaReceita($this->linhas[13], $dadosMsc, ['131']);
        $this->calcularLinhaReceita($this->linhas[116], $dadosMsc, ['731']);
        // Linha 14 e 117 - Valores Mobiliários
        $this->calcularLinhaReceita($this->linhas[14], $dadosMsc, ['132']);
        $this->calcularLinhaReceita($this->linhas[117], $dadosMsc, ['732']);
        // Linha 15 e 118 - Delegação de Serviços Públicos Mediante Concessão, Permissão, Autorização ou Licença
        $this->calcularLinhaReceita($this->linhas[15], $dadosMsc, ['133']);
        $this->calcularLinhaReceita($this->linhas[118], $dadosMsc, ['733']);
        // Linha 16 e 119 - Exploração de Recursos Naturais
        $this->calcularLinhaReceita($this->linhas[16], $dadosMsc, ['134']);
        $this->calcularLinhaReceita($this->linhas[119], $dadosMsc, ['734']);
        // Linha 17 e 120 - Exploração do Patrimônio Intangível
        $this->calcularLinhaReceita($this->linhas[17], $dadosMsc, ['135']);
        $this->calcularLinhaReceita($this->linhas[120], $dadosMsc, ['735']);
        // Linha 18 e 121 - Cessão de Direitos
        $this->calcularLinhaReceita($this->linhas[18], $dadosMsc, ['136']);
        $this->calcularLinhaReceita($this->linhas[121], $dadosMsc, ['736']);
        // Linha 19 e 122 - Demais Receitas Patrimoniais
        $this->calcularLinhaReceita($this->linhas[19], $dadosMsc, ['139']);
        $this->calcularLinhaReceita($this->linhas[122], $dadosMsc, ['739']);

        // Linha 20 e 123 - RECEITA AGROPECUÁRIA
        $this->calcularLinhaReceita($this->linhas[20], $dadosMsc, ['14']);
        $this->calcularLinhaReceita($this->linhas[123], $dadosMsc, ['74']);
        // Linha 21 e 124 - RECEITA INDUSTRIAL
        $this->calcularLinhaReceita($this->linhas[21], $dadosMsc, ['15']);
        $this->calcularLinhaReceita($this->linhas[124], $dadosMsc, ['75']);

        // Linha 23 e 126 - Serviços Administrativos e Comerciais Gerais
        $this->calcularLinhaReceita($this->linhas[23], $dadosMsc, ['161']);
        $this->calcularLinhaReceita($this->linhas[126], $dadosMsc, ['761']);
        // Linha 24 e 127 - Serviços e Atividades Referentes à Navegação e ao Transporte
        $this->calcularLinhaReceita($this->linhas[24], $dadosMsc, ['162']);
        $this->calcularLinhaReceita($this->linhas[127], $dadosMsc, ['762']);
        // Linha 25 e 128 - Serviços e Atividades referentes à Saúde
        $this->calcularLinhaReceita($this->linhas[25], $dadosMsc, ['163']);
        $this->calcularLinhaReceita($this->linhas[128], $dadosMsc, ['763']);
        // Linha 26 e 129 - Serviços e Atividades Financeiras
        $this->calcularLinhaReceita($this->linhas[26], $dadosMsc, ['164']);
        $this->calcularLinhaReceita($this->linhas[129], $dadosMsc, ['764']);
        // Linha 27 e 130 - Outros Serviços
        $this->calcularLinhaReceita($this->linhas[27], $dadosMsc, ['169']);
        $this->calcularLinhaReceita($this->linhas[130], $dadosMsc, ['769']);

        // Linha 29 e 132 - TRANSFERÊNCIAS CORRENTES - Transferências da União e de suas Entidades
        $this->calcularLinhaReceita($this->linhas[29], $dadosMsc, ['171']);
        $this->calcularLinhaReceita($this->linhas[132], $dadosMsc, ['771']);
        // Linha 30 e 133 - TRANSFERÊNCIAS CORRENTES - Transferências dos Estados e do DF e de suas Entidades
        $this->calcularLinhaReceita($this->linhas[30], $dadosMsc, ['172']);
        $this->calcularLinhaReceita($this->linhas[133], $dadosMsc, ['772']);
        // Linha 31 e 134 - TRANSFERÊNCIAS CORRENTES - Transferências dos Municípios e de suas Entidades
        $this->calcularLinhaReceita($this->linhas[31], $dadosMsc, ['173']);
        $this->calcularLinhaReceita($this->linhas[134], $dadosMsc, ['773']);
        // Linha 32 e 135 - TRANSFERÊNCIAS CORRENTES - Transferências de Instituições Privadas
        $this->calcularLinhaReceita($this->linhas[32], $dadosMsc, ['174']);
        $this->calcularLinhaReceita($this->linhas[135], $dadosMsc, ['774']);
        // Linha 33 e 136 - TRANSFERÊNCIAS CORRENTES - Transferências de Outras Instituições Públicas
        $this->calcularLinhaReceita($this->linhas[33], $dadosMsc, ['175']);
        $this->calcularLinhaReceita($this->linhas[136], $dadosMsc, ['775']);
        // Linha 34 e 137 - TRANSFERÊNCIAS CORRENTES - Transferências do Exterior
        $this->calcularLinhaReceita($this->linhas[34], $dadosMsc, ['176']);
        $this->calcularLinhaReceita($this->linhas[137], $dadosMsc, ['776']);
        // Linha 35 e 138 - TRANSFERÊNCIAS CORRENTES - Demais Transferências Correntes
        $this->calcularLinhaReceita($this->linhas[35], $dadosMsc, ['179']);
        $this->calcularLinhaReceita($this->linhas[138], $dadosMsc, ['779']);

        // Linha 37 e 140 - Multas Administrativas, Contratuais e Judiciais
        $this->calcularLinhaReceita($this->linhas[37], $dadosMsc, ['191']);
        $this->calcularLinhaReceita($this->linhas[140], $dadosMsc, ['791']);
        // Linha 38 e 141 - Indenizações, Restituições e Ressarcimentos
        $this->calcularLinhaReceita($this->linhas[38], $dadosMsc, ['192']);
        $this->calcularLinhaReceita($this->linhas[141], $dadosMsc, ['792']);
        // Linha 39 e 142 - Bens, Direitos e Valores Incorporados ao Patrimônio Público
        $this->calcularLinhaReceita($this->linhas[39], $dadosMsc, ['193']);
        $this->calcularLinhaReceita($this->linhas[142], $dadosMsc, ['793']);
        // Linha 40 e 143 - Multas e Juros de Mora das Receitas de Capital
        $this->calcularLinhaReceita($this->linhas[40], $dadosMsc, ['194']);
        $this->calcularLinhaReceita($this->linhas[143], $dadosMsc, ['794']);
        // Linha 41 e 144 - Demais Receitas Correntes
        $this->calcularLinhaReceita($this->linhas[41], $dadosMsc, ['199']);
        $this->calcularLinhaReceita($this->linhas[144], $dadosMsc, ['799']);

        // Linha 44 e 147 - Operações de Crédito - Mercado Interno
        $this->calcularLinhaReceita($this->linhas[44], $dadosMsc, ['211'], ['211102', '211255']);
        $this->calcularLinhaReceita($this->linhas[147], $dadosMsc, ['811'], ['811102', '811255']);
        // Linha 45 e 148 - Operações de Crédito - Mercado Externo
        $this->calcularLinhaReceita($this->linhas[45], $dadosMsc, ['212'], ['212102', '212255']);
        $this->calcularLinhaReceita($this->linhas[148], $dadosMsc, ['812'], ['812102', '812255']);
        // Linha 47 e 150 - Alienação de Bens Móveis
        $this->calcularLinhaReceita($this->linhas[47], $dadosMsc, ['221']);
        $this->calcularLinhaReceita($this->linhas[150], $dadosMsc, ['821']);
        // Linha 48 e 151 - Alienação de Bens Imóveis
        $this->calcularLinhaReceita($this->linhas[48], $dadosMsc, ['222']);
        $this->calcularLinhaReceita($this->linhas[151], $dadosMsc, ['822']);
        // Linha 49 e 152 - Alienação de Bens Intangíveis
        $this->calcularLinhaReceita($this->linhas[49], $dadosMsc, ['223']);
        $this->calcularLinhaReceita($this->linhas[152], $dadosMsc, ['823']);
        // Linha 50 e 153 - AMORTIZAÇÕES DE EMPRÉSTIMOS
        $this->calcularLinhaReceita($this->linhas[50], $dadosMsc, ['23']);
        $this->calcularLinhaReceita($this->linhas[153], $dadosMsc, ['83']);

        // Linha 52 e 155 - TRANSFERÊNCIAS DE CAPITAL - Transferências da União e de suas Entidades
        $this->calcularLinhaReceita($this->linhas[52], $dadosMsc, ['241']);
        $this->calcularLinhaReceita($this->linhas[155], $dadosMsc, ['841']);
        // Linha 53 e 156 - TRANSFERÊNCIAS DE CAPITAL - Transferências dos Estados e do DF e de suas Entidades
        $this->calcularLinhaReceita($this->linhas[53], $dadosMsc, ['242']);
        $this->calcularLinhaReceita($this->linhas[156], $dadosMsc, ['842']);
        // Linha 54 e 157 - TRANSFERÊNCIAS DE CAPITAL - Transferências dos Municípios e de suas Entidades
        $this->calcularLinhaReceita($this->linhas[54], $dadosMsc, ['243']);
        $this->calcularLinhaReceita($this->linhas[157], $dadosMsc, ['843']);
        // Linha 55 e 158 - TRANSFERÊNCIAS DE CAPITAL - Transferências de Instituições Privadas
        $this->calcularLinhaReceita($this->linhas[55], $dadosMsc, ['244']);
        $this->calcularLinhaReceita($this->linhas[158], $dadosMsc, ['844']);
        // Linha 56 e 159 - TRANSFERÊNCIAS DE CAPITAL - Transferências de Outras Instituições Públicas
        $this->calcularLinhaReceita($this->linhas[56], $dadosMsc, ['245']);
        $this->calcularLinhaReceita($this->linhas[159], $dadosMsc, ['845']);
        // Linha 57 e 160 - TRANSFERÊNCIAS DE CAPITAL - Transferências do Exterior
        $this->calcularLinhaReceita($this->linhas[57], $dadosMsc, ['246']);
        $this->calcularLinhaReceita($this->linhas[160], $dadosMsc, ['846']);
        // Linha 58 e 161 - TRANSFERÊNCIAS DE CAPITAL - Demais Transferências de Capital
        $this->calcularLinhaReceita($this->linhas[58], $dadosMsc, ['249']);
        $this->calcularLinhaReceita($this->linhas[161], $dadosMsc, ['849']);

        // Linha 60 e 163 - Integralização do Capital Social
        $this->calcularLinhaReceita($this->linhas[60], $dadosMsc, ['291']);
        $this->calcularLinhaReceita($this->linhas[163], $dadosMsc, ['891']);
        // Linha 61 e 164 - Remuneração das Disponibilidades do Tesouro
        $this->calcularLinhaReceita($this->linhas[61], $dadosMsc, ['293']);
        $this->calcularLinhaReceita($this->linhas[164], $dadosMsc, ['893']);
        // Linha 62 e 165 - Resgate de Títulos do Tesouro
        $this->calcularLinhaReceita($this->linhas[62], $dadosMsc, ['294']);
        $this->calcularLinhaReceita($this->linhas[165], $dadosMsc, ['894']);
        // Linha 63 e 166 - Demais Receitas de Capital
        $this->calcularLinhaReceita($this->linhas[63], $dadosMsc, ['299']);
        $this->calcularLinhaReceita($this->linhas[166], $dadosMsc, ['899']);

        // Linha 68 - Operações de Crédito - Mercado Interno - Mobiliária
        $this->calcularLinhaReceita($this->linhas[68], $dadosMsc, ['211102', '811102']);
        // Linha 69 - Operações de Crédito - Mercado Interno - Contratual
        $this->calcularLinhaReceita($this->linhas[69], $dadosMsc, ['211255', '811255']);
        // Linha 71 - Operações de Crédito - Mercado Externo - Mobiliária
        $this->calcularLinhaReceita($this->linhas[71], $dadosMsc, ['212102', '812102']);
        // Linha 72 - Operações de Crédito - Mercado Externo - Contratual
        $this->calcularLinhaReceita($this->linhas[72], $dadosMsc, ['212255', '812255']);
        // Linha 77 - Recursos Arrecadados em Exercícios Anteriores - RPPS
        $this->calcularLinhaReceita($this->linhas[77], $dadosMsc, ['999']);

        // Linha 78 - Superávit Financeiro Utilizado para Créditos Adicionais
        $dadosMsc78 = $this->executarMsc(['5221301']);
        $this->calcularLinha78($this->linhas[78], $dadosMsc78);
    }

    /**
     * Pega do mapeamento das seções do relatório
     * @return void
     */
    protected function mapeiaLinhas()
    {
        $rec1 = $this->sections['receita_1'];
        $rec2 = $this->sections['receita_2'];
        $this->linhasReceitas = array_merge(range($rec1[0], $rec1[1]), range($rec2[0], $rec2[1]));

        $desp1 = $this->sections['despesa_1'];
        $desp2 = $this->sections['despesa_2'];
        $this->linhasDespesas = array_merge(range($desp1[0], $desp1[1]), range($desp2[0], $desp2[1]));
    }

    /**
     * O cálculo é realizado da seguinte forma: ($valor1 / $valor2) * 100.
     * O valor retornado tem a precisão de 2 digitos
     *
     * @param $valor1
     * @param $valor2
     * @return float
     */
    protected function calculaPercentual($valor1, $valor2)
    {
        return round(($valor1 / $valor2) * 100, 2);
    }

    protected function calcularDespesas()
    {
        $dadosMsc = $this->executarMsc(['52211', '52212', '52219', '62213']);

        // Linha 81 e 169 - PESSOAL E ENCARGOS SOCIAIS
        $this->calcularLinhaDespesa($this->linhas[81], $dadosMsc, ['31'], ['3191']);
        $this->calcularLinhaDespesa($this->linhas[169], $dadosMsc, ['3191']);

        // Linha 82 e 170 - JUROS E ENCARGOS DA DÍVIDA
        $this->calcularLinhaDespesa($this->linhas[82], $dadosMsc, ['32'], ['3291']);
        $this->calcularLinhaDespesa($this->linhas[170], $dadosMsc, ['3291']);

        // Linha 171 - OUTRAS DESPESAS CORRENTES
        $this->calcularLinhaDespesa($this->linhas[171], $dadosMsc, ['3391']);

        // Linha 84 - Transferências a Municípios2
        $this->calcularLinhaDespesa($this->linhas[84], $dadosMsc, ['334081']);
        // Linha 85 - Demais Despesas Correntes²
        $this->calcularLinhaDespesa($this->linhas[85], $dadosMsc, ['33'], ['3391', '334081']);

        // Linha 87 e 173 - INVESTIMENTOS
        $this->calcularLinhaDespesa($this->linhas[87], $dadosMsc, ['44'], ['4491']);
        $this->calcularLinhaDespesa($this->linhas[173], $dadosMsc, ['4491']);

        // Linha 88 - 174 - INVERSÕES FINANCEIRAS
        $this->calcularLinhaDespesa($this->linhas[88], $dadosMsc, ['45'], ['4591']);
        $this->calcularLinhaDespesa($this->linhas[174], $dadosMsc, ['4591']);

        // Linha 89 - 175 - AMORTIZAÇÃO DA DÍVIDA
        $this->calcularAmortizacaoDivida($dadosMsc);

        // Linha 90 e 176 - RESERVA DE CONTINGÊNCIA
        // 176 é 100% manual
        $this->calcularReservaContigencia($dadosMsc);

        // Linha 95 - Amortização da Dívida Interna - Dívida Mobiliária
        $this->calcularDivida($this->linhas[95], $dadosMsc, ['46__76'], ['4691'], ['28841', '28843']);
        // Linha 96 - Amortização da Dívida Interna - Dívida Contratual
        $this->calcularDivida($this->linhas[96], $dadosMsc, ['46__77'], ['4691'], ['28841', '28843']);
        // Linha 98 -Amortização da Dívida Externa - Dívida Mobiliária
        $this->calcularDivida($this->linhas[98], $dadosMsc, ['46__76'], ['4691'], ['28842', '28844']);
        // Linha 99 -Amortização da Dívida Externa - Dívida Contratual
        $this->calcularDivida($this->linhas[99], $dadosMsc, ['46__77'], ['4691'], ['28842', '28844']);
        // Linha 103 - RESERVA DO RPPS
        $this->calcularReservaRpps($dadosMsc);
    }

    /**
     * @param stdClass $linha
     * @param array $dadosMsc
     * @param array $contasSomar
     * @param $contasDeduzir
     * @return void
     * @throws Exception
     */
    protected function calcularLinhaReceita(stdClass $linha, array $dadosMsc, array $contasSomar, $contasDeduzir = [])
    {
        $regra = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->addContas($contasSomar);
        if (count($contasDeduzir)) {
            $regra->addContas($contasDeduzir, true);
        }
        $this->calculaColunasReceita($linha, $regra, $dadosMsc);
    }

    protected function calcularLinhaDespesa(stdClass $linha, array $dadosMsc, array $contasSomar, $contasDeduzir = [])
    {
        $regra = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contasSomar);
        if (count($contasDeduzir)) {
            $regra->addContas($contasDeduzir, true);
        }
        $this->calculaColunasDespesa($linha, $regra, $dadosMsc);
    }

    protected function calculaColunasDespesa(stdClass $linha, RegraCalculoLinhaLrfBuilder $regra, array $dadosMsc)
    {
        // DOTAÇÃO INICIAL (d)
        $this->calculaColunaStrPos(
            $linha,
            $regra->addPcasp(['52211'])->build(),
            $dadosMsc,
            'saldo_inicial',
            self::CALCULAR_SALDO_FINAL
        );

        // DOTAÇÃO ATUALIZADA (e)
        $this->calculaColunaStrPos(
            $linha,
            $regra->addPcasp(['52211', '52212', '52219'])->build(),
            $dadosMsc,
            'total_creditos',
            self::CALCULAR_SALDO_FINAL
        );

        // DESPESAS EMPENHADAS - No Bimestre
        $this->calculaColunaStrPos(
            $linha,
            $regra->addPcasp(['62213'])->build(),
            $dadosMsc,
            'empenhado_liquido',
            self::CALCULAR_PERIODO
        );

        // DESPESAS EMPENHADAS - Até o Bimestre (f)
        $this->calculaColunaStrPos(
            $linha,
            $regra->addPcasp(['62213'])->build(),
            $dadosMsc,
            'empenhado_liquido_acumulado',
            self::CALCULAR_SALDO_FINAL
        );

        // DESPESAS LIQUIDADAS - No Bimestre
        $this->calculaColunaStrPos(
            $linha,
            $regra->addPcasp(['6221303', '6221304', '6221307'])->build(),
            $dadosMsc,
            'liquidado',
            self::CALCULAR_PERIODO
        );
        // DESPESAS LIQUIDADAS - Até o Bimestre (f)
        $this->calculaColunaStrPos(
            $linha,
            $regra->addPcasp(['6221303', '6221304', '6221307'])->build(),
            $dadosMsc,
            'liquidado_acumulado',
            self::CALCULAR_SALDO_FINAL
        );

        // DESPESAS PAGAS ATÉ O BIMESTRE (j)
        $this->calculaColunaStrPos(
            $linha,
            $regra->addPcasp(['6221304'])->build(),
            $dadosMsc,
            'pago_acumulado',
            self::CALCULAR_SALDO_FINAL
        );

        if ((int)$this->periodo->getCodigo() === 11) {
            // INSCRITAS EM RESTOS A PAGAR NÃO PROCESSADOS (k)
            $this->calculaColunaStrPos(
                $linha,
                $regra->addPcasp(['6221305', '6221306'])->build(),
                $dadosMsc,
                'a_liquidar',
                self::CALCULAR_SALDO_FINAL
            );
        }
    }

    /**
     * Linha 90 - RESERVA DE CONTINGÊNCIA
     * @param array $dadosMsc
     * @return void
     * @throws Exception
     */
    protected function calcularReservaContigencia(array $dadosMsc)
    {
        $linha = $this->linhas[90];
        $regra = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas(['99999999'])
            ->addFuncaoSubfuncao(['99999']);

        // DOTAÇÃO INICIAL (d)
        $this->calculaColunaStrPos(
            $linha,
            $regra->addPcasp(['52211'])->build(),
            $dadosMsc,
            'saldo_inicial',
            self::CALCULAR_SALDO_FINAL
        );

        // DOTAÇÃO ATUALIZADA (e)
        $this->calculaColunaStrPos(
            $linha,
            $regra->addPcasp(['52211', '52212', '52219'])->build(),
            $dadosMsc,
            'total_creditos',
            self::CALCULAR_SALDO_FINAL
        );
    }

    /**
     * Linha 103 - RESERVA DO RPPS
     * @param array $dadosMsc
     * @return void
     * @throws Exception
     */
    protected function calcularReservaRpps(array $dadosMsc)
    {
        $linha = $this->linhas[103];
        $regra = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas(['99999999'])
            ->addFuncaoSubfuncao(['99997']);

        // DOTAÇÃO INICIAL (d)
        $this->calculaColunaStrPos(
            $linha,
            $regra->addPcasp(['52211'])->build(),
            $dadosMsc,
            'saldo_inicial',
            self::CALCULAR_SALDO_FINAL
        );

        // DOTAÇÃO ATUALIZADA (e)
        $this->calculaColunaStrPos(
            $linha,
            $regra->addPcasp(['52211', '52212', '52219'])->build(),
            $dadosMsc,
            'total_creditos',
            self::CALCULAR_SALDO_FINAL
        );
    }

    /**
     * Linha: 89
     *  - Fórmula: ND: 4.6.00.00.00 (MOD.= EXCETO 91) (-) [4.6.XX.76; (-) 4.6.XX.77; FS = 841,842,843,844,846]
     * Linha: 175
     *  - Fórmula: ND: 4.6.91
     * @param array $dadosMsc
     * @return void
     * @throws Exception
     */
    protected function calcularAmortizacaoDivida(array $dadosMsc)
    {
        // Linha 175 é INTRA-ORÇAMENTÁRIAS
        $this->calcularLinhaDespesa($this->linhas[175], $dadosMsc, ['4691']);

        $linha = $this->linhas[89];
        $linhaP1 = clone $linha;
        $this->calcularLinhaDespesa($linhaP1, $dadosMsc, ['46'], ['4691']);

        $linhaP2 = clone $linha;
        $regra = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($this->getPlanoDespesa($this->exercicio, ['46__76', '46__77']))
            ->addFuncaoSubfuncao(['28841', '28842', '28843', '28844', '28846']);
        $this->calculaColunasDespesa($linhaP2, $regra, $dadosMsc);

        foreach ($linha->colunas as $std) {
            $linha->{$std->coluna} = $linhaP1->{$std->coluna} - $linhaP2->{$std->coluna};
        }
    }

    /**
     * Realiza o cálculo da Amortização da Dívida
     * @param stdClass $linha
     * @param array $dadosMsc
     * @param array $contas
     * @param array $contasExclusao
     * @param array $fs
     * @return void
     * @throws Exception
     */
    protected function calcularDivida(stdClass $linha, array $dadosMsc, array $contas, array $contasExclusao, array $fs)
    {
        $regra = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($this->getPlanoDespesa($this->exercicio, $contas))
            ->addContas($contasExclusao, true)
            ->addFuncaoSubfuncao($fs);

        $this->calculaColunasDespesa($linha, $regra, $dadosMsc);
    }

    /**
     * Realiza o cálculo do Déficit
     * A Regra é:
     * Nos cinco primeiros bimestres quando (A) for menor que (E), então: B = E - A.
     * No último bimestre quando (A) for menor que (C), então: B = C - A.
     *
     * Onde:
     *  - A = Linha 73 - TOTAL DAS RECEITAS (V) = (III + IV)
     *  - B = Linha 74 - DÉFICIT (VI)¹
     *  - C = Linha 100 - TOTAL DAS DESPESAS (XII) = (X + XI) | coluna: empenhado_liquido_acumulado
     *  - E = Linha 100 - TOTAL DAS DESPESAS (XII) = (X + XI) | coluna: liquidado_acumulado
     *
     * @return void
     */
    protected function calcularDefict()
    {
        $linhaB = $this->linhas[74];
        foreach ($linhaB->colunas as $std) {
            $linhaB->{$std->coluna} = null;
        }
        $a = $this->linhas[73]->arrecadado_acumulado;
        $c = $this->linhas[100]->empenhado_liquido_acumulado;
        $e = $this->linhas[100]->liquidado_acumulado;

        if (in_array($this->periodo->getCodigo(), [6, 7, 8, 9, 10])) {
            if ($a < $e) {
                $linhaB->arrecadado_acumulado = $e - $a;
            }
        }

        if ((int)$this->periodo->getCodigo() === 11) {
            if ($a < $c) {
                $linhaB->arrecadado_acumulado = $c - $a;
            }
        }
    }

    /**
     * Realiza o cálculo do Superávit
     * A Regra é:
     * No controle do superávit pelas despesas empenhadas quando (A) for maior que (C), então: D = A - C.
     * No controle do superávit pelas despesas liquidadas quando (A) for maior que (E), então: F = A - E.
     * No controle do superávit pelas despesas pagas quando (A) for maior que (G), então: H = A - G.
     *
     * Onde:
     *  - A = Linha 73 - TOTAL DAS RECEITAS (V) = (III + IV)
     *  - D = Linha 101 - SUPERÁVIT (XIII)¹
     *  - C = Linha 100 - TOTAL DAS DESPESAS (XII) = (X + XI) | coluna: empenhado_liquido_acumulado
     *  - E = Linha 100 - TOTAL DAS DESPESAS (XII) = (X + XI) | coluna: liquidado_acumulado
     *  - G = Linha 100 - TOTAL DAS DESPESAS (XII) = (X + XI) | coluna: pago_acumulado
     *
     * @return void
     */
    protected function calcularSuperavit()
    {
        $linhaD = $this->linhas[101];
        foreach ($linhaD->colunas as $std) {
            $linhaD->{$std->coluna} = null;
        }
        $a = $this->linhas[73]->arrecadado_acumulado;
        $c = $this->linhas[100]->empenhado_liquido_acumulado;
        $e = $this->linhas[100]->liquidado_acumulado;
        $g = $this->linhas[100]->pago_acumulado;

        if ($a > $c) {
            $linhaD->empenhado_liquido_acumulado = $a - $c;
        }

        if ($a > $e) {
            $linhaD->liquidado_acumulado = $a - $e;
        }

        if ($a > $g) {
            $linhaD->pago_acumulado = $a - $g;
        }
    }

    /**
     * Linha 75 - TOTAL COM DÉFICIT (VII) = (V + VI)
     * @return void
     */
    protected function calculaTotalComDeficit()
    {
        $v = $this->linhas[73]; // TOTAL DAS RECEITAS (V) = (III + IV)
        $vi = $this->linhas[74]; // DÉFICIT (VI)

        foreach ($this->linhas[75]->colunas as $std) {
            $this->linhas[75]->{$std->coluna} = $v->{$std->coluna} + $vi->{$std->coluna};
        }
        $this->linhas[75]->saldo = null;
    }

    /**
     * Linha 102 - TOTAL COM SUPERÁVIT (XIV) = (XII + XIII)
     * @return void
     */
    protected function calculaTotalComSuperavit()
    {
        $v = $this->linhas[100]; // TOTAL DAS DESPESAS (XII) = (X + XI)
        $vi = $this->linhas[101]; // SUPERÁVIT (XIII)

        foreach ($this->linhas[102]->colunas as $std) {
            $this->linhas[102]->{$std->coluna} = $v->{$std->coluna} + $vi->{$std->coluna};
        }
    }


    protected function calculaPercentualReceitas()
    {
        foreach ($this->linhas as $linha) {
            if (!in_array($linha->ordem, $this->linhasReceitas)) {
                continue;
            }

            $prevAtu = $linha->previsao_atualizada;
            if ($prevAtu != 0) {
                $linha->percentual_no_bimestre = $this->calculaPercentual($linha->arrecadado_periodo, $prevAtu);
                $linha->percentual_acumulado = $this->calculaPercentual($linha->arrecadado_acumulado, $prevAtu);
            }
        }
    }

    /**
     * Aplica NULL nas colunas que não deve apresentar valores
     * @return void
     */
    protected function limpaValoresColunas()
    {
        // DÉFICIT (VI)1
        $this->linhas[74]->valor_inicial = null;
        $this->linhas[74]->previsao_atualizada = null;
        $this->linhas[74]->arrecadado_periodo = null;
        $this->linhas[74]->percentual_no_bimestre = null;
        $this->linhas[74]->percentual_acumulado = null;
        $this->linhas[74]->saldo = null;
        // SALDOS DE EXERCÍCIOS ANTERIORES
        $this->linhas[76]->arrecadado_periodo = null;
        $this->linhas[76]->percentual_no_bimestre = null;
        $this->linhas[76]->percentual_acumulado = null;
        $this->linhas[76]->saldo = null;
        // Recursos Arrecadados em Exercícios Anteriores - RPPS
        $this->linhas[77]->arrecadado_periodo = null;
        $this->linhas[77]->percentual_no_bimestre = null;
        $this->linhas[77]->percentual_acumulado = null;
        $this->linhas[77]->saldo = null;
        // Superávit Financeiro Utilizado para Créditos Adicionais
        $this->linhas[78]->valor_inicial = null;
        $this->linhas[78]->arrecadado_periodo = null;
        $this->linhas[78]->percentual_no_bimestre = null;
        $this->linhas[78]->percentual_acumulado = null;
        $this->linhas[78]->saldo = null;
        // RESERVA DE CONTINGÊNCIA
        $this->linhas[90]->empenhado_liquido = null;
        $this->linhas[90]->empenhado_liquido_acumulado = null;
        $this->linhas[90]->liquidado = null;
        $this->linhas[90]->liquidado_acumulado = null;
        $this->linhas[90]->pago_acumulado = null;
        $this->linhas[90]->a_liquidar = null;
        // SUPERÁVIT (XIII)
        $this->linhas[101]->saldo_inicial = null;
        $this->linhas[101]->total_creditos = null;
        $this->linhas[101]->empenhado_liquido = null;
        $this->linhas[101]->saldo_empenhado = null;
        $this->linhas[101]->liquidado = null;
        $this->linhas[101]->saldo_liquidado = null;
        $this->linhas[101]->a_liquidar = null;

        // RESERVA DO RPPS
        $this->linhas[103]->empenhado_liquido = null;
        $this->linhas[103]->empenhado_liquido_acumulado = null;
        $this->linhas[103]->liquidado = null;
        $this->linhas[103]->liquidado_acumulado = null;
        $this->linhas[103]->a_liquidar = null;
        $this->linhas[103]->pago_acumulado = null;
    }

    /**
     * Linha 78 - Superávit Financeiro Utilizado para Créditos Adicionais
     * @param stdClass $linha
     * @param array $dadosMsc78
     * @return void
     * @throws Exception
     */
    protected function calcularLinha78(stdClass $linha, array $dadosMsc78)
    {
        $regra = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->build();

        // PREVISÃO ATUALIZADA (a)
        $this->calculaColunaStrPos(
            $linha,
            $regra,
            $dadosMsc78,
            'previsao_atualizada',
            self::CALCULAR_SALDO_FINAL
        );
        // RECEITAS REALIZADAS Até o Bimestre (c)
        $this->calculaColunaStrPos(
            $linha,
            $regra,
            $dadosMsc78,
            'arrecadado_acumulado',
            self::CALCULAR_SALDO_FINAL
        );
    }
}
