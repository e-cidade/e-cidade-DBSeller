<?php

namespace App\Domain\Financeiro\Orcamento\Services;

use App\Domain\Financeiro\Orcamento\Models\Complemento;
use App\Domain\Financeiro\Orcamento\Models\FonteRecurso as FonteRecursoModel;
use App\Domain\Financeiro\Orcamento\Models\FontesSiconfi;
use App\Domain\Financeiro\Orcamento\Models\Parametro;
use App\Domain\Financeiro\Orcamento\Models\Recurso;
use App\Domain\Financeiro\Orcamento\Models\ValorEstrutural;
use App\Domain\Financeiro\Orcamento\Repositories\RecursoRepository;
use App\Domain\Financeiro\Orcamento\Repositories\ValorEstruturalRepository;
use ECidade\Financeiro\Orcamento\Model\FonteRecurso;
use ECidade\Financeiro\Orcamento\Repository\FonteRecursoSiconfiRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class RecursoService
{
    /**
     * @var ValorEstruturalRepository
     */
    protected $valorEstruturalRepository;
    /**
     * @var RecursoRepository
     */
    protected $repository;

    public function __construct(RecursoRepository $repository, ValorEstruturalRepository $valorEstruturalRepository)
    {
        $this->repository = $repository;
        $this->valorEstruturalRepository = $valorEstruturalRepository;
    }

    /**
     * @param $dados
     * @return void
     * @throws Exception
     */
    public function salvar($dados)
    {
        $exercicio = $dados['DB_anousu'];
        $dados['siconfi'] = "{$dados['codificacao']}{$dados['recursoSiconfi']}";
        $valorEstrutural = $this->getValorEstrutural($exercicio, $dados['recursoSiconfi'], $dados['descricao']);

        $recurso = $this->buildModel($dados);
        $recurso->setDbEstruturaValor($valorEstrutural);
        $this->validarRecurso($recurso, $dados['siconfi'], $exercicio);

        $recurso = $this->repository->persist($recurso);

        $this->salvarFonteRecurso(
            $recurso,
            $dados['recursoSiconfi'],
            $dados['recursoGestao'],
            $exercicio,
            $dados['descricao'],
            $dados['codificacao']
        );
    }

    /**
     * Salva a fonte de recurso no exercício
     * @param Recurso $recurso
     * @param string $recursoSiconfi
     * @param string $recursoGestao
     * @param integer $exercicio
     * @param string $descricao
     * @param null|integer $codificacao
     * @return void
     * @throws Exception
     */
    protected function salvarFonteRecurso(
        Recurso $recurso,
        $recursoSiconfi,
        $recursoGestao,
        $exercicio,
        $descricao,
        $codificacao = null
    ) {
        $repository = new FonteRecursoSiconfiRepository();
        $fonteRecurso = $repository->scopeExercicio($exercicio)
            ->scopeCodigoRecurso($recurso->o15_codigo)
            ->first();
        if (empty($fonteRecurso)) {
            $fonteRecurso = new FonteRecurso();
        }

        $fonte = FontesSiconfi::find($recursoSiconfi);
        $classificacao = 0;
        if ($exercicio > 2021 && is_null($fonte)) {
            throw new Exception("Não foi encontrado a fonte de siconfi.");
        } elseif ($exercicio > 2021) {
            $classificacao = $fonte->classificacao->id;
        }

        if (!is_null($codificacao)) {
            $recursoSiconfi = sprintf('%s%s', $codificacao, $recursoSiconfi);
        }

        $fonteRecurso->setClassificacaofrId($classificacao);
        $fonteRecurso->setOrctiporecId($recurso->o15_codigo);
        $fonteRecurso->setExercicio($exercicio);
        $fonteRecurso->setCodigoSiconfi($recursoSiconfi);
        $fonteRecurso->setGestao($recursoGestao);
        $fonteRecurso->setDescricao($descricao);
        $fonteRecurso->setTipoDetalhamento(str_pad($recurso->o15_loatipo, 2, '0', STR_PAD_LEFT));
        $repository->salvar($fonteRecurso);

        $this->replicaAberturaExercicio($repository, $fonteRecurso);
    }

    /**
     * @param $dados
     * @return Recurso
     * @throws Exception
     */
    protected function buildModel($dados)
    {
        if (!empty($dados['codigo'])) {
            $recurso = Recurso::query()->recursoJaFoiUsado($dados['codigo'])->first();

            if (!is_null($recurso)) {
                $fonte = $recurso->fonteRecurso($dados['DB_anousu']);

                if ($fonte->codigo_siconfi != $dados['siconfi'] ||
                    $fonte->gestao != $dados['recursoGestao'] ||
                    $recurso->o15_complemento != $dados['complemento'] ||
                    $recurso->o15_recurso != $dados['subRecurso']) {
                    $msg = 'Esse recurso já foi utilizado. Você não pode alterá-lo. ';
                    $msg .= 'Caso esse recurso já não atenda a sua necessidade, você deve inativá-lo e criar um novo.';
                    throw new Exception($msg);
                }
            }

            $recurso = Recurso::find($dados['codigo']);
        } else {
            $recurso = new Recurso();
        }

        $recurso->o15_descr = substr($dados['descricao'], 0, 100);
        $recurso->o15_codigo = $dados['codigo'];
        $recurso->o15_codtri = $dados['recursoGestao'];
        $recurso->o15_tipo = $dados['tipoRecurso'];
        $recurso->o15_codigosiconfi = $dados['siconfi'];
        $recurso->o15_loaidentificadoruso = null;
        $recurso->o15_loatipo = null;
        $recurso->o15_loagrupo = null;
        $recurso->o15_loaespecificacao = null;
        $recurso->o15_recurso = $dados['subRecurso'];
        $data = !empty($dados['dataLimite']) ? $dados['dataLimite'] : null;
        $recurso->o15_datalimite = $data;
        $recurso->setComplemento(Complemento::find($dados['complemento']));
        $recurso->o15_descr = substr($dados['descricao'], 0, 100);
        $recurso->o15_finali = $dados['finalidade'];

        return $recurso;
    }

    protected function getValorEstrutural($exercicio, $recurso, $descricao)
    {
        $codigoEstrutura = $this->getCodigoEstrutura($exercicio);
        $valorEstrutural = ValorEstrutural::where('db121_db_estrutura', $codigoEstrutura)
            ->where('db121_estrutural', $recurso)
            ->first();

        if (is_null($valorEstrutural)) {
            $valorEstrutural = $this->criarValorEstrutural($codigoEstrutura, $recurso, $descricao);
        }

        return $valorEstrutural;
    }

    protected function getCodigoEstrutura($exercicio)
    {
        $paramentro = Parametro::query()->where('o50_anousu', $exercicio)->first();
        return $paramentro->o50_estruturarecurso;
    }


    /**
     * Para manter compatibilidade com o cadastro de recurso, é incluso o registro na tabela db_estruturavalor
     * @param $codigoEstrutural
     * @param $recurso
     * @param $descricao
     * @return ValorEstrutural
     * @throws Exception
     */
    protected function criarValorEstrutural($codigoEstrutural, $recurso, $descricao)
    {
        $valorEstrutural = new ValorEstrutural();
        $valorEstrutural->db121_sequencial = null;
        $valorEstrutural->db121_db_estrutura = $codigoEstrutural;
        $valorEstrutural->db121_estrutural = $recurso;
        $valorEstrutural->db121_descricao = $descricao;
        $valorEstrutural->db121_estruturavalorpai = null;
        $valorEstrutural->db121_nivel = 0;
        $valorEstrutural->db121_tipoconta = 0;

        return $this->valorEstruturalRepository->salvar($valorEstrutural);
    }

    /**
     * @param Recurso $newRecurso
     * @return bool
     */
    protected function recursoUtilizado(Recurso $newRecurso)
    {
        if (empty($newRecurso->getCodigo())) {
            return false;
        }
        $sql = <<<SQL
        select 1
          from orctiporec
        where o15_codigo = ?
          and (
               exists(select 1 from caiparametro where caiparametro.k29_orctiporecfundeb = orctiporec.o15_codigo) or
               exists(select 1 from classificacaocredoresrecurso
                where classificacaocredoresrecurso.cc33_orctiporec = orctiporec.o15_codigo) or
               exists(select 1 from conlancamrecurso where conlancamrecurso.c130_orctiporec = orctiporec.o15_codigo) or
               exists(select 1 from conplanoexe where conplanoexe.c62_codrec = orctiporec.o15_codigo) or
               exists(select 1 from conplanoexerecurso where conplanoexerecurso.c89_recurso = orctiporec.o15_codigo) or
               exists(select 1 from conplanoorcamentoanalitica
                where conplanoorcamentoanalitica.c61_codigo = orctiporec.o15_codigo) or
               exists(select 1 from conplanoreduz where conplanoreduz.c61_codigo = orctiporec.o15_codigo) or
               exists(select 1 from contacorrentedetalhe
                where contacorrentedetalhe.c19_orctiporec = orctiporec.o15_codigo) or
               exists(select 1 from empautidot where empautidot.e56_orctiporec = orctiporec.o15_codigo) or
               exists(select 1 from empresto where empresto.e91_recurso = orctiporec.o15_codigo) or
               exists(select 1 from orcdotacao where orcdotacao.o58_codigo = orctiporec.o15_codigo) or
               exists(select 1 from orcdotacaocontr where orcdotacaocontr.o61_codigo = orctiporec.o15_codigo) or
               exists(select 1 from orcimpactomovtiporec
                where orcimpactomovtiporec.o67_codigo = orctiporec.o15_codigo) or
               exists(select 1 from orcimpactorecmov where orcimpactorecmov.o69_codigo = orctiporec.o15_codigo) or
               exists(select 1 from orcimpactotiporec where orcimpactotiporec.o93_codigo = orctiporec.o15_codigo) or
               exists(select 1 from orcparamrecurso where orcparamrecurso.o44_codrec = orctiporec.o15_codigo) or
               exists(select 1 from orcparamrecursoval where orcparamrecursoval.o48_codrec = orctiporec.o15_codigo) or
               exists(select 1 from orcppatiporec where orcppatiporec.o26_codigo = orctiporec.o15_codigo) or
               exists(select 1 from orcprevdesp where orcprevdesp.o35_codigo = orctiporec.o15_codigo) or
               exists(select 1 from orcreceita where orcreceita.o70_codigo = orctiporec.o15_codigo) or
               exists(select 1 from orcreserprev where orcreserprev.o33_codigo = orctiporec.o15_codigo) or
               exists(select 1 from orctiporecconvenio
                where orctiporecconvenio.o16_orctiporec = orctiporec.o15_codigo) or
               exists(select 1 from origemcomplementorecurso
                where origemcomplementorecurso.o206_recurso = orctiporec.o15_codigo) or
               exists(select 1 from placaixarec where placaixarec.k81_codigo = orctiporec.o15_codigo) or
               exists(select 1 from rhcontasrec where rhcontasrec.rh41_codigo = orctiporec.o15_codigo) or
               exists(select 1 from rhdevolucaofolha where rhdevolucaofolha.rh69_recurso = orctiporec.o15_codigo) or
               exists(select 1 from rhempenhofolha where rhempenhofolha.rh72_recurso = orctiporec.o15_codigo) or
               exists(select 1 from rhempenhofolhaexcecaorubrica
                where rhempenhofolhaexcecaorubrica.rh74_recurso = orctiporec.o15_codigo) or
               exists(select 1 from rhempfolha where rhempfolha.rh40_recurso = orctiporec.o15_codigo) or
               exists(select 1 from rhlotavinc where rhlotavinc.rh25_recurso = orctiporec.o15_codigo) or
               exists(select 1 from rhlotavincrec where rhlotavincrec.rh43_recurso = orctiporec.o15_codigo) or
               exists(select 1 from rhslipfolha where rhslipfolha.rh79_recurso = orctiporec.o15_codigo) or
               exists(select 1 from sliprecurso where sliprecurso.k29_recurso = orctiporec.o15_codigo) or
               exists(select 1 from sliprecursocontas
                where sliprecursocontas.k181_recursocredito = orctiporec.o15_codigo) or
               exists(select 1 from sliprecursocontas
                where sliprecursocontas.k181_recursodebito = orctiporec.o15_codigo) or
               exists(select 1 from tabplansaldorecurso
                where tabplansaldorecurso.k111_recurso = orctiporec.o15_codigo) or
               exists(select 1 from tabplansaldorecursomov
                where tabplansaldorecursomov.k113_recurso = orctiporec.o15_codigo)
        );
SQL;

        return count(DB::select($sql, [$newRecurso->getCodigo()])) > 0;
    }

    /**
     * @param Recurso $recurso
     * @param $recursoSiconfi
     * @param $exercicio
     * @return bool
     * @throws Exception
     */
    protected function validarRecurso(Recurso $recurso, $recursoSiconfi, $exercicio)
    {
        $first = FonteRecursoModel::where('codigo_siconfi', $recursoSiconfi)
            ->where('exercicio', $exercicio)
            ->recursoExiste($recurso)
            ->first();

        if (!empty($first)) {
            throw new Exception(sprintf(
                'Já existe o recurso "%s" cadastrado no sistema com o sub recurso "%s" e complemento "%s".',
                $recursoSiconfi,
                $first->recurso->getRecurso(),
                $first->recurso->getComplemento()->o200_sequencial
            ), 406);
        }
        return true;
    }

    /**
     * @param Recurso $recurso
     * @return void
     * @throws Exception
     */
    protected function recursoJaCadastrado(Recurso $recurso)
    {
        /**
         * @var Recurso $first
         */
        $first = $recurso->recursoExiste($recurso)->first();
        if (!empty($first)) {
            throw new Exception(sprintf(
                "Já existe um recurso cadastrado no sistema com o sub recurso %s e complemento %s.",
                $first->getRecurso(),
                $first->getComplemento()->o200_sequencial
            ), 406);
        }
    }

    public function buscar($idOrctiporec, $exercicio)
    {
        $recurso = Recurso::find($idOrctiporec);
        $fonteRecurso = $recurso->fonteRecurso($exercicio);
        $codigoSiconfi = substr($fonteRecurso->codigo_siconfi, 1);
        $recurso->invalido = is_null(FontesSiconfi::find($codigoSiconfi));
        $recurso->fonte_recurso = $fonteRecurso;
        $recurso->codigo_siconfi = $codigoSiconfi;
        return $recurso;
    }

    public function inativar(array $dados)
    {
        $dataLimite = $dados['dataLimite'];

        FonteRecursoModel::find($dados['codigos'])
            ->each(function (FonteRecursoModel $fonteRecurso) use ($dataLimite) {
                $recurso = $fonteRecurso->recurso;
                $recurso->o15_datalimite = empty($dataLimite) ? null : $dataLimite;
                $this->repository->persist($recurso);
            });
    }

    public function excluir(array $dados)
    {
        $recursosUtilizados = [];
        $fontes = FonteRecursoModel::find($dados['codigos']);
        $recusosExcluir = $fontes->filter(function (FonteRecursoModel $fonteRecurso) use (&$recursosUtilizados) {
            if (!$this->recursoUtilizado($fonteRecurso->recurso)) {
                return true;
            }
            $recursosUtilizados[] = $fonteRecurso;
        });

        if ($recusosExcluir->isEmpty()) {
            throw new Exception("Exclusão abortada! Todos recursos selecionados já foram utilizados.");
        }

        $recusosExcluir->each(function (FonteRecursoModel $fonteRecurso) {
            $recurso = $fonteRecurso->recurso;
            if ($fonteRecurso->recurso->fontesRecursos->count() === 1) {
                $recurso->delete();
            } else {
                $fonteRecurso->delete();
            }
        });

        return $recursosUtilizados;
    }

    /**
     * @param FonteRecursoSiconfiRepository $repository
     * @param FonteRecurso $fonteRecurso
     * @return true
     * @throws Exception
     */
    private function replicaAberturaExercicio(FonteRecursoSiconfiRepository $repository, FonteRecurso $fonteRecurso)
    {
        $abertura = DB::table('conaberturaexe')
            ->select(DB::raw('max(c91_anousudestino) as exercicio'))
            ->where('c91_anousuorigem', $fonteRecurso->getExercicio())
            ->first();
        if (is_null($abertura->exercicio)) {
            return true;
        }

        $exercicios = range($fonteRecurso->getExercicio() + 1, $abertura->exercicio);
        foreach ($exercicios as $exercicio) {
            $fonte = $repository->scopeExercicio($exercicio)
                ->scopeCodigoRecurso($fonteRecurso->getOrctiporecId())
                ->first();
            if (is_null($fonte)) {
                $fonteRecurso->setId(null);
                $fonteRecurso->setExercicio($exercicio);
                $fonte = $fonteRecurso;
            } else {
                $fonte->setClassificacaofrId($fonteRecurso->getClassificacaofrId());
                $fonte->setCodigoSiconfi($fonteRecurso->getCodigoSiconfi());
                $fonte->setGestao($fonteRecurso->getGestao());
                $fonte->setDescricao($fonteRecurso->getDescricao());
                $fonte->setTipoDetalhamento($fonteRecurso->getTipoDetalhamento());
            }

            $repository->salvar($fonte);
        }

        return  true;
    }
}
