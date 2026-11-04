<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios;

class BalanceteDespesaPlanoPadraoService extends RelatorioBalanceteDespesaService
{

    /**
     * @var mixed
     */
    protected $tipoPlano;


    public function setFiltrosRequest(array $filtros)
    {
        parent::setFiltrosRequest($filtros);

        $this->tipoPlano = $filtros['tipoPlano'];
    }

    public function sqlPrincipal()
    {
        $where = $this->tipoPlano === 'uniao' ? "uniao is true" : "uniao is false";

        $campos = " x.reduzido, x.ano, x.recurso, x.complemento, x.principal, x.cp, x.saldo_inicial, x.saldo_anterior,
        x.total_creditos, saldo_disponivel, x.suplementado, x.suplementado_especial, x.reducoes,
        x.saldo_alteracoes_orcamentarias, x.empenhado, empenhado_liquido, x.anulado, x.liquidado, x.pago,
        x.empenhado_acumulado, empenhado_liquido_acumulado, x.anulado_acumulado, x.liquidado_acumulado,
        x.pago_acumulado, x.a_liquidar, x.a_pagar, a_pagar_liquidado, x.orgao, x.descricao_orgao, x.unidade,
        x.descricao_unidade, x.funcao, x.descricao_funcao, x.subfuncao, x.descricao_subfuncao, x.programa,
        x.descricao_programa, x.projeto, x.descricao_projeto,
        planodespesa.conta as elemento, planodespesa.nome as descricao_elemento,
        x.fonte_recurso, x.gestao, x.siconfi, x.descricao_recurso, x.descricao_complemento, x.localizador_gasto,
        descricao_localizador_gasto, x.caracteristica_peculiar, x.nome_instituicao
        ";

        $sql = parent::sqlPrincipal();
        return "
            select {$campos}
              from ($sql) as x
              join conplanoorcamento co on co.c60_codcon = x.o58_codele
                   and co.c60_anousu = x.ano
              join contabilidade.planodespesaconplanoorcamento on conplanoorcamento_codigo = c60_codigo
              join planodespesa on planodespesa.id = planodespesa_id
            where $where
        ";
    }
}
