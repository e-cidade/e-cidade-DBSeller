<?php


namespace App\Domain\Financeiro\Contabilidade\Services;

use App\Domain\Financeiro\Orcamento\Models\Complemento;
use App\Domain\Financeiro\Orcamento\Models\FonteRecurso;
use App\Domain\Financeiro\Orcamento\Models\Recurso;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

/**
 * Class ManutencaoFonteRecurso
 * @package App\Domain\Financeiro\Contabilidade\Repositories
 */
abstract class ManutencaoFonteRecursoService
{
    /**
     * @var FormRequest
     */
    protected $request;

    /**
     * @var array
     */
    protected $recursos = [];

    /**
     * @param $fonteGestao
     * @param $subrecurso
     * @param $exercicio
     * @return FonteRecurso|Collection
     */
    protected function getRecurso($fonteGestao, $subrecurso, $exercicio)
    {
        $this->recursos = FonteRecurso::fonteRecurso($fonteGestao, $exercicio, $subrecurso)
            ->orderBy('o15_complemento')
            ->get()
            ->map(function (FonteRecurso $fonte) {
                $fonte->complemento = Complemento::find($fonte->o15_complemento);
                return $fonte;
            });
        return $this->recursos;
    }

    /**
     * @return Recurso[]
     * @throws Exception
     */
    public function buscarRecursos()
    {
        if (!empty($this->recursos)) {
            return $this->recursos;
        }

        $fonteGestao = $this->request->get('gestao');
        $recurso = $this->request->get('subrecurso');
        $ano = $this->request->get('DB_anousu');
        if (!empty($recurso)) {
            return $this->getRecurso($fonteGestao, $recurso, $ano);
        }

        $idEmpenho = $this->request->get('idEmpenho');
        if (!empty($idEmpenho)) {
            $empenho = new \EmpenhoFinanceiro($idEmpenho);

            if ($empenho->isRP($ano)) {
                $recurso = DB::table('empresto')
                    ->select(['gestao', 'o15_recurso'])
                    ->join('orctiporec', 'orctiporec.o15_codigo', '=', 'empresto.e91_recurso')
                    ->join('fonterecurso', function ($join) use ($ano) {
                        $join->on('orctiporec_id', '=', 'o15_codigo')
                            ->where('exercicio', '=', $ano);
                    })
                    ->where('e91_numemp', '=', $idEmpenho)
                    ->where('e91_anousu', '<=', $ano)
                    ->orderBy('e91_anousu', 'desc')
                    ->first();

                return $this->getRecurso($recurso->gestao, $recurso->o15_recurso, $ano);
            }
            $recurso = $empenho->getDotacao()->getDadosRecurso();
            $fonte = $recurso->getFonteRecurso($ano);
            return $this->getRecurso($fonte->gestao, $recurso->getFonteDeRecurso(), $ano);
        }

        $codigoReceita = $this->request->get('codigoReceita');

        if (!empty($codigoReceita)) {
            $recurso = DB::table('conlancamrec')
                ->join('conlancam', 'conlancam.c70_codlan', '=', 'conlancamrec.c74_codlan')
                ->join('orcreceita', function ($join) {
                    $join->on('orcreceita.o70_anousu', '=', 'conlancamrec.c74_anousu')
                        ->on('orcreceita.o70_codrec', '=', 'conlancamrec.c74_codrec');
                })
                ->join('orctiporec', 'orctiporec.o15_codigo', '=', 'orcreceita.o70_codigo')
                ->join('fonterecurso', function ($join) use ($ano) {
                    $join->on('orctiporec_id', '=', 'o15_codigo')
                        ->where('exercicio', '=', $ano);
                })
                ->select(['gestao', 'o15_recurso'])
                ->where('o70_codrec', $codigoReceita)
                ->where('o70_anousu', $ano)
                ->first();

            if (is_null($recurso)) {
                throw new Exception("Não foi encontrado lançamentos para receita selecionada.");
            }
            return $this->getRecurso($recurso->gestao, $recurso->o15_recurso, $ano);
        }
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function buscarComplementos()
    {
        $this->buscarRecursos();

        return $this->recursos->map(function (Recurso $recurso) {
            return $recurso->getComplemento();
        });
    }

    /**
     * @param FormRequest $request
     */
    public function setRequest(FormRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Atualiza a fonte de recursos do
     * @throws Exception
     */
    public function atualizaRecursos()
    {
        foreach ($this->request->get('itens') as $item) {
            $item = str_replace('\"', '"', $item);
            if (strpos($item, '\"')) {
                $item = str_replace('\"', '"', $item);
            }

            $item = \JSON::create()->parse($item);
            $recursos = array_filter($item->recursos, function ($recurso) use ($item) {
                return $item->complemento == $recurso->complemento->codigo;
            });

            if (empty($recursos)) {
                throw new Exception("Erro ao identificar o recurso.", 406);
            }

            $recurso = array_shift($recursos);
            $this->atualizarRecurso($item->codigo, $recurso->o15_codigo);
        }
    }

    /**
     * @param integer $codigo código do empenho ou lançamento depende da fonte
     * @param string $fonteRecuso cógido da fonte de recurso
     * @return mixed
     */
    abstract public function atualizarRecurso($codigo, $fonteRecuso);
}
