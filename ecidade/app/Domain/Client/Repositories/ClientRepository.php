<?php

namespace App\Domain\Client\Repositories;

use App\Domain\Configuracao\Usuario\Models\Usuario;
use ECidade\Lib\Session\DefaultSession;

class ClientRepository
{
    /**
     * Busca um usuário pelo CPF ou CNPJ
     * @param $sCpfCnpj
     * @return array
     * @throws \Exception
     */
    public function getUserEcidadeAuthByCpfCnpj($sCpfCnpj)
    {
        $user = new Usuario();
        $aUser = $user->getUserEcidadeByCpfCnpj($sCpfCnpj);

        if (!$aUser->count()) {
            throw new \Exception("Usuário não encontrado na base do e-cidade.", 401);
        }

        $oUser = $aUser->offsetGet(0);

        $oRetorno = new \stdClass();
        $oRetorno->oUser = $oUser;
        $oRetorno->aUser = $oUser->toArray();

        return $oRetorno;
    }

    /**
     * Gera um access token de usuário para autenticar as rotas privadas do e-cidade
     * @param Usuario $user
     * @return string
     */
    public function getAccessTokenUser(Usuario $user)
    {
        return $user->createToken('tokenId')->accessToken;
    }

    /**
     * Atualiza a sessão do usuário com base no usuário autenticado
     * @param $id_usuario
     * @throws \DBException
     */
    public function updateSession($id_usuario)
    {
        DefaultSession::getInstance()->atualizaDadosUsuario($id_usuario);
    }
}
