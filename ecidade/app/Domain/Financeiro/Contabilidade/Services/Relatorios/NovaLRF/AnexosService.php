<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF;

use App\Domain\Configuracao\Departamento\Models\Departamento;
use App\Domain\Configuracao\Helpers\StorageHelper;
use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Configuracao\RelarorioLegal\Model\Relatorio;
use App\Domain\Configuracao\Services\AssinaturaService;
use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Financeiro\Contabilidade\Factories\TemplateFactory;
use App\Domain\Financeiro\Contabilidade\Models\EmissoesLrf;
use App\Domain\Financeiro\Contabilidade\Models\LrfValorManual;
use App\Domain\Financeiro\Contabilidade\Models\PlanoDespesa;
use App\Domain\Financeiro\Contabilidade\Models\PlanoReceita;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\BalanceteVerificacaoInformacaoComplementarService;
use App\Domain\Financeiro\Orcamento\Models\LrfNotaExplicativa;
use App\Notifications\EmissaoRelatorioLegalNotification;
use Carbon\Carbon;
use DBDate;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceitaPadrao;
use ECidade\Financeiro\Contabilidade\Relatorio\DemonstrativoFiscal;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use JSON;
use Periodo;
use stdClass;

abstract class AnexosService
{
    const TIPO_PREFEITURA = 1;

    const CALCULAR_SALDO_INICIAL = 1;
    const CALCULAR_DEBITO = 2;
    const CALCULAR_CREDITO = 3;
    const CALCULAR_PERIODO = 4;
    const CALCULAR_SALDO_FINAL = 5;

    /**
     * Possui todos os filtros de emissão incluindo a sessão
     * @var array
     */
    protected $filtros = [];

    /**
     * código do relatório legal
     * @var integer
     */
    protected $relatorio;

    /**
     * @var Periodo
     */
    protected $periodo;

    /**
     * Linhas do relatório, incluindo as totalizadoras.
     * Origem é o json que mapeia o relatório
     * @var stdClass[]
     */
    protected $linhas = [];

    protected $linhasManuais = [];

    protected $sections = [];

    /**
     * Quando relatório organizado por seções
     * @var array
     */
    protected $linhasOrganizadas = [];

    /**
     * @var AssinaturaService
     */
    protected $assinatura;

    /**
     * Lista do objeto de instituições presente na emissão
     * @var DBConfig[]|Collection
     */
    protected $instituicoes;

    /**
     * Lista com os códigos das instituições presente na emissão
     * @var integer[]
     */
    protected $listaInstituicoes;

    /**
     * @var integer
     */
    protected $exercicio;

    /**
     * Data inicial do período de emissão
     * @var Carbon
     */
    protected $dataIncio;

    /**
     * Data final do período de emissão
     * @var Carbon
     */
    protected $dataFim;

    /**
     * @var \Instituicao
     */
    protected $emissor;
    /**
     * Ente federativo da instituição emissora
     * @var string
     */
    protected $enteFederativo;

    /**
     * Abaixo segue outros filtros usados para processar a Matriz
     */
    protected $comEncerramento = false;
    protected $indicadorSuperavit = 'T';
    protected $sistemaContas = 99;
    protected $contasComMovimento = true;

    public function __construct(array $filtros)
    {
        $this->filtros = $filtros;
        $this->relatorio = $this->filtros['relatorio'];
        $this->exercicio = $this->filtros['DB_anousu'];
    }

    /**
     * Lê o JSON de mapeamento do relatório e carrega as linhas
     * @return array|stdClass[]
     */
    public function getLinhas()
    {
        if (empty($this->linhas)) {
            $servico = new LinhasRelatorioLegal($this->relatorio, $this->filtros['tipo']);
            $this->linhas = $servico->getLinhas();

            $this->criaPropriedadesValores();
        }

        return $this->linhas;
    }

    public function emitirComUpload()
    {
        $files = $this->emitir();

        $retornoStorage = $this->enviarStorage($files);
        $this->atualizaStatusEnvio($retornoStorage);
        $this->filtros['pdf'] = $retornoStorage->pdf;
        $this->filtros['xls'] = $retornoStorage->xls;
        Usuario::find($this->filtros['DB_id_usuario'])
            ->notify(new EmissaoRelatorioLegalNotification($this->filtros));
    }

    /**
     * @return void
     */
    public function identificaLinhasManuais()
    {
        if (empty($this->linhasManuais)) {
            foreach ($this->getLinhas() as $linha) {
                if ($linha->valorManual) {
                    $this->linhasManuais[$linha->ordem] = $linha;
                }
            }
        }
        return $this->linhasManuais;
    }

    /**
     * Realiza a construção dos parâmetros / objetos necessários para processamento do relatório
     * Se necessário sobrescrever esse método, cuidar que pode haver dependência na ordem chamada das funções
     * @return void
     * @throws Exception
     */
    protected function processarFiltros()
    {
        $this->construirPeriodo();
        $this->carregarParserXls();
        $this->construirInstituicoes();
        $this->construirAssinaturas();
        $this->getLinhas();
        $this->processaEmissor();
        $this->processaEnteFederativo();
    }

    protected function processaEmissor()
    {
        $this->emissor = \InstituicaoRepository::getInstituicaoByCodigo($this->filtros['DB_instit']);
    }

    protected function processaEnteFederativo()
    {
        $this->enteFederativo = DemonstrativoFiscal::getEnteFederativo($this->emissor);
        if ($this->emissor->getTipo() != self::TIPO_PREFEITURA) {
            $this->enteFederativo .= "\n" . $this->emissor->getDescricao();
        }
    }

    /**
     * Cria a instância da classe período e com base nas informações dela seta os dados do período de emissão
     * @return void
     * @throws Exception
     */
    protected function construirPeriodo()
    {
        $this->periodo = new Periodo($this->filtros['periodo']);
        $this->dataIncio = Carbon::createFromFormat('d/m/Y', $this->periodo->stringDataInicial($this->exercicio));
        $this->dataFim = Carbon::createFromFormat('d/m/Y', $this->periodo->stringDataFinal($this->exercicio));
    }

    /**
     * Constrói uma instância com acesso as assinaturas de: Prefeito, Tesoureiro, Secretaria da Fazenda e Contador
     * @return void
     */
    protected function construirAssinaturas()
    {
        $this->assinatura = new AssinaturaService($this->filtros['DB_instit']);
    }

    /**
     * Constrói a lista de instituição que deve ser processado.
     * Se não foi informado uma lista de instituições o sistema processará consolidado. (Todas Instituições)
     */
    protected function construirInstituicoes()
    {
        $dbConfig = DBConfig::query();
        if (empty($this->filtros['consolidado']) && !empty($this->filtros['instituicoes'])) {
            $dbConfig->whereIn('codigo', $this->filtros['instituicoes']);
        }
        $this->instituicoes = $dbConfig->get();
        $this->listaInstituicoes = $this->instituicoes->map(function (DBConfig $instituicao) {
            return $instituicao->codigo;
        });
    }

    /**
     * Monta uma estrutura para filtrar as informações da MSC
     * Com essa estrutura padrão podemos alterar de forma simples os filtros de emissão para cada processamento
     * necessário da MSC.
     * @return array
     */
    protected function getWhere()
    {
        return [
            'instituicoes' => $this->listaInstituicoes,
            'dataInicial' => $this->dataIncio->format('Y-m-d'),
            'dataFinal' => $this->dataFim->format('Y-m-d'),
            'comEncerramento' => $this->comEncerramento,
            'exercicio' => $this->exercicio,
            'estruturais' => [], // filtra os estruturais do e-cidade. Não será usado nos relatórios da LRF
            'estruturaisUniao' => [],
            'indicadorSuperavit' => $this->indicadorSuperavit,
            'sistemaContas' => $this->sistemaContas,
            'contasComMovimento' => $this->contasComMovimento,
        ];
    }

    protected function executarMsc($estruturais = [], $comEncerramento = false)
    {
        $service = new BalanceteVerificacaoInformacaoComplementarService();
        $filtros = $this->getWhere();

        $filtros['estruturaisUniao'] = $estruturais;
        $filtros['comEncerramento'] = $comEncerramento;
        $service->setFiltrosArray($filtros);
        return $service->processar();
    }

    /**
     * @param string $estrutural
     * @return string
     */
    protected function receitaEstruturalAteNivel($estrutural)
    {
        return (new EstruturalReceitaPadrao($estrutural))->getEstruturalAteNivel();
    }

    /**
     * Retorna uma lista de contas da receita que dão match com os parâmetros informados
     * @param integer $exercicio
     * @param array $contem
     * @param array $naoContem
     * @return array
     */
    protected function getContasReceita($exercicio, $contem = [], $naoContem = [])
    {
        return PlanoReceita::query()
            ->select('conta')
            ->whereRaw('uniao is true')
            ->where('exercicio', $exercicio)
            ->when(!empty($contem), function ($query) use ($contem) {
                $like = [];
                foreach ($contem as $conta) {
                    $like[] = " conta like '{$conta}%' ";
                }

                $query->whereRaw(sprintf('(%s)', implode(' or ', $like)));
            })
            ->when(!empty($naoContem), function ($query) use ($naoContem) {
                $dislike = [];
                foreach ($naoContem as $conta) {
                    $dislike[] = " conta not like '{$conta}%' ";
                }

                $query->whereRaw(sprintf('(%s)', implode(' and ', $dislike)));
            })
            ->get()
            ->map(function (PlanoReceita $plano) {
                return $plano->conta;
            })
            ->toArray();
    }

    /**
     * Quando template é por session, esse método joga as linhas dentro de cada sessão
     */
    protected function organizaLinhas()
    {
        foreach ($this->sections as $section => $deAte) {
            $linhasSection = range($deAte[0], $deAte[1]);
            foreach ($linhasSection as $ordemLinha) {
                $this->linhasOrganizadas[$section][] = $this->linhas[$ordemLinha];
            }
        }
    }

    /**
     * @param int $modelo Código do modelo, podendo ser: (0 - padrão, 1 - IN RS, 2 - Porto Velho, 3 - Mdf)
     * @param string $anexo nome do anexo
     * @return string
     * @throws Exception
     */
    protected function carregarTemplate($modelo = 0, $anexo = '')
    {
        $template = TemplateFactory::getTemplate(
            $this->relatorio,
            $this->periodo->getCodigo(),
            $modelo
        );
        if (!file_exists($template)) {
            throw new \Exception("Não foi encontrado o template do {$anexo}.", 403);
        }
        return $template;
    }

    public function getNotaExplicativa()
    {
        $textos = [];

        // busca o nome do departamento para substituir na Nota Explicativa Padrão
        $dpto = Departamento::query()->select('descrdepto')->find($this->filtros['DB_coddepto']);
        $dtEmissao = date("d/m/Y", db_getsession("DB_datausu"));
        $hEmissao = date("H:i:s");

        $variaveis = ['[nome_departamento]', '[data_emissao]', '[hora_emissao]'];
        $valores = [$dpto->descrdepto, $dtEmissao, $hEmissao];

        // busca a Nota Explicativa Padrão e realiza o parse das variáveis acima
        $notaPadrao = Relatorio::query()->select('o42_notapadrao')->find($this->relatorio);
        if ($notaPadrao instanceof Relatorio) {
            $textos[] = str_replace($variaveis, $valores, $notaPadrao->o42_notapadrao);
        }

        $nota = LrfNotaExplicativa::query()
            ->periodo()
            ->where('o42_codparrel', $this->relatorio)
            ->where('o42_anousu', $this->exercicio)
            ->where('o42_instit', $this->filtros['DB_instit'])
            ->where('o42_periodo', $this->filtros['periodo'])
            ->first();

        if (is_null($nota)) {
            return $textos[0];
        }

        if (!empty($nota->o42_fonte)) {
            $textos[] = "Fonte: " . str_replace($variaveis, $valores, $nota->o42_fonte);
        }

        if (!empty($nota->o42_nota)) {
            $textos[] = "Nota Explicativa: {$nota->o42_nota}";
        }

        return implode("\n", $textos);
    }

    /**
     * @param array $files
     * @return object
     * @throws Exception
     */
    protected function enviarStorage(array $files)
    {
        $metadata = (object)[
            'id_emissao' => $this->filtros['id_emissao'],
            'tipo' => $this->filtros['tipo'],
            'anexo' => $this->filtros['anexo'],
            'codigo_relatorio' => $this->filtros['relatorio'],
            'codigo_periodo' => $this->filtros['periodo'],
            'instituicao_emissora' => $this->filtros['DB_instit'],
            'usuario' => $this->filtros['DB_id_usuario'],
        ];

        $idStorageXls = StorageHelper::uploadArquivo($files['xls'], null, true, $metadata);
        $idStoragePdf = StorageHelper::uploadArquivo($files['pdf'], null, true, $metadata);

        return (object)['pdf' => $idStoragePdf, 'xls' => $idStorageXls];
    }

    /**
     * @param $retornoStorage
     * @return EmissoesLrf
     */
    protected function atualizaStatusEnvio($retornoStorage)
    {
        $emissao = EmissoesLrf::find($this->filtros['id_emissao']);
        $emissao->c181_status = 'PROCESSADO';
        $emissao->c181_storage = JSON::create()->stringify($retornoStorage);
        $emissao->save();

        return $emissao;
    }

    /**
     * @param stdClass $regra Recebe uma regra
     * @param stdClass $msc linha da msc
     * @return bool
     */
    protected function match(stdClass $regra, stdClass $msc)
    {
        if (!$this->matchPo($regra, $msc)) {
            return false;
        }
        if (!$this->matchPcasp($regra, $msc)) {
            return false;
        }

        if (!$this->matchConta($regra, $msc)) {
            return false;
        }

        if (!$this->matchSiconfi($regra, $msc)) {
            return false;
        }

        if (!$this->matchComplemento($regra, $msc)) {
            return false;
        }

        if (!$this->matchFuncaoSubfuncao($regra, $msc)) {
            return false;
        }

        if (!$this->matchFuncaoSubfuncao($regra, $msc)) {
            return false;
        }

        if (!$this->matchFP($regra, $msc)) {
            return false;
        }

        if (!$this->matchDC($regra, $msc)) {
            return false;
        }

        return true;
    }

    /**
     * @param stdClass $regra regra da linha do relatório
     * @param stdClass $msc linha da msc
     * @return bool
     */
    private function matchPo(stdClass $regra, stdClass $msc)
    {
        if (!$regra->po->isEmpty()) {
            $count = $regra->po->filter(function ($po) use ($regra, $msc) {
                return strpos($msc->poder_ordao, (string)$po) === 0;
            })->count();

            if ($count === 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param stdClass $regra regra da linha do relatório
     * @param stdClass $msc linha da msc
     * @return bool
     */
    protected function matchPcasp(stdClass $regra, stdClass $msc)
    {
        if (!$regra->pcasp->isEmpty()) {
            $count = $regra->pcasp->filter(function ($conta) use ($regra, $msc) {
                return strpos($msc->estrutural_padrao, (string)$conta) === 0;
            })->count();

            if ($count === 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param stdClass $regra regra da linha do relatório
     * @param stdClass $msc linha da msc
     * @return bool
     */
    protected function matchConta(stdClass $regra, stdClass $msc)
    {
        if (!$regra->contas->isEmpty()) {
            $count = $regra->contas->filter(function ($conta) use ($regra, $msc) {
                return strpos($msc->{$regra->natureza}, (string)$conta) === 0;
            })->count();

            if ($count === 0) {
                return false;
            }
        }


        if (!$regra->contasExclusao->isEmpty()) {
            $count = $regra->contasExclusao->filter(function ($conta) use ($regra, $msc) {
                return strpos($msc->{$regra->natureza}, (string)$conta) === 0;
            })->count();

            if ($count !== 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param stdClass $regra regra da linha do relatório
     * @param stdClass $msc linha da msc
     * @return bool
     */
    protected function matchSiconfi(stdClass $regra, stdClass $msc)
    {
        $codigo = substr($msc->siconfi, 1);
        if (!$regra->siconfi->isEmpty() and !$regra->siconfi->contains($codigo)) {
            return false;
        }

        if (!$regra->siconfiExclusao->isEmpty() and $regra->siconfiExclusao->contains($codigo)) {
            return false;
        }
        return true;
    }

    /**
     * @param stdClass $regra regra da linha do relatório
     * @param stdClass $msc linha da msc
     * @return bool
     */
    protected function matchComplemento(stdClass $regra, stdClass $msc)
    {
        if (!$regra->co->isEmpty() and !$regra->co->contains($msc->complemento)) {
            return false;
        }

        if (!$regra->coExclusao->isEmpty() and $regra->coExclusao->contains($msc->complemento)) {
            return false;
        }
        return true;
    }

    protected function matchFuncaoSubfuncao(stdClass $regra, stdClass $msc)
    {
        $valor = sprintf('%s%s', $msc->funcao, $msc->subfuncao);

        if (!$regra->fs->isEmpty() and !$regra->fs->contains($valor)) {
            return false;
        }

        if (!$regra->fsExclusao->isEmpty() and $regra->fsExclusao->contains($valor)) {
            return false;
        }
        return true;
    }

    protected function matchFP(stdClass $regra, stdClass $msc)
    {
        if (!is_null($regra->fp) and !$regra->fp !== $msc->indicador_superavit) {
            return false;
        }

        if (!is_null($regra->fp) and $regra->fp !== $msc->indicador_superavit) {
            return false;
        }

        return true;
    }

    protected function matchDC(stdClass $regra, stdClass $msc)
    {
        if (!is_null($regra->dc) and !$regra->dc !== $msc->divida_consolidada) {
            return false;
        }

        return true;
    }


    /**
     * @param stdClass $linha
     * @param stdClass $regras
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula
     * @return void
     */
    protected function calculaColunaStrPos(
        stdClass $linha,
        stdClass $regras,
        array    $dadosMsc,
        $coluna,
        $formula
    ) {
        foreach ($dadosMsc as $msc) {
            if (!$this->match($regras, $msc)) {
                continue;
            }

            switch ($formula) {
                case self::CALCULAR_SALDO_INICIAL:
                    $linha->{$coluna} += $msc->saldo_anterior;
                    break;
                case self::CALCULAR_DEBITO:
                    $linha->{$coluna} += $msc->saldo_debito;
                    break;
                case self::CALCULAR_CREDITO:
                    $linha->{$coluna} += $msc->saldo_credito;
                    break;
                case self::CALCULAR_PERIODO:
                    $linha->{$coluna} += ($msc->saldo_debito + $msc->saldo_credito);
                    break;
                case self::CALCULAR_SALDO_FINAL:
                    $linha->{$coluna} += $msc->saldo_final;
                    break;
            }
        }
    }

    /**
     * Retorna a descrição dos meses processado no período emitido
     * @return string
     * @throws Exception
     */
    protected function mesesProcessadosPeriodo()
    {
        return sprintf(
            '%s - %s',
            DBDate::getMesExtenso($this->periodo->getMesInicial()),
            DBDate::getMesExtenso($this->periodo->getMesFinal())
        );
    }

    /**
     * @param integer $exercicio
     * @param array $contem
     * @return mixed
     */
    protected function getPlanoDespesa($exercicio, array $contem)
    {
        return PlanoDespesa::query()
            ->select('conta')
            ->whereRaw('uniao is true')
            ->where('exercicio', $exercicio)
            ->when(!empty($contem), function ($query) use ($contem) {
                $like = [];
                foreach ($contem as $conta) {
                    $like[] = " conta like '{$conta}%' ";
                }

                $query->whereRaw(sprintf('(%s)', implode(' or ', $like)));
            })->get()
            ->map(function (PlanoDespesa $plano) {
                return $plano->conta;
            })
            ->toArray();
    }

    /**
     * @param array $linhas ordem das linhas calculadas
     * @return \Illuminate\Support\Collection
     */
    protected function getValoresManuais($linhas = [])
    {
        $meses = range(1, (int)$this->periodo->getMesFinal());

        $valoresManuais = [];
        $dados = LrfValorManual::query()
            ->where('c180_relatorio', $this->relatorio)
            ->whereIn('c180_instituicao', $this->listaInstituicoes)
            ->where('c180_exercicio', $this->exercicio)
            ->wherein('c180_mes', $meses)
            ->when(!empty($linhas), function ($query) use ($linhas) {
                $query->whereIn('c180_linha', $linhas);
            })
            ->get();
        if ($dados->count()) {
            $valoresManuais = array_merge($valoresManuais, $dados->toArray());
        }

        return collect($valoresManuais);
    }

    protected function totalizarSomaLinhas()
    {
        foreach ($this->linhas as $linha) {
            if (!$linha->totalizadora || empty($linha->somar)) {
                continue;
            }

            $this->somarLinha($linha, $linha->somar);
        }
    }

    protected function totalizarSubtracaoLinhas()
    {
        foreach ($this->linhas as $linha) {
            if (!$linha->totalizadora || empty($linha->subtrair)) {
                continue;
            }

            $this->subtraiLinha($linha, $linha->subtrair);
        }
    }

    /**
     * Realiza a soma dos valores presente nas linhas
     *
     * @param stdClass $linhaTotalizar Linha a ser somada
     * @param array $somar Ordens das linhas que tem que somar
     */
    protected function somarLinha(stdClass $linhaTotalizar, array $somar)
    {
        $colunas = $linhaTotalizar->colunas;
        foreach ($somar as $idLinhaSoma) {
            $linhaSomar = $this->linhas[$idLinhaSoma];

            foreach ($colunas as $dadoColuna) {
                $linhaTotalizar->{$dadoColuna->coluna} += $linhaSomar->{$dadoColuna->coluna};
            }
        }
    }

    /**
     * Realiza a subtração dos valores presente nas linhas
     *
     * @param stdClass $linhaTotalizar Linha a ser somada
     * @param array $subtrair Ordens das linhas que tem que subtrair
     */
    protected function subtraiLinha(stdClass $linhaTotalizar, array $subtrair)
    {
        $colunas = $linhaTotalizar->colunas;
        $ordem = array_shift($subtrair); // extrai a ordem da primeira coluna a ser subtraída
        foreach ($colunas as $dadoColuna) {
            // define o valor inicial da coluna para após aplicar a subtração das demais
            $linhaTotalizar->{$dadoColuna->coluna} = $this->linhas[$ordem]->{$dadoColuna->coluna};
            foreach ($subtrair as $ordemSubtrai) {
                $linhaTotalizar->{$dadoColuna->coluna} -= $this->linhas[$ordemSubtrai]->{$dadoColuna->coluna};
            }
        }
    }

    /**
     * Inicializa as propriedades que serão usadas nos cálculos para apresentação no relatório
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
     * Valida nas colunas as contas de natureza Credora e multiplica por -1 seu valor.
     * Isso é necessário pois na PL que executa a MSC, sempre contabilizamos um valor Credor como negativo.
     * Dessa forma simplificamos os calculos executados, porem na hora de apresentar as contas credoras, devemos
     * apresentar como positiva se a natureza permanece Credora e Negativa quando Devedora
     * @return void
     */
    protected function alteraSinalContas()
    {
        foreach ($this->linhas as $linha) {
            foreach ($linha->colunas as $coluna) {
                if (isset($coluna->natureza) && $coluna->natureza === 'C') {
                    $linha->{$coluna->coluna} *= -1;
                }
            }
        }
    }

    /**
     * Busca as linhas manuais e soma os valores na respectiva linha e coluna
     * @return void
     */
    protected function calculaLinhasManuais()
    {
        $this->identificaLinhasManuais();
        $valoresManuais = $this->getValoresManuais();
        $valoresManuais->each(function ($valorManual) {
            $coluna = $valorManual['c180_coluna'];
            $ordem = $valorManual['c180_linha'];
            $this->linhas[$ordem]->{$coluna} += $valorManual['c180_valor'];
        });
    }

    /**
     * Retorna um array com os caminhos de emissão do relatório:
     * No array deve conter os index:
     *  - xls
     *  - xlsLinkExterno
     *  - pdf
     *  - pdfLinkExterno
     * @return []
     */
    abstract public function emitir();

    /**
     * Deve retornar o nome do anexo
     * @return string
     */
    abstract public function getNome();

    /**
     * Busca o template e carrega a versão do Parser correto para o relatório em emissão
     * @return void
     */
    abstract protected function carregarParserXls();
}
