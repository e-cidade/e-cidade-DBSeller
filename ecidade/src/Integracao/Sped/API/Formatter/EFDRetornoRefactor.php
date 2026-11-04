<?php

namespace ECidade\Integracao\Sped\API\Formatter;

/**
 * Responsavel por refatorar os dados dos eventos
 * de retorno do EFD-REINF
 */
class EFDRetornoRefactor
{
    /**
     * Dados a serem manipulados
     *
     * @var stdClass
     */
    protected $data;

    /**
     * Tipo de Layout
     *
     * @var string
     */
    protected $layout;

    /**
     * Contrutor
     *
     * @param string $layout
     * @param \stdClass $data
     */
    public function __construct($layout, \stdClass $data)
    {
        $this->data = $data;
        $this->layout = $layout;
    }

    /**
     * Centralizador das funcoes de formatacao dos dados
     * por tipo de evento
     *
     * @return object
     */
    public function format()
    {
        switch ($this->layout) {
            case 'R-9001':
                return $this->formatR9001();
                break;
            case 'R-9011':
                return $this->fortmatR9011();
                break;
            default:
                return $this->data;
                break;
        }
    }


    /**
     * Formatacao para o evento R-9011
     *
     * @return object
     */
    private function fortmatR9011()
    {
        if (isset($this->data->infoTotalContrib)) {
            $infoTotalContrib = $this->data->infoTotalContrib;

            // R-2010 SERVICOS TOMADOS
            if (isset($infoTotalContrib->RTom)) {
                foreach ($infoTotalContrib->RTom as $item) {
                    $item->CRTom         = $item->infoCRTom->CRTom;
                    $item->vlrCRTom      = $item->infoCRTom->vlrCRTom;
                    $item->vlrCRTomSusp  = $item->infoCRTom->vlrCRTomSusp;

                    $item->vlrCRTom        = $this->formatMoney($item->vlrCRTom);
                    $item->vlrCRTomSusp    = $this->formatMoney($item->vlrCRTomSusp);
                    $item->vlrTotalBaseRet = $this->formatMoney($item->vlrTotalBaseRet);

                    $item->cnpjPrestador = preg_replace(
                        "/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/",
                        "\$1.\$2.\$3/\$4-\$5",
                        $item->cnpjPrestador
                    );

                    unset($item->infoCRTom);
                }
            }

            // R-2055 AQUISICAO PRODUCAO RURAL
            if (isset($infoTotalContrib->RAquis)) {
                foreach ($infoTotalContrib->RAquis as $item) {
                    $item->vlrCRAquis = $this->formatMoney($item->vlrCRAquis);
                }
            }
        }

        return $this->data;
    }

    /**
     * Formatacao para o evento R-9001
     *
     * @return object
     */
    public function formatR9001()
    {
        // R-2010 SERVICOS TOMADOS
        if (isset($this->data->RTom)) {
            $rtom = &$this->data->RTom;

            $rtom->CRTom        = $rtom->infoCRTom->CRTom;
            $rtom->vlrCRTom     = $rtom->infoCRTom->vlrCRTom;
            $rtom->vlrCRTomSusp = $rtom->infoCRTom->vlrCRTomSusp;
            $rtom->vlrCRTom     = $this->formatMoney($rtom->vlrCRTom);
            $rtom->vlrCRTomSusp = $this->formatMoney($rtom->vlrCRTomSusp);

            $rtom->cnpjPrestador = preg_replace(
                "/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/",
                "\$1.\$2.\$3/\$4-\$5",
                $rtom->cnpjPrestador
            );

            unset($rtom->infoCRTom);
            unset($rtom);
        }

        // R-2055 AQUISICAO PRODUCAO RURAL
        if (isset($this->data->RAquis)) {
            foreach ($this->data->RAquis as $item) {
                $item->vlrCRAquis = $this->formatMoney($item->vlrCRAquis);
            }
        }

        return $this->data;
    }

    /**
     * Helper para formatar padrão moeda
     *
     * @return
     */
    private function formatMoney($value)
    {
        if (empty($value)) {
            return '0,00';
        }

        $value = preg_replace('/\,/', '.', $value);
        $value = number_format($value, 2, ',', '.');

        return $value;
    }
}
