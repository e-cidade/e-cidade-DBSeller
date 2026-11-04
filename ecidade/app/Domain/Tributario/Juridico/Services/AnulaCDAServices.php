<?php

namespace App\Domain\Tributario\Juridico\Services;

use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Tributario\Juridico\Repository\CDARepository;
use App\Domain\Tributario\Juridico\Repository\InicialRepository;
use App\Notifications\UserNotification;
use DBString;
use Exception;

class AnulaCDAServices
{
    private $observacao;
    private $cda;
    private $inicial;
    private $DB_anousu;
    private $DB_instit;
    private $DB_datausu;
    private $DB_id_usuario;
    private $DB_acessado;
    private $CDARepository;

    public function __construct(
        $cda,
        $observacao,
        $inicial,
        $DB_anousu,
        $DB_instit,
        $DB_datausu,
        $DB_id_usuario,
        $DB_acessado
    ) {
        $this->cda         = $cda;
        $this->observacao    = $observacao;
        $this->inicial       =  $inicial;
        $this->DB_anousu     = $DB_anousu;
        $this->DB_instit     = $DB_instit;
        $this->DB_datausu    = $DB_datausu;
        $this->DB_id_usuario = $DB_id_usuario;
        $this->DB_acessado   = $DB_acessado;

        require_once(modification("dbforms/db_funcoes.php"));
    }

    public function anula()
    {
        try {
            $_SESSION["DB_instit"]     = $this->DB_instit;
            $_SESSION["DB_anousu"]     = $this->DB_anousu;
            $_SESSION["DB_id_usuario"] = $this->DB_id_usuario;
            $_SESSION["DB_acessado"]   = $this->DB_acessado;
            $_SESSION["DB_datausu"]    = $this->DB_datausu;

            db_inicio_transacao();
            db_query("select fc_putsession('DB_anousu','" . $this->DB_anousu . "');");
            db_query("select fc_putsession('DB_instit','" . $this->DB_instit . "');");
            db_query("select fc_putsession('DB_datausu','" . date("Y-m-d", $this->DB_datausu) . "');");
            db_query("select fc_putsession('DB_id_usuario','" . $this->DB_id_usuario . "');");
            $this->observacao = DBString::utf8_decode_all($this->observacao);
            $sqlerro = false;

            $this->CDARepository = new CDARepository(
                $this->cda,
                $this->observacao,
                $this->DB_datausu,
                $this->DB_id_usuario,
                $this->DB_instit
            );

            if ($this->inicial != '') {
                $inicialcert = db_query("select * from inicialcert where v51_inicial = " . $this->inicial);
                $num_inicialcert = pg_num_rows($inicialcert);
                $InicialRepository = new InicialRepository($this->cda, $this->inicial, $this->observacao);
                if ($num_inicialcert == 1) {
                    $sqlerro = $InicialRepository->anulaInicial();
                } else {
                    $sqlerro = $InicialRepository->desvinculaCDA();
                }
                if (!$sqlerro) {
                    $sqlerro =  $this->CDARepository->cancelaCDA();
                }
            } else {
                $sqlerro =  $this->CDARepository->cancelaCDA();
            }
            db_fim_transacao($sqlerro);
            return true;
        } catch (Exception $th) {
            return false;
        }
    }

    public function notificaUsuario()
    {
        $titulo = 'Processamento concluído.';
        $mensagem = 'Anulação de CDA por Lista.';
        $user = Usuario::find($this->DB_id_usuario);
        $user->notify(new UserNotification($titulo, $mensagem));
    }
}
