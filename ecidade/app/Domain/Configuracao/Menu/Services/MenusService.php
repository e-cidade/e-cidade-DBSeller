<?php

namespace App\Domain\Configuracao\Menu\Services;

use App\Domain\Configuracao\Menu\Models\Item;
use App\Domain\Configuracao\Menu\Models\ItemMenuFavorito;
use App\Domain\Configuracao\Menu\Models\Menu;
use App\Domain\Configuracao\Menu\Models\Modulo;
use App\Domain\Configuracao\Menu\Models\Permissao;
use App\Domain\Configuracao\Menu\Repositories\ItensRepository;
use App\Domain\Configuracao\Menu\Repositories\MenusRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class MenusService
{
    /**
     * @var MenusRepository
     */
    private $repository;

    /**
     * @var ItensRepository
     */
    private $itensRepository;

    public function __construct(MenusRepository $repository, ItensRepository $itensRepository)
    {
        $this->repository = $repository;
        $this->itensRepository = $itensRepository;
    }

    /**
     * @return Modulo[]|Builder[]|Collection|\Illuminate\Support\Collection
     */
    public function getModulos()
    {
        return Modulo::whereHas('item', function (Builder $query) {
            $query->where('libcliente', true);
        })->whereHas('permissao', function (Builder $query) {
            if (session('DB_id_usuario') != 1) {
                $query->where('id_usuario', session('DB_id_usuario'));
            }
        })->orderBy('nome_modulo')->get();
    }

    /**
     * @param int $codigo
     * @param int $codigoModulo
     * @return Builder[]|Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function getArvoreMenu($codigo, $codigoModulo)
    {
        $menus = $this->itensRepository->getMenusFilho($codigo, $codigoModulo);
        foreach ($menus as $menu) {
            $menu->filhos = $this->getArvoreMenu($menu->id_item, $codigoModulo);
        }

        return $menus;
    }

    /**
     * @param int $codigoModulo
     * @param int $codigoPai
     * @param array $params
     * @return void
     * @throws \Exception
     */
    public function salvar($codigoModulo, $codigoPai, array $params)
    {
        $item = new Item();
        $item->descricao = $params['descricao'];
        $item->help = $params['ajuda'];
        $item->funcao = $params['rota'];
        $item->itemativo = 1;
        $item->manutencao = '1';
        $item->desctec = $params['descricaoTecnica'];
        $item->libcliente = $params['liberadoCliente'];

        if (!$item->save()) {
            throw new \Exception('Erro ao salvar item de menu.');
        }

        $menu = new Menu();
        $menu->id_item = $codigoPai;
        $menu->modulo = $codigoModulo;
        $menu->id_item_filho = $item->id_item;
        $menu->menusequencia = $this->repository->getPosicaoMenu($codigoModulo);

        if (!$menu->save()) {
            throw new \Exception('Erro ao vincular menu.');
        }

        $permissao = new Permissao();
        $permissao->id_item = $item->id_item;
        $permissao->id_modulo = $codigoModulo;
        $permissao->permissaoativa = '1';
        $permissao->id_instit = session('DB_instit');
        $permissao->id_usuario = session('DB_id_usuario');
        $permissao->anousu = session('DB_anousu');

        if (!$permissao->save()) {
            throw new \Exception('Erro ao salvar permissão do menu.');
        }

        \DBMenu::limpaCache();
    }

    public function salvarItemMenuFavorito(
        $idMenu,
        $idInsituicao,
        $idusuario,
        $idModulo,
        $idArea
    ) {
        $itemMenuFavorito = new ItemMenuFavorito();
        $itemMenuFavorito->id_item_menu = (integer) $idMenu;
        $itemMenuFavorito->id_insituicao = (integer) $idInsituicao;
        $itemMenuFavorito->id_usuario = (integer) $idusuario;
        $itemMenuFavorito->modulo = (integer) $idModulo;
        $itemMenuFavorito->area = (integer) $idArea;
        return $itemMenuFavorito->save();
    }

    public function deletaFavorito(
        $idMenu,
        $idUsuario,
        $idInsituicao
    ) {
        return ItemMenuFavorito::where('id_item_menu', (integer) $idMenu)
            ->where('id_usuario', (integer) $idUsuario)
            ->where('id_insituicao', (integer) $idInsituicao)
            ->delete();
    }

    public function buscarFavoritos($idUsuario)
    {
        return ItemMenuFavorito::query()
            ->with('instituicao', 'item')
            ->where('id_usuario', $idUsuario)
            ->get();
    }
}
