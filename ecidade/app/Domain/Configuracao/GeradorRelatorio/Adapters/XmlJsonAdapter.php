<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Adapters;

use App\Domain\Configuracao\GeradorRelatorio\Models\Relatorio;
use App\Domain\Configuracao\Helpers\StorageHelper;

class XmlJsonAdapter
{
    /**
     * @var Relatorio
     */
    private $relatorio;

    /**
     * @var string
     */
    private $sql;

    /**
     * @var string
     */
    private $view;

    /**
     * @param Relatorio $relatorio
     * @return void
     */
    public function setRelatorio(Relatorio $relatorio)
    {
        $this->relatorio = $relatorio;
    }

    /**
     * @param $sql
     * @return void
     */
    public function setSql($sql)
    {
        $this->sql = $sql;
    }

    /**
     * @param $view
     * @return void
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getJson()
    {
        $this->requireDependencies();
        $xml = new \dbGeradorRelatorio($this->relatorio ? $this->relatorio->db63_sequencial : null);

        if ($this->sql) {
            $xml->setOrigemRelatorio(1);
            $xml->addSqlFrom(str_replace(';', '', $this->sql));
            $xml->verificaVariaveisConsulta();
        } elseif ($this->view) {
            $xml->setOrigemRelatorio(2);
            $xml->addSqlFrom($this->view);
        }

        return [
            'tipo' => $this->buildTipo(),
            'grupo' => $this->buildGrupo(),
            'template' => $this->restoreTemplate(),
            'origem' => $xml->getOrigemRelatorio(),
            'isOrigemSql' => $xml->getOrigemRelatorio() == 1,
            'campos' => $this->buildCampos($xml->getDadosCampos(), $xml->getColunas() ?: []),
            'ordem' => $this->buildOrdem($xml->getDadosCampos(), $xml->getOrdem()),
            'filtros' => $this->buildFiltros($xml->getFiltros()),
            'layout' => $this->buildLayout($xml->getPropriedades()),
            'variaveis' => $this->buildVariaveis($xml->getVariaveis()),
            'sql' => $xml->getSqlFrom('Principal')
        ];
    }

    /**
     * @return void
     */
    private function requireDependencies()
    {
        require_once modification('libs/db_stdlib.php');
        require_once modification('dbforms/db_funcoes.php');
        require_once modification('model/dbPropriedadeRelatorio.php');
        require_once modification('model/dbVariaveisRelatorio.php');
        require_once modification('model/dbColunaRelatorio.php');
    }

    /**
     * @return array|null
     * @throws \Exception
     */
    private function restoreTemplate()
    {
        if ($this->relatorio === null
            || $this->relatorio->template === null
            || $this->relatorio->template->db15_estorage === null
        ) {
            return null;
        }

        $arquivo = StorageHelper::downloadArquivo($this->relatorio->template->db15_estorage);
        $arquivo = str_replace(ECIDADE_PATH, '', $arquivo); // Retira o caminho absoluto do arquivo

        $fileName = 'template_' . str_replace(' ', '_', strtolower($this->relatorio->db63_nomerelatorio)) . '.docx';
        $fileName = \DBString::removerAcentuacao($fileName);
        return [
            'name' => $fileName,
            'path' => ECIDADE_REQUEST_PATH . $arquivo
        ];
    }

    /**
     * @return object|null
     * @throws \Exception
     */
    private function buildTipo()
    {
        if ($this->relatorio === null) {
            return null;
        }

        return (object)[
            'codigo' => $this->relatorio->tipo->db14_sequencial,
            'descricao' => $this->relatorio->tipo->db14_descricao
        ];
    }

    private function buildGrupo()
    {
        if ($this->relatorio === null) {
            return null;
        }

        return (object)[
            'codigo' => $this->relatorio->grupo->db13_sequencial,
            'descricao' => $this->relatorio->grupo->db13_descricao
        ];
    }

    /**
     * @param \dbColunaRelatorio[] $campos
     * @param \dbColunaRelatorio[] $camposConfigurados
     * @return object
     */
    private function buildCampos(array $campos, array $camposConfigurados)
    {
        $dados = (object)['naoConfigurados' => [], 'configurados' => []];

        foreach ($camposConfigurados as $campo) {
            $dados->configurados[$campo->getId()] = $this->buildCampo($campo);
        }

        foreach ($campos as $campo) {
            if (array_key_exists($campo->getId(), $dados->configurados)) {
                continue;
            }
            $dados->naoConfigurados[] = $this->buildCampo($campo);
        }

        $dados->configurados = array_values($dados->configurados);

        return $dados;
    }

    private function buildCampo($campo)
    {
        return (object)[
            'codigo' => $campo->getId(),
            'nome' => $campo->getNome(),
            'alias' => $campo->getAlias(),
            'largura' => $campo->getLargura(),
            'alinhamento' => $campo->getAlinhamento(),
            'alinhamentoCabecalho' => $campo->getAlinhamentoCab(),
            'mascara' => $campo->getMascara(),
            'totalizar' => $campo->getTotalizar(),
            'quebra' => (bool)$campo->getQuebra()
        ];
    }

    /**
     * @param \dbColunaRelatorio[] $campos
     * @param \dbOrdemRelatorio[][] $ordens
     * @return object
     */
    private function buildOrdem(array $campos, array $ordens)
    {
        $dados = (object)['naoConfigurados' => [], 'configurados' => []];
        foreach ($campos as $campo) {
            $ordemCampo = (object)[
                'codigo' => '',
                'nome' => $campo->getNome(),
                'alias' => $campo->getAlias(),
                'tipo' => 'asc'
            ];

            $ordemConfigurada = null;
            foreach ($ordens as $aOrdem) {
                $ordemExistente = array_filter($aOrdem, function (\dbOrdemRelatorio $ordem) use ($campo) {
                    return $ordem->getNome() === $campo->getNome();
                });

                if ($ordemExistente) {
                    $ordemConfigurada = reset($ordemExistente);
                }
            }

            if ($ordemConfigurada !== null) {
                $ordemCampo->codigo = $ordemConfigurada->getId();
                $ordemCampo->tipo = $ordemConfigurada->getAscDesc();
                $dados->configurados[] = $ordemCampo;
            } else {
                $dados->naoConfigurados[] = $ordemCampo;
            }
        }

        return $dados;
    }

    /**
     * @param \dbFiltroRelatorio[][] $filtros
     * @return array
     */
    private function buildFiltros(array $filtros)
    {
        $dados = [];
        foreach ($filtros as $aFiltro) {
            foreach ($aFiltro as $filtro) {
                $dados[] = (object)[
                    'operador' => $filtro->getOperador(),
                    'campo' => $filtro->getCampo(),
                    'condicao' => $filtro->getCondicao(),
                    'valor' => $filtro->getValor()
                ];
            }
        }

        return $dados;
    }

    /**
     * @param \dbPropriedadeRelatorio|array $propriedades
     * @return object
     */
    private function buildLayout($propriedades)
    {
        $layout = (object)[
            'nome' => '',
            'versao' => '1.0',
            'orientacao' => 'portrait',
            'formato' => 'A4',
            'layout' => 'dbseller', // validar se pode ficar assim
            'tipoSaida' => 'pdf',
            'delimitarTexto' => true,
            'imprimirCabecalho' => true,
            'margem' => (object)[
                'direita' => 20,
                'esquerda' => 20,
                'inferior' => 0,
                'superior' => 0
            ]
        ];

        if ($propriedades instanceof \dbPropriedadeRelatorio) {
            $layout->nome = $propriedades->getNome();
            $layout->versao = $propriedades->getVersao();
            $layout->orientacao = $propriedades->getOrientacao();
            $layout->formato = $propriedades->getFormato();
            $layout->layout = $propriedades->getLayout();
            $layout->tipoSaida = $propriedades->getTipoSaida();
            $layout->delimitarTexto = (bool)$propriedades->getDelimitarTexto();
            $layout->imprimirCabecalho = (bool)$propriedades->getImprimirCabecalho();
            $layout->margem->direita = $propriedades->getMargemDir();
            $layout->margem->esquerda = $propriedades->getMargemEsq();
            $layout->margem->inferior = $propriedades->getMargemInf();
            $layout->margem->superior = $propriedades->getMargemSup();
        }

        return $layout;
    }

    /**
     * @param \dbVariaveisRelatorio[] $variaveis
     * @return array
     */
    private function buildVariaveis(array $variaveis)
    {
        $dados = [];
        foreach ($variaveis as $variavel) {
            $dados[] = (object)[
                'nome' => $variavel->getNome(),
                'label' => $variavel->getLabel(),
                'default' => $variavel->getValor(),
                'tipo' => $variavel->getTipoDado(),
                'sql' => $variavel->getSql()
            ];
        }

        return $dados;
    }
}
