<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2023;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2023\Disponibilidade2023Builder;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\PadService;
use Exception;

class Disponibilidade2023Service extends PadService
{
    protected $fileName = 'CTA_DISP.TXT';

    public function __construct($exercicio, $instituicoes, $dataInicio, $dataFim)
    {
        $this->instituicoes = $instituicoes;
        $this->ano = $exercicio;
        $this->dataInicial = $dataInicio;
        $this->dataFinal = $dataFim;
    }

    /**
     * @return string
     */
    protected function getSql()
    {
        $listaEstruturais = [
            "estrutural like '11111%'",
            "estrutural like '114%'",
            "estrutural like '11131%'",
            "estrutural like '11132%'",
        ];

        $filtros = [
            sprintf('instituicao in (%s)', $this->getListaInstituicoes()),
            sprintf('(%s)', implode(' or ', $listaEstruturais)),
            "(saldo_anterior != 0 or saldo_debito != 0 or saldo_credito != 0)",
            "indicador_superavit = 'F'"
        ];
        $where = implode(' and ', $filtros);

        $sql = "
         with contas as (
            select estrutural,
                   reduzido,
                   exercicio,
                   codigo_conplano,
                   classe,
                   nome,
                   substring(siconfi, 2) as siconfi,
                   case when saldo_anterior != 0 then subrecurso else '0000' end as subrecurso,
                   indicador_superavit,
                   (saldo_anterior != 0) as tem_saldo_anterior,
                   case when c63_banco is null then '000' else c63_banco end as banco,
				   case when c63_agencia is null then '000' else c63_agencia end as agencia,
				   case when c63_conta is null then '0000' else c63_conta end as conta,
                   c63_dvagencia as digito_agencia,
                   c63_dvconta as digito_conta,
                   c63_codigooperacao as codigo_operacao,
                   case when o200_tribunal is true then complemento else 0 end as complemento,
                   codtrib as orgao_unidade,
                   db21_tipoinstit as tipo_instituicao
              from contabilidade.balancete_verificacao_por_recurso(
                     $this->ano, '$this->dataInicial', '$this->dataFinal', false, null
                   )
              join configuracoes.db_config on db_config.codigo = instituicao
              join orcamento.complementofonterecurso on o200_sequencial = complemento
              join contabilidade.conplanoreduz on c61_reduz = reduzido and c61_anousu = exercicio
              left join conplanoconta on c63_codcon = c61_codcon
                                     and c63_anousu = c61_anousu
                                     and c63_reduz = c61_reduz
            where $where
        ), agrupa as (
            select  distinct estrutural,
                    reduzido,
                    exercicio,
                    nome,
                    siconfi,
                    subrecurso,
                    complemento,
                    indicador_superavit,
                    tem_saldo_anterior,
                    banco,
                    agencia,
                    conta,
                    digito_agencia,
                    digito_conta,
                    codigo_operacao,
                    orgao_unidade,
                    tipo_instituicao
            from contas
            order by estrutural, siconfi, subrecurso, complemento
        ) select agrupa.* from agrupa
        ";
        return $sql;
    }

    protected function getDados()
    {
        $rs = $this->processaDadosRelatorio();
        while ($state = pg_fetch_assoc($rs)) {
            $builder = $this->getBuilder();
            yield $builder->addDados($state)
                ->build();
        }
    }

    /**
     * Essa versão vale apenas para o exercício de 2023 em anos posteriores olhar classes futuras
     * @return Disponibilidade2023Builder
     */
    protected function getBuilder()
    {
        return new Disponibilidade2023Builder();
    }

    /**
     * @return resource
     * @throws Exception
     */
    protected function processaDadosRelatorio()
    {
        $sql = $this->getSql();

        $rs = db_query($sql);
        if (!$rs) {
            throw new \Exception('Erro ao buscar contas bancárias.', 400);
        }

        if (pg_num_rows($rs) === 0) {
            throw new \Exception('Sem registros no arquivo disponibilidade.', 400);
        }

        return $rs;
    }
}
