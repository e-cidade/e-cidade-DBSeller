<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */
namespace ECidade\RecursosHumanos\ESocial\Agendamento;

use App\Domain\RecursosHumanos\ESocial\Models\ExameToxicologico;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\AdmissaoPreliminar;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\AfastamentoTemporario;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\AlteracaoContratual;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\AlteraDadosCadastraisServidor;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\AvisoPrevio;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\CadastroBeneficiario;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\CadastroBeneficiarioAlt;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\CadastroBeneficio;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\AlteracaoBeneficio;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\CessaoExercicio;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\Cargo;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\ContribuicaoSindical;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\Contribuinte;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\DesligamentoServidor;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\EFDExclusaoEventos;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\EFDFechamentoPeriodicos;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\EFDProcesso;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\EFDReaberturaEventos;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\EFDServicosPrestados;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\EFDServicosTomados;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\EmpregadorObras;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\ExameToxicologicoMotoristaProfissionalEmpregado;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\ExclusaoEventos;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\FechamentoEventosPeriodicos;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\Funcao;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\Horario;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\InformacoesComplementaresEventosPeriodicos;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\LotacaoTributaria;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\MonitoramentoSaude;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\PagamentosRendimentosTrabalho;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\Processos;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\ReaberturaEventosPeriodicos;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\Reintegracao;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\RemuneracaoRGPS;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\RemuneracaoRPPS;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\RemuneracaoBeneficioEntePublico;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\BeneficioTermino;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\Rubrica;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\Servidor;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\TrabalhadorSemVinculo;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\TrabalhadorSemVinculoTermino;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\TrabalhoIntermitente;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\TotalicacaoPagamentosContingencia;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\Cat;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\EFDAquisicaoProducaoRural;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\EFDPagamentosCreditosNI;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\EFDPagamentosCreditosPF;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\EFDPagamentosCreditosPJ;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\EFDReaberturaFechR4000;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\ExposicaoRisco;

use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use InvalidArgumentException;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\EstabelecimentoObras;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\ExclusaoEventosProcessos;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\ProcessoTrabalhista;
use ECidade\RecursosHumanos\ESocial\Agendamento\Processamento\ProcessoTributoTrabalhista;

final class ProcessamentoStaticFactory
{
    public static function factory($tipo, $cgm, $instituicao, $layout, $ano = null, $mes = null)
    {
        switch ($tipo) {
            case Tipo::RUBRICA:
                return new Rubrica($cgm);
            case Tipo::LOTACAO_TRIBUTARIA:
                return new LotacaoTributaria($cgm);
            case Tipo::OBRAS:
                $empregadorObras = new EstabelecimentoObras($cgm);
                $empregadorObras->setLayout($layout);
                return $empregadorObras;
            case Tipo::EMPREGADOR:
                $empregadorObras = new EmpregadorObras($cgm);
                $empregadorObras->setLayout($layout);
                return $empregadorObras;
            case Tipo::PROCESSOS:
                return new Processos($cgm);
            case Tipo::CARGO:
                return new Cargo($cgm);
            case Tipo::ADMISSAO_PRELIMINAR:
                return new AdmissaoPreliminar($cgm);
            case Tipo::HORARIO:
                return new Horario($cgm);
            case Tipo::FUNCAO:
                return new Funcao($cgm);
            case Tipo::SERVIDOR:
                $servidor = new Servidor($cgm);
                $servidor->setInstituicao($instituicao);
                return $servidor;
            case TIPO::ALTERACAO_SERVIDOR:
                $servidor = new AlteraDadosCadastraisServidor($cgm);
                $servidor->setInstituicao($instituicao);
                return $servidor;
            case Tipo::TRABALHO_INTERMITENTE:
                return new TrabalhoIntermitente($cgm);
            case Tipo::AVISO_PREVIO:
                return new AvisoPrevio($cgm);
                break;
            case Tipo::CAT:
                return new Cat($cgm);
                break;
            case Tipo::MONITORAMENTO_SAUDE:
                $afastamentoTemporario = new MonitoramentoSaude($cgm);
                $afastamentoTemporario->setMes($mes);
                $afastamentoTemporario->setAno($ano);
                return $afastamentoTemporario;
                break;
            case Tipo::EXAME_TOXICOLOGICO_MOTORISTA_PROFISSIONAL_EMPREGADO:
                $exame = new ExameToxicologicoMotoristaProfissionalEmpregado($cgm);
                $exame->setInstituicao($instituicao);
                return $exame;
                break;
            case Tipo::AFASTAMENTO_TEMPORARIO:
                $afastamentoTemporario = new AfastamentoTemporario($cgm);
                $afastamentoTemporario->setMes($mes);
                $afastamentoTemporario->setAno($ano);
                return $afastamentoTemporario;
                break;
            case Tipo::CESSAO_EXERCICIO:
                $cessaoExercicio = new CessaoExercicio($cgm);
                $cessaoExercicio->setMes($mes);
                $cessaoExercicio->setAno($ano);
                return $cessaoExercicio;
                break;
            case Tipo::EXPOSICAO_RISCO:
                $exposicaoRisco = new ExposicaoRisco($cgm);
                $exposicaoRisco->setMes($mes);
                $exposicaoRisco->setAno($ano);
                return $exposicaoRisco;
                break;
            case Tipo::DESLIGAMENTO_SERVIDOR:
                return new DesligamentoServidor($cgm);
                break;
            case Tipo::EXCLUSAO_EVENTOS:
                return new ExclusaoEventos($cgm);
                break;
            case Tipo::TRABALHADOR_SEM_VINCULO:
                $trabalhador = new TrabalhadorSemVinculo($cgm);
                $trabalhador->setInstituicao($instituicao);
                return $trabalhador;
            case Tipo::ALTERACAO_TRABALHADOR_SEM_VINCULO:
                $trabalhador = new TrabalhadorSemVinculo($cgm);
                $trabalhador->setInstituicao($instituicao);
                $trabalhador->setAlteracao(true);
                return $trabalhador;
            case Tipo::REINTEGRACAO:
                return new Reintegracao($cgm);
                break;
            case Tipo::ALTERACAO_CONTRATUAL:
                return new AlteracaoContratual($cgm);
                break;
            case Tipo::TERMINO_TRABALHADOR_SEM_VINCULO:
                return new TrabalhadorSemVinculoTermino($cgm);
                break;
            case Tipo::CADASTRO_BENEFICIARIO:
                $cadastroBeneficiario = new CadastroBeneficiario($cgm);
                $cadastroBeneficiario->setMes($mes);
                $cadastroBeneficiario->setAno($ano);
                return $cadastroBeneficiario;
                break;
            case Tipo::CADASTRO_BENEFICIARIO_ALTERACAO:
                $cadastroBeneficiario = new CadastroBeneficiarioAlt($cgm);
                return $cadastroBeneficiario;
                break;
            case Tipo::CADASTRO_BENEFICIO:
                $cadastroBeneficio = new CadastroBeneficio($cgm);
                $cadastroBeneficio->setMes($mes);
                $cadastroBeneficio->setAno($ano);
                return $cadastroBeneficio;
                break;
            case Tipo::ALTERACAO_BENEFICIO:
                $cadastroBeneficio = new AlteracaoBeneficio($cgm);
                $cadastroBeneficio->setMes($mes);
                $cadastroBeneficio->setAno($ano);
                return $cadastroBeneficio;
                break;
            case Tipo::REMUNERACAO_RGPS:
                $oRemuneracao = new RemuneracaoRGPS($cgm);
                $oRemuneracao->setMes($mes);
                $oRemuneracao->setAno($ano);
                return $oRemuneracao;
            case Tipo::REMUNERACAO_RPPS:
                $oRemuneracao = new RemuneracaoRPPS($cgm);
                $oRemuneracao->setMes($mes);
                $oRemuneracao->setAno($ano);
                return $oRemuneracao;
            case Tipo::REMUNERACAO_BENEFICIOS_ENTES_PUBLICOS:
                $oRemuneracao = new RemuneracaoBeneficioEntePublico($cgm);
                $oRemuneracao->setMes($mes);
                $oRemuneracao->setAno($ano);
                return $oRemuneracao;
                break;
            case Tipo::BENEFICIO_TERMINO:
                $terminoBeneficio = new BeneficioTermino($cgm);
                $terminoBeneficio->setMes($mes);
                $terminoBeneficio->setAno($ano);
                return $terminoBeneficio;
                break;
            case Tipo::CONTRIBUINTE:
                return new Contribuinte($cgm);
                break;
            case Tipo::EFD_PROCESSOS:
                return new EFDProcesso($cgm);
                break;
            case Tipo::EFD_RETENCOES_SERVICOS_TOMADOS:
                return new EFDServicosTomados($cgm, $instituicao, $ano, $mes);
            case Tipo::EFD_EXCLUSAO_EVENTOS:
                return new EFDExclusaoEventos($cgm);
                break;
            case Tipo::EFD_SERVICOS_PRESTADOS:
                return new EFDServicosPrestados($cgm);
                break;
            case Tipo::EFD_AQUISICAO_PRODUCAO_RURAL:
                return new EFDAquisicaoProducaoRural($cgm, $instituicao, $ano, $mes);
                break;
            case Tipo::PAGAMENTOS_RENDIMENTOS_TRABALHO:
                $pagamentos =  new PagamentosRendimentosTrabalho($cgm);
                return $pagamentos;
            case Tipo::CONTRIBUICAO_SINDICAL_PATRONAL:
                return new ContribuicaoSindical($cgm);
                break;
            case Tipo::REABERTURA_EVENTOS_PERIODICOS:
                $reabertura = new ReaberturaEventosPeriodicos($cgm);
                $reabertura->setAno($ano);
                $reabertura->setMes($mes);
                return $reabertura;
            case Tipo::FECHAMENTO_EVENTOS_PERIODICOS:
                return new FechamentoEventosPeriodicos($cgm);
                break;
            case Tipo::INFORMACOES_COMPLEMENTARES_EVENTOS_PERIODICOS:
                $informacoesComplementares = new InformacoesComplementaresEventosPeriodicos($cgm);
                $informacoesComplementares->setAnoCompetencia($ano);
                $informacoesComplementares->setMesCompetencia($mes);
                return $informacoesComplementares;
                break;
            case Tipo::TOTALIZACAO_PAGAMENTOS_CONTINGENCIA:
                return new TotalicacaoPagamentosContingencia($cgm);
                break;
            case Tipo::EFD_FECHAMENTO_PERIODICOS:
                return new EFDFechamentoPeriodicos($cgm, $ano, $mes);
            case Tipo::EFD_REABERTURA_EVENTOS:
                return new EFDReaberturaEventos($cgm, $instituicao, $ano, $mes);
            case Tipo::EFD_PAGAMENTOS_CREDITOS_PF:
                return new EFDPagamentosCreditosPF($cgm, $instituicao, $ano, $mes);
            case Tipo::EFD_PAGAMENTOS_CREDITOS_PJ:
                return new EFDPagamentosCreditosPJ($cgm, $instituicao, $ano, $mes);
            case Tipo::EFD_PAGAMENTOS_CREDITOS_NI:
                return new EFDPagamentosCreditosNI($cgm, $instituicao, $ano, $mes);
            case TIPO::EFD_REABERTURA_FECH_R4000:
                return new EFDReaberturaFechR4000($cgm, $ano, $mes);
            case Tipo::PROCESSO_TRABALHISTA:
                $processoTrabalhista = new ProcessoTrabalhista($cgm);
                $processoTrabalhista->setAnoCompetencia($ano);
                $processoTrabalhista->setMesCompetencia($mes);
                return $processoTrabalhista;
                break;
            case Tipo::TRIBUTO_TRABALHISTA:
                $tributoTrabalhista = new ProcessoTributoTrabalhista($cgm);
                $tributoTrabalhista->setAnoCompetencia($ano);
                $tributoTrabalhista->setMesCompetencia($mes);
                return $tributoTrabalhista;
                break;
            case Tipo::EXCLUSAO_PROCESSO_TRABALHISTA:
                return new ExclusaoEventosProcessos($cgm);
                break;
            default:
                throw new InvalidArgumentException('Tipo de Formulário do eSocial Inválido');
        }
    }
}
