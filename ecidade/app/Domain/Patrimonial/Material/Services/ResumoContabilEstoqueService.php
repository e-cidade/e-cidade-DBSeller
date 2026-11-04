<?php

namespace App\Domain\Patrimonial\Material\Services;

use App\Domain\Patrimonial\Material\Relatorios\ResumoEstoquePDF;
use DBDate;
use ECidade\Patrimonial\Material\Repositories\DepositoRepository;
use Exception;

class ResumoContabilEstoqueService
{
    private $exibirTransferencias = false;
    private $dataInicial;
    private $dataFinal;
    private $depositos;
    private $exibirMateriaisSemEstoque = false;
    private $exibirSomenteInconsistencias = false;
    private $tipoImpressao;
    private $ordem = 0;

    private $agruparPorConta = false;
    private $agruparPorGrupo = false;
    private $contas;
    private $grupos;

    /**
     * @throws Exception
     */
    public function buscarDados()
    {
        $valor_anterior = 0;
        $quantidade_anterior = 0;
        $whereFim = "";
        if (!is_null($this->dataInicial)) {
            $periodoInicial = $this->dataInicial->getDate();
            $valor_anterior = <<<sql
                sum(case
                   when tipo = 1 and m80_data < '{$periodoInicial}'
                       then valor_financeiro
                   when tipo = 2 and m80_data < '{$periodoInicial}'
                       then (valor_financeiro) * -1
                   else 0 end)
sql;
            $quantidade_anterior = <<<sql
                sum(case
                   when tipo = 1 and codtipo <> 999 and m80_data < '{$periodoInicial}' then m82_quant
                   when tipo = 2 and codtipo <> 998 and m80_data < '{$periodoInicial}' then m82_quant * -1
                   else 0 end)
sql;

            $whereFim .= "and m80_data >= '{$periodoInicial}' ";
        }

        if (!is_null($this->dataFinal)) {
            $whereFim .= "and m80_data <= '{$this->dataFinal->getDate()}'";
        }

        $instit = db_getsession('DB_instit');

        $where = ["instit = $instit"];
        $where[] = "m71_servico = false";
        $where[] = "m60_servico = false";
        $where[] = "m81_tipo in (1, 2)";
        $where[] = "m60_ativo = true";

        if (!empty($this->depositos)) {
            $where[] = "m91_codigo in ({$this->depositos})";
        }
        if (!empty($this->contas)) {
            $where[] = "c60_codcon in ({$this->contas})";
        }
        if (!empty($this->grupos)) {
            $where[] = "m65_sequencial in ({$this->grupos})";
        }
        $where['codtipo'] = "m80_codtipo not in (998)";
        if (!$this->exibirTransferencias) {
            $where['codtipo'] = "m80_codtipo not in (8, 21, 998)";
        }

        $anousu = db_getsession('DB_anousu');

        $ordem = [];
        if ($this->agruparPorConta) {
            $ordem[] = 'c60_estrut';
        }
        if ($this->agruparPorGrupo) {
            $ordem[] = 'db121_estrutural';
        }

        if ($this->ordem == 1) {
            $ordem[] = 'm60_descr';
        } else {
            $ordem[] = 'm70_codmatmater';
        }

        $ordem = implode(', ', $ordem);

        $where = implode(' and ', $where);
        $sql = <<<sql
with movimentacoes as (
    select m70_codmatmater,
           m60_descr,
           m80_codtipo,
           m81_tipo,
           m80_data,
           case
               when m80_codtipo = 999
                   then (m89_valorfinanceiro) -
                        coalesce((select inimei_ajuste.m82_quant * x.m89_valorunitario
                         from matestoqueinimei as a
                                  join matestoqueini as b ON b.m80_codigo = a.m82_matestoqueini
                                  join matestoqueini ajuste
                                       ON to_timestamp(ajuste.m80_data || ' ' || ajuste.m80_hora,
                                                       'yyyy-mm-dd hh24:mi') =
                                          to_timestamp(b.m80_data || ' ' || b.m80_hora, 'yyyy-mm-dd hh24:mi') and
                                          ajuste.m80_codtipo = 998
                                  join matestoqueinimei inimei_ajuste
                                       on inimei_ajuste.m82_matestoqueini = ajuste.m80_codigo
                                  join matestoqueinimeipm as x
                                       ON x.m89_matestoqueinimei = inimei_ajuste.m82_codigo
                         where a.m82_codigo = matestoqueinimei.m82_codigo limit 1), 0)
               else m89_valorfinanceiro end as valor_financeiro,
           m89_valorunitario,
           case
               when m80_codtipo = 999
                   then m82_quant -
                        coalesce((select inimei_ajuste.m82_quant
                         from matestoqueinimei as a
                                  join matestoqueini as b ON b.m80_codigo = a.m82_matestoqueini
                                  join matestoqueini ajuste
                                       ON to_timestamp(ajuste.m80_data || ' ' || ajuste.m80_hora,
                                                       'yyyy-mm-dd hh24:mi') =
                                          to_timestamp(b.m80_data || ' ' || b.m80_hora, 'yyyy-mm-dd hh24:mi') and
                                          ajuste.m80_codtipo = 998
                                  join matestoqueinimei inimei_ajuste
                                       on inimei_ajuste.m82_matestoqueini = ajuste.m80_codigo
                                  join matestoqueinimeipm as x
                                       ON x.m89_matestoqueinimei = inimei_ajuste.m82_codigo
                         where a.m82_codigo = matestoqueinimei.m82_codigo limit 1), 0)
               else m82_quant end as m82_quant,
           db121_db_estrutura,
           db121_estrutural,
           db121_descricao,
           db121_estruturavalorpai,
           db121_nivel,
           db121_tipoconta,
           c61_reduz,
           c60_estrut,
           c60_descr
    from matestoqueinimei
             join matestoqueini on m80_codigo = m82_matestoqueini
             join matestoqueinimeipm on m89_matestoqueinimei = m82_codigo
             join matestoquetipo on m80_codtipo = m81_codtipo
             join matestoqueitem on m82_matestoqueitem = m71_codlanc
             join matestoque on m71_codmatestoque = m70_codigo
             join matmater on m60_codmater = m70_codmatmater
             join matmatermaterialestoquegrupo ON m68_matmater = matmater.m60_codmater
             join materialestoquegrupo ON m65_sequencial = m68_materialestoquegrupo
             join db_estruturavalor ON db121_sequencial = m65_db_estruturavalor
             join materialestoquegrupoconta ON m66_materialestoquegrupo = m65_sequencial and m66_anousu = {$anousu}
             join conplano ON c60_codcon = m66_codcon and c60_anousu = m66_anousu
             join conplanoreduz ON c61_codcon = c60_codcon and c61_anousu = c60_anousu and c61_instit = {$instit}
             join db_depart as departamento_origem on m70_coddepto = coddepto
             join db_almox on m91_depto = coddepto
      where {$where}
), filtro_ajustes as (
    select m70_codmatmater,
           m60_descr,
           m80_data,
           case when m80_codtipo = 999 and valor_financeiro < 0 then 998
                else m80_codtipo end as codtipo,
           case when m80_codtipo = 999 and valor_financeiro < 0 then 2
                else m81_tipo end as tipo,
           case when m80_codtipo = 999 and valor_financeiro < 0 then valor_financeiro*-1
                else valor_financeiro end as valor_financeiro,
           m82_quant,
           db121_db_estrutura,
           db121_estrutural,
           db121_descricao,
           db121_estruturavalorpai,
           db121_nivel,
           db121_tipoconta,
           c61_reduz,
           c60_estrut,
           c60_descr
           from movimentacoes
) select
    m70_codmatmater,
    m60_descr,
    {$valor_anterior} as valor_anterior,
    {$quantidade_anterior} as quantidade_anterior,
    sum(case when tipo = 1 {$whereFim} then valor_financeiro else 0 end) as valor_entradas,
    sum(case when tipo = 1 {$whereFim} then m82_quant else 0 end) as quantidade_entrada,
    sum(case when tipo = 2 {$whereFim} then valor_financeiro else 0 end) as valor_saidas,
    sum(case when tipo = 2 {$whereFim} then m82_quant else 0 end) as quantidade_saida,
    db121_db_estrutura,
    db121_estrutural,
    db121_descricao,
    db121_estruturavalorpai,
    db121_nivel,
    db121_tipoconta,
    c61_reduz,
    c60_estrut,
    c60_descr
 from filtro_ajustes
group by m70_codmatmater, m60_descr, db121_db_estrutura, db121_estrutural, db121_descricao, db121_estruturavalorpai,
         db121_nivel, db121_tipoconta, c61_reduz, c60_estrut, c60_descr
order by {$ordem}
sql
            ;

        $rs = db_query($sql);
        if (!$rs) {
            throw new Exception("Erro ao buscar as movimentações dos materiais.");
        }

        if (pg_num_rows($rs) == 0) {
            throw new Exception("Nenhuma movimentação para os filtros informados.");
        }

        return pg_fetch_all($rs);
    }

    /**
     * @param $exibir
     */
    public function exibirTransferencias($exibir)
    {
        $this->exibirTransferencias = $exibir;
    }

    /**
     * @param $depositos
     */
    public function setDepositos($depositos)
    {
        $this->depositos = $depositos;
    }

    /**
     * @param $exibirSemEstoque
     */
    public function exibirMateriaisSemEstoque($exibirSemEstoque)
    {
        $this->exibirMateriaisSemEstoque = $exibirSemEstoque;
    }

    /**
     * @throws Exception
     */
    public function emitir()
    {
        $dados = $this->buscarDados();
        $dadosAgrupados = $this->agruparMateriais($dados);
        return $this->emitirPdf($dadosAgrupados);
    }

    /**
     * @param $dadosAgrupados
     * @return string[]
     * @throws Exception
     */
    private function emitirPdf($dadosAgrupados)
    {
        $depositos = [];
        if (!empty($this->depositos)) {
            $codigosDepositos = explode(',', $this->depositos);
            foreach ($codigosDepositos as $codigoDeposito) {
                $depositos[] = DepositoRepository::find($codigoDeposito);
            }
        }
        $configuracoes = (object)[
            "dataInicial" => !empty($this->dataInicial) ? $this->dataInicial->getDate(DBDate::DATA_PTBR) : '',
            "dataFinal" => !empty($this->dataFinal) ? $this->dataFinal->getDate(DBDate::DATA_PTBR) : '',
            "agruparPorConta" => $this->agruparPorConta,
            "agruparPorGrupo" => $this->agruparPorGrupo,
            "tipoImpressao" => $this->tipoImpressao,
            "ordem" => $this->ordem,
            "depositos" => $depositos,
            "exibirTransferencias" => $this->exibirTransferencias,
            "exibirMateriaisSemEstoque" => $this->exibirMateriaisSemEstoque
        ];
        $pdf = new ResumoEstoquePDF($dadosAgrupados, $configuracoes);
        return $pdf->emitirPdf();
    }

    /**
     * @param DBDate $dataInicial
     */
    public function setDataInicial(DBDate $dataInicial)
    {
        $this->dataInicial = $dataInicial;
    }

    /**
     * @param DBDate $dataFinal
     */
    public function setDataFinal(DBDate $dataFinal)
    {
        $this->dataFinal = $dataFinal;
    }

    private function agruparMateriais(array $dados)
    {
        $dadosAgrupados = (object)[
            "total_geral" => $this->criarArrayTotais()
        ];
        $contas = [];
        $grupos = [];
        foreach ($dados as $dado) {
            $quantidade_anterior = $dado['quantidade_anterior'];
            $material = (object)[
                "codigo" => $dado['m70_codmatmater'],
                "descricao" => $dado['m60_descr'],
                "valor_anterior" => $dado['valor_anterior'],
                "quantidade_anterior" => $quantidade_anterior,
                "valor_entradas" => $dado['valor_entradas'],
                "quantidade_entrada" => $dado['quantidade_entrada'],
                "valor_saidas" => $dado['valor_saidas'],
                "quantidade_saida" => $dado['quantidade_saida'],
                "saldo_final" => $dado['valor_anterior'] + $dado['valor_entradas'] - $dado['valor_saidas'],
                "quantidade_final" => $quantidade_anterior + $dado['quantidade_entrada'] - $dado['quantidade_saida'],
            ];
            if (!$this->exibirMateriaisSemEstoque &&
                round($material->saldo_final, 2) == 0 &&
                round($material->quantidade_final, 3) == 0) {
                continue;
            }

            /**
             * Filtro para usuário DBSeller verificar somente itens com possível inconsistencia
             */
            if (db_getsession('DB_id_usuario') == 1 && $this->exibirSomenteInconsistencias) {
                /**
                 * Se o item tem saldo e tem quatidade pode ser que não tenha inconsistência
                 * Ou se estiver zerado
                 * Então, pula ele
                 */
                if ((round($material->saldo_final, 2) > 0 && round($material->quantidade_final, 3) > 0) ||
                    (round($material->saldo_final, 2) == 0 && round($material->quantidade_final, 3) == 0)
                ) {
                    continue;
                }
            }

            $dadosAgrupados->total_geral = $this->somarTotais($dadosAgrupados->total_geral, $material);

            $codigoGrupo = $dado['db121_estrutural'];
            $codigoConta = $dado['c61_reduz'];
            if ($this->agruparPorGrupo) {
                if (!array_key_exists($codigoGrupo, $grupos)) {
                    $grupos[$codigoGrupo] = $this->criarGrupo($dado);
                }
                $grupos[$codigoGrupo]->total_geral = $this->somarTotais($grupos[$codigoGrupo]->total_geral, $material);
                $grupos[$codigoGrupo]->materiais[] = $material;

                if ($this->agruparPorConta) {
                    if (!array_key_exists($codigoConta, $contas)) {
                        $contas[$codigoConta] = $this->criarConta($dado);
                    }
                    $totais = $contas[$codigoConta]->total_geral;
                    $contas[$codigoConta]->total_geral = $this->somarTotais($totais, $material);
                    $contas[$codigoConta]->grupos[$codigoGrupo] = $grupos[$codigoGrupo];
                    $dadosAgrupados->contas = $contas;
                    continue;
                }
                $dadosAgrupados->grupos = $grupos;
                continue;
            }

            if ($this->agruparPorConta) {
                if (!array_key_exists($codigoConta, $contas)) {
                    $contas[$codigoConta] = $this->criarConta($dado);
                }
                $contas[$codigoConta]->total_geral = $this->somarTotais($contas[$codigoConta]->total_geral, $material);
                $contas[$codigoConta]->materiais[] = $material;
                $dadosAgrupados->contas = $contas;
                continue;
            }
            $dadosAgrupados->materiais[] = $material;
        }
        return $dadosAgrupados;
    }

    private function criarConta($dado)
    {
        return (object)[
            "codigo" => $dado['c61_reduz'],
            "estrutural" => $dado['c60_estrut'],
            "descricao" => $dado['c60_descr'],
            "total_geral" => $this->criarArrayTotais()
        ];
    }

    private function criarGrupo($dado)
    {
        return (object)[
            "estrutural" => $dado['db121_estrutural'],
            "descricao" => $dado['db121_descricao'],
            "total_geral" => $this->criarArrayTotais(),
            "materiais" => []
        ];
    }

    /**
     * @param $agrupar
     */
    public function agruparPorConta($agrupar)
    {
        $this->agruparPorConta = $agrupar;
    }

    /**
     * @param $agrupar
     */
    public function agruparPorGrupo($agrupar)
    {
        $this->agruparPorGrupo = $agrupar;
    }

    private function criarArrayTotais()
    {
        return [
            "valor_anterior" => 0,
            "quantidade_anterior" => 0,
            "valor_entradas" => 0,
            "quantidade_entrada" => 0,
            "valor_saidas" => 0,
            "quantidade_saida" => 0,
            "saldo_final" => 0,
            "quantidade_final" => 0,
            "total_itens" => 0
        ];
    }

    private function somarTotais($totais, $material)
    {
        $totais["valor_anterior"] += $material->valor_anterior;
        $totais["quantidade_anterior"] += $material->quantidade_anterior;
        $totais["valor_entradas"] += $material->valor_entradas;
        $totais["quantidade_entrada"] += $material->quantidade_entrada;
        $totais["valor_saidas"] += $material->valor_saidas;
        $totais["quantidade_saida"] += $material->quantidade_saida;
        $totais["saldo_final"] += $material->saldo_final;
        $totais["quantidade_final"] += $material->quantidade_final;
        $totais["total_itens"]++;

        return $totais;
    }

    public function setTipoImpressao($tipo)
    {
        $this->tipoImpressao = $tipo;
    }

    public function setContas($contas)
    {
        $this->contas = $contas;
    }

    public function setGrupos($grupos)
    {
        $this->grupos = $grupos;
    }

    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;
    }

    public function exibirSomenteInconsistencias($param)
    {
        $this->exibirSomenteInconsistencias = $param;
    }
}
