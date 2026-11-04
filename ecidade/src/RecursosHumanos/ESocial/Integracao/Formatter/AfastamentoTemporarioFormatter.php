<?php
namespace ECidade\RecursosHumanos\ESocial\Integracao\Formatter;

use DBDate;
use ECidade\Integracao\Sped\Common\Configuracao\ConfiguracaoFactory;
use ECidade\RecursosHumanos\ESocial\Factory\Mapeador;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ECidade\RecursosHumanos\ESocial\Repository\FormularioRepository;
use ECidade\RecursosHumanos\ESocial\Repository\TrabalhadorSemVinculoInicio;

/**
 * Class AfastamentoTemporarioFormatter
 * @package ECidade\RecursosHumanos\ESocial\Integracao\Formatter
 */
class AfastamentoTemporarioFormatter extends Formatter
{
    /**
     * Array que faz o dePara entre os códigos do e-cidade e do eSocial para Regime de Trabalho
     * Chave: código e-cidade
     * Valor: código eSocial
     *
     * @var array
     */
    private $deParaRegimeTrabalho = [
        1 => 2,
        2 => 1,
        3 => 2
    ];

    /**
     * @ Pega a instância do servidor
     */
    private $servidor;
    /**
     * Realiza a formatação dos dados para envio da API
     *
     * @param array $dados
     * @return array
     */
    public function formatar($dados)
    {
        $dadosFormatado = parent::formatar($dados);
        return $this->posProcessamento($dadosFormatado);
    }

    /**
     * Realiza uma consistencia nos dados enviados
     *
     * @param array  $dadosFormatado
     * @return array
     */
    private function posProcessamento($dadosFormatado)
    {
        $grupoPerAquis = true;

        foreach ($dadosFormatado as $dadoFormatado) {
            if (isset($dadoFormatado->ideVinculo->nisTrab)) {
                unset($dadoFormatado->ideVinculo->nisTrab);
            }
            $matricula = $dadoFormatado->ideVinculo->matricula;
            //Variavel de controle de estagiario
            $estagiario = false;
            if (!isset($dadoFormatado->ideVinculo->codCateg)) {
                unset($dadoFormatado->ideVinculo->codCateg);
            }

            $dadoFormatado->infoAfastamento->iniAfastamento->codMotAfast = str_pad(
                $dadoFormatado->infoAfastamento->iniAfastamento->codMotAfast,
                2,
                0,
                STR_PAD_LEFT
            );

            if (isset($dadoFormatado->infoAfastamento->iniAfastamento->tpAcidTransito)) {
                $dadoFormatado->infoAfastamento->iniAfastamento->tpAcidTransito =
                    (int)$dadoFormatado->infoAfastamento->iniAfastamento->tpAcidTransito;
            }
            if (!empty($dadoFormatado->ideVinculo->matricula)) {
                $this->servidor = \ServidorRepository::getInstanciaByCodigo($dadoFormatado->ideVinculo->matricula);
                // Validamos o servidor e se ele nao possui vinculo Empregaticio
                if ($this->servidor->isEstagiario()) {
                    $estagiario = true;
                }

                if ($this->servidor->getMatriculaAnterior() != "") {
                    $dadoFormatado->ideVinculo->matricula = $this->servidor->getMatriculaAnterior();
                }

                //Codigo afastamento igual a 15
                //Código Categoria validar Array 1XX, 301, 302, 303, 304, 306, 307, 309, 310, 312, 410
                $codigoCategoria = $this->servidor->getVinculo()->getCodigoCategoria();
                $substrOne = substr($codigoCategoria, 0, 1);
                if ($dadoFormatado->infoAfastamento->iniAfastamento->codMotAfast != '15') {
                    $grupoPerAquis = false;
                } else {
                    $tpRegTrab = $this->deParaRegimeTrabalho[$this->servidor->getTipoRegime()];
                    /**
                     * Foi adicionado o else e alterada a logica, seguindo exatamente o manual, para evitar bugs
                     * Antes a logica era sem o else para economia de linhas de codigo, porem estava frequente os bugs
                     * e o entendimento da logica era mais complexo por ser logica invertida (complementar)
                     */
                    $validaTipoRegimeOrigem = false;

                    if ($this->servidor->getTipoRegimeOrigem() == '1') {
                        $validaTipoRegimeOrigem = true;
                    }

                    if (((($this->servidor->validaCategoriaPeriodoAquisitivo() || $substrOne == '1') &&
                        $tpRegTrab == '1') || ($codigoCategoria == '401' &&
                        $tpRegTrab == '1')) || ($validaTipoRegimeOrigem)) {
                        $grupoPerAquis = true;
                    } else {
                        $grupoPerAquis = false;
                    }
                }
            }
            if (!$grupoPerAquis) {
                unset($dadoFormatado->infoAfastamento->iniAfastamento->perAquis);
            }

            if (isset($dadoFormatado->infoAfastamento->iniAfastamento->perAquis)) {
                if (isset($dadoFormatado->infoAfastamento->iniAfastamento->perAquis->dtInicio)
                    && !empty($dadoFormatado->infoAfastamento->iniAfastamento->perAquis->dtInicio)
                ) {
                    $dadoFormatado->infoAfastamento->iniAfastamento->perAquis->dtInicio = \DBDate::format(
                        $dadoFormatado->infoAfastamento->iniAfastamento->perAquis->dtInicio,
                        \DBDate::DATA_EN
                    );
                    if (isset($dadoFormatado->infoAfastamento->iniAfastamento->perAquis->dtFim)
                        && !empty($dadoFormatado->infoAfastamento->iniAfastamento->perAquis->dtFim)) {
                        $dadoFormatado->infoAfastamento->iniAfastamento->perAquis->dtFim = \DBDate::format(
                            $dadoFormatado->infoAfastamento->iniAfastamento->perAquis->dtFim,
                            \DBDate::DATA_EN
                        );
                    }
                } else {
                    unset($dadoFormatado->infoAfastamento->iniAfastamento->perAquis);
                }
            }

            if ($estagiario && isset($dadoFormatado->infoAfastamento->iniAfastamento->perAquis)) {
                unset($dadoFormatado->infoAfastamento->iniAfastamento->perAquis);
            }
            if (!isset($dadoFormatado->infoAfastamento->fimAfastamento->dtTermAfast)) {
                unset($dadoFormatado->infoAfastamento->fimAfastamento);
            }
            // Removido dados da retificação quando não houver a origem da mesma.
            if (!isset($dadoFormatado->infoAfastamento->infoRetif->origRetif)) {
                unset($dadoFormatado->infoAfastamento->infoRetif);
            }

            // Removido os dados do atestado caso o motivo do afastamento for diferente de:
            // 1 - Acidente/Doença do trabalho e 3 - Acidente/Doença não relacionada ao trabalho
            if (!in_array($dadoFormatado->infoAfastamento->iniAfastamento->codMotAfast, array('01', '03'))) {
                unset($dadoFormatado->infoAfastamento->iniAfastamento->infoAtestado);
            }
            // Removido dados do Mandato Sindical quando motivo do afastamento for diferente de:
            // 24 - Mandato Sindical - Afastamento temporário para exercício de mandato sindical
            if ($dadoFormatado->infoAfastamento->iniAfastamento->codMotAfast != 24) {
                unset($dadoFormatado->infoAfastamento->iniAfastamento->infoMandSind);
            }

            // Removido dados da Cessão quando motivo do afastamento for diferente de:
            // 14 - Cessão / Requisição
            if ($dadoFormatado->infoAfastamento->iniAfastamento->codMotAfast != 14) {
                unset($dadoFormatado->infoAfastamento->iniAfastamento->infoCessao);
            }
            // Solucao temporaria para envio de formularios da clin
            if (empty($dadoFormatado->infoAfastamento->fimAfastamento->dtTermAfast)) {
                unset($dadoFormatado->infoAfastamento->fimAfastamento);
            }
            //VERIFICA SE O GRUPO ESTA PREENCHIDO
            if (empty($dadoFormatado->infoAfastamento->infoMandElet)) {
                unset($dadoFormatado->infoAfastamento->infoMandElet);
            } else {
                // CNPJ DO ORGÃO QUE O TRABALHADOR VAI EXERCER O CARGO
                if (empty($dadoFormatado->infoAfastamento->infoMandElet->cnpjMandElet)) {
                    unset($dadoFormatado->infoAfastamento->infoMandElet->cnpjMandElet);
                }
                //Indicar se o servidor optou pela remuneração do cargo efetivo.
                if (empty($dadoFormatado->infoAfastamento->infoMandElet->indRemunCargo)) {
                    unset($dadoFormatado->infoAfastamento->infoMandElet->indRemunCargo);
                } else {
                    //VALIDAÇÃO EXCLUSIVA PARA O CODIGO CATEGORIA 301
                    if (!empty($dadoFormatado->ideVinculo->matricula)) {
                        $servidor = \ServidorRepository::getInstanciaByCodigo($matricula);
                        $codCategoria = $servidor->getVinculo()->getCodigoCategoria();
                        if ($codCategoria !== 301) {
                            unset($dadoFormatado->infoAfastamento->infoMandElet->indRemunCargo);
                        }
                    }
                }
                if (empty($dadoFormatado->infoAfastamento->infoMandElet->cnpjMandElet) &&
                    empty($dadoFormatado->infoAfastamento->infoMandElet->indRemunCargo)) {
                    unset($dadoFormatado->infoAfastamento->infoMandElet);
                }
            }
            /**
             * Verifica se o servidor é TSE - Trabalhador Sem Vinculo
             * Caso seja TSE
             */
            if (isset($dadoFormatado->ideVinculo->matricula) &&
                !empty($dadoFormatado->ideVinculo->matricula)) {
                if (!$this->validaEnvioMatricula($matricula, $this->getEmpregador())) {
                    unset($dadoFormatado->ideVinculo->matricula);
                }
            }

            if (!isset($dadoFormatado->ideVinculo->matricula)
                || (isset($dadoFormatado->ideVinculo->matricula) && empty($dadoFormatado->ideVinculo->matricula))
            ) {
                $servidor = \ServidorRepository::getInstanciaByCodigo($matricula);
                $dadoFormatado->ideVinculo->codCateg = (int) $servidor->getVinculo()->getCodigoCategoria();
            }

            if (isset($dadoFormatado->infoAfastamento->iniAfastamento->infoMandSind->cnpjSind) &&
            !empty($dadoFormatado->infoAfastamento->iniAfastamento->infoMandSind->cnpjSind)) {
                $cnpjSind = $dadoFormatado->infoAfastamento->iniAfastamento->infoMandSind->cnpjSind;
                $cnpjSind = preg_replace(
                    '\'[^0-9]\'',
                    '',
                    $cnpjSind
                );
                $dadoFormatado->infoAfastamento->iniAfastamento->infoMandSind->cnpjSind = $cnpjSind;
            }

            if (isset($dadoFormatado->infoAfastamento->iniAfastamento->infoCessao->cnpjCess) &&
            !empty($dadoFormatado->infoAfastamento->iniAfastamento->infoCessao->cnpjCess)) {
                $cnpjCess = $dadoFormatado->infoAfastamento->iniAfastamento->infoCessao->cnpjCess;
                $cnpjCess = preg_replace(
                    '\'[^0-9]\'',
                    '',
                    $cnpjCess
                );
                $dadoFormatado->infoAfastamento->iniAfastamento->infoCessao->cnpjCess = $cnpjCess;
            }
            /**
             *  Validamos se o inicio do afastamento é anterior ao inicio de obrigatoriedade, nesse caso nao enviamos
             *   o periodo inicial do afastamento pois ele ja foi enviado no evendo S-2200/S2300
             */
            $dataObrigatoriedade = \DBPessoal::getDataFaseEsocial(2);
            $dataInicioAfastamento = new DBDate($dadoFormatado->infoAfastamento->iniAfastamento->dtIniAfast);
            if ($dataInicioAfastamento < $dataObrigatoriedade) {
                unset($dadoFormatado->infoAfastamento->iniAfastamento);
            }
        }

        return $dadosFormatado;
    }
}
