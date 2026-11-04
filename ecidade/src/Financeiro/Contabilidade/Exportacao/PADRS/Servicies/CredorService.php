<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2023\Credor2023Builder;
use Exception;

abstract class CredorService extends PadService
{
    /**
     * @var string
     */
    protected $fileName = 'CREDOR.TXT';

    public function __construct($exercicio, $instituicoes, $dataInicio, $dataFim)
    {
        $this->instituicoes = $instituicoes;
        $this->ano = $exercicio;
        $this->dataInicial = $dataInicio;
        $this->dataFinal = $dataFim;
    }

    protected function getDados()
    {
        $sql = $this->getSql();
        $rs = db_query($sql);
        if (!$rs && pg_num_rows($rs) === 0) {
            throw new Exception('Erro ao buscar os credores.');
        }

        while ($state = pg_fetch_array($rs)) {
            $builder = $this->getBuilder();
            yield $builder->addDados($state)->build();
        }
    }

    protected function getBuilder()
    {
        return new Credor2023Builder();
    }

    /**
     * @return string
     */
    protected function getSql()
    {
        $sql = "
        with cgms_empenho_exercicio as (
            select e60_numcgm
              from empempenho
             where e60_anousu = {$this->ano}
             union
            select e60_numcgm
              from empenho.empresto
              join empempenho on e60_numemp = e91_numemp
             where e91_anousu = {$this->ano}
        ), dados as (
        select distinct
               z01_numcgm as cgm,
               z01_nome   as nome,
               z01_cgccpf as cnpj_cpf,
               z01_incest as inscricao_estadual,
               '' as inscricao_municipal,
               z01_ender as endereco,
               z01_munic as cidade,
               z01_uf as uf,
               z01_cepcon as cep,
               z01_telcon as fone,
               z01_telcon as fax,
               case when length(z01_cgccpf) = 11 then 1 else 2 end as tipo_pessoa
        from cgm
        join cgms_empenho_exercicio on e60_numcgm = z01_numcgm
        ) select * from dados";

        return $sql;
    }
}
