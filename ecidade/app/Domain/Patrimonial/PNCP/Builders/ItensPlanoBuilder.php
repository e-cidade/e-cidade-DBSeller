<?php

namespace App\Domain\Patrimonial\PNCP\Builders;

use Illuminate\Http\Request;

class ItensPlanoBuilder
{
    private $dados;


    /**
     * @return $this
     */
    public function setDados($dados)
    {
        $this->dados = $dados;
        return $this;
    }

    public function build()
    {
        return $this->buildItensPlano($this->dados);
    }

    private function buildItensPlano($itens)
    {
        $itensPlano = [];
        foreach ($itens as $item) {
            $itemBuild = (object)[
                'numeroItem' => $item['pn06_item'],
                'categoriaItemDescricao' => $this->obterDescricao($item['pn06_categoriaitem']),
                'unidadeFornecimento' => $item['m61_descr'],
                'quantidade' => $item['pn06_quantidade'],
                'descricao' => $item['pc01_descrmater'],
                'valorUnitario' => $item['pn06_valorunitario'],
                'valorTotal' => $item['pn06_valortotal'],
                'valorOrcamentoExercicio' => $item['pn06_valororcamento'],
                'unidadeRequisitante' => $item['descrdepto'],
                'dataDesejada' => $item['pn06_datadesejada'],
                'classificacaoCatalogo' => $item['pn06_classificacaocatalogo'],
                'codigoItem' => $item['pn06_codmater'],
                'classificacaoSuperiorCodigo' => $item['pn06_classificacaosuperiorcodigo'],
                'classificacaoSuperiorNome' => $item['pc03_descrgrupo'],
                'unidadeFornecimentoCodigo' => $item['pn06_unidadefornecimentocodigo']
            ];
            $itensPlano[] = $itemBuild;
        }
        return $itensPlano;
    }

    private function obterDescricao($codigo)
    {
        switch ($codigo) {
            case 1:
                $descricao = "Material";
                break;
            case 2:
                $descricao = "Serviço";
                break;
            case 3:
                $descricao = "Obras";
                break;
            case 4:
                $descricao = "Serviços de Engenharia";
                break;
            case 5:
                $descricao = "Soluções de TIC";
                break;
            case 6:
                $descricao = "Locação de Imóveis";
                break;
            case 7:
                $descricao = "Alienação/Concessão/Permissão";
                break;
            default:
                $descricao = "Código não reconhecido";
                break;
        }

        return $descricao;
    }
}
