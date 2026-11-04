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

namespace App\Domain\Educacao\MatriculaOnline\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\CentralMatriculas\Helpers\ConfiguracaoCentralHelper;
use App\Domain\Educacao\MatriculaOnline\Enums\TiposDocumentosEnum;
use App\Domain\Educacao\MatriculaOnline\Models\Documento;
use App\Domain\Educacao\MatriculaOnline\Resources\DocumentoResource;
use App\Domain\Educacao\MatriculaOnline\Services\DocumentosService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DocumentosController extends Controller
{
    protected $helper;
    public function __construct(ConfiguracaoCentralHelper $helper)
    {
        $this->helper = $helper;
    }

    public function salvar(Request $request)
    {
        $dados = $request->all();
        $id = array_remove($dados, 'tipo');
        $documento = $this->helper->documentos()->update($dados, $id);
        return new DBJsonResponse($documento);
    }

    public function index()
    {
        $tipos = collect(TiposDocumentosEnum::toArrayWithNames())->map(function ($tipo) {
            $tp = (object) $tipo;
            $tp->url = env('CENTRAL_MATRICULAS_URL');
            switch ($tp->value) {
                case TiposDocumentosEnum::LEGISLACAO:
                    $tp->url .= '/documentos/legislacao.pdf';
                    break;
                case TiposDocumentosEnum::TERMOS_USO:
                    $tp->url .= '/documentos/termos_uso.pdf';
                    break;
                case TiposDocumentosEnum::QUADRO_VAGAS:
                    $tp->url .= '/documentos/quadro_vagas.pdf';
                    break;
                case TiposDocumentosEnum::MANUAL:
                    $tp->url .= '/documentos/manual.pdf';
                    break;
                case TiposDocumentosEnum::LEVANTAMENTO_DADOS:
                    $tp->url .= '/documentos/levantamento_dados.pdf';
                    break;
            }

            return $tp;
        });

        return new DBJsonResponse($tipos);
    }
}
