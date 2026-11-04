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

class TriagemLaboratorialRequest
{
    public static function validaRequestBuscar($dadosRequest)
    {
        $rules = [
            'codRequisicao' => ['required','integer']
        ];
        validaRequest($dadosRequest, $rules);
    }

    public static function validaRequestProcessar($dadosRequest)
    {
        $rules = [
            'requestTriagens.*.codigoMaterial' => ['required','integer'],
            'requestTriagens.*.codigoRequisicao' => ['required','integer'],
            'requestTriagens.*.itensRequisicao' => ['required','array'],
            'requestTriagens.*.itensRequisicao.*' => ['required', 'integer'],
            'requestTriagens.*.recebida' => ['required','boolean'],
            'requestTriagens.*.motivoRejeicao' => ['required','integer'],
            'requestTriagens.*.outroMotivoRejeicao' => ['required','string'],
            'requestTriagens.*.idUsuario' => ['required','integer'],
        ];
        validaRequest($dadosRequest, $rules);
    }

    public static function validaRequestExportarPendencias($dadosRequest)
    {
        $rules = [
            'dataInicial' => ['nullable', function ($attribute, $value, $fail) {
                $date = \DateTime::createFromFormat('d/m/Y', $value);
                if (!$date || $date->format('d/m/Y') !== $value) {
                    $fail('O campo ' . $attribute . ' não possui uma data váida no formato dd/mm/YYYY.');
                }
            }],
            'dataFinal' => ['nullable', function ($attribute, $value, $fail) {
                $date = \DateTime::createFromFormat('d/m/Y', $value);
                if (!$date || $date->format('d/m/Y') !== $value) {
                    $fail('O campo ' . $attribute . ' não possui uma data váida no formato dd/mm/YYYY.');
                }
            }]
        ];
        validaRequest($dadosRequest, $rules);
    }

    public static function validaRequestBuscarRequisicoes($dadosRequest)
    {
        $rules = [
            'departamentoUsuario' => ['required','integer'],
            'codigoRequisicao' => ['nullable','integer'],
            'dataInicial' => ['nullable', function ($attribute, $value, $fail) {
                $date = \DateTime::createFromFormat('d/m/Y', $value);
                if (!$date || $date->format('d/m/Y') !== $value) {
                    $fail('O campo ' . $attribute . ' não possui uma data válida no formato dd/mm/YYYY.');
                }
            }],
            'dataFinal' => ['nullable', function ($attribute, $value, $fail) {
                $date = \DateTime::createFromFormat('d/m/Y', $value);
                if (!$date || $date->format('d/m/Y') !== $value) {
                    $fail('O campo ' . $attribute . ' não possui uma data válida no formato dd/mm/YYYY.');
                }
            }],
            'paciente' => ['nullable','string']
        ];
        validaRequest($dadosRequest, $rules);
    }
}
