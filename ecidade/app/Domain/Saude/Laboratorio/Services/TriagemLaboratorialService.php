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

namespace App\Domain\Saude\Laboratorio\Services;

use App\Domain\Saude\Laboratorio\Repositories\TriagemLaboratorialRepository;
use Illuminate\Support\Facades\Storage;
use ECidade\Library\File\File;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Collection\TagsXML as TagsXMLCollection;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Converter\Pedido;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Enum\Parametros as ParametrosEnum;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Service\IntegraPedidos;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Service\Parametros as ParametrosService;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Service\Pedido as PedidoService;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Service\TagXML;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Enum\Parametros as ParametrosIntegracao;
use ECidade\Saude\Laboratorio\Model\Parametros as ParametrosLaboratorio;
use ECidade\Saude\Laboratorio\Repository\LaboratorioRepository;
use ECidade\Saude\Laboratorio\Service\LaboratorioService;
use ECidade\Saude\Laboratorio\Repository\Parametros;


use Exception;

class TriagemLaboratorialService
{
    protected $repository;
    protected $parametrosLaboratorio;
    protected $setoresInfinity = [22, 23, 24, 27, 28];

    public function __construct()
    {
        $this->repository = new TriagemLaboratorialRepository();
        $parametros =  new Parametros();
        $this->parametrosLaboratorio = $parametros->buscar();
    }

    public function buscar($filtros)
    {
        return [
            'triagens' => $this->repository->buscarTriagens($filtros),
            'motivosRejeicaoAmostra' => $this->repository->buscarMotivosRejeicaoAmostra($filtros),
        ];
    }
    public function buscarPendenciasTriagem($filtros)
    {
        $pendenciasTriagem = $this->repository->buscarPendenciasTriagem($filtros);
        $pendencias = [];
        foreach ($pendenciasTriagem as $pendenciaTriagem) {
            $pendencias[] = [
                "requisicao" => $pendenciaTriagem['requisicao'],
                "laboratorio" => $pendenciaTriagem['laboratorio'],
                "paciente" => strlen($pendenciaTriagem['paciente']) > 60 ?
                    substr($pendenciaTriagem['paciente'], 0, 60)."...": $pendenciaTriagem['paciente'],
                "amostra" => $pendenciaTriagem['amostra'],
                "data_coleta" => $pendenciaTriagem['data_coleta'],
                "motivo_rejeicao_amostra" => $pendenciaTriagem['motivo_rejeicao_amostra']
            ];
        }
        return $pendencias;
    }


    public function buscarRequisicoes($filtros)
    {
        return $this->repository->buscarRequisicoes($filtros);
    }

    public function processar($dados)
    {
        try {
            foreach ($dados as $dado) {
                $situacao = "45 - Em processamento";
                $motivoRejeicao = null;
                $outroMotivoRejeicao = null;
                $idUsuario = $dado->idUsuario;

                if (!$dado->recebida) {
                    $situacao = "80 - Amostra Rejeitada - Nova Coleta";
                    $motivoRejeicao = $dado->motivoRejeicao;
                    if ($motivoRejeicao == 8) {
                        $outroMotivoRejeicao = $dado->outroMotivoRejeicao;
                    }
                }

                foreach ($dado->itensRequisicao as $itemRequisicao) {
                    $this->repository->cadastrarTriagem([
                        'itemRequisicao' => $itemRequisicao,
                        'idUsuario' => $idUsuario
                    ]);

                    $this->repository->alterarSituacaoItemAmostra([
                        'itemRequisicao' => $itemRequisicao,
                        'situacao' => $situacao,
                        'motivoRejeicao' => $motivoRejeicao,
                        'outroMotivoRejeicao' =>mb_convert_encoding($outroMotivoRejeicao, 'ISO-8859-1', 'UTF-8')
                    ]);
                }

                if ($dado->recebida) {
                    $this->gerarArquivoPedidos($dado);
                }
            }
        } catch (Exception $erro) {
            throw new Exception($erro);
        }
    }

    public function gerarArquivoPedidos($dados)
    {

        $laboratorioService = new LaboratorioService(new LaboratorioRepository(new \cl_lab_laboratorio()));
        
        $interfaceado = $laboratorioService->verificaLaboratorioInterfaceadoRequisicao(
            $dados->codigoRequisicao
        );

        /**
         * verifica se o parametro da integracao com a luckman esta configurada
        */
        $interfaceadoLuckman = $interfaceado == 't'
           && (int)$this->parametrosLaboratorio->getIntegracao() === ParametrosLaboratorio::INTEGRACAO_LUCKMANN;
                
        /**
         * verifica se o parametro da integracao com o infinity esta configurado
         */
        $interfaceadoInfinity =  $interfaceado == 't'
            && $this->parametrosLaboratorio->getIntegracaoInfinity() == ParametrosLaboratorio::INTEGRACAO_INFINITY;
                        
        $tagXmlService = new TagXML(new TagsXMLCollection(), new File(), ParametrosEnum::PEDIDOS);
        $tagsXmlCollection = $tagXmlService->criarInstancias();
        
        $pedidoService = new PedidoService(new \RequisicaoLaboratorial($dados->codigoRequisicao));
            
        /**
        * Verifica se o setor do item corresponde a um fluxo do luckman. Cada
        * fluxo dependerá do setor
        */
        if ($this->verificarSetorItemRequisicao($dados->itensRequisicao, $this->setoresInfinity)
            && $interfaceadoInfinity
        ) {
            $this->gerarArquivoPedidosInfinity(
                $pedidoService->getDados($dados->itensRequisicao),
                $tagsXmlCollection
            );
        } elseif ($interfaceadoLuckman) {
            $this->gerarArquivoPedidosLuckman(
                $pedidoService->getDados($dados->itensRequisicao),
                $tagsXmlCollection
            );
        }
    }

    public function verificarSetorItemRequisicao($itensRequisicao, $setores)
    {
        $filtros = [
            'itensRequisicao' => $itensRequisicao,
            'setores' => $setores
        ];

        $setores = $this->repository->getSetoresItemRequisicao($filtros);

        if (count($setores) > 0) {
            return true;
        }
        return false;
    }

    public function gerarArquivoPedidosLuckman($dados, TagsXMLCollection $tagsXmlCollection)
    {

        $arquivo_outros = null;
        $arquivo_hemato = null;
        $arquivo_urinalise = null;

        $dados_outros = $dados;
        $dados_outros["Exames"] = [];
        $dados_hemato = $dados;
        $dados_hemato["Exames"] = [];
        $dados_urinanalise = $dados;
        $dados_urinanalise["Exames"] = [];
        
        foreach ($dados["Exames"] as $exame) {
            if (substr($exame["Amostra"], -2) == "06") {
                $dados_hemato["Exames"][] = $exame;
            } elseif (substr($exame["Amostra"], -2) == "03") {
                $dados_urinanalise["Exames"][] = $exame;
            } else {
                $dados_outros["Exames"][] = $exame;
            }
        }

        $pedidoConverter = new Pedido($tagsXmlCollection);
        if (count($dados_outros["Exames"])) {
            $arquivo_outros = $pedidoConverter->gerarArquivoPedidos($dados_outros, false, false);
        }
        if (count($dados_hemato["Exames"])) {
            $arquivo_hemato = $pedidoConverter->gerarArquivoPedidos($dados_hemato, true, false);
        }
        if (count($dados_urinanalise["Exames"])) {
            $arquivo_urinalise = $pedidoConverter->gerarArquivoPedidos($dados_urinanalise, false, true);
        }

        $parametrosService = new ParametrosService(ParametrosEnum::JSON_CONFIGURACOES);
        $parametrosModel = $parametrosService->getParametros();
        $integra = new IntegraPedidos($parametrosModel, ParametrosEnum::PEDIDOS);
        
        if (isset($arquivo_outros) && !empty($arquivo_outros)) {
            $integra->enviarArquivo($arquivo_outros);
        }
        if (isset($arquivo_hemato) && !empty($arquivo_hemato)) {
            $integra->enviarArquivo($arquivo_hemato);
        }
        if (isset($arquivo_urinalise) && !empty($arquivo_urinalise)) {
            $integra->enviarArquivo($arquivo_urinalise);
        }
    }

    public function gerarArquivoPedidosInfinity($dados, TagsXMLCollection $tagsXmlCollection)
    {
        $arquivoInfinity = null;
        $dadosInfinity = $dados;
        $dadosInfinity["Exames"] = [];
        
        foreach ($dados["Exames"] as $exame) {
            $dadosInfinity["Exames"][] = $exame;
        }
        
        $pedidoConverter = new Pedido($tagsXmlCollection);
        
        if (count($dadosInfinity["Exames"])) {
            $arquivoInfinity = $pedidoConverter->gerarArquivoPedidosInfinity($dadosInfinity);
        }
        
        $parametrosService = new ParametrosService(
            ParametrosEnum::JSON_CONFIGURACOES,
            ParametrosEnum::JSON_CONFIGURACOES_INFINITY
        );

        $parametrosModel = $parametrosService->getParametros();
        $integra = new IntegraPedidos($parametrosModel, ParametrosEnum::PEDIDOS_INIFINITY);
        
        if (isset($arquivoInfinity) && !empty($arquivoInfinity)) {
            $integra->enviarArquivo($arquivoInfinity);
            $caminhoBackup = ParametrosIntegracao::CAMINHO_BACKUP_PEDIDO_INFINITY;
            $caminhoBackup = Storage::path($caminhoBackup);
            $integra->salvarBackupPedidoInfinity($arquivoInfinity, $caminhoBackup);
        }
    }
}
