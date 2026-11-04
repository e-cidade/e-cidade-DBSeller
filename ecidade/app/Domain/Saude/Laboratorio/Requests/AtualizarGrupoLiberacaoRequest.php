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

namespace App\Domain\Saude\Laboratorio\Requests;

use App\Http\Requests\DBFormRequest;

/**
 * @property integer $grupoId
 * @property integer $setor
 * @property string $nomeGrupo
 */
class AtualizarGrupoLiberacaoRequest extends DBFormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'grupoId' => ['required', 'integer', 'exists:gruposliberacao,la71_sequencial'],
            'setor' => ['integer', 'exists:lab_setor,la23_i_codigo'],
            'nomeGrupo' => ['required', 'string']
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'grupoId.required' => "Obrigatório informar um grupo!",
            'grupoId.integer' => "Valor inválido para o grupo informado!",
            'grupoId.exists' => "O grupo informado não existe!",

            'setor.integer' => "Valor inválido para o setor informado!",
            'setor.exists' => "O setor informado não existe!",

            'nomeGrupo.required' => "Obrigatório informar um nome para o grupo!",
            'nomeGrupo.string' => "Valor inválido para o nome do grupo!",
        ];
    }
}
