<?php


namespace App\Domain\Tributario\Caixa\Models;

use App\Domain\Tributario\Cadastro\Models\Cfiptu;
use App\Domain\Tributario\Cadastro\Models\Iptucadtaxaexe;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ParametroBaixaIsencao
 *
 * @package App\Domain\Tributario\Arrecadacao\Models
 *
 * @property int k02_codigo
 * @property string|null k02_tipo
 * @property string|null k02_descr
 * @property int|null k02_drecei
 * @property int k02_codjm
 * @property int|null k02_recjur
 * @property int|null k02_recmul
 * @property string|null k02_limite Data formato Y-m-d
 * @property int k02_tabrectipo
 * @property int|null k02_reccredito
 */
class TabRec extends Model
{
    public $timestamps = false;

    protected $table      = 'caixa.tabrec';
    protected $primaryKey = 'k02_codigo';

    public $fillable = [
        'k02_codigo',
        'k02_tipo',
        'k02_descr',
        'k02_drecei',
        'k02_codjm',
        'k02_recjur',
        'k02_recmul',
        'k02_limite',
        'k02_tabrectipo',
        'k02_reccredito'
    ];

    /**
     * Verifica se existe uma receita invalida para o ano do calculo do IPTU
     *
     * @return array<string> Lista de receitas com erro
     */
    public static function verificaReceitasInvalidas($ano)
    {
        $K02Codigos   = [];
        $receitasErro = [];

        Iptucadtaxaexe::where('j08_anousu', $ano)
            ->select('j08_tabrec')
            ->each(function ($IPTUCadtaxaexe) use (&$K02Codigos) {
                array_push($K02Codigos, $IPTUCadtaxaexe->j08_tabrec);
            });

        Cfiptu::where('j18_anousu', $ano)
            ->select('j18_rterri')
            ->each(function ($cfiptu_rt) use (&$K02Codigos) {
                array_push($K02Codigos, $cfiptu_rt->j18_rterri);
            });

        Cfiptu::where('j18_anousu', $ano)
            ->select('j18_rpredi')
            ->each(function ($cfiptu_rp) use (&$K02Codigos) {
                array_push($K02Codigos, $cfiptu_rp->j18_rpredi);
            });

        $whereIn  = array_unique($K02Codigos);
        $receitas = DB::table('caixa.tabrec as tabrec')
            ->whereIn('tabrec.k02_codigo', $whereIn)
            ->leftJoin(
                'caixa.tabrec as juros',
                'juros.k02_recjur',
                '=',
                'tabrec.k02_codigo'
            )
            ->leftJoin(
                'caixa.tabrec as multas',
                'multas.k02_recmul',
                '=',
                'tabrec.k02_codigo'
            )
            ->where(function ($query) use (&$ano) {
                return $query->orWhere('tabrec.k02_limite', '<', ($ano . '-01-01'))
                    ->orWhere('multas.k02_limite', '<', ($ano . '-01-01'))
                    ->orWhere('juros.k02_limite', '<', ($ano . '-01-01'));
            })
            ->select(
                'tabrec.k02_codigo as k02_codigo',
                'tabrec.k02_limite as limite_receita_principal',
                'juros.k02_limite  as limite_receita_juros',
                'multas.k02_limite  as limite_receita_multas'
            )
            ->get();
        
        if ($receitas->count() > 0) {
            $receitas->each(function ($receita) use (&$receitasErro) {
                $msg = (
                    "Verifique o cadastro da Receita {$receita->k02_codigo}, ".
                    "data limite informada para a(s) receita(s): "
                );

                $errorsReceitas = [];

                if (!empty($receita->limite_receita_principal)) {
                    $errorsReceitas[] = "Principal";
                }

                if (!empty($receita->limite_receita_juros)) {
                    $errorsReceitas[] = "Juros";
                }

                if (!empty($receita->limite_receita_multas)) {
                    $errorsReceitas[] = "Multas";
                }

                $msg .= implode(", ", $errorsReceitas);

                array_push($receitasErro, $msg);
            });
        }

        return $receitasErro;
    }

    public function juros()
    {
        return $this->hasMany(self::class, 'k02_codigo', 'k02_recjur');
    }

    public function multas()
    {
        return $this->hasMany(self::class, 'k02_codigo', 'k02_recmul');
    }
}
