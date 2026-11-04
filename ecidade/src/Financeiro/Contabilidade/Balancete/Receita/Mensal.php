<?php

namespace ECidade\Financeiro\Contabilidade\Balancete\Receita;

use db_utils;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceita;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * Balancete com valores por mes
 * Class Mensal
 * @package ECidade\Financeiro\Contabilidade\Balancete\Receita
 */
class Mensal
{
    protected $receita = null;
    protected $anousu = null;
    protected $sql = null;
    protected $numrows = 0;
    protected $result = false;
    protected $dataInicial = null;
    protected $dataFinal = null;
    protected $previsaoCronograma = false;
    protected $estruturais = null; // string de estruturais
    protected $institucoes = null;

    /**
     * executa a consulta e prepara os dados
     */
    private function montarConsultaSql()
    {
        $anos = [];

        if ($this->anousu == null) {
            $this->anousu = db_getsession("DB_anousu");
        }
        if ($this->dataInicial == null) {
            db_msgbox('Data inicio não informada.');
        }
        if ($this->dataFinal == null) {
            db_msgbox('Data final não informada.');
        }

        $instituicoes = "(" . implode(', ', $this->institucoes) . ")";
        $dataInicial = new \DBDate($this->dataInicial);
        $dataFinal = new \DBDate($this->dataFinal);
        $anos[] = $dataFinal->getAno();
        $anos[] = $dataInicial->getAno();
        $this->sql = "
        select *
         from (
           SELECT O70_ANOUSU,
                  O70_CODREC,
                  o70_instit,
                  O57_FONTE,O57_DESCR,
                  o70_codigo,
                  coalesce(complemento, '0')::integer as complemento,
                  coalesce((
                      select s.o70_valor
                       from orcreceita s
                      where s.o70_anousu = y.o70_anousu
                       and  s.o70_codrec = y.o70_codrec
                       and s.o70_anousu = {$this->anousu}), 0) as o70_valor,
                  sum(ADICIONAL)::float8 as adicional,
                  SUM(JANEIRO) AS JANEIRO,
                  SUM(FEVEREIRO) AS FEVEREIRO,
                  SUM(MARCO) AS MARCO,
                  SUM(ABRIL) AS ABRIL,
                  SUM(MAIO) AS MAIO,
                  SUM(JUNHO) AS JUNHO,
                  SUM(JULHO) AS JULHO,
                  SUM(AGOSTO) AS AGOSTO,
                  SUM(SETEMBRO) AS SETEMBRO,
                  SUM(OUTUBRO) AS OUTUBRO,
                  SUM(NOVEMBRO) AS NOVEMBRO,
                  SUM(DEZEMBRO) AS DEZEMBRO,
                  prev_jan::float8 as prev_jan,
                  prev_fev::float8 as prev_fev,
                  prev_mar::float8 as prev_mar,
                  prev_abr::float8 as prev_abr,
                  prev_mai::float8 as prev_mai,
                  prev_jun::float8 as prev_jun,
                  prev_jul::float8 as prev_jul,
                  prev_ago::float8 as prev_ago,
                  prev_set::float8 as prev_set,
                  prev_out::float8 as prev_out,
                  prev_nov::float8 as prev_nov,
                  prev_dez::float8 as prev_dez
           FROM (
            SELECT O70_ANOUSU,
                   O70_CODREC,
                   o70_instit,
                   o57_fonte,
                   o57_descr,
                   o70_codigo,
                   case
                     when complemento_lancamento is null then complemento_recurso
                     else complemento_lancamento
                   end as complemento,
                   case when o70_anousu = {$this->anousu} then sum(ADICIONAL) else 0 end as adicional,
                   sum(coalesce(case when O71_MES=1 then  0.0 end,0.0)) as  prev_jan,
                   sum(coalesce(case when O71_MES=2 then  0.0 end,0.0)) as  prev_fev,
                   sum(coalesce(case when O71_MES=3 then  0.0 end,0.0)) as  prev_mar,
                   sum(coalesce(case when O71_MES=4 then  0.0 end,0.0)) as  prev_abr,
                   sum(coalesce(case when O71_MES=5 then  0.0 end,0.0)) as  prev_mai,
                   sum(coalesce(case when O71_MES=6 then  0.0 end,0.0)) as  prev_jun,
                   sum(coalesce(case when O71_MES=7 then  0.0 end,0.0)) as  prev_jul,
                   sum(coalesce(case when O71_MES=8 then  0.0 end,0.0)) as  prev_ago,
                   sum(coalesce(case when O71_MES=9 then  0.0 end,0.0)) as  prev_set,
                   sum(coalesce(case when O71_MES=10 then 0 end,0.0)) as prev_out,
                   sum(coalesce(case when O71_MES=11 then 0.0 end,0.0)) as prev_nov,
                   sum(coalesce(case when O71_MES=12 then 0.0 end,0.0)) as prev_dez,
                   CASE WHEN O71_MES = 1 THEN sum(ARRECADADO) ELSE 0::FLOAT8 END AS JANEIRO,
                   CASE WHEN O71_MES = 2 THEN sum(ARRECADADO) ELSE 0::FLOAT8 END AS FEVEREIRO,
                   CASE WHEN O71_MES = 3 THEN sum(ARRECADADO) ELSE 0::FLOAT8 END AS MARCO,
                   CASE WHEN O71_MES = 4 THEN sum(ARRECADADO) ELSE 0::FLOAT8 END AS ABRIL,
                   CASE WHEN O71_MES = 5 THEN sum(ARRECADADO) ELSE 0::FLOAT8 END AS MAIO,
                   CASE WHEN O71_MES = 6 THEN sum(ARRECADADO) ELSE 0::FLOAT8 END AS JUNHO,
                   CASE WHEN O71_MES = 7 THEN sum(ARRECADADO) ELSE 0::FLOAT8 END AS JULHO,
                   CASE WHEN O71_MES = 8 THEN sum(ARRECADADO) ELSE 0::FLOAT8 END AS AGOSTO,
                   CASE WHEN O71_MES = 9 THEN sum(ARRECADADO) ELSE 0::FLOAT8 END AS SETEMBRO,
                   CASE WHEN O71_MES =10 THEN sum(ARRECADADO) ELSE 0::FLOAT8 END AS OUTUBRO,
                   CASE WHEN O71_MES =11 THEN sum(ARRECADADO) ELSE 0::FLOAT8 END AS NOVEMBRO,
                   CASE WHEN O71_MES =12 THEN sum(ARRECADADO) ELSE 0::FLOAT8 END AS DEZEMBRO
             FROM (
               SELECT O70_ANOUSU,O70_CODREC,
                      o70_instit,
                      o57_fonte,
                      o57_descr,
                      coalesce(o70_codigo, 0) as o70_codigo,
                      case
                        when o201_complemento is not null and complemento_lancamento.o200_tribunal is true
                          then o201_complemento
                        when o201_sequencial is null
                          then null
                        when complemento_lancamento.o200_tribunal is false
                          then 0
                      end as complemento_lancamento,

                      case
                        when complemento_recurso.o200_tribunal is true
                          then complemento_recurso.o200_sequencial
                        else 0
                      end as complemento_recurso,
                      TO_CHAR(C70_DATA,'MM')::integer AS O71_MES,
                      round(SUM(CASE C53_TIPO WHEN 110 THEN
                      case
                      when fc_conplano_grupo( O70_ANOUSU, substr(o57_fonte,1,2) || '%', 9000 ) is false
                          then ROUND(C70_VALOR,2)::FLOAT8 else ROUND(C70_VALOR*-1,2)::FLOAT8 end
                      WHEN 111 THEN
                      case when fc_conplano_grupo( O70_ANOUSU, substr(o57_fonte,1,2) || '%', 9000 ) is false
                          then ROUND(C70_VALOR*-1,2)::FLOAT8 else ROUND(C70_VALOR,2)::FLOAT8 end
                      ELSE 0::FLOAT8 END ),2) AS ADICIONAL,
                      round(SUM( CASE C53_TIPO WHEN 100 THEN
                      case when fc_conplano_grupo( O70_ANOUSU, substr(o57_fonte,1,2) || '%', 9000 ) is false
                          then ROUND(C70_VALOR,2)::FLOAT8
                      else ROUND(C70_VALOR*-1,2)::FLOAT8 end
                      WHEN 101 THEN
                      case when fc_conplano_grupo( O70_ANOUSU, substr(o57_fonte,1,2) || '%', 9000 ) is false
                          then ROUND(C70_VALOR*-1,2)::FLOAT8
                      else ROUND(C70_VALOR,2)::FLOAT8 end
                      ELSE 0::FLOAT8 END ),2) AS ARRECADADO
                 FROM orcamento.ORCRECEITA
                 JOIN orcamento.ORCFONTES ON O70_CODFON = O57_CODFON AND O57_ANOUSU = O70_ANOUSU
                 inner join orcamento.orctiporec recurso on o70_codigo = recurso.o15_codigo
                 inner join orcamento.fonterecurso on orctiporec_id = recurso.o15_codigo
                            and fonterecurso.exercicio = orcreceita.o70_anousu
                 inner join orcamento.complementofonterecurso as complemento_recurso
                       on complemento_recurso.o200_sequencial = recurso.o15_complemento

                 left JOIN contabilidade.CONLANCAMREC ON C74_ANOUSU = O70_ANOUSU
                                                     AND C74_CODREC = O70_CODREC
                                                     AND c74_data between '{$this->dataInicial}'
                                                    and '{$this->dataFinal}'
                 left JOIN contabilidade.CONLANCAM    ON C74_CODLAN = C70_CODLAN
                 left JOIN contabilidade.CONLANCAMDOC ON C71_CODLAN = C70_CODLAN
                 left JOIN contabilidade.CONHISTDOC ON C53_CODDOC = C71_CODDOC
                 left join contabilidade.conlancamcomplementorecurso on o201_codlan = c70_codlan
                 left join orcamento.complementofonterecurso as complemento_lancamento
                     on complemento_lancamento.o200_sequencial = o201_complemento
                WHERE o70_instit in {$instituicoes}
                  and o70_anousu in (" . implode(", ", $anos) . ")
                  and ( c70_valor <> 0 or o70_valor <> 0)

         ";
        if ($this->receita != null) {
            $this->sql .= " AND O70_CODREC = " . $this->receita;
        }
        $this->sql .= "
        GROUP BY O70_ANOUSU,
                 O70_CODREC,
                 o70_instit,
                 O71_MES,
                 O57_FONTE,
                 O57_DESCR,
                 o70_codigo,
                 complemento_lancamento,
                 complemento_recurso
    ) AS X

    group by
    O70_ANOUSU, O70_CODREC, o70_codigo, complemento,o70_instit, O71_MES,o57_fonte, o57_descr

    ";

        $this->sql .= ") AS Y
    GROUP BY O70_ANOUSU,
    O70_CODREC,
    o70_instit,
    O57_FONTE,
    O57_DESCR,
    o70_codigo,
    complemento,
    prev_jan,
    prev_fev,
    prev_mar,
    prev_abr,
    prev_mai,
    prev_jun,
    prev_jul,
    prev_ago,
    prev_set,
    prev_out,
    prev_nov,
    prev_dez

    ) as X

    ";
        if ($this->receita != null) {
            $this->sql .= " AND O70_CODREC = " . $this->receita;
        }

        $this->sql .= " order by o57_fonte ";
    }

    /**
     * Retorna os dados da Consulta
     */
    public function getDados()
    {
        if ($this->sql == null) {
            $this->montarConsultaSql($this->receita);
        }

        db_query("drop table if exists work_plano ");
        db_query("drop table if exists work_plano_estrut ");
        db_query("CREATE TEMPORARY TABLE work_plano AS " . $this->sql);
        db_query("CREATE INDEX work_plano_estrut ON work_plano(o57_fonte)");
        $result = db_query("SELECT * FROM work_plano");

        $previsaoJaneiro = 0;
        $previsaoFevereiro = 0;
        $previsaoMarco = 0;
        $previsaoAbril = 0;
        $previsaoMaio = 0;
        $previsaoJunho = 0;
        $previsaoJulho = 0;
        $previsaoAgosto = 0;
        $previsaoSetembro = 0;
        $previsaoOutubro = 0;
        $previsaoNovembro = 0;
        $previsaoDezembro = 0;
        $totalReceita = 0;

        for ($i = 0; $i < pg_num_rows($result); $i++) {
            $dadosReceita = \db_utils::fieldsmemory($result, $i);
            $estrutural = $dadosReceita->o57_fonte;

            if ($this->anousu < 2022) {
                $sSqlSaldoPrevisao = "
                SELECT mes,coalesce(o34_valor, 0) as valor, {$dadosReceita->o70_codrec} as receita
                 from generate_series(1,12) as mes
                ";

                $sSqlSaldoPrevisao .= "       left join orcprevrec ";
                $sSqlSaldoPrevisao .= "       on mes = o34_mes and o34_anousu = {$this->anousu} ";
                $sSqlSaldoPrevisao .= "                        and o34_codrec = $dadosReceita->o70_codrec";
                $sSqlSaldoPrevisao .= "    order by mes";
                if ($this->previsaoCronograma) {
                    $sSqlSaldoPrevisao = "
                     SELECT coalesce(valor, 0) as valor, {$dadosReceita->o70_codrec} as receita, mes
                       from generate_series(1,12) as mes
                           left join (
                               SELECT coalesce(o127_valor, 0) as valor,  o127_mes
                                 from cronogramaperspectivareceita
                                 join orcreceita on o70_codrec  = o126_codrec
                                      and o126_anousu = {$this->anousu}
                                 join cronogramametareceita on o127_cronogramaperspectivareceita = o126_sequencial
                                where o126_codrec = {$dadosReceita->o70_codrec}
                           ) as bases on mes = o127_mes
                     order by mes ";
                }


                $rsSaldoPrevisao = db_query($sSqlSaldoPrevisao);
                $aMeses = db_utils::getCollectionByRecord($rsSaldoPrevisao);
                $aValorPrevMes = array();
                foreach ($aMeses as &$oSaldoMes) {
                    $aValorPrevMes[$oSaldoMes->mes] = $oSaldoMes->valor;
                }
                $dadosReceita->prev_jan += $aValorPrevMes[1];
                $dadosReceita->prev_fev += $aValorPrevMes[2];
                $dadosReceita->prev_mar += $aValorPrevMes[3];
                $dadosReceita->prev_abr += $aValorPrevMes[4];
                $dadosReceita->prev_mai += $aValorPrevMes[5];
                $dadosReceita->prev_jun += $aValorPrevMes[6];
                $dadosReceita->prev_jul += $aValorPrevMes[7];
                $dadosReceita->prev_ago += $aValorPrevMes[8];
                $dadosReceita->prev_set += $aValorPrevMes[9];
                $dadosReceita->prev_out += $aValorPrevMes[10];
                $dadosReceita->prev_nov += $aValorPrevMes[11];
                $dadosReceita->prev_dez += $aValorPrevMes[12];
            } else {
                $sqlPrevisao = "
                select acompanhamentocronogramareceita.*
                  from orcamento.acompanhamentocronogramareceita
                 where exercicio = {$this->anousu}
                   and receita_id = {$dadosReceita->o70_codrec}
                ";

                $rs = db_query($sqlPrevisao);
                if ($rs && pg_num_rows($rs) > 0) {
                    $previsao = db_utils::fieldsMemory($rs, 0);
                    $dadosReceita->prev_jan += $previsao->janeiro;
                    $dadosReceita->prev_fev += $previsao->fevereiro;
                    $dadosReceita->prev_mar += $previsao->marco;
                    $dadosReceita->prev_abr += $previsao->abril;
                    $dadosReceita->prev_mai += $previsao->maio;
                    $dadosReceita->prev_jun += $previsao->junho;
                    $dadosReceita->prev_jul += $previsao->julho;
                    $dadosReceita->prev_ago += $previsao->agosto;
                    $dadosReceita->prev_set += $previsao->setembro;
                    $dadosReceita->prev_out += $previsao->outubro;
                    $dadosReceita->prev_nov += $previsao->novembro;
                    $dadosReceita->prev_dez += $previsao->dezembro;
                }
            }

            if ($this->previsaoCronograma) {
                db_query(
                    "update work_plano set
                           prev_jan  = prev_jan  + $dadosReceita->prev_jan,
                           prev_fev  = prev_fev  + $dadosReceita->prev_fev +0.0,
                           prev_mar  = prev_mar  + $dadosReceita->prev_mar +0.0,
                           prev_abr  = prev_abr  + $dadosReceita->prev_abr +0.0,
                           prev_mai  = prev_mai  + $dadosReceita->prev_mai +0.0,
                           prev_jun  = prev_jun  + $dadosReceita->prev_jun +0.0,
                           prev_jul  = prev_jul  + $dadosReceita->prev_jul +0.0,
                           prev_ago  = prev_ago  + $dadosReceita->prev_ago +0.0,
                           prev_set  = prev_set  + $dadosReceita->prev_set +0.0,
                           prev_out  = prev_out  + $dadosReceita->prev_out +0.0,
                           prev_nov  = prev_nov  + $dadosReceita->prev_nov +0.0,
                           prev_dez  = prev_dez  + $dadosReceita->prev_dez +0.0
                     where o70_codrec = {$dadosReceita->o70_codrec}
                       and o70_anousu = {$this->anousu}"
                );
            }

            for ($ii = 1; $ii < 11; $ii++) {
                if ($estrutural == "") {
                    continue;
                }

                if ($this->anousu <= 2017) {
                    $estrutural = db_le_mae_conplano($estrutural);
                    $nivel = db_le_mae_conplano($estrutural, true);
                } else {
                    $estruturalReceita = new EstruturalReceita($estrutural);
                    $estruturalPai = new EstruturalReceita($estruturalReceita->getCodigoEstruturalPai());
                    $estrutural = $estruturalPai->getEstrutural();
                    $nivel = $estruturalPai->getNivel();
                }

                $sqlEstrutural = "select o57_descr from work_plano ";
                $sqlEstrutural .= "where o57_fonte = '$estrutural' ";
                $sqlEstrutural .= "  and o70_anousu = {$dadosReceita->o70_anousu}";
                $result_estrut = db_query($sqlEstrutural);
                if (pg_num_rows($result_estrut) == 0) {
                    $sqlReceitaFonte = "select o57_descr ";
                    $sqlReceitaFonte .= " from orcamento.orcfontes ";
                    $sqlReceitaFonte .= "where o57_anousu = {$dadosReceita->o70_anousu} ";
                    $sqlReceitaFonte .= "  and o57_fonte = '$estrutural'";
                    $result_estrut = db_query($sqlReceitaFonte);

                    if (pg_num_rows($result_estrut) == 0) {
                        echo "Conta não encontrada nas fontes de Receita Comando: "
                            . "select o57_descr from orcamento.orcfontes
                                where o57_anousu = " . $dadosReceita->o70_anousu
                            . " and o57_fonte = '$estrutural'";
                        die();
                    }
                    db_fieldsmemory($result_estrut, 0);

                    $sInsert = "
                      insert into work_plano values(
                      " . $dadosReceita->o70_anousu . ",
                      0,
                      0,
                      '$estrutural',
                      '$dadosReceita->o57_descr',
                      $dadosReceita->o70_codigo,
                      0,
                       $dadosReceita->o70_valor,
                       $dadosReceita->adicional,
                       $dadosReceita->janeiro,
                       $dadosReceita->fevereiro,
                       $dadosReceita->marco,
                       $dadosReceita->abril,
                       $dadosReceita->maio,
                       $dadosReceita->junho,
                       $dadosReceita->julho,
                       $dadosReceita->agosto,
                       $dadosReceita->setembro,
                       $dadosReceita->outubro,
                       $dadosReceita->novembro,
                       $dadosReceita->dezembro,
                       $dadosReceita->prev_jan,
                       $dadosReceita->prev_fev,
                       $dadosReceita->prev_mar,
                       $dadosReceita->prev_abr,
                       $dadosReceita->prev_mai,
                       $dadosReceita->prev_jun,
                       $dadosReceita->prev_jul,
                       $dadosReceita->prev_ago,
                       $dadosReceita->prev_set,
                       $dadosReceita->prev_out,
                       $dadosReceita->prev_nov,
                       $dadosReceita->prev_dez
                      )
                    ";
                    $result_1 = db_query($sInsert);
                } else {
                    db_query(
                        "update work_plano
                           set
                              o70_valor = o70_valor +$dadosReceita->o70_valor,
                              adicional= adicional  +$dadosReceita->adicional,
                              janeiro  = janeiro    +$dadosReceita->janeiro,
                              fevereiro= fevereiro  +$dadosReceita->fevereiro,
                              marco    = marco      +$dadosReceita->marco,
                              abril    = abril      +$dadosReceita->abril,
                              maio     = maio       +$dadosReceita->maio,
                              junho    = junho      +$dadosReceita->junho,
                              julho    = julho      +$dadosReceita->julho,
                              agosto   = agosto     +$dadosReceita->agosto,
                              setembro = setembro   +$dadosReceita->setembro,
                              outubro  = outubro    +$dadosReceita->outubro,
                              novembro = novembro   +$dadosReceita->novembro,
                              dezembro = dezembro   +$dadosReceita->dezembro,
                              prev_jan  = prev_jan  +$dadosReceita->prev_jan,
                              prev_fev  = prev_fev  +$dadosReceita->prev_fev+0.0,
                              prev_mar  = prev_mar  +$dadosReceita->prev_mar+0.0,
                              prev_abr  = prev_abr  +$dadosReceita->prev_abr+0.0,
                              prev_mai  = prev_mai  +$dadosReceita->prev_mai+0.0,
                              prev_jun  = prev_jun  +$dadosReceita->prev_jun+0.0,
                              prev_jul  = prev_jul  +$dadosReceita->prev_jul+0.0,
                              prev_ago  = prev_ago  +$dadosReceita->prev_ago+0.0,
                              prev_set  = prev_set  +$dadosReceita->prev_set+0.0,
                              prev_out  = prev_out  +$dadosReceita->prev_out+0.0,
                              prev_nov  = prev_nov  +$dadosReceita->prev_nov+0.0,
                              prev_dez  = prev_dez  +$dadosReceita->prev_dez+0.0

                           where o57_fonte = '$estrutural'
                             and o70_anousu = {$dadosReceita->o70_anousu}"
                    );
                }
                if ($nivel == 1) {
                    break;
                }
            }
        }
        //exit;
        $sql = "
            SELECT O70_ANOUSU,
                   O70_CODREC,
                   o70_codigo,
                   complemento,
                   o70_instit,
                   O57_FONTE,
                   O57_DESCR,
                   round(O70_VALOR,2) AS o70_valor,
                   round(adicional,2) AS adicional,
                   round(JANEIRO,2 ) AS JANEIRO,
                   round(FEVEREIRO,2) AS FEVEREIRO,
                   round(MARCO,2  )  AS MARCO,
                   round(ABRIL,2  )  AS ABRIL,
                   round(MAIO, 2  )  AS MAIO,
                   round(JUNHO,2  )  AS JUNHO,
                   round(JULHO,2  )  AS JULHO,
                   round(AGOSTO,2 )  AS AGOSTO,
                   round(SETEMBRO,2)  AS SETEMBRO,
                   round(OUTUBRO,2 )  AS OUTUBRO,
                   round(NOVEMBRO,2)  AS NOVEMBRO,
                   round(DEZEMBRO,2)  AS DEZEMBRO,
                   prev_jan,
                   prev_fev,
                   prev_mar,
                   prev_abr,
                   prev_mai,
                   prev_jun,
                   prev_jul,
                   prev_ago,
                   prev_set,
                   prev_out,
                   prev_nov,
                   prev_dez
         FROM work_plano
        ";
        //--
        if ($this->estruturais != null) {
            $sql .= "where O57_FONTE IN  " . $this->estruturais;
        }

        $sql .= "order by o57_fonte ";
        $this->result = db_query($sql);
        if ($this->anousu == 2010) {
            //db_criatabela($this->result);
        }
        if ($this->result != false) {
            $this->numrows = pg_num_rows($this->result);
        } else {
            $this->numrows = 0;
        }
        return $this->result;
    }

    /**
     * @return null
     */
    public function getReceita()
    {
        return $this->receita;
    }

    /**
     * @param null $receita
     */
    public function setReceita($receita)
    {
        $this->receita = $receita;
    }

    /**
     * @return null
     */
    public function getDataInicial()
    {
        return $this->dataInicial;
    }

    /**
     * @param null $dataInicial
     */
    public function setDataInicial($dataInicial)
    {
        $this->dataInicial = $dataInicial;
    }

    /**
     * @return null
     */
    public function getDataFinal()
    {
        return $this->dataFinal;
    }

    /**
     * @param null $dataFinal
     */
    public function setDataFinal($dataFinal)
    {
        $this->dataFinal = $dataFinal;
    }

    /**
     * @return bool
     */
    public function isPrevisaoCronograma()
    {
        return $this->previsaoCronograma;
    }

    /**
     * @param bool $previsaoCronograma
     */
    public function setPrevisaoCronograma($previsaoCronograma)
    {
        $this->previsaoCronograma = $previsaoCronograma;
    }

    /**
     * @return null
     */
    public function getEstruturais()
    {
        return $this->estruturais;
    }

    /**
     * @param null $estruturais
     */
    public function setEstruturais($estruturais)
    {
        $this->estruturais = $estruturais;
    }

    /**
     * @return null
     */
    public function getInstitucoes()
    {
        return $this->institucoes;
    }

    /**
     * @param array $institucoes
     */
    public function setInstitucoes(array $institucoes)
    {
        $this->institucoes = $institucoes;
    }

    /**
     * @return null
     */
    public function getAno()
    {
        return $this->anousu;
    }

    /**
     * @param null $anousu
     */
    public function setAno($anousu)
    {
        $this->anousu = $anousu;
    }
}
