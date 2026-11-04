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
 *  junto com este programa; se nao, escreva para a Free Softwareb
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace ECidade\RecursosHumanos\ESocial\Integracao;

use Assentamento;
use BusinessException;
use CgmFactory;
use DBException;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ECidade\RecursosHumanos\ESocial\Repository\ESocialEnvioRepository;
use ECidade\RecursosHumanos\ESocial\Repository\ServidorMatriculas;
use ECidade\RecursosHumanos\ESocial\Integracao\ESocialEnvioStatus;
use JSON;
use ParameterException;
use Servidor;
use stdClass;

/**
 * Class ESocialEnvio
 *
 * @package ECidade\RecursosHumanos\ESocial\Integracao
 */
class ESocialEnvio
{
    /**
     * @var
     */
    private $codigo;

    /**
     * @var \stdClass[]
     */
    private $ocorrencias;

    /**
     * @var \stdClass[]
     */
    private $recibos;
    /**
     * @var
     */
    private $evento;

    /**
     * @var string $descricao
     */
    private $descricao;

    /**
     * @var
     */
    private $empregador;
    /**
     * @var
     */
    private $responsavelPreenchimento;
    /**
     * @var
     */
    private $dados;
    /**
     * @var
     */
    private $md5;
    /**
     * @var
     */
    private $situacao;
    /**
     * @var string
     */
    private $situacaoSalva;
    /**
     * @var
     */
    private $data;

    /**
     * @var bool
     */
    private $permiteAtualizar;
    /**
     * @var bool
     */
    private $processadoSucesso;
    /**
     * @var bool
     */
    private $aguardandoProcessamento;

    /**
     * @var integer
     */
    private $codigoEnvio;

    /**
     * @var ESocialEnvioStatus
     */
    private $envioStatus;

    public function __construct($codigo = null)
    {
        if (!empty($codigo)) {
            $esocialEnvio = ESocialEnvioRepository::find($codigo);
            $this->codigo = $esocialEnvio->getCodigo();
            $this->evento = $esocialEnvio->getEvento();
            $this->empregador = $esocialEnvio->getEmpregador();
            $this->responsavelPreenchimento = $esocialEnvio->getResponsavelPreenchimento();
            $this->dados = $esocialEnvio->getDados();
            $this->md5 = $esocialEnvio->getMd5();
            $this->situacao = $esocialEnvio->getSituacao();
            $this->situacaoSalva = $esocialEnvio->getSituacaoSalva();
            $this->data = $esocialEnvio->getData();
        }
    }

    /**
     * @return string
     * @throws BusinessException
     * @throws DBException
     * @throws ParameterException
     */
    public function getDescricaoIdentificacao()
    {
        $descricao = $this->responsavelPreenchimento;

        if ($this->evento) {
            switch ($this->evento) {
                case Tipo::S1005:
                    $cgm = CgmFactory::getInstanceByCgm($this->empregador);
                    $descricao = "{$this->getResponsavelPreenchimento()} - {$cgm->getNome()}";
                    break;
                case Tipo::S1000:
                case Tipo::R1000:
                    $cgm = CgmFactory::getInstanceByCgm($this->empregador);
                    $descricao = "{$cgm->getCnpj()} - {$cgm->getNome()}";
                    break;
                case Tipo::S1020:
                    $cgm = CgmFactory::getInstanceByCgm($this->empregador);
                    $descricao = "{$cgm->getCnpj()} - {$cgm->getNome()} ";
                    $dados = json_decode($this->dados);
                    $descricao .= "<br>LOTAÇÃO: " . $dados->ideLotacao->codLotacao;
                    break;
                case Tipo::S2190:
                    $dados = json_decode($this->dados);
                    $descricao = 'CPF: ' . db_formatar($dados->infoRegPrelim->cpfTrab, 'CPF');
                    $matricula = $dados->infoRegPrelim->matricula;
                    if (!empty($matricula)) {
                        try {
                            $servidor = new Servidor($this->responsavelPreenchimento);
                            $nomeCgm = \CgmRepository::getNomeByCodigo($servidor->getCodigoCgm());
                            $descricao .= "<br> {$this->responsavelPreenchimento} - $nomeCgm";
                        } catch (BusinessException $e) {
                            $descricao .= "<br> Servidor não cadastrado";
                        }
                    }
                    break;
                case Tipo::S1070:
                    if ($this->responsavelPreenchimento) {
                        $descricao = $this->buscaProcesso($this->responsavelPreenchimento);
                    }
                    break;
                case Tipo::R2020:
                    $dados = JSON::create()->parse($this->dados);
                    $descricao = $dados->ideEstabPrest->ideTomador->nrInscTomador
                        . ' - ' . $dados->ideEstabPrest->perApur;
                    break;
                case Tipo::R2010:
                    $prestador = JSON::create()->parse($this->dados);
                    $cnpj_prestador = $prestador->ideEstabObra->idePrestServ->cnpjPrestador;
                    $descricao = \CgmRepository::getNomeByCNPJ($cnpj_prestador);
                    unset($prestador, $cnpj_prestador);
                    break;
                case Tipo::R2055:
                    $prestador = JSON::create()->parse($this->dados);
                    $cnpj_prestador = $prestador->ideEstabAdquir->ideprodutor->nrinscprod;
                    $descricao = \CgmRepository::getNomeByCNPJ($cnpj_prestador);
                    unset($prestador, $cnpj_prestador);
                    break;
                case Tipo::S2200:
                case Tipo::S2206:
                case Tipo::S2250:
                case Tipo::S2298:
                case Tipo::S2205:
                case Tipo::S2300:
                case Tipo::S2306:
                case Tipo::S2420:
                    if (\ServidorRepository::isMatriculaValida($this->responsavelPreenchimento)) {
                        $servidor = new Servidor($this->responsavelPreenchimento);
                        $descricao = "{$this->responsavelPreenchimento} - "
                            . \CgmRepository::getNomeByCodigo($servidor->getCodigoCgm());
                    } else {
                        $descricao = "Matricula : {$this->responsavelPreenchimento} não encontrada .";
                    }
                    break;
                case Tipo::S2240:
                    $dados = explode("_", $this->responsavelPreenchimento);

                    if (\ServidorRepository::isMatriculaValida($dados[0])) {
                        $servidor = new Servidor($dados[0]);
                        $descricao = "Matrícula: $dados[0]<br>";
                        $descricao .= \CgmRepository::getNomeByCodigo($servidor->getCodigoCgm());

                        foreach ($servidor->getLocaisTrabalho() as $localTrabalho) {
                            if ($localTrabalho->getCodigo() == $dados[1]) {
                                $descricao .= "<br>" . $localTrabalho->getDescricao();
                            }
                        }
                    } else {
                        $descricao = "Matricula : {$dados[0]} não encontrada.";
                    }
                    break;
                case Tipo::S2399:
                    $matricula = substr(
                        $this->responsavelPreenchimento,
                        0,
                        strlen($this->responsavelPreenchimento) - 6
                    );
                    $servidor = new Servidor($matricula);
                    $descricao = "{$matricula} - "
                        . \CgmRepository::getNomeByCodigo($servidor->getCodigoCgm());
                    break;
                case Tipo::S2299:
                    $competencia = substr($this->responsavelPreenchimento, -6);
                    /**
                     * SE a competencia for do formato AAAAMM
                     */
                    if (($competencia[1] == "0" || $competencia[1] == "9")
                        && (int)substr($competencia, 4) <= 12
                    ) {
                        $matricula = substr(
                            $this->responsavelPreenchimento,
                            0,
                            strlen($this->responsavelPreenchimento) - 6
                        );
                        /**
                         * Se a competencia for do formato AAAAM
                         */
                    } elseif ($competencia[2] == "0" || $competencia[2] == "9") {
                        $matricula = substr(
                            $this->responsavelPreenchimento,
                            0,
                            strlen($this->responsavelPreenchimento) - 5
                        );
                        /**
                         * Se a competencia for do Formato AAAA
                         */
                    } else {
                        $matricula = substr(
                            $this->responsavelPreenchimento,
                            0,
                            strlen($this->responsavelPreenchimento) - 4
                        );
                    }
                    $servidor = \ServidorRepository::getInstanciaByCodigo($matricula);
                    $descricao = $servidor->getMatricula() . ' - ' . $servidor->getCgm()->getNome();
                    break;
                case Tipo::S2220:
                    try {
                        $assentamento = new Assentamento($this->responsavelPreenchimento);
                        $servidor = $assentamento->getServidor();
                        $descricao = "{$servidor->getMatricula()} - {$servidor->getCgm()->getNome()}";
                        $descricao .= "<br>({$assentamento->getInstanciaTipoAssentamento()->getDescricao()})";
                        $descricao .= "<br>{$assentamento->getDataConcessao()}";
                        if (!empty($assentamento->getDataTermino())) {
                            $descricao .= " até {$assentamento->getDataTermino()}";
                        }
                    } catch (\Exception $e) {
                        $descricao = "";
                        $dados = JSON::create()->parse($this->dados);
                        if (!empty($dados->ideVinculo->matricula)) {
                            $descricao .= "Matrícula: {$dados->ideVinculo->matricula}";
                        } else {
                            $descricao .= "CPF: {$dados->ideVinculo->cpfTrab}";
                        }
                        $descricao .= "<br> Código do assentamento: {$this->responsavelPreenchimento} <br>";
                        $data = "";
                        $data = "";
                        if (isset($dados->exMedOcup->aso->dtAso) && !empty($dados->exMedOcup->aso->dtAso)) {
                            $data = "Data do Atestado " . \DBDate::format($dados->exMedOcup->aso->dtAso);
                        }
                        $descricao .= " {$data} <br>";
                        $descricao .= " Não localizado no sistema.<br>(Provavelmente o assentamento foi excluído).";
                        $descricao = "<strong>{$descricao}</strong>";
                    }
                    break;
                case Tipo::S2230:
                    try {
                        $assentamento = new Assentamento($this->responsavelPreenchimento);
                        $servidor = $assentamento->getServidor();
                        $descricao = "{$servidor->getMatricula()} - {$servidor->getCgm()->getNome()}";
                        $descricao .= "<br>({$assentamento->getInstanciaTipoAssentamento()->getDescricao()})";
                        $descricao .= "<br>{$assentamento->getDataConcessao()}";
                        if (!empty($assentamento->getDataTermino())) {
                            $descricao .= " até {$assentamento->getDataTermino()}";
                        }
                    } catch (\Exception $e) {
                        $descricao = "";
                        $dados = JSON::create()->parse($this->dados);
                        if (!empty($dados->ideVinculo->matricula)) {
                            $descricao .= "Matrícula: {$dados->ideVinculo->matricula}";
                        } else {
                            $descricao .= "CPF: {$dados->ideVinculo->cpfTrab}";
                        }
                        $descricao .= "<br> Código do assentamento: {$this->responsavelPreenchimento} <br>";
                        $data = "";
                        if (!empty($dados->infoAfastamento->iniAfastamento->dtIniAfast)
                            && !empty($dados->infoAfastamento->fimAfastamento->dtTermAfast)
                        ) {
                            $data = "Periodo " . \DBDate::format($dados->infoAfastamento->iniAfastamento->dtIniAfast)
                                . " até " . \DBDate::format($dados->infoAfastamento->fimAfastamento->dtTermAfast);
                        } else {
                            if (!empty($dados->infoAfastamento->iniAfastamento->dtIniAfast)) {
                                $data = "Inicio em "
                                    . \DBDate::format($dados->infoAfastamento->iniAfastamento->dtIniAfast);
                            } else {
                                $data = "Termino em "
                                    . \DBDate::format($dados->infoAfastamento->fimAfastamento->dtTermAfast);
                            }
                        }
                        $descricao .= " {$data} <br>";
                        $descricao .= " Não localizado no sistema.<br>(Provavelmente o assentamento foi excluído).";
                        $descricao = "<strong>{$descricao}</strong>";
                    }
                    break;
                case Tipo::S2231:
                    $dados = json_decode($this->dados);
                    $referencias = explode("_", $dados->referencia);
                    $servidor = \ServidorRepository::getInstanciaByCodigo($referencias[0]);
                    $descricao = $servidor->getMatricula() . ' - ' . $servidor->getCgm()->getNome();
                    $dataFormatada = date('d/m/Y', strtotime($referencias[3]));
                    if ($referencias[2] == 'inicio') {
                        $descricao .= "<br>" . 'INÍCIO: ' . $dataFormatada;
                    } else {
                        $descricao .= "<br>" . 'FIM: ' . $dataFormatada;
                    }
                    break;
                case Tipo::S2260:
                    $descricao = "CÓDIGO DA CONVOCAÇÃO: {$this->responsavelPreenchimento}";
                    break;
                case Tipo::S3000:
                    $dados = json_decode($this->dados);
                    $descricao = "<span title='O evento S-3000 foi originado do evento";
                    $descricao .= " {$dados->infoExclusao->tpEvento}.'>";
                    $descricao .= "Nº DO RECIBO EXCLUÍDO: {$this->responsavelPreenchimento}";
                    switch ($dados->infoExclusao->tpEvento) {
                        case "S-1210":
                            $nome = \CgmRepository::getNomeByCpf($dados->infoExclusao->ideTrabalhador->cpfTrab);
                            $descricao .= "<br>CPF: {$dados->infoExclusao->ideTrabalhador->cpfTrab} - {$nome}";
                            $descricao .= "<br>Evento: S-1210 - Referencia: ";
                            $descricao .= $dados->infoExclusao->ideFolhaPagto->perApur;
                            break;
                        case "S-2230":
                            $nome = \CgmRepository::getNomeByCpf($dados->infoExclusao->ideTrabalhador->cpfTrab);
                            $descricao .= "<br>CPF: {$dados->infoExclusao->ideTrabalhador->cpfTrab} - {$nome}";
                            $descricao .= "<br>Evento: S2230 - Afastamento Temporário";
                            break;
                    }
                    break;
                case Tipo::S3500:
                    $dados = json_decode($this->dados);
                    $descricao = "Originado do evento ";
                    if ($dados->infoExclusao->tpEvento == 'S-2500') {
                        $dadoReferencia = explode('-', $dados->referencia);
                        $servidor = \ServidorRepository::getInstanciaByCodigo($dadoReferencia[0]);
                        $nome = $servidor->getCgm()->getNome();
                        $matricula = $servidor->getMatricula();
                        $descricao .= $dados->infoExclusao->tpEvento;
                        $descricao .= "<br>Matricula: {$dadoReferencia[0]} - {$nome}";
                    }
                    if ($dados->infoExclusao->tpEvento == 'S-2501') {
                        $dadoReferencia = explode('-', $dados->referencia);
                        $nome = \CgmRepository::getNomeByCpf($dadoReferencia[0]);
                        $descricao .= $dados->infoExclusao->tpEvento;
                        $descricao .= "<br>CPF: {$dadoReferencia[0]} - {$nome}";
                    }
                    $descricao .= "<br>Nº DE PROCESSO: {$dados->ideProcTrab->nrProcTrab}";
                    $descricao .= "<br>Nº DO RECIBO EXCLUÍDO: <strong>{$dados->infoExclusao->nrRecEvt}</strong>";
                    break;
                case Tipo::S2400:
                    $nome = \CgmRepository::getNomeByCpf($this->responsavelPreenchimento);
                    $servidores = \ServidorRepository::getServidoresPorCpf($this->responsavelPreenchimento);
                    $descricao = $nome;
                    $matriculas = "<br>Matricula(s): ";
                    foreach ($servidores as $servidor) {
                        if (!$servidor->isRescindido()) {
                            if (!$servidor->isAtivo()) {
                                $matriculas .= $servidor->getMatricula() . ", ";
                            }
                        }
                    }
                    if ($matriculas != "<br>Matricula(s): ") {
                        $descricao .= substr($matriculas, 0, -2);
                    }
                    break;
                case Tipo::S2410:
                case Tipo::S2416:
                    $dados = json_decode($this->dados);
                    if (\ServidorRepository::isMatriculaValida($dados->referencia)) {
                        $servidor = \ServidorRepository::getInstanciaByCodigo($dados->referencia);
                        $descricao = $servidor->getCgm()->getNome() . ' -  ' . $servidor->getMatricula();
                    } else {
                        $descricao = "Matricula : {$dados->referencia} não encontrada .";
                    }
                    break;
                case Tipo::S2405:
                    $dados = json_decode($this->dados);
                    $nome = \CgmRepository::getNomeByCpf($this->responsavelPreenchimento);
                    $servidores = \ServidorRepository::getServidoresPorCpf($this->responsavelPreenchimento);
                    $descricao = $nome;
                    $matriculas = "<br>Matricula(s): ";
                    foreach ($servidores as $servidor) {
                        if (!$servidor->isRescindido()) {
                            if (!$servidor->isAtivo()) {
                                $matriculas .= $servidor->getMatricula() . ", ";
                            }
                        }
                    }
                    if ($matriculas != "<br>Matricula(s): ") {
                        $descricao .= substr($matriculas, 0, -2);
                    }
                    break;
                case Tipo::S1200:
                    $dados = json_decode($this->dados);
                    $numeroCgm = substr($this->responsavelPreenchimento, 0, -7);
                    $nome = \CgmRepository::getNomeByCodigo($numeroCgm);

                    $descricao = "CPF: " . $dados->ideTrabalhador->cpfTrab;
                    $descricao .= "<br>Matrícula(s): ";
                    foreach ($dados->dmDev as $dmDev) {
                        if (isset($dmDev->infoPerAnt)) {
                            foreach ($dmDev->infoPerAnt->ideADC[0]->idePeriodo[0]->ideEstabLot as $ideEstabLot) {
                                foreach ($ideEstabLot->remunPerAnt as $remunPerAnt) {
                                    if (isset($remunPerAnt->matricula) && !empty($remunPerAnt->matricula)) {
                                        if (!str_contains($descricao, $remunPerAnt->matricula)) {
                                            $descricao .= (string)$remunPerAnt->matricula . ", ";
                                        }
                                    }
                                }
                            }
                        } else {
                            foreach ($dmDev->infoPerApur->ideEstabLot as $ideEstabLot) {
                                foreach ($ideEstabLot->remunPerApur as $remunPerApur) {
                                    if (isset($remunPerApur->matricula) && !empty($remunPerApur->matricula)) {
                                        if (!str_contains($descricao, $remunPerApur->matricula)) {
                                            $descricao .= (string)$remunPerApur->matricula . ", ";
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $descricao = substr($descricao, 0, -2);

                    $descricao .= " - {$nome}";
                    $competencia = substr($dados->referencia, -7, 6);
                    $descricao .= "<br> COMPETÊNCIA: " . substr($competencia, -2)
                        . '/' . substr($competencia, 0, 4) . "</span>";
                    if ($dados->indApuracao == 2) {
                        $descricao .= " (Referente ao 13º)";
                    }

                    unset($cgm);
                    unset($dados);
                    break;
                case Tipo::S1202:
                    $dados = json_decode($this->dados);
                    $referencias = explode("-", $dados->referencia);
                    $numeroCgm = $referencias[0];
                    $nome = \CgmRepository::getNomeByCodigo($numeroCgm);

                    $descricao = "CPF: " . $dados->ideTrabalhador->cpfTrab;
                    $descricao .= "<br>Matrícula(s): ";
                    foreach ($dados->dmDev as $dmDev) {
                        if (!empty($dmDev->infoPerApur->ideEstab)) {
                            foreach ($dmDev->infoPerApur->ideEstab as $ideEstab) {
                                if (!empty($ideEstab->remunPerApur)) {
                                    foreach ($ideEstab->remunPerApur as $remunPerApur) {
                                        if (isset($remunPerApur->matricula) && !empty($remunPerApur->matricula)) {
                                            $descricao .= (string)$remunPerApur->matricula . ", ";
                                        }
                                    }
                                }
                            }
                        }
                        if (!empty($dmDev->infoPerAnt->idePeriodo[0])) {
                            foreach ($dmDev->infoPerAnt->idePeriodo[0]->ideEstab as $ideEstab) {
                                if (!empty($ideEstab->remunPerAnt)) {
                                    foreach ($ideEstab->remunPerAnt as $remunPerAnt) {
                                        if (isset($remunPerAnt->matricula) && !empty($remunPerAnt->matricula)) {
                                            $descricao .= (string)$remunPerAnt->matricula . ", ";
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $descricao = substr($descricao, 0, -2);

                    $descricao .= " - {$nome}";
                    $competencia = $referencias[1];
                    $descricao .= "<br> COMPETÊNCIA: " . substr($competencia, -2)
                        . '/' . substr($competencia, 0, 4) . "</span>";
                    if ($dados->indApuracao == 2) {
                        $descricao .= " (Referente ao 13º)";
                    }

                    unset($cgm);
                    unset($dados);
                    break;
                case Tipo::S1207:
                    $dados = json_decode($this->dados);
                    $referencia = explode('_', $this->responsavelPreenchimento);
                    $nome = \CgmRepository::getNomeByCodigo($referencia[0]);
                    $descricao = "CPF: " . $dados->ideBenef->cpfBenef;
                    $descricao .= "<br>Matrícula(s): ";
                    $matriculas = [];
                    foreach ($dados->dmDev as $dmDev) {
                        $matriculas[$dmDev->nrBeneficio] = $dmDev->nrBeneficio;
                    }
                    $descricao .= implode(",", $matriculas);

                    $descricao .= " - {$nome}";
                    $competencia = $referencia[1];
                    $descricao .= "<br> COMPETÊNCIA: " . substr($competencia, -2)
                        . '/' . substr($competencia, 0, 4) . "</span>";
                    if ($dados->indApuracao == 2) {
                        $descricao .= " (Referente ao 13º)";
                    }

                    unset($cgm);
                    unset($dados);
                    break;
                case Tipo::S1210:
                    $dados = json_decode($this->dados);
                    $tipoPagamento = [];
                    $arrayRef = [];
                    foreach ($dados->ideBenef->infoPgto as $infoPgto) {
                        $tipoPagamento[0] = $infoPgto->tpPgto;
                        $arrayRef[$infoPgto->perRef] = $infoPgto->perRef;
                    }

                    $decimo = false;

                    $referencias = explode("_", $dados->referencia);
                    $cgm = CgmFactory::getInstanceByCgm($referencias[0]);

                    if (empty($cgm)) {
                        $cpf = $dados->ideBenef->cpfBenef;
                        $cgm = CgmFactory::getCgmByCnpjCpf($cpf);
                    }
                    $competencia = str_split($referencias[1], 4);
                    if (strlen($competencia[1]) == 3) {
                        $competencia[1] = substr($competencia[1], 0, 2);
                        $decimo = true;
                    }
                    $servidorMatriculas = new ServidorMatriculas($cgm->getCodigo());
                    $matricula = $servidorMatriculas->getMatriculasByMovimentacao(
                        $competencia[1],
                        $competencia[0],
                        $tipoPagamento
                    );

                    $origem = [
                        "1" => "S1200",
                        "2" => "S2299",
                        "3" => "S2399",
                        "4" => "S1202",
                        "5" => "S1207"
                    ];

                    $descricao = "<span title='O evento S1210 foi originado do evento {$origem[$tipoPagamento[0]]}.'>";
                    $descricao .= "CPF: " . $dados->ideBenef->cpfBenef;
                    $descricao .= "<br>Matrícula(s): ";
                    $matri_nome = "{$matricula} - {$cgm->getNome()}<br> COMPETÊNCIA C.X:";
                    $descricao .= " {$matri_nome}";
                    $periodoApuracao = explode('-', $dados->perApur);
                    $descricao .= "{$periodoApuracao[1]}/$periodoApuracao[0]</span>";
                    $competencias = [];
                    foreach ($arrayRef as $value) {
                        $comp = explode('-', $value);
                        $qtd = sizeof($comp);

                        switch ($qtd) {
                            case 1:
                                $competencias[] = " 13º/{$comp[0]}";
                                break;
                            case 2:
                                $competencias[] = " {$comp[1]}/{$comp[0]}";
                                break;
                        }
                    }

                    $descricao .= "<br>(COMPETÊNCIA(S) PGTO:";
                    $descricao .= implode(",", $competencias);
                    $descricao .= ")";

                    if ($decimo) {
                        $descricao .= " (Referente ao 13º)";
                    }
                    break;
                case Tipo::S1295:
                case Tipo::S1299:
                    $dados = json_decode($this->dados);
                    $descricao = $dados->perApur;
                    break;
                case Tipo::S1300:
                    $dados = json_decode($this->dados);
                    $descricao = $dados->ideEmpregador->perApur;
                    break;
                case Tipo::EFD_FECHAMENTO_PERIODICOS:
                    $cgm = CgmFactory::getInstanceByCgm($this->empregador);
                    $descricao = "{$this->responsavelPreenchimento} - {$cgm->getNome()}";
                    break;
                case Tipo::S1298:
                    $dados = json_decode($this->dados);
                    $referencias = explode("_", $dados->referencia);
                    $competencia = explode('-', $referencias[1]);

                    $descricao = "COMPETÊNCIA: {$competencia[0]}";
                    if (!empty($competencia[1])) {
                        $descricao = "COMPETÊNCIA: {$competencia[1]}/{$competencia[0]}";
                    }
                    break;
                case Tipo::S2210:
                    $dados = json_decode($this->dados);
                    $dataAcidente = \DBDate::format($dados->cat->dtAcid);
                    $nome = \CgmRepository::getNomeByCpf($dados->ideVinculo->cpfTrab);
                    $descricao = "<br>CPF: {$dados->ideVinculo->cpfTrab} - {$nome}";
                    if (!empty($dados->ideVinculo->matricula)) {
                        $descricao .= "<br>Matrícula: {$dados->ideVinculo->matricula}";
                    }
                    $descricao .= "<br>Data do Acidente: {$dataAcidente}";
                    break;
                case Tipo::S2500:
                    $dados = json_decode($this->dados);
                    $dadoReferencia = explode('-', $dados->referencia);
                    $matricula = $dadoReferencia[0];
                    if (isset($dados->infoProcesso->dadosCompl->infoProcJud->dtSent)) {
                        $dataSentenca = \DBDate::format($dados->infoProcesso->dadosCompl->infoProcJud->dtSent);
                    }
                    if (isset($dados->infoProcesso->dadosCompl->infoCCP->dtCCP)) {
                        $dataAcordo = \DBDate::format($dados->infoProcesso->dadosCompl->infoCCP->dtCCP);
                    }
                    $nome = \CgmRepository::getNomeByCpf($dados->ideTrab->cpfTrab);
                    $descricao = "<br>CPF: {$dados->ideTrab->cpfTrab} - {$nome}";
                    if (!empty($matricula)) {
                        $descricao .= "<br>Matrícula: {$matricula}";
                    }
                    $descricao .= "<br>Processo: {$dados->infoProcesso->nrProcTrab}";
                    if (!empty($dataSentenca)) {
                        $descricao .= "<br>Data Sentença: {$dataSentenca}";
                    }
                    if (!empty($dataAcordo)) {
                        $descricao .= "<br>Data Acordo: {$dataAcordo}";
                    }
                    break;
                case Tipo::S2501:
                    $dados = json_decode($this->dados);
                    $dadoReferencia = explode('-', $dados->referencia);
                    $nome = \CgmRepository::getNomeByCpf($dadoReferencia[0]);
                    $descricao = "<br>CPF: {$dadoReferencia[0]} - {$nome}";
                    $descricao .= "<br>Processo: {$dados->ideProc->nrProcTrab}";
                    $descricao .= "<br>Pagamento: {$dados->ideProc->perApurPgto}";
                    foreach ($dados->ideTrab as $servidor) {
                        if ($servidor->cpfTrab == $dadoReferencia[0]) {
                            foreach ($servidor->calcTrib as $indice => $periodoApuracao) {
                                if ($indice == 0) {
                                    $descricao .= "<br>Competência: ";
                                }
                                $descricao .= ($indice == 0 ? "" : ", ") . $periodoApuracao->perRef;
                            }
                        }
                    }
                    break;
                case Tipo::R4020:
                    $dados = json_decode($this->dados);
                    $cnpj_prestador = $dados->idebenef->cnpjbenef;
                    $descricao = \CgmRepository::getNomeByCNPJ($cnpj_prestador);
                    break;
                case Tipo::R4010:
                    $dados = json_decode($this->dados);
                    $cpfbenef = $dados->idebenef->cpfbenef;
                    $descricao = \CgmRepository::getNomeByCNPJ($cpfbenef);
                    break;
                case Tipo::R4040:
                    $cgm = CgmFactory::getInstanceByCgm($this->empregador);
                    $descricao = "{$cgm->getCnpj()} - {$cgm->getNome()}";
                    break;
                case Tipo::R4099:
                    $dados = json_decode($this->dados);
                    $tipo = ($dados->fechret == 0) ? 'FECHAMENTO' : 'REABERTURA';
                    $descricao = $tipo;
                    break;
                case Tipo::R9000:
                    $dados = json_decode($this->dados);
                    $descricao  = "<b>Evento:</b> {$dados->infoExclusao->tpEvento} ";
                    $descricao .= "<b>Competência:</b> {$dados->infoExclusao->perApur} ";
                    $descricao .= "<b>Recibo:</b> {$dados->infoExclusao->nrRecEvt}";
                    break;
            }
        }

        return $descricao;
    }

    /**
     * @param  $responsavel
     * @return bool|string
     */
    private function buscaProcesso($responsavel)
    {
        $avaliacaoGrupoRespostaProcesso = new \cl_avaliacaogruporespostaprocesso();
        $sqlProcesso = $avaliacaoGrupoRespostaProcesso->processoPorEnvio($responsavel);

        $rsProcesso = db_query($sqlProcesso);

        if (!$rsProcesso) {
            return false;
        }

        $dado = pg_fetch_object($rsProcesso);

        return "{$dado->processo} / {$dado->tipo}";
    }

    /**
     * @return mixed
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @param mixed $codigo
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    /**
     * @return mixed
     */
    public function getEvento()
    {
        return $this->evento;
    }

    /**
     * @param mixed $evento
     */
    public function setEvento($evento)
    {
        $this->evento = $evento;
    }

    /**
     * @return mixed
     */
    public function getEmpregador()
    {
        return $this->empregador;
    }

    /**
     * @param mixed $empregador
     */
    public function setEmpregador($empregador)
    {
        $this->empregador = $empregador;
    }

    /**
     * @return mixed
     */
    public function getResponsavelPreenchimento()
    {
        return $this->responsavelPreenchimento;
    }

    /**
     * @param mixed $responsavelPreenchimento
     */
    public function setResponsavelPreenchimento($responsavelPreenchimento)
    {
        $this->responsavelPreenchimento = $responsavelPreenchimento;
    }

    /**
     * @return mixed
     */
    public function getDados()
    {
        return $this->dados;
    }

    /**
     * @param mixed $dados
     */
    public function setDados($dados)
    {
        $this->dados = $dados;
    }

    /**
     * @return mixed
     */
    public function getMd5()
    {
        return $this->md5;
    }

    /**
     * @param mixed $md5
     */
    public function setMd5($md5)
    {
        $this->md5 = $md5;
    }

    /**
     * @return mixed
     */
    public function getSituacao()
    {
        return $this->situacao;
    }

    /**
     * @param mixed $situacao
     */
    public function setSituacao($situacao)
    {
        $this->situacao = $situacao;
    }

    /**
     * @return \DateTime
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \DateTime $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getSituacaoSalva()
    {
        return $this->situacaoSalva;
    }

    /**
     * @param mixed $situacaoSalva
     */
    public function setSituacaoSalva($situacaoSalva)
    {
        $this->situacaoSalva = $situacaoSalva;
    }

    /**
     * @return bool
     */
    public function isPermiteAtualizar()
    {
        return $this->permiteAtualizar;
    }

    /**
     * @param bool $permiteAtualizar
     */
    public function setPermiteAtualizar($permiteAtualizar)
    {
        $this->permiteAtualizar = $permiteAtualizar;
    }

    /**
     * @return bool
     */
    public function isProcessadoSucesso()
    {
        return $this->processadoSucesso;
    }

    /**
     * @param bool $processadoSucesso
     */
    public function setProcessadoSucesso($processadoSucesso)
    {
        $this->processadoSucesso = $processadoSucesso;
    }

    /**
     * @return bool
     */
    public function isAguardandoProcessamento()
    {
        return $this->aguardandoProcessamento;
    }

    /**
     * @param bool $aguardandoProcessamento
     */
    public function setAguardandoProcessamento($aguardandoProcessamento)
    {
        $this->aguardandoProcessamento = $aguardandoProcessamento;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    /**
     * @return \stdClass[]
     */
    public function getOcorrencias()
    {
        return $this->ocorrencias;
    }

    /**
     * @param \stdClass[] $ocorrencias
     */
    public function setOcorrencias($ocorrencias)
    {
        $this->ocorrencias = $ocorrencias;
    }

    /**
     * @return \stdClass[]
     */
    public function getRecibos()
    {
        return $this->recibos;
    }

    /**
     * @param \stdClass[] $recibos
     */
    public function setRecibos($recibos)
    {
        $this->recibos = $recibos;
    }

    /**
     * @throws BusinessException
     * @throws DBException
     * @throws ParameterException
     */
    public function adicionaDescricaoPadrao()
    {
        $this->descricao = $this->getDescricaoIdentificacao();
    }

    public function serialize()
    {
        $serialize = clone $this;
        if ($this->data instanceof \DateTime) {
            $serialize->setData($this->data->format("d/m/Y H:i:s"));
        }
        return JSON::create()->stringify(get_object_vars($serialize));
    }

    public static function fromState(array $state)
    {

        $esocialEnvio = new self();

        if (array_key_exists('rh213_sequencial', $state)) {
            $esocialEnvio->setCodigo((int)$state['rh213_sequencial']);
        }

        if (array_key_exists('rh213_evento', $state)) {
            $esocialEnvio->setEvento((string)$state['rh213_evento']);
        }

        if (array_key_exists('rh213_empregador', $state)) {
            $esocialEnvio->setEmpregador((int)$state['rh213_empregador']);
        }

        if (array_key_exists('rh213_responsavelpreenchimento', $state)) {
            $esocialEnvio->setResponsavelPreenchimento((string)$state['rh213_responsavelpreenchimento']);
        }

        if (array_key_exists('rh213_dados', $state)) {
            $esocialEnvio->setDados((string)$state['rh213_dados']);
        }

        if (array_key_exists('rh213_md5', $state)) {
            $esocialEnvio->setMd5((string)$state['rh213_md5']);
        }

        if (array_key_exists('rh213_data', $state)) {
            $esocialEnvio->setData(new \DateTime($state['rh213_data']));
        }
        if (array_key_exists('rh213_situacao', $state)) {
            $esocialEnvio->setSituacaoSalva((string)$state['rh213_situacao']);
        }

        if (array_key_exists('rh214_sequencial', $state)) {
            $esocialEnvio->setSituacao($state['rh214_descricao']);
            $esocialEnvio->setPermiteAtualizar(false);
            $esocialEnvio->setProcessadoSucesso(($state['rh214_situacao'] == "f" ? false : true));
            $esocialEnvio->setAguardandoProcessamento(false);
        } else {
            $mensagem = "Aguardando envio na rotina eSocial > Procedimentos > Envio de eventos para o eSocial.";
            $esocialEnvio->setSituacao($mensagem);
            $esocialEnvio->setPermiteAtualizar(false);
            $esocialEnvio->setProcessadoSucesso(true);
            $esocialEnvio->setAguardandoProcessamento(true);
        }

        $esocialEnvio->adicionaDescricaoPadrao();

        return $esocialEnvio;
    }

    /**
     * @return int
     */
    public function getCodigoEnvio()
    {
        return $this->codigoEnvio;
    }

    /**
     * @param int $codigoEnvio
     */
    public function setCodigoEnvio($codigoEnvio)
    {
        $this->codigoEnvio = $codigoEnvio;
    }

    /**
     * Funcao responsavel por formatar o texto do campo data
     * utilizar somente para exibição de dados, pois a proprieada é perdida a partir do momento que a função é chamada
     */
    public function formataExibicaoData($dado)
    {
        $this->data = $dado;
    }

    public function getEnvioStatus()
    {
        return $this->envioStatus;
    }

    public function setEnvioStatus($envioStatus)
    {
        $this->envioStatus = $envioStatus;
    }
}
