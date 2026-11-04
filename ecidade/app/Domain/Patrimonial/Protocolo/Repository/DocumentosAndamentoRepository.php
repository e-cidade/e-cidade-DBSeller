<?php

namespace App\Domain\Patrimonial\Protocolo\Repository;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Patrimonial\Protocolo\Model\DocumentoAndamento;
use cl_documentos_andamento;
use ECidade\Core\Request\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class DocumentosAndamentoRepository extends BaseRepository
{
    /**
     * @var string
     */
    protected $modelClass = DocumentoAndamento::class;
    /**
     * @var cl_documentos_andamento
     */
    private $dao;

    private $scopes = [];

    public function __construct()
    {
        $this->dao = new cl_documentos_andamento();
    }

    /**
     * @return DocumentoAndamento[]
     * @throws Exception
     */
    public function get()
    {
        $sql = $this->dao->sql_query_file(null, '*', null, implode(' AND ', $this->scopes));
        $rs = db_query($sql);
        if (!$rs) {
            throw new Exception("Erro ao buscar Documento");
        }
        $dados = [];
        while ($state = pg_fetch_assoc($rs)) {
            $dados[] = DocumentoAndamento::fromState($state);
        }
        return $dados;
    }

    /**
     * @return DocumentoAndamento|null
     * @throws Exception
     */
    public function first()
    {
        $data = $this->get();
        if (count($data) == 0) {
            return null;
        }
        return array_shift($data);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function buscarDocumentosPorUsuario(
        $usuario,
        $dataInicio = null,
        $dataFim = null,
        $limit = null,
        $filtros = null
    ) {

        $where = array();

        if (!empty($dataInicio)) {
            $dataInicio = new  \DBDate($dataInicio);
            $where[] = "p58_dtproc >= '{$dataInicio->convertTo("Y-m-d")}'";
        }


        if (!empty($dataFim)) {
            $dataFim = new  \DBDate($dataFim);
            $where[] = "p58_dtproc <= '{$dataFim->convertTo("Y-m-d")}'";
        }

        if (!empty($filtros["tipo"])) {
            $codigoOrigem = '-1';
            switch ($filtros["tipo"]) {
                case 6:
                    $codigos = $this->filtroEmpenho($filtros);
                    if (count($codigos) > 0) {
                        $codigoOrigem = join(",", $codigos);
                    }
                    $where[] = "protocolo.documentos_andamento.p116_codigo_origem IN ({$codigoOrigem})";
                    break;
            }
            $where[] = "protocolo.tipoproc.p51_prottipodocumentoprocesso = {$filtros["tipo"]}";
        }

        $where[] = "p119_id_usuario = {$usuario->getCodigo()}";
        $where[] = "not exists(
                select 1
                    from documentos_movimentacao
                join processo_atividadesexecucao atividade_executada
                ON atividade_executada.p118_codigo = documentos_movimentacao.p117_processo_atividadesexecucao
                where
                        atividade_executada.p118_atividadesexecucao = proxima_atividade.p118_atividadesexecucao
                    and p117_id_usuario = p119_id_usuario
                    and p117_documento_andamento = p116_codigo
                    and p117_devolucao is false
                    and p117_invalida is false
         )";

        $sql = $this->dao->sql_query_documentos_usuario(
            null,
            'distinct p116_codigo',
            null,
            implode(' and ', $where)
        );

        if (!empty($limit) and is_numeric($limit)) {
            $sql .= " LIMIT {$limit}";
        }

        $rs = db_query($sql);
        if (!$rs) {
            throw new Exception("Erro ao buscar Documentos para Andamento");
        }

        $ids = array();
        while ($result = pg_fetch_object($rs)) {
            $ids[] = $result->p116_codigo;
        }

        return DocumentoAndamento::with('atividadeAtual', 'proximaAtividade')->whereIn("p116_codigo", $ids)->get();
    }

    /**
     * @throws Exception
     */
    public function salvar(DocumentoAndamento $documentoAndamento)
    {
        $this->dao->p116_codigo = $documentoAndamento->p116_codigo;
        $this->dao->p116_descricao = $documentoAndamento->p116_descricao;
        $this->dao->p116_protprocesso = $documentoAndamento->p116_protprocesso;
        $this->dao->p116_protprocessodocumento = $documentoAndamento->p116_protprocessodocumento;
        $this->dao->p116_atividade_atual = $documentoAndamento->p116_atividade_atual;
        $this->dao->p116_proxima_atividade = $documentoAndamento->p116_proxima_atividade;
        $this->dao->p116_codigo_origem = $documentoAndamento->p116_codigo_origem;
        $dataSessao = new \DateTime(date("Y-m-d", db_getsession("DB_datausu")) . date("H:i:s", time()));
        $this->dao->p116_data_modificacao = $dataSessao->format('Y-m-d H:i:s');

        if (empty($this->dao->p116_codigo)) {
            $this->dao->p116_qrcode = $documentoAndamento->p116_qrcode;
            $this->dao->p116_data_criacao = $dataSessao->format('Y-m-d H:i:s');
            $this->dao->incluir(null);
        } else {
            $this->dao->alterar($this->dao->p116_codigo);
        }

        if ($this->dao->erro_status == 0) {
            throw new Exception("Erro ao salvar Documento");
        }

        $documentoAndamento->p116_codigo = $this->dao->p116_codigo;
        return $documentoAndamento;
    }

    /**
     * @param $qrcode
     * @return $this
     */
    public function scopeQRCode($qrcode)
    {
        $this->scopes["qrcode"] = "p116_qrcode = '{$qrcode}'";
        return $this;
    }

    public function scopeCodigoOrigem($codigoOrigem)
    {
        $this->scopes["origem"] = "p116_codigo_origem = {$codigoOrigem}";
        return $this;
    }

    public static function orgaosDoUsuario($codigoUsuario, $ano = null)
    {
        if (empty($ano)) {
            $ano = db_getsession("DB_anousu");
        }

        return DB::select("
            select
            distinct
                o40_orgao,
                orcamento.orcorgao.o40_descr
            from
                configuracoes.db_usupermemp
            inner join configuracoes.db_permemp
            on configuracoes.db_permemp.db20_codperm = configuracoes.db_usupermemp.db21_codperm
            inner join orcamento.orcorgao
            on orcamento.orcorgao.o40_orgao  = configuracoes.db_permemp.db20_orgao
            and  orcamento.orcorgao.o40_anousu  = configuracoes.db_permemp.db20_anousu
            and orcamento.orcorgao.o40_anousu  = {$ano}
            where
            configuracoes.db_usupermemp.db21_id_usuario  = {$codigoUsuario}
            order by o40_orgao asc ;
        ");
    }

    private function filtroEmpenho($filtros)
    {
        $where = [];

        $sql = "
           SELECT
                empenho.empempenho.e60_numemp
            FROM
                empenho.empempenho
            INNER JOIN orcamento.orcdotacao ON
            orcamento.orcdotacao.o58_anousu = empenho.empempenho.e60_anousu
            AND empenho.empempenho.e60_coddot = orcamento.orcdotacao.o58_coddot
           WHERE
               TRUE
        ";

        if (!empty($filtros["orgao"])) {
            $where[] = "AND orcamento.orcdotacao.o58_orgao = {$filtros["orgao"]}";
        }

        $sql = $sql . join(" AND ", $where);
        $results = DB::select($sql);
        $codigos = array();
        foreach ($results as $result) {
            $codigos[] = $result->e60_numemp;
        }
        return $codigos;
    }

    public function documentosComAtividadeEmExecutacao(
        $usuario_atividade = null
    ) {

        $query = $this->newQuery()
            ->join(
                "processo_atividadesexecucao as proxima_atividade",
                "proxima_atividade.p118_codigo",
                'documentos_andamento.p116_proxima_atividade'
            )
            ->leftJoin("processo_usuarios", function ($join) {
                $join->on(
                    "processo_usuarios.p119_protprocesso",
                    "proxima_atividade.p118_protprocesso"
                );
                $join->on(
                    "proxima_atividade.p118_atividadesexecucao",
                    "processo_usuarios.p119_atividadeexecucao"
                );
            })
            ->leftJoin(
                "protocolo.atividadesexecucao",
                "protocolo.atividadesexecucao.p114_codigo",
                "proxima_atividade.p118_atividadesexecucao"
            )->leftJoin(
                "db_usuarios",
                "db_usuarios.id_usuario",
                "processo_usuarios.p119_id_usuario"
            )
            ->orderBy("p116_descricao")
            ->whereRaw("not exists(
                    select
                      1
                    from
                       documentos_movimentacao
                    join processo_atividadesexecucao as atividade_executada on
                    atividade_executada.p118_codigo = documentos_movimentacao.p117_processo_atividadesexecucao
                    where
                    atividade_executada.p118_atividadesexecucao = proxima_atividade.p118_atividadesexecucao
                    and p117_documento_andamento = p116_codigo
                    and p117_devolucao is false
                    and p117_invalida is false
                )
            ");

        if (is_numeric($usuario_atividade)) {
            $query->where("processo_usuarios.p119_id_usuario", $usuario_atividade);
        } else {
            $query->whereNull("processo_usuarios.p119_id_usuario");
        }

        $query = $query->selectRaw(
            "
                documentos_andamento.p116_codigo,
                documentos_andamento.p116_descricao,
	        CASE
	         WHEN array_length(
	          regexp_split_to_array(p116_descricao, ' '),
	          1) > 1
	          THEN
	               array_to_string(
	                  (regexp_split_to_array(p116_descricao, ' '))[
	                    1:array_length( regexp_split_to_array(p116_descricao, ' '), 1)
	                 -1], ' '
	               )
	           ELSE p116_descricao
	         END AS documento,
            (regexp_split_to_array(p116_descricao, ' '))[
               array_length((regexp_split_to_array(p116_descricao, ' ')), 1)
            ] AS numero_documento,
            documentos_andamento.p116_protprocesso,
            protocolo.atividadesexecucao.p114_atividade,
            proxima_atividade.p118_atividadesexecucao,
            db_usuarios.login,
            db_usuarios.nome
            "
        );

        return $query->get();
    }
}
