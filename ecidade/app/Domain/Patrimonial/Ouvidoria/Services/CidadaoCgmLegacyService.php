<?php

namespace App\Domain\Patrimonial\Ouvidoria\Services;

use App\Domain\Patrimonial\Ouvidoria\Model\Cidadao\Cidadao;
use CgmBase;
use CgmFactory;
use CgmFisico;
use CgmJuridico;
use Exception;
use InstituicaoRepository;

/**
 * Classe CidadaoCgmLegacyService
 * Gerencia as interações de um atendimento com um processo
 * TODO: Refatorar todos métodos e remover a herança
 */
class CidadaoCgmLegacyService
{

    /**
     * Função que retorna um cgm a partir de um atendimento
     *
     * @param \stdClass $solicitacaoOuvidoria
     * @return CgmBase
     */
    public function getCgmBySolicitacao($solicitacaoOuvidoria)
    {
        if (empty($solicitacaoOuvidoria->cidadao)) {
            $cgm = InstituicaoRepository::getInstituicaoPrefeitura()->getCgm();
        } else {
            $cgm = CgmFactory::getInstanceByCnpjCpf($solicitacaoOuvidoria->cidadao->getCnpjCpf());
            if (!$cgm) {
                $cgm = $this->criaCgm($solicitacaoOuvidoria->cidadao);
            }
        }

        return $cgm;
    }

    /**
     * Função que cria um cgm a partir de um cidadao
     *
     * @param Cidadao $cidadao
     * @return CgmBase|CgmFisico|CgmJuridico|object
     * @throws Exception
     */
    private function criaCgm(Cidadao $cidadao)
    {
        if (strlen(trim($cidadao->getCnpjCpf())) == '11') {
            $oCgm = CgmFactory::getInstanceByType(CgmFactory::FISICO);
            $oCgm->setCpf($cidadao->getCnpjCpf());
            $oCgm->setSexo(strtoupper($cidadao->getSexo()));
            $oCgm->setDataNascimento($cidadao->getDataNascimento());
        } elseif (strlen(trim($cidadao->getCnpjCpf())) == '14') {
            $oCgm = CgmFactory::getInstanceByType(CgmFactory::JURIDICO);
            $oCgm->setCnpj($cidadao->getCnpjCpf());
            $oCgm->setNomeFantasia(
                \DBString::upperCaseRemoveCaracteresEspeciais(substr($cidadao->getNome(), 0, 100))
            );
        } else {
            throw new Exception("CPF/CNPJ ou objeto inválido!");
        }

        $endereco = (!empty($cidadao->getEndereco())) ? $cidadao->getEndereco() : 'Nao Informado';

        $oCgm->setNome(
            \DBString::upperCaseRemoveCaracteresEspeciais(substr($cidadao->getNome(), 0, 40))
        );
        $oCgm->setNomeCompleto(\DBString::upperCaseRemoveCaracteresEspeciais(
            substr($cidadao->getNome(), 0, 100)
        ));
        $oCgm->setUf(strtoupper($cidadao->getUf()));
        $oCgm->setCep(strtoupper(trim(str_replace("-", "", $cidadao->getCep()))));
        $oCgm->setBairro(strtoupper($cidadao->getBairro()));
        $oCgm->setNumero(strtoupper($cidadao->getNumero()));
        $oCgm->setMunicipio(strtoupper($cidadao->getMunicipio()));
        $oCgm->setLogradouro(strtoupper($endereco));
        $oCgm->setComplemento(strtoupper($cidadao->getComplemento()));

        $oCgm->save();

        return $oCgm;
    }


    /**
     * @param $oCgm
     * @param \cl_cidadao $cidadao
     * @param $dadosEauth
     * @return mixed
     * @throws Exception
     */
    private function atualizarCgmEauth($oCgm, \cl_cidadao $cidadao, $dadosEauth)
    {

        if (strlen(trim($cidadao->ov02_cnpjcpf)) == '11') {
            $oCgm->setDataNascimento($cidadao->ov02_datanascimento);
            if (!empty($dadosEauth->pai)) {
                $oCgm->setNomePai(
                    \DBString::upperCaseRemoveCaracteresEspeciais(substr($dadosEauth->pai, 0, 40))
                );
            }
            if (!empty($dadosEauth->mae)) {
                $oCgm->setNomeMae(
                    \DBString::upperCaseRemoveCaracteresEspeciais(
                        substr($dadosEauth->mae, 0, 40)
                    )
                );
            }
        } elseif (strlen(trim($cidadao->ov02_cnpjcpf)) == '14') {
            $oCgm->setNomeFantasia(strtoupper(substr($cidadao->ov02_nome, 0, 100)));
        } else {
            throw new Exception("CPF/CNPJ ou objeto inválido!");
        }

        return $this->saveCGM($cidadao, $oCgm, $dadosEauth);
    }


    /**
     * @param \cl_cidadao $cidadao
     * @param $dadosEauth
     * @return CgmBase|CgmFisico|CgmJuridico|object
     * @throws Exception
     */
    private function criaCgmEauth(\cl_cidadao $cidadao, $dadosEauth)
    {
        if (strlen(trim($cidadao->ov02_cnpjcpf)) == '11') {
            $oCgm = CgmFactory::getInstanceByType(CgmFactory::FISICO);
            $oCgm->setCpf($cidadao->ov02_cnpjcpf);
            $oCgm->setDataNascimento($cidadao->ov02_datanascimento);
            if (!empty($dadosEauth->pai)) {
                $oCgm->setNomePai(
                    \DBString::upperCaseRemoveCaracteresEspeciais(substr($dadosEauth->pai, 0, 40))
                );
            }
            if (!empty($dadosEauth->mae)) {
                $oCgm->setNomeMae(
                    \DBString::upperCaseRemoveCaracteresEspeciais(substr($dadosEauth->mae, 0, 40))
                );
            }
        } elseif (strlen(trim($cidadao->ov02_cnpjcpf)) == '14') {
            $oCgm = CgmFactory::getInstanceByType(CgmFactory::JURIDICO);
            $oCgm->setCnpj($cidadao->ov02_cnpjcpf);
            $oCgm->setNomeFantasia(
                \DBString::upperCaseRemoveCaracteresEspeciais(substr($cidadao->ov02_nome, 0, 100))
            );
        } else {
            throw new Exception("CPF/CNPJ ou objeto inválido!");
        }

        return $this->saveCGM($cidadao, $oCgm, $dadosEauth);
    }


    /**
     * @param $solicitacaoOuvidoria
     * @param $dadosEauth
     * @return CgmBase|CgmFactory
     * @throws Exception
     */
    public function getCgmBySolicitacaoPrimeiroACesso($solicitacaoOuvidoria, $dadosEauth)
    {
        /**
         * CADASTRA OU ATUALIZA CIDADÃO
         */
        $cidadao = new \cl_cidadao();
        $sqlCidadao = $cidadao->sql_query_file(
            null,
            null,
            "*",
            null,
            "ov02_cnpjcpf='{$dadosEauth->cgccpf}'"
        );

        $objCidadao = false;
        $rsCidadao = $cidadao->sql_record($sqlCidadao);
        if ($rsCidadao) {
            $objCidadao = pg_fetch_object($rsCidadao);
        }
        $cidadao->ov02_ativo = 't';
        $cidadao->ov02_nome = \DBString::upperCaseRemoveCaracteresEspeciais($dadosEauth->nome);
        $cidadao->ov02_endereco = $dadosEauth->logradouro;
        $cidadao->ov02_numero = $dadosEauth->numero;
        $cidadao->ov02_compl = $dadosEauth->complemento;
        $cidadao->ov02_bairro = $dadosEauth->bairro;
        $cidadao->ov02_munic = $dadosEauth->municipio;
        $cidadao->ov02_cep = $dadosEauth->cep;
        $cidadao->ov02_data = Date('Y-m-d');
        $cidadao->ov02_situacaocidadao = 4;
        if (isset($dadosEauth->sexo) && !empty($dadosEauth->sexo)) {
            $cidadao->ov02_sexo = $dadosEauth->sexo;
        }
        if (!empty($dadosEauth->uf)) {
            $cidadao->ov02_uf = $dadosEauth->uf;
        }
        $cidadao->ov02_cnpjcpf = $dadosEauth->cgccpf;
        if (!empty($dadosEauth->data)) {
            $cidadao->ov02_datanascimento = $dadosEauth->data;
        }
        if ($objCidadao) {
            $cidadao->ov02_sequencial = $objCidadao->ov02_sequencial;
            $cidadao->ov02_seq = $objCidadao->ov02_seq;
            if (!$cidadao->alterar($objCidadao->ov02_sequencial, $cidadao->ov02_seq)) {
                throw new Exception("Erro ao atualizar dados do cidadão");
            }
        } else {
            $cidadao->ov02_seq = 1;
            if (!$cidadao->incluir(null, null)) {
                throw new Exception("Erro ao cadastrar dados do cidadão");
            }
            $sql_ultimo_sequencial = pg_query("SELECT CURRVAL('cidadao_ov02_sequencial_seq')");
            $sequencial = pg_fetch_object($sql_ultimo_sequencial)->currval;
            $cidadao->ov02_sequencial = $sequencial;
        }

        /**
         * RELACIONA O CIDADÃO AO ATENDIMENTO
         */
        $atendimentoCidadao = new \cl_ouvidoriaatendimentocidadao();
        $atendimentoCidadao->ov10_cidadao = $cidadao->ov02_sequencial;
        $atendimentoCidadao->ov10_ouvidoriaatendimento = $solicitacaoOuvidoria->sequencial;
        $atendimentoCidadao->ov10_seq = $cidadao->ov02_seq;

        if (!$atendimentoCidadao->incluir(null)) {
            throw new Exception("Erro ao vincular cidadao ao atendimento");
        }

        /**
         * VERIFICA SE POSSUI CGM E ATUALIZA OS DADOS CASO NÃO TENHA CRIA UM NOVO CGM PARA O CIDADÃO
         */
        $cgm = CgmFactory::getInstanceByCnpjCpf($dadosEauth->cgccpf);

        if ($cgm) {
            $cgm = $this->atualizarCgmEauth($cgm, $cidadao, $dadosEauth);
        } else {
            $cgm = $this->criaCgmEauth($cidadao, $dadosEauth);
        }

        return $cgm;
    }

    /**
     * @param \cl_cidadao $cidadao
     * @param $oCgm
     * @param $dadosEauth
     * @return mixed
     */
    private function saveCGM(\cl_cidadao $cidadao, CgmBase $oCgm, $dadosEauth)
    {
        $endereco = (!empty($cidadao->ov02_endereco)) ? $cidadao->ov02_endereco : 'Nao Informado';
        if ((bool)env("PROCESSO_ELETRONICO_NAO_ATUALIZA_NOME_CGM") !== true or empty($oCgm->getCodigo())) {
            $oCgm->setNome(\DBString::upperCaseRemoveCaracteresEspeciais(substr($cidadao->ov02_nome, 0, 40)));
            $oCgm->setNomeCompleto(
                \DBString::upperCaseRemoveCaracteresEspeciais(
                    substr($cidadao->ov02_nome, 0, 100)
                )
            );
        }

        $oCgm->setUf(strtoupper($cidadao->ov02_uf));
        $oCgm->setCep(strtoupper($cidadao->ov02_cep));
        $oCgm->setBairro(strtoupper($cidadao->ov02_bairro));
        $oCgm->setNumero(strtoupper($cidadao->ov02_numero));
        $oCgm->setMunicipio(strtoupper($cidadao->ov02_munic));
        $oCgm->setLogradouro(strtoupper($endereco));
        $oCgm->setComplemento(strtoupper($cidadao->ov02_compl));
        $oCgm->setEmail(trim($dadosEauth->email));
        $oCgm->setTelefone($dadosEauth->telefone);
        $oCgm->setCelular($dadosEauth->celular);
        if (isset($dadosEauth->sexo)) {
            $oCgm->setSexo($dadosEauth->sexo);
        }
        $oCgm->save();

        return $oCgm;
    }
}
