<?php

namespace App\Domain\Financeiro\Planejamento\Services\Relatorios;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Planejamento\Relatorios\LOA\AnexoTresPdf;

class AnexoTresReceitaService extends ReceitaService
{
    public function __construct($filtros)
    {
        $this->filtros = $filtros;
        $this->processarFiltros();
    }

    public function emitirPdf()
    {
        $this->processar();

        $relatorio = new AnexoTresPdf();
        $relatorio->setDados($this->dados);
        return $relatorio->emitir();
    }

    public function processaProjecao()
    {
        $projecao = $this->buscarProjecao();
        return $this->montaArvoreEstrutural($projecao);
    }

    protected function processar()
    {
        $projecao = $this->processaProjecao();
        $arvore = [];
        foreach ($projecao as $dado) {
            if (array_key_exists($dado->fonte, $arvore)) {
                continue;
            }
            $arvore[$dado->fonte] = $dado;
        }

        $this->organizaDados($arvore);
    }

    protected function organizaDados(array $dados)
    {
        parent::organizaDados($dados);
        $this->dados['ementario'] = $this->ementario;
        $this->dados['instituicaoEmissora'] = DBConfig::find($_SESSION['DB_instit']);
    }
}
