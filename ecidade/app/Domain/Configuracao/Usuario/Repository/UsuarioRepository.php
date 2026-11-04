<?php

namespace App\Domain\Configuracao\Usuario\Repository;

use App\Domain\Configuracao\Configuracao\Repositories\MenuPermissionRepository;

class UsuarioRepository
{
    /**
     * @param $user_id
     * @param $permission_id
     * @return bool
     */
    public static function hasPermission($user_id, $permission_id)
    {
        $menuPermissionRepository =  new MenuPermissionRepository();
        if ($menuPermissionRepository->verificaAdmin($user_id)) {
            return true;
        }

        $results = $menuPermissionRepository->userHasPermission($user_id, $permission_id);
        return !$results->isEmpty();
    }
}
