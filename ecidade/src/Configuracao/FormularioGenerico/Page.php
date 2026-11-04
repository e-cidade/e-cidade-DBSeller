<?php
namespace ECidade\Configuracao\FormularioGenerico;

class Page
{
    /**
     * Number of the page
     * @var integer
     */
    protected $number;

    /**
     * Size of the page
     * @var integer
     */
    protected $size;

    /**
     * Length of pages
     * @var integer
     */
    protected $total;

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param int $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }
}
