<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Services;

use App\Domain\Configuracao\GeradorRelatorio\Models\Relatorio;

class ImportarExportarRelatorioService
{
    /**
     * @param array $params
     * @return void
     * @throws \Exception
     */
    public function importar(array $params)
    {
        $dom = new \DOMDocument();
        if (!$dom->load($params['arquivo']->path())) {
            throw new \Exception('Ocorreu um erro ao abrir o arquivo XML.', 400);
        }

        \dbGeradorRelatorio::importar($dom, $params['grupo'], $params['tipo']);
    }

    public function exportar(Relatorio $relatorio)
    {
        require_once modification('model/dbPropriedadeRelatorio.php');
        require_once modification('model/dbVariaveisRelatorio.php');
        require_once modification('model/dbColunaRelatorio.php');
        $xml = new \dbGeradorRelatorio($relatorio->db63_sequencial);

        $name = str_replace(' ', '-', strtolower($relatorio->db63_nomerelatorio)) . '.xml';
        $name = \DBString::removerAcentuacao($name);

        return [
            'name' => $name,
            'path' => ECIDADE_REQUEST_PATH . $xml->exportar()
        ];
    }
}
