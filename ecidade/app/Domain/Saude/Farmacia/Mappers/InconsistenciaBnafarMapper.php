<?php

namespace App\Domain\Saude\Farmacia\Mappers;

class InconsistenciaBnafarMapper
{
    /**
     * @var object
     */
    private $dadosEnviados;

    /**
     * @var array
     */
    private $inconsistencias;

    private $erros = [
        /**
         * O estabelecimento informado não consta no CNES.
         */
        'MSG06' => 'id_estabelecimento',
        /**
         * O CNPJ não consta no cadastro da Receita Federal.
         */
        'MSG12' => 'id_estabelecimento',
        /**
         * O Tipo de Entrada é inválido.
         */
        'MSG14' => 'movimentacao',
        /**
         * O Tipo de Saída é inválido
         */
        'MSG21' => 'movimentacao',
        /**
         * Para o usuarioSus o CNS não foi encontrado no CADSUS.
         */
        'MSG24' => 'cns_paciente',
        /**
         * Para o usuarioSus o CPF não foi encontrado na base de dados da Receita Federal.
         */
        'MSG33' => 'cpf_paciente',
        /**
         * Para o usuarioSus é obrigatório informar pelo menos o CPF ou CNS.
         */
        'MSG55' => 'cpf_paciente',
    ];

    /**
     * Erros relacionados ao medicamento
     * @var array
     */
    private $errosItem = [
        /**
         * Para o itens[<<posição do registro>>].numero o Número do Produto é inválido.
         */
        'MSG09' => 'numero_produto',
        /**
         * O CNPJ não consta no cadastro da Receita Federal.
         */
        'MSG12' => 'id_fabricante',
    ];

    /**
     * Erros relacionados a campos sem necessidade de validação e/ou não exportados
     * @var array
     */
    private $errosNaoMapeados = [
        /**
         * Para o itens[<<posição do registro>>].siglaProgramaSaude o Programa de Saúde é inválido.
         */
        'MSG10',
        /**
         * Para o itens[<<posição do registro>>] os campos CNPJ do Fabricante e Fabricante Internacional não podem
         * ser preenchidos simultaneamente e/ou não foram informados.
         */
        'MSG13',
        /**
         * O registro já consta na base de dados com o identificador <<código registro>>.
         */
        'MSG15',
        /**
         * O prazo para inclusão desse registro foi expirado em <<data limite>>.
         */
        'MSG16',
        /**
         * O prazo para reclusão desse registro foi expirado em <<data limite>>.
         */
        'MSG17',
        /**
         * O prazo para exclusão desse registro foi expirado em <<data limite>>.
         */
        'MSG18',
        /**
         * Protocolo não encontrado.
         */
        'MSG19',
        /**
         * O campo itens[<<posição do registro>>].<<campo>> é de preenchimento obrigatório
         * quando o produto é do tipo especializado
         */
        'MSG27',
        /**
         * O CID-10 do itens[<<posição do registro>>].cid10 é inválido
         */
        'MSG29',
        /**
         * Para o procedimentos[<<posição do registro>>].numero o Número do Procedimento não foi encontrado.
         */
        'MSG30',
        /**
         * Para o itens[<<posição do registro>>].posologia.unidadeDose[asd] a Unidade da Dose informada é inválida
         */
        'MSG34',
        /**
         * Informe somente um parâmetro além do Ente Federativo
         * para a requisição (lista de itens ou código do protocolo).
         */
        'MSG38',
        /**
         * Há óbito declarado para <<campo>> informado.
         */
        'MSG40',
        /**
         * Para o itens[<<posição do registro>>].profissionalPrescritor os campos CNS/CPF e Número de Registro
         * Profissional/UF não podem ser preenchidos simultaneamente e/ou não foram informados.
         */
        'MSG42',
        /**
         * O Serviço <<nome do portfólio>> está indisponível.
         */
        'MSG44',
        /**
         * O Ente Federativo informado não é o mesmo do(s) dado(s) cadastrado(s).
         */
        'MSG51',
        /**
         * Identificador do registro no corpo da mensagem é diferente da url.
         */
        'IDNOTVALID'
    ];

    /**
     * @param object $dadosEnviados
     * @param array $inconsistencias
     */
    public function __construct($dadosEnviados, $inconsistencias)
    {
        $this->dadosEnviados = $dadosEnviados;
        $this->inconsistencias = $inconsistencias;
    }

    /**
     * @return array
     */
    public function get()
    {
        $erros = [];
        foreach ($this->inconsistencias as $inconsistencia) {
            $idEstoqueItem = $this->getIdEstoqueItem($inconsistencia);
            $erro = (object)[
                'campo' => $this->getCampo($inconsistencia, $idEstoqueItem),
                'idMovimentacao' => $this->dadosEnviados->caracterizacao->codigoOrigem,
                'descricao' => utf8_decode($inconsistencia->mensagem) . $this->getAjuda($inconsistencia),
                'idEstoqueItem' => $idEstoqueItem
            ];

            if (property_exists($inconsistencia, 'valorRejeitado')) {
                $erro->valorRejeitado = $inconsistencia->valorRejeitado;
            }

            $erros[] = $erro;
        }

        return $erros;
    }

    /**
     * @param object $inconsistencia
     * @return string|null
     */
    private function getCampo($inconsistencia, $idEstoqueItem)
    {
        if (!array_key_exists($inconsistencia->codigo, $this->erros)
            && !array_key_exists($inconsistencia->codigo, $this->errosItem)) {
            return null;
        }

        $dePara = $this->erros;
        if (array_key_exists($inconsistencia->codigo, $this->errosItem) && $idEstoqueItem !== null) {
            $dePara = $this->errosItem;
        }

        return $dePara[$inconsistencia->codigo];
    }

    private function getAjuda($inconsistencia)
    {
        $msg09 = sprintf(
            'Para corrigir informe corretamente %s no campo %s no %s',
            'o código CATMAT',
            'Farmácia Básica',
            'Cadastro de Medicamentos'
        );
        $ajudas = [
            'MSG09' => $msg09
        ];

        if (!array_key_exists($inconsistencia->codigo, $ajudas)) {
            return '';
        }

        return ' ' . $ajudas[$inconsistencia->codigo];
    }

    private function getIdEstoqueItem($inconsistencia)
    {
        if (!array_key_exists($inconsistencia->codigo, $this->errosItem)) {
            return null;
        }

        $matches = [];
        if (!preg_match('/(\[[0-9]+)/', $inconsistencia->mensagem, $matches)) {
            return null;
        }

        $index = substr($matches[0], 1);
        if (!array_key_exists($index, $this->dadosEnviados->itens)) {
            return null;
        }

        $item = $this->dadosEnviados->itens[$index];

        return $item->codigoOrigem;
    }
}
