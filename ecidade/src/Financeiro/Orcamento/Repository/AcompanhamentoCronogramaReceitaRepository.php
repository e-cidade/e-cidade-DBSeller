<?php

namespace ECidade\Financeiro\Orcamento\Repository;

use ECidade\Educacao\Escola\Repository\Repository;
use ECidade\Financeiro\Orcamento\Model\AcompanhamentoCronogramaReceita;
use ECidade\Financeiro\Orcamento\Model\Receita;
use Exception;

class AcompanhamentoCronogramaReceitaRepository extends Repository
{
    /**
     * @param $id
     * @return AcompanhamentoCronogramaReceita
     * @throws Exception
     */
    public static function find($id)
    {
        $dao = new \cl_acompanhamentocronogramareceita();
        $rs = db_query($dao->sql_query_receita($id));
        if (!$rs) {
            throw new Exception("Erro ao buscar acompanhamento do cronograma de desembolso.");
        }

        return AcompanhamentoCronogramaReceita::fromState(pg_fetch_array($rs));
    }

    /**
     * @return AcompanhamentoCronogramaReceita[]
     * @throws Exception
     */
    public function get()
    {
        $dao = new \cl_acompanhamentocronogramareceita();
        $rs = db_query($dao->sql_query_receita(null, '*', null, implode(' and ', $this->scopes)));
        if (!$rs) {
            throw new Exception("Erro ao buscar acompanhamentos do cronograma de desembolso.");
        }

        $cronogramas = [];
        if (pg_num_rows($rs) === 0) {
            return $cronogramas;
        }

        while ($state = pg_fetch_assoc($rs)) {
            $cronogramas[] = AcompanhamentoCronogramaReceita::fromState($state);
        }

        return $cronogramas;
    }

    /**
     * @param AcompanhamentoCronogramaReceita $cronograma
     * @return AcompanhamentoCronogramaReceita
     * @throws Exception
     */
    public function save(AcompanhamentoCronogramaReceita $cronograma)
    {
        $dao = new \cl_acompanhamentocronogramareceita();
        $dao->id = $cronograma->id;
        $dao->receita_id = $cronograma->receita_id;
        $dao->exercicio = $cronograma->exercicio;
        $dao->base_calculo = $cronograma->base_calculo;
        $dao->janeiro = "$cronograma->janeiro";
        $dao->fevereiro = "$cronograma->fevereiro";
        $dao->marco = "$cronograma->marco";
        $dao->abril = "$cronograma->abril";
        $dao->maio = "$cronograma->maio";
        $dao->junho = "$cronograma->junho";
        $dao->julho = "$cronograma->julho";
        $dao->agosto = "$cronograma->agosto";
        $dao->setembro = "$cronograma->setembro";
        $dao->outubro = "$cronograma->outubro";
        $dao->novembro = "$cronograma->novembro";
        $dao->dezembro = "$cronograma->dezembro";

        if (empty($dao->id)) {
            $dao->incluir(null);
        } else {
            $dao->alterar($dao->id);
        }

        if ($dao->erro_status == 0) {
            throw new Exception("Erro ao salvar acompanhamento da estimativa da receita." . $dao->erro_msg);
        }

        $cronograma->id = $dao->id;
        return $cronograma;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function excluir()
    {
        $where = !empty($this->scopes) ? implode(' and ', $this->scopes) : '';
        $dao = new \cl_acompanhamentocronogramareceita();
        $dao->excluir(null, $where);

        if ($dao->erro_status == 0) {
            throw new Exception("Erro ao excluir acompanhamento da estimativa da receita.");
        }

        return true;
    }

    /**
     * @return AcompanhamentoCronogramaReceita|false|null
     * @throws Exception
     */
    public function first()
    {
        $receitas = $this->get();
        if (count($receitas) === 0) {
            return false;
        }
        return array_shift($receitas);
    }

    /**
     * @param $exercicio
     * @return $this
     */
    public function scopeExercicio($exercicio)
    {
        $this->scopes['exercicio'] = "acompanhamentocronogramareceita.exercicio = {$exercicio}";
        return $this;
    }

    /**
     * @param Receita $receita
     * @return $this
     */
    public function scopeReceita(Receita $receita)
    {
        $this->scopeReduzido($receita->getReduzido())
            ->scopeExercicio($receita->getAno());
        return $this;
    }

    /**
     * @param $reduzido
     * @return $this
     */
    public function scopeReduzido($reduzido)
    {
        $this->scopes['reduzido'] = "acompanhamentocronogramareceita.receita_id = {$reduzido}";
        return $this;
    }

    public function scopeInstituicoes($valores, $operador = 'in')
    {
        $this->scopes['instituicoes'] = sprintf("o70_instit %s (%s)", $operador, implode(', ', $valores));
        return $this;
    }

    /**
     * @param $id
     * @return $this
     */
    public function scopeCodigo($id)
    {
        $this->scopes['id'] = "acompanhamentocronogramareceita.id = {$id}";
        return $this;
    }

    /**
     * @param $exercicio
     * @return \stdClass[]
     * @throws Exception
     */
    public function buscarDados($exercicio)
    {
        $dataInicio = "$exercicio-01-01";
        $dataFinal = "$exercicio-12-31";
        $this->scopeExercicio($exercicio);

        $where = implode(' and ', $this->scopes);

        $sql = "
            select acompanhamentocronogramareceita.id as id_cronograma,
                   acompanhamentocronogramareceita.receita_id as receita_id,
                   acompanhamentocronogramareceita.exercicio as exercicio,
                   acompanhamentocronogramareceita.base_calculo as base_calculo,
                   acompanhamentocronogramareceita.janeiro,
                   acompanhamentocronogramareceita.fevereiro,
                   acompanhamentocronogramareceita.marco,
                   acompanhamentocronogramareceita.abril,
                   acompanhamentocronogramareceita.maio,
                   acompanhamentocronogramareceita.junho,
                   acompanhamentocronogramareceita.julho,
                   acompanhamentocronogramareceita.agosto,
                   acompanhamentocronogramareceita.setembro,
                   acompanhamentocronogramareceita.outubro,
                   acompanhamentocronogramareceita.novembro,
                   acompanhamentocronogramareceita.dezembro,
                   balancete_receita_complemento.*,
                   substr(natureza,1,1)::int4 as classe,
                   substr(natureza, 2)::varchar as resto,
                   orcfontes.o57_descr AS descricao_fonte,
                   orcorgao.o40_descr AS descricao_orgao,
                   orcunidade.o41_descr AS descricao_unidade,
                   concarpeculiar.c58_descr AS caracteristica_peculiar,
                   db_config.nomeinst AS nome_instituicao,
                   orctiporec.o15_descr AS descricao_recurso,
                   fonterecurso.gestao AS recurso,
                   o200_descricao AS complemento,
                   CASE c60_identificadoresultadoprimario
                       WHEN 1 THEN 'Financeiro'
                       WHEN 2 THEN 'Primário'
                       WHEN 3 THEN 'Primária Obrigatória'
                       WHEN 4 THEN 'Primária Discricionária'
                       ELSE 'Não se Aplica'
                   END AS identificador_resultado,
                   CASE esfera
                       WHEN 10 THEN 'F - Orçamento Fiscal'
                       WHEN 20 THEN 'S - Orçamento da Seguridade Social'
                       WHEN 30 THEN 'I - Orçamento de Investimento'
                       ELSE 'Não se Aplica'
                   END AS esfera_orcamentaria
              from orcreceita
              join acompanhamentocronogramareceita on (exercicio, receita_id) = (o70_anousu, o70_codrec)
              join balancete_receita_complemento(
                     o70_anousu, o70_codfon, o70_concarpeculiar, '{$dataInicio}', '{$dataFinal}'
                   ) on o70_codfon = fonte and o70_anousu = ano
              join orcamento.orcunidade
                   ON (o41_anousu, o41_orgao, o41_unidade) = (o70_anousu, o70_orcorgao, o70_orcunidade)
              join orcamento.orcorgao ON (o40_anousu, o40_orgao) = (o70_anousu, o70_orcorgao)
              join contabilidade.concarpeculiar ON c58_sequencial = orcreceita.o70_concarpeculiar
              join configuracoes.db_config ON db_config.codigo = orcreceita.o70_instit
              join orcamento.orcfontes ON (o57_codfon, o57_anousu) = (o70_codfon, o70_anousu)
              join orcamento.orctiporec ON orctiporec.o15_codigo = orcreceita.o70_codigo
              join orcamento.fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
                   and fonterecurso.exercicio = orcreceita.o70_anousu
              join complementofonterecurso ON complementofonterecurso.o200_sequencial = orctiporec.o15_complemento
              join contabilidade.conplanoorcamento ON (c60_codcon, c60_anousu) = (o57_codfon, o57_anousu)
              where {$where}
              order by resto, natureza;
        ";
        $rs = db_query($sql);
        if (!$rs || pg_num_rows($rs) === 0) {
            throw new Exception("Erro ao buscar dados do cronograma da receita", 400);
        }

        return \db_utils::getCollectionByRecord($rs);
    }

    public function getDadosRelatorio()
    {
        $where = implode(' and ', $this->scopes);
        $sql = "
            select acompanhamentocronogramareceita.id as id_cronograma,
                   acompanhamentocronogramareceita.receita_id as receita_id,
                   acompanhamentocronogramareceita.exercicio as exercicio,
                   acompanhamentocronogramareceita.base_calculo as base_calculo,
                   acompanhamentocronogramareceita.janeiro,
                   acompanhamentocronogramareceita.fevereiro,
                   acompanhamentocronogramareceita.marco,
                   acompanhamentocronogramareceita.abril,
                   acompanhamentocronogramareceita.maio,
                   acompanhamentocronogramareceita.junho,
                   acompanhamentocronogramareceita.julho,
                   acompanhamentocronogramareceita.agosto,
                   acompanhamentocronogramareceita.setembro,
                   acompanhamentocronogramareceita.outubro,
                   acompanhamentocronogramareceita.novembro,
                   acompanhamentocronogramareceita.dezembro,
                   orcfontes.o57_fonte AS natureza,
                   orcfontes.o57_descr AS descricao,
                   o15_codigo as codigo_recurso,
                   fonterecurso.descricao AS descricao_recurso,
                   fonterecurso.gestao AS fonte_recurso,
                   o200_sequencial AS complemento,
                   o200_descricao AS descricao_complemento
              from orcreceita
              join acompanhamentocronogramareceita on (exercicio, receita_id) = (o70_anousu, o70_codrec)
              join orcamento.orcfontes ON (o57_codfon, o57_anousu) = (o70_codfon, o70_anousu)
              join orcamento.orctiporec ON orctiporec.o15_codigo = orcreceita.o70_codigo
              join orcamento.fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
                   and fonterecurso.exercicio = orcreceita.o70_anousu
              join complementofonterecurso ON complementofonterecurso.o200_sequencial = orctiporec.o15_complemento
        where {$where}
              order by o57_fonte;
        ";

        $rs = db_query($sql);
        if (!$rs || pg_num_rows($rs) === 0) {
            throw new Exception("Erro ao buscar dados do cronograma da receita", 400);
        }

        return \db_utils::getCollectionByRecord($rs);
    }

    public function scopeRecursos(array $recursos)
    {
        $this->scopes['codigo_recursos'] = sprintf("o15_codigo in (%s) ", implode(', ', $recursos));
    }

    public function getDadosRelatorioPlanoPadrao($tipoPlano)
    {
        $this->scopes['tipoPlano'] = $tipoPlano === 'uniao' ? 'uniao is true' : 'uniao is false';
        $where = implode(' and ', $this->scopes);
        $sql = "
            select
                   acompanhamentocronogramareceita.exercicio as exercicio,
                   acompanhamentocronogramareceita.base_calculo as base_calculo,
                   planoreceita.conta AS natureza,
                   planoreceita.nome  AS descricao,
                   o15_codigo as codigo_recurso,
                   fonterecurso.descricao AS descricao_recurso,
                   fonterecurso.gestao AS fonte_recurso,
                   fonterecurso.codigo_siconfi AS siconfi,
                   o200_sequencial AS complemento,
                   o200_descricao AS descricao_complemento,
                   o70_concarpeculiar as cp,
                   o70_instit as instituicao,
                   array_agg(acompanhamentocronogramareceita.id) as id_cronograma,
                   array_agg(acompanhamentocronogramareceita.receita_id) as receita_id,
                   sum(acompanhamentocronogramareceita.janeiro) as janeiro,
                   sum(acompanhamentocronogramareceita.fevereiro) as fevereiro,
                   sum(acompanhamentocronogramareceita.marco) as marco,
                   sum(acompanhamentocronogramareceita.abril) as abril,
                   sum(acompanhamentocronogramareceita.maio) as maio,
                   sum(acompanhamentocronogramareceita.junho) as junho,
                   sum(acompanhamentocronogramareceita.julho) as julho,
                   sum(acompanhamentocronogramareceita.agosto) as agosto,
                   sum(acompanhamentocronogramareceita.setembro) as setembro,
                   sum(acompanhamentocronogramareceita.outubro) as outubro,
                   sum(acompanhamentocronogramareceita.novembro) as novembro,
                   sum(acompanhamentocronogramareceita.dezembro) as dezembro
              from orcreceita
              join acompanhamentocronogramareceita on (exercicio, receita_id) = (o70_anousu, o70_codrec)
              join conplanoorcamento on (c60_codcon, c60_anousu) = (o70_codfon, o70_anousu)
              join planoreceitaconplanoorcamento on conplanoorcamento_codigo = c60_codigo
              join planoreceita on planoreceita.id = planoreceita_id
              join orcamento.orctiporec ON orctiporec.o15_codigo = orcreceita.o70_codigo
              join orcamento.fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
                   and fonterecurso.exercicio = orcreceita.o70_anousu
              join complementofonterecurso ON complementofonterecurso.o200_sequencial = orctiporec.o15_complemento
        where {$where}
        group by 1,2,3,4,5,6,7,8,9,10,11,12
        order by 3;
        ";

        $rs = db_query($sql);
        if (!$rs || pg_num_rows($rs) === 0) {
            throw new Exception("Erro ao buscar dados do cronograma da receita", 400);
        }

        return \db_utils::getCollectionByRecord($rs);
    }
}
