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
class Anexo8 extends Base implements In22
{

    const CODIGO_RELATORIO = 213;
    const TEMPLATE = 'config/templates/IN22/anexo8.xlsx';

    /**
     * @return mixed|void
     */
    public function processar()
    {
        $periodo = new \Periodo($this->codigoPeriodo);
        $parser = new Parser();
        $parser->loadXLS(self::TEMPLATE);
        $parser->addVariable('data_emissao', $this->getDataEmissao()->getDate(\DBDate::DATA_PTBR));
        $parser->addVariable('mes_ano_emissao', $periodo->getDescricao() . "/{$this->ano}");
        $dados = $this->processarDados();
        $totalValorPago = 0;
        foreach ($dados as $dado) {
            $totalValorPago += $dado->valorpago;
            $dado->elemento = ' ' . $dado->elemento . ' ';
            $dado->valorpago = $this->formataValor($dado->valorpago);
        }
        $totalValorPago = $this->formataValor($totalValorPago);
        $parser->addCollection('dados', $dados);
        $parser->addVariable('totalvalorpago', $totalValorPago);
        $parser->addCollection('dados', $dados);
        $parser->parse();
        $path = 'tmp/anexo8.xlsx';
        $parser->save($path);
        return $path;
    }


    private function processarDados()
    {
        $periodo = new \Periodo($this->codigoPeriodo);
        $dataInicial = $periodo->getDataInicial($this->ano);
        $dataFinal = $periodo->getDataFinal($this->ano);
        $whereFiltro = $this->getFiltrosConfigurados();

        $campoProjeto = "lpad(o58_orgao::varchar, 2 , '0') || '.'||lpad(o58_unidade::varchar, 2 , '0') || '.' ||";
        $campoProjeto .= "lpad(o58_funcao::varchar, 3, '0') || '.'|| ";
        $campoProjeto .= "lpad(o58_subfuncao::varchar, 3, '0') || '.' || lpad(o58_programa, 4,  '0') || '.' ||";
        $campoProjeto .= "lpad(o58_projativ::varchar, 4, '0')";
        $campoProjeto .= "as projetoatividade";

        $campoContaCorrente = "db89_db_bancos||' '||db89_codagencia||'-'||db89_digito||' '||db83_conta||'-'||";
        $campoContaCorrente .= "db83_dvconta as contacorrente";
        $campos = [
            "e150_numeroprocesso as processo",
            "to_char(c70_data, 'DD/MM/YYYY') as datapagamento",
            $campoProjeto,
            $campoContaCorrente,
            "substr(o56_elemento, 2, 12) as elemento",
            "round(sum(case when c53_tipo = 31 then c70_valor * -1 else c70_valor end), 2) as valorpago"
        ];
        $where = [
            "c70_data between '{$dataInicial->getDate()}' and '{$dataFinal->getDate()}'",
            "c70_anousu = {$this->ano}",
            "e60_instit in (".implode(",", $this->instituicoes).")",
            "c53_tipo in(30,31)",
        ];

        $daoConlancamemp = new \cl_conlancamemp();
        $sql = $daoConlancamemp->sql_query_dados_empenho(
            implode(", ", $campos),
            implode(" and ", $where).$whereFiltro,
            "datapagamento, processo",
            "e150_numeroprocesso, c70_data,
         o58_orgao,
         o58_unidade,
         o58_funcao,
         o58_subfuncao,
         o58_programa,
         o58_projativ,
         o56_elemento,
         db89_db_bancos,
         db89_codagencia,
         db89_digito,
         db83_conta,
         db83_dvconta"
        );
        $rsDados = db_query($sql);
        $dados =\db_utils::getCollectionByRecord($rsDados);
        return $dados;
    }
}
