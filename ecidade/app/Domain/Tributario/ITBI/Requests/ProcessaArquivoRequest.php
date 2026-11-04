<?php

namespace App\Domain\Tributario\ITBI\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessaArquivoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "itbi" => ["required", "integer"],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,ppt,pptx,txt,csv,zip,odt,ods,odp'],
            "desc" => ["required", "string"],
            "obs" => ["string"]
        ];
    }


    public function messages()
    {
        return [
            "file.file" => "arqInvalido",
            "file.mimes" => "arqInvalido",
        ];
    }
}
