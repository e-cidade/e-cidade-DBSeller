<?php

namespace App\Providers;

use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use App\Domain\Tributario\Cadastro\Models\Iptubase;
use App\Domain\Tributario\ISSQN\Model\IssBase;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class CustomValidationProvider extends ServiceProvider
{
    public function boot()
    {
        $this->addRuleCPFCNPJ();
        $this->addRuleAtivos();
        $this->addRuleContribuinte();
    }

    /**
     * Adiciona no core a validação customizada para ativos Folha
     *
     * @return void
     */
    private function addRuleAtivos()
    {
        Validator::extend('ativos', function ($attribute, $value, $parameters) {
            $comissoesAtivas  = $parameters[0];
            $limiteDaComissao = $parameters[1];

            return $comissoesAtivas < $limiteDaComissao;
        });
    }

    /**
     * Adiciona no core a validação customizada para o
     * valor do tipo do contribuinte
     *
     * @return void
     */
    public function addRuleContribuinte()
    {
        Validator::extend('contribuinte', function ($attribute, $value, $parameters, $validator) use (&$msg) {
            $tipo = $validator->getData()['tipo'];

            switch ($tipo) {
                case "M":
                    return (Iptubase::where('j01_matric', $value)->count() > 0);
                    break;
                case "I":
                    return (IssBase::where('q02_inscr', $value)->count() > 0);
                    break;
                case "C":
                    return (Cgm::where('j01_matric', $value)->count() > 0);
                    break;
                default:
                    return false;
            }
        });
    }

    /**
     * Adiciona no core a validação customizada para CPF/CNPJ
     *
     * @return void
     */
    private function addRuleCPFCNPJ()
    {
        /**
        * Validação customizada para CPF/CNPJ
        *
        * @param mixed $attribute
        * @param mixed $value
        * @param mixed $parameters
        *
        * @return bool
        *
        * @var \Closure ruleCPFCNPJ
        */
        $ruleCPFCNPJ = function ($attribute, $value, $parameters) {
            $value = preg_replace('/\D/', '', $value);

            if (strlen($value) === 11 or strlen($value) === 14) {
                if (preg_match('/(\d)\1{10}/', $value)) {
                    return false;
                }

                if (strlen($value) === 11) {
                    for ($t = 9; $t < 11; $t++) {
                        for ($d = 0, $c = 0; $c < $t; $c++) {
                            $d += $value[$c] * (($t + 1) - $c);
                        }
                        
                        $d = ((10 * $d) % 11) % 10;

                        if ($value[$c] != $d) {
                            return false;
                        }
                    }

                    return true;
                } elseif (strlen($value) === 14) {
                    for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
                        $soma += $value[$i] * $j;
                        $j = ($j == 2) ? 9 : $j - 1;
                    }

                    $resto = $soma % 11;

                    if ($value[12] != ($resto < 2 ? 0 : 11 - $resto)) {
                        return false;
                    }

                    for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
                        $soma += $value[$i] * $j;
                        $j = ($j == 2) ? 9 : $j - 1;
                    }

                    $resto = $soma % 11;

                    if ($value[13] == ($resto < 2 ? 0 : 11 - $resto)) {
                        return true;
                    }
                }
            }
   
            return false;
        };

        Validator::extend('cpf_cnpj', $ruleCPFCNPJ, 'O campo CPF/CNPJ é invalido');
    }
}
