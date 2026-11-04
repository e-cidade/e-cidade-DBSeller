<?php

namespace App\Domain\Patrimonial\Protocolo\Repository\Processo;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Patrimonial\Protocolo\Model\Processo\AndamentoProcessoInterno;

class ProcessoAndamentoInternoRepository extends BaseRepository
{

    protected $modelClass = AndamentoProcessoInterno::class;

    /**
     * @var \cl_procandamint
     */
    private $dao;


    /**
     * Construtor da classe
     */
    public function __construct()
    {
        $this->dao = new \cl_procandamint();
    }


    /**
     * Função que salva um novo registro
     *
     * @param  AndamentoProcessoInterno  $model
     *
     * @throws \Exception
     */
    public function persist(
        AndamentoProcessoInterno $model
    ) {
        $this->dao->p78_codandam     = $model->getCodigoAndamento();
        $this->dao->p78_data         = $model->getData();
        $this->dao->p78_hora         = $model->getHora();
        $this->dao->p78_usuario      = $model->getCodigoUsuario();
        $this->dao->p78_despacho     = $model->getDespacho();
        $this->dao->p78_publico      = $model->isPublico() ? 't' : 'f';
        $this->dao->p78_transint     = $model->isTransint() ? 't' : 'f';
        $this->dao->p78_tipodespacho = $model->getTipoDespacho();

        if (! empty($model->getSequencial())) {
            $this->dao->p78_sequencial = $model->getSequencial();
            $this->dao->alterar($model->getSequencial());
        } else {
            $this->dao->incluir(null);
            $model->setSequencial($this->dao->p78_sequencial);
        }

        if ($this->dao->erro_status == 0) {
            throw new \Exception($this->dao->erro_msg);
        }

        return $model;
    }
}
