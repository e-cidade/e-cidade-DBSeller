<?php

namespace App\Domain\Patrimonial\PNCP\Resources;

use App\Domain\Patrimonial\Compras\Models\Solicitacao;
use App\Domain\Patrimonial\Licitacoes\Models\Licitacao;
use App\Domain\Patrimonial\PNCP\Services\CompraEditalAvisoService;
use Illuminate\Support\Collection;
use ItemLicitacao;

class CompraEditalAvisoResource
{
    /**
     * @param Licitacao $licitacao
     * @param $orcamentoSigiloso
     * @param $itens
     * @return object
     */

    const NAO_GERA_DESPESA = '2';
    public static function toResponse(Licitacao $licitacao, $orcamentoSigiloso, $itens, $dados = '')
    {
        $dadosLicitacao = new \licitacao($licitacao->l20_codigo);
        $processo = $dadosLicitacao->getProcessoProtocolo();
        $processProtocolo = $licitacao->l20_procadmin;

        if (!empty($processo)) {
            $processProtocolo = $processo->getNumeroProcesso() . "/" . $processo->getAnoProcesso();
        }
        return (object)[
            'homologacao' => $dadosLicitacao->getSituacao()->getSDescricao(),
            'numeroCompra' => $licitacao->l20_numero,
            'anoCompra' => $licitacao->l20_anousu,
            'numeroProcesso' => $processProtocolo,
            'objetoCompra' => $licitacao->l20_objeto,
            'sistemaRegistroPreco' => $licitacao->l20_usaregistropreco,
            'dataAberturaProposta' => $licitacao->l20_dataaber,
            'horaAberturaProposta' => $licitacao->l20_horaaber,
            'orcamentoSigiloso' => $orcamentoSigiloso,
            'justificativaPresencial' => '',
            'itens' => self::itensToResponse($itens, $licitacao->l20_codigo, $orcamentoSigiloso),
            'resultadoItem' => $dados,
            'link' => !empty($dados['link']) ? $dados['link'] : '',
            'sequencialCompra' => !empty($dados['sequencialCompra']) ? $dados['sequencialCompra'] : ''
        ];
    }

    /**
     * @param ItemLicitacao[] $itens
     * @return array
     */
    public static function itensToResponse($itens, $licitacao, $orcamentoSigiloso)
    {
        $model = new \licitacao($licitacao);
        if ($model->getSituacao()->getSDescricao() === 'Homologada') {
            $dataHomologacao = date('d/m/Y', $model->getDataHomologacao()->getTimeStamp());
        }
        if (!empty($itens)) {
            foreach ($itens as $item) {
                $itemSolicitacao = $item->getItemSolicitacao();
                $materialOuServico = $itemSolicitacao->servico() ? 'S' : 'M';
                $resumo = preg_replace('/\s+/', ' ', urldecode($itemSolicitacao->getResumo()));
                $resumoItem = utf8_encode(str_replace("''", '', $resumo));
                $descricaoMaterial = utf8_encode(urldecode($itemSolicitacao->getDescricaoMaterial()));

                $dadosFornecedor = CompraEditalAvisoService::getFornecedorJulgado(
                    $item->getLicitacao()->getCodigo(),
                    $item->getCodigo()
                );
                $unidadeMedida = urldecode($itemSolicitacao->getUnidadeDescr());
                if (urldecode($itemSolicitacao->getUnidadeDescr()) === "" && $materialOuServico === "S") {
                    $unidadeMedida = "SERVIÇO";
                }

                $dados[] = (object)[
                    'numeroItem' => $item->getOrdem(),
                    'materialOuServico' => $materialOuServico,
                    'tipoBeneficioId' => self::getTipoBeneficio(
                        $model->obterTipoBeneficioMicroempresaEmpresaPequenoPorte()
                    ),
                    'criterioJulgamentoId' => self::getTipoJulgamento($model->obterTipoLicitacao()),
                    'indicadorSubcontratacao' => self::getSubcontratacao($model->getSubcontratacao()),
                    'incentivoProdutivoBasico' => "0",
                    'orcamentoSigiloso' => $orcamentoSigiloso === 'f' ? 'false' : 'true',
                    'itemCategoriaId' => "0",
                    'codigoRegistroImobiliario' => '',
                    'percentualMargemPreferenciaAdicional' => '',
                    'percentualMargemPreferenciaNormal' => '',
                    'descricao' => $descricaoMaterial,
                    'resumo' => stripslashes($resumoItem),
                    'quantidade' => $itemSolicitacao->getQuantidade(),
                    'unidadeMedida' => $unidadeMedida,
                    'valorUnitarioEstimado' => self::getValoresEstimadoHomologado(
                        $item->getCodigo(),
                        $item->getLicitacao()->getCodigo()
                    )->unitario_estimado,
                    'valorTotalEstimado' => self::getValoresEstimadoHomologado(
                        $item->getCodigo(),
                        $item->getLicitacao()->getCodigo()
                    )->total_estimado,
                    'valorUnitarioHomologado' => self::getValoresEstimadoHomologado(
                        $item->getCodigo(),
                        $item->getLicitacao()->getCodigo()
                    )->unitario_homologado,
                    'valorTotalHomologado' => self::getValoresEstimadoHomologado(
                        $item->getCodigo(),
                        $item->getLicitacao()->getCodigo()
                    )->total_homologado,
                    'dadosFornecedor' => $dadosFornecedor,
                    'situacao' =>  $model->getSituacao()->getSDescricao(),
                    'dataHomologacao' => isset($dataHomologacao) ? $dataHomologacao : '',
                ];
            }

            return $dados;
        }
        return [];
    }

    private static function getValoresEstimadoHomologado($iCodigoItemLicitacao, $licitacao)
    {
        $licitacao = new \licitacao($licitacao);

        $oStdValorHomologado = new \stdClass();
        $oStdValorHomologado->total_estimado = null;
        $oStdValorHomologado->unitario_estimado = null;
        $oStdValorHomologado->total_homologado = null;
        $oStdValorHomologado->unitario_homologado = null;

        $oOrcamentoLicitacao = new \OrcamentoLicitacao($licitacao);
        $oOrcamentoLicitacao->setCodigoItem($iCodigoItemLicitacao);


        $oStdValorHomologado->total_estimado = number_format(
            $oOrcamentoLicitacao->getValorTotalEstimado(),
            2,
            '.',
            ''
        );
        $oStdValorHomologado->total_homologado = number_format(
            $oOrcamentoLicitacao->getValorTotalHomologado(),
            2,
            '.',
            ''
        );
        $oStdValorHomologado->unitario_estimado = number_format(
            $oOrcamentoLicitacao->getValorUnitarioEstimado(),
            2,
            '.',
            ''
        );
        $oStdValorHomologado->unitario_homologado = number_format(
            $oOrcamentoLicitacao->getValorUnitarioHomologado(),
            2,
            '.',
            ''
        );
        if ($licitacao->getDados()->l20_tipo !== self::NAO_GERA_DESPESA) {
            if ($oStdValorHomologado->total_estimado === "0.00" || $oStdValorHomologado->unitario_estimado === "0.00") {
                throw new \Exception("Não foi possível buscar o valor de referência.
            Verifique se existe orçamento para o processo de compra!");
            }
        };

        return $oStdValorHomologado;
    }

    private static function getTipoBeneficio($tipoBeneficio)
    {
        $result = '';
        switch ($tipoBeneficio) {
            case "L":
                $result =  "1";
                break;
            case "S":
                $result = "2";
                break;
            case "N":
                $result = "5";
                break;
            default:
                $result = "0";
        }
        return $result;
    }

    private static function getTipoJulgamento($tipoJulgamento)
    {
        $result = '';
        switch ($tipoJulgamento) {
            case "MPR":
                $result = "1";
                break;
            case "MDE":
                $result = "2";
                break;
            case "MCA":
            case "MTC":
                $result = "3";
                break;
            case "TPR":
                $result = "4";
                break;
            case "MLO":
                $result = "5";
                break;
            case "MRE":
                $result = "6";
                break;
            case "NSA":
                $result = "7";
                break;
            default:
                $result = "0";
        }

        return $result;
    }

    private static function getSubcontratacao($subcontratacao)
    {
        if ($subcontratacao === '' || $subcontratacao === null) {
            return '0';
        }

        return $subcontratacao;
    }
}
