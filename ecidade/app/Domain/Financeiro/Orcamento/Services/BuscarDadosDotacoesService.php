<?php

namespace App\Domain\Financeiro\Orcamento\Services;

use App\Domain\Financeiro\Orcamento\Models\Dotacao;
use App\Domain\Financeiro\Orcamento\Models\Elemento;
use cl_orcprograma;
use cl_permusuario_dotacao;
use db_utils;
use Illuminate\Support\Facades\DB;
use Matrix\Exception;
use stdClass;

class BuscarDadosDotacoesService
{
    public function getDotacoes($elemento)
    {
        $resultado = Dotacao
            ::select(
                DB::raw('fc_estruturaldotacao(o58_anousu,o58_coddot) as o50_estrutdespesa'),
                'o58_coddot',
                'o58_orgao',
                'o58_unidade',
                'o58_programa',
                'o41_unidade',
                'o41_descr',
                'o55_descr',
                'o58_anousu',
                'o55_finali',
                'o56_descr'
            )
            ->join('orctiporec', 'o15_codigo', '=', 'o58_codigo')
            ->join('orcelemento', function ($join) {
                $join->on('o56_codele', '=', 'o58_codele')
                    ->on('o56_anousu', '=', 'o58_anousu');
            })
            ->join('orcunidade', function ($join) {
                $join->on('o41_unidade', '=', 'o58_unidade')
                    ->on('o41_anousu', '=', 'o58_anousu')
                    ->on('o41_orgao', '=', 'o58_orgao');
            })
            ->join('orcprojativ', function ($join) {
                $join->on('o55_projativ', '=', 'o58_projativ')
                    ->on('o55_anousu', '=', 'o58_anousu');
            })
            ->where('o56_elemento', 'like', $elemento.'%')
            ->where('o58_anousu', '=', db_getsession("DB_anousu"))
            ->whereIn('o58_instit', [db_getsession("DB_instit")])
            ->orderBy('o50_estrutdespesa')
            ->get();
        return $resultado;
    }

    public function getSaldoDotacao($codigoDotacao)
    {
        $dotacao = new \Dotacao($codigoDotacao, db_getsession("DB_anousu"));
        return $dotacao->getSaldoFinal();
    }

    private function getOrgao()
    {
        $sql = " SELECT db20_orgao as o40_orgao, o40_descr
      FROM DB_PERMEMP
      INNER JOIN DB_USUPERMEMP U ON U.DB21_CODPERM = DB20_CODPERM AND U.DB21_ID_USUARIO = "
            . db_getsession("DB_id_usuario") .
            "INNER JOIN orcorgao on o40_orgao = db20_orgao and o40_anousu = db20_anousu
            LEFT OUTER JOIN DB_DEPARTORG D ON DB20_ORGAO = D.DB01_ORGAO AND
             D.DB01_ANOUSU = ".db_getsession("DB_anousu")."
      WHERE db20_tipoperm ='M' and DB20_ANOUSU = " .db_getsession("DB_anousu") . " and db01_coddepto = " .
            db_getsession("DB_coddepto");
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Erro ao buscar orgao");
        }

        $resultado = db_utils::fieldsMemory($rs, 0);
        return $resultado;
    }

    public function getDesdobramentoMaterial($codigoMaterial)
    {
        return Elemento::select(
            'o56_codele',
            'o56_elemento',
            'o56_descr'
        )->join('pcmaterele', function ($join) {
                $join->on('pc07_codele', '=', 'o56_codele')
                ->where('o56_anousu', '=', db_getsession('DB_anousu'));
        })->join('pcmater', function ($join) {
            $join->on('pc01_codmater', '=', 'pc07_codmater')
            ->where('pc01_ativo', '=', 'false')
            ->where('pc01_conversao', '=', 'false');
        })->join('pcsubgrupo', 'pc04_codsubgrupo', '=', 'pc01_codsubgrupo')
            ->where('pc07_codmater', '=', $codigoMaterial)
            ->get();
    }

    public function getDesdobramentoItem($codele)
    {
        return Elemento::select('o56_codele', 'o56_elemento', 'o56_descr')
            ->where('o56_codele', '=', $codele)
            ->where('o56_anousu', '=', db_getsession('DB_anousu'))
            ->get();
    }

    public function buscarDadosFiltrosDotacoes($request)
    {
        $dados = new stdClass();
        $clpermusuario_dotacao =  new cl_permusuario_dotacao(
            db_getsession('DB_anousu'),
            db_getsession('DB_id_usuario')
        );


        $dados->programas = $this->getProgramas();
        $dados->departamentos = $this->getDepartamentos();
        $rsOrgaos = db_query($clpermusuario_dotacao->orgaos);
        if (!$rsOrgaos) {
            throw new Exception("Erro ao buscar orgaos");
        }
        $dados->orgaos = db_utils::getCollectionByRecord($rsOrgaos);
        $dados->orgaoSelecionado = $this->getOrgao();
        return $dados;
    }

    private function getDepartamentos()
    {
        $ano = db_getsession("DB_anousu");
        $instit = db_getsession("DB_instit");
        $sql = "select db_depart.coddepto || ' - ' || descrdepto as descricao,db01_unidade
                    from db_depart
                        inner join db_departorg on db_depart.coddepto = db01_coddepto
                where db01_anousu = $ano and instit = $instit
                order by coddepto";
        $rsDepartamentos = db_query($sql);
        if (!$rsDepartamentos) {
            throw new Exception("Departamentos não encontrados");
        }
        $departamentos = db_utils::getCollectionByRecord($rsDepartamentos);
        return $departamentos;
    }

    private function getProgramas()
    {
        $oDaoOrcPrograma = new cl_orcprograma();
        $anousu          = db_getsession("DB_anousu");
        $sSqlProgramas   = $oDaoOrcPrograma->sql_query_file(
            $anousu,
            null,
            "o54_programa,
            o54_programa || ' - ' || o54_descr as descricao",
            null
        );
        $rsProgramas     = db_query($sSqlProgramas);
        if (!$rsProgramas) {
            throw new Exception("Erro ao buscar programas");
        }
        $programas = db_utils::getCollectionByRecord($rsProgramas);
        return $programas;
    }
}
