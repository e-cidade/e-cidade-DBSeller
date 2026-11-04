<?php


namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2020\BalanceteReceitaAnteriorBuilder2020;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2022\BalanceteReceitaAnteriorBuilder2022;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2023\BalanceteReceitaAnteriorBuilder2023;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\LayoutPad;
use Exception;
use Instituicao;
use ParametroPCASP;

/**
 * Class BalanceteReceitaAnteriorService
 * @package ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies
 */
class BalanceteReceitaAnteriorService extends PadService
{
    protected $fileName = 'BREC_ANT.TXT';

    /**
     * @var integer
     */
    protected $ano;
    protected $dataInicio;
    protected $dataFinal;

    /**
     * BalanceteReceitaAnteriorService constructor.
     * @param Instituicao[] $instituicoes
     * @param integer $ano para calculo
     */
    public function __construct(array $instituicoes, $ano)
    {
        $this->instituicoes = $instituicoes;
        $this->ano = $ano;
        $this->dataInicio = "{$ano}-01-01";
        $this->dataFinal = "{$ano}-12-31";
    }

    /**
     * @return LayoutPad[]
     * @throws Exception
     */
    protected function getDados()
    {
        $where = "o70_instit in ({$this->getListaInstituicoes()})";

        $sql = ReceitaSaldoComplemento(
            11,
            1,
            3,
            true,
            $where,
            $this->ano,
            $this->dataInicio,
            $this->dataFinal,
            true
        );

        $sql = "
            select case when fc_conplanoorcamento_grupo($this->ano, substr(o57_fonte, 1, 1)||'%', 9000) IS false
                    then substr(o57_fonte, 2)
                    else o57_fonte
                   end as fonte,
                   case when substr(o57_fonte, 1, 1)::int = 4
                       then nivel - 1
                       else nivel
                   end as nivel,
                   x.classe,
                   x.o57_descr,
                   round(sum(x.saldo_inicial), 2) as saldo_inicial,
                   round(sum(x.saldo_arrecadado_acumulado), 2) as saldo_arrecadado_acumulado,
                   x.recurso,
                   x.codigo_siconfi,
                   x.o70_codrec,
                   coalesce(o70_instit,0) as o70_instit,
                   case
                      when x.complemento is not null
                           and x.complemento in (3110, 3120, 3140, 3150, 3160, 1111, 1121, 2111, 2121, 0)
                          then x.complemento
                      else 0
                   end as complemento,
                   x.o70_concarpeculiar,
                   orgao_unidade

              from ({$sql}) as x
             where o57_fonte not in ('400000000000000')
             group by 1, 2, 3, 4, 7, 8, 9, 10, 11, 12, 13
             order by classe, fonte
        ";

        if (ParametroPCASP::utilizaPCASPNoAno($this->ano)) {
            $sql = analiseQueryPlanoOrcamento($sql, $this->ano);
        }

        $rs = db_query($sql);
        while ($state = pg_fetch_array($rs)) {
            $builder = $this->getBuilder();
            $state['exercicio'] = $this->ano;
            yield $builder->addDados($state)->build();
        }
    }

    /**
     * @return BalanceteReceitaAnteriorBuilder2020|BalanceteReceitaAnteriorBuilder2022
     *          |BalanceteReceitaAnteriorBuilder2023
     * @throws Exception
     */
    protected function getBuilder()
    {
        $ano = $this->ano + 1;
        switch ($ano) {
            case 2020:
            case 2021:
                return new BalanceteReceitaAnteriorBuilder2020();
            case 2022:
                return new BalanceteReceitaAnteriorBuilder2022();
            case 2023:
                return new BalanceteReceitaAnteriorBuilder2023();
            default:
                throw new Exception("Layout {$this->fileName} não foi implementado para o ano {$ano}.");
        }
    }
}
