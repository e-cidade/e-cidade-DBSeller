<?php

namespace App\Domain\Saude\Laboratorio\Services;

class ResultadoExameService
{
    /**
     * @param integer $idRequisicaoExame
     * @return object
     * @throws \Exception
     */
    public function getResultado($idRequisicaoExame, $idPaciente)
    {
        $requisicaoExame = new \RequisicaoExame((int)$idRequisicaoExame);

        if ($requisicaoExame->getCodigo() === null) {
            throw new \BusinessException('Não existem requisições de exame com o código informado.', 409);
        }

        if ($requisicaoExame->getSolicitante()->getCodigo() != $idPaciente) {
            throw new \Exception('Requisição de exame não pertence ao paciente informado.', 401);
        }

        if (!in_array($requisicaoExame->getSituacao(), [\RequisicaoExame::CONFERIDO, \RequisicaoExame::ENTREGUE])) {
            throw new \BusinessException('O resultado ainda não foi conferido.', 409);
        }

        $resultado = null;
        $resultadoExame = $requisicaoExame->getResultado();
        // pré carrega o resultado anterior, caso exista.
        $resultadoExame->getResultadoAnterior();

        $dataExameAnterior = $resultadoExame->getDataResultadoAnterior();
        if ($dataExameAnterior !== null) {
            $dataExameAnterior = $dataExameAnterior->convertTo('Y-m-d');
        }
        $resultadoAtributos = $resultadoExame->getResultadoCompleto();
        foreach ($resultadoAtributos as $resultadoAtributo) {
            $atributo = $resultadoAtributo->getAtributo();
            $atributoExame = (object)[
                'nome' => $atributo->getNome(),
                'nivel' => $atributo->getNivel(),
                'tipo' => $atributo->getDescricaoTipo()
            ];

            $unidade = $atributo->getUnidadeMedida() != "" ? $atributo->getUnidadeMedida()->getNome() : '';
            if ($unidade !== '') {
                $atributoExame->unidade = $unidade;
            }

            if ($atributo->getNivel() == 1) {
                $atributoExame->dataResultadoAnterior = $dataExameAnterior;
            }

            $atributoFormatado = $this->formataResultadoAtributo(
                $resultadoAtributo,
                $requisicaoExame,
                $atributo->getTipoReferencia()
            );

            $atributoExame->valorAbsoluto = $atributoFormatado->valorAbsoluto;
            $atributoExame->valorPercentual = $atributoFormatado->valorPercentual;
            $atributoExame->referencia = $atributoFormatado->referencia;
            $atributoExame->titulacao = $atributoFormatado->titulacao;

            $resultadoAnterior = $resultadoExame->getValorDoAtributoResultadoAnterior($atributo);

            if (!empty($resultadoAnterior)) {
                $atributoFormatado = $this->formataResultadoAtributo(
                    $resultadoAnterior,
                    $requisicaoExame,
                    $atributo->getTipoReferencia()
                );

                $atributoExame->valorAbsolutoAnterior = $atributoFormatado->valorAbsoluto;
                $atributoExame->valorPercentualAnterior = $atributoFormatado->valorPercentual;
                $atributoExame->referenciaAnterior = $atributoFormatado->referencia;
                $atributoExame->titulacaoAnterior = $atributoFormatado->titulacao;
            }

            $this->organizaAtributos($atributo, $atributoExame, $resultado);
        }

        return $this->formataResultado($resultado);
    }

    /**
     * @param \ResultadoExameAtributo $resultadoAtributo
     * @param \RequisicaoExame $requisicaoExame
     * @param integer $tipoReferencia
     * @return \stdClass
     */
    private function formataResultadoAtributo(
        \ResultadoExameAtributo $resultadoAtributo,
        \RequisicaoExame $requisicaoExame,
        $tipoReferencia
    ) {
        $atributoExame = new \stdClass();
        $atributoExame->valorAbsoluto = $resultadoAtributo->getValorAbsoluto();
        $atributoExame->valorPercentual = $resultadoAtributo->getValorPercentual();
        $atributoExame->titulacao = $resultadoAtributo->getTitulacao();
        $atributoExame->referencia = '';

        switch ($tipoReferencia) {
            case \AtributoExame::REFERENCIA_NUMERICA:
                $referencia = $resultadoAtributo->getFaixaUtilizada();

                if (!empty($referencia) && $referencia->getCodigo() == '') {
                    $referencia = $resultadoAtributo->getAtributo()->getValoresDeReferenciaParaExame($requisicaoExame);
                }

                $decimais = null;
                if ($referencia instanceof \AtributoValorReferenciaNumerico) {
                    $decimais = $referencia->getCasasDecimaisApresentacao();
                }

                $atributoExame->valorAbsoluto = \MascaraValorAtributoExame::mascarar(
                    $decimais,
                    $atributoExame->valorAbsoluto
                );

                if ($referencia != '') {
                    $valorMinimo = \MascaraValorAtributoExame::mascarar($decimais, $referencia->getValorMinimo());
                    $valorMaximo = \MascaraValorAtributoExame::mascarar($decimais, $referencia->getValorMaximo());

                    $atributoExame->referencia = "({$valorMinimo} - {$valorMaximo})";
                }
                break;

            case \AtributoExame::REFERENCIA_SELECIONAVEL:
                $dao = new \cl_lab_valorreferenciasel();
                $sql = $dao->sql_query_file($resultadoAtributo->getValorAbsoluto());
                $rs = $dao->sql_record($sql);

                if ($dao->numrows > 0) {
                    $atributoExame->valorAbsoluto = \db_utils::fieldsMemory($rs, 0)->la28_c_descr;
                }
                break;

            case \AtributoExame::REFERENCIA_FIXA:
                $atributoExame->valorAbsoluto = $resultadoAtributo->getValorAbsoluto();
                break;
        }

        return $atributoExame;
    }

    /**
     * Organiza os atributos conforme o estrutural do mesmo
     * @param \AtributoExame $atributo
     * @param object $atributoFormatado
     * @param object|null $resultado
     */
    private function organizaAtributos(\AtributoExame $atributo, $atributoFormatado, &$resultado)
    {
        if ($resultado === null) {
            $resultado = $atributoFormatado;
        }

        if ($resultado->nivel > $atributoFormatado->nivel) {
            $aux = $resultado;
            $resultado = $atributoFormatado;
            $resultado->atributos = $aux;
        }

        $atributoPai = $resultado;

        $estrutural = explode('.', $atributo->getEstrutural());
        for ($i = 1; $i < $atributo->getNivel(); $i++) {
            if (!property_exists($atributoPai, 'atributos')) {
                $atributoPai->atributos = [];
            }
            if (!array_key_exists($estrutural[$i], $atributoPai->atributos)) {
                $atributoPai->atributos[$estrutural[$i]] = $atributoFormatado;
                continue;
            }
            $atributoPai = $atributoPai->atributos[$estrutural[$i]];
        }
    }

    /**
     * Retira as chaves do array recursivamente
     * @param object $resultado
     * @return object
     */
    private function formataResultado($resultado)
    {
        if ($resultado === null) {
            return null;
        }

        if (!property_exists($resultado, 'atributos')) {
            return $resultado;
        }

        foreach ($resultado->atributos as $key => $atributo) {
            $resultado->atributos[$key] = $this->formataResultado($atributo);
        }

        $resultado->atributos = array_values($resultado->atributos);
        return $resultado;
    }
}
