<?php

namespace App\Domain\Patrimonial\Protocolo\Repository\Processo;

use App\Domain\Configuracao\Helpers\StorageHelper;
use cl_protprocessodocumento;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\AbstractPaginator as Paginator;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Patrimonial\Protocolo\Model\Processo\ProcessoDocumento;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\Contracts\ProcessoDocumentoRepository as RepositoryInterface;

use Exception;
use Ramsey\Uuid\Uuid;

/**
 * Classe abstrata para Repositorys. Usa eloquent
 *
 * @var string
 */
final class ProcessoDocumentoRepository extends BaseRepository implements RepositoryInterface
{
    /**
     * Model class for repo.
     *
     * @var string
     */
    protected $modelClass = ProcessoDocumento::class;
    /**
     * @var cl_protprocessodocumento
     */
    private $dao;

    /**
     * Construtor da classe
     */
    public function __construct()
    {
        $this->dao = new cl_protprocessodocumento();
    }

    /**
     * Função que salva um novo registro
     *
     * @param ProcessoDocumento $model
     * @return ProcessoDocumento
     * @throws Exception
     */
    public function persist(
        ProcessoDocumento $model
    ) {

        $this->dao->p01_protprocesso = $model->getProcesso();
        $this->dao->p01_descricao = $model->getDescricao();
        $this->dao->p01_nomedocumento = $model->getNomeDocumento();
        $this->dao->p01_documento = $model->getDocumento();
        $this->dao->p01_usuario = $model->getUsuario();
        $this->dao->p01_data = $model->getData();
        $this->dao->p01_procandamint = $model->getAndamento();
        $this->dao->p01_estorage = ($model->getStorage() === true) ? 't' : 'f';
        $this->dao->p01_ordem = ($model->getOrdem() === null) ? 0 : $model->getOrdem();
        $this->dao->p01_c70codlan = ($model->getC70codlan() === null) ? null : $model->getC70codlan();
        $this->dao->p01_upload_name = ($model->getNomeUpload() === null) ? null : $model->getNomeUpload();

        if (!$this->dao->incluir(null)) {
            throw new Exception($this->dao->erro_msg);
        }

        $model->setCodigo($this->dao->p01_sequencial);
        return $model;
    }

    /**
     * Busca os documentos pelo código do processo
     *
     * @param int $codigoProcesso
     */
    public function findByProcesso($codigoProcesso)
    {
        $query = $this->newQuery();
        $query->selectRaw(
            "p01_sequencial as sequencial,
            p01_ordem as ordem,
            p01_documento as numdocumento,
            COALESCE(p01_documento::text,p01_nomedocumento)  AS id_estorage,
            p01_procandamint as codigo_andamento_interno,
            p01_descricao as descricao,
            p01_assinado as assinado,
            p01_assinado_por as assinador_por,
            (select nome from db_usuarios where  id_usuario =  p01_assinado_por ) AS nome_assinou
            "
        );

        $query->where('p01_protprocesso', '=', $codigoProcesso);
        $query->where('p01_estorage', true);
        $query->orderBy('p01_ordem');
        $query->orderBy('p01_sequencial');
        return $this->doQuery($query);
    }


    /**
     * Busca os documentos pelo código do processo
     *
     * @param int $codigoProcesso
     */
    public function findByProcandamint($procandamint)
    {
        $query = $this->newQuery();
        $query->selectRaw(
            "p01_sequencial as sequencial,
            p01_ordem as ordem,
            p01_documento as numdocumento,
            COALESCE(p01_documento::text,p01_nomedocumento)  AS id_estorage,
            p01_procandamint as codigo_andamento_interno,
            p01_descricao as descricao,
            p01_assinado as assinado,
            p01_assinado_por as assinador_por,
            p01_documento_hash as qrcode_hash,
            (select nome from db_usuarios where  id_usuario =  p01_assinado_por ) AS nome_assinou
            "
        );

        $query->where('p01_procandamint', '=', $procandamint);
        $query->where('p01_estorage', true);
        $query->orderBy('p01_ordem');
        $query->orderBy('p01_sequencial');
        $docs = $this->doQuery($query);
        $docs = $docs->map(function ($doc) {
            if (empty($doc->qrcode_hash)) {
                $doc->qrcode_hash = Uuid::uuid4();
            }
            return $doc;
        });
        return $docs;
    }

    /**
     * Busca os documentos pelo código do processo
     *
     * @param int $codigoProcesso
     */
    public function findByProcessoAndProcandamint($codigoProcesso, $procandamint)
    {
        $query = $this->newQuery();
        $query->selectRaw(
            "p01_sequencial as sequencial,
            p01_ordem as ordem,
            p01_documento as numdocumento,
            COALESCE(p01_documento::text,p01_nomedocumento)  AS id_estorage,
            p01_procandamint as codigo_andamento_interno,
            p01_descricao as descricao,
            p01_assinado as assinado,
            p01_assinado_por as assinador_por,
            p01_documento_hash as qrcode_hash,
            (select nome from db_usuarios where  id_usuario =  p01_assinado_por ) AS nome_assinou
            "
        );

        $query->where('p01_protprocesso', '=', $codigoProcesso);
        $query->where('p01_procandamint', '=', $procandamint);
        $query->where('p01_estorage', true);
        $query->orderBy('p01_ordem');
        $query->orderBy('p01_sequencial');
        $docs = $this->doQuery($query);
        $docs = $docs->map(function ($doc) {
            $response = StorageHelper::currentVersion($doc->id_estorage);
            if (!empty($response->data->codigo)) {
                $processoDocumento = ProcessoDocumento::where(
                    "p01_procandamint",
                    "=",
                    $doc->codigo_andamento_interno
                )->first();
                $processoDocumento->p01_documento = $response->data->codigo;
                $doc->id_estorage = $response->data->codigo;
            }

            if (empty($doc->qrcode_hash)) {
                $doc->qrcode_hash = Uuid::uuid4();
            }
            return $doc;
        });
        return $docs;
    }

    public function deleteByP01Documento($p01_documento)
    {
        $this->dao->excluir(
            null,
            "p01_documento = $p01_documento"
        );
    }
}
