<?php
/**
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

namespace ECidade\RecursosHumanos\ESocial\Repository;

use ECidade\RecursosHumanos\Pessoal\Service\PensaoAlimenticiaService;

use BaseClassRepository;
use BusinessException;
use DBCompetencia;
use ECidade\RecursosHumanos\ESocial\Repository\MonitoramentoSaude;
use ECidade\RecursosHumanos\ESocial\DadosESocial;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ECidade\RecursosHumanos\ESocial\Repository\CadastroBeneficio;
use ServidorRepository;

/**
 * Class Referencia
 * @package ECidade\RecursosHumanos\ESocial\Repository
 */
class Referencia extends BaseClassRepository
{
    protected static $oInstance;

    private $ano;
    private $anoCaixa;
    private $mes;
    private $mesCaixa;
    private $matriculas;
    private $selecao;
    private $layout;
    private $referencias;
    private $competencia;
    private $cgmEmpregador;
    private $indicativoApuracao;

    public function __construct()
    {
    }

    public function setAno($ano)
    {
        $this->ano = $ano;
    }

    public function getAno()
    {
        return $this->ano;
    }

    public function setAnoCaixa($anoCaixa)
    {
        $this->anoCaixa = $anoCaixa;
    }

    public function getAnoCaixa()
    {
        return $this->anoCaixa;
    }

    public function setMes($mes)
    {
        $this->mes = $mes;
    }

    public function getMes()
    {
        return $this->mes;
    }

    public function setMesCaixa($mesCaixa)
    {
        $this->mesCaixa = $mesCaixa;
    }

    public function getMesCaixa()
    {
        return $this->mesCaixa;
    }

    public function setMatriculas($matriculas)
    {
        $this->matriculas = $matriculas;
    }

    public function getMatriculas()
    {
        return $this->matriculas;
    }

    public function setSelecao($selecao)
    {
        $this->selecao = $selecao;
    }

    public function getSelecaoo()
    {
        return $this->selecao;
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function getLayout()
    {
        return $this->layout;
    }

    public function setCompetencia($competencia)
    {
        $this->competencia = $competencia;
    }

    public function getCompetencia()
    {
        return $this->competencia;
    }

    public function getReferencias()
    {
        return $this->referencias;
    }

    public function setCgmEmpregador($cgmEmpregador)
    {
        $this->cgmEmpregador = $cgmEmpregador;
    }

    public function getCgmEmpregador()
    {
        return $this->cgmEmpregador;
    }

    public function setIndicativoApuracao($indicativoApuracao)
    {
        $this->indicativoApuracao = $indicativoApuracao;
    }

    public function getIndicativoApuracao()
    {
        return $this->indicativoApuracao;
    }

    public function buscarDados()
    {
        switch ($this->layout) {
            case '1200':
                $this->dadosS1200();
                break;
            case '1202':
                $this->dadosS1202();
                break;
            case '1210':
                $this->dadosS1210();
                break;
            case '2220':
                $this->dadosS2220();
                break;
            case '2230':
                $this->dadosS2230();
                break;
            case '2410':
                $this->dadosS2410();
                break;
        }
        return $this->referencias;
    }

    private function dadosS1200()
    {
        if (empty($this->ano) && empty($this->mes)) {
            throw new BusinessException("Competência não informada.");
        }

        if (empty($this->ano)) {
            throw new BusinessException("Ano não informado.");
        }

        if (empty($this->mes)) {
            throw new BusinessException("Mês não informado.");
        }
        
        $this->competencia = "{$this->ano}{$this->mes}";
        if (empty($this->selecao) && empty($this->matriculas)) {
            return $this->referencias;
        }
        if (!empty($this->selecao)) {
            $servidores = \ServidorRepository::getServidoresBySelecao($this->ano, $this->mes, $this->selecao);
            if (sizeof($servidores) == 0) {
                $msg = "Nenhum servidor encontrado na competência {$this->mes}/{$this->ano} para a seleção informada.";
                throw new BusinessException($msg);
            }
            foreach ($servidores as &$servidor) {
                if (!empty($this->indicativoApuracao)) {
                    $this->referencias[] = $servidor->getCodigoCgm() . $this->competencia . $this->indicativoApuracao;
                } else {
                    $this->referencias[] = $servidor->getCodigoCgm() . $this->competencia . "1"; // mensal
                    $this->referencias[] = $servidor->getCodigoCgm() . $this->competencia . "2"; // anual
                }
                unset($servidor);
            }
        }

        if (!empty($this->matriculas)) {
            $servidores = \ServidorRepository::getServidoresByMatriculas($this->ano, $this->mes, $this->matriculas);
            if (sizeof($servidores) == 0) {
                $msg = "Nenhum servidor encontrado na competência {$this->mes}/{$this->ano}";
                $msg .= " para as matrículas selecionadas.";
                throw new BusinessException($msg);
            }
            $this->matriculas = [];
            foreach ($servidores as &$servidor) {
                if (!empty($this->indicativoApuracao)) {
                    $this->referencias[] = $servidor->getCodigoCgm() . $this->competencia . $this->indicativoApuracao;
                } else {
                    $this->referencias[] = $servidor->getCodigoCgm() . $this->competencia . "1"; // mensal
                    $this->referencias[] = $servidor->getCodigoCgm() . $this->competencia . "2"; // anual
                }
                unset($servidor);
            }
        }
        if (!empty($this->indicativoApuracao)) {
            $this->competencia = "{$this->ano}{$this->mes}{$this->indicativoApuracao}";
        }
    }

    private function dadosS1202()
    {
        if (empty($this->ano) && empty($this->mes)) {
            throw new BusinessException("Competência não informada.");
        }

        if (empty($this->ano)) {
            throw new BusinessException("Ano não informado.");
        }

        if (empty($this->mes)) {
            throw new BusinessException("Mês não informado.");
        }

        $this->competencia = "{$this->ano}{$this->mes}";

        if (!empty($this->selecao)) {
            $servidores = \ServidorRepository::getServidoresBySelecao($this->ano, $this->mes, $this->selecao);
            if (sizeof($servidores) == 0) {
                $msg = "Nenhum servidor encontrado na competência {$this->mes}/{$this->ano} para a seleção informada.";
                throw new BusinessException($msg);
            }
            foreach ($servidores as &$servidor) {
                if (!empty($this->indicativoApuracao)) {
                    $this->referencias[] = $servidor->getCodigoCgm() . "-" . $this->competencia . "-"
                        . $this->indicativoApuracao;
                } else {
                    $this->referencias[] = $servidor->getCodigoCgm() . "-" . $this->competencia . "-" . "1"; // mensal
                    $this->referencias[] = $servidor->getCodigoCgm() . "-" . $this->competencia . "-" . "2"; // anual
                }
                unset($servidor);
            }
        }

        if (!empty($this->matriculas)) {
            $servidores = \ServidorRepository::getServidoresByMatriculas($this->ano, $this->mes, $this->matriculas);
            if (sizeof($servidores) == 0) {
                $msg = "Nenhum servidor encontrado na competência {$this->mes}/{$this->ano}";
                $msg .= " para as matrículas selecionadas.";
                throw new BusinessException($msg);
            }
            $this->matriculas = [];
            foreach ($servidores as &$servidor) {
                if (!empty($this->indicativoApuracao)) {
                    $this->referencias[] = $servidor->getCodigoCgm() . "-" . $this->competencia . "-"
                        . $this->indicativoApuracao;
                } else {
                    $this->referencias[] = $servidor->getCodigoCgm() . "-" . $this->competencia . "-" . "1"; // mensal
                    $this->referencias[] = $servidor->getCodigoCgm() . "-" . $this->competencia . "-" . "2"; // anual
                }
                unset($servidor);
            }
        }
        if (!empty($this->indicativoApuracao)) {
            $this->competencia = "{$this->ano}{$this->mes}-{$this->indicativoApuracao}";
        }
    }

    private function dadosS1210()
    {
        if (empty($this->ano) && empty($this->mes)) {
            throw new BusinessException("Competência não informada.");
        }

        if (empty($this->ano)) {
            throw new BusinessException("Ano da competência não informada.");
        }

        if (empty($this->mes)) {
            throw new BusinessException("Mês da competência não informada.");
        }

        $this->competencia = "{$this->ano}{$this->mes}";
        if (empty($this->selecao) && empty($this->matriculas)) {
            return $this->referencias;
        }
        if (!empty($this->selecao)) {
            $servidores = \ServidorRepository::getServidoresBySelecao($this->ano, $this->mes, $this->selecao);
            if (sizeof($servidores) == 0) {
                $msg = "Nenhum servidor encontrado na competência {$this->mes}/{$this->ano} para a seleção informada.";
                throw new BusinessException($msg);
            }
            foreach ($servidores as &$servidor) {
                $this->referencias[] = $servidor->getCodigoCgm() . "_" . $this->competencia;
                unset($servidor);
            }
        }

        if (!empty($this->matriculas)) {
            $servidores = \ServidorRepository::getServidoresByMatriculas($this->ano, $this->mes, $this->matriculas);
            if (sizeof($servidores) == 0) {
                $msg = "Nenhum servidor encontrado na competência {$this->mes}/{$this->ano}";
                $msg .= " para as matrículas selecionadas.";
                throw new BusinessException($msg);
            }
            $this->matriculas = [];
            foreach ($servidores as &$servidor) {
                $this->referencias[] = $servidor->getCodigoCgm() . "_" . $this->competencia;
                unset($servidor);
            }
        }
    }

    private function dadosS2220()
    {
        if (empty($this->ano) && empty($this->mes)) {
            throw new BusinessException("Competência não informada.");
        }

        $competencia = new DBCompetencia($this->ano, $this->mes);
        if (!empty($this->selecao)) {
            $servidores = \ServidorRepository::getServidoresBySelecao($this->ano, $this->mes, $this->selecao);
            if (sizeof($servidores) == 0) {
                $msg = "Nenhum servidor encontrado na competência {$this->mes}/{$this->ano} para a seleção informada.";
                throw new BusinessException($msg);
            }
            $servidores = array_values($servidores);
            $monitoramentoSaude = MonitoramentoSaude::buscarTodosAssentamentosCompetencia($competencia, $servidores);
            if (sizeof($monitoramentoSaude) == 0) {
                $msg = "Nenhum servidor encontrado na competência {$this->mes}/{$this->ano} para a seleção informada.";
                throw new BusinessException($msg);
            }

            foreach ($monitoramentoSaude as $monitoramento) {
                $this->referencias[] = $monitoramento->getCodigo();
            }
            $this->competencia = "";
            return $this->referencias;
        }

        if (!empty($this->matriculas)) {
            $servidores = \ServidorRepository::getServidoresByMatriculas($this->ano, $this->mes, $this->matriculas);
            if (sizeof($servidores) == 0) {
                $msg = "Nenhum servidor encontrado na competência {$this->mes}/{$this->ano}";
                $msg .= " para as matrículas selecionadas.";
                throw new BusinessException($msg);
            }
            $servidores = array_values($servidores);
            $monitoramentoSaude = MonitoramentoSaude::buscarTodosAssentamentosCompetencia($competencia, $servidores);
            if (sizeof($monitoramentoSaude) == 0) {
                $msg = "Nenhum servidor encontrado na competência {$this->mes}/{$this->ano}";
                $msg .= " para as matrículas selecionadas.";
                throw new BusinessException($msg);
            }

            foreach ($monitoramentoSaude as $monitoramento) {
                $this->referencias[] = $monitoramento->getCodigo();
            }
            $this->competencia = "";
            return $this->referencias;
        }

        $monitoramentoSaude = MonitoramentoSaude::buscarTodosAssentamentosCompetencia($competencia);
        if (sizeof($monitoramentoSaude) == 0) {
            $msg = "Nenhum servidor encontrado na competência {$this->mes}/{$this->ano}.";
            throw new BusinessException($msg);
        }
        foreach ($monitoramentoSaude as $monitoramento) {
            $this->referencias[] = $monitoramento->getCodigo();
        }
        $this->competencia = "";
        return $this->referencias;
    }

    private function dadosS2230()
    {
        if (empty($this->ano) && empty($this->mes)) {
            throw new BusinessException("Competência não informada.");
        }

        $matriculas = [];

        if (!empty($this->matriculas)) {
            $matriculas = $this->matriculas;
        }

        if (!empty($this->selecao)) {
            $servidores = \ServidorRepository::getServidoresBySelecao($this->ano, $this->mes, $this->selecao);
            if (sizeof($servidores) == 0) {
                $msg = "Nenhum servidor encontrado na competência {$this->mes}/{$this->ano} para a seleção informada.";
                throw new BusinessException($msg);
            }
            foreach ($servidores as &$servidor) {
                $matriculas[] = $servidor->getMatricula();
                unset($servidor);
            }
        }

        $where = DadosESocial::buscaCondicaoResponsavelPreenchimento(
            Tipo::AFASTAMENTO_TEMPORARIO,
            $matriculas,
            (object)[
                "ano" => $this->ano,
                "mes" => $this->mes
            ],
            null,
            null,
            null,
            $this->cgmEmpregador
        );

        $dados = str_replace(" AND rh213_responsavelpreenchimento in (", "", $where);
        $dados = str_replace(")", "", $dados);
        $dados = str_replace("'", "", $dados);
        $dados = explode(",", $dados);
        $this->competencia = "";

        return $this->referencias = $dados;
    }

    private function dadosS2410()
    {
        if (empty($this->ano) && empty($this->mes)) {
            throw new BusinessException("Competência não informada.");
        }

        $competencia =  new DBCompetencia($this->ano, $this->mes);

        $servidores = null;

        if (!empty($this->matriculas)) {
            $servidores = ServidorRepository::getServidoresByMatriculas($this->ano, $this->mes, $this->matriculas);

            if (empty($servidores)) {
                throw new BusinessException("Nenhuma matrícula encontrada.");
            }
        }

        $beneficios = CadastroBeneficio::buscarBeneficios(
            $competencia,
            $servidores,
            $this->selecao
        );

        if (empty($beneficios)) {
            throw new BusinessException("Nenhuma informação encontrada para os filtros informados.");
        }

        foreach ($beneficios as &$servidor) {
            $this->referencias[] = $servidor->getMatricula();
            unset($servidor);
        }
        return $this->referencias;
    }
}
