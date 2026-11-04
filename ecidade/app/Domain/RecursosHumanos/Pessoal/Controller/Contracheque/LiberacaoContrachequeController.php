<?php

namespace App\Domain\RecursosHumanos\Pessoal\Controller\Contracheque;

use App\Domain\RecursosHumanos\Pessoal\Services\Contracheque\LiberacaoContrachequeService;
use App\Http\Controllers\Controller;
use BusinessException;
use Exception;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\Pessoal\Model\Contracheque\LiberacaoContrachequeModel;
use App\Domain\RecursosHumanos\Pessoal\Repository\Helper\CompetenciaHelper;
use App\Domain\RecursosHumanos\Pessoal\Requests\Contracheque\BuscaLiberacaoContrachequeRequest;
use App\Domain\RecursosHumanos\Pessoal\Requests\Contracheque\SalvarLiberacaoContrachequeRequest;

class LiberacaoContrachequeController extends Controller
{
    /**
     * @var LiberacaoContrachequeService
     */
    private $liberacaoContrachequeService;

    public function __construct(LiberacaoContrachequeService $liberacaoContracheque)
    {
        $this->liberacaoContrachequeService = $liberacaoContracheque;
    }

    public function saveConfig(SalvarLiberacaoContrachequeRequest $request)
    {
        foreach ($request->configuracoes as $configuracao) {
            $liberacao = $this->liberacaoContrachequeService->buscaLiberacao($configuracao["codigo"]);
            try {
                $liberacao->rh301_salario = $configuracao["salario"];
                $liberacao->rh301_rescisao = $configuracao["rescisao"];
                $liberacao->rh301_complementar = $configuracao["complementar"];
                $liberacao->rh301_decimo = $configuracao["decimo"];
                $liberacao->rh301_adiantamento = $configuracao["adiantamento"];
                $liberacao->rh301_suplementar = $configuracao["suplementar"];
                $liberacao->save();
            } catch (Exception $e) {
                throw new BusinessException($e->getMessage());
            }
        }
        return new DBJsonResponse([], 'Liberações atualizadas com sucesso.');
    }

    public function getConfig(BuscaLiberacaoContrachequeRequest $request)
    {
        $anoCompetencia = (int) CompetenciaHelper::get()->getAno();
        $mesCompetencia = (int) CompetenciaHelper::get()->getMes();
        // retorna 1 array de liberacoes em ordem decrecente por ano e mes
        $liberacoes = $this->liberacaoContrachequeService->buscaLiberacoes($request->get('DB_instit'));
        if (sizeof($liberacoes) > 0) {
            while ($liberacoes[0]['ano'] != $anoCompetencia || $liberacoes[0]['mes'] != $mesCompetencia) {
                $anoLiberacao = $liberacoes[0]['ano'];
                $mesLiberacao = $liberacoes[0]['mes'] + 1;
                if ($mesLiberacao > 12) {
                    $mesLiberacao = 1;
                    $anoLiberacao += 1;
                }

                $liberacao = new LiberacaoContrachequeModel();
                $liberacao->rh301_instituicao = $request->get('DB_instit');
                $liberacao->rh301_ano = $anoLiberacao;
                $liberacao->rh301_mes = $mesLiberacao;
                $liberacao->rh301_salario = false;
                $liberacao->rh301_rescisao = false;
                $liberacao->rh301_complementar = false;
                $liberacao->rh301_decimo = false;
                $liberacao->rh301_adiantamento = false;
                $liberacao->rh301_suplementar = false;

                $liberacao->save();
                $liberacoes = $this->liberacaoContrachequeService->buscaLiberacoes($request->get('DB_instit'));
            }
        }
        return $liberacoes;
    }
}
