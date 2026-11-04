<?php

namespace App\Domain\Financeiro\Planejamento\Services\Relatorios;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Planejamento\Relatorios\LOA\AnexoDoisResumoReceitaPdf;
use App\Domain\Financeiro\Planejamento\Relatorios\LOA\OrcamentoPdf;

class AnexoDoisReceitaService extends ReceitaService
{
    /**
     * Nível e-cidade:
     * CATEGORIA ECONÔMICA: Nível 1 e 2
     * FONTE: Nível 3 e 4
     * DESDOBRAMENTO: A partir do nível 5
     *
     * Nível União e Estado:
     * CATEGORIA ECONÔMICA: Nível 1
     * FONTE: Nível 2 e Nível 3
     * DESDOBRAMENTO: Tanto na União quanto estado os desdobramentos só começam no nível 4
     * @return void
     */
    protected $mapaValores = [
        'ecidade' => [
            1 => 'categoriaEconomica',
            2 => 'categoriaEconomica',
            3 => 'fonte',
            4 => 'fonte',
            5 => 'desdobramento',
        ],
        'uniao' => [
            1 => 'categoriaEconomica',
            2 => 'fonte',
            3 => 'fonte',
            4 => 'desdobramento'
        ],
        'estado' => [
            1 => [
                1 => 'categoriaEconomica',
                2 => 'fonte',
                3 => 'fonte',
                4 => 'desdobramento'
            ],
            2 => [
                1 => 'categoriaEconomica',
                2 => 'categoriaEconomica',
                3 => 'fonte',
                4 => 'fonte',
                5 => 'desdobramento',
            ]
        ]
    ];


    protected $totalizaReceitasCorrentes = [];
    protected $totalizaReceitasCapital = [];
    protected $totalizaReceitasCorrentesIntra = [];
    protected $totalizaReceitasCapitalIntra = [];

    public function __construct($filtros)
    {
        $this->filtros = $filtros;
        $this->processarFiltros();
    }

    public function emitirPdf()
    {
        $this->processar();

        $relatorio = new AnexoDoisResumoReceitaPdf();
        $relatorio->setDados($this->dados);
        return $relatorio->emitir();
    }

    public function processaProjecao()
    {
        $projecao = $this->buscarProjecao();
        return $this->montaArvoreEstrutural($projecao);
    }

    protected function processar()
    {
        $projecao = $this->processaProjecao();

        $exercicio = $this->planejamento->pl2_ano_inicial;

        $resumo = [];
        foreach ($projecao as $dado) {
            $dado->natureza = $dado->fonte;
            if (!array_key_exists($dado->natureza, $resumo)) {
                $resumo[$dado->natureza] = $this->organiza($dado);
            }

            $estrutural = estruturalFormatterReceita($dado->fonte, $this->ementario);
            $propriedadeSomar = $this->propriedeSomar($estrutural);
            $propriedade = "valor_{$exercicio}";
            $resumo[$dado->natureza]->valor += $dado->$propriedade;
            $resumo[$dado->natureza]->$propriedadeSomar += $dado->$propriedade;
        }

        // remove as contas zeradas
        foreach ($resumo as $index => $item) {
            if ($item->valor == 0) {
                unset($resumo[$index]);
            }
        }

        $this->totalizaResumo($resumo);
        $this->organizaDados($resumo);
    }

    protected function organizaDados(array $dados)
    {
        parent::organizaDados($dados);
        $this->dados['ementario'] = $this->ementario;
        $this->dados['instituicaoEmissora'] = DBConfig::find($_SESSION['DB_instit']);
        $this->dados['totalizaReceitasCorrentes'] = $this->totalizaReceitasCorrentes;
        $this->dados['totalizaReceitasCapital'] = $this->totalizaReceitasCapital;
        $this->dados['totalizaReceitasCorrentesIntra'] = $this->totalizaReceitasCorrentesIntra;
        $this->dados['totalizaReceitasCapitalIntra'] = $this->totalizaReceitasCapitalIntra;
    }

    protected function organiza($dado)
    {
        return (object)[
            "natureza" => $dado->fonte,
            "mascara" => $dado->estrutural,
            "descricao" => $dado->descricao,
            "nivel" => $dado->nivel,
            "sintetico" => $dado->sintetico,
            "valor" => 0, // essa propriedade é uma redundancia para facilitar a montagem do quadro final
            "categoriaEconomica" => 0,
            "fonte" => 0,
            "desdobramento" => 0,
        ];
    }

    public function propriedeSomar($estrutural)
    {
        if ($this->ementario === 'ecidade') {
            $mapa = $this->mapaValores['ecidade'];
            return $this->getPropriedadeMapa($estrutural, $mapa);
        }
        if ($this->ementario === 'uniao') {
            $mapa = $this->mapaValores['uniao'];
            return $this->getPropriedadeMapa($estrutural, $mapa);
        }

        if ($this->ementario === 'estadual') {
            return $this->getPropriedadeEstado($estrutural);
        }
    }

    private function getPropriedadeMapa($estrutural, $mapa)
    {
        $propriedade = 'desdobramento';
        if (array_key_exists($estrutural->getNivel(), $mapa)) {
            $propriedade = $mapa[$estrutural->getNivel()];
        }
        return $propriedade;
    }

    private function getPropriedadeEstado($estrutural)
    {
        $mapa = $this->mapaValores['estado'][1];
        if ($estrutural->isDeducao()) {
            $mapa = $this->mapaValores['estado'][2];
        }
        return $this->getPropriedadeMapa($estrutural, $mapa);
    }

    private function totalizaResumo(array $resumo)
    {
        if ($this->ementario === 'ecidade') {
            $this->totalizaResumoEcidade($resumo);
        }
        if ($this->ementario === 'uniao') {
            $this->totalizaResumoUniao($resumo);
        }
        if ($this->ementario === 'estadual') {
            $this->totalizaResumoEstado($resumo);
        }
    }

    /**
     * No e-cidade, como temos o número 4 e o 9 na frente de todas as contas, a Categoria Econômica encontra-se no
     * nível 2
     * Totaliza todas as contas de nível dois (3) de acordo com sua Categoria Econômica
     * @param array $resumo
     * @return void
     */
    private function totalizaResumoEcidade(array $resumo)
    {
        foreach ($resumo as $dado) {
            $estrutural = estruturalFormatterReceita($dado->natureza, $this->ementario);
            if ($estrutural->getNivel() != 3) {
                continue;
            }

            // pega a categoria econômica
            $key = substr($dado->natureza, 0, 2);
            if (in_array($key, ['41', '91'])) {
                $this->totalizaReceitasCorrentes[] = $dado;
            }
            if (in_array($key, ['42', '92'])) {
                $this->totalizaReceitasCapital[] = $dado;
            }
            if (in_array($key, ['47', '97'])) {
                $this->totalizaReceitasCorrentesIntra[] = $dado;
            }
            if (in_array($key, ['48', '98'])) {
                $this->totalizaReceitasCapitalIntra[] = $dado;
            }
        }
    }

    /**
     * No plano da união, as contas de Dedução não possuem o dígito 9 na frente, portanto todas contas estão no mesmo
     * nível.
     * Totaliza todas as contas de nível dois (2) de acordo com sua Categoria Econômica
     * @param array $resumo
     * @return void
     */
    private function totalizaResumoUniao(array $resumo)
    {
        foreach ($resumo as $dado) {
            $estrutural = estruturalFormatterReceita($dado->natureza, $this->ementario);
            if ($estrutural->getNivel() != 2) {
                continue;
            }

            // pega a categoria econômica
            $key = (int)substr($dado->natureza, 0, 1);
            if ($key === 1) {
                $this->totalizaReceitasCorrentes[] = $dado;
            }
            if ($key == 2) {
                $this->totalizaReceitasCapital[] = $dado;
            }
            if ($key == 7) {
                $this->totalizaReceitasCorrentesIntra[] = $dado;
            }
            if ($key == 8) {
                $this->totalizaReceitasCapitalIntra[] = $dado;
            }
        }
    }

    /**
     * No plano estadual do RS, o estado manteve o 9 nas contas de dedução.
     * Portanto, o nível das principais são diferentes das de dedução de mesma categoria econômica.
     *
     * Totaliza todas as contas principais no de nível 2
     * Totaliza todas as contas de dedução no de nível 3
     *
     * @param array $resumo
     */
    private function totalizaResumoEstado(array $resumo)
    {
        foreach ($resumo as $dado) {
            $estrutural = estruturalFormatterReceita($dado->natureza, $this->ementario);

            if ((!$estrutural->isDeducao() && $estrutural->getNivel() != 2) ||
                ($estrutural->isDeducao() && $estrutural->getNivel() != 3)) {
                continue;
            }

            // pega a categoria econômica
            $length = $estrutural->isDeducao() ? 2 : 1;
            $key = substr($dado->natureza, 0, $length);
            if (in_array($key, ['1', '91'])) {
                $this->totalizaReceitasCorrentes[] = $dado;
            }
            if (in_array($key, ['2', '92'])) {
                $this->totalizaReceitasCapital[] = $dado;
            }
            if (in_array($key, ['7', '97'])) {
                $this->totalizaReceitasCorrentesIntra[] = $dado;
            }
            if (in_array($key, ['8', '98'])) {
                $this->totalizaReceitasCapitalIntra[] = $dado;
            }
        }
    }
}
