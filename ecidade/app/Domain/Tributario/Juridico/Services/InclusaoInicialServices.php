<?php

namespace App\Domain\Tributario\Juridico\Services;

use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Tributario\Juridico\Repository\InicialRepository;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\DB;
use DBString;
use Exception;

class InclusaoInicialServices
{
    private $DB_anousu;
    private $DB_instit;
    private $DB_datausu;
    private $DB_id_usuario;
    private $DB_acessado;

    public function __construct(
        $DB_anousu,
        $DB_instit,
        $DB_datausu,
        $DB_id_usuario,
        $DB_acessado
    ) {

        $this->DB_anousu     = $DB_anousu;
        $this->DB_instit     = $DB_instit;
        $this->DB_datausu    = $DB_datausu;
        $this->DB_id_usuario = $DB_id_usuario;
        $this->DB_acessado   = $DB_acessado;

        $_SESSION["DB_instit"]     = $DB_instit;
        $_SESSION["DB_anousu"]     = $DB_anousu;
        $_SESSION["DB_id_usuario"] = $DB_id_usuario;
        $_SESSION["DB_acessado"]   = $DB_acessado;
        $_SESSION["DB_datausu"]    = $DB_datausu;

        require_once(modification("dbforms/db_funcoes.php"));
    }

    public function incluir(
        $certid,
        $observacao,
        $v50_advog,
        $v50_codlocal,
        $gera,
        $cert_ant
    ) {
        try {
            db_inicio_transacao();
            db_query("select fc_putsession('DB_anousu','" . $this->DB_anousu . "');");
            db_query("select fc_putsession('DB_instit','" . $this->DB_instit . "');");
            db_query("select fc_putsession('DB_datausu','" . date("Y-m-d", $this->DB_datausu) . "');");
            db_query("select fc_putsession('DB_id_usuario','" . $this->DB_id_usuario . "');");
            $observacao = DBString::utf8_decode_all($observacao);
            $sqlerro = false;

            $InicialRepository = new InicialRepository($certid, null, $observacao);
            $sqlerro = $InicialRepository->inclusaoInicial(
                $v50_advog,
                $v50_codlocal,
                $gera,
                $cert_ant
            );
            db_fim_transacao($sqlerro);
            return true;
        } catch (Exception $th) {
            return false;
        }
    }

    public function notificaUsuario($obs)
    {
        $titulo = 'Inlcusão de Inicial por lista concluída.';
        $mensagem = "Verifique o intervalo na aba Hitorico";
        $user = Usuario::find($this->DB_id_usuario);
        $user->notify(new UserNotification($titulo, $mensagem));
    }
}
