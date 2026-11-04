<?php

namespace App\Domain\Configuracao\Instituicao\Controller;

use App\Domain\Configuracao\Departamento\Models\Departamento;
use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Configuracao\Instituicao\Model\DBConfig as Instituicao;
use App\Domain\Configuracao\Instituicao\Repository\InstituicaoRepository;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Http\Controllers\Controller;
use ECidade\Configuracao\Instituicao\Repository\InstituicaoRepository as RepositoryInstituicao;
use Illuminate\Http\Request;

class InstituicaoController extends Controller
{

    protected $repository;

    /**
     * Construtor da classe
     *
     * @return void
     */
    public function __construct(InstituicaoRepository $instituicaoRepository)
    {
        $this->repository = $instituicaoRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return DBJsonResponse
     */
    public function index()
    {
        return new DBJsonResponse($this->repository->findAll());
    }

    /**
     * Retorna uma instituição identificada pelo id passado.
     *
     * @return DBJsonResponse
     */
    public function show($id)
    {
        return new DBJsonResponse($this->repository->find($id));
    }

    /**
     * Salva o Departamento Principal da Instituição na tabela db_config
     * @param Request $request
     */
    public function configurarDepartamentoPrincipal(Request $request)
    {
        $repository = new RepositoryInstituicao();
        $instituicao = $repository->find($request->get('DB_instit'));
        $instituicao->setDescricaoDepartamentoAbreviado($request->get('descricaoAbreviada'));
        $instituicao->setCodigoDepartamentoPrincipal($request->get('departamento'));
        $repository->salvar($instituicao);

        return new DBJsonResponse([], 'Departamento salvo com sucesso!');
    }

    public function search(Request $request)
    {
        $perpage = 10;
        $instituicao = new Instituicao();
        if (!empty($request->get("perpage")) and is_numeric($request->get("perpage"))) {
            $perpage = $request->get("perpage");
        }

        if (!empty($request->get("nome"))) {
            $instituicao = $instituicao->likeNome($request->get("nome"));
        }

        if (!empty($request->get("instituicao"))) {
            $instituicao = $instituicao->inCodigoinstituicao(explode(",", $request->get("codigo")));
        }

        if (!empty($request->get("cnpj"))) {
            $instituicao = $instituicao->likeCnpj($request->get("cnpj"));
        }

        return new DBJsonResponse($instituicao->orderBy('codigo')->paginate($perpage));
    }

    public function searchDepartamentos(Instituicao $instituicao, Request $request)
    {
        $perpage = 10;

        if (!empty($request->get("perpage")) and is_numeric($request->get("perpage"))) {
            $perpage = $request->get("perpage");
        }

        if (empty($instituicao->codigo)) {
            throw new \Exception("Instituição não existe!");
        }

        if (!$instituicao->departamentos()->exists()) {
            throw new \Exception("Instituição não possui departamentos!");
        }

        $departamentos = $instituicao->departamentos();

        if (!empty($request->get("nome"))) {
            $departamentos = $instituicao->departamentos()->likeDescricao($request->get("nome"));
        }

        $dataLimite = date('Y-m-d');

        if (!empty(session("DB_datausu"))) {
            $dataLimite = date('Y-m-d', session("DB_datausu"));
        }

        $departamentos = $departamentos->dataLimite($dataLimite);

        return new DBJsonResponse($departamentos->orderBy('coddepto')->paginate($perpage));
    }

    public function searchDepartamentoUsuarios(
        Instituicao  $instituicao,
        Departamento $departamento,
        Request      $request
    ) {

        $perpage = 10;

        if (!empty($request->get("perpage")) and is_numeric($request->get("perpage"))) {
            $perpage = $request->get("perpage");
        }

        if (empty($instituicao->codigo)) {
            throw new \Exception("Instituição não existe!");
        }

        if (empty($departamento->coddepto)) {
            throw new \Exception("Departamento não existe!");
        }

        if ($instituicao->codigo != $departamento->instit) {
            throw new \Exception("Departamento não pertence a instituição!");
        }

        if (!$departamento->usuarios()->isAtivo()->exists()) {
            throw new \Exception("Departamento não possui usuários");
        }

        $usuarios = $departamento->usuarios()->isAtivo();

        if (!empty($request->get("nome"))) {
            $usuarios = $usuarios->likeNome($request->get("nome"));
        }

        if (!empty($request->get("email"))) {
            $usuarios = $usuarios->likeEmail($request->get("email"));
        }

        if (!empty($request->get("login"))) {
            $usuarios = $usuarios->likeLogin($request->get("login"));
        }

        return new DBJsonResponse($usuarios->orderBy('nome')->paginate($perpage));
    }

    /**
     * Retorna as instituições em que o usuário está logado
     * @param Request $request
     * @return DBJsonResponse
     */
    public function usuarioLogado(Request $request)
    {
        $instituicoes = $request->user()->instituicoes->map(function (DBConfig $config) {
            $config->instituicaoLogada = $config->codigo === (int) session('DB_instit');
            return $config;
        });

        return new DBJsonResponse($instituicoes, 'Instituições do usuário.');
    }
}
