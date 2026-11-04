<?php
namespace App\Domain\Patrimonial\Protocolo\Repository\Processo;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Patrimonial\Protocolo\Model\Processo\Processo;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\Contracts\ProcessoRepository as RepositoryInterface;
use App\Domain\Tributario\Cadastro\Models\DbSyscampo;
use App\Traits\Pagination;
use cl_protprocesso;
use DBDepartamentoRepository;

use ECidade\Lib\Session\DatabaseSession;
use ECidade\Lib\Session\DefaultSession;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Classe abstrata para Repositorys. Usa eloquent
 *
 * @var string
 */
final class ProcessoRepository extends BaseRepository implements RepositoryInterface
{
    use Pagination;
    /**
     * Model class for repo.
     *
     * @var string
     */
    protected $modelClass = Processo::class;

    /**
     * @var cl_protprocesso
     */
    private $dao;


    /**
     * Construtor da classe
     *
     * @param cl_protprocesso $dao
     */
    public function __construct()
    {
        $this->dao = new cl_protprocesso();
    }

    /**
     * Fun??o que salva um novo registro
     *
     * @param Processo $model
     * @return Processo
     * @throws Exception
     */
    public function persist(Processo $model, $siglaDoDocumentoNaNumeracao = true)
    {
        DefaultSession::getInstance()->set(DefaultSession::DB_CODDEPTO, db_getsession('DB_coddepto'));
        DatabaseSession::getInstance()->addSessionToDatabase();
        $this->dao->p58_codigo     = $model->getCodigo();
        $this->dao->p58_numcgm     = $model->getCgm();
        $this->dao->p58_requer     = $model->getRequerente();
        $this->dao->p58_codandam   = $model->getCodigoAndamento();
        $this->dao->p58_obs        = str_replace("'", "\"", $model->getObservacao());
        $this->dao->p58_despacho   = $model->getDespacho();
        $this->dao->p58_interno    = ($model->getInterno()) ? 'true' : 'false';
        $this->dao->p58_publico    = ($model->getPublico()) ? 'true' : 'false';
        $this->dao->p58_ano        = $model->getAno();
        $this->dao->p58_processopai = $model->getProcessoPai();

        if (empty($model->getCodigoProcesso())) {
            $this->dao->p58_coddepto    = db_getsession('DB_coddepto');
            $this->dao->p58_dtproc      = date('Y-m-d', db_getsession('DB_datausu'));
            $this->dao->p58_id_usuario  = db_getsession('DB_id_usuario');
            $this->dao->p58_hora        = db_hora();
            $this->dao->p58_instit      = db_getsession('DB_instit');
            $model->setDepartamento($this->dao->p58_coddepto);
            $model->setInstituicao($this->dao->p58_instit);
            $model->setData($this->dao->p58_dtproc);
            $model->setUsuario($this->dao->p58_id_usuario);

            $orgao = null;
            $tipoDocumentoProcesso = null;
            if (\ProcessoProtocoloNumeracao::TIPOORGAO == \ProcessoProtocoloNumeracao::getTipoConfiguracao()
                or $siglaDoDocumentoNaNumeracao
            ) {
                $orgao = DBDepartamentoRepository::getIdOrgaoByCodigo(db_getsession('DB_coddepto'));
                $daoTipoProcesso = new \cl_tipoproc;
                $sql = $daoTipoProcesso->sql_query(
                    null,
                    'p51_prottipodocumentoprocesso',
                    null,
                    "p51_codigo = {$this->dao->p58_codigo}"
                );
                $postgresObject = db_query($sql);

                $rsTipoProcesso = pg_fetch_assoc($postgresObject);
                $tipoDocumentoProcesso = $rsTipoProcesso['p51_prottipodocumentoprocesso'];
            }


            if (!empty($model->getProcessopai())) {
                $processo = Processo::find($model->getProcessopai());
                if (!$processo) {
                    throw new \Exception("Processo Principal não encotrado!");
                }
                $volume  = 0;
                $maiorVolume  = $processo->volumes()->max('p58_volume');
                if (!empty($maiorVolume)) {
                    $volume = $maiorVolume;
                }
                $volume++;
                $this->dao->p58_volume = $volume;
                $volume = str_pad($volume, 3, '0', STR_PAD_LEFT);
                $this->dao->p58_ano = $processo->getAno();
                $this->dao->p58_numero =   $processo->getNumero().$volume;
            } else {
                $this->dao->p58_numero = \ProcessoProtocoloNumeracao::getProximoNumero(
                    $orgao,
                    $tipoDocumentoProcesso,
                    $siglaDoDocumentoNaNumeracao
                );
            }


            $this->dao->p58_tipoprocesso = $model->getTipoProcesso();
            $this->dao->incluir(null);

            $model->setCodigoProcesso($this->dao->p58_codproc);
            $model->setNumero($this->dao->p58_numero);
            $model->setData($this->dao->p58_dtproc);
            $model->setHora($this->dao->p58_hora);
            $model->setUsuario($this->dao->p58_id_usuario);
        } else {
            $this->dao->p58_codproc = $model->getCodigoProcesso();
            $this->dao->alterar($model->getCodigoProcesso());
        }

        if ($this->dao->erro_status == 0) {
            throw new Exception("Erro ao salvar dados do processo!\\n{$this->dao->erro_msg}");
        }

        return $model;
    }

    public function rotulosPesquisa()
    {
        $data = [];
        $nomeCampos = [
            'p58_codproc',
            'p58_codigo',
            'p58_dtproc',
            'p58_id_usuario',
            'p58_numcgm',
            'p58_requer',
            'p58_obs',
            'p58_ano'
        ];

        $nomeLabels = array_map(function ($nomeCampo) {
            return DbSyscampo::select("rotulo")->where('nomecam', '=', $nomeCampo)->first()->toArray();
        }, $nomeCampos);
        if (count($nomeCampos) == count($nomeLabels)) {
            for ($index = 0; $index < count($nomeCampos); $index++) {
                $data[$nomeCampos[$index]] = ucfirst($nomeLabels[$index]["rotulo"]);
            }
        }

        return $data;
    }

    public static function getByParams(
        $p58_dtproc,
        $p58_codproc,
        $p58_requer,
        $p58_obs,
        $p58_ano,
        $porPagina
    ) {

        $lista = new Processo();

        $where = " 1 = 1 ";

        if (trim($p58_codproc) != "") {
            $where .= " and p58_codproc = {$p58_codproc} ";
        }

        if (trim($p58_requer) != "") {
            $where .= " and p58_requer ilike '{$p58_requer}' ";
        }

        if (trim($p58_obs) != "") {
            $where .= " and p58_obs ilike '{$p58_obs}' ";
        }

        if (trim($p58_ano) != "") {
            $where .= " and p58_ano = {$p58_ano} ";
        }

        if (trim($p58_dtproc) != "") {
            $where .= " and p58_dtproc = '{$p58_dtproc}' ";
        }

        $pesquisa = $lista->whereRaw($where)
            ->select([
                'p58_codproc',
                'p58_requer',
                'p58_obs',
                'p58_ano',
                \DB::raw('TO_CHAR(p58_dtproc, \'dd/mm/yyyy\') as p58_dtproc')
            ])->orderBy('p58_codproc', 'ASC')
            ->paginate($porPagina);

        return $pesquisa;
    }


    public function pesquisaProcesso(
        $page = 1,
        $perPage = 10,
        $ocultarPai = "false",
        $ocultarVolume = "false",
        $ocultarArquivado = "false",
        $filtros = array(
             "field" => "processo_codigo",
             "type" => "ilike",
             "value" => ""
         ),
        $order = array(
            "field" => "processo_codigo",
            "dir" => "asc"
        )
    ) {

        $clprotprocesso = new \cl_protprocesso;
        $campos = [];
        $ordem = "";
        $where = [];

        $tiposDeFiltros = array("ilike", "=", ">", "<", "<=", ">=");

        $camposAceitos = array(
            'processo_codigo',
            'processo_numero',
            'processo_tipo',
            'processo_data',
            'processo_descricao',
            'instituicao_nome',
            'titular_cgm',
            'titular_nome',
            'processo_requerente',
            'atendimento_numero',
            'processo_volume',
        );



        $sqlFiltro = "";
        if (!empty($filtros["value"])) {
            if (!in_array($filtros["field"], $camposAceitos)) {
                throw new \Exception("Campo de filtro inv?lido!");
            }

            if (!in_array($filtros["type"], $tiposDeFiltros)) {
                throw new \Exception("Tipo de filtro inv?lido!");
            }

            if ($filtros["type"] == "ilike") {
                $filtros["field"] = $filtros["field"]."::text";
                $filtros["value"] = "'%{$filtros["value"]}%'";
            }

            if ($filtros["type"] != "ilike") {
                if ($filtros["field"] == "processo_data") {
                    $filtros["field"] = $filtros["field"]."::date";
                    $filtros["value"] = "'{$filtros["value"]}'";
                } else {
                    $filtros["value"] = "'{$filtros["value"]}'";
                }
            }


            $sqlFiltro = "AND ".join(" ", $filtros);
        }

        if ($ocultarArquivado == "true") {
            $where[] = "not exists (
              select 1 from arqandam
              where p69_codandam = p58_codandam
                and p69_arquivado is true
            )";
        }

        $campos[] = "   p58_codproc AS processo_codigo,
                        cast(p58_numero||'/'||p58_ano as varchar) AS processo_numero,
                        p109_nome AS processo_tipo,
                        p58_dtproc AS processo_data,
                        p58_requer AS processo_requerente,
                        p51_descr AS processo_descricao,
                        p58_obs AS processo_observacao,
                        z01_numcgm AS titular_cgm,
                        z01_nome AS titular_nome,
                        a.nomeinst AS  instituicao_nome,
                        (ov01_numero ||'/'||ov01_anousu)::varchar as atendimento_numero,
                        case when p58_processopai = 0 then 'Não' else 'Sim' end AS processo_volume
                  ";

        $cl_acordoparam = new \cl_acordoparam;
        $rsAcordoparam = $cl_acordoparam->sql_record($cl_acordoparam->sql_query_file(
            null,
            "ac59_permiteporinstit",
            null,
            "ac59_instituicao = " . db_getsession('DB_instit')
        ));
        $parametro =  pg_fetch_object($rsAcordoparam);

        if (!empty($parametro)) {
            if ($parametro->ac59_permiteporinstit === 'f') {
                $where[] = "p58_instit = " . db_getsession("DB_instit");
            }
        }

        if ($ocultarVolume == "true") {
            $where[] = 'p58_processopai = 0';
        }

        if ($ocultarPai == "true") {
            $where[] = 'p58_processopai != 0';
        }

        $where[] = "p58_tipoprocesso != 4";
        $where = implode(' AND ', $where);
        $campos = implode(' ,', $campos);


        $sqlAuxiliar = $clprotprocesso->sql_query("", $campos, $ordem, $where);

        $sql = "
            with consulta_processo as (
                $sqlAuxiliar
            )
            select
            *
            from
                consulta_processo
            where
                 true
            {$sqlFiltro}
            order by {$order["field"]} {$order["dir"]}
        ";

        $result =  DB::select($sql);
        return $this->paginate($result, $perPage, $page);
    }
}
