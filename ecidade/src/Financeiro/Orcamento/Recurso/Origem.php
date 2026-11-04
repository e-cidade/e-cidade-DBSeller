<?php

namespace ECidade\Financeiro\Orcamento\Recurso;

use Exception;

/**
 * Class Origem
 * @package ECidade\Financeiro\Orcamento\Recurso
 */
class Origem
{
    const DESPESA = 1; // o206_numero = empenho empempenho.e60_numemp
    const EMISSAO_EMPENHO = 1; // o206_numero = empenho empempenho.e60_numemp
    const EMPENHO_RP = 10; // o206_numero = empenho empempenho.e60_numemp quando inscrito em RP
    const LANCAMENTO_CONTABIL = 2; // o206_numero = contabilidade.conlancam c70_codlan
    const AUTORIZACAO_EMPENHO = 100; // o206_numero) = autorizacao: empautoriza.e54_autori
    const PLANILHA_ARRECADACAO = 200; // k81_seqpla da tabela placaixarec... buscar o k81_seqpla pelo codigo da planilha
    const RECIBO = 300; // cornump k12_numpre
    const COMPLEMENTO_PADRAO = 400;

    private static function get($numero, $origem)
    {
        $where = implode(' and ', [
            "o206_origem = {$origem}",
            "o206_numero = {$numero}",
        ]);
        $dao = new \cl_origemcomplementorecurso();
        $sql = $dao->sql_query_complemento("*", $where);
        $res = db_query($sql);
        if (!$res || pg_num_rows($res) == 0) {
            return false;
        }
        return \db_utils::fieldsMemory($res, 0);
    }

    /**
     * Retorna a origem selecionada pelo usuário na autorização do empenho.
     * @param $numero
     * @return bool|\stdClass
     */
    public static function getAutorizacao($numero)
    {
        return self::get($numero, self::AUTORIZACAO_EMPENHO);
    }

    /**
     * Retorna a origem selecionada pelo usuário na emissão do empenho.
     * @param $numero
     * @param $ano ano que esta processando
     * @return \_db_fields|bool|\stdClass
     */
    public static function getEmpenho($numero, $ano)
    {
        if (self::empenhoIsRP($numero, $ano)) {
            return self::get($numero, self::EMPENHO_RP);
        }

        return self::get($numero, self::EMISSAO_EMPENHO);
    }

    /**
     * Define o codigo do empenho
     *
     * @param integer $numero
     * @param integer $recurso
     * @param integer $complemento
     * @return bool
     * @throws Exception
     */
    public static function setEmpenho($numero, $recurso, $complemento, $ano)
    {
        if (self::empenhoIsRP($numero, $ano)) {
            return self::set(self::EMPENHO_RP, $numero, $recurso, $complemento);
        }

        self::set(self::EMISSAO_EMPENHO, $numero, $recurso, $complemento);
    }

    /**
     * @param integer $numero
     * @param integer $recurso
     * @param integer $complemento
     * @return bool
     * @throws Exception
     */
    public static function setEmpenhoRP($numero, $recurso, $complemento)
    {
        return self::set(self::EMPENHO_RP, $numero, $recurso, $complemento);
    }

    /**
     *  Define o codigo da autorizacao de empenho
     *
     * @param integer $autorizacao código da autorizacao
     * @param integer $recurso
     * @param integer $complemento
     * @throws Exception
     */
    public static function setAutorizacao($autorizacao, $recurso, $complemento)
    {
        self::set(self::AUTORIZACAO_EMPENHO, $autorizacao, $recurso, $complemento);
    }

    /**
     * @param $numero -
     * @param $recurso
     * @param $complemento
     *
     * @throws Exception
     */
    public static function setPlanilhaReceita($numero, $recurso, $complemento)
    {
        self::set(self::PLANILHA_ARRECADACAO, $numero, $recurso, $complemento);
    }

    /**
     * @param $numero -
     * @param $recurso
     * @param $complemento
     *
     * @throws Exception
     */
    public static function setReciboReceita($numero, $recurso, $complemento)
    {
        self::set(self::RECIBO, $numero, $recurso, $complemento);
    }

    /**
     * @param $numero -
     * @param $recurso
     * @param $complemento
     *
     * @throws Exception
     */
    public static function setLancamentoContabil($numero, $recurso, $complemento)
    {
        self::set(self::LANCAMENTO_CONTABIL, $numero, $recurso, $complemento);
    }

    /**
     * @param $numero -
     * @param $recurso
     * @param $complemento
     *
     * @throws Exception
     */
    public static function setComplementoPadrao($numero, $recurso, $complemento)
    {
        self::set(self::COMPLEMENTO_PADRAO, $numero, $recurso, $complemento);
    }

    /**
     * @param $origem
     * @param $numero
     * @return bool
     * @throws Exception
     */
    public static function set($origem, $numero, $recurso, $complemento)
    {
        $codigoOrigem = self::get($numero, $origem);
        $daoComplemento = new \cl_origemcomplementorecurso();
        $daoComplemento->o206_numero = $numero;
        $daoComplemento->o206_origem = $origem;
        $daoComplemento->o206_complementorecurso = $complemento;
        $daoComplemento->o206_recurso = $recurso;
        if (!empty($codigoOrigem)) {
            $daoComplemento->o206_sequencial = $codigoOrigem->o206_sequencial;
            $daoComplemento->alterar($codigoOrigem->o206_sequencial);
        } else {
            $daoComplemento->incluir(null);
        }

        if ($daoComplemento->erro_status == 0) {
            switch ($origem) {
                case self::AUTORIZACAO_EMPENHO:
                    $texto = ' a autorização de empenho ';
                    break;
                case self::EMISSAO_EMPENHO:
                case self::DESPESA:
                    $texto = ' o empenho ';
                    break;
                case self::LANCAMENTO_CONTABIL:
                    $texto = ' o lançamento contábil';
                    break;
                case self::PLANILHA_ARRECADACAO:
                    $texto = ' a planilha de receita ';
                    break;
                case self::RECIBO:
                    $texto = ' o recibo de receita ';
                    break;
                case self::COMPLEMENTO_PADRAO:
                    $texto = ' o complemento padrão de receita ';
                    break;
                default:
                    throw new Exception('Origem de complemeto não implementada');
            }
            throw new Exception("Não foi possível salvar origem do complemento para {$texto} {$numero}.");
        }
        return true;
    }

    /**
     * @param $numero
     *
     * @return bool|\stdClass
     */
    public static function getPlanilhaArrecadacao($numero)
    {
        return self::get($numero, self::PLANILHA_ARRECADACAO);
    }

    /**
     * @param $numero
     *
     * @return bool|\stdClass
     */
    public static function getRecibo($numero)
    {
        return self::get($numero, self::RECIBO);
    }

    /**
     * @param $numero
     *
     * @return bool|\stdClass
     */
    public static function getPadrao($numero)
    {
        return self::get($numero, self::COMPLEMENTO_PADRAO);
    }

    /**
     * @param $numero
     * @param $ano
     * @return bool
     * @throws Exception
     */
    private static function empenhoIsRP($numero, $ano)
    {
        $empenho = new \EmpenhoFinanceiro($numero);
        return $empenho->isRP($ano);
    }

    /**
     * @param integer $numero
     * @param integer $origem
     * @return bool
     * @throws Exception
     */
    public static function delete($numero, $origem)
    {
        $where = implode(' and ', [
            "o206_origem = {$origem}",
            "o206_numero = {$numero}",
        ]);
        $dao = new \cl_origemcomplementorecurso();
        $dao->excluir(null, $where);
        if ($dao->erro_status == 0) {
            throw new Exception("Erro ao excluir origem.");
        }

        return true;
    }
}
