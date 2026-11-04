<?php

namespace ECidade\Configuracao\RelatorioLegal\Servico;

use DOMDocument;
use DOMNodeList;
use stdClass;

class ParseConfiguracaoXml
{
    private $linha;
    /**
     * @var integer
     */
    private $exercicio;

    public function __construct($linha, $exercicio)
    {
        $this->linha = $linha;
        $this->exercicio = $exercicio;
    }

    public function parse($stringXml)
    {
        $filtro = $this->montaStdClassFiltros();

        $oDomXml = new DOMDocument();
        $oDomXml->loadXML($stringXml);

        $filtro->contas = $this->processaContas($oDomXml->getElementsByTagName("conta"));
        $filtro->contasConformeConfiguracao = $this->processaConformeConfiguracao(
            $oDomXml->getElementsByTagName("conta")
        );
        $this->parseVinculoOrcamento($oDomXml, $filtro);

        return $filtro;
    }

    /**
     * @param DOMDocument $oDomXml
     * @param stdClass $filtro
     */
    private function parseVinculoOrcamento(DOMDocument $oDomXml, stdClass $filtro)
    {
        $orgao = $oDomXml->getElementsByTagName("orgao");

        $filtro->orgao = $this->parseValoresVinculoOrcamento($orgao);

        $unidade = $oDomXml->getElementsByTagName("unidade");
        $filtro->unidade = $this->parseValoresVinculoOrcamento($unidade);

        $funcao = $oDomXml->getElementsByTagName("funcao");
        $filtro->funcao = $this->parseValoresVinculoOrcamento($funcao);

        $subFuncao = $oDomXml->getElementsByTagName("subfuncao");
        $filtro->subfuncao = $this->parseValoresVinculoOrcamento($subFuncao);

        $programa = $oDomXml->getElementsByTagName("programa");
        $filtro->programa = $this->parseValoresVinculoOrcamento($programa);

        $projeto = $oDomXml->getElementsByTagName("projativ");
        $filtro->projativ = $this->parseValoresVinculoOrcamento($projeto);

        $recurso = $oDomXml->getElementsByTagName("recurso");

        if ($recurso->length > 0) {
            $filtro->codigoRecurso = $this->parseValoresVinculoOrcamento($recurso);
        }

        $fonteRecurso = $oDomXml->getElementsByTagName("fonterecurso");
        if ($fonteRecurso->length > 0) {
            $filtro->fonteRecurso = $this->parseValoresVinculoOrcamento($fonteRecurso);
        }

        $complemento = $oDomXml->getElementsByTagName("complemento");
        if ($complemento->length > 0) {
            $filtro->complemento = $this->parseValoresVinculoOrcamento($complemento);
        }

        $cp = $oDomXml->getElementsByTagName("caracteristica");
        if ($cp->length > 0) {
            $filtro->caracteristica = $this->parseValoresVinculoOrcamento($cp);
        }
    }

    private function parseValoresVinculoOrcamento(DOMNodeList $item)
    {
        $valor = $item->item(0)->getAttribute("valor");
        $valores = [];
        if ($valor !== '') {
            $valores = array_map(function ($v) {
                return trim($v);
            }, explode(',', $valor));
        }
        return (object)[
            'operador' => $item->item(0)->getAttribute("operador"),
            'valores' => $valores
        ];
    }

    /**
     * @param $estrutural
     * @return array|mixed|null
     * @todo só esta realizado implementado a Receita e Despesa
     *
     *
     */
    public function buscarContas($estrutural, $indicadorSuperavit = null)
    {
        $ateNivel = estruturalAteNivel($estrutural);
        switch ($this->linha->origem) {
            case 1:
                return $this->getEstruturaisAnaliticosReceita($ateNivel);
            case 2:
            case 4:
                return $this->getEstruturaisDespesa($ateNivel);
            case 3:
                return $this->getEstruturaisVerificacao($ateNivel, $indicadorSuperavit);
            default:
                return [$estrutural];
        }
    }

    /**
     * Retorna os estruturais da receita compatíveis com o configurado na linha
     * @param $ateNivel
     * @return array
     */
    private function getEstruturaisAnaliticosReceita($ateNivel)
    {
        $exercicio = $this->exercicio - 1;
        $receitas = receitasAnaliticas($exercicio, ' >= ');
        return $this->matchEstrutural($receitas, $ateNivel);
    }

    /**
     * Retorna os estruturais da despesa compatíveis com o configurado na linha
     * @param $ateNivel
     * @return array
     */
    private function getEstruturaisDespesa($ateNivel)
    {
        $elementos = todosElementosDespesa();
        return $this->matchEstrutural($elementos, $ateNivel);
    }

    /**
     * Retorna os estruturais do balancete de verificação compatíveis com o configurado na linha
     * @param $ateNivel
     * @param $indicadorSuperavit
     * @return array
     */
    private function getEstruturaisVerificacao($ateNivel, $indicadorSuperavit = null)
    {
        $contas = contasBalanceteVerificacao($this->exercicio - 1, '>=', $indicadorSuperavit);
        return $this->matchEstrutural($contas, $ateNivel);
    }

    /**
     * @param $contas
     * @param $ateNivel
     * @return array
     */
    private function matchEstrutural($contas, $ateNivel)
    {
        return array_filter($contas, function ($estrutural) use ($ateNivel) {
            return strpos($estrutural, $ateNivel) === 0;
        });
    }

    /**
     * Re
     * @param $contasProcessadas
     * @param $estrutural
     */
    private function excluirContas(&$contasProcessadas, $estrutural)
    {
        $ateNivel = estruturalAteNivel($estrutural);

        $contasProcessadas = array_filter($contasProcessadas, function ($natureza) use ($ateNivel) {
            return strpos($natureza, $ateNivel) !== 0;
        });
    }

    /**
     * @return stdClass
     */
    private function montaStdClassFiltros()
    {
        $filtro = new stdClass();
        $filtro->contas = [];
        $filtro->contasConformeConfiguracao = [];
        $filtro->orgao = new stdClass();
        $filtro->orgao->operador = 'in';
        $filtro->orgao->valor = '';

        $filtro->unidade = new stdClass();
        $filtro->unidade->operador = 'in';
        $filtro->unidade->valor = '';

        $filtro->funcao = new stdClass();
        $filtro->funcao->operador = 'in';
        $filtro->funcao->valor = '';

        $filtro->subfuncao = new stdClass();
        $filtro->subfuncao->operador = 'in';
        $filtro->subfuncao->valor = '';

        $filtro->programa = new stdClass();
        $filtro->programa->operador = 'in';
        $filtro->programa->valor = '';

        $filtro->projativ = new stdClass();
        $filtro->projativ->operador = 'in';
        $filtro->projativ->valor = '';

        $filtro->codigoRecurso = new stdClass();
        $filtro->codigoRecurso->operador = 'in';
        $filtro->codigoRecurso->valor = '';

        $filtro->fonteRecurso = new stdClass();
        $filtro->fonteRecurso->operador = 'in';
        $filtro->fonteRecurso->valor = '';

        $filtro->complemento = new stdClass();
        $filtro->complemento->operador = 'in';
        $filtro->complemento->valor = '';

        $filtro->caracteristica = new stdClass();
        $filtro->caracteristica->operador = 'in';
        $filtro->caracteristica->valor = '';
        $filtro->observacao = '';
        return $filtro;
    }

    /**
     * @param $contas configuradas
     * @return array
     */
    protected function processaContas($contas)
    {
        $estruturais = [];
        // processa todas contas de inclusão
        foreach ($contas as $oConta) {
            if ($oConta->getAttribute("exclusao") != "false") {
                continue;
            }
            $estrut = $this->buscarContas($oConta->getAttribute("estrutural"), $oConta->getAttribute("indicador"));
            $estruturais = array_merge($estruturais, $estrut);
        }

        // processa todas contas de exclusão removendo do array
        foreach ($contas as $oConta) {
            if ($oConta->getAttribute("exclusao") != "true") {
                continue;
            }

            $this->excluirContas($estruturais, $oConta->getAttribute("estrutural"));
        }

        return $estruturais;
    }

    /**
     * Retorna os dados das contas como na configuração
     * @param DOMNodeList $contas
     * @return array
     */
    private function processaConformeConfiguracao(DOMNodeList $contas)
    {
        $estruturais = [];
        foreach ($contas as $oConta) {
            $estrutural = $oConta->getAttribute("estrutural");
            $estruturais[] = (object)[
                'estrutural' => $estrutural,
                'exclusao' => $oConta->getAttribute("exclusao") == "true",
                'estruturalAteNivel' => estruturalAteNivel($estrutural),
                'indicadorSuparavit' => $oConta->getAttribute("indicador")
            ];
        }
        return $estruturais;
    }
}
