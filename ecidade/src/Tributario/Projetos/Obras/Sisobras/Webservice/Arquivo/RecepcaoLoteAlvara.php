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

namespace ECidade\Tributario\Projetos\Obras\Sisobras\Webservice\Arquivo;

use DOMDocument;
use DOMElement;

use NFePHP\Common\Signer;
use NFePHP\Common\Certificate;

/**
 * Classe responsável pela criação do xml para a operação recepcaoLote de Alvará
 * @author Matheus Sousa <matheus.sousa@dbseller.com.br>
 */
class RecepcaoLoteAlvara implements RequisicaoInterface
{
    private $oXml;
    private $arrayRegistroAlvara;
    private $sOperacao;

  /**
   * Criamos o objeto da classe com as informações necessárias para a sua exitência
   *
   * @param stdClass $arrayRegistroAlvara
   */
    public function __construct($arrayRegistroAlvara, DOMDocument $oXml, $localA1, $senhaA1)
    {
        $this->oXml                     = $oXml;
        $this->oXml->preserveWhiteSpace = false;
        $this->oXml->formatOutput       = true;
        $this->arrayRegistroAlvara      = $arrayRegistroAlvara;
        $this->sOperacao                = "recepcaoLote";
        $this->localA1                  = $localA1;
        $this->senhaA1                  = $senhaA1;
    }

  /**
   * Buscamos a operação que será executada no webservice
   *
   * @return string
   */
    public function getOperacao()
    {
        return $this->sOperacao;
    }

  /**
   * Geramos o xml com os dados do alvará
   *
   * @return DOMDocument
   */
    public function getRequestXml()
    {
        foreach ($this->arrayRegistroAlvara as $key => $oRegistro) {
            $this->oXml->appendChild($this->getDadosXml($oRegistro));
        }
        return $this->oXml;
    }

    public function getDadosXml($oRegistro)
    {
        $Alvara = $this->oXml->createElement("Alvara");
        $infAlvara = $this->oXml->createElement("infAlvara");

        $Id = $this->oXml->createAttribute("Id");
        $Id->value = $oRegistro->oRegistroAlvara->getId();

        $numeroAlvara = $this->oXml->createElement(
            "numeroAlvara",
            $oRegistro->oRegistroAlvara->getNumeroAlvara()
        );
        if (!empty($oRegistro->oRegistroAlvara->getNumeroProtocoloAnterior())) {
            $numeroProtocoloAnterior = $this->oXml->createElement(
                "numeroProtocoloAnterior",
                $oRegistro->oRegistroAlvara->getNumeroProtocoloAnterior()
            );
        }
        $nomeObra = $this->oXml->createElement(
            "nomeObra",
            \DBString::removerAcentuacao(trim($oRegistro->oRegistroAlvara->getNomeObra()))
        );
        $dataAlvara = $this->oXml->createElement(
            "dataAlvara",
            $oRegistro->oRegistroAlvara->getDataAlvara()
        );
        $dataInicioObra = $this->oXml->createElement(
            "dataInicioObra",
            $oRegistro->oRegistroAlvara->getDataInicioObra()
        );
        if (!empty($oRegistro->oRegistroAlvara->getDataFinalObra())) {
            $dataFinalObra = $this->oXml->createElement(
                "dataFinalObra",
                $oRegistro->oRegistroAlvara->getDataFinalObra()
            );
        }
        $tipoAlvara = $this->oXml->createElement(
            "tipoAlvara",
            $oRegistro->oRegistroAlvara->getTipoAlvara()
        );

        // Dados referentes a tag responsavelExecObra (deve conter apenas 1)
        if ($oRegistro->oRegistroAlvara->getProprietarioDoImovel()) {
            $proprietario_do_imovel = $this->oXml->createElement(
                "proprietario_do_imovel"
            );
        } elseif (!empty(
            $oRegistro->oRegistroAlvara->getDonoDaObraCnpj() ||
            $oRegistro->oRegistroAlvara->getDonoDaObraCpf()
        )
        ) {
            $dono_da_obra_cnpj = $this->oXml->createElement(
                "cnpj",
                $oRegistro->oRegistroAlvara->getDonoDaObraCnpj()
            );
            $dono_da_obra_cpf = $this->oXml->createElement(
                "cpf",
                $oRegistro->oRegistroAlvara->getDonoDaObraCpf()
            );
        } elseif (!empty(
            $oRegistro->oRegistroAlvara->getIncorporadorConstrucaoCivilCnpj() ||
            $oRegistro->oRegistroAlvara->getIncorporadorConstrucaoCivilCpf()
        )
        ) {
            $incorporador_construcao_civil_cnpj = $this->oXml->createElement(
                "incorporador_construcao_civil_cnpj",
                $oRegistro->oRegistroAlvara->getIncorporadorConstrucaoCivilCnpj()
            );
            $incorporador_construcao_civil_cpf = $this->oXml->createElement(
                "incorporador_construcao_civil_cpf",
                $oRegistro->oRegistroAlvara->getIncorporadorConstrucaoCivilCpf()
            );
        } elseif (!empty($oRegistro->oRegistroAlvara->getEmpresaConstrutoraCnpj())) {
            $empresa_construtora_cnpj = $this->oXml->createElement(
                "empresa_construtora_cnpj",
                $oRegistro->oRegistroAlvara->getEmpresaConstrutoraCnpj()
            );
        } elseif (!empty(
            $oRegistro->oRegistroAlvara->getConstrucaoNomeColetivoCnpj() ||
            $oRegistro->oRegistroAlvara->getConstrucaoNomeColetivoCpf()
        )
        ) {
            if (!empty($oRegistro->oRegistroAlvara->getConstrucaoNomeColetivoCnpj())) {
                $cnpjResponsavelPrincipal = $this->oXml->createElement(
                    "cnpjResponsavelPrincipal",
                    $oRegistro->oRegistroAlvara->getCnpjResponsavelPrincipal()
                );
            } else {
                $cpfResponsavelPrincipal = $this->oXml->createElement(
                    "cpfResponsavelPrincipal",
                    $oRegistro->oRegistroAlvara->getCpfResponsavelPrincipal()
                );
            }

            if (!empty($oRegistro->oRegistroAlvara->getConstrucaoNomeColetivoCnpj())) {
                $cnpjsNomeColetivo = $oRegistro->oRegistroAlvara->getConstrucaoNomeColetivoCnpj();
                $construcao_nome_coletivo_cnpjs = [];

                foreach ($cnpjsNomeColetivo as $cnpjNomeColetivo) {
                    $construcao_nome_coletivo_cnpjs[] = $this->oXml->createElement(
                        "cnpj",
                        $cnpjNomeColetivo
                    );
                }
            }

            if (!empty($oRegistro->oRegistroAlvara->getConstrucaoNomeColetivoCpf())) {
                $cpfsNomeColetivo = $oRegistro->oRegistroAlvara->getConstrucaoNomeColetivoCpf();
                $construcao_nome_coletivo_cpfs = [];

                foreach ($cpfsNomeColetivo as $cpfNomeColetivo) {
                    $construcao_nome_coletivo_cpfs[] = $this->oXml->createElement(
                        "cpf",
                        $cpfNomeColetivo
                    );
                }
            }
        }

        /************************** FUTURA IMPLEMENTAÇÃO **************************/
        // $cnpjConsorcio = $this->oXml->createElement(
        //     "cnpjConsorcio",
        //     $oRegistro->oRegistroAlvara->getCnpjConsorcio()
        // );
        // $cnpjEmpresaLiderConsorcio = $this->oXml->createElement(
        //     "cnpjEmpresaLiderConsorcio",
        //     $oRegistro->oRegistroAlvara->getCnpjEmpresaLiderConsorcio()
        // );

        // Dados referentes a tag endereco
        $cep = $this->oXml->createElement(
            "cep",
            $oRegistro->oRegistroAlvara->getCep()
        );
        $tipoLogradouro = $this->oXml->createElement(
            "tipoLogradouro",
            $oRegistro->oRegistroAlvara->getTipoLogradouro()
        );
        $logradouro = $this->oXml->createElement(
            "logradouro",
            \DBString::removerAcentuacao($oRegistro->oRegistroAlvara->getLogradouro())
        );
        $numero = $this->oXml->createElement(
            "numero",
            $oRegistro->oRegistroAlvara->getNumero()
        );
        if (!empty($oRegistro->oRegistroAlvara->getComplemento())) {
            $complemento = $this->oXml->createElement(
                "complemento",
                $oRegistro->oRegistroAlvara->getComplemento()
            );
        }
        $bairro = $this->oXml->createElement(
            "bairro",
            \DBString::removerAcentuacao($oRegistro->oRegistroAlvara->getBairro())
        );

        $unidadeMedida = $this->oXml->createElement(
            "unidadeMedida",
            $oRegistro->oRegistroAlvara->getUnidadeMedida()
        );
        if (!empty($oRegistro->oRegistroAlvara->getValorUnidadeMedida())) {
            $valorUnidadeMedida = $this->oXml->createElement(
                "valorUnidadeMedida",
                $oRegistro->oRegistroAlvara->getValorUnidadeMedida()
            );
        }
        if (!empty($oRegistro->oRegistroAlvara->getProprietarioObraCpf())) {
            $proprietarioObraCpf = $this->oXml->createElement(
                "cpf",
                $oRegistro->oRegistroAlvara->getProprietarioObraCpf()
            );
        }
        if (!empty($oRegistro->oRegistroAlvara->getProprietarioObraCnpj())) {
            $proprietarioObraCnpj = $this->oXml->createElement(
                "cnpj",
                $oRegistro->oRegistroAlvara->getProprietarioObraCnpj()
            );
        }
        if (!empty($oRegistro->oRegistroAlvara->getSituacao())) {
            $situacao = $this->oXml->createElement(
                "situacao",
                $oRegistro->oRegistroAlvara->getSituacao()
            );
        }

        /************************** FUTURA IMPLEMENTAÇÃO **************************/
        // $classe = $this->oXml->createElement(
        //     "classe",
        //     $oRegistro->oRegistroAlvara->getClasse()
        // );
        if (!empty($oRegistro->oRegistroAlvara->getNumeroProcesso())) {
            $numeroProcesso = $this->oXml->createElement(
                "numeroProcesso",
                $oRegistro->oRegistroAlvara->getNumeroProcesso()
            );
        }

        // Dados referentes a tag responsavelTecnico
        $engenheiroNomeTecnico = $this->oXml->createElement(
            "nome",
            $oRegistro->oRegistroAlvara->getEngenheiroNomeTecnico()
        );
        $engenheiroCreaTecnico = $this->oXml->createElement(
            "crea",
            $oRegistro->oRegistroAlvara->getEngenheiroCreaTecnico()
        );
        $engenheiroArtTecnico = $this->oXml->createElement(
            "art",
            $oRegistro->oRegistroAlvara->getEngenheiroArtTecnico()
        );
        $arquitetoNomeTecnico = $this->oXml->createElement(
            "nome",
            $oRegistro->oRegistroAlvara->getArquitetoNomeTecnico()
        );
        $arquitetoCauTecnico = $this->oXml->createElement(
            "cau",
            $oRegistro->oRegistroAlvara->getArquitetoCauTecnico()
        );
        $arquitetoRrtTecnico = $this->oXml->createElement(
            "rrt",
            $oRegistro->oRegistroAlvara->getArquitetoRrtTecnico()
        );
        $engenheiroNomeProjeto = $this->oXml->createElement(
            "nome",
            $oRegistro->oRegistroAlvara->getEngenheiroNomeProjeto()
        );
        $engenheiroCreaProjeto = $this->oXml->createElement(
            "crea",
            $oRegistro->oRegistroAlvara->getEngenheiroCreaProjeto()
        );
        $engenheiroArtProjeto = $this->oXml->createElement(
            "art",
            $oRegistro->oRegistroAlvara->getEngenheiroArtProjeto()
        );
        $arquitetoNomeProjeto = $this->oXml->createElement(
            "nome",
            $oRegistro->oRegistroAlvara->getArquitetoNomeProjeto()
        );
        $arquitetoCauProjeto = $this->oXml->createElement(
            "cau",
            $oRegistro->oRegistroAlvara->getArquitetoCauProjeto()
        );
        $arquitetoRrtProjeto = $this->oXml->createElement(
            "rrt",
            $oRegistro->oRegistroAlvara->getArquitetoRrtProjeto()
        );

        if (!empty($oRegistro->oRegistroAlvara->getEspecificacao())) {
            $especificacao = $this->oXml->createElement(
                "especificacao",
                $oRegistro->oRegistroAlvara->getEspecificacao()
            );
        }
        if (!empty($oRegistro->oRegistroAlvara->getObservacao())) {
            $observacao = $this->oXml->createElement(
                "observacao",
                \DBString::removerAcentuacao(trim($oRegistro->oRegistroAlvara->getObservacao()))
            );
        }

        // Atribui tags criadas ao nodo infAlvara
        $infAlvara->appendChild($Id);
        $infAlvara->appendChild($numeroAlvara);
        if (!empty($numeroProtocoloAnterior)) {
            $infAlvara->appendChild($numeroProtocoloAnterior);
        }
        $infAlvara->appendChild($nomeObra);
        $infAlvara->appendChild($dataAlvara);
        $infAlvara->appendChild($dataInicioObra);
        if (!empty($dataFinalObra)) {
            $infAlvara->appendChild($dataFinalObra);
        }
        $infAlvara->appendChild($tipoAlvara);

        // Monta tags referentes a tag endereco
        $responsavelExecObra = $this->oXml->createElement("responsavelExecObra");
        $infAlvara->appendChild($responsavelExecObra);

        if (!empty($oRegistro->oRegistroAlvara->getProprietarioDoImovel())) {
            $responsavelExecObra->appendChild($proprietario_do_imovel);
        } elseif (!empty(
            $oRegistro->oRegistroAlvara->getDonoDaObraCnpj() ||
            $oRegistro->oRegistroAlvara->getDonoDaObraCpf()
        )
            ) {
            $dono_da_obra = $this->oXml->createElement("dono_da_obra");
            $responsavelExecObra->appendChild($dono_da_obra);
            $dono_da_obra->appendChild($dono_da_obra_cnpj);
            $dono_da_obra->appendChild($dono_da_obra_cpf);
        } elseif (!empty(
            $oRegistro->oRegistroAlvara->getIncorporadorConstrucaoCivilCnpj() ||
            $oRegistro->oRegistroAlvara->getIncorporadorConstrucaoCivilCpf()
        )
            ) {
            $incorporador_construcao_civil = $this->oXml->createElement("incorporador_construcao_civil");
            $responsavelExecObra->appendChild($incorporador_construcao_civil);
            $incorporador_construcao_civil->appendChild($incorporador_construcao_civil_cnpj);
            $incorporador_construcao_civil->appendChild($incorporador_construcao_civil_cpf);
        } elseif (!empty($oRegistro->oRegistroAlvara->getEmpresaConstrutoraCnpj())) {
            $empresa_construtora = $this->oXml->createElement("empresa_construtora");
            $responsavelExecObra->appendChild($empresa_construtora);
            $empresa_construtora->appendChild($empresa_construtora_cnpj);
        } elseif (!empty(
            $oRegistro->oRegistroAlvara->getConstrucaoNomeColetivoCnpj() ||
            $oRegistro->oRegistroAlvara->getConstrucaoNomeColetivoCpf()
        )
        ) {
            $construcao_nome_coletivo = $this->oXml->createElement("construcao_nome_coletivo");
            $responsavelExecObra->appendChild($construcao_nome_coletivo);

            if (!empty($cpfResponsavelPrincipal)) {
                $construcao_nome_coletivo->appendChild($cpfResponsavelPrincipal);
            } else {
                $construcao_nome_coletivo->appendChild($cnpjResponsavelPrincipal);
            }

            if (!empty($oRegistro->oRegistroAlvara->getConstrucaoNomeColetivoCnpj())) {
                foreach ($construcao_nome_coletivo_cnpjs as $construcao_nome_coletivo_cnpj) {
                    $construcao_nome_coletivo->appendChild($construcao_nome_coletivo_cnpj);
                }
            }

            if (!empty($oRegistro->oRegistroAlvara->getConstrucaoNomeColetivoCpf())) {
                foreach ($construcao_nome_coletivo_cpfs as $construcao_nome_coletivo_cpf) {
                    $construcao_nome_coletivo->appendChild($construcao_nome_coletivo_cpf);
                }
            }
        }

        /************************** FUTURA IMPLEMENTAÇÃO **************************/
        // $empresa_lider_consorcio = $this->oXml->createElement("empresa_lider_consorcio");
        // $responsavelExecObra->appendChild($empresa_lider_consorcio);
        // $empresa_lider_consorcio->appendChild($cnpjConsorcio);
        // $empresa_lider_consorcio->appendChild($cnpjEmpresaLiderConsorcio);

        // $consorcio = $this->oXml->createElement("consorcio");
        // $responsavelExecObra->appendChild($consorcio);
        // $consorcio->appendChild($cnpjConsorcio);
        // $consorcio->appendChild($cnpjEmpresaLiderConsorcio);

        // Monta tags referentes a tag endereco
        $enderecoObra = $this->oXml->createElement("enderecoObra");
        $infAlvara->appendChild($enderecoObra);
        $enderecoObra->appendChild($cep);
        $enderecoObra->appendChild($tipoLogradouro);
        $enderecoObra->appendChild($logradouro);
        $enderecoObra->appendChild($numero);
        if (!empty($complemento)) {
            $enderecoObra->appendChild($complemento);
        }
        $enderecoObra->appendChild($bairro);

        $infAlvara->appendChild($unidadeMedida);
        if (!empty($valorUnidadeMedida)) {
            $infAlvara->appendChild($valorUnidadeMedida);
        }

        // Monta tags referentes a tag area
        $area = $this->oXml->createElement("area");
        $infAlvara->appendChild($area);
        $area->appendChild($this->getDadosAreaPrincipalXml($oRegistro->oRegistroAreaPrincipal));

        /*
         * Percorre Array de objetos da Area Complementar,
         * caso tenha algum dado, adiciona Area Complementar,
         * caso contrário não insere
         */
        $cont=0;
        foreach ($oRegistro->oRegistroAreaComplementar as $value) {
            if (!empty($value)) {
                $cont++;
            }
        }
        if (!empty($cont)) {
            $area->appendChild($this->getDadosAreaComplementarXml($oRegistro->oRegistroAreaComplementar));
        }

        $proprietarioObra = $this->oXml->createElement("proprietarioObra");
        $infAlvara->appendChild($proprietarioObra);
        
        if (!empty($oRegistro->oRegistroAlvara->getProprietarioObraCpf())) {
            $proprietarioObra->appendChild($proprietarioObraCpf);
        }
        if (!empty($oRegistro->oRegistroAlvara->getProprietarioObraCnpj())) {
            $proprietarioObra->appendChild($proprietarioObraCnpj);
        }

        // Monta tags referentes a tag infoAdicionais
        $infoAdicionais = $this->oXml->createElement("infoAdicionais");
        $infAlvara->appendChild($infoAdicionais);
        if (!empty($oRegistro->oRegistroAlvara->getSituacao())) {
            $infoAdicionais->appendChild($situacao);
        }

        /************************** FUTURA IMPLEMENTAÇÃO **************************/
        // $infoAdicionais->appendChild($classe);

        if (!empty($oRegistro->oRegistroAlvara->getNumeroProcesso())) {
            $infoAdicionais->appendChild($numeroProcesso);
        }

        // Monta tags referentes a tag responsavelTecnico
        if (!empty($oRegistro->oRegistroAlvara->getEngenheiroNomeTecnico()) ||
            !empty($oRegistro->oRegistroAlvara->getArquitetoNomeTecnico())
        ) {
            $responsavelTecnico = $this->oXml->createElement("responsavelTecnico");
            $infoAdicionais->appendChild($responsavelTecnico);
            
            if (!empty($oRegistro->oRegistroAlvara->getEngenheiroNomeTecnico()) &&
                !empty($oRegistro->oRegistroAlvara->getEngenheiroCreaTecnico()) &&
                !empty($oRegistro->oRegistroAlvara->getEngenheiroArtTecnico())
            ) {
                $engenheiroTecnico = $this->oXml->createElement("engenheiro");
                $responsavelTecnico->appendChild($engenheiroTecnico);
                $engenheiroTecnico->appendChild($engenheiroNomeTecnico);
                $engenheiroTecnico->appendChild($engenheiroCreaTecnico);
                $engenheiroTecnico->appendChild($engenheiroArtTecnico);
            }
            if (!empty($oRegistro->oRegistroAlvara->getArquitetoNomeTecnico()) &&
                !empty($oRegistro->oRegistroAlvara->getArquitetoCauTecnico()) &&
                !empty($oRegistro->oRegistroAlvara->getArquitetoRrtTecnico())
            ) {
                $arquitetoTecnico = $this->oXml->createElement("arquiteto");
                $responsavelTecnico->appendChild($arquitetoTecnico);
                $arquitetoTecnico->appendChild($arquitetoNomeTecnico);
                $arquitetoTecnico->appendChild($arquitetoCauTecnico);
                $arquitetoTecnico->appendChild($arquitetoRrtTecnico);
            }
        }
        
        // Monta tags referentes a tag responsavelProjeto
        if (!empty($oRegistro->oRegistroAlvara->getEngenheiroNomeProjeto()) ||
            !empty($oRegistro->oRegistroAlvara->getArquitetoNomeProjeto())
        ) {
            $responsavelProjeto = $this->oXml->createElement("responsavelProjeto");
            $infoAdicionais->appendChild($responsavelProjeto);
            
            if (!empty($oRegistro->oRegistroAlvara->getEngenheiroNomeProjeto()) &&
                !empty($oRegistro->oRegistroAlvara->getEngenheiroCreaProjeto()) &&
                !empty($oRegistro->oRegistroAlvara->getEngenheiroArtProjeto())
            ) {
                $engenheiroProjeto = $this->oXml->createElement("engenheiro");
                $responsavelProjeto->appendChild($engenheiroProjeto);
                $engenheiroProjeto->appendChild($engenheiroNomeProjeto);
                $engenheiroProjeto->appendChild($engenheiroCreaProjeto);
                $engenheiroProjeto->appendChild($engenheiroArtProjeto);
            }
            if (!empty($oRegistro->oRegistroAlvara->getArquitetoNomeProjeto()) &&
                !empty($oRegistro->oRegistroAlvara->getArquitetoCauProjeto()) &&
                !empty($oRegistro->oRegistroAlvara->getArquitetoRrtProjeto())
            ) {
                $arquitetoProjeto = $this->oXml->createElement("arquiteto");
                $responsavelProjeto->appendChild($arquitetoProjeto);
                $arquitetoProjeto->appendChild($arquitetoNomeProjeto);
                $arquitetoProjeto->appendChild($arquitetoCauProjeto);
                $arquitetoProjeto->appendChild($arquitetoRrtProjeto);
            }
        }

        if (!empty($oRegistro->oRegistroAlvara->getEspecificacao())) {
            $infoAdicionais->appendChild($especificacao);
        }
        if (!empty($oRegistro->oRegistroAlvara->getObservacao())) {
            $infoAdicionais->appendChild($observacao);
        }

        $infAlvara->appendChild($infoAdicionais);
        $Alvara->appendChild($infAlvara);
                
        /*INICIO NFEPHP*/
        $pfx = file_get_contents($this->localA1);
        $cert = Certificate::readPfx($pfx, $this->senhaA1);

        $tagname = 'infAlvara'; //tag a ser assinada,
                    //se este campo for deixado vazio a tag raiz será assinada

        $mark = 'Id'; //indica se a assinatura fará referencia a uma tag
            //com atributo de identificação definido,
            //se for assinar a raiz do documento este campo deverá
            //ser deixado em branco

        $algorithm = OPENSSL_ALGO_SHA1; //algoritmo de encriptação a ser usado

        $canonical = [true,false,null,null]; //veja função C14n do PHP

        $rootname = 'Alvara'; //este campo indica em qual node a assinatura deverá ser inclusa
        $this->oXml->formatOutput = false;
        $sXml = $this->oXml->saveXML();
        $sXml = utf8_encode($sXml);
        $sXml = $Alvara;

        $Body = $sXml;
        $Document = new DOMDocument();
        $Document->appendChild($Document->importNode($Body, true));

        $sXml = $Document->saveXML();
        try {
            $signed = Signer::sign(
                $cert,
                $sXml,
                $tagname,
                $mark,
                $algorithm,
                $canonical,
                $rootname
            );
        } catch (\Exception $e) {
            //aqui você trata a exceção
            dd($e->getMessage());
        }
        $signed = utf8_decode($signed);
        $signed = str_replace('&lt;', '<', $signed);
        $signed = str_replace('&gt;', '>', $signed);
        $signed = str_replace('<Alvara>', '', $signed);
        $signed = str_replace('</Alvara>', '', $signed);

        $AlvaraAssinado = new DOMElement('Alvara', $signed);
        $this->oXml->appendChild($AlvaraAssinado);
        return $AlvaraAssinado;

        /*FIM NFEPHP*/

        // $this->oXml->appendChild($Alvara);

        // return $Alvara;
    }

    public function getDadosAreaPrincipalXml($oRegistroAreaPrincipal)
    {
        $areaPrincipal = $this->oXml->createElement("areaPrincipal");

        $categoria = $this->oXml->createElement(
            "categoria",
            strtolower($oRegistroAreaPrincipal->getCategoria())
        );
        $destinacao = $this->oXml->createElement(
            "destinacao",
            strtolower($oRegistroAreaPrincipal->getDestinacao())
        );
        $tipoObra = $this->oXml->createElement(
            "tipoObra",
            $oRegistroAreaPrincipal->getTipoObra()
        );
        if (!empty($oRegistroAreaPrincipal->getQtdTotalUnidadesBloco())) {
            $qtd_total_unidades_bloco = $this->oXml->createElement(
                "qtd_total_unidades_bloco",
                $oRegistroAreaPrincipal->getQtdTotalUnidadesBloco()
            );
        }
        $area = $this->oXml->createElement(
            "area",
            number_format($oRegistroAreaPrincipal->getArea(), 2, '.', '')
        );

        $areaPrincipal->appendChild($categoria);
        $areaPrincipal->appendChild($destinacao);
        $areaPrincipal->appendChild($tipoObra);
        if (!empty($qtd_total_unidades_bloco)) {
            $areaPrincipal->appendChild($qtd_total_unidades_bloco);
        }
        $areaPrincipal->appendChild($area);

        $this->oXml->appendChild($areaPrincipal);

        return $areaPrincipal;
    }

    public function getDadosAreaComplementarXml($oRegistroAreaComplementar)
    {
        $areaComplementar = $this->oXml->createElement("areaComplementar");

        $categoria = $this->oXml->createElement(
            "categoria",
            $oRegistroAreaComplementar->getCategoria()
        );
        $destinacao = $this->oXml->createElement(
            "destinacao",
            $oRegistroAreaComplementar->getDestinacao()
        );
        $tipoObra = $this->oXml->createElement(
            "tipoObra",
            $oRegistroAreaComplementar->getTipoObra()
        );
        $tipoAreaComplementar = $this->oXml->createElement(
            "tipoAreaComplementar",
            $oRegistroAreaComplementar->getTipoAreaComplementar()
        );
        if (!empty($oRegistroAreaComplementar->getQtdTotalUnidadesBloco())) {
            $qtd_total_unidades_bloco = $this->oXml->createElement(
                "qtd_total_unidades_bloco",
                $oRegistroAreaComplementar->getQtdTotalUnidadesBloco()
            );
        }
        $areaCoberta = $this->oXml->createElement(
            "areaCoberta",
            $oRegistroAreaComplementar->getAreaCoberta()
        );
        $areaDescoberta = $this->oXml->createElement(
            "areaDescoberta",
            $oRegistroAreaComplementar->getAreaDescoberta()
        );

        $areaComplementar->appendChild($categoria);
        $areaComplementar->appendChild($destinacao);
        $areaComplementar->appendChild($tipoObra);
        $areaComplementar->appendChild($tipoAreaComplementar);
        if (!empty($qtd_total_unidades_bloco)) {
            $areaComplementar->appendChild($qtd_total_unidades_bloco);
        }
        $areaComplementar->appendChild($areaCoberta);
        $areaComplementar->appendChild($areaDescoberta);

        $this->oXml->appendChild($areaComplementar);

        return $areaComplementar;
    }
}
