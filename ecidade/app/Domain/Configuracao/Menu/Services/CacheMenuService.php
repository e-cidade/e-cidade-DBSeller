<?php

namespace App\Domain\Configuracao\Menu\Services;

use DBCache;
use Illuminate\Support\Facades\DB;

class CacheMenuService
{
    const CACHE_PATH = 'menu';

    private $cache;

    public function __construct()
    {
        $this->cache = new DBCache;
    }

    /**
     * Realiza o cache das areas
     *
     * @return void
     */
    public function cacheAreas()
    {
        $areas = DB::select("
            select distinct at26_sequencial as id,
            at25_descr as nome
            from atendcadarea
            inner join atendcadareamod on at26_sequencial = at26_codarea
            order by at25_descr
        ");

        $this->saveOnCache(self::CACHE_PATH . '/areas', $areas);
    }

    /**
     * Realiza o cache dos modulos
     *
     * @return void
     */
    public function cacheModulos()
    {
        $modulos = DB::select("
            select distinct db_modulos.id_item as id,
            db_modulos.descr_modulo as nome,
            at26_codarea as area
            from db_itensmenu
             inner join db_menu on db_itensmenu.id_item = db_menu.id_item
             inner join db_modulos on db_itensmenu.id_item = db_modulos.id_item
             inner join atendcadareamod on db_modulos.id_item = at26_id_item
             inner join atendcadarea on atendcadarea.at26_sequencial = atendcadareamod.at26_codarea
            where libcliente is true
            order by db_modulos.descr_modulo
        ");

        $this->saveOnCache(self::CACHE_PATH . '/modulos', $modulos);
    }

    /**
     * Realiza o cache dos itens
     *
     * @return void
     */
    public function cacheItens()
    {
        $itensMenu = DB::select("
            select m.modulo as modulo,
            m.id_item as id_pai,
            m.id_item_filho as id,
            i.descricao as nome,
            i.funcao as action,
            db_modulos.descr_modulo as modulo_nome,
            menusequencia
            from db_menu m
            inner join db_itensmenu i on i.id_item = m.id_item_filho
            left join db_modulos on m.modulo = db_modulos.id_item
            where libcliente is true
            and i.itemativo = '1'
            order by menusequencia
        ");

        $itensModulo = array_filter($itensMenu, function ($item) {
            return $item->id_pai == $item->modulo;
        });

        foreach ($itensModulo as &$item) {
            $item->breadcrumb = $item->modulo_nome . ' > ' . $item->nome;
            $this->getItensFilho($item, $itensMenu);
        }

        $this->saveOnCache(self::CACHE_PATH . '/itens', $itensModulo);
    }

    /**
     * Chamada recursiva para montar o menu e filhos
     *
     * @param object $item
     * @param array $itensMenu Todos os itens do menu
     * @return void
     */
    private function getItensFilho(&$item, $itensMenu)
    {
        $filhos = array_filter($itensMenu, function ($filho) use ($item) {
            return $filho->id_pai == $item->id && $item->modulo == $filho->modulo;
        });

        $item->filhos = empty($filhos) ? false : $filhos;

        if ($filhos) {
            foreach ($filhos as &$itemFilho) {
                $itemFilho->breadcrumb = $item->breadcrumb . ' > ' . $itemFilho->nome;
                $this->getItensFilho($itemFilho, $itensMenu);
            }
        }
    }

    /**
     * Retorna os breadcrumbs para o search
     *
     * @param array|null $itens
     * @param array $breadcrumbs
     * @return array|false
     */
    public function getBreadcrumbs($itens = null, $breadcrumbs = [])
    {
        if (empty($itens)) {
            $itens = $this->cache->read(self::CACHE_PATH . '/itens');
            if (!$itens) {
                return false;
            }
        }

        foreach ($itens as $item) {
            if ($item->action) {
                $breadcrumbs[] = (object) [
                    'id' => $item->id,
                    'nome' => $item->nome,
                    'action' => $item->action,
                    'modulo' => $item->modulo,
                    'area' => '1',
                    'breadcrumb' => $item->breadcrumb
                ];
            }

            if ($item->filhos) {
                $breadcrumbs = $this->getBreadcrumbs($item->filhos, $breadcrumbs);
            }
        }

        return $breadcrumbs;
    }

    /**
     * Retorna as areas do cache
     *
     * @return array|false
     */
    public function getAreas()
    {
        if ($this->cache->check(self::CACHE_PATH . '/areas')) {
            return $this->cache->read(self::CACHE_PATH . '/areas');
        }

        return false;
    }

    /**
     * Retorna os modulos do cache
     *
     * @param int $areaId
     * @return array|false
     */
    public function getModulos($areaId)
    {
        if ($this->cache->check(self::CACHE_PATH . '/modulos')) {
            $modulos = $this->cache->read(self::CACHE_PATH . '/modulos');
            if ($modulos) {
                return array_filter($modulos, function ($modulo) use ($areaId) {
                    return $modulo->area == $areaId;
                });
            }
        }

        return false;
    }

    /**
     * Retorna os itens do cache
     *
     * @param int $moduloId
     * @return array|false
     */
    public function getItens($moduloId)
    {
        if ($this->cache->check(self::CACHE_PATH . '/itens')) {
            $itens = $this->cache->read(self::CACHE_PATH . '/itens');
            if ($itens) {
                return array_filter($itens, function ($item) use ($moduloId) {
                    return $item->modulo == $moduloId;
                });
            }
        }

        return false;
    }

    /**
     * Salva os dados no cache
     *
     * @param string $path
     * @param mixed $data
     * @return void
     */
    public function saveOnCache($path, $data)
    {
        $data = \DBString::utf8_encode_all($data);
        $this->cache->write($path, $data);
    }

    /**
     * Limpa o cache no caminho informado
     *
     * @param string $path
     * @return void
     */
    public function clear($path)
    {
        $this->cache->remove($path);
    }

    /**
     * Limpa todo o cache
     *
     * @return void
     */
    public function clearAll()
    {
        $this->cache->remove(self::CACHE_PATH . '/areas');
        $this->cache->remove(self::CACHE_PATH . '/modulos');
        $this->cache->remove(self::CACHE_PATH . '/itens');
    }
}
