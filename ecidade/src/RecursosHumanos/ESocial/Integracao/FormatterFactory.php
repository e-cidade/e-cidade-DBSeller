<?php

namespace ECidade\RecursosHumanos\ESocial\Integracao;

use ECidade\RecursosHumanos\ESocial\Integracao\Formatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\AdmissaoPreliminarFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\AfastamentoTemporarioFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\AlteracaoContratualFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\AlteracaoDadosServidorFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\AvisoPrevioFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\CadastroBeneficiarioFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\CadastroBeneficioFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\AlteracaoBeneficioFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\CadastroBeneficiarioAltFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\BeneficioTerminoFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\CargoFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\ContribuinteFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\DesligamentoServidorFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\EFDExclusaoEventos;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\EFDFechamentoPeriodicosFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\EFDProcessos;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\EFDServicosTomadosFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\EmpregadorFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\EstabelecimentoFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\ExameToxicologicoMotoristaProfissionalEmpregadoFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\ExclusaoEventosFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\ExposicaoRiscoFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\FuncaoFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\HorarioFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\InformacoesComplementaresEventosPeriodicosFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\LotacaoTributariaFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\PagamentosRendimentosTrabalhoFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\ProcessoAdministrativoFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\ReaberturaEventosPeriodicosFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\ReintegracaoFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\RemuneracaoRGPSFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\RemuneracaoRPPSFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\RemuneracaoBeneficioEntePublicoFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\RubricaFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\ServidorFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\CessaoExercicioFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\TrabalhoIntermitenteFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\TSVEAlteracaoFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\TSVEInicialFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\TSVETerminoFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\TotalicacaoPagamentosContingenciaFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\CatFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\Formatter as FormatterStandart;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\MonitoramentoSaudeFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\ProcessoTrabalhistaFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\TributoTrabalhistaFormatter;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\ExclusaoEventosProcessosTrabalhistasFormatter;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use Exception;

/**
 * Class FormatterFactory
 * @package ECidade\RecursosHumanos\ESocial\Integracao
 */
class FormatterFactory
{

    /**
     * @param $tipo
     * @return ContribuinteFormatter|AdmissaoPreliminarFormatter|CargoFormatter|EmpregadorFormatter
     * |EstabelecimentoFormatter|ExclusaoEventosFormatter|Formatter\Formatter
     * |LotacaoTributariaFormatter|RubricaFormatter|AlteracaoContratualFormatter
     * @throws Exception
     */
    public static function get($tipo)
    {
        $path = ECIDADE_PATH . DS . 'src' . DS . 'RecursosHumanos' . DS . 'ESocial' . DS
            . 'Integracao' . DS . 'Formatter' . DS . 'Templates';

        switch ($tipo) {
            case Tipo::S1000:
                $formatter = new EmpregadorFormatter();
                $formatter->setDePara(require($path . DS . 'templateEmpregador.php'));
                break;
            case Tipo::S1005:
                $formatter = new EstabelecimentoFormatter();
                $formatter->setDePara(require($path . DS . 'templateEstabelecimentoObras.php'));
                break;
            case Tipo::S1010:
                $formatter = new RubricaFormatter();
                $formatter->setDePara(require($path . DS . 'templateRubrica.php'));
                break;
            case Tipo::S1020:
                $formatter = new LotacaoTributariaFormatter();
                $formatter->setDePara(require($path . DS . 'templateLotacaoTributaria.php'));
                break;
            case Tipo::S1030:
                $formatter = new CargoFormatter();
                $formatter->setDePara(require($path . DS . 'templateCargo.php'));
                break;
            case Tipo::S1200:
                $formatter = new RemuneracaoRGPSFormatter();
                $formatter->setDePara(require($path . DS . 'templateRemuneracaoRGPS.php'));
                break;
            case Tipo::S1202:
                $formatter = new RemuneracaoRPPSFormatter();
                break;
            case Tipo::S1207:
                $formatter = new RemuneracaoBeneficioEntePublicoFormatter();
                break;
            case Tipo::S1040:
                $formatter = new FuncaoFormatter();
                $formatter->setDePara(require($path . DS . 'templateFuncao.php'));
                break;
            case Tipo::S1050:
                $formatter = new HorarioFormatter();
                $formatter->setDePara(require($path . DS . 'templateHorario.php'));
                break;
            case Tipo::S1070:
                $formatter = new ProcessoAdministrativoFormatter();
                $formatter->setDePara(require($path . DS . 'templateProcesso.php'));
                break;
            case Tipo::S1210:
                $formatter = new PagamentosRendimentosTrabalhoFormatter();
                break;
            case Tipo::S1280:
                $formatter = new InformacoesComplementaresEventosPeriodicosFormatter();
                break;
            case Tipo::S1295:
                $formatter = new TotalicacaoPagamentosContingenciaFormatter();
                $formatter->setDePara(require($path . DS . 'templateTotalizacaoPagamentosContingencia.php'));
                break;
            case Tipo::S1298:
                $formatter = new ReaberturaEventosPeriodicosFormatter();
                break;
            case Tipo::S1299:
                $formatter = new Formatter\FechamentoEventosPeriodicos();
                $formatter->setDePara(require($path . DS . 'templateFechamentoEventosPeriodicos.php'));
                break;
            case Tipo::S2190:
                $formatter = new AdmissaoPreliminarFormatter();
                $formatter->setDePara(require($path . DS . 'templateAdmissaoPreliminar.php'));
                break;
            case Tipo::S2200:
                $formatter = new ServidorFormatter();
                break;
            case Tipo::S2205:
                $formatter = new AlteracaoDadosServidorFormatter();
                break;
            case Tipo::S2206:
                $formatter = new AlteracaoContratualFormatter();
                break;
            case Tipo::S2210:
                $formatter = new CatFormatter();
                $formatter->setDePara(require($path . DS . 'templateAcidenteTrabalho.php'));
                break;
            case Tipo::S2220:
                $formatter = new MonitoramentoSaudeFormatter();
                break;
            case Tipo::S2221:
                $formatter = new ExameToxicologicoMotoristaProfissionalEmpregadoFormatter();
                break;
            case Tipo::S2260:
                $formatter = new TrabalhoIntermitenteFormatter();
                $formatter->setDePara(require($path . DS . 'templateTrabalhoIntermitente.php'));
                break;
            case Tipo::S2230:
                $formatter = new AfastamentoTemporarioFormatter();
                $formatter->setDePara(require($path . DS . 'templateAfastamentoTemporario.php'));
                break;
            case Tipo::S2231:
                $formatter = new CessaoExercicioFormatter();
                break;
            case Tipo::S2240:
                $formatter = new ExposicaoRiscoFormatter();
                break;
            case Tipo::S2250:
                $formatter = new AvisoPrevioFormatter();
                $formatter->setDePara(require($path . DS . 'templateAvisoPrevio.php'));
                break;
            case Tipo::S2299:
                $formatter = new DesligamentoServidorFormatter();
                break;
            case Tipo::S2298:
                $formatter = new ReintegracaoFormatter();
                $formatter->setDePara(require($path . DS . 'templateReintegracao.php'));
                break;
            case Tipo::S2300:
                $formatter = new TSVEInicialFormatter();
                break;
            case Tipo::S2306:
                $formatter = new TSVEAlteracaoFormatter();
                break;
            case Tipo::S2399:
                $formatter = new TSVETerminoFormatter();
                break;
            case Tipo::S2400:
                $formatter = new CadastroBeneficiarioFormatter();
                break;
            case Tipo::S2405:
                $formatter = new CadastroBeneficiarioAltFormatter();
                break;
            case Tipo::S2410:
                $formatter = new CadastroBeneficioFormatter();
                break;
            case Tipo::S2416:
                $formatter = new AlteracaoBeneficioFormatter();
                break;
            case Tipo::S2420:
                $formatter = new BeneficioTerminoFormatter();
                break;
            case Tipo::S2500:
                $formatter = new ProcessoTrabalhistaFormatter();
                break;
            case Tipo::S2501:
                $formatter = new TributoTrabalhistaFormatter();
                break;
            case Tipo::S3000:
                $formatter = new ExclusaoEventosFormatter();
                $formatter->setDePara(require($path . DS . 'templateExclusaoEventos.php'));
                break;
            case Tipo::S3500:
                $formatter = new ExclusaoEventosProcessosTrabalhistasFormatter();
                break;
            case Tipo::R1000:
                $formatter = new ContribuinteFormatter();
                $formatter->setDePara(require($path . DS . 'templateContribuinte.php'));
                break;
            case Tipo::R1070:
                $formatter = new EFDProcessos();
                $formatter->setDePara(require($path . DS . 'templateEfdProcessos.php'));
                break;
            case Tipo::R2010:
                $formatter = new EFDServicosTomadosFormatter();
                $formatter->setDePara(require($path . DS . 'templateServicosTomados.php'));
                break;
            case Tipo::R9000:
                $formatter = new EFDExclusaoEventos();
                $formatter->setDePara(require($path . DS . 'templateEfdExclusaoEventos.php'));
                break;
            case Tipo::R2020:
                $formatter = new Formatter\EFDServicosPrestados();
                $formatter->setDePara(require($path . DS . 'templateEfdServicosPrestados.php'));
                break;
            case Tipo::R2099:
                $formatter = new EFDFechamentoPeriodicosFormatter();
                $formatter->setDePara(require($path . DS . 'templateEfdFechamentoPeriodicos.php'));
                break;
            case Tipo::R2055:
            case Tipo::R4010:
            case Tipo::R4020:
            case Tipo::R4040:
            case Tipo::R4099:
                $formatter = new FormatterStandart();
                break;
            default:
                throw new Exception('Tipo de fomulário não encontrado.');
        }

        return $formatter;
    }
}
