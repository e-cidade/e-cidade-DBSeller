<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Services;

use App\Domain\Configuracao\GeradorRelatorio\Models\RelatorioTemplate;
use App\Domain\Configuracao\GeradorRelatorio\Relatorios\Traits\PodeMascarar;
use App\Domain\Configuracao\Helpers\StorageHelper;
use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use Carbon\Carbon;
use NcJoes\OfficeConverter\OfficeConverter;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;

class ImprimirRelatorioTemplateService
{
    use PodeMascarar;

    private $codigoRelatorio;

    public function __construct($codigoRelatorio)
    {
        $this->codigoRelatorio = $codigoRelatorio;
    }

    /**
     * @param array $dados
     * @param array $campos
     * @return array
     * @throws \Exception
     */
    public function execute(array $dados, array $campos)
    {
        $dados = array_merge($this->getVariaveisGlobais(), $dados);
        $arquivoTemplate = $this->getTemplate();
        Settings::setTempDir('tmp/');
        $processor = new TemplateProcessor($arquivoTemplate);

        foreach ($processor->getVariables() as $variable) {
            if (!array_key_exists($variable, $dados)) {
                continue;
            }

            $value = $dados[$variable];
            $key = array_search($variable, array_column($campos, 'nome'));
            if ($key !== false) {
                // Formata o valor conforme configuração do relatório
                $value = $this->getValorMascarado((object)$dados, $campos[$key]);
            }

            $processor->setValue($variable, trim($value));
        }

        $processor->saveAs($arquivoTemplate);

        $converter = new OfficeConverter($arquivoTemplate);
        $arquivoPdf = 'gerador_relatorio_' . time() . '.pdf';
        $converter->convertTo($arquivoPdf);

        unlink($arquivoTemplate);

        return [
            'path' => ECIDADE_REQUEST_PATH . "tmp/{$arquivoPdf}",
        ];
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function getTemplate()
    {
        $template = RelatorioTemplate::where('db15_db_relatorio', $this->codigoRelatorio)->first();
        if ($template->db15_estorage === null) {
            throw new \BusinessException('Template não possui vínculo com o e-Storage.', 400);
        }

        return StorageHelper::downloadArquivo($template->db15_estorage);
    }

    /**
     * @return array
     */
    private function getVariaveisGlobais()
    {
        $instituicao = DBConfig::find(session('DB_instit'));
        $date = Carbon::now();
        $dataAtualExtenso = $date->formatLocalized('%d de %B de %Y');

        return [
            'db_codigoinstit' => $instituicao->codigo,
            'db_nomeinstit' => $instituicao->nomeinst,
            'db_enderinst' => $instituicao->ender,
            'db_municinst' => $instituicao->munic,
            'db_ufinst' => $instituicao->uf,
            'db_foneinst' => $instituicao->telef,
            'db_emailinst' => $instituicao->email,
            'db_siteinst' => $instituicao->url,

            'db_id_usuario' => session('DB_id_usuario'),
            'db_login' => session('DB_login'),
            'db_coddepto' => session('DB_coddepto'),

            'db_anousu' =>  session('DB_anousu'),
            'db_datausu' => date('d/m/Y', session('DB_datausu')),
            'db_horausu' => date('H:i:s'),
            'db_data_atual_extenso' => $dataAtualExtenso
        ];
    }
}
