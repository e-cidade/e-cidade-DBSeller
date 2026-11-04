<?php

namespace App\Domain\Patrimonial\Contratos\Services;

use Acordo;
use AcordoEvento;
use AcordoPosicao;
use AcordoRepository;
use App\Domain\Patrimonial\Contratos\Models\AcordoPosicaoPeriodo;
use cl_acordoposicao;
use cl_acordoposicaoaditamento;
use cl_acordovigencia;
use db_stdClass;
use db_utils;
use DBDate;
use DBException;
use Exception;
use stdClass;

class EnviarEventoAcordoService
{
    public function enviarEventoAutomatico($request)
    {
        $acordoPosicao = $request['acordoPosicao'];
        $novoEvento = $request['evento'];
        $itensSelecionados = $request['itensSelecionados'];
        $todosItens = $request['itens'];
        $acordo = new Acordo($acordoPosicao['ac16_sequencial']);
        $novaPosicao = new AcordoPosicao();
        $novaPosicao->setAcordo($acordoPosicao['ac16_sequencial']);
        $novaPosicao->setData(date("Y-m-d", db_getsession("DB_datausu")));
        $novaPosicao->setEmergencial(false);
        $novaPosicao->setNumero($acordo->getProximoNumeroAditamento());
        $novaPosicao->setNumeroAditamento($novoEvento['numTermo']);
        $novaPosicao->setSituacao($acordoPosicao['ac26_situacao']);
        $novaPosicao->setTipo(7); // tipo vai ser 7 por que é uma alteracao de dotacao

        $novaPosicao->setVigenciaInicial($acordoPosicao['dtVigenciaInicial']);
        $novaPosicao->setVigenciaFinal($acordoPosicao['dtVigenciaFinal']);
        $novaPosicao->setPosicaoPeriodo(
            $acordoPosicao['dtVigenciaInicial'],
            $acordoPosicao['dtVigenciaFinal'],
            $acordo->getPeriodoComercial()
        );
        $novaPosicao->setObservacao(utf8_decode($novoEvento['justificativa']));
        $novaPosicao->setTipoOperacao(null);
        $novaPosicao->save();

        $acordoEvento = new AcordoEvento();
        if ($novoEvento['opcaoSelecionada'] == 1) {
            $acordoEvento->setTipoEvento(AcordoEvento::TIPO_EVENTO_TERMO_ADITIVIVO);
        } else {
            $acordoEvento->setTipoEvento(AcordoEvento::TIPO_EVENTO_APOSTILA);
        }
        $acordoEvento->setAcordo($acordo);
        $acordoEvento->setData(new DBDate(date("Y-m-d", db_getsession("DB_datausu"))));
        $acordoEvento->salvar();
        $acordoEvento->adicionarAcordoPosicaoEvento($novaPosicao);





        $this->salvarSaldoAditamento($novaPosicao->getCodigo(), $itensSelecionados);
        $todosItens = $this->zerarItensNaoSelecionados($todosItens, $itensSelecionados);
        $this->salvaritens($todosItens, $acordoPosicao, $novaPosicao->getCodigo());
        return true;
    }

    public function getProximoNumeroAditamento($codigoAcordoPosicao)
    {
        $oDaoPosicao = new cl_acordoposicao;
        $sWhere = "ac26_acordo       = {$this->getCodigoAcordo()}";
        $sSqlultimaPosicao = $oDaoPosicao->sql_query_file(
            null,
            "coalesce(max(ac26_numero), 0) + 1 as proximo_numero",
            null,
            $sWhere
        );

        $rsPosicao = $oDaoPosicao->sql_record($sSqlultimaPosicao);
        if ($oDaoPosicao->numrows == 0) {
            throw new Exception("Acordo sem posições definidas.");
        }

        $iProximoNumero = db_utils::fieldsMemory($rsPosicao, 0)->proximo_numero;
        return $iProximoNumero;
    }


    private function salvarPeriodos($acordoPosicao, $novoSequencial)
    {
        foreach ($acordoPosicao['periodo'] as $periodo) {
            $novoPeriodo = new AcordoPosicaoPeriodo();
            $novoPeriodo->ac36_acordoposicao = $novoSequencial;
            $novoPeriodo->ac36_datainicial = $periodo['ac36_datainicial'];
            $novoPeriodo->ac36_datafinal = $periodo['ac36_datafinal'];
            $novoPeriodo->ac36_descricao = $periodo['ac36_descricao'];
            $novoPeriodo->ac36_numero = $periodo['ac36_numero'];
            $novoPeriodo->save();
        }
    }

    private function salvarVigencia($acordoPosicao, $novoSequencial)
    {
        $oDaoAcordoVigencia = new cl_acordovigencia();
        $oDaoAcordoVigencia->ac18_acordoposicao = $novoSequencial;
        $oDaoAcordoVigencia->ac18_ativo = "true";
        $oDaoAcordoVigencia->ac18_datainicio = $acordoPosicao['ac16_datainicio'];
        $oDaoAcordoVigencia->ac18_datafim = $acordoPosicao['ac16_datafim'];
        $oDaoAcordoVigencia->incluir(null);
        if ($oDaoAcordoVigencia->erro_status == 0) {
            throw new Exception("Erro ao definir vigência do contrato.\n{$oDaoAcordoVigencia->erro_msg}");
        }
    }

    private function salvarSaldoAditamento($novaPosicao, $itensSelecionados)
    {
        $vlrTotal = 0;
        foreach ($itensSelecionados as $item) {
            $vlrTotal += $item['ac20_valortotal'];
        }

        $oDaoAcordoPosicaoAditamento = new cl_acordoposicaoaditamento();
        $oDaoAcordoPosicaoAditamento->ac35_acordoposicao = $novaPosicao;
        $oDaoAcordoPosicaoAditamento->ac35_valor = $vlrTotal;
        $oDaoAcordoPosicaoAditamento->incluir(null);

        if ($oDaoAcordoPosicaoAditamento->erro_status == 0) {
            throw new DBException("Erro ao salvar dados do aditamento");
        }
    }

    private function zerarItensNaoSelecionados($itens, $itensSelecionados)
    {
        for ($i = 0; $i < count($itens); $i++) {
            if (!in_array($itens[$i], $itensSelecionados)) {
                $itens[$i]['ac20_valorunitario'] = 0;
                $itens[$i]['ac20_valortotal'] = 0;
            }
        }
        return $itens;
    }

    private function salvarItens($itens, $acordoPosicao, $novaPosicao)
    {
        foreach ($itens as $item) {
            $acordo = new Acordo($acordoPosicao['ac16_sequencial']);
            $itemContrato = $acordo->
            getPosicaoByCodigo($item['ac20_acordoposicao'])->getItemByCodigo($item['ac20_sequencial']);
            $origem = $itemContrato->getOrigem();
            $novoItem = new \AcordoItem(null);
            $novoItem->setCodigoPosicao($novaPosicao);
            $novoItem->setElemento($item['ac20_elemento']);
            $novoItem->setMaterial($itemContrato->getMaterial());
            $novoItem->setResumo(utf8_decode($item['ac20_resumo']));
            $novoItem->setOrigem($origem->codigo, $origem->tipo, $origem->codigoorigem);
            $novoItem->setUnidade($item['ac20_matunid']);
            $novoItem->setTipoControle($item['ac20_tipocontrole']);
            $novoItem->setItemVinculo($itemContrato->getCodigo());

            $periodosItem = $itemContrato->getPeriodosItem();
            if (!empty($aPeriodosItem)) {
                $novoItem->setPeriodos($itemContrato->getPeriodosItem());
            }

            $novoItem->setQuantidade($item['ac20_quantidade']);
            $novoItem->setValorUnitario($item['ac20_valorunitario']);
            $novoItem->setValorTotal($item['ac20_valortotal']);
            $novoItem->setControlaQuantidade($item['ac20_servicoquantidade']);
            if (!$item['ac20_servicoquantidade']) {
                $novoItem->setControlaQuantidade(null);
            }
            foreach ($item['dotacoes'] as $dotacao) {
                $dotacaoAdicionar = new stdClass();
                $dotacaoAdicionar->ano = db_getsession("DB_anousu");
                $dotacaoAdicionar->valor = $dotacao['ac22_valor'];
                $dotacaoAdicionar->dotacao = $dotacao['ac22_coddot'];
                $dotacaoAdicionar->quantidade = $dotacao['ac22_quantidade'];
                $dotacaoAdicionar->acordoitem = $dotacao['ac22_acordoitem'];

                $novoItem->adicionarDotacoes($dotacaoAdicionar);
            }
            $novoItem->save(false);
        }
    }
}
