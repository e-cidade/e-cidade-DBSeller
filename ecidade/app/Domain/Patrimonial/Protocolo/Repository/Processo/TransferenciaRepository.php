<?php
namespace App\Domain\Patrimonial\Protocolo\Repository\Processo;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Patrimonial\Protocolo\Model\Processo\Transferencia;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\Contracts\TransferenciaRepository as RepositoryInterface;

use cl_proctransfer;
use Exception;

/**
* Classe repository do model que faz ligação entre um processo e um atendimento da ouvidoria
*
* @var string
*/
final class TransferenciaRepository extends BaseRepository implements RepositoryInterface
{
    /**
     * Model class for repo.
     *
     * @var string
     */
    protected $modelClass = Transferencia::class;
    /**
     * @var cl_proctransfer
     */
    private $dao;

    /**
     * Construtor da classe
     *
     * @param cl_proctransfer
     */
    public function __construct()
    {
        $this->dao = new cl_proctransfer();
    }

    /**
     * Função que salva um novo registro
     *
     * @param Transferencia $model
     * @throws Exception
     */
    public function persist(
        Transferencia $model
    ) {

        $usuarioRecebimento  =  $model->getUsuarioRecebimento();
        $this->dao->p62_dttran      = $model->getData();
        $this->dao->p62_id_usuario  = $model->getUsuario();
        $this->dao->p62_coddepto    = $model->getDepartamento();
        $this->dao->p62_id_usorec   = !empty($usuarioRecebimento) ? $usuarioRecebimento : 0;
        $this->dao->p62_coddeptorec = $model->getDepartamentoRecebimento();
        $this->dao->p62_hora        = $model->getHora();

        if (!empty($model->getCodigo())) {
            $this->dao->p62_codtran = $model->getCodigo();
            $this->dao->alterar($model->getCodigo());
        } else {
            $this->dao->incluir(null);
            $model->setCodigo($this->dao->p62_codtran);
        }

        if ($this->dao->erro_status == 0) {
            throw new \Exception($this->dao->erro_msg);
        }
    }
}
