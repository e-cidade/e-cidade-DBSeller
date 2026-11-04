<?php
/**
 * Created by PhpStorm.
 * User: robson
 * Date: 2020-02-05
 * Time: 16:36
 */

namespace ECidade\Financeiro\Contabilidade\Relatorio\TCE\RO\IN22;

use DBDate;
use ECidade\Library\SpreadSheet\Template\Parser;
use Periodo;

/**
 * Class Anexo10
 * @package ECidade\Financeiro\Contabilidade\Relatorio\TCE\RO\IN22
 */
class Anexo10A extends Base implements In22
{
    const CODIGO_RELATORIO = 235;
    const TEMPLATE = 'config/templates/IN22/anexo10_A.xlsx';

    /**
     * @return mixed|void
     * @throws \BusinessException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function processar()
    {
        $periodo = new Periodo($this->codigoPeriodo);
        $parser = new Parser();
        $parser->loadXLS(self::TEMPLATE);
        $parser->addVariable('data_emissao', $this->getDataEmissao()->getDate(DBDate::DATA_PTBR));
        $parser->addVariable('mes_ano_emissao', $periodo->getDescricao() . "/{$this->ano}");
        $dados = $this->processarDados();
        $totalValorInscrito = 0;
        $totalValorPago = 0;
        foreach ($dados as $dado) {
            $totalValorInscrito += $dado->valorinscrito;
            $totalValorPago += $dado->valorpago;

            $dado->valorpago = $this->formataValor($dado->valorpago);
            $dado->valorinscrito = $this->formataValor($dado->valorinscrito);
        }
        $totalValorInscrito = $this->formataValor($totalValorInscrito);
        $totalValorPago = $this->formataValor($totalValorPago);

        $parser->addCollection('dados', $dados);
        $parser->addVariable('totalvalorinscrito', $totalValorInscrito);
        $parser->addVariable('totalvalorpago', $totalValorPago);
        $parser->addCollection('dados', $dados);
        $parser->parse();
        $path = 'tmp/anexo10_A.xlsx';
        $parser->save($path);
        return $path;
    }


    /**
     * @return array
     * @throws \BusinessException
     */
    private function processarDados()
    {
        $periodo = new Periodo($this->codigoPeriodo);
        $dataInicial = $periodo->getDataInicial($this->ano);
        $dataFinal = $periodo->getDataFinal($this->ano);
        $whereFiltro = $this->getFiltrosConfigurados();

        $campoProjeto = "lpad(o58_orgao::varchar, 2 , '0') || '.'||lpad(o58_unidade::varchar, 2 , '0') || '.' ||";
        $campoProjeto .= "lpad(o58_funcao::varchar, 3, '0') || '.'|| ";
        $campoProjeto .= "lpad(o58_subfuncao::varchar, 3, '0') || '.' || lpad(o58_programa, 4,  '0') || '.' ||";
        $campoProjeto .= "lpad(o58_projativ::varchar, 4, '0')||'.'||o56_elemento||'.'||o15_codigo ";
        $campoProjeto .= "as funcionalprogramatica";

        $campoContaCorrente = "db89_db_bancos||' '||db89_codagencia||'-'||db89_digito||' '||db83_conta||'-'||";
        $campoContaCorrente .= "db83_dvconta as contacorrente";
        $campos = [
            "e150_numeroprocesso as processo",
            "to_char(c70_data, 'DD/MM/YYYY') as datapagamento",
            $campoProjeto,
            $campoContaCorrente,
            "e60_codemp||'/'||e60_anousu::text as empenho",
            "z01_nome as fornecedor",
            "round((e91_vlremp-e91_vlranu-e91_vlrpag), 2) as valorinscrito",
            "round(sum(case when c53_tipo = 31 then c70_valor * -1 else c70_valor end), 2) as valorpago",
            "(case when substr(o56_elemento, 1, 3)  = '331' then 1
                  else 2 end) as tipo"
        ];
        $where = [
            "c70_data between '{$dataInicial->getDate()}' and '{$dataFinal->getDate()}'",
            "c70_anousu = {$this->ano}",
            "e91_anousu = {$this->ano}",
            "e60_instit in (" . implode(",", $this->instituicoes) . ")",
            "c53_tipo in(30,31)",
        ];
        $daoConlancamemp = new \cl_conlancamemp();
        $sql = $daoConlancamemp->sql_query_dados_empenho(
            implode(", ", $campos),
            implode(" and ", $where) . $whereFiltro,
            "datapagamento, processo",
            "e150_numeroprocesso, c70_data,
                    o58_orgao,
                    o58_unidade,
                    o58_funcao,
                    o58_subfuncao,
                    o58_programa,
                    o58_projativ,
                    o56_elemento,
                    o15_codigo,
                    e60_codemp,
                    e60_anousu,
                    o58_concarpeculiar,
                    z01_nome,
                    db89_db_bancos,
                    db89_codagencia,
                    db89_digito,
                    e91_vlremp,e91_vlranu,e91_vlrliq,e91_vlrliq,e91_vlrpag,
                    db83_conta,
                    db83_dvconta",
            true,
            $this->ano
        );

        $rsDados = db_query($sql);
        $dados = \db_utils::getCollectionByRecord($rsDados);
        return $dados;
    }
}
