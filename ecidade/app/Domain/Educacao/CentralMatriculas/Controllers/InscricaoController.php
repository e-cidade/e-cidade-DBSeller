<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace App\Domain\Educacao\CentralMatriculas\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\CentralMatriculas\Models\ObservacaoInscricao;
use App\Domain\Educacao\CentralMatriculas\Services\ProcessoInscricaoService;
use App\Http\Controllers\Controller;
use DBString;
use ECidade\Configuracao\Api\Generator\HashGenerator;
use ECidade\Configuracao\Api\Repository\ApiClienteRepository;
use ECidade\Educacao\MatriculaOnline\Model\Inscricao;
use ECidade\Educacao\MatriculaOnline\Pdf\ComprovanteInscricao;
use ECidade\Educacao\MatriculaOnline\Registry\ConfiguracaoRegistry;
use ECidade\Educacao\MatriculaOnline\Repository\AlteracaoInscricaoRepository;
use ECidade\Educacao\MatriculaOnline\Request\InscricaoRequest;
use ECidade\Educacao\MatriculaOnline\Service\InscricaoService;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class InscricaoController extends Controller
{
    protected $service;
    public function __construct()
    {
        require_once(modification("dbforms/db_funcoes.php"));
        $this->service = new ProcessoInscricaoService();
    }

    public function inscricao(Request $inscricaoRequest)
    {
        db_inicio_transacao();
        try {
            if ($inscricaoRequest->has('hash')) {
                $dados = $this->decriptLegacyApiV1($inscricaoRequest);
                $dados = DBString::urldecode_all($dados);
                $dados = DBString::utf8_decode_all($dados);
                $inscricaoRequest = new InscricaoRequest($dados);
            }

            $inscricarService = new InscricaoService();
            $configuracao = ConfiguracaoRegistry::get();

            if ($configuracao->isValidaAlunoMatriculado()) {
                $candidatoJaEstaMatriculado = $inscricarService->verificaAlunoMatriculado($inscricaoRequest);
                // nova validacao
                $validaProtocolo = false;
                if ($inscricaoRequest->get('protocolo')) {
                    $validaProtocolo = true;
                    $candidatoJaEstaMatriculado = false;
                }

                if ($candidatoJaEstaMatriculado && !$validaProtocolo) {
                    $procesamentoService = new ProcessoInscricaoService();
                    $tipo = 'cpf';
                    $data = $inscricaoRequest->get('data_nascimento');
                    $data = explode("/", $data);
                    $dataNascimento = "{$data[2]}-{$data[1]}-{$data[0]}";
                    $dadoConsulta = '';
                    $nacionalidade = $inscricaoRequest->get('nacionalidade');
                    $cpf = $inscricaoRequest->get('cpf');
                    $rnm = $inscricaoRequest->get('rnm');
                    $visto = $inscricaoRequest->get('visto');

                    if ($nacionalidade == 3 && !empty($visto)) {
                        $tipo = "visto";
                        $dadoConsulta = $visto;
                    }

                    if ($nacionalidade == 3 && !empty($rnm)) {
                        $tipo = "rne";
                        $dadoConsulta = $rnm;
                    }

                    if (!empty($cpf) && $cpf !== '00000000000') {
                        $tipo = "cpf";
                        $dadoConsulta = $cpf;
                    }

                    $candidatoJaEstaMatriculado = $procesamentoService->consultaCandidato(
                        $tipo,
                        $dadoConsulta,
                        $dataNascimento,
                        true
                    );
                }

                if ($candidatoJaEstaMatriculado) {
                    $response = ['erro' => 'candidato_ja_matriculado'];
                    db_fim_transacao(true);
                    return new DBJsonResponse($response, "Candidato já está matriculado em uma escola da Rede!", 400);
                }
            }

            $inscricao = $inscricarService->saveFromRequest($inscricaoRequest);
            $listaEsperaService = new \ECidade\Educacao\MatriculaOnline\Service\ListaEsperaService();
            $listaEsperaService->setFase($inscricao->getFase());

            $opcoesLista = $inscricao->getOpcoesListaEspera();
            foreach ($opcoesLista as $opcaoListaEspera) {
                $listaEsperaService->setEscola($opcaoListaEspera->getEscola())
                    ->setEtapa($opcaoListaEspera->getEtapa())
                    ->setTurno($opcaoListaEspera->getTurno());

                $listaEsperaService->classificar();
            }

            $comprovante = $this->emitirComprovante($inscricao);

            $response = [
                'protocolo' => $inscricao->getProtocolo(),
                'path' => $comprovante
            ];

            db_fim_transacao(false);
            return new DBJsonResponse($response, "Inscrição efetuada com sucesso!");
        } catch (Exception $exception) {
            db_fim_transacao(true);
            return new DBJsonResponse([], $exception->getMessage(), 400);
        }
    }

    public function emissaoProtocolo(Request $request)
    {
        try {
            $dados = $request->all();
            if ($request->has('hash')) {
                $dados = $this->decriptLegacyApiV1($request);
            }
            if (!isset($dados['protocolo'])) {
                throw new Exception("Protocolo não foi informado.");
            }

            $inscricarService = new InscricaoService();
            $inscricarService->setProtocolo($dados['protocolo']);
            $inscricao = $inscricarService->getInscricao();

            $comprovante = $this->emitirComprovante($inscricao);

            return new DBJsonResponse([
                'success' => true,
                'message' => utf8_encode("Comprovante gerado com sucesso!"),
                'body' => [
                    'protocolo' => $inscricao->getProtocolo(),
                    'path' => $comprovante
                ]
            ], '', 200);
        } catch (Exception $e) {
            return new DBJsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], '', 400);
        }
    }

    private function emitirComprovante(Inscricao $inscricao)
    {
        $alteracaoInscricaoRepository = new AlteracaoInscricaoRepository();
        $alteracoesInscricao = $alteracaoInscricaoRepository->getLastByType($inscricao);

        $inscricao->setAlteracoesInscricao($alteracoesInscricao);
        $comprovanteInscricao = new ComprovanteInscricao($inscricao);

        return $comprovanteInscricao->imprimir();
    }

    public function buscarObservacoes(Request $request, $candidato)
    {
        $observacoes = ObservacaoInscricao::with('usuario')->candidato($candidato)->get();

        return new DBJsonResponse($observacoes);
    }

    public function adicionarObservacao(Request $request)
    {
        $this->validate($request, [
            'codigo_candidato' => 'required|integer',
            'DB_id_usuario' => 'required|integer',
            'observacao' => 'required',
        ]);

        $observacao = new ObservacaoInscricao();
        if ($request->has('codigo_observacao') && !empty($request->get('codigo_observacao'))) {
            $observacao = ObservacaoInscricao::find($request->get('codigo_observacao'));
        }

        $observacao->mo62_base = $request->get('codigo_candidato');
        $observacao->mo62_usuario = $request->get('DB_id_usuario');
        $texto = str_replace("\\\"", "\"", $request->get('observacao'));
        $texto = str_replace("\\'", "'", $texto);
        $observacao->mo62_observacao = $texto;
        $observacao->save();
        $observacao->load('usuario');

        return new DBJsonResponse($observacao);
    }

    public function excluirObservacao(ObservacaoInscricao $observacao)
    {
        $observacao->delete();

        return new DBJsonResponse();
    }

    /**
     * @param Request $request
     * @return array|bool
     * @throws Exception
     */
    private function decriptLegacyApiV1(Request $request)
    {
        if (!$request->has('hash') || !$request->has('id')) {
            throw new AccessDeniedException('Sem permissão para acessar a API.', 401);
        }

        $id = $request->get('id');

        if (!is_numeric($id)) {
            throw new AccessDeniedException('Sem permissão para acessar a API.', 401);
        }

        $hash = $request->get('hash');

        $cliente = ApiClienteRepository::find($id);

        if ($cliente === false) {
            throw new AccessDeniedException('Cliente não encontrado.', 401);
        }

        $secret = $cliente->getChave();

        $decrypted = HashGenerator::decrypt($hash, $secret);

        if ($decrypted === false) {
            throw new AccessDeniedException('Sem permissão para acessar a API.', 401);
        }

        return $decrypted;
    }

    public function consulta(Request $request, $tipo, $valor)
    {
        return new DBJsonResponse($this->service->consultaInscricao($tipo, $valor, null, $request->get('edicao')));
    }

    public function consultaCpf($cpf, $tipo, $valor)
    {
        return new DBJsonResponse($this->service->consultaInscricao($tipo, $valor, $cpf));
    }

    public function consultaCandidato($tipo, $dado, $nascimento)
    {
        return new DBJsonResponse($this->service->consultaCandidato($tipo, $dado, $nascimento));
    }

    public function delete($protocolo)
    {
        return new DBJsonResponse($this->service->excluirInscricao($protocolo));
    }
}
