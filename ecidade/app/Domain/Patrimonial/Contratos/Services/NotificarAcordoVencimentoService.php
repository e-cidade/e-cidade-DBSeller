<?php

namespace App\Domain\Patrimonial\Contratos\Services;

use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Patrimonial\Contratos\Models\MensageriaAcordo;
use App\Domain\Patrimonial\Contratos\Models\MensageriaAcordoUsuarioNotificado;
use App\Notifications\UserNotificationAcordoVencimento;
use Illuminate\Support\Facades\DB;

class NotificarAcordoVencimentoService
{
    public static function getAcordosNaoVencidos()
    {

        return DB::select("
            select
                acordos.acordo.ac16_instit as instituicao,
                acordos.acordo.ac16_sequencial as id_acordo,
                configuracoes.db_usuarios.id_usuario,
                DATE_PART('day',ac16_datafim::timestamp - CURRENT_DATE::timestamp) as diff_dias
            from
                acordos.acordo
            inner join acordos.acordocomissao on
                acordos.acordo.ac16_acordocomissao = acordos.acordocomissao.ac08_sequencial
            inner join acordos.acordocomissaomembro on
                acordos.acordocomissao.ac08_sequencial = acordos.acordocomissaomembro.ac07_acordocomissao
            inner join configuracoes.db_usuacgm on
                acordos.acordocomissaomembro.ac07_numcgm = configuracoes.db_usuacgm.cgmlogin
            inner join configuracoes.db_usuarios on
                configuracoes.db_usuacgm.id_usuario = configuracoes.db_usuarios.id_usuario
            WHERE
                ac16_datafim > CURRENT_DATE;
        ");
    }

    public static function notificar(
        $mensageria_acordo_dias,
        $usuario_id,
        $acordo_id
    ) {

        MensageriaAcordoUsuarioNotificado::create([
            'mensageria_acordo_dias' => $mensageria_acordo_dias->dia,
            'usuario_id' => $usuario_id,
            'acordo_id' => $acordo_id
        ]);

        Usuario::find($usuario_id)->notify(new UserNotificationAcordoVencimento(
            $mensageria_acordo_dias->id,
            $acordo_id
        ));
    }

    public static function checarSeFoiNotificado($mensageria_acordo_dias, $usuario_id, $acordo_id)
    {
        return MensageriaAcordoUsuarioNotificado::where(
            'mensageria_acordo_dias',
            $mensageria_acordo_dias
        )
            ->where('usuario_id', $usuario_id)
            ->where('acordo_id', $acordo_id)->first();
    }

    public static function getConfiguracoes()
    {
        return MensageriaAcordo::all();
    }

    public static function notificarAcordoVencimento()
    {
        $acordos = self::getAcordosNaoVencidos();
        $configuracoes = self::getConfiguracoes();

        foreach ($configuracoes as $configuracao) {
            self::notificarAcordosDaInstituicao($configuracao, $acordos);
        }
    }

    private static function notificarAcordosDaInstituicao($configuracao, $acordos)
    {
        foreach ($configuracao->dias as $dia) {
            self::notificarAcordosDoDia($configuracao, $dia, $acordos);
        }
    }

    private static function notificarAcordosDoDia($configuracao, $dia, $acordos)
    {
        foreach ($acordos as $acordo) {
            if ($acordo->diff_dias == $dia->dia and $acordo->instituicao == $configuracao->instituicao_id) {
                if (self::checarSeFoiNotificado($dia->dia, $acordo->id_usuario, $acordo->id_acordo)) {
                    continue;
                }
                self::notificar($dia, $acordo->id_usuario, $acordo->id_acordo);
            }
        }
    }
}
