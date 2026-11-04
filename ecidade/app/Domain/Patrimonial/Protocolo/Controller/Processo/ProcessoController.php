<?php

namespace App\Domain\Patrimonial\Protocolo\Controller\Processo;

use App\Domain\Configuracao\Helpers\StorageHelper;
use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use App\Domain\Patrimonial\Protocolo\Model\Processo\Processo;
use App\Domain\Patrimonial\Protocolo\Services\MensageriaService;
use App\Domain\Patrimonial\Protocolo\Services\ProcessoService;
use cl_db_depart;
use ECidade\Patrimonial\Protocolo\Servicos\AndamentoProcessoService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\ProcessoRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProcessoController extends Controller
{
    /**
     * Construtor da classe
     *
     * @return void
     */
    public function __construct(
        ProcessoRepository $processoRepository,
        ProcessoService $processoService,
        AndamentoProcessoService $andamentoProcessoService
    ) {
        $this->repository = $processoRepository;
        $this->processoService = $processoService;
        $this->andamentoProcessoService = $andamentoProcessoService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new DBJsonResponse($this->repository->findAll());
    }



    public function rotulosPesquisa()
    {
        return new DBJsonResponse($this->repository->rotulosPesquisa());
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function getProcesso(Request $request)
    {
        try {
            $porPagina = $request->porPagina ? $request->porPagina : 10;
            $p58_codproc = $request->p58_codproc ? $request->p58_codproc : "";
            $p58_dtproc = $request->p58_dtproc ? $request->p58_dtproc : "";
            $p58_requer = $request->p58_requer ? $request->p58_requer : "";
            $p58_obs = $request->p58_obs ? $request->p58_obs : "";
            $p58_ano = $request->p58_ano ? $request->p58_ano : "";

            $processo = $this->repository->getByParams(
                $p58_dtproc,
                $p58_codproc,
                $p58_requer,
                $p58_obs,
                $p58_ano,
                $porPagina
            )->toArray();
            return new DBJsonResponse($processo);
        } catch (\Exception $e) {
            return new DBJsonResponse([], $e->getMessage(), 400);
        }
    }

    public function getMensagens(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'processo' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $mensageriaService = new MensageriaService();
        $mensagens = $mensageriaService->getMensagensProcesso($request->all());
        return new DBJsonResponse($mensagens);
    }

    /**
     * @throws \Exception
     */
    public function sendMensagem(Request $request)
    {
        $mensageriaService = new MensageriaService();
        return $mensageriaService->saveMensagem($request->all());
    }

    public function criarDespacho(Request $request)
    {
        $mensagem = json_decode($request->get('mensagem'));
        $despachoAnexos = collect();

        if (isset($mensagem->documentos)) {
            foreach ($mensagem->documentos as $anexo) {
                $caminhoArquivo = StorageHelper::downloadArquivo($anexo->p124_codigo_storage);
                $anexo = (object)[
                    'caminho' => $caminhoArquivo,
                    'descricao' => $anexo->p124_nome_documento,
                ];
                $despachoAnexos->push($anexo);
            }
        }

        $parametros = (object)[
            'despachoInterno' => $mensagem->p123_mensagem,
            'codigoProcesso' => $mensagem->p123_processo,
            'despachoPublico' => $request->get('publico') ? 't' : 'false',
            'despachoAnexos' => json_encode($despachoAnexos),
            'origemMensagem' => true
        ];

        $andamentoProcesso = new AndamentoProcessoService($parametros);
        $retorno = $andamentoProcesso->despachar();

        if ($retorno != null) {
            return new DBJsonResponse(
                ['mensagem' => 'Sucesso ao inserir despacho.', 'codigoDespacho' => $retorno->codigo, 'erro' => false]
            );
        } else {
            return new DBJsonResponse(['mensagem' => 'Erro ao criar despacho.', 'erro' => true]);
        }
    }

    public function vincularDespachoMensagem(Request $request)
    {
        $codigoDespacho = (int) $request->get('codigoDespacho');
        $mensageriaService = new MensageriaService();
        $retorno = $mensageriaService->vincularDespachoMensagem($codigoDespacho, $request->get('idMensagem'));

        if ($retorno) {
            return new DBJsonResponse(['mensagem' => 'Sucesso ao inserir e vincular despacho.', 'erro' => false]);
        } else {
            return new DBJsonResponse(['mensagem' => 'Erro ao inserir e vincular despacho.', 'erro' => true]);
        }
    }

    public function getCgmByCpfCnpj(Request $request)
    {
        $cpfcnpj = (String) $request->get('cpfcnpj');
        $cgm = Cgm::where('z01_cgccpf', '=', $cpfcnpj)->first();

        if (empty($cgm)) {
            return new DBJsonResponse(['mensagem' => 'Não foi encontrado CGM para esse CPF/CNPJ', 'erro' => true]);
        }

        return new DBJsonResponse(['mensagem' => 'Dados encontrados', 'cgm' => $cgm->z01_numcgm,'erro' => false]);
    }

    public function buscarProcessos(Request $request)
    {
        $perPage = 5;
        if ($request->get('perPage') !== null) {
            $perPage = $request->get('perPage');
        }

        $processos = null;
        try {
            $processos = $this->processoService->buscarProcessos(
                $perPage,
                $request
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return new DBJsonResponse([], "Erro ao buscar processos", 400);
        }

        return new DBJsonResponse($processos);
    }

    public function processar(Request $request)
    {
        $this->validate($request, [
            'acao' => 'required'
        ]);

        switch ($request->get('acao')) {
            case 'receber':
                $this->validate($request, [
                    'codigoProcesso' => 'required',
                    'hash' => 'required',
                ]);

                try {
                    $resposta = $this->processoService->apenasReceber(
                        $request->get('hash'),
                        $request->get('codigoProcesso'),
                        $request->get('codigoTransferencia')
                    );

                    return new DBJsonResponse($resposta, "Processo recebido com sucesso.", 200);
                } catch (\Exception $e) {
                    Log::error($e->getMessage(), $e->getTrace());
                    return new DBJsonResponse(
                        ["error" => true],
                        "Erro ao receber processo. => {$e->getMessage()}",
                        200
                    );
                }
            case 'receberMassa':
                $resposta = $this->processoService->receberMassa($request->get('processos'));
                return new DBJsonResponse($resposta, "Processados", 200);

            case 'despachar':
                $this->validate($request, [
                    'despachoInterno' => 'required',
                    'despachoPublico' => 'required',
                    'origemMensagem' => 'required'
                ]);

                $resposta = $this->andamentoProcessoService->despacharProcesso($request);
                return new DBJsonResponse($resposta, "Processo despachado com sucesso.", 200);

            case 'despacharMassa':
                $this->validate($request, [
                    'processos' => 'required',
                    'despachoInterno' => 'required',
                    'despachoPublico' => 'required',
                    'origemMensagem' => 'required',
                ]);

                $resposta = $this->andamentoProcessoService->despacharEmMassa($request);
                return new DBJsonResponse($resposta, "Despachados", 200);

            default:
                return new DBJsonResponse(
                    [],
                    "O Tipo de ação não encontrada.",
                    500
                );
        }
    }

    public function arquivar(Request $request)
    {
        $this->validate($request, [
            'codigo' => 'required',
            'motivo' => 'required',
        ]);

        try {
            if (is_array($request->get('codigo'))) {
                $resposta = $this->arquivamentoEmMassa(
                    $request->get('codigo'),
                    $request->get('motivo')
                );
            } else {
                $resposta = "Sucesso";
                $processo = Processo::find($request->get('codigo'));
                $this->processoService->arquivar(
                    $processo,
                    $request->get('motivo'),
                    null
                );
            }

            return new DBJsonResponse($resposta, "Processo arquivado com sucesso.", 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return new DBJsonResponse(
                ["error" => true],
                "Erro ao arquivar processo. => {$e->getMessage()}",
                200
            );
        }
    }

    public static function getOrgao()
    {
        $cldbdepart = new cl_db_depart;
        $dadosOrgaoDepartamento = $cldbdepart->get_orgao() !== null ? $cldbdepart->get_orgao() : null;
        return new DBJsonResponse($dadosOrgaoDepartamento);
    }

    public function verificaPresencaDepartamento($codProc)
    {
        $sql = "select p62_coddeptorec, p58_numero, p58_ano from protocolo.proctransferproc
        inner join protocolo.protprocesso on protocolo.protprocesso.p58_codproc = protocolo.proctransferproc.p63_codproc
        inner join protocolo.proctransfer on protocolo.proctransfer.p62_codtran = protocolo.proctransferproc.p63_codtran
        where p63_codproc = $codProc order by p63_codtran desc limit 1";

        $result = pg_fetch_array(db_query($sql));

        if ((integer) $result[0] === (integer) db_getsession('DB_coddepto')) {
            return new DBJsonResponse(['mensagem' => 'Processo está presente no departamento.', 'erro' => false]);
        }

        $codDepto = (integer) $result[0];
        $sql = "select descrdepto from configuracoes.db_depart where coddepto = $codDepto";

        $nomeDepto = pg_fetch_array(db_query($sql));

        $processo = $result[1] . "/" . $result[2];
        $mensagem = "Processo ".$processo." foi transferido para o departamento ". $result[0] ." - ". $nomeDepto[0];

        return new DBJsonResponse(['mensagem' => $mensagem, 'erro' => true]);
    }

    public function arquivamentoEmMassa($processos, $motivo)
    {
        $processosSuccess = array();
        $processosError = array();

        foreach ($processos as $processo) {
            try {
                $processo = Processo::find($processo["codigoProcesso"]);
                $this->processoService->arquivar(
                    $processo,
                    $motivo,
                    null
                );
                $processosSuccess[] = $processo;
            } catch (\Exception $e) {
                $processoErro = new \stdClass();
                $processoErro->processo = $processo;
                $processoErro->mensagem = $e->getMessage();

                $processosError[] = $processoErro;
                Log::error($e->getMessage(), $e->getTrace());
            }
        }

        $result = new \stdClass();
        $result->arquivados = $processosSuccess;
        $result->erros = $processosError;

        return $result;
    }

    public static function permissoesAndamentoProcesso()
    {

        $idUser = db_getsession("DB_id_usuario");
        $ano = db_getsession("DB_anousu");
        $instituicao = db_getsession("DB_instit");


        $sql = "select administrador from configuracoes.db_usuarios
                     where id_usuario = {$idUser} and usuarioativo = 1 and administrador = 1";
        $result = pg_fetch_all(db_query($sql));

        if (!empty($result)) {
            return 'administrador';
        } else {
            $sql = "SELECT
    i.id_item
FROM
    db_menu m
INNER JOIN
    db_permissao p
    ON p.id_item = m.id_item_filho
INNER JOIN
    db_itensmenu i
    ON i.id_item = m.id_item_filho
    AND p.permissaoativa = '1'
    AND p.anousu = $ano
    AND p.id_instit = $instituicao
WHERE
    i.itemativo = '1'
    AND libcliente = TRUE
    AND api = true
    AND p.id_usuario = $idUser
    and i.id_item in (229286, 229287, 229288, 229289)

UNION

SELECT
     i.id_item
FROM
    db_menu m
INNER JOIN
    db_permherda h
    ON h.id_usuario = $idUser
INNER JOIN
    db_usuarios u
    ON u.id_usuario = h.id_perfil
    AND u.usuarioativo = '1'
INNER JOIN
    db_permissao p
    ON p.id_item = m.id_item_filho
INNER JOIN
    db_itensmenu i
    ON i.id_item = m.id_item_filho
    AND p.permissaoativa = '1'
    AND p.anousu = $ano
    AND p.id_instit = $instituicao
WHERE
    i.itemativo = '1'
    AND api = true
    AND libcliente = TRUE
    AND p.id_usuario = h.id_perfil
    and i.id_item in (229286, 229287, 229288, 229289)";

            return pg_fetch_all(db_query($sql));
        }
    }

    public function pesquisaProcesso(Request $request)
    {

        $order = array(
            "field" => "processo_codigo",
            "dir" => "asc"
        );

        if ($request->get("sortField")) {
            $order["field"] = $request->get("sortField");
        }

        if ($request->get("sortDirection") and in_array($request->get("sortDirection"), array("asc", "desc"))) {
            $order["dir"] = $request->get("sortDirection");
        }


        $filter  = array(
            "field" => $request->get("filterField"),
            "type" => $request->get("filterType"),
            "value" => "{$request->get("filterValue")}"
        );

        $data = $this->repository->pesquisaProcesso(
            $request->get("page", 1),
            $request->get("perpage", 10),
            $request->get("ocultarPai", "false"),
            $request->get("ocultarVolume", "false"),
            $request->get("ocultarArquivado", "false"),
            $filter,
            $order
        );

        return new DBJsonResponse($data);
    }
}
