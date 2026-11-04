<?php

namespace ECidade\Financeiro\Orcamento\Repository;

use ECidade\Educacao\Escola\Repository\Repository;
use ECidade\Financeiro\Orcamento\Model\AcompanhamentoCronogramaDespesa;
use ECidade\Financeiro\Orcamento\Model\Dotacao;
use Exception;

class AcompanhamentoCronogramaDespesaRepository extends Repository
{
    /**
     * @param $id
     * @return AcompanhamentoCronogramaDespesa
     * @throws Exception
     */
    public static function find($id)
    {
        $dao = new \cl_acompanhamentocronogramadespesa();
        $rs = db_query($dao->sql_query_dotacao($id));
        if (!$rs) {
            throw new Exception("Erro ao buscar acompanhamento do cronograma de desembolso.");
        }

        return AcompanhamentoCronogramaDespesa::fromState(pg_fetch_array($rs));
    }

    /**
     * @return AcompanhamentoCronogramaDespesa[]
     * @throws Exception
     */
    public function get()
    {
        $dao = new \cl_acompanhamentocronogramadespesa();
        $rs = db_query($dao->sql_query_dotacao(null, '*', null, implode(' and ', $this->scopes)));
        if (!$rs) {
            throw new Exception("Erro ao buscar acompanhamentos do cronograma de desembolso.");
        }

        $cronogramas = [];
        if (pg_num_rows($rs) === 0) {
            return $cronogramas;
        }

        while ($state = pg_fetch_assoc($rs)) {
            $cronogramas[] = AcompanhamentoCronogramaDespesa::fromState($state);
        }

        return $cronogramas;
    }

    /**
     * @param AcompanhamentoCronogramaDespesa $cronograma
     * @return AcompanhamentoCronogramaDespesa
     * @throws Exception
     */
    public function save(AcompanhamentoCronogramaDespesa $cronograma)
    {
        $dao = new \cl_acompanhamentocronogramadespesa();
        $dao->id = $cronograma->id;
        $dao->dotacao_id = $cronograma->dotacao_id;
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
            $dao->alterar($cronograma->id);
        }

        if ($dao->erro_status == 0) {
            throw new Exception("Erro ao salvar acompanhamento do cronograma de desembolso.");
        }

        $cronograma->id = $dao->id;
        return $cronograma;
    }

    public function excluir()
    {
        $where = !empty($this->scopes) ? implode(' and ', $this->scopes) : '';
        $dao = new \cl_acompanhamentocronogramadespesa();
        $dao->excluir(null, $where);

        if ($dao->erro_status == 0) {
            throw new Exception("Erro ao excluir acompanhamento do cronograma de desembolso.");
        }

        return true;
    }

    /**
     * @return AcompanhamentoCronogramaDespesa|false
     * @throws Exception
     */
    public function first()
    {
        $acompanhamentos = $this->get();
        if (count($acompanhamentos) === 0) {
            return false;
        }
        return array_shift($acompanhamentos);
    }

    public function scopeCodigo($id)
    {
        $this->scopes['id'] = "acompanhamentocronogramadespesa.id = {$id}";
        return $this;
    }

    public function scopeExercicio($exercicio)
    {
        $this->scopes['exercicio'] = "acompanhamentocronogramadespesa.exercicio = {$exercicio}";
        return $this;
    }

    public function scopeDotacao(Dotacao $dotacao)
    {
        $this->scopeCodigoDotacao($dotacao->getCodigoDotacao())
            ->scopeExercicio($dotacao->getAno());
        return $this;
    }

    public function scopeCodigoDotacao($codigoDotacao)
    {
        $this->scopes['dotacao'] = "dotacao_id = {$codigoDotacao}";
        return $this;
    }

    public function buscarDados($exercicio)
    {
        $dataInicio = "$exercicio-01-01";
        $dataFinal = "$exercicio-12-31";
        $this->scopeExercicio($exercicio);

        $sql = "
        select  acompanhamentocronogramadespesa.*,
                saldo_inicial,
                total_creditos as previsao_atualizada,
                empenhado_liquido,
                liquidado_acumulado,
                pago_acumulado,
                o58_orgao as orgao,
                trim(o40_descr) as descricao_orgao,
                o58_unidade as unidade,
                trim(o41_descr) as descricao_unidade,
                o58_funcao as funcao,
                trim(o52_descr) as descricao_funcao,
                o58_subfuncao as subfuncao,
                trim(o53_descr) as descricao_subfuncao,
                o58_programa as programa,
                trim(o54_descr) as descricao_programa,
                o58_projativ as projeto,
                trim(o55_descr) as descricao_projeto,
                o56_elemento as elemento,
                trim(o56_descr) as descricao_elemento,
                recurso,
                fonterecurso.gestao as fonte_recurso,
                trim(fonterecurso.descricao) as descricao_recurso,
                complemento,
                o200_descricao as descricao_complemento,
                o58_localizadorgastos as localizador_gasto,
                o11_descricao as descricao_localizador_gasto,
                cp,
                c58_descr as caracteristica_peculiar,
                nomeinst as nome_instituicao
          from orcamento.acompanhamentocronogramadespesa
          join fc_saldo_dotacao(exercicio, dotacao_id, '{$dataInicio}', '$dataFinal') on dotacao_id = reduzido
               and exercicio = ano
          join orcamento.orcdotacao on (o58_anousu, o58_coddot) = (exercicio, dotacao_id)
          join orcamento.orcorgao on (o40_anousu, o40_orgao) = (o58_anousu, o58_orgao)
          join orcamento.orcunidade on (o41_anousu, o41_orgao, o41_unidade) = (o58_anousu, o58_orgao, o58_unidade)
          join orcamento.orcfuncao on (o52_funcao) = (o58_funcao)
          join orcamento.orcsubfuncao on (o53_subfuncao) = (o58_subfuncao)
          join orcamento.orcprograma on (o54_anousu, o54_programa) = (o58_anousu, o58_programa)
          join orcamento.orcprojativ on (o55_anousu, o55_projativ) = (o58_anousu, o58_projativ)
          join orcamento.orcelemento on (o56_codele, o56_anousu) = (o58_codele, o58_anousu)
          join orcamento.fonterecurso on fonterecurso.orctiporec_id = recurso
               and fonterecurso.exercicio = acompanhamentocronogramadespesa.exercicio
          join orcamento.ppasubtitulolocalizadorgasto on (o11_sequencial) = (o58_localizadorgastos)
          join contabilidade.concarpeculiar on (c58_sequencial)= (o58_concarpeculiar)
          join complementofonterecurso on complementofonterecurso.o200_sequencial = complemento
          join configuracoes.db_config on (codigo) = (o58_instit)
        ";

        if (!empty($this->scopes)) {
            $sql .= " where " . implode(' and ', $this->scopes);
        }

        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Erro ao buscar dados do cronograma da despesa", 400);
        }

        if (pg_num_rows($rs) === 0) {
            throw new Exception("Sem registros para os filtros informados no cronograma da despesa", 400);
        }

        return \db_utils::getCollectionByRecord($rs);
    }

    public function scopeInstituicoes($valores, $operador = 'in')
    {
        $this->scopes['instituicoes'] = sprintf("o58_instit %s (%s)", $operador, implode(', ', $valores));
        return $this;
    }

    public function scopeProgramas($valores, $operador = 'in')
    {
        $this->scopes['programas'] = sprintf("o58_programa %s (%s)", $operador, implode(', ', $valores));
        return $this;
    }

    public function scopeOrgaos($valores, $operador = 'in')
    {
        $this->scopes['orgaos'] = sprintf("o58_orgao %s (%s)", $operador, implode(', ', $valores));
        return $this;
    }

    public function scopeProjetos($valores, $operador = 'in')
    {
        $this->scopes['projetos'] = sprintf("o58_projativ %s (%s)", $operador, implode(', ', $valores));
        return $this;
    }

    public function scopeFuncoes($valores, $operador = 'in')
    {
        $this->scopes['funcao'] = sprintf("o58_funcao %s (%s)", $operador, implode(', ', $valores));
        return $this;
    }

    public function scopeSubfuncoes($valores, $operador = 'in')
    {
        $this->scopes['subfuncao'] = sprintf("o58_subfuncao %s (%s)", $operador, implode(', ', $valores));
        return $this;
    }
    public function scopeElementos($valores, $operador = 'in')
    {
        $this->scopes['elementos'] = sprintf(
            "
            exists(
                select 1 from orcelemento
                 where o56_codele = o58_codele
                   and o56_anousu = o58_anousu
                   and o56_elemento %s ('%s')
            )",
            $operador,
            implode("', '", $valores)
        );
        return $this;
    }

    public function scopeRecursos($valores, $operador = 'in')
    {
        $this->scopes['recursos'] = sprintf("o58_codigo %s (%s)", $operador, implode(', ', $valores));
        return $this;
    }

    public function scopeOrgaosUnidades($valores, $operador = 'in')
    {
        $filtroUnidades = [];
        foreach ($valores as $unidade) {
            $data = explode('-', $unidade);
            $filtroUnidades[] = sprintf(
                '(o58_orgao %s (%s) and o58_unidade %s (%s))',
                $operador,
                $data[0],
                $operador,
                $data[1]
            );
        }
        $this->scopes['orgao-unidade'] = sprintf('(%s)', implode(' or ', $filtroUnidades));
        return $this;
    }
}
