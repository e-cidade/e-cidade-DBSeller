<?php
namespace App\Domain\Tributario\Arrecadacao\Controller\Parcelamento;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Tributario\Arrecadacao\Services\DebitoService;
use App\Domain\Tributario\Arrecadacao\Services\ParcelamentoService;
use App\Domain\Tributario\Arrecadacao\Services\ParcelamentoImpressaoTermoService;
use App\Domain\Tributario\Arrecadacao\Services\ParcelamentoImpressaoSimulacaoService;
use ECidade\Lib\Session\DefaultSession;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface;
use Exception;

class ParcelamentoController extends Controller
{
    /**
     * @param Request $request
     * @return DBJsonResponse
     */
    public function index(Request $request)
    {
    }

    public function retornaDebitos(ServerRequestInterface $request)
    {
        $debitos = new DebitoService();
        if ($request->getParsedBody()["origem_parcelamento"] == 1) {
            $complMsg = " Contate o Suporte.";
        } else {
            $complMsg = " Contate a Prefeitura.";
        }
        $debitos->setTipoDebito($request->getParsedBody()["tipo_debito"]);
        $debitos->setGrupoDebito($request->getParsedBody()["grupo_debito"]);
        $debitos->setAnoUsu($request->getParsedBody()["anousu"]);
        $debitos->setDataUsu($request->getParsedBody()["datausu"]);
        $debitos->setCgm($request->getParsedBody()["ver_numcgm"]);
        $debitos->setMatric($request->getParsedBody()["ver_matric"]);
        $debitos->setInscr($request->getParsedBody()["ver_inscr"]);
        $debitos->setParcelamento($request->getParsedBody()["parcelamento"]);
        $debitos->setPermParc($request->getParsedBody()["permiteparcelar"]);
        $debitos->setValor($request->getParsedBody()["valor"]);
        $debitos->setValorCorr($request->getParsedBody()["valorcorr"]);
        $debitos->setJuros($request->getParsedBody()["juros"]);
        $debitos->setMulta($request->getParsedBody()["multa"]);
        $debitos->setTotReg($request->getParsedBody()["totregistros"]);
        $debitos->setAreaRegraParc($request->getParsedBody()["origem_parcelamento"]);
        $debitos->setInstit(DefaultSession::getInstance()->get('DB_instit'));
        $debitos->setFormEmissao($request->getParsedBody()["formemissao"]);
        $debitos->setCertidao($request->getParsedBody()["certidao"]);
        $debitos->setPerfilProc($request->getParsedBody()["perfil_procuradoria"]);
        $debitos->setNumpres($request->getParsedBody()["numpresaparcelar"]);
        if ($request->getParsedBody()["inicial"] == 'false') {
            $debitos->setTipoReg("NUMPRE");
        } else {
            $debitos->setTipoReg("INICIAL");
        }
        return $debitos;
    }

    public function retornaOpcoes(ServerRequestInterface $request)
    {
        $debitos = $this->retornaDebitos($request);
        if ($request->getParsedBody()["origem_parcelamento"] == 1) {
            $complMsg = " Contate o Suporte.";
        } else {
            $complMsg = "cac@fazenda.niteroi.rj.gov.br, ou atendimentoppf@pgm.niteroi.rj.gov.br";
        }
        try {
            $parcelamentos = new ParcelamentoService();
            $parcelamentos->setDebitos($debitos);
            $parcelamentos->iniciaTransacao();
            $parcelamentos->excluiProtesto();
            if ($debitos->getTipoReg() == "INICIAL") {
                $parcelamentos->validaInicialCRA();
                $parcelamentos->validaProtesto();
            }
            $parcelamentos->validaLoteador();
            $parcelamentos->validaAreaRegraParc();
            $parcelamentos->validaTipoPessoa();
            $parcelamentos->validaDescrDebito();
            $parcelamentos->validaReceita();
            $parcelamentos->getRegraParcelamento();
            $parcelas = $parcelamentos->getOpcoesParcelas();
            $parcelamentos->cancelaTransacao();
            if (count($parcelas) > 0) {
                return new DBJsonResponse($parcelas, "", 200);
            } else {
                return new DBJsonResponse(
                    [],
                    "Houve um erro ao retornar as Opções.".$complMsg,
                    400
                );
            }
        } catch (Exception $e) {
            return new DBJsonResponse(
                ["exception" => utf8_encode_all($e->getMessage().$complMsg)],
                $e->getMessage(),
                400
            );
        }
    }

    public function processaParcelamento(ServerRequestInterface $request)
    {
        $debitos = $this->retornaDebitos($request);
        if ($request->getParsedBody()["origem_parcelamento"] == 1) {
            $complMsg = " Contate o Suporte.";
        } else {
            $complMsg = " Contate a Prefeitura.";
        }
        try {
            $parcelamentos = new ParcelamentoService();
            $parcelamentos->setDebitos($debitos);
            $parcelamentos->iniciaTransacao();
            $parcelamentos->excluiProtesto();
            if ($debitos->getTipoReg() == "INICIAL") {
                $parcelamentos->validaInicialCRA();
                $parcelamentos->validaProtesto();
            }
            $parcelamentos->validaLoteador();
            $parcelamentos->validaAreaRegraParc();
            $parcelamentos->validaTipoPessoa();
            $parcelamentos->validaDescrDebito();
            $parcelamentos->validaReceita();
            $parcelamentos->getRegraParcelamento();
            $parcelamentos->getOpcoesParcelas();
            $parcelamentos->setSelecaoParcela($request->getParsedBody()["opcao"]);
            $parcelamentos->setPrimeiraParcela($request->getParsedBody()["priParc"]);
            $parcelamentos->setSegundaParcela($request->getParsedBody()["segParc"]);
            $parcelamentos->setDiaVencimento($request->getParsedBody()["diaParc"]);
            $parcelamentos->setValorParcela($request->getParsedBody()["vlrParc"]);
            $parcelamentos->setValorUltimaParcela($request->getParsedBody()["vlrUltParc"]);
            $parcelamentos->setLogin($request->getParsedBody()["login"]);
            $parcelamentos->setIPAddress($request->getParsedBody()["ipAddress"]);
            DefaultSession::getInstance()->set('DB_itemmenu_acessado', 1);
            DefaultSession::getInstance()->set('DB_desativar_account', false);
            $retornoProc = $parcelamentos->processar();
            if ($request->getParsedBody()["acao"] == 2) {
                $impressaoParc = new ParcelamentoImpressaoSimulacaoService();
                $impressaoParc->setDadosParcelamento($parcelamentos);
                $mensagem = "Simulacao gerada com sucesso!";
                $url = $impressaoParc->gerarArquivoSimulacao();
                $parcelamentos->cancelaTransacao();
                $redirDbpref = "";
            } else {
                $parcelamentos->validaTransacao();
                $impressaoParc = new ParcelamentoImpressaoTermoService();
                $impressaoParc->setDadosParcelamento($parcelamentos);
                if ($request->getParsedBody()["origem_parcelamento"] == 1) {
                    $mensagem = "Parcelamento ".$parcelamentos->getTermo();
                    $mensagem.= " gerado com sucesso! Numpre: ".$parcelamentos->getNumpreTermo();
                } else {
                    $mensagem = "Parcelamento ".$parcelamentos->getTermo()." gerado com sucesso!";
                }
                $url = $impressaoParc->gerarArquivoTermo();
                $redirDbpref = $parcelamentos->geraRedirecionamento();
            }
            return new DBJsonResponse(array(
                'success' => true,
                'message' => $mensagem,
                'path' => $url,
                'redir' => $redirDbpref,
                'acao' => $request->getParsedBody()["acao"]
            ), 200);
        } catch (Exception $e) {
            return new DBJsonResponse(
                ["exception" => utf8_encode_all($e->getMessage().$complMsg)],
                $e->getMessage(),
                400
            );
        }
    }
}
