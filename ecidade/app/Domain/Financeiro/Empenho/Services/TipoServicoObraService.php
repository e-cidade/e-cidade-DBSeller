<?php

namespace App\Domain\Financeiro\Empenho\Services;

use App\Domain\Financeiro\Empenho\Models\TipoServicoObra;
use App\Domain\Financeiro\Empenho\Repositories\TipoServicoObraRepository;

class TipoServicoObraService
{
    /**
     * Responsavel por armazenar uma estancia do repositorio
     *
     * @var TipoServicoObraRepository
     */
    protected $repository;

    /**
     * Responsavel por armazear uma estancia da model
     *
     * @var TipoServicoObra
     */
    protected $model;

    /**
     * @param int|null $id e154_sequencial
     */
    public function __construct($id = null)
    {
        $this->repository = new TipoServicoObraRepository;
        $this->model = $this->repository->getModelInstance($id);
    }

    /**
     * Setter numemp
     *
     * @param int $numemp
     * @return void
     */
    public function setNumemp($numemp)
    {
        // verificar se já possui indicativo para o empenho informado
        $tipoServicoObra = $this->repository->findByEmpenho($numemp);

        if ($tipoServicoObra) {
            $this->model = $tipoServicoObra;
        } else {
            $this->model->e154_numemp = $numemp;
        }

        unset($tipoServicoObra);
    }

    /**
     * Setter tipo
     *
     * @param [type] $tipo
     * @return void
     */
    public function setTipo($tipo)
    {
        $label = $this->label($tipo);
        $this->model->e154_tipo  = $tipo;
        $this->model->e154_label = $label;
    }

    /**
     * Setter CNO
     *
     * @param string $cno Codigo Nacional de Obras
     * @return void
     */
    public function setCNO($cno)
    {
        $cno = preg_replace('/\D/', '', $cno);
        $this->model->e154_cno = $cno;
    }

    /**
     * Getter numemp
     *
     * @return string
     */
    public function getNumemp()
    {
        return $this->model->e154_numemp;
    }

    /**
     * Getter tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->model->e154_tipo;
    }

    /**
     * Getter label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->model->e154_label;
    }

    /**
     * Getter CNO - Codigo Nacional de Obras
     *
     * @return null|string
     */
    public function getCNO()
    {
        return $this->model->e154_cno;
    }

    /**
     * Persistir os dados da $model no banco de dados
     *
     * @return boolean
     */
    public function save()
    {
        $validate = $this->validate();
        if ($validate['error']) {
            throw new \Exception($validate['msg']);
            return false;
        }

        return $this->repository->persist($this->model);
    }

    /**
     * Validações dos atributos antes da persistência
     *
     * @return array
     */
    private function validate()
    {
        $validate = ['error'=> false, 'msg' => false];

        if ($this->getTipo() == '' && $this->getNumemp() == '') {
            $validate['error'] = true;
            $validate['msg'] .= "[Tipo de Serviço] Campos obrigatórios não informados.\n";
        }

        if ($this->getTipo() != 0 && $this->getCNO() == '') {
            $validate['error'] = true;
            $validate['msg'] .= "[Tipo de Serviço] O CNO deve ser informado.\n";
        }

        if (!in_array($this->getTipo(), array_keys(TipoServicoObraRepository::labels()))) {
            $validate['error'] = true;
            $validate['msg'] .= "[Tipo de Serviço] O Tipo informado não existe.\n";
        }

        return $validate;
    }

    /**
     * Retorna o label correspondente ao tipo passsado
     *
     * @param string $tipo
     * @return string
     */
    public function label($tipo)
    {
        return TipoServicoObraRepository::labels($tipo);
    }
}
