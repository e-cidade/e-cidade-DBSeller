<?php

namespace ECidade\Patrimonial\Protocolo\Servicos;

use CgmFactory;
use endereco;
use ECidade\Patrimonial\Protocolo\Processo\ProcessoEletronico\Helper\ProcessoEletronicoHelper;

// /var/www/e-cidade/prot1_cadgeralmunic.RPC.php
// incluirAlterar

/**
 * Class InclusaoCgmLegacy
 * @package ECidade\Patrimonial\Protocolo\Servicos
 */
class InclusaoCgmLegacy
{

    public function processaDadosCgm(\Stdclass $dados)
    {
        $sCgcCpf = null;
        if (array_key_exists('cpf', $dados) && !empty($dados->cpf->value)) {
            $sCgcCpf = $dados->cpf->value;
        }

        if (array_key_exists('cnpj', $dados) && !empty($dados->cnpj->value)) {
            $sCgcCpf = $dados->cnpj->value;
        }

        if (empty($sCgcCpf)) {
            if (array_key_exists('cpf_cnpj', $dados) && !empty($dados->cpf_cnpj)) {
                $sCgcCpf = $dados->cpf_cnpj->value;
            }
        }

        $sCgcCpf = preg_replace('/\D/', "", $sCgcCpf);

        $cgm = $this->getCgmByCpfCnpj($sCgcCpf);

        if (!$cgm) {
            $cgm = $this->criaCgm($dados);
        } else {
            $cgm = $this->atualizarCgm($dados, $cgm);
        }

        return $cgm;
    }

    public function consultarDadosCgm(\Stdclass $dados)
    {
        $sCgcCpf = null;

        if (array_key_exists('cpf', $dados) && !empty($dados->cpf->value)) {
            $sCgcCpf = $dados->cpf->value;
        }

        if (array_key_exists('cnpj', $dados) && !empty($dados->cnpj->value)) {
            $sCgcCpf = $dados->cnpj->value;
        }

        if (empty($sCgcCpf)) {
            if (array_key_exists('cpf_cnpj', $dados) && !empty($dados->cpf_cnpj)) {
                $sCgcCpf = $dados->cpf_cnpj->value;
            }
        }

        $sCgcCpf = preg_replace('/\D/', "", $sCgcCpf);

        return $this->getCgmByCpfCnpj($sCgcCpf);
    }

    public function atualizarCgm(\Stdclass $dados, $oCgm)
    {
        \db_query("select fc_putsession('DB_habilita_trigger_endereco','false')");

        $sCgcCpf = '';
        $cpf = null;
        $cnpj = null;
        if (array_key_exists('cpf', $dados) && !empty($dados->cpf)) {
            $cpf = $this->getAtribute($dados->cpf);
        }

        if (array_key_exists('cnpj', $dados) && !empty($dados->cnpj)) {
            $cnpj = $this->getAtribute($dados->cnpj);
        }

        $sCgcCpf = !empty($cpf) ? $cpf : $cnpj;

        if (empty($sCgcCpf)) {
            if (array_key_exists('cpf_cnpj', $dados) && !empty($dados->cpf_cnpj)) {
                $sCgcCpf = $this->getAtribute($dados->cpf_cnpj);
            }
        }

        $sCgcCpf = preg_replace('/\D/', "", $sCgcCpf);

        if (!empty($dados->tipo_empresa)) {
            $tipoEmpresaCodigo = $this->extrairCodigoTipoEmpresa($dados->tipo_empresa);
        }

        if (strlen(trim($sCgcCpf)) == '11') {
            $oCgm->setCpf($sCgcCpf);
            $oCgm->setSexo((isset($dados->sexo) ? $this->getAtribute($dados->sexo) : ""));
            $oCgm->setDataNascimento((isset($dados->nascimento) ? $this->getAtribute($dados->nascimento) : ""));
            $oCgm->setNacionalidade((isset($dados->nacionalidade) ? $this->getAtribute($dados->nacionalidade) : 0));
            $oCgm->setEstadoCivil((isset($dados->estado_civil) ? $this->getAtribute($dados->estado_civil) : 0));
            $oCgm->setTipoEmpresa(!empty($tipoEmpresaCodigo) ? $tipoEmpresaCodigo : 31);
            if (array_key_exists('nome', $dados)) {
                $nome = $this->getAtribute($dados->nome);
            } elseif (array_key_exists('nome_fantasia', $dados)) {
                $nome = $this->getAtribute($dados->nome_fantasia);
            } elseif (array_key_exists('razao_social', $dados)) {
                $nome = $this->getAtribute($dados->razao_social);
            }
        } elseif (strlen(trim($sCgcCpf)) == '14') {
            $oCgm->setCnpj($sCgcCpf);
            $oCgm->setTipoEmpresa(!empty($tipoEmpresaCodigo) ? $tipoEmpresaCodigo : 36);
            $nomeFantasia = null;
            if (array_key_exists('nome_fantasia', $dados)) {
                $nomeFantasia = $this->getAtribute($dados->nome_fantasia);
            }

            if (array_key_exists('inscricao_estadual', $dados)
                && (is_null($this->getAtribute($dados->inscricao_estadual))
                    || $this->getAtribute($dados->inscricao_estadual) != ''
                )
            ) {
                $oCgm->setInscricaoEstadual(str_replace('.', '', $this->getAtribute($dados->inscricao_estadual)));
            }

            $nome = $nomeFantasia;
            $oCgm->setNomeFantasia(
                \DBString::upperCaseRemoveCaracteresEspeciais(substr($nomeFantasia, 0, 100))
            );

            if (array_key_exists('razao_social', $dados)) {
                $nome = $this->getAtribute($dados->razao_social);
            } elseif (array_key_exists('nome', $dados)) {
                $nome = $this->getAtribute($dados->nome);
            }
        } else {
            throw new \Exception("CPF/CNPJ ou objeto inválido!");
        }

        if (array_key_exists('endereco', $dados)) {
            $endereco = $dados->endereco;
        } else {
            $endereco = $dados;
        }

        if (array_key_exists('telefone', $dados)) {
            $oCgm->setTelefone($this->getAtribute($dados->telefone));
        } elseif (array_key_exists('endereco', $dados)
            && array_key_exists('telefone', $dados->endereco)
        ) {
            $oCgm->setTelefone($this->getAtribute($dados->endereco->telefone));
        }

        if (array_key_exists('celular', $dados)) {
            $oCgm->setCelular($this->getAtribute($dados->celular));
        } elseif (array_key_exists('endereco', $dados)
            && array_key_exists('celular', $dados->endereco)
        ) {
            $oCgm->setCelular($this->getAtribute($dados->endereco->celular));
        }

        $uf = (array_key_exists('estado', $endereco)) ? substr($this->getAtribute($endereco->estado), 0, 2) : null;
        $cep = (array_key_exists('cep', $endereco)) ? str_replace("-", "", $this->getAtribute($endereco->cep)) : null;
        $bairro = (array_key_exists('bairro', $endereco)) ? $this->getAttributeDescription($endereco->bairro) : null;
        $numero = (array_key_exists('numero', $endereco)) ? $this->getAtribute($endereco->numero) : null;
        $municipio = (array_key_exists('municipio', $endereco)) ?
            $this->getAttributeDescription($endereco->municipio) :
            null;
        $logradouro = (array_key_exists('logradouro', $endereco)) ?
            $this->getAttributeDescription($endereco->logradouro) :
            null;
        $complemento = (array_key_exists('complemento', $endereco)) ? $this->getAtribute($endereco->complemento) : null;

        if (empty($municipio) || empty($uf)) {
            /**
             * Caso alguns dados não sejam informados, irá buscar o que já está vinculado á matrícula
             */
            if (!empty($endereco->matricula) && is_object($endereco->matricula)) {
                $matricula = $this->getAtribute($endereco->matricula);

                $daoIssBase = new \cl_issbase();
                $sqlIssBase = $daoIssBase->sql_query($matricula, 'z01_munic, z01_uf');
                $rsIssBase = pg_query($sqlIssBase);

                if (!$rsIssBase) {
                    throw new \Exception('Não foi possvel buscar os dados da matírcula.');
                }

                $dadosMatricula = pg_fetch_object($rsIssBase);

                $uf = $dadosMatricula->z01_uf;
                $municipio = $dadosMatricula->z01_munic;
            }
        }

        $oCgm->setNome(
            \DBString::upperCaseRemoveCaracteresEspeciais(substr($nome, 0, 40))
        );

        $oCgm->setNomeCompleto(
            \DBString::upperCaseRemoveCaracteresEspeciais(
                substr($nome, 0, 100)
            )
        );

        if (array_key_exists('email', $dados) && !empty($dados->email)) {
            $oCgm->setEmail($this->getAtribute($dados->email));
        }

        if (array_key_exists('nomeMae', $dados) && !empty($dados->nomeMae)) {
            $oCgm->setNomeMae($this->getAtribute($dados->nomeMae));
        }

        $oCgm->setCep(mb_strtoupper($cep));
        $oCgm->setBairro(mb_strtoupper($bairro));
        $oCgm->setNumero(mb_strtoupper($numero));
        $oCgm->setMunicipio(mb_strtoupper($municipio));
        $oCgm->setLogradouro(mb_strtoupper($logradouro));
        $oCgm->setComplemento(mb_strtoupper($complemento));

        $oCgm->save();

        return $oCgm;
    }

    private function getCgmByCpfCnpj($cpfCnpj)
    {
        return CgmFactory::getCgmByCnpjCpf($cpfCnpj);
    }

    private function criaCgm($dados)
    {
        \db_query("select fc_putsession('DB_habilita_trigger_endereco','false')");

        $sCgcCpf = '';
        $cpf = null;
        $cnpj = null;
        if (array_key_exists('cpf', $dados) && !empty($dados->cpf)) {
            $cpf = $this->getAtribute($dados->cpf);
        }

        if (array_key_exists('cnpj', $dados) && !empty($dados->cnpj)) {
            $cnpj = $this->getAtribute($dados->cnpj);
        }

        $sCgcCpf = !empty($cpf) ? $cpf : $cnpj;

        if (empty($sCgcCpf)) {
            if (array_key_exists('cpf_cnpj', $dados) && !empty($dados->cpf_cnpj)) {
                $sCgcCpf = $this->getAtribute($dados->cpf_cnpj);
            }
        }

        $sCgcCpf = preg_replace('/\D/', "", $sCgcCpf);

        if (!empty($dados->tipo_empresa)) {
            $tipoEmpresa = $this->extrairCodigoTipoEmpresa($dados->tipo_empresa);
        }

        if (strlen(trim($sCgcCpf)) == '11') {
            $oCgm = CgmFactory::getInstanceByType(CgmFactory::FISICO);
            $oCgm->setCpf($sCgcCpf);
            $oCgm->setSexo((isset($dados->sexo) ? $this->getAtribute($dados->sexo) : ""));
            $oCgm->setDataNascimento((isset($dados->nascimento) ? $this->getAtribute($dados->nascimento) : ""));
            $oCgm->setNacionalidade((isset($dados->nacionalidade) ? $this->getAtribute($dados->nacionalidade) : 0));
            $oCgm->setEstadoCivil((isset($dados->estado_civil) ? $this->getAtribute($dados->estado_civil) : 0));
            $oCgm->setTipoEmpresa(!empty($tipoEmpresa) ? $tipoEmpresa : 31);

            if (array_key_exists('nome', $dados)) {
                $nome = $this->getAtribute($dados->nome);
            } elseif (array_key_exists('nome_fantasia', $dados)) {
                $nome = $this->getAtribute($dados->nome_fantasia);
            } elseif (array_key_exists('razao_social', $dados)) {
                $nome = $this->getAtribute($dados->razao_social);
            }
        } elseif (strlen(trim($sCgcCpf)) == '14') {
            $oCgm = CgmFactory::getInstanceByType(CgmFactory::JURIDICO);
            $oCgm->setCnpj($sCgcCpf);
            $oCgm->setTipoEmpresa(!empty($tipoEmpresa) ? $tipoEmpresa : 36);

            $nomeFantasia = null;
            if (array_key_exists('nome_fantasia', $dados)) {
                $nomeFantasia = $this->getAtribute($dados->nome_fantasia);
            }

            if (array_key_exists('inscricao_estadual', $dados)
                && (is_null($this->getAtribute($dados->inscricao_estadual))
                    || $this->getAtribute($dados->inscricao_estadual) != ''
                )
            ) {
                $oCgm->setInscricaoEstadual(str_replace('.', '', $this->getAtribute($dados->inscricao_estadual)));
            }

            $nome = $nomeFantasia;
            $oCgm->setNomeFantasia(
                \DBString::upperCaseRemoveCaracteresEspeciais(substr($nomeFantasia, 0, 100))
            );

            if (array_key_exists('razao_social', $dados)) {
                $nome = $this->getAtribute($dados->razao_social);
            } elseif (array_key_exists('nome', $dados)) {
                $nome = $this->getAtribute($dados->nome);
            }
        } else {
            throw new \Exception("CPF/CNPJ ou objeto inválido!");
        }

        if (array_key_exists('endereco', $dados)) {
            $endereco = $dados->endereco;
        } else {
            $endereco = $dados;
        }

        if (array_key_exists('email', $dados) && !empty($dados->email)) {
            $oCgm->setEmail($this->getAtribute($dados->email));
        }

        if (array_key_exists('telefone', $dados)) {
            $oCgm->setTelefone($this->getAtribute($dados->telefone));
        } elseif (array_key_exists('endereco', $dados)
            && array_key_exists('telefone', $dados->endereco)
        ) {
            $oCgm->setTelefone($this->getAtribute($dados->endereco->telefone));
        }

        if (array_key_exists('celular', $dados)) {
            $oCgm->setCelular($this->getAtribute($dados->celular));
        } elseif (array_key_exists('endereco', $dados)
            && array_key_exists('celular', $dados->endereco)
        ) {
            $oCgm->setCelular($this->getAtribute($dados->endereco->celular));
        }

        $uf = (array_key_exists('estado', $endereco)) ? substr($this->getAtribute($endereco->estado), 0, 2) : null;
        $cep = (array_key_exists('cep', $endereco)) ? str_replace("-", "", $this->getAtribute($endereco->cep)) : null;
        $bairro = (array_key_exists('bairro', $endereco)) ? $this->getAttributeDescription($endereco->bairro) : null;
        $numero = (array_key_exists('numero', $endereco)) ? $this->getAtribute($endereco->numero) : null;
        $municipio = (array_key_exists('municipio', $endereco)) ?
            $this->getAttributeDescription($endereco->municipio) :
            null;
        $logradouro = (array_key_exists('logradouro', $endereco)) ?
            $this->getAttributeDescription($endereco->logradouro) :
            null;
        $complemento = (array_key_exists('complemento', $endereco)) ? $this->getAtribute($endereco->complemento) : null;

        if (empty($municipio) || empty($uf)) {
            /**
             * Caso alguns dados não sejam informados, irá buscar o que já está vinculado á matrícula
             */
            if (!empty($endereco->matricula) && is_object($endereco->matricula)) {
                $matricula = $this->getAtribute($endereco->matricula);

                $daoIssBase = new \cl_issbase();
                $sqlIssBase = $daoIssBase->sql_query($matricula, 'z01_munic, z01_uf');
                $rsIssBase = pg_query($sqlIssBase);

                if (!$rsIssBase) {
                    throw new \Exception('Não foi possível buscar os dados da matrícula.');
                }

                $dadosMatricula = pg_fetch_object($rsIssBase);

                $uf = $dadosMatricula->z01_uf;
                $municipio = $dadosMatricula->z01_munic;
            }
        }

        $oCgm->setNome(
            \DBString::upperCaseRemoveCaracteresEspeciais(substr($nome, 0, 40))
        );
        $oCgm->setNomeCompleto(\DBString::upperCaseRemoveCaracteresEspeciais(
            substr($nome, 0, 100)
        ));
        $oCgm->setUf(mb_strtoupper($uf));

        $oCgm->setCep(mb_strtoupper($cep));
        $oCgm->setBairro(mb_strtoupper($bairro));
        $oCgm->setNumero(mb_strtoupper($numero));
        $oCgm->setMunicipio(mb_strtoupper($municipio));
        $oCgm->setLogradouro(mb_strtoupper($logradouro));
        $oCgm->setComplemento(mb_strtoupper($complemento));
        $oCgm->setCadastro(date("Y-m-d"));

        if (array_key_exists('nomeMae', $dados) && !empty($dados->nomeMae)) {
            $oCgm->setNomeMae($this->getAtribute($dados->nomeMae));
        }

        $oCgm->save();

        return $oCgm;
    }

    private function getAttributeDescription($attribute)
    {
        if (is_object($attribute)) {
            if (is_object($attribute->value)) {
                return trim($attribute->value->descricao);
            }

            return $attribute->value;
        }

        return $attribute;
    }

    private function getAtribute($var)
    {
        return ProcessoEletronicoHelper::getValueJson($var->value);
    }

    private function extrairCodigoTipoEmpresa($tipoEmpresa)
    {

        if (is_string($tipoEmpresa) or is_numeric($tipoEmpresa)) {
            return $tipoEmpresa;
        }

        if ($tipoEmpresa instanceof \stdClass) {
            if (!empty($tipoEmpresa->value) and !empty($tipoEmpresa->value->codigo)) {
                return $tipoEmpresa->value->codigo;
            }
        }

        return false;
    }
}
