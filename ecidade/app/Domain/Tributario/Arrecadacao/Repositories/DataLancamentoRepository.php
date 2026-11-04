<?php

namespace App\Domain\Tributario\Arrecadacao\Repositories;

use App\Domain\Tributario\Cadastro\Models\DbSyscampo;
use App\Traits\Pagination;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Classe para cuidar das datas de lancamento tributario
 * da tabela informacaodebito
 */
class DataLancamentoRepository
{
    use Pagination;
    /**
     * @param integer $cgm
     * @param integer $porPagina
     * @param integer $pagina
     */
    public function pesquisaCgm($cgm, $porPagina, $pagina = 1)
    {
        $sqlPesquisa = $this->getQueryPesquisa($cgm);
        $resultados = \DB::select($sqlPesquisa);

        return $this->paginate($resultados, $porPagina, $pagina);
    }

    /**
     * @param integer $matricula
     * @param integer $porPagina
     * @param integer $pagina
     */
    public function pesquisaMatricula($matricula, $porPagina, $pagina = 1)
    {
        $sqlPesquisa = $this->getQueryPesquisa("", $matricula);
        $resultados = \DB::select($sqlPesquisa);

        return $this->paginate($resultados, $porPagina, $pagina);
    }

    /**
     * @param integer $inscricao
     * @param integer $porPagina
     * @param integer $pagina
     */
    public function pesquisaInscricaoMunicipal($inscricao, $porPagina, $pagina = 1)
    {
        $sqlPesquisa = $this->getQueryPesquisa("", "", $inscricao);
        $resultados = \DB::select($sqlPesquisa);

        return $this->paginate($resultados, $porPagina, $pagina);
    }

    /**
     * @param integer $numpre
     * @param boolean $distinctNumpar
     */
    public function pesquisaNumpre($numpre, $distinctNumpar)
    {
        $sqlPesquisa = $this->getQueryPesquisa("", "", "", false, $numpre, $distinctNumpar);

        return \DB::select($sqlPesquisa);
    }


    /**
     * @param integer $numpre
     * @param integer $numpar
     * @param string $data
     * @param string $observacao
     */
    public function incluiRegistro($numpre, $numpar, $data, $observacao)
    {
        $sql = "insert into
                    informacaodebito (
                        k163_sequencial,
                        k163_numpre,
                        k163_numpar,
                        k163_data,
                        k163_observacao,
                        k163_manual
                    ) values (
                        nextval('informacaodebito_k163_sequencial_seq'),
                        {$numpre}, {$numpar}, '{$data}', '{$observacao}', true
                    )
                ";

        \DB::statement(\DB::raw($sql));

        return;
    }

    /**
     * @param integer $numpre
     * @param string $data
     * @param string $observacao
     */
    public function editaRegistro($numpre, $data, $observacao)
    {
        $sql = "update
                    informacaodebito
                set k163_data = '{$data}',
                    k163_observacao = '{$observacao}'
                where k163_numpre = {$numpre}
                ";

        \DB::statement(\DB::raw($sql));

        return;
    }

    /**
     * @param integer $numpreRegistro
     */
    public function excluiRegistro($numpreRegistro)
    {
        return \DB::table('informacaodebito')->where('k163_numpre', $numpreRegistro)->delete();
    }

    /**
     * Helper para gerar a query de pesquisa
     *
     * @param integer $cgm
     * @param integer $matricula
     * @param integer $inscricao
     * @param boolean $distinctNumpre
     * @param integer $numpre
     * @param boolean $distinctNumpar
     */
    private function getQueryPesquisa(
        $cgm = "",
        $matricula = "",
        $inscricao = "",
        $distinctNumpre = true,
        $numpre = "",
        $distinctNumpar = true
    ) {

        $join = "";
        $where = "";
        $whereIndex = "";
        $inicio = "";
        $final = "";

        if ($cgm !== "") {
            $join .= " inner join cgm on cgm.z01_numcgm = arrecad.k00_numcgm ";
            $where .= " and cgm.z01_numcgm = {$cgm} ";
        } elseif ($matricula !== "") {
            $join .= " inner join arrematric on arrematric.k00_numpre = arrecad.k00_numpre 
                       inner join iptubase on iptubase.j01_matric = arrematric.k00_matric 
                     ";
            $where .= " and arrematric.k00_matric = {$matricula} ";
        } elseif ($inscricao !== "") {
            $join .= " inner join arreinscr on arreinscr.k00_numpre = arrecad.k00_numpre ";
            $where .= " and arreinscr.k00_inscr = {$inscricao} ";
        } elseif ($numpre !== "") {
            $where = " and arrecad.k00_numpre = {$numpre} ";
        }

        if ($distinctNumpre) {
            $whereIndex = " where index = 1 ";
        }

        if (!$distinctNumpar) {
            $inicio = " select distinct(numpar) from ( ";
            $final = " )as resultados order by numpar  ";
        }

        $sql  = " {$inicio}                                                                                     ";
        $sql .= " select                                                                                        ";
        $sql .= "     tabela.numpre,                                                                            ";
        $sql .= "     tabela.numpar,                                                                            ";
        $sql .= "     tabela.data,                                                                              ";
        $sql .= "     tabela.tipodebito,                                                                        ";
        $sql .= "     tabela.datalancamento,                                                                    ";
        $sql .= "     tabela.observacao,                                                                        ";
        $sql .= "     tabela.manutencaoliberada,                                                                ";
        $sql .= "     tabela.debitolancado,                                                                     ";
        $sql .= "     tabela.lancamentomanual                                                                   ";
        $sql .= " from                                                                                          ";
        $sql .= "     (                                                                                         ";
        $sql .= "         select                                                                                ";
        $sql .= "             arrecad.k00_numpre as numpre,                                                     ";
        $sql .= "             arrecad.k00_numpar as numpar,                                                     ";
        $sql .= "             arrecad.k00_dtoper as data,                                                       ";
        $sql .= "             CONCAT(arretipo.k00_tipo, ' - ', arretipo.k00_descr) AS tipodebito,               ";
        $sql .= "             informacaodebito.k163_data AS datalancamento,                                     ";
        $sql .= "             informacaodebito.k163_observacao AS observacao,                                   ";
        $sql .= "             (informacaodebito.k163_data is not null) as debitolancado,                        ";
        $sql .= "             (k163_manual is true) as lancamentomanual,                                        ";
        $sql .= "             (                                                                                 ";
        $sql .= "                 arretipo.k00_tipo not in (3, 33)                                              ";
        $sql .= "                 and (                                                                         ";
        $sql .= "                     select                                                                    ";
        $sql .= "                         count(*)                                                              ";
        $sql .= "                     from                                                                      ";
        $sql .= "                         arreold                                                               ";
        $sql .= "                     where                                                                     ";
        $sql .= "                         arreold.k00_numpre = arrecad.k00_numpre                               ";
        $sql .= "                 ) = 0                                                                         ";
        $sql .= "             ) as manutencaoliberada,                                                          ";
        $sql .= "             ROW_NUMBER() OVER(                                                                ";
        $sql .= "                 PARTITION BY arrecad.k00_numpre                                               ";
        $sql .= "                 ORDER BY                                                                      ";
        $sql .= "                     arrecad.k00_dtoper                                                        ";
        $sql .= "             ) as index                                                                        ";
        $sql .= "         from                                                                                  ";
        $sql .= "             arrecad                                                                           ";
        $sql .= "             {$join}                                                                           ";
        $sql .= "             inner join arretipo ON arretipo.k00_tipo = arrecad.k00_tipo                       ";
        $sql .= "             left join informacaodebito on informacaodebito.k163_numpre = arrecad.k00_numpre   ";
        $sql .= "         where                                                                                 ";
        $sql .= "             1 = 1                                                                             ";
        $sql .= "             {$where}                                                                          ";
        $sql .= "     ) as tabela                                                                               ";
        $sql .= " {$whereIndex}                                                                                 ";
        $sql .= " {$final}                                                                                      ";

        return $sql;
    }

    /**
     * Metodo para buscar as labels para a tabela do frontend
     * @param array $nomeCampos
     * @return array
     */
    public function getLabelsLancamentoDebito($nomeCampos)
    {
        $labels = array_map(function ($nomeCampo) {
            return DbSyscampo::select("rotulo")->where('nomecam', '=', $nomeCampo)->first()->toArray();
        }, $nomeCampos);

        return $labels;
    }
}
