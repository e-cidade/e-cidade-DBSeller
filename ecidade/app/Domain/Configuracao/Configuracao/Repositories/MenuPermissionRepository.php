<?php

namespace App\Domain\Configuracao\Configuracao\Repositories;

use Illuminate\Support\Facades\DB;

class MenuPermissionRepository
{
    protected $instituicao;
    protected $ano;
    const PERMISSION_RECEBER_PROCESSOS_TERCEIROS = 229362;

    public function __construct()
    {
        $this->instituicao = db_getsession("DB_instit");
        $this->ano = db_getsession("DB_anousu");
    }

    /**
     * Verifica se um usuário tem permissão para um item o item de menu..
     *
     * @param int $idUser O ID do usuário.
     * @param int $id O ID do item de menu.
     * @return \Illuminate\Support\Collection A coleção de IDs de itens.
     */
    public function userHasPermission($idUser, $id)
    {
        $ano = $this->ano;
        $instituicao = $this->instituicao;

        $subQuery1 = DB::table('db_menu as m')
            ->join('db_permissao as p', 'p.id_item', '=', 'm.id_item_filho')
            ->join('db_itensmenu as i', 'i.id_item', '=', 'm.id_item_filho')
            ->where('p.permissaoativa', '1')
            ->where('p.anousu', $ano)
            ->where('p.id_instit', $instituicao)
            ->where('i.itemativo', '1')
            ->where('libcliente', true)
            ->where('api', true)
            ->where('p.id_usuario', $idUser)
            ->where('i.id_item', $id)
            ->select('i.id_item');

        $subQuery2 = DB::table('db_menu as m')
            ->join('db_permherda as h', 'h.id_usuario', '=', DB::raw($idUser))
            ->join('db_usuarios as u', 'u.id_usuario', '=', 'h.id_perfil')
            ->join('db_permissao as p', 'p.id_item', '=', 'm.id_item_filho')
            ->join('db_itensmenu as i', 'i.id_item', '=', 'm.id_item_filho')
            ->where('u.usuarioativo', '1')
            ->where('p.permissaoativa', '1')
            ->where('p.anousu', $ano)
            ->where('p.id_instit', $instituicao)
            ->where('i.itemativo', '1')
            ->where('api', true)
            ->where('libcliente', true)
            ->whereRaw('p.id_usuario = h.id_perfil')
            ->where('i.id_item', $id)
            ->select('i.id_item');

        $query = $subQuery1->union($subQuery2);
        $permissions = $query->get();

        return $permissions;
    }

    /**
     * Verifica se um usuário é um administrador.
     *
     * @param datatype $idUser descrição
     * @return bool
     *@throws Some_Exception_Class descrição da exceção
     */
    public function verificaAdmin($idUser)
    {
        return DB::table('configuracoes.db_usuarios')
            ->where('id_usuario', $idUser)
            ->where('usuarioativo', 1)
            ->where('administrador', 1)
            ->exists();
    }
}
