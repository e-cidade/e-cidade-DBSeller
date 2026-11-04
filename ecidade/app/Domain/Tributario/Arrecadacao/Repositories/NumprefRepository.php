<?php

namespace App\Domain\Tributario\Arrecadacao\Repositories;

use App\Domain\Tributario\Arrecadacao\Models\Numpref;

class NumprefRepository
{
    /**
     * @var Numpref
     */
    private $numpref;

    public function __construct()
    {
        $this->numpref = new Numpref();
    }

    /**
     * Retorna os dados com base no ano
     * @param $ano
     * @param string[] $campos
     * @return Numpref|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|null
     */
    public function getByAno($ano, $campos = ["*"])
    {
        return $this->numpref->where(
            "k03_anousu",
            "=",
            $ano
        )->select($campos)->first();
    }

    /**
     * @return bool
     */
    public function buscaParametroCustasHonorarios()
    {
        $ano = db_getsession("DB_anousu");
        $instit = db_getsession("DB_instit");
        $sSqlParCustasHonorarios = "SELECT k03_custashonorarios 
                                      FROM numpref 
                                     WHERE k03_anousu = {$ano} 
                                       AND k03_instit = {$instit}";

        $rsParCustasHonorarios = db_query($sSqlParCustasHonorarios);
        $k03_custashonorarios = pg_fetch_result($rsParCustasHonorarios, 0, "k03_custashonorarios");
        
        return ($k03_custashonorarios) ? true : false;
    }
}
