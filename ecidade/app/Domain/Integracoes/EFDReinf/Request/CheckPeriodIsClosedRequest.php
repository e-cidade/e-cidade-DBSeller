<?php

namespace App\Domain\Integracoes\EFDReinf\Request;

use Illuminate\Foundation\Http\FormRequest;

class CheckPeriodIsClosedRequest extends FormRequest
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
            'event' => 'required|string|in:R-2099,R-4099',
            'competence' => 'string',
            'DB_instit' => 'required'
        ];
    }
}
