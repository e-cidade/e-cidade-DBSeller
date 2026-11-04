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

namespace App\Domain\Patrimonial\Licitacoes\Requests;

use App\Http\Requests\DBFormRequest;

/**
 * @property $tipoInstrumento
 * @property $garantia
 * @property $caracteristicas
 * @property $codigoTipoFamilia
 * @property $codigoTipoSubFamilia
 * @property $cep
 * @property $logradouro
 * @property $bairro
 * @property $municipio
 * @property $cnpj
 */
class IncluirObraRequest extends DBFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tipoInstrumento' => 'required|string',
            'codigoTipoFamilia' => 'required|string',
            'codigoTipoSubFamilia' => 'required|string',
            'caracteristicas' => 'required|array',
            'cep' => 'required|string',
            'logradouro' => 'required|string',
            'bairro' => 'required|string',
            'municipio' => 'required|string',
        ];
    }
}
