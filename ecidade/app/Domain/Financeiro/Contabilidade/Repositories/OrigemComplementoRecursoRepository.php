<?php


namespace App\Domain\Financeiro\Contabilidade\Repositories;

use App\Domain\Financeiro\Orcamento\Models\FonteRecurso;
use App\Domain\Financeiro\Orcamento\Models\Recurso;
use Carbon\Carbon;
use Exception;

/**
 * Class OrigemComplementoRecursoRepository
 * @package App\Domain\Financeiro\Contabilidade\Repositories
 */
class OrigemComplementoRecursoRepository
{
    private $scopes = [];

    private $ano;

    /**
     * @param Carbon $data1
     * @param Carbon $data2
     * @return $this
     */
    public function scopeIntervaloEmissaoEmpenho($data1, $data2)
    {
        $this->scopes['emissao'] = "
            e60_emiss >= '{$data1}' and e60_emiss <= '{$data2}'
        ";

        return $this;
    }

    /**
     * @param FonteRecurso[]|\Illuminate\Database\Eloquent\Collection $recursos
     * @return OrigemComplementoRecursoRepository
     */
    public function scopeRecursosDespesa($recursos)
    {
        $idsRecurso = $recursos->map(function (FonteRecurso $recurso) {
            return $recurso->o15_codigo;
        })->toArray();

        $codigos = implode(', ', $idsRecurso);

        $this->scopes['recursos'] = "(o206_recurso in ({$codigos}) or o58_codigo in ({$codigos}))";
        return $this;
    }

    /**
     * @param $codigoEmpenho
     * @return $this
     */
    public function scopeIdEmpenho($codigoEmpenho)
    {
        $this->scopes['IdRecurso'] = "e60_numemp = {$codigoEmpenho}";
        return $this;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getComplementosEmpenho()
    {
        $scopesRp = $this->scopes;
        $scopesEmpenho = $this->scopes;

        if (!empty($this->ano)) {
            $scopesEmpenho[] = "
                not exists(
                select 1 from empresto where empresto.e91_numemp = e60_numemp and e91_anousu <= {$this->ano}
                )
            ";
            $scopesRp[] = "e91_anousu = {$this->ano}";
        }

        $whereRp = implode(' and ', $this->scopes);
        $whereEmpenho = implode(' and ', $scopesEmpenho);
        $campos = [
            'e60_numemp as codigo',
            "e60_codemp||'/'||e60_anousu as numero",
            "fc_estruturaldotacao(e60_anousu, o58_coddot) as dotacao",
            'e60_vlremp as valor',
            'o206_complementorecurso as complemento',
            "e60_emiss"
        ];

        $campos = implode(', ', $campos);

        $sql = "
        select {$campos}, 'f' as rp
          from empempenho
          join orcdotacao  on orcdotacao.o58_anousu    = empempenho.e60_anousu
                          and orcdotacao.o58_coddot    = empempenho.e60_coddot
          left join origemcomplementorecurso on origemcomplementorecurso.o206_numero = empempenho.e60_numemp
                                            and origemcomplementorecurso.o206_origem = 1
         where {$whereEmpenho}
        union all
        select {$campos}, 't' as rp
          from empempenho
          join empresto on empresto.e91_numemp = e60_numemp
          join orcdotacao  on orcdotacao.o58_anousu    = empempenho.e60_anousu
                          and orcdotacao.o58_coddot    = empempenho.e60_coddot
          left join origemcomplementorecurso on origemcomplementorecurso.o206_numero = empempenho.e60_numemp
                                            and origemcomplementorecurso.o206_origem = 10
         where $whereRp
        ";

        $rs = db_query($sql);

        if (pg_num_rows($rs) === 0) {
            throw new Exception("Nenhum empenho encontrado para os filtros informados.", 406);
        }

        return pg_fetch_all($rs);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getComplementosReceita()
    {
        $where = implode(' and ', $this->scopes);
        $sql = "
         select c70_codlan as codigo,
                fc_estruturalreceita(o70_anousu, orcreceita.o70_codrec) || ' - ' ||o57_descr as receita,
                c70_valor as valor,
                o201_complemento as complemento,
                exists(select 1 from orcreceita
                   join orcfontesdes on  (o60_codfon, o60_anousu) = (o70_codfon, o70_anousu)
                  where o70_anousu = c74_anousu
                    and o70_codrec =  c74_codrec
                ) as tem_desdobramento
           from conlancamrec
                join conlancam on conlancam.c70_codlan = conlancamrec.c74_codlan
                join conlancaminstit on conlancaminstit.c02_codlan = conlancam.c70_codlan
                join orcreceita on orcreceita.o70_anousu = conlancamrec.c74_anousu
                	 and orcreceita.o70_codrec = conlancamrec.c74_codrec
                join orctiporec on orctiporec.o15_codigo = orcreceita.o70_codigo
                join orcfontes on orcfontes.o57_codfon = orcreceita.o70_codfon
                     and orcfontes.o57_anousu = orcreceita.o70_anousu
                join conlancamcomplementorecurso on o201_codlan = c70_codlan
                left join condataconf on condataconf.c99_instit = c02_instit
                          and condataconf.c99_anousu = c70_anousu
          where {$where}
        ";


        $rs = db_query($sql);
        if (pg_num_rows($rs) === 0) {
            throw new Exception("Nenhum lançamento encontrado para os filtros informados.", 406);
        }

        return pg_fetch_all($rs);
    }

    /**
     * @param $receita
     * @return $this
     */
    public function scopeCodigoReceita($receita)
    {
        $this->scopes['receita'] = "o70_codrec = {$receita}";
        return $this;
    }

    public function scopeRecursosReceita($recursos)
    {
        $idsRecurso = $recursos->map(function (FonteRecurso $recurso) {
            return $recurso->o15_codigo;
        })->toArray();

        $recursos = implode(', ', $idsRecurso);

        $this->scopes['recursos'] = "(o201_orctiporec in ({$recursos}) or o70_codigo in ({$recursos}))";
        return $this;
    }

    /**
     * @param Carbon $data1
     * @param Carbon $data2
     * @return $this
     */
    public function scopeIntervaloLancamento($data1, $data2)
    {
        $this->scopes['emissao'] = "c70_data >= '{$data1}' and c70_data <= '{$data2}'";
        return $this;
    }

    /**
     * Informa o ano em que esta trabalhando
     * @param integer $ano
     */
    public function setAno($ano)
    {
        $this->ano = $ano;
    }

    /**
     * @return $this
     */
    public function scopeValidaEncerramentoContabil()
    {
        $this->scopes['encerramento'] = "
           (case when c99_data is not null then conlancam.c70_data > c99_data else true end)
        ";
        return $this;
    }
}
