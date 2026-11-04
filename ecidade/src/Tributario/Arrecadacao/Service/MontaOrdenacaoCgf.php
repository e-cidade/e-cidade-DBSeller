<?php

namespace ECidade\Tributario\Arrecadacao\Service;

use App\Domain\Tributario\Arrecadacao\Repositories\OrdenacaoCgfRepository;
use ECidade\Tributario\Arrecadacao\Enums\TipoCampoOrdenacaoCgfEnum;
use ECidade\Tributario\Arrecadacao\Enums\TipoOrdenacaoCgfEnum;
use ECidade\Tributario\Arrecadacao\Model\OrdenacaoCgf;
use DateTime;

class MontaOrdenacaoCgf
{
    /** @var int */
    private $grupoDebito;

    /** @var array */
    private $campos = [];

    /**
     * @param int $grupoDebito
     * @return void
     */
    public function setGrupoDebito($grupoDebito)
    {
        $this->grupoDebito = $grupoDebito;
    }

    /**
     * @param string $nome
     * @param int $tipoOrdenacao
     * @param int|null $tipoCampo
     * @return void
     */
    public function addCampo($nome, $tipoOrdenacao, $tipoCampo = null)
    {
        $this->campos[] = (object) [
            'nome' => $nome,
            'tipoOrdenacao' => $tipoOrdenacao,
            'tipoCampo' => $tipoCampo,
        ];
    }

    /**
     * @return string
     */
    public function montaSqlOrderBy()
    {
        $sql = '';

        // busca das ordenacoes cadastradas no banco de dados para esse tipo de debito
        $ordenacoes = $this->buscarOrdenacoesTipoDebito();

        $numeroOrdenacoes = count($ordenacoes);
        $numeroCampos = count($this->campos);
        if ($numeroOrdenacoes == 0 || $numeroCampos == 0) {
            // caso nao existam ordenacoes nao existe order by
            return '';
        }

        // para cada ordenacao da lista: busca o nome do campo e concatena na string sql order by
        for ($index = 0; $index < $numeroOrdenacoes; $index++) {
            $nomeCampo = $this->buscaNomeCampoPorTipo($ordenacoes[$index]);
            if (isset($nomeCampo)) {
                $sql .= " {$nomeCampo},";
            }
        }

        return rtrim($sql, ',');
    }

    /**
     * @param array $array
     * @return array
     */
    public function ordenarArray($array)
    {
        $arrayOrdenado = $array;

        // busca das ordenacoes cadastradas no banco de dados para esse tipo de debito
        $ordenacoes = $this->buscarOrdenacoesTipoDebito();

        $numeroOrdenacoes = count($ordenacoes);
        $numeroCampos = count($this->campos);
        if ($numeroOrdenacoes == 0 || $numeroCampos == 0) {
            // caso nao existam ordenacoes
            return '';
        }

        // para cada ordenacao da lista cadastrada no banco de dados
        for ($index = ($numeroOrdenacoes - 1); $index >= 0; $index--) {
            // busca o nome e o tipo de campo do array de objetos referenciado pelo indice da ordenacao
            $campoReferenciado = array_filter($this->campos, function ($campo) use ($ordenacoes, $index) {
                return $campo->tipoOrdenacao == $ordenacoes[$index];
            });

            if (count($campoReferenciado) == 0) {
                continue;
            }

            $campoReferenciado = current($campoReferenciado);

            dump($campoReferenciado);

            // ordenacao do array de acordo com o campo referenciado
            usort($arrayOrdenado, function ($a, $b) use ($campoReferenciado) {
                switch ($campoReferenciado->tipoCampo) {
                    case TipoCampoOrdenacaoCgfEnum::DATA:
                        $campoA = DateTime::createFromFormat('Y-m-d', $a->{$campoReferenciado->nome})->format('Y-m-d');
                        $campoB = DateTime::createFromFormat('Y-m-d', $b->{$campoReferenciado->nome})->format('Y-m-d');
                        break;
                    case TipoCampoOrdenacaoCgfEnum::NUMERO:
                        $campoA = $a->{$campoReferenciado->nome};
                        $campoB = $b->{$campoReferenciado->nome};
                        break;
                }

                if ($campoA > $campoB) {
                    return 1;
                }
                if ($campoA < $campoB) {
                    return -1;
                }
                return 0;
            });
        }

        return $arrayOrdenado;
    }

    /**
     * @return array
     */
    private function buscarOrdenacoesTipoDebito()
    {
        $ordenacaoDebitosRepository = new OrdenacaoCgfRepository();
        $ordenacoes = $ordenacaoDebitosRepository->getByGrupoDebito($this->grupoDebito);

        return array_map(function (OrdenacaoCgf $grupo) {
            return TipoOrdenacaoCgfEnum::getEnumByTipo($grupo->getTipo());
        }, $ordenacoes);
    }

    /**
     * @param int|null $tipo
     * @return string
     */
    private function buscaNomeCampoPorTipo($tipo)
    {
        foreach ($this->campos as $campo) {
            if ($campo->tipoOrdenacao == $tipo) {
                return $campo->nome;
            }
        }
        return null;
    }
}
