<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Fix;

use App\Domain\Financeiro\Contabilidade\Models\ConlancamRecurso;
use Illuminate\Support\Facades\DB;

class CorrigeLancamentosEstoquePatrimonio
{

    /**
     * @var string
     */
    private $data;

    public function arrumar($data)
    {
        $this->data = implode('-', array_reverse(explode('/', $data)));

        $this->corrigir();
        $this->corrigirRecursoAlmoxOuPatrimonioEmpenho();
    }


    private function corrigir()
    {
        DB::unprepared(<<<SQL
drop table if exists lancamentos_caso_um;

create table lancamentos_caso_um as
select c69_anousu, c69_codlan, c69_credito, c69_debito, reduzcredito.c61_instit,
       reduzcredito.c61_codigo as recurso_credito, reduzdebito.c61_codigo as recurso_debito, c69_ordem
  from contabilidade.conlancamval
  join contabilidade.conlancam on c70_codlan = c69_codlan
  join contabilidade.conlancamdoc on c70_codlan = c71_codlan
  join contabilidade.conlancaminstit on c70_codlan = c02_codlan
  join contabilidade.conplanoreduz reduzdebito on c69_debito = reduzdebito.c61_reduz
       and c69_anousu = reduzdebito.c61_anousu
  join contabilidade.conplano planodebito     on reduzdebito.c61_codcon = planodebito.c60_codcon
       and reduzdebito.c61_anousu = planodebito.c60_anousu
  join contabilidade.conplanoreduz reduzcredito on c69_credito = reduzcredito.c61_reduz
       and c69_anousu = reduzcredito.c61_anousu
  join contabilidade.conplano planocredito    on reduzcredito.c61_codcon = planocredito.c60_codcon
       and reduzcredito.c61_anousu = planocredito.c60_anousu
 where c70_data >= '$this->data'
   and c71_coddoc in (400, 401, 402, 403, 404, 600, 601, 602, 603, 604, 605, 700, 701, 702, 703, 704, 705,
                      706, 708, 707, 709, 710)
order by c69_codlan, c69_ordem;

delete from contabilidade.conlancamrecurso
using lancamentos_caso_um
where c130_conlancam = c69_codlan;


insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c69_codlan, recurso_debito, c69_debito, c69_anousu, 'D'
  from lancamentos_caso_um;

insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c69_codlan, recurso_credito, c69_credito, c69_anousu, 'C'
  from lancamentos_caso_um;
SQL
        );
    }

    private function corrigirRecursoAlmoxOuPatrimonioEmpenho()
    {
        DB::unprepared(<<<SQL
drop table if exists lancamentos_caso_dois;

create table lancamentos_caso_dois as
select c69_anousu, c69_codlan, c71_coddoc, c69_credito, c69_debito, reduzcredito.c61_instit,
       reduzcredito.c61_codigo as recurso_credito, reduzdebito.c61_codigo as recurso_debito,
       o15_codigo as recurso_empenho, o15_complemento as complemento_empenho,
       c69_ordem
  from contabilidade.conlancamval
  join contabilidade.conlancam on c70_codlan = c69_codlan
  join contabilidade.conlancamemp on c75_codlan = c70_codlan
  join empempenho on e60_numemp = c75_numemp
  left join empenho.empresto on e91_numemp = e60_numemp
       and e91_anousu = c70_anousu
  join origemcomplementorecurso on o206_numero = e60_numemp
       and o206_origem = (case when e91_numemp is not null then 10 else 1 end)
  join orcamento.orctiporec on o15_codigo = o206_recurso
  join contabilidade.conlancamdoc on c70_codlan = c71_codlan
  join contabilidade.conlancaminstit on c70_codlan = c02_codlan
  join contabilidade.conplanoreduz reduzdebito on c69_debito = reduzdebito.c61_reduz
       and c69_anousu = reduzdebito.c61_anousu
  join contabilidade.conplano planodebito     on reduzdebito.c61_codcon = planodebito.c60_codcon
       and reduzdebito.c61_anousu = planodebito.c60_anousu
  join contabilidade.conplanoreduz reduzcredito on c69_credito = reduzcredito.c61_reduz
       and c69_anousu = reduzcredito.c61_anousu
  join contabilidade.conplano planocredito    on reduzcredito.c61_codcon = planocredito.c60_codcon
       and reduzcredito.c61_anousu = planocredito.c60_anousu
 where c70_data >= '$this->data'
   and c71_coddoc in (210, 212, 208, 214, 211, 213, 209, 215)
order by c69_codlan, c69_ordem;

delete from contabilidade.conlancamrecurso using lancamentos_caso_dois where c130_conlancam = c69_codlan;
delete from contabilidade.conlancamcomplementorecurso using lancamentos_caso_dois where o201_codlan = c69_codlan;

insert into contabilidade.conlancamcomplementorecurso (o201_codlan, o201_complemento, o201_orctiporec)
select distinct c69_codlan, complemento_empenho, recurso_empenho from lancamentos_caso_dois;

SQL
        );

        $dados = DB::select('select * from lancamentos_caso_dois');

        $organizados = [];
        $xyz = [];
        foreach ($dados as $dado) {
            if (!array_key_exists($dado->c69_codlan, $organizados)) {
                $organizados[$dado->c69_codlan] = [];
            }

            $xyz[] = $this->preparaDados($dado, 'D');
            $xyz[] = $this->preparaDados($dado, 'C');
            if (count($xyz) > 100) {
                $this->insere($xyz);
                $xyz = [];
            }
        }

        $this->insere($xyz);
    }

    private function preparaDados($dado, $tipo)
    {
        $codlan = $dado->c69_codlan;
        if ($dado->c69_ordem != 1) {
            $conta = $tipo === 'D' ? $dado->c69_debito : $dado->c69_credito;
            return $this->build($codlan, $dado->recurso_empenho, $dado->c69_anousu, $conta, $tipo);
        }

        if (in_array($dado->c71_coddoc, [209, 211, 213, 215]) && $tipo === 'C') {
            return $this->build($codlan, $dado->recurso_credito, $dado->c69_anousu, $dado->c69_credito, $tipo);
        }
        if (in_array($dado->c71_coddoc, [209, 211, 213, 215]) && $tipo === 'D') {
            return $this->build($codlan, $dado->recurso_empenho, $dado->c69_anousu, $dado->c69_debito, $tipo);
        }

        if (in_array($dado->c71_coddoc, [210, 212, 208, 214]) && $tipo === 'D') {
            return $this->build($codlan, $dado->recurso_debito, $dado->c69_anousu, $dado->c69_debito, $tipo);
        }

        if (in_array($dado->c71_coddoc, [210, 212, 208, 214]) && $tipo === 'C') {
            return $this->build($codlan, $dado->recurso_empenho, $dado->c69_anousu, $dado->c69_credito, $tipo);
        }
    }

    /**
     * @param $codlan
     * @param $recurso
     * @param $ano
     * @param $conta
     * @param $natureza
     * @return array
     */
    public function build($codlan, $recurso, $ano, $conta, $natureza)
    {
        return ['c130_conlancam' => $codlan,
            'c130_orctiporec' => $recurso,
            'c130_anousu' => $ano,
            'c130_conta' => $conta,
            'c130_natureza' => $natureza
        ];
    }

    private function insere(array $xyz)
    {
        $model = new ConlancamRecurso();
        $model->insert($xyz);
    }
}
