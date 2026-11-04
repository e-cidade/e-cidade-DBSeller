<?php
/**
 * Created by PhpStorm.
 * User: dbseller
 * Date: 11/12/18
 * Time: 16:21
 */

namespace ECidade\RecursosHumanos\ESocial\Agendamento\Processamento;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Integracoes\EFDReinf\Services\ConfiguracaoService;
use ECidade\RecursosHumanos\ESocial\Agendamento\Evento;
use ECidade\RecursosHumanos\ESocial\DadosESocial;
use ECidade\RecursosHumanos\ESocial\Integracao\FormatterFactory;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use stdClass;

class Contribuinte extends ProcessamentoAbstract implements ProcessamentoInterface
{
    private $cgm;

    public function __construct($cgm)
    {
        $config = db_getsession('DB_instit');
        $this->cgm = $cgm;
        $this->config = ConfiguracaoService::getInstance($config);
    }

    public function processar()
    {
        $cgm = $this->cgm;
        $alteracaoDados = false;

        $dadosEsocial = new DadosESocial();
        $dadosEsocial->setIntegracao(Tipo::EFD_REINF);
        $dadosEsocial->setReponsavelPeloPreenchimento($cgm);
        $dadosPreenchimento = $dadosEsocial->getPorTipo(Tipo::CONTRIBUINTE);

        $formatterContribuinte = FormatterFactory::get(Tipo::R1000);
        $dadosFormatadosContribuinte = $formatterContribuinte->formatar($dadosPreenchimento);

        $this->tratamentoPreProcessamento($dadosFormatadosContribuinte);

        foreach ($dadosFormatadosContribuinte as $dados) {
            $eventoFila = new Evento(Tipo::R1000, $cgm, $cgm, $dados);

            if ($eventoFila->adicionarFila()) {
                $alteracaoDados = true;
            }
        }

        return $alteracaoDados;
    }

    public function tratamentoPreProcessamento(array $dados)
    {
        foreach ($dados as $item) {
            $item->softhouse = new stdClass;

            // Ente Federativo Responsável - EFR
            $instituicao = DBConfig::where('numcgm', '=', $this->cgm)->orderBy('codigo')->first();

            if (in_array($instituicao->getTipoInstituicao(), [101, 2])) {
                if (!$instituicao->getEnteFederativoResp()) {
                    $item->infoefr->ideefr = 'N';
                    $item->infoefr->cnpjefr = $instituicao->db21_cnpj_efr;
                } else {
                    $item->infoefr->ideefr = 'S';
                    $item->infoefr->cnpjefr = null;
                }
            }

            // Informações da empresa desenvolvedora
            $dbseller = new stdClass;
            $dbseller->cnpjsofthouse = '05238851000190';
            $dbseller->nmrazao = 'DBSELLER SERVICOS DE INFORMATICA LTDA';
            $dbseller->nmcont = 'Suporte';
            $dbseller->telefone = '5130765101';
            $dbseller->email = 'suporte@dbseller.com.br';

            $item->softhouse = [$dbseller];

            // remover novavalidade quando não tiver preenchimento
            if ($item->novavalidade) {
                if ($item->novavalidade->inivalid == "" && $item->novavalidade->fimvalid == "") {
                    unset($item->novavalidade);
                }
            }
        }
    }
}
