<?php
namespace App\Domain\Patrimonial\Protocolo\Repository\Processo;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Patrimonial\Protocolo\Model\Processo\ProcessoAndamento;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\Contracts\ProcessoAndamentoRepository as RepositoryInterface; // @codingStandardsIgnoreLine

use cl_procandam;
use Exception;

/**
* Classe repository do model que faz ligação entre um processo e um atendimento da ouvidoria
*
* @var string
*/
final class ProcessoAndamentoRepository extends BaseRepository implements RepositoryInterface
{
    /**
     * Model class for repo.
     *
     * @var string
     */
    protected $modelClass = ProcessoAndamento::class;

    /**
     * @var cl_procandam
     */
    private $dao;

    /**
     * Construtor da classe
     */
    public function __construct()
    {
        $this->dao = new cl_procandam();
    }

    /**
     * Função que salva um novo registro
     *
     * @param ProcessoAndamento $model
     * @throws Exception
     */
    public function persist(
        ProcessoAndamento $model
    ) {

        $this->dao->p61_codproc    = $model->getProcesso();
        $this->dao->p61_id_usuario = $model->getUsuario();
        $this->dao->p61_dtandam    = $model->getData();
        $this->dao->p61_despacho   = $model->getDespacho();
        $this->dao->p61_coddepto   = $model->getDepartamento();
        $this->dao->p61_publico    = $model->getPublico() ? 't' : 'f';
        $this->dao->p61_hora       = $model->getHora();

        if (!empty($model->getCodigo())) {
            $this->dao->p61_codandam = $model->getCodigo();
            $this->dao->alterar($model->getCodigo());
        } else {
            $this->dao->incluir(null);
            $model->setCodigo($this->dao->p61_codandam);
        }

        if ($this->dao->erro_status == 0) {
            throw new \Exception($this->dao->erro_msg);
        }

        return $model;
    }
}
