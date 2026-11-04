<?php

namespace App\Domain\Configuracao\Usuario\Services;

use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Configuracao\Usuario\Requests\LoginRequest;
use App\Domain\Configuracao\Usuario\Requests\UnblockRequest;
use App\Domain\Core\Exceptions\SecuriImageException;
use ECidade\V3\Window\Session;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use ECidade\V3\Extension\Registry;

const MENSAGEM = 'configuracao.configuracao.abrir.';

/**
 *
 */
class AuthService
{
    private $authorizationServer;

    public function __construct(AuthorizationServer $authorizationServer)
    {
        $this->authorizationServer = $authorizationServer;
    }

    /**
     * @todo revisar esse fluxo
     * A maneira que est? atualmente, foi basicamente "copiada" do fluxo de login antigo.
     *
     * @param LoginRequest $request
     * @return array
     * @throws SecuriImageException
     * @throws \Exception
     */
    public function authenticate(LoginRequest $request)
    {
        try {
            $this->start($request);
            $this->validaCaptcha($request->usuario, $request->username, $request->conteudoCaptcha);
            if ($request->usuario === null || $request->usuario->usuext !== 0) {
                $mensagem = _M(MENSAGEM . "login_invalido");
                db_logsmanual_demais($mensagem);

                throw new \BusinessException($mensagem, 400);
            }
            if (!$request->usuario->validateForPassportPasswordGrant($request->password)) {
                $log = _M(MENSAGEM . 'logs_senha_invalida', (object)['sCampo' => $request->usuario->login]);
                db_logsmanual_demais($log, $request->usuario->getCodigo());

                throw new \BusinessException(_M(MENSAGEM . 'senha_invalida'), 400);
            }

            $this->validaUsuario($request->usuario);
            $this->validaVersao($request->usuario);

            $token = $this->createToken($request->username, $request->password);

            $this->buildSession($request);

            return $token;
        } catch (\Exception $e) {
            session_unset();
            session_destroy();

            throw $e;
        }
    }

    /**
     * @param UnblockRequest $request
     * @return array
     * @throws \Exception
     */
    public function unblock(UnblockRequest $request)
    {
        $session = new Session('MAIN');
        $session->create()->start();
        if (!$session->has('DB_id_usuario')) {
            throw new \BusinessException('Sess?o inv?lida.');
        }

        $usuario = Usuario::find($session->get('DB_id_usuario'));
        if (!$usuario->validateForPassportPasswordGrant($request->password)) {
            $log = _M(MENSAGEM . 'logs_senha_invalida', (object)['sCampo' => $usuario->login]);
            db_logsmanual_demais($log, $usuario->getCodigo());

            throw new \BusinessException(_M(MENSAGEM . 'senha_invalida'), 403);
        }

        $token = $this->refreshToken($request->refresh_token);

        $_SESSION['blocked'] = false;

        return $token;
    }

    /**
     * @param Usuario $usuario
     * @return void
     */
    public function logout(Usuario $usuario)
    {
        $usuario->token()->revoke();

        $this->kill();
    }

    /**
     * Destrói os cookies de sessão
     *
     * @return void
     */
    public function kill()
    {
        Session::destroyAll(true);
    }

    /**
     * @param LoginRequest $request
     * @return void
     * @throws \Exception
     */
    private function start(&$request)
    {
        Session::destroyAll();
        $session = new Session('MAIN');
        $session->create()->start();

        /**
         * Verificamos se existe outra sessao ja registrada e caso exista
         * efetua o unset e o destroy da mesma
         */
        if (!empty($_SESSION['DB_id_usuario'])) {
            session_unset();
            session_destroy();
            session_start();
        }

        db_query("select fc_startsession()");
        db_logsmanual_demais(_M(MENSAGEM . "abrindo_sistema", (object)['sCampo' => $request->username]));

        $request->usuario = (new Usuario())->findForPassport($request->username);
    }

    /**
     * @param Usuario|null $usuario
     * @param string $login
     * @param string $conteudoCaptcha
     * @return void
     * @throws SecuriImageException
     * @throws \BusinessException
     */
    private function validaCaptcha($usuario, $login, $conteudoCaptcha = '')
    {
        /**
         * Valida Tentativas de login do usu?rio
         *
         * Buscamos o parametro de configura??o do n?mero de tentativas de acesso ao portal
         */
        $preferenciaCliente = new \PreferenciaCliente();
        $maxTentativaLogin = $preferenciaCliente->getTentativasLogin();

        /**
         * Verificamos se existe a variavel de tentativ de acesso na sessao
         */
        if (empty($_SESSION['DB_tentativasAcesso'])) {
            $_SESSION['DB_tentativasAcesso'] = new \stdClass();
        }

        $totalTentativas = array_sum((array) $_SESSION['DB_tentativasAcesso']);

        $utilizaCaptcha = env('UTILIZA_CAPTCHA', false);
        if (env('VERIFICA_IP_PRIVADO', false) && $utilizaCaptcha) {
            if (verifica_ip_privado($_SERVER["REMOTE_ADDR"])) {
                $utilizaCaptcha = false;
            }
        }

        if ($utilizaCaptcha || $totalTentativas >= 3) {
            require_once modification('securimage/securimage.php');
            $securiImage = new \Securimage();

            if (!$securiImage->check($conteudoCaptcha)) {
                throw new SecuriImageException(_M(MENSAGEM . "codigo_seguranca_invalido"), 403);
            }
        }

        if (empty($_SESSION['DB_tentativasAcesso']->{$login})) {
            $_SESSION['DB_tentativasAcesso']->{$login} = 1;
            return;
        }

        $_SESSION['DB_tentativasAcesso']->{$login}++;

        /**
         * Validamos se o numero de tentativas excedeu o numero limite configurado
         */
        if ($_SESSION['DB_tentativasAcesso']->{$login} > $maxTentativaLogin) {
            if ($usuario instanceof Usuario && $usuario->isUsuarioAtivo() && !$usuario->isAdministrador()) {
                $usuario->usuarioativo = 2;
                $usuario->save();
            }

            throw new \BusinessException(_M(MENSAGEM . "excedeu_tentativas_acesso"), 403);
        }
    }

    /**
     * @param Usuario $usuario
     * @throws \Exception
     */
    private function validaUsuario($usuario)
    {
        if (!$usuario->isUsuarioAtivo()) {
            throw new \BusinessException(_M(MENSAGEM . 'usuario_bloqueado'), 403);
        }
        // valida data limite para  login
        $dataExpira = $usuario->getDataExpiracao();
        if (!empty($dataExpira) && $dataExpira->getTimestamp() < strtotime(date('Y-m-d'))) {
            $log = _M(MENSAGEM . 'logs_data_expira', (object)['sCampo' => $usuario->login]);
            db_logsmanual_demais($log, $usuario->getCodigo());

            throw new \BusinessException(_M(MENSAGEM . 'data_expira'), 403);
        }

        if (db_verifica_ip_banco($usuario->id_usuario) != '1') {
            throw new \BusinessException(_M(MENSAGEM . 'ip_nao_autorizado'), 403);
        }

        if ($usuario->login === 'dbseller' || $usuario->isAdministrador()) {
            return;
        }

        $rs = db_query("SELECT db21_ativo FROM db_config WHERE prefeitura = TRUE");
        if (!$rs) {
            throw new \DBException('Erro ao verificar se o sistema est? liberado! Contate o suporte.');
        }

        $ativo = pg_fetch_result($rs, 0, 0);
        if (pg_num_rows($rs) == 0) {
            $mensagemLogs = _M(MENSAGEM . "logs_login_sem_departamento", (object)['sCampo' => $usuario->login]);
            db_logsmanual_demais($mensagemLogs);

            throw new \BusinessException(_M(MENSAGEM . "login_sem_departamento"), 403);
        }

        if ($ativo == 3) {
            $mensagem = _M(MENSAGEM . "sistema_desativado");
            db_logsmanual_demais($mensagem);
            throw new \BusinessException($mensagem, 403);
        } elseif ($ativo == 2) {
            $mensagemLogs = _M(MENSAGEM . "logs_acesso_negado", (object)['sCampo' => $usuario->login]);
            db_logsmanual_demais($mensagemLogs);

            throw new \BusinessException(_M(MENSAGEM . "acesso_negado"), 403);
        }
    }

    /**
     * @param Usuario $usuario
     * @return void
     * @throws \DBException
     */
    private function validaVersao($usuario)
    {
        $dao = new \cl_db_versao;
        $sql = $dao->sql_query(null, "db30_codversao, db30_codrelease", "db30_codver desc limit 1");
        $rs = $dao->sql_record($sql);

        if ($dao->numrows == 0) {
            $versaoBanco = (object)[
                'db30_codversao' => '1',
                'db30_codrelease' => '1'
            ];
        } else {
            $versaoBanco = \db_utils::fieldsMemory($rs, 0);
        }

        require_once modification('libs/db_acessa.php');

        $db_fonte_codversao = isset($db_fonte_codversao) ? $db_fonte_codversao : '0';
        $db_fonte_codrelease = isset($db_fonte_codrelease) ? $db_fonte_codrelease : '0';

        $versaoDiferente = $versaoBanco->db30_codversao != $db_fonte_codversao;
        $releaseDiferente = $versaoBanco->db30_codrelease != $db_fonte_codrelease;
        if ($versaoDiferente || $releaseDiferente) {
            $opcoes = (object)[
                'sVersaoFonte' => $db_fonte_codversao . $db_fonte_codrelease,
                'sVersaoBanco' => $versaoBanco->db30_codversao . $versaoBanco->db30_codrelease
            ];

            db_logsmanual_demais(_M(MENSAGEM . 'logs_versao_banco', $opcoes), $usuario->getCodigo());

            throw new \DBException(_M(MENSAGEM . 'versao_banco', $opcoes));
        }
    }

    /**
     * @return void
     * @throws \DBException
     */
    private function buildSession(LoginRequest $request)
    {
        /**
         * Desregistramos a variavel que controla as tentativas de acesso
         */
        unset($_SESSION['DB_tentativasAcesso']);

        if (!isset($_SESSION["DB_acessado"])) {
            $_SESSION["DB_acessado"] = '0';
        }

        // Seta os dados de conex?o com o banco para serem usados pela V3
        $_SESSION["DB_base"] = env('DB_DATABASE', '');
        $_SESSION["DB_NBASE"] = env('DB_DATABASE', '');
        $_SESSION["DB_servidor"] = env('DB_HOST', '');
        $_SESSION["DB_porta"] = env('DB_PORT', '');
        $_SESSION["DB_senha"] = env('DB_PASSWORD', '');
        $_SESSION["DB_user"] = env('DB_USERNAME', '');

        if (app()->isLocal() && !empty($request->DB_HOST) && !empty($request->DB_DATABASE)) {
            $_SESSION["DB_servidor"] = $request->DB_HOST;
            $_SESSION["DB_base"] = $request->DB_DATABASE;
            $_SESSION["DB_NBASE"] = $request->DB_DATABASE;
            $_SESSION["DB_porta"] = $request->DB_PORT;
            $_SESSION["DB_user"] = base64_decode($request->DB_USERNAME);
            $_SESSION["DB_senha"] = base64_decode(\db_stdClass::db_stripTagsJsonSemEscape($request->DB_PASSWORD));
        }

        $_SESSION["DB_login"] = $request->usuario->getLogin();
        $_SESSION["DB_id_usuario"] = $request->usuario->getCodigo();
        $_SESSION["DB_administrador"] = $request->usuario->isAdministrador() ? 1 : 0;
        $_SESSION["DB_desativar_account"] = false;

        /**
         * Realiza a busca das prefer?ncias do usu?rio.
         */
        $preferenciaUsuario = (new \UsuarioSistema(null, null, $request->usuario))->getPreferenciasUsuario();
        $_SESSION["DB_preferencias_usuario"] = base64_encode(serialize($preferenciaUsuario));
        $_SESSION["DB_ip"] = $_SERVER["REMOTE_ADDR"];
    }

    /**
     * @param string $username
     * @param string $password
     * @return array
     * @throws OAuthServerException
     */
    private function createToken($username, $password)
    {
        $clientId = Registry::get('app.config')->get('api.client.id');
        $clientSecret = Registry::get('app.config')->get('api.client.secret');

        return $this->requestToPassport([
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password,
            'client_id' => $clientId,
            'client_secret' => $clientSecret
        ]);
    }

    /**
     * @param string $refreshToken
     * @return array
     * @throws OAuthServerException
     */
    private function refreshToken($refreshToken)
    {
        $clientId = Registry::get('app.config')->get('api.client.id');
        $clientSecret = Registry::get('app.config')->get('api.client.secret');

        return $this->requestToPassport([
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $clientId,
            'client_secret' => $clientSecret
        ]);
    }

    /**
     * @param array $data
     * @return array
     * @throws OAuthServerException
     */
    private function requestToPassport(array $data)
    {
        $request = (new ServerRequest('POST', 'not-important'))->withParsedBody($data);
        $response = $this->authorizationServer->respondToAccessTokenRequest($request, new Response);

        return json_decode($response->getBody()->__toString(), true);
    }
}
