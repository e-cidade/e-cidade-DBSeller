<?php
/**
 * Created by PhpStorm.
 * User: robson
 * Date: 2020-02-05
 * Time: 16:36
 */

namespace ECidade\Financeiro\Contabilidade\Relatorio\TCE\RO\IN22;

use ECidade\Library\SpreadSheet\Template\Parser;

/**
 * Class Anexo2
 * @package ECidade\Financeiro\Contabilidade\Relatorio\TCE\RO\IN22
 */
class Anexo13 extends Base implements In22
{

    const CODIGO_RELATORIO = 228;
    const TEMPLATE = 'config/templates/IN22/anexo13.xlsx';


    /**
     * @return mixed|void
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \BusinessException
     *
     */
    public function processar()
    {
        $periodo = new \Periodo($this->codigoPeriodo);
        $parser = new Parser();
        $parser->loadXLS(self::TEMPLATE);
        $parser->addVariable('data_emissao', $this->getDataEmissao()->getDate(\DBDate::DATA_PTBR));
        $parser->addVariable('mes_ano_emissao', $periodo->getDescricao() . "/{$this->ano}");
        $dados = $this->processarDados();

        $total_vlrempmes = 0;
        $total_vlrempacu = 0;
        $total_vlrliqmes = 0;
        $total_vlrliqacu = 0;
        $total_vlrpagmes = 0;
        $total_vlrpagacu = 0;

        foreach ($dados as $dado) {
            $total_vlrempmes += $dado->vlrempmes;
            $total_vlrempacu += $dado->vlrempacu;
            $total_vlrliqmes += $dado->vlrliqmes;
            $total_vlrliqacu += $dado->vlrliqacu;
            $total_vlrpagmes += $dado->vlrpagmes;
            $total_vlrpagacu += $dado->vlrpagacu;

            $dado->vlrempmes = $this->formataValor($dado->vlrempmes);
            $dado->vlrempacu = $this->formataValor($dado->vlrempacu);
            $dado->vlrliqmes = $this->formataValor($dado->vlrliqmes);
            $dado->vlrliqacu = $this->formataValor($dado->vlrliqacu);
            $dado->vlrpagmes = $this->formataValor($dado->vlrpagmes);
            $dado->vlrpagacu = $this->formataValor($dado->vlrpagacu);
            $dado->elemento =  ' ' . $dado->elemento . ' ';
        }
        $parser->addCollection('dados', $dados);

        $total_vlrempmes = $this->formataValor($total_vlrempmes);
        $total_vlrempacu = $this->formataValor($total_vlrempacu);
        $total_vlrliqmes = $this->formataValor($total_vlrliqmes);
        $total_vlrliqacu = $this->formataValor($total_vlrliqacu);
        $total_vlrpagmes = $this->formataValor($total_vlrpagmes);
        $total_vlrpagacu = $this->formataValor($total_vlrpagacu);


        $parser->addVariable('total_vlrempmes', $total_vlrempmes);
        $parser->addVariable('total_vlrempacu', $total_vlrempacu);
        $parser->addVariable('total_vlrliqmes', $total_vlrliqmes);
        $parser->addVariable('total_vlrliqacu', $total_vlrliqacu);
        $parser->addVariable('total_vlrpagmes', $total_vlrpagmes);
        $parser->addVariable('total_vlrpagacu', $total_vlrpagacu);

        $parser->parse();
        $caminho = 'tmp/anexo13_' . date('d-m-Y', db_getsession('DB_datausu')) . '.xlsx';
        $parser->save($caminho);
        return $caminho;
    }

    /**
     * Processa os dados do
     * @return \stdClass[]
     * @throws \BusinessException
     */
    private function processarDados()
    {
        $periodo = new \Periodo($this->codigoPeriodo);
        $dataInicial = $periodo->getDataInicial($this->ano);
        $dataInicial2 = "{$this->ano}-01-01";
        $dataFinal = $periodo->getDataFinal($this->ano);
        $whereFiltro = $this->getFiltrosConfigurados();
        $campoProjeto = "o58_projativ::varchar as projetoatividade";
        $campoContaCorrente = "array_to_string(array_accum(distinct db89_db_bancos||' '||db89_codagencia";
        $campoContaCorrente .= "||'-'||db89_digito||' '||db83_conta||'-'||db83_dvconta), ',') as contacorrente";

        $programatica = "lpad(o58_orgao::varchar, 2 , '0') || '.'||lpad(o58_unidade::varchar, 2 , '0') || '.' ||";
        $programatica .= "lpad(o58_funcao::varchar, 3, '0') || '.'|| ";
        $programatica .= "lpad(o58_subfuncao::varchar, 3, '0') || '.' || lpad(o58_programa, 4,  '0') || '.' ||";
        $programatica .= "lpad(o58_projativ::varchar, 4, '0')||'.'||substr(o56_elemento, 2,12)||'.'||o15_codigo ";
        $programatica .= "as funcionalprogramatica";

        $valor_empenhado_acu = "
            round(coalesce(sum(case when c53_tipo = 11 then c70_valor * -1 else (case when
                c53_tipo = 10 then c70_valor else 0 end) end), 0), 2) as vlrempacu";
        $valor_liquidado_acu = "
            round(coalesce(sum(case when c53_tipo = 21 then c70_valor * -1 else (case when
                c53_tipo = 20 then c70_valor else 0 end) end), 0), 2) as vlrliqacu";
        $valor_pago_acu = "
            round(coalesce(sum(case when c53_tipo = 31 then c70_valor * -1 else (case when
                c53_tipo = 30 then c70_valor else 0 end) end), 0), 2) as vlrpagacu";
        $valor_empenhado = "
            round(coalesce(sum(case when c70_data BETWEEN '{$dataInicial->getDate()}' and '{$dataFinal->getDate()}' then
            	(case when c53_tipo = 11 then c70_valor * -1 else (case when
            	    c53_tipo = 10 then c70_valor else 0 end) end) end), 0), 2) as vlrempmes";
        $valor_liquidado = "
            round(coalesce(sum(case when c70_data BETWEEN '{$dataInicial->getDate()}' and '{$dataFinal->getDate()}' then
            	(case when c53_tipo = 21 then c70_valor * -1 else (case when
            	    c53_tipo = 20 then c70_valor else 0 end) end) end), 0), 2) as vlrliqmes";
        $valor_pago = "
            round(coalesce(sum(case when c70_data BETWEEN '{$dataInicial->getDate()}' and '{$dataFinal->getDate()}' then
            	(case when c53_tipo = 31 then c70_valor * -1 else (case when
            	    c53_tipo = 30 then c70_valor else 0 end) end) end), 0), 2) as vlrpagmes";

        $campos = [
            "to_char('{$dataFinal->getDate()}'::date, 'DD/MM/YYYY') as datapagamento",
            "substr(o56_elemento, 2,12) as elemento",
            $campoProjeto,
            $campoContaCorrente,
            $programatica,
            $valor_empenhado,
            $valor_empenhado_acu,
            $valor_liquidado,
            $valor_liquidado_acu,
            $valor_pago,
            $valor_pago_acu
        ];
        $where = [
            "c70_data between '{$dataInicial2}' and '{$dataFinal->getDate()}'",
            "c70_anousu = {$this->ano}",
            "e60_instit in (" . implode(",", $this->instituicoes) . ")",
            "c53_tipo in(10, 11, 20, 21, 30, 31)",
        ];
        $daoConlancamemp = new \cl_conlancamemp();
        $sql = $daoConlancamemp->sql_query_dados_empenho(
            implode(", ", $campos),
            implode(" and ", $where) . $whereFiltro,
            "datapagamento",
            "o58_projativ,
            o56_elemento,
            o58_orgao,
            o58_unidade,
            o58_funcao,
            o58_subfuncao,
            o58_programa,
            o58_projativ,
            o15_codigo"
        );
        $rsDados = db_query($sql);
        $dados = \db_utils::getCollectionByRecord($rsDados);
        return $dados;
    }
}
