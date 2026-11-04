<?php

namespace App\Domain\Saude\Ambulatorial\Services;

use App\Domain\Saude\Ambulatorial\Builders\CgsApiBuilder;
use App\Domain\Saude\Ambulatorial\Models\CgsUnidade;
use App\Domain\Saude\Ambulatorial\Requests\FindOrCreateCgsRequest;
use ECidade\Saude\Ambulatorial\Service\CgsAuditoriaService;
use Exception;

class FindOrCreateCgsService
{
    /**
     * @var CgsApiBuilder
     */
    private $builder;

    /**
     * @var \Cgs
     */
    private $model;

    public function __construct(CgsApiBuilder $builder, \Cgs $model)
    {
        $this->builder = $builder;
        $this->model = $model;
    }

    /**
     * @param FindOrCreateCgsRequest $request
     * @return integer
     * @throws Exception
     */
    public function execute(FindOrCreateCgsRequest $request)
    {
        if ($request->tipo == 1 && $cgs = $this->find($request)) {
            return $cgs;
        }

        return $this->create($request);
    }

    /**
     * @param FindOrCreateCgsRequest $request
     * @return integer
     * @throws Exception
     */
    public function find(FindOrCreateCgsRequest $request)
    {
        $cgs = $this->findByCpfCns($request->cpf, $request->cns);
        if ($cgs === null) {
            $cgs = $this->findUnique($request->nome, $request->nome_mae, $request->data_nascimento);
        }

        if ($cgs === null) {
            return null;
        }

        return $cgs->z01_i_cgsund;
    }

    /**
     * @param string $cpf
     * @param string $cns
     * @return CgsUnidade|null
     * @throws Exception
     */
    private function findByCpfCns($cpf, $cns)
    {
        if ($cpf === null && $cns === null) {
            throw new Exception('É obrigatório informar cpf ou cns quando tipo é 1.', 406);
        }

        $cgs = null;
        if ($cpf) {
            $cgs = CgsUnidade::cpf($cpf)->orderBy('z01_i_cgsund')->first();
        }
        if ($cgs === null && $cns) {
            $cgs = CgsUnidade::cns($cns)->orderBy('z01_i_cgsund')->first();
        }

        return $cgs;
    }

    /**
     * @param string $nome
     * @param string $nomeMae
     * @param string $dataNascimento
     * @return CgsUnidade|null
     */
    private function findUnique($nome, $nomeMae, $dataNascimento)
    {
        return CgsUnidade::nome($nome)
            ->nomeMae($nomeMae)
            ->dataNascimento($dataNascimento)
            ->orderBy('z01_i_cgsund')
            ->first();
    }

    /**
     * @param FindOrCreateCgsRequest $request
     * @return integer
     * @throws Exception
     */
    private function create(FindOrCreateCgsRequest $request)
    {
        $dados = $this->builder->build($request);
        $cgs = $this->model->salvar($dados);

        $_SESSION['DB_login'] = $this->getUserParameter();

        $auditoria = new CgsAuditoriaService($cgs->z01_i_cgsund, null);
        $auditoria->salvar();

        return (int)$cgs->z01_i_cgsund;
    }

    private function getUserParameter()
    {
        $dao = new \cl_sau_config();
        $sql = $dao->sql_query('', 'login');
        $rs = $dao->sql_record($sql);
        if ($dao->numrows > 0) {
            return \db_utils::fieldsMemory($rs, 0)->login;
        }

        return 'dbseller';
    }
}
