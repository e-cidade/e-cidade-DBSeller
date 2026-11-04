<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Services;

use App\Domain\Configuracao\GeradorRelatorio\Adapters\JsonXmlAdapter;
use App\Domain\Configuracao\GeradorRelatorio\Adapters\XmlJsonAdapter;
use App\Domain\Configuracao\GeradorRelatorio\Enums\TipoVisualizacaoRelatorioEnum;
use App\Domain\Configuracao\GeradorRelatorio\Models\Relatorio;
use App\Domain\Configuracao\GeradorRelatorio\Models\RelatorioDepartamento;
use App\Domain\Configuracao\GeradorRelatorio\Models\RelatorioTemplate;
use App\Domain\Configuracao\GeradorRelatorio\Models\RelatorioUsuario;
use App\Domain\Configuracao\Helpers\StorageHelper;
use Illuminate\Http\UploadedFile;

class RelatorioService
{
    /**
     * @param object $param
     * @param Relatorio|null $relatorio
     * @return Relatorio
     * @throws \Exception
     */
    public function salvar($param, $relatorio = null)
    {
        if (!$relatorio instanceof Relatorio) {
            $relatorio = new Relatorio();
        }

        $xmlAdapter = new JsonXmlAdapter($param);

        $relatorio->db63_db_tiporelatorio = $param->tipo;
        $relatorio->db63_db_gruporelatorio = $param->grupo;
        $relatorio->db63_db_relatorioorigem = $param->origem;
        $relatorio->db63_nomerelatorio = $param->layout['nome'];
        $relatorio->db63_versao_xml = $param->layout['versao'];
        $relatorio->db63_xmlestruturarel = $xmlAdapter->getXml();

        $relatorio->save();

        if ($param->tipoVisualizacao == TipoVisualizacaoRelatorioEnum::USUARIO) {
            $relatorioUsuario = RelatorioUsuario::query()
                ->where('db09_db_relatorio', $relatorio->db63_sequencial)
                ->first();
            $relatorioUsuario = $relatorioUsuario ?: new RelatorioUsuario();

            $relatorioUsuario->db09_db_relatorio = $relatorio->db63_sequencial;
            $relatorioUsuario->db09_db_usuarios = session('DB_id_usuario');

            $relatorioUsuario->save();
        }

        if ($param->tipoVisualizacao == TipoVisualizacaoRelatorioEnum::DEPARTAMENTO) {
            $relatorioDepartamento = RelatorioDepartamento::query()
                ->where('db07_db_relatorio', $relatorio->db63_sequencial)
                ->first();
            $relatorioDepartamento = $relatorioDepartamento ?: new RelatorioDepartamento();

            $relatorioDepartamento->db07_db_relatorio = $relatorio->db63_sequencial;
            $relatorioDepartamento->db07_db_depart = session('DB_id_usuario');

            $relatorioDepartamento->save();
        }

        if ($relatorio->db63_db_tiporelatorio != 2) {
            $this->deleteTemplateIfExists($relatorio);
        }

        return $relatorio;
    }

    /**
     * @param Relatorio $relatorio
     * @return void
     * @throws \Exception
     */
    public function apagar(Relatorio $relatorio)
    {
        $relatorio->template()->delete();
        $relatorio->relatorioUsuario()->delete();
        $relatorio->relatorioDepartamento()->delete();
        $relatorio->delete();
    }

    /**
     * @param Relatorio $relatorio
     * @return array
     * @throws \Exception
     */
    public function load(Relatorio $relatorio)
    {
        $adapter = new XmlJsonAdapter();
        $adapter->setRelatorio($relatorio);

        return $adapter->getJson();
    }

    /**
     * @param string $sql
     * @param string $view
     * @return array
     * @throws \Exception
     */
    public function build($sql = '', $view = '')
    {
        $adapter = new XmlJsonAdapter();
        $adapter->setSql($sql);
        $adapter->setView($view);

        return $adapter->getJson();
    }

    /**
     * @param Relatorio $relatorio
     * @param UploadedFile $template
     * @return void
     * @throws \Exception
     */
    public function importarTemplate(Relatorio $relatorio, UploadedFile $template)
    {
        $model = RelatorioTemplate::query()
            ->where('db15_db_relatorio', $relatorio->db63_sequencial)
            ->first() ?: new RelatorioTemplate();

        if ($model->db15_estorage) {
            StorageHelper::deleteArquivo($model->db15_estorage);
        }

        $fileName = 'gerador_relatorio_template_' . time() . '.docx';
        $id = StorageHelper::uploadArquivo($template->path(), [], true, null, null, $fileName);

        $model->db15_db_relatorio = $relatorio->db63_sequencial;
        $model->db15_documento = null;
        $model->db15_extensao_arquivo = 'docx';
        $model->db15_estorage = $id;
        $model->save();
    }

    /**
     * @param Relatorio $relatorio
     * @return void
     * @throws \Exception
     */
    private function deleteTemplateIfExists(Relatorio $relatorio)
    {
        $model = RelatorioTemplate::query()
            ->where('db15_db_relatorio', $relatorio->db63_sequencial)
            ->first();

        if (!$model instanceof RelatorioTemplate) {
            return;
        }

        if ($model->db15_estorage) {
            StorageHelper::deleteArquivo($model->db15_estorage);
        }

        $model->delete();
    }
}
