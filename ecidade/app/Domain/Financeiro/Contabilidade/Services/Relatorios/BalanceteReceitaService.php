<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Contabilidade\Models\PlanoReceita;
use App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Receita\BalanceteReceitaCsv;
use App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Receita\BalanceteReceitaPaisagemPdf;
use App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Receita\PrevisaoInicialReceitaCsv;
use App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Receita\PrevisaoInicialReceitaPdf;
use App\Domain\Financeiro\Orcamento\Models\FonteReceita;
use Carbon\Carbon;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceita;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceitaFormatter;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceitaPadrao;
use stdClass;

/**
 * Abstração para emissão dos Balancos
 */
abstract class BalanceteReceitaService
{
    /**
     * Estrutural da receita
     * @var array
     */
    protected $filtrarNatureza;

    /**
     * Lista de IDs das instituições selecionadas
     * @var \Illuminate\Support\Collection
     */
    protected $filtrarInstituicoes;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $filtrarRecursos;

    /**
     * @var Carbon
     */
    protected $filtroDataInicio;

    /**
     * @var Carbon
     */
    protected $filtroDataFinal;

    /**
     * @var array
     */
    protected $filtrarOrgaoUnidade = [];

    /**
     * @var integer
     */
    protected $agrupador;
    /**
     * @var bool
     */
    protected $filtrarApenasComMovimentacao = false;
    /**
     * @var int
     */
    protected $ano;

    /**
     * @var string[]
     */
    protected $nomeInstituicoes = [];

    /**
     * @var bool
     */
    protected $planoEcidade = true;

    /**
     * Dados que deve apresentar:
     * Se o Orçamento = "orcamento" apenas saldo inicial
     * Se o Balanço = "balanco" com execução
     * @string Tipo do
     */
    protected $dadosEmissao;

    /**
     * Valores possíveis
     *  - ecidade
     *  - uniao
     *  - estadual
     * @var string
     */
    protected $ementario;

    /**
     * Fontes de receita
     * @var array
     */
    protected $fontesReceitas;

    /**
     * Um dos bugs mais comuns ao alterar a massa de dados nesse relatório, é que ao agrupar as informações no metodo
     * processar(), se a função que monta o hash foi mal aplicada, vai sobrescrever um index do array.
     *
     * Para tentar garantir que as linhas buscadas na query estão presente no balancete, criei esse parâmetro como
     * forma de debug. Ative apenas para garantir que todas as linhas estão presente no balancete.
     *
     * @var bool
     */
    protected $validarQuantidadeDeContasAnaliticas = false;
    /**
     * @var string
     */
    protected $apresentar;

    public function setFiltrosRequest(array $filtros)
    {
        if (!empty($filtros['natureza'])) {
            $this->filtrarNatureza = explode(',', str_replace('.', '', $filtros['natureza']));
        }

        if (!empty($filtros['instituicoes'])) {
            $instituicoes = str_replace('\"', '"', $filtros['instituicoes']);
            $instituicoes = \JSON::create()->parse($instituicoes);

            $this->filtrarInstituicoes = collect($instituicoes)->map(function ($instituicao) {
                $nome = DBConfig::find($instituicao->codigo)->nomeinstabrev;
                if (strlen(trim($nome)) == 0) {
                    $nome = $instituicao->nome;
                }
                $this->nomeInstituicoes[] = $nome;
                return $instituicao->codigo;
            });
        }

        if (!empty($filtros['apenasComMovimentacao'])) {
            $this->filtrarApenasComMovimentacao = $filtros['apenasComMovimentacao'] == 1;
        }

        if (!empty($filtros['recursos'])) {
            $this->filtrarRecursos = collect($filtros['recursos']);
        }

        if (!empty($filtros['dadosEmissao'])) {
            $this->dadosEmissao = $filtros['dadosEmissao'];
        }

        if (!empty($filtros['filtros'])) {
            $dados = str_replace('\"', '"', $filtros['filtros']);
            $dados = \JSON::create()->parse($dados);
            if (!empty($dados->unidade->aUnidades)) {
                $this->filtrarOrgaoUnidade = $dados->unidade->aUnidades;
            }
        }

        if (strpos($filtros['dataInicio'], '/') !== false) {
            $this->filtroDataInicio = Carbon::createFromFormat('d/m/Y', $filtros['dataInicio']);
            $this->filtroDataFinal = Carbon::createFromFormat('d/m/Y', $filtros['dataFinal']);
        } else {
            $this->filtroDataInicio = Carbon::createFromFormat('Y-m-d', $filtros['dataInicio']);
            $this->filtroDataFinal = Carbon::createFromFormat('Y-m-d', $filtros['dataFinal']);
        }

        $this->ano = $this->filtroDataFinal->year;

        $this->agrupador = 0;
        if (isset($filtros['nivelAgrupar'])) {
            $this->agrupador = $filtros['nivelAgrupar'];
        }

        $this->ementario = 'ecidade';
        if (isset($filtros['ementario'])) {
            $this->ementario = $filtros['ementario'];
            if ($filtros['ementario'] !== 'ecidade') {
                $this->planoEcidade = false;
            }
        }

        if (!empty($filtros['apresentar'])) {
            $this->apresentar = $filtros['apresentar'];
        }
    }

    /**
     * @param DBConfig[] $instituicoes
     */
    public function setInstituicoes($instituicoes)
    {
        $this->filtrarInstituicoes = $instituicoes->map(function (DBConfig $config) {
            $this->nomeInstituicoes[] = $config->nome;
            return $config->codigo;
        });
    }

    /**
     * Emite em Pdf
     * @param $dados
     * @return array
     */
    public function emitirPdf($dados)
    {
        $titulo = $this->getHeaderTitulo();
        $periodo = $this->getHeaderPeriodo();
        $plano = $this->getHeaderTipoPlano();
        $relatorio = $this->getInstanciaBalancetePdf($this->apresentar);
        $relatorio->headers($titulo, $periodo, implode(', ', $this->nomeInstituicoes), $plano);
        $relatorio->setDadosBalancete($dados);

        return $relatorio->imprimir();
    }

    /**
     * Emite em csv
     * @param $dados
     * @return array
     */
    public function emitirCsv($dados)
    {
        $relatorio = $this->getInstanciaBalanceteCSV();
        $relatorio->setCsvControl(";");
        $relatorio->setDados($dados);
        return $relatorio->emitir();
    }

    /**
     * Retorna a arvore do balancete
     * @return array
     */
    public function getArvore()
    {
        return $this->processar();
    }

    public function emitir()
    {
        $dados = $this->processar();

        return array_merge($this->emitirPdf($dados), $this->emitirCsv($dados));
    }


    /**
     * Retorna o formatter para os estruturais de receita do e-Cidade.
     * @param $natureza
     * @return EstruturalReceitaFormatter|EstruturalReceitaPadrao|EstruturalReceita
     */
    protected function estruturalFormatter($natureza)
    {
        if ($this->planoEcidade) {
            return new EstruturalReceita($natureza);
        }
        return new EstruturalReceitaPadrao($natureza);
    }

    protected function montaArvore($receitas)
    {
        $arvore = [];
        foreach ($receitas as $receita) {
            $estrutural = $this->estruturalFormatter($receita->natureza);
            $receita->natureza = $estrutural->getEstrutural();
            $nivel = $estrutural->getNivel();

            $hash = $this->montaHashArvore($receita);

            $arvore[$hash] = $this->mapperReceitaAnalitica($receita, $estrutural);
            list($estrutural, $arvore) = $this->montaContaPai($nivel, $estrutural, $arvore, $receita);
        }
        ksort($arvore);
        return $arvore;
    }

    /**
     * @param array $receitas
     * @return void
     */
    protected function montaArvorePorGrupo(array $receitas)
    {
        $arvore = [];
        foreach ($receitas as $receita) {
            $estrutural = $this->estruturalFormatter($receita->natureza);
            $hash = $this->montaHashGrupo($receita);
            $arvore[$hash] = $this->mapperReceitaAnalitica($receita, $estrutural);
            //Se a classe da receita for 9 (dedução) altera pela classe 4, para deduzir nas contas pais
            if ($receita->classe = 9) {
                $estrutural = $this->estruturalFormatter("4{$receita->resto}");
            }
            $nivel = $estrutural->getNivel();

            list($estrutural, $arvore) = $this->montaContaPai($nivel, $estrutural, $arvore, $receita);
        }
        ksort($arvore);
        return $arvore;
    }

    /**
     * @param int $nivel
     * @param EstruturalReceitaFormatter $estrutural
     * @param array $arvore
     * @param stdClass $receita
     * @return array
     */
    protected function montaContaPai($nivel, EstruturalReceitaFormatter $estrutural, $arvore, $receita)
    {
        while ($nivel != 1) {
            $estrutural = $this->estruturalFormatter($estrutural->getCodigoEstruturalPai());
            /**
             * As classes de formatter do estrutural, não validam a existência do estrutural no sistema.
             * A função buscaFonteReceita realiza uma busca recursiva pelo estrutural pai caso o estrutural informado
             * por parâmetro não exista no sistema.
             */
            $fonteReceita = $this->buscaFonteReceita($estrutural->getEstrutural());
            $estrutural = $this->estruturalFormatter($fonteReceita->natureza);

            $hash = $fonte = $estrutural->getEstrutural();
            $nivel = $estrutural->getNivel();

            // Quando alterado o agrupamento para agrupar as deduções do mesmo grupo, retiramos a classe do estrutural
            // no index para mander a dedução junto a receita.
            if ($this->agrupador == 2) {
                $hash = substr($fonte, 1);
            }

            if (!array_key_exists($hash, $arvore)) {
                $arvore[$hash] = $this->mapperReceitaSintetica($fonteReceita->nome, $estrutural);
            }

            $arvore[$hash]->valor_inicial += $receita->valor_inicial;
            $arvore[$hash]->previsao_adicional_acumulado += $receita->previsao_adicional_acumulado;
            $arvore[$hash]->previsao_atualizada += $receita->previsao_atualizada;
            $arvore[$hash]->arrecadado_anterior += $receita->arrecadado_anterior;
            $arvore[$hash]->arrecadado_periodo += $receita->arrecadado_periodo;
            $arvore[$hash]->valor_a_arrecadar += $receita->valor_a_arrecadar;
            $arvore[$hash]->arrecadado_acumulado += $receita->arrecadado_acumulado;
            $arvore[$hash]->previsao_adicional += $receita->previsao_adicional;
        }

        return array($estrutural, $arvore);
    }

    /**
     * Retorna as fontes de receita do e-cidade
     * @return array
     */
    protected function getFontesReceitas()
    {
        if (is_null($this->fontesReceitas)) {
            $this->fontesReceitas = getFontesEmentario($this->ementario, $this->ano);
        }

        return $this->fontesReceitas;
    }

    /**
     * @param $fonte
     * @return FonteReceita|PlanoReceita
     */
    protected function buscaFonteReceita($fonte)
    {
        if (!array_key_exists($fonte, $this->getFontesReceitas())) {
            $estruturalPai = $this->estruturalFormatter($fonte)->getEstruturalPai();
            $fonte = $estruturalPai->getEstrutural();
            return $this->buscaFonteReceita($fonte);
        }
        return $this->fontesReceitas[$fonte];
    }

    /**
     * Retorna um array com os dados do balancete
     */
    abstract public function processar();

    /**
     * @return mixed
     */
    abstract protected function getDados();

    /**
     * @param stdClass $receita
     * @return string
     */
    abstract protected function montaHashArvore($receita);

    /**
     * @param stdClass $receita
     * @return string
     */
    abstract protected function montaHashGrupo($receita);

    /**
     * @return array
     */
    protected function montaWhere()
    {
        $instituicoes = $this->filtrarInstituicoes->implode(',');

        $where = [
            "ano = {$this->ano}",
            "instituicao in ($instituicoes)",
        ];

        if (!empty($this->filtrarNatureza)) {
            $naturezas = array_map(function ($natureza) {
                $estrutural = $this->estruturalFormatter($natureza);
                return "(natureza like '{$estrutural->getEstruturalAteNivel()}%')";
            }, $this->filtrarNatureza);

            $where[] = '(' . implode(' or ', $naturezas) . ')';
        }

        if (!empty($this->filtrarRecursos)) {
            $where[] = $this->montaWhereFiltraRecurso();
        }

        if ($this->filtrarApenasComMovimentacao) {
            $where[] = sprintf(
                "(%s or %s or %s or %s)",
                'previsao_adicional_acumulado != 0',
                'valor_a_arrecadar != 0',
                'arrecadado_periodo != 0',
                'arrecadado_acumulado != 0'
            );
        }

        if (!empty($this->filtrarOrgaoUnidade)) {
            $where[] = $this->montaWhereOrgaoUnidade();
        }

        $dataInicio = $this->filtroDataInicio->format('Y-m-d');
        $dataFinal = $this->filtroDataFinal->format('Y-m-d');
        $where = implode(' and ', $where);
        return array($where, $dataInicio, $dataFinal);
    }

    /**
     * @return string
     */
    protected function montaWhereOrgaoUnidade()
    {
        $orgaoUnidade = [];
        foreach ($this->filtrarOrgaoUnidade as $dadoOrgaoUnidade) {
            $dados = explode('-', $dadoOrgaoUnidade);
            $orgaoUnidade[] = "(o70_orcorgao = {$dados[0]} and o70_orcunidade = {$dados[1]})";
        }

        $filtro = "(" . implode(' or ', $orgaoUnidade) . ")";
        return $filtro;
    }

    /**
     * ATENÇÃO por default, essa classe está filtrando o recurso da receita, para usar no balancete por complemento
     * sobrescreva a condição por: "recurso_lancamento"
     *
     * @return string
     */
    protected function montaWhereFiltraRecurso()
    {
        $recursos = $this->filtrarRecursos->implode(',');
        return "recurso_receita in ($recursos)";
    }

    /**
     * @return object
     */
    protected function criaObjetoReceita()
    {
        return (object)[
            "natureza" => '',
            "mascara" => '',
            "reduzido" => null,
            "fonte" => null,
            "ano" => null,
            "descricao" => '',
            "cp" => '',
            "instituicao" => null,
            "orgao" => null,
            "unidade" => null,
            "esfera" => null,
            "valor_inicial" => 0,
            "subrecurso" => null,
            "gestao" => null,
            "siconfi" => null,
            "recurso_lancamento" => null,
            "complemento" => null,
            "previsao_adicional_acumulado" => 0,
            "previsao_atualizada" => 0,
            "arrecadado_anterior" => 0,
            "arrecadado_periodo" => 0,
            "valor_a_arrecadar" => 0,
            "arrecadado_acumulado" => 0,
            "previsao_adicional" => 0,
            "ordem" => 0,
            "sintetico" => false,
        ];
    }

    protected function mapperReceitaAnalitica($receita, EstruturalReceitaFormatter $estrutural)
    {
        $std = $this->criaObjetoReceita();

        $std->natureza = $receita->natureza;
        $std->mascara = $estrutural->getEstruturalComMascara();
        $std->reduzido = $receita->reduzido;
        $std->fonte = $receita->fonte;
        $std->ano = $receita->ano;
        $std->descricao = $receita->descricao;
        $std->cp = $receita->cp;
        $std->instituicao = $receita->instituicao;
        $std->orgao = $receita->orgao;
        $std->unidade = $receita->unidade;
        $std->esfera = $receita->esfera;
        $std->subrecurso = $receita->subrecurso;
        $std->gestao = $receita->gestao;
        $std->siconfi = $receita->siconfi;
        $std->complemento = $receita->complemento;
        $std->valor_inicial = $receita->valor_inicial;
        $std->previsao_adicional_acumulado = $receita->previsao_adicional_acumulado;
        $std->previsao_atualizada = $receita->previsao_atualizada;
        $std->arrecadado_anterior = $receita->arrecadado_anterior;
        $std->arrecadado_periodo = $receita->arrecadado_periodo;
        $std->valor_a_arrecadar = $receita->valor_a_arrecadar;
        $std->arrecadado_acumulado = $receita->arrecadado_acumulado;
        $std->previsao_adicional = $receita->previsao_adicional;
        return $std;
    }

    protected function mapperReceitaSintetica($nomeReceita, EstruturalReceitaFormatter $estrutural)
    {
        $std = $this->criaObjetoReceita();
        $std->natureza = $estrutural->getEstrutural();
        $std->mascara = $estrutural->getEstruturalComMascara();
        $std->descricao = $nomeReceita;
        $std->sintetico = true;
        return $std;
    }

    protected function getInstanciaBalanceteCSV()
    {
        if ($this->dadosEmissao === 'orcamento') {
            return new PrevisaoInicialReceitaCsv();
        }
        return new BalanceteReceitaCsv();
    }

    protected function getInstanciaBalancetePdf($apresentar)
    {

        if ($this->dadosEmissao === 'orcamento') {
            $balancete = new PrevisaoInicialReceitaPdf();
        } else {
            $balancete = new BalanceteReceitaPaisagemPdf();
        }
        $balancete->setDados($apresentar);
        return $balancete;
    }


    /**
     * Essa função só retorna o label para aplicar no header do relatório do Tipo de Plano que esta sendo emitido
     * @return string
     */
    protected function getHeaderTipoPlano()
    {
        $plano = 'e-Cidade';
        if ($this->ementario === 'uniao') {
            $plano = 'Plano União/Federação';
        }
        if ($this->ementario === 'estadual') {
            $plano = 'Plano Estadual/Regional';
        }
        return $plano;
    }

    /**
     * Essa função só retorna o label para aplicar no header do relatório
     * @return string
     */
    public function getHeaderPeriodo()
    {
        return sprintf(
            '%s até %s',
            $this->filtroDataInicio->format('d/m/Y'),
            $this->filtroDataFinal->format('d/m/Y')
        );
    }

    /**
     * Essa função só retorna o label para aplicar no header do relatório
     * @return string
     */
    protected function getHeaderTitulo()
    {
        return 'BALANCETE DA RECEITA';
    }

    /**
     * Um dos bugs mais comuns ao alterar a massa de dados nesse relatório, é que ao agrupar as informações no metodo
     * processar(), se a função que monta o hash foi mal aplicada, vai sobrescrever um index do array.
     *
     * Para tentar garantir que as linhas buscadas na query estão presente no balancete, criei esse parâmetro como
     * forma de debug. Ative apenas para garantir que todas as linhas estão presente no balancete.
     *
     * @param array $receitas receitas analiticas buscadas na query
     * @param array $balancete arvore do balancete
     * @throws Exception
     */
    protected function validarQuantidadeDeContasAnaliticas($receitas, $balancete)
    {
        $totalContasAnaliticasBalancete = collect($balancete)->filter(function ($conta) {
            return !$conta->sintetico;
        })->count();

        if (count($receitas) !== $totalContasAnaliticasBalancete) {
            throw new Exception('Há uma inconsistência no relatório.');
        }
        dd(count($receitas), $totalContasAnaliticasBalancete);
    }
}
