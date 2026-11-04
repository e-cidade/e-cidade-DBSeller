<?php

namespace App\Notifications;

use App\Domain\Configuracao\Menu\Models\Item;
use App\Domain\Configuracao\RelarorioLegal\Model\Relatorio;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class EmissaoRelatorioLegalNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $message;

    /**
     *
     * @var array
     */
    public $action;

    /**
     * @var array
     */
    public $externalLinks = [];
    /**
     * @var string
     */
    public $menuName;

    /**
     * Create a new notification instance.
     * @param array $filtros
     * @return void
     */
    public function __construct($filtros)
    {
        $nomeRelatorio = $this->getNomeRelatorio($filtros['relatorio']);
        $this->titulo = \DBString::utf8_encode_all('Anexo da Lrf emitido');
        $this->message = \DBString::utf8_encode_all(sprintf('Emissão do %s esta pronta.', $nomeRelatorio));

        $this->createMenuRedirection($filtros);
        $this->createDownloadLinks($filtros);
    }

    public function via($notifiable)
    {
        return ['broadcast', 'database'];
    }

    public function toArray()
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'action' => $this->action,
            'menuName' => $this->menuName,
            'externalLinks' => $this->externalLinks
        ];
    }

    private function getNomeMenu($codigo)
    {
        $model = Item::find($codigo);
        return $model->descricao;
    }

    protected function getNomeRelatorio($codigo)
    {
        $model = Relatorio::find($codigo);
        return $model->o42_descrrel;
    }

    /**
     * @param array $filtros
     * @return void
     */
    private function createMenuRedirection(array $filtros)
    {
        $menuName = $this->getNomeMenu($filtros['DB_itemmenu_acessado']);
        $this->menuName = \DBString::utf8_encode_all($menuName);
        $url = sprintf(
            'web/financeiro/contabilidade/msc/lrf/anexo/%s/%s/%s',
            strtolower($filtros['tipo']),
            $filtros['anexo'],
            $filtros['consolidado']
        );

        $this->action = [
            "action" => $url,
            "iInstitId" => $filtros['DB_instit'],
            "iAreaId" => 2,
            "iModuloId" => $filtros['DB_modulo'],
            "lAtalhoDesktop" => true
        ];
    }

    private function createDownloadLinks(array $filtros)
    {
        $rota = 'v4/api/financeiro/contabilidade/relatorio-legal/emissao/download';

        $pdf = sprintf('%s/%s', $rota, $filtros['pdf']);
        $xls = sprintf('%s/%s', $rota, $filtros['xls']);

        $this->externalLinks[] = (object)[
            'label' => 'PDF',
            'url' => url($pdf)
        ];

        $this->externalLinks[] = (object)[
            'label' => 'XLS',
            'url' => url($xls)
        ];
    }
}
