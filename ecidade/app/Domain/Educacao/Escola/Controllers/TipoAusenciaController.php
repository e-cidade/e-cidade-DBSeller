<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace App\Domain\Educacao\Escola\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Escola\Models\TipoAusencia;
use ECidade\Enum\Educacao\Escola\TipoAusenciaCategoriaEnum as CategoriaEnum;
use ECidade\Enum\Educacao\Escola\TipoAusenciaSubcategoriaEnum as SubcategoriaEnum;

class TipoAusenciaController extends Controller
{
    public function listar(Request $request)
    {
        validaRequest($request->all(), [
            'codigo' => 'nullable|string',
            'descricao' => 'nullable|string',
        ]);

        $lista = TipoAusencia::when($request->codigo && !$request->descricao, function ($query) use ($request) {
            return $query->where('ed320_sequencial', $request->codigo);
        })->when($request->descricao && !$request->codigo, function ($query) use ($request) {
            return $query->where('ed320_descricao', 'ILIKE', '%' . $request->descricao . '%');
        })->orderBy('ed320_descricao', 'ASC')->get();

        return new DBJsonResponse($lista->map(function ($item) {
            if ($item['ed320_categoria']) {
                $sCategoria = (new CategoriaEnum($item->ed320_categoria))->name();
                $sSubcategoria = (new SubcategoriaEnum($item->ed320_subcategoria))->name();
                $item['ed320_categoria'] = $sCategoria . " ($sSubcategoria)";
            }

            return $item;
        })->toArray());
    }

    public function buscar($codigo = '')
    {
        $item = TipoAusencia::where('ed320_sequencial', $codigo)->first();
        return new DBJsonResponse($item);
    }

    public function incluir(Request $request)
    {
        TipoAusencia::validate($request);

        $ocupacao = new TipoAusencia([
            'ed320_descricao' => $request->descricao,
            'ed320_categoria' => $request->categoria,
            'ed320_subcategoria' => $request->subcategoria,
        ]);
        $ocupacao->save();

        return new DBJsonResponse([]);
    }

    public function editar(Request $request, TipoAusencia $item = null)
    {
        $item->validate($request);
        $item->fill([
            'ed320_descricao' => $request->descricao,
            'ed320_categoria' => $request->categoria,
            'ed320_subcategoria' => $request->subcategoria,
        ]);

        $item->update();
        return new DBJsonResponse([]);
    }

    public function deletar($codigo)
    {
        TipoAusencia::where('ed320_sequencial', $codigo)->delete();
        return new DBJsonResponse();
    }
}
