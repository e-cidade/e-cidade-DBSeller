<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoUm;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Contabilidade\Factories\TemplateFactory;
use App\Domain\Financeiro\Contabilidade\Relatorios\LRF\RREO\XlsAnexoUm;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\AnexosService;
use PhpOffice\PhpWord\Settings;
use NcJoes\OfficeConverter\OfficeConverter;
use DBDate;
use stdClass;

class AnexoUmService extends AnexosService
{
    protected $sections = [
        'receita_1' => [1, 79],
        'receita_2' => [105, 168],
        'despesa_1' => [80, 104],
        'despesa_2' => [169, 178],
    ];

    protected $colunasVerificacao = [
        "saldo_final_acumulado" => "arrecadado_acumulado",
    ];

    /**
     * Como o relatório usa as linhas do quadro de baixo nos quadros de cima, inverti o cálculo dos totalizadores
     * Na configuração que fiz abaixo, totalizador está somando dados de outros totalizadores.
     * Fiz isso pq as somas são em cima de muitas linhas.
     * @var array
     */
    protected $totalizarSoma = [
        // despesas 2 (intra)
        170 => [171, 172, 173],
        174 => [175, 176, 177],
        169 => [171, 172, 173, 175, 176, 177, 178],

        // receitas 2 (intra orçamentárias)
        107 => [108, 109, 110],
        111 => [112, 113, 114, 115],
        116 => [117, 118, 119, 120, 121, 122, 123],
        126 => [127, 128, 129, 130, 131],
        132 => [133, 134, 135, 136, 137, 138, 139],
        140 => [141, 142, 143, 144, 145],
        106 => [108, 109, 110, 112, 113, 114, 115, 117, 118, 119, 120, 121, 122, 123, 124, 125,
            127, 128, 129, 130, 131, 133, 134, 135, 136, 137, 138, 139, 141, 142, 143, 144, 145
        ],
        147 => [148, 149],
        150 => [151, 152, 153],
        155 => [156, 157, 158, 159, 160, 161, 162, 163],
        164 => [165, 166, 167, 168],
        146 => [148, 149, 151, 152, 153, 154, 156, 157, 158, 159, 160, 161, 162, 163, 165, 166, 167, 168],

        105 => [108, 109, 110, 112, 113, 114, 115, 117, 118, 119, 120, 121, 122, 123, 124, 125, 127, 128, 129, 130,
            131, 133, 134, 135, 136, 137, 138, 139, 141, 142, 143, 144, 145,
            148, 149, 151, 152, 153, 154, 156, 157, 158, 159, 160, 161, 162, 163, 165, 166, 167, 168
        ],


        // receitas 1 NÃO (intra orçamentárias)
        3 => [4, 5, 6],
        7 => [8, 9, 10, 11],
        12 => [13, 14, 15, 16, 17, 18, 19],
        22 => [23, 24, 25, 26, 27],
        28 => [29, 30, 31, 32, 33, 34, 35],
        36 => [37, 38, 39, 40, 41],
        2 => [4, 5, 6, 8, 9, 10, 11, 13, 14, 15, 16, 17, 18, 19, 20, 21, 23, 24, 25, 26, 27, 29, 30, 31, 32, 33, 34,
            35, 37, 38, 39, 40, 41,
        ],
        43 => [44, 45],
        46 => [47, 48, 49],
        51 => [52, 53, 54, 55, 56, 57, 58, 59],
        60 => [61, 62, 63, 64],
        42 => [44, 45, 47, 48, 49, 50, 52, 53, 54, 55, 56, 57, 58, 59, 61, 62, 63, 64],
        1 => [4, 5, 6, 8, 9, 10, 11, 13, 14, 15, 16, 17, 18, 19, 20, 21, 23, 24, 25, 26, 27, 29, 30, 31, 32, 33, 34,
            35, 37, 38, 39, 40, 41, 44, 45, 47, 48, 49, 50, 52, 53, 54, 55, 56, 57, 58, 59, 61, 62, 63, 64
        ],

        65 => [105], // linha 65 é igual a linha 105 RECEITAS (INTRA-ORÇAMENTÁRIAS) (II)
        66 => [1, 65],
        68 => [69, 70],
        71 => [72, 73],
        67 => [69, 70, 72, 73],

        74 => [66, 67], // (V) = (III + IV)
        75 => [], // (VI)¹
        76 => [74, 75], // (VII) = (V + VI)
        77 => [78, 79],

        //despesa 1
        84 => [85, 86],
        87 => [88, 89, 90],
        81 => [82, 83, 85, 86],
        80 => [82, 83, 85, 86, 88, 89, 90, 91],
        92 => [169], // linha 92 é igual a linha 169 DESPESAS (INTRA-ORÇAMENTÁRIAS) (IX)
        93 => [80, 92], //   (X) = (VIII + IX)

        95 => [96, 97],
        98 => [99, 100],
        94 => [95, 98],
        101 => [93, 94], // (XII) = (X + XI)
        102 => [102], // SUPERÁVIT (XIII)
        103 => [101, 102], // TOTAL (XIV) = (XII + XIII)
    ];

    protected $ordemLinhaTotalReceitas = 74;
    protected $ordemLinhaDefict = 75;
    protected $ordemLinhaSaldosExercíciosAnteriores = 79;
    protected $ordemLinhaTotalDespesas = 101;
    protected $ordemLinhaSuperavit = 102;
    protected $ordemLinhaTotalSuperavit = 103;

    protected $linhasReceita = [[1, 79], [105, 168]];
    protected $linhasDespesa = [[80, 104], [169, 178]];

    public function __construct($filtros, $carregarTemplate = true)
    {
        $this->exercicio = $filtros['DB_anousu'];
        $this->emissor = \InstituicaoRepository::getInstituicaoByCodigo($filtros['DB_instit']);
        if ($carregarTemplate) {
            $template = TemplateFactory::getTemplate($filtros['codigo_relatorio'], $filtros['periodo']);
            $this->parser = new XlsAnexoUm($template);
        }

        $this->constructAssinaturas($filtros['DB_instit']);
        $this->constructInstituicoes(DBConfig::whereIn('codigo', $filtros['instituicoes'])->get());
        $this->constructPeriodo($filtros['periodo']);
        $this->constructRelatorio($filtros['codigo_relatorio']);
        $this->processaEnteFederativo();
    }

    public function emitir()
    {
        $this->processar();

        foreach ($this->linhasOrganizadas as $section => $linhas) {
            $this->parser->addCollection($section, $linhas);
        }
        $mesesPeriodo = sprintf(
            '%s - %s',
            DBDate::getMesExtenso($this->periodo->getMesInicial()),
            DBDate::getMesExtenso($this->periodo->getMesFinal())
        );

        $this->parser->setEnteFederativo($this->enteFederativo);
        $this->parser->setEmissor($this->emissor);
        $this->parser->setPeriodo($this->periodo->getDescricao());
        $this->parser->setMesesPeriodo($mesesPeriodo);

        $this->parser->setNotaExplicativa($this->getNotaExplicativa());
        $this->parser->setNomePrefeito($this->assinatura->assinaturaPrefeito());
        $this->parser->setNomeContador($this->assinatura->assinaturaContador());
        $this->parser->setNomeOrdenador($this->assinatura->assinaturaSecretarioFazenda());

        $filename = $this->parser->gerar();

        Settings::setTempDir('tmp/');
        $filePdf = basename($filename, ".xlsx") . ".pdf";
        $converter = new OfficeConverter($filename);
        $converter->convertTo($filePdf);

        return [
            'xls' => $filename,
            'xlsLinkExterno' => ECIDADE_REQUEST_PATH . $filename,
            'outros' => [
                [
                    'documento' => Settings::getTempDir() . $filePdf,
                    'nome' => "Anexo I - Balanço Orçamentário (PDF)"
                ]
            ]
        ];
    }

    protected function processar()
    {
        $this->processaLinhas($this->linhas);
        $this->criaProriedadesValor();
        $this->linha79();

        $this->calcularSoma();
        $this->outrosCalculosReceita();
        $this->outrosCalculosDespesa();
        $this->limparColunas();
        $this->calcularDefictSuperavit();
        $this->organizaLinhas();
    }

    protected function outrosCalculosReceita()
    {
        foreach ($this->linhasReceita as $intervalo) {
            $linhas = range($intervalo[0], $intervalo[1]);
            foreach ($linhas as $ordem) {
                $linha = $this->linhas[$ordem];
                $this->calculaPercentuais($linha);
                $linha->saldo = $linha->previsao_atualizada - $linha->arrecadado_acumulado;
            }
        }
    }

    protected function outrosCalculosDespesa()
    {
        foreach ($this->linhasDespesa as $intervalo) {
            $linhas = range($intervalo[0], $intervalo[1]);
            foreach ($linhas as $ordem) {
                $linha = $this->linhas[$ordem];

                $linha->saldo_empenhado = $linha->total_creditos - $linha->empenhado_liquido_acumulado;
                $linha->saldo_liquidado = $linha->total_creditos - $linha->liquidado_acumulado;
            }
        }
    }


    /**
     * O cálculo é realizado da seguinte forma: ($valor1 / $valor2) 100. O valor retornado tem a precisão de 2 digitos
     *
     * @param $valor1
     * @param $valor2
     * @return float
     */
    protected function calculaPercentual($valor1, $valor2)
    {
        return round(($valor1 / $valor2) * 100, 2);
    }

    /**
     * @param stdClass $linha
     * @return void
     */
    public function calculaPercentuais(stdClass $linha)
    {
        $linha->percentual_no_bimestre = 0;
        $linha->percentual_acumulado = 0;
        if ($linha->previsao_atualizada > 0) {
            $linha->percentual_no_bimestre = $this->calculaPercentual(
                $linha->arrecadado_periodo,
                $linha->previsao_atualizada
            );
            $linha->percentual_acumulado = $this->calculaPercentual(
                $linha->arrecadado_acumulado,
                $linha->previsao_atualizada
            );
        }
    }

    public function getSimplificado()
    {
        $this->processar();
        // se for do 1º ao 5º bi coluna despesas liquidadas, até o bimestre (h)
        // se for o 6º bimestre, despesas empenhadas, até o bimestre (f)
        $vlrSuperavit = $this->linhas[$this->ordemLinhaSuperavit]->liquidado_acumulado;
        if ($this->periodo->getCodigo() == 11) {
            $vlrSuperavit = $this->linhas[$this->ordemLinhaSuperavit]->empenhado_liquido_acumulado;
        }

        $totalReceitas = $this->linhas[$this->ordemLinhaTotalReceitas];
        $defict = $this->linhas[$this->ordemLinhaDefict];
        $totalDespesas = $this->linhas[$this->ordemLinhaTotalDespesas];

        $simplificado = [
            $this->montaLinhaSimplificado('RECEITAS'),
            $this->montaLinhaSimplificado('Previsão Inicial', $totalReceitas->valor_inicial),
            $this->montaLinhaSimplificado('Previsão Atualizada', $totalReceitas->previsao_atualizada),
            $this->montaLinhaSimplificado('Receitas Realizadas', $totalReceitas->arrecadado_acumulado),
            $this->montaLinhaSimplificado('Déficit Orçamentário', $defict->arrecadado_acumulado),
            $this->montaLinhaSimplificado(
                'Saldos de Exercícios Anteriores(Utilizados para Créditos Adicionais)',
                $this->linhas[$this->ordemLinhaSaldosExercíciosAnteriores]->arrecadado_acumulado
            ),
            $this->montaLinhaSimplificado('DESPESAS'),
            $this->montaLinhaSimplificado('Dotação Inicial', $totalDespesas->saldo_inicial),
            $this->montaLinhaSimplificado('Dotação Atualizada', $totalDespesas->total_creditos),
            $this->montaLinhaSimplificado('Despesas Empenhadas', $totalDespesas->empenhado_liquido_acumulado),
            $this->montaLinhaSimplificado('Despesas Liquidadas', $totalDespesas->liquidado_acumulado),
            $this->montaLinhaSimplificado('Despesas Pagas', $totalDespesas->pago_acumulado),
            $this->montaLinhaSimplificado('Superávit Orçamentário', $vlrSuperavit),
        ];

        return $simplificado;
    }

    protected function montaLinhaSimplificado($label, $valor = null)
    {
        return (object)['descricao' => $label, 'valor' => $valor];
    }

    /**
     * @return void
     */
    protected function linha79()
    {
        $this->linhas[79]->previsao_atualizada = $this->linhas[79]->arrecadado_acumulado;
    }

    protected function calcularDefictSuperavit()
    {
        $c = $this->linhas[$this->ordemLinhaTotalReceitas]->arrecadado_acumulado;
        $f = $this->linhas[$this->ordemLinhaTotalDespesas]->empenhado_liquido_acumulado;
        $h = $this->linhas[$this->ordemLinhaTotalDespesas]->liquidado_acumulado;
        $j = $this->linhas[$this->ordemLinhaTotalDespesas]->pago_acumulado;


        $linhaTotalSuperavt = $this->linhas[$this->ordemLinhaTotalSuperavit];

        $defict = $this->linhas[$this->ordemLinhaDefict];
        $superavit = $this->linhas[$this->ordemLinhaSuperavit];

        $defict->valor_inicial = ' - ';
        $defict->previsao_atualizada = ' - ';
        $defict->arrecadado_periodo = ' - ';
        $defict->arrecadado_acumulado = ' - ';
        $defict->saldo = ' - ';
        $defict->percentual_no_bimestre = ' - ';
        $defict->percentual_acumulado = ' - ';

        $superavit->saldo_inicial = ' - ';
        $superavit->total_creditos = ' - ';
        $superavit->empenhado_liquido = ' - ';
        $superavit->empenhado_liquido_acumulado = ' - ';
        $superavit->saldo_empenhado = ' - ';
        $superavit->liquidado = ' - ';
        $superavit->liquidado_acumulado = ' - ';
        $superavit->saldo_liquidado = ' - ';
        $superavit->pago_acumulado = ' - ';
        $superavit->a_liquidar = ' - ';

        if ($this->periodo->getCodigo() < 11) {
            // Défict
            if ($c < $h) {
                $defict->arrecadado_acumulado = $h - $c;
            }
        }
        // 6º bimestre
        if ($this->periodo->getCodigo() == 11) {
            // Défict
            if ($c < $f) {
                $defict->arrecadado_acumulado = $f - $c;
            }
        }
        // superávit
        if ($c > $f) {
            $superavit->empenhado_liquido_acumulado = $c - $f;
            $linhaTotalSuperavt->empenhado_liquido_acumulado += $superavit->empenhado_liquido_acumulado;
        }
        if ($c > $h) {
            $superavit->liquidado_acumulado = $c - $h;
            $linhaTotalSuperavt->liquidado_acumulado += $superavit->liquidado_acumulado;
        }

        if ($c > $j) {
            $superavit->pago_acumulado = $c - $j;
            $linhaTotalSuperavt->pago_acumulado += $superavit->pago_acumulado;
        }
    }

    protected function limparColunas()
    {
        $this->linhas[76]->saldo = ' - ';

        $this->linhas[77]->arrecadado_periodo = ' - ';
        $this->linhas[77]->percentual_no_bimestre = ' - ';
        $this->linhas[77]->percentual_acumulado = ' - ';
        $this->linhas[77]->saldo = ' - ';

        $this->linhas[78]->arrecadado_periodo = ' - ';
        $this->linhas[78]->percentual_no_bimestre = ' - ';
        $this->linhas[78]->arrecadado_acumulado = ' - ';
        $this->linhas[78]->percentual_acumulado = ' - ';
        $this->linhas[78]->saldo = ' - ';

        $this->linhas[79]->valor_inicial = ' - ';
        $this->linhas[79]->arrecadado_periodo = ' - ';
        $this->linhas[79]->percentual_no_bimestre = ' - ';
        $this->linhas[79]->percentual_acumulado = ' - ';
        $this->linhas[79]->saldo = ' - ';

        // reserva de contingência
        $this->linhas[91]->empenhado_liquido = '-';
        $this->linhas[91]->empenhado_liquido_acumulado = '-';
        $this->linhas[91]->liquidado = '-';
        $this->linhas[91]->liquidado_acumulado = '-';
        $this->linhas[91]->pago_acumulado = '-';

        // despesa
        $this->linhas[103]->saldo_empenhado = '-';
        $this->linhas[103]->saldo_liquidado = '-';

        $this->linhas[104]->empenhado_liquido = '-';
        $this->linhas[104]->empenhado_liquido_acumulado = '-';
        $this->linhas[104]->liquidado = '-';
        $this->linhas[104]->liquidado_acumulado = '-';
        $this->linhas[104]->pago_acumulado = '-';
        $this->linhas[104]->a_liquidar = '-';
    }
}
