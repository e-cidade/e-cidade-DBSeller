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

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Domain\Educacao\CentralMatriculas\Models\CBOOcupacao;
use Exception;

class OcupacoesController extends Controller
{
    public function listar(Request $request)
    {
        validaRequest($request->all(), [
            'codigo' => 'nullable|string',
            'titulo' => 'nullable|string',
        ]);

        $lista = CBOOcupacao::when($request->codigo && !$request->titulo, function ($query) use ($request) {
            return $query->where('mo304_codigo', $request->codigo);
        })->when($request->titulo && !$request->codigo, function ($query) use ($request) {
            return $query->whereRaw('LOWER(mo304_titulo) LIKE ?', ['%' . strtolower($request->titulo) . '%']);
        })->get();

        return new DBJsonResponse($lista->toArray());
    }

    public function buscar($codigo = '')
    {
        $item = CBOOcupacao::where('mo304_codigo', $codigo)->first();
        return new DBJsonResponse($item);
    }

    public function incluir(Request $request)
    {
        CBOOcupacao::validate($request);

        $ocupacao = new CBOOcupacao([
            'mo304_titulo' => $request->titulo
        ]);
        $ocupacao->save();

        return new DBJsonResponse([]);
    }

    public function editar(
        Request $request,
        CBOOcupacao $item = null
    ) {
        $item->validate($request);
        $item->fill([
            'mo304_titulo' => $request->titulo
        ]);

        $item->update();
        return new DBJsonResponse([]);
    }

    public function deletar($codigo)
    {
        try {
            CBOOcupacao::where('mo304_codigo', $codigo)->delete();
        } catch (Exception $e) {
            return new DBJsonResponse([], 'Erro ao deletar profissão.
                                        Esta profissão está vinculada com o cadastro de um aluno.', 412);
        }

        return new DBJsonResponse();
    }

    public function salvaOcupacao(
        $aluno = '',
        $column = '',
        $value = ''
    ) {
        if (!$aluno || !$column || !$value
            || !in_array($column, ['ed47_i_ocupacaopai', 'ed47_i_ocupacaomae', 'ed47_i_ocupacaoresp'])
            || !is_numeric($value)
        ) {
            return new DBJsonResponse([], 'Erro ao salvar profissão.', 412);
        }

        DB::statement("UPDATE escola.aluno SET {$column} = {$value} WHERE ed47_i_codigo = {$aluno};");

        return new DBJsonResponse([]);
    }

    public function buscaOcupacao($aluno = '')
    {
        if (!$aluno || !is_numeric($aluno)) {
            return new DBJsonResponse([], 'Erro ao consultar profissões.', 412);
        }

        $ocupacoes = DB::table('escola.aluno')
        ->where('ed47_i_codigo', $aluno)
        ->leftJoin('escola.cboocupacao as ocupacaopai', 'ocupacaopai.mo304_codigo', '=', 'aluno.ed47_i_ocupacaopai')
        ->leftJoin('escola.cboocupacao as ocupacaomae', 'ocupacaomae.mo304_codigo', '=', 'aluno.ed47_i_ocupacaomae')
        ->leftJoin('escola.cboocupacao as ocupacaoresp', 'ocupacaoresp.mo304_codigo', '=', 'aluno.ed47_i_ocupacaoresp')
            ->select(
                'ocupacaopai.mo304_codigo as ocupacaopai',
                'ocupacaopai.mo304_titulo as ocupacaopai_label',
                'ocupacaomae.mo304_codigo as ocupacaomae',
                'ocupacaomae.mo304_titulo as ocupacaomae_label',
                'ocupacaoresp.mo304_codigo as ocupacaoresp',
                'ocupacaoresp.mo304_titulo as ocupacaoresp_label'
            )->first();

        return new DBJsonResponse($ocupacoes);
    }

    public function limparOcupacao($aluno = '', $column = '')
    {
        if (empty($aluno) || empty($column)) {
            return new DBJsonResponse([], 'Erro ao limpar profissão.', 412);
        }

        DB::statement("UPDATE escola.aluno SET {$column} = null WHERE ed47_i_codigo = {$aluno};");

        return new DBJsonResponse([]);
    }
}
