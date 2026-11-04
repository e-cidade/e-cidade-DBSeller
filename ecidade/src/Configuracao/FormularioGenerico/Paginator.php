<?php
namespace ECidade\Configuracao\FormularioGenerico;

use \League\Fractal\Pagination\PaginatorInterface;

class Paginator implements PaginatorInterface
{
    /**
     * @var Page
     */
    protected $paginator;

    protected $data;

    /**
     * @return void
     */
    public function __construct(Page $paginator, $data)
    {
        $this->paginator = $paginator;
        $this->data = $data;
    }

    /**
     * Get the current page.
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->paginator->getNumber();
    }

    /**
     * Get the last page.
     *
     * @return int
     */
    public function getLastPage()
    {
        return ceil($this->paginator->getTotal() / $this->paginator->getSize());
    }

    /**
     * Get the total.
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->paginator->getTotal();
    }

    /**
     * Get the count.
     *
     * @return int
     */
    public function getCount()
    {
        return count((array) $this->data);
    }

    /**
     * Get the number per page.
     *
     * @return int
     */
    public function getPerPage()
    {
        return $this->paginator->getSize();
    }

    /**
     * Get the url for the given page.
     *
     * @param int $page
     *
     * @return string
     */
    public function getUrl($page)
    {
        // @TODO
        return $page;
    }
}
