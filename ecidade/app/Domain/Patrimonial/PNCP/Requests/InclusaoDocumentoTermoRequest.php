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

namespace App\Domain\Patrimonial\PNCP\Requests;

use App\Http\Requests\DBFormRequest;

/**
 * @property $sequencialTermoContrato
 * @property $tituloDocumento
 * @property $tipoDocumentoId
 * @property $anoContrato
 * @property $documento
 * @property $cnpj
 */
class InclusaoDocumentoTermoRequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'cnpj' => 'string|required',
            'tituloDocumento' => 'string|required',
            'tipoDocumentoId' => 'string|required',
            'anoContrato' => 'string|required',
            'numeroContrato' => 'string|required',
            'sequencialTermoContrato' => 'string|required',
            'documento' => [
                'required',
                'file',
                'max:30000',
                'mimes:pdf,rtf,doc,docx,odt,sxw,zip,7z,rar,dwg,dwt,dxf,dwf,dwfx,
                svg,sldprt,sldasm,dgn,ifc,skp,3ds,dae,obj,rfa,rte,txt'
            ]
        ];
    }

    public function messages()
    {
        return [
            'documento.required' => 'Nenhum documento anexado ao Contrato.',
            'documento.file' => 'O documento deve ser um arquivo suportado.',
            'documento.max' => 'O tamanho máximo aceito, por arquivo enviado, é de 30 MB (Megabytes).',
            'documento.mimes' => 'A extensão do arquivo anexado não é aceita pelo PNCP.'
        ];
    }
}
