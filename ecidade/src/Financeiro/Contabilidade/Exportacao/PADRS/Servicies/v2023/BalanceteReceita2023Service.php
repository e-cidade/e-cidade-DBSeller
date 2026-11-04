<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2023;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\BalanceteReceitaPADService;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2023\BalanceteReceita2023Builder;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\PadService;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceitaPadrao;

class BalanceteReceita2023Service extends PadService
{
    protected $fileName = 'BAL_REC.TXT';

    private $deParaInstituicaoOrgaoUnidade = [];

    public function __construct($anousu, $instituicoes, $dataInicio, $dataFim)
    {
        $this->ano = $anousu;
        $this->instituicoes = $instituicoes;
        $this->dataInicial = $dataInicio;
        $this->dataFinal = $dataFim;
    }

    /**
     * @param $dado
     * @return void
     */
    protected function identificaOrgaoUnidade($dado)
    {
        if (!is_null($dado->instituicao)) {
            if (array_key_exists($dado->instituicao, $this->deParaInstituicaoOrgaoUnidade)) {
                return $this->deParaInstituicaoOrgaoUnidade[$dado->instituicao];
            }
            foreach ($this->instituicoes as $instituicao) {
                if ($instituicao->getCodigo() != $dado->instituicao) {
                    continue;
                }
                $this->deParaInstituicaoOrgaoUnidade[$dado->instituicao] = $instituicao->getCodigoTribunal();
            }

            return $this->deParaInstituicaoOrgaoUnidade[$dado->instituicao];
        }
        return null;
    }

    protected function getDados()
    {
        $codigos = array_map(function ($instituicao) {
            return $instituicao->getCodigo();
        }, $this->instituicoes);


        $filtros = [
            'natureza' => '',
            'nivelAgrupar' => 0,
            'apenasComMovimentacao' => 1,
            'ementario' => 'estadual',
            'dataInicio' => implode('/', array_reverse(explode('-', $this->dataInicial))),
            'dataFinal' => implode('/', array_reverse(explode('-', $this->dataFinal))),
        ];
        $service = new BalanceteReceitaPADService();
        $service->setModeloMgs($this->emissaoMGS);
        $service->setFiltrosRequest($filtros);
        $instituicoes = DBConfig::whereIn('codigo', $codigos)->get();
        $service->setInstituicoes($instituicoes);
        $dados = $service->processar();
        foreach ($dados as $dado) {
            $formater = new EstruturalReceitaPadrao($dado->natureza);
            $dado->orgao_unidade = $this->identificaOrgaoUnidade($dado);
            $dado->nivel = $formater->getNivel();

            $builder = $this->getBuilder();

            yield $builder->addDados((array)$dado)
                ->addModelo($this->emissaoMGS)
                ->build();
        }
    }

    protected function getBuilder()
    {
        return new BalanceteReceita2023Builder();
    }
}
