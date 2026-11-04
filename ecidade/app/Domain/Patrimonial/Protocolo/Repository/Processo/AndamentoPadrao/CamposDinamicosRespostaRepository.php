<?php
namespace App\Domain\Patrimonial\Protocolo\Repository\Processo\AndamentoPadrao;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Patrimonial\Protocolo\Model\Processo\AndamentoPadrao\CamposDinamicosResposta;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\Contracts\AndamentoPadrao\CamposDinamicosRespostaRepository as
    RepositoryInterface;

use Exception;

/**
* Classe abstrata para Repositorys. Usa eloquent
*
* @var string
*/
final class CamposDinamicosRespostaRepository extends BaseRepository implements RepositoryInterface
{
    /**
     * Model class for repo.
     *
     * @var string
     */
    protected $modelClass = CamposDinamicosResposta::class;

    /**
     * Construtor da classe
     *
     * @param cl_camposandpadraoresposta $dao
     */
    public function __construct(\cl_camposandpadraoresposta $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Função que salva um novo registro
     *
     * @param CamposDinamicosResposta $model
     */
    public function persist(CamposDinamicosResposta $model)
    {
        $this->dao->p111_sequencial       = $model->getCodigo();
        $this->dao->p111_camposandpadrao  = $model->getCamposandpadrao();
        $this->dao->p111_codandam         = $model->getCodandam();
        $this->dao->p111_resposta         = $model->getResposta();

        if (empty($model->getCodigo())) {
            $this->dao->incluir(null);
            $model->setCodigo($this->dao->p111_sequencial);
        } else {
            $this->dao->alterar($model->getCodigo());
        }

        if ($this->dao->erro_status == 0) {
            throw new Exception("Erro ao salvar resposta dos campos dinamicos do processo!\\n{$this->dao->erro_msg}");
        }

        return $this->dao;
    }

    public function getAll($codigoVinculoCampo)
    {
        return $this->newQuery()
                    ->where('p111_camposandpadrao', $codigoVinculoCampo)
                    ->join('db_syscampo', 'db_syscampo.codcam', '=', 'camposandpadraoresposta.p111_codcam')
                    ->orderBy('p111_sequencial', 'desc')
                    ->get();
    }

    public function getUltimaResposta($codigoVinculoCampo, $codigoAndamento)
    {
        return $this->newQuery()
                    ->join('db_syscampo', 'db_syscampo.codcam', '=', 'camposandpadraoresposta.p111_codcam')
                    ->where('p111_camposandpadrao', $codigoVinculoCampo)
                    ->where('p111_codandam', $codigoAndamento)
                    ->orderBy('p111_sequencial', 'desc')
                    ->limit(1)
                    ->get();
    }

    public function findByCodigo($codigoSequencial)
    {
        return $this->newQuery()
                    ->join('db_syscampo', 'db_syscampo.codcam', '=', 'camposandpadraoresposta.p111_codcam')
                    ->where('p111_sequencial', $codigoSequencial)
                    ->get();
    }
}
