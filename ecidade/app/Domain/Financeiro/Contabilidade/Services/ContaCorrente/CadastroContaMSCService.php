<?php

namespace App\Domain\Financeiro\Contabilidade\Services\ContaCorrente;

use App\Domain\Financeiro\Contabilidade\Models\ConplanoExeContaCorrente;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoExeContaCorrenteAtributos;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoExercicio;
use Exception;

class CadastroContaMSCService
{

    /**
     * @var integer
     */
    private $exercicio;

    public function verificaExistenciaExeContaCorrente(array $conta)
    {
        return ConplanoExeContaCorrente::where('c143_conplanoreduz', $conta["reduzido"])
            ->where('c143_exercicio', $conta["exercicio"])
            ->count() > 0;
    }

    public function configurarSaldoInicialPorRecurso(array $conta)
    {
        if ($this->verificaExistenciaExeContaCorrente($conta)) {
            throw new Exception("Esta conta já possui saldo inicial por recurso no exercicio.", 403);
        }

        $conplanoExe = $this->verificaConplanoExe($conta);
        $classeEstrutural =  substr($conta["estrutural"], 0, 1);

        $novaConta  = new ConplanoExeContaCorrente();

        $novaConta->c143_conplanoreduz = $conta["reduzido"];
        $novaConta->c143_exercicio = $conta["exercicio"];
        $novaConta->c143_conplanosistema = 100;


        if ($conplanoExe->c62_vlrcre == 0 && $conplanoExe->c62_vlrdeb == 0) {
            $novaConta->c143_natureza = $classeEstrutural % 2  ?  'D' : 'C';
            $novaConta->c143_saldo = 0;
        }
        //verifica se o valor do credito é maior que o do debito.
        if ($conplanoExe->c62_vlrcre > 0) {
            $novaConta->c143_natureza = 'C';
            $novaConta->c143_saldo = $conplanoExe->c62_vlrcre;
        }
        //Verifica se o valor do debito é maio que o do credito
        if ($conplanoExe->c62_vlrdeb > 0) {
            $novaConta->c143_natureza = 'D';
            $novaConta->c143_saldo = $conplanoExe->c62_vlrdeb;
        }
        $novaConta->save();

        $contaAtributos = new ConplanoExeContaCorrenteAtributos();
        $contaAtributos->c144_conplanoexecontacorrente = $novaConta->id;
        $contaAtributos->c144_conplanoinfocomplementar = 100;
        $contaAtributos->c144_valor = $conta["recurso_id"];
        $contaAtributos->save();
    }

    public function verificaConplanoExe($conta)
    {
        $response = ConplanoExercicio::where('c62_reduz', $conta["reduzido"])
            ->where('c62_anousu', $conta["exercicio"])
            ->get()->first();

        return $response;
    }
}
