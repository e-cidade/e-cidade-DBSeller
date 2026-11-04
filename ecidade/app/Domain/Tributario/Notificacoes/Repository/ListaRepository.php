<?php

namespace App\Domain\Tributario\Notificacoes\Repository;

use App\Domain\Tributario\Notificacoes\Model\Lista;
use App\Domain\Tributario\Cadastro\Models\DbSyscampo;
use Illuminate\Support\Facades\DB;

class ListaRepository
{
    public static function getByParams($k60_descr, $k60_codigo, $k60_datadeb, $k60_tipo, $k60_filtros, $porPagina)
    {
        $lista = new Lista();
        
        $where = " 1 = 1 ";

        if (trim($k60_codigo) != "") {
            $where .= " and k60_codigo = {$k60_codigo} ";
        }

        if (trim($k60_descr) != "") {
            $where .= " and k60_descr ilike '{$k60_descr}' ";
        }

        if (trim($k60_filtros) != "") {
            $where .= " and k60_filtros ilike '{$k60_filtros}' ";
        }

        if (trim($k60_tipo) != "") {
            $where .= " and k60_tipo = {$k60_tipo} ";
        }

        if (trim($k60_datadeb) != "") {
            $where .= " and k60_datadeb = '{$k60_datadeb}' ";
        }

        $pesquisa = $lista->whereRaw($where)
            ->select([
                'k60_codigo',
                'k60_descr',
                'k60_filtros',
                'k60_tipo',
                \DB::raw('TO_CHAR(k60_datadeb, \'dd/mm/yyyy\') as k60_datadeb')
            ])->orderBy('k60_codigo', 'ASC')
            ->paginate($porPagina);

        return $pesquisa;
    }

    public static function verificaTiposDebitos($k60_codigo)
    {
        return DB::select('
        select k03_parcelamento,k03_descr
        from lista
            inner join listatipos on k60_codigo = k62_lista inner join arretipo a on k62_tipodeb = a.k00_tipo 
            inner join cadtipo b on a.k03_tipo = b.k03_tipo 
        where  k60_codigo = ?', [$k60_codigo]);
    }

    public static function getRotulosPesquisaLista()
    {
        $data = [];
        $nomeCampos = [
            'k60_codigo',
            'k60_descr',
            'k60_tipo',
            'k60_datadeb',
            'k60_filtros',
            'k60_usuario',
            'k60_instit'
        ];

        $nomeLabels = array_map(function ($nomeCampo) {
            return DbSyscampo::select("rotulo")->where('nomecam', '=', $nomeCampo)->first()->toArray();
        }, $nomeCampos);
        if (count($nomeCampos) == count($nomeLabels)) {
            for ($index = 0; $index < count($nomeCampos); $index++) {
                $data[$nomeCampos[$index]] = ucfirst($nomeLabels[$index]["rotulo"]);
            }
        }

        return $data;
    }
}
