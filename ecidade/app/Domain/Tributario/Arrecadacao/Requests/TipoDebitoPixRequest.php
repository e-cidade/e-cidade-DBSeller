<?php

namespace App\Domain\Tributario\Arrecadacao\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TipoDebitoPixRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @param $k00_tipo Indece da tabela arretipo
     *
     * @return array
     */
    public function rules($codtipopix = null)
    {
        $codbank = request("codbank");

        $rules = [
            "modsistema"   => ["boolean"],
            "moddbpref"    => ["boolean"],
            "dtini"        => ["date"],
            "dtfim"        => ["date"],
            "valorinicial" => ["numeric"],
            "valorfinal"   => ["numeric"],
            "qtdemissao"   => ["required", "numeric"],
            "codbank"      => ["required", "array", "min:1"],
            "codbank.*"    => [
                "required",
                "numeric",
                "exists:db_bancos_pix,db90_codban"
            ],
            "k00_tipo"     => [
                "required",
                "numeric",
                "exists:arretipo,k00_tipo",
                Rule::unique("arretipopix")->ignore($codtipopix, "codtipopix")
            ]
        ];

        if (is_null($codbank)) {
            unset($rules["codbank"], $rules["codbank.*"]);
            array_shift($rules["qtdemissao"]);
        } else {
            if (!is_null($codbank) and
                is_array($codbank) and
                count($codbank) <= 1
            ) {
                array_shift($rules["qtdemissao"]);
            }
        }

        return $rules;
    }
}
