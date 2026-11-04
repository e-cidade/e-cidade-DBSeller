<?php

namespace App\Domain\Saude\Ambulatorial\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Saude\Ambulatorial\Models\CartaoSus;
use App\Domain\Saude\Ambulatorial\Models\CgsUnidade;
use App\Domain\Saude\Ambulatorial\Repositories\Contracts\CgsUnidadeRepository as CgsUnidadeRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CgsUnidadeRepository extends BaseRepository implements CgsUnidadeRepositoryInterface
{
    protected $modelClass = CgsUnidade::class;

    /**
     * @param $dados
     * @return bool
     */
    public function atualizarCgsCpf($dados)
    {
        try {
            DB::beginTransaction();

            $cgsUnd = CgsUnidade::find($dados->cgsPaciente);
            $cgsUnd->z01_v_cgccpf = (empty($dados->cpf) ? null : $dados->cpf);
            $cgsUnd->save();

            $cartaoSus = new CartaoSus();

            if (!empty($dados->idCartaoSus)) {
                $cartaoSus = CartaoSus::where('s115_i_codigo', $dados->idCartaoSus)->first();
                $cartaoSus->s115_c_cartaosus = (empty($dados->cns) ? null : $dados->cns);
                $cartaoSus->s115_c_tipo = CartaoSus::TIPO_CARTAO_SUS;
                $cartaoSus->save();
                DB::commit();
                return true;
            }

            $cartaoSus->s115_i_codigo = $cartaoSus->max('s115_i_codigo') + 1;
            $cartaoSus->s115_i_cgs = $dados->cgsPaciente;
            $cartaoSus->s115_c_cartaosus = $dados->cns;
            $cartaoSus->s115_c_tipo = CartaoSus::TIPO_CARTAO_SUS;
            $cartaoSus->s115_i_entrada = CartaoSus::ENTRADA_CARTAO_SUS;
            $cartaoSus->save();

            DB::commit();
            return true;
        } catch (\PDOException $e) {
            DB::rollBack();
            return false;
        }
    }
}
