<?php

namespace App\Domain\Configuracao\DocumentosTemplate\Reports\Cadastro\Helpers;

use \cl_iptubase;
use cl_propri;
use \db_utils;

class FiltroDadosCertidoesHelper
{
    public function adicionaFiltros($dados)
    {
        $dados = $this->traduzNumeroParaExtensoDados($dados);
        $dados = $this->formataNumeroDados($dados);
        $dados = $this->transformaValoresZeradosEmVazio($dados);
        $dados = $this->adicionaCifraoMoedaDados($dados);
        $dados = $this->adicionaVariaveis($dados);
        $dados = $this->adicionaMascarasCpfCnpj($dados);
        $dados = $this->adicionaVirgulasDados($dados);
        $dados = $this->adicionaOcorrencias($dados);
        $dados = $this->adicionaInformacoesConstrucoes($dados);
        $dados = $this->adicionaOutrosProprietariosMatricula($dados);

        return $dados;
    }

    private function formataNumeroDados($dados)
    {
        $listaPropriedades = ['vlr_terreno', 'vlr_construcao'];

        foreach ($listaPropriedades as $propriedade) {
            $dados[$propriedade] = $this->formataNumero($dados[$propriedade]);
        }

        return $dados;
    }

    private function transformaValoresZeradosEmVazio($dados)
    {
        $listaPropriedades = ['vlr_terreno', 'vlr_construcao'];

        foreach ($listaPropriedades as $propriedade) {
            $dados[$propriedade] = (int)$dados[$propriedade] == 0 ? '' : $dados[$propriedade];
        }

        return $dados;
    }

    private function traduzNumeroParaExtensoDados($dados)
    {
        $listaPropriedades = [
            ['vlr_terreno', 'vlr_terreno_extenso'],
            ['vlr_construcao', 'vlr_construcao_extenso']
        ];

        foreach ($listaPropriedades as $propriedade) {
            $variavelPorExtenso = '';
            $propriedadeValida = isset($dados, $propriedade[0])
                && $dados[$propriedade[0]]
                && is_numeric($dados[$propriedade[0]]);

            if ($propriedadeValida) {
                $variavelPorExtenso = $this->traduzNumeroParaExtenso(
                    $dados[$propriedade[0]]
                );
            }

            if (strtolower($variavelPorExtenso) == 'zero') {
                $variavelPorExtenso = '';
            }

            $dados[$propriedade[1]] = $variavelPorExtenso;
        }

        return $dados;
    }

    private function adicionaCifraoMoedaDados($dados)
    {
        $listaPropriedades = ['vlr_terreno', 'vlr_construcao'];

        foreach ($listaPropriedades as $propriedade) {
            $dados[$propriedade] = $this->adicionaCifraoMoeda($dados[$propriedade]);
        }

        return $dados;
    }

    private function adicionaVariaveis($dados)
    {
        $dados['matricula_RI'] = $dados['matricula_ri'];

        return $dados;
    }

    private function adicionaMascarasCpfCnpj($dados)
    {
        $listaPropriedades = ['cpf_cnpj_proprietario', 'cpf_cnpj_promitente'];

        foreach ($listaPropriedades as $propriedade) {
            $dados[$propriedade] = $this->adicionaMascaraCgccpf($dados[$propriedade]);
        }

        return $dados;
    }

    private function adicionaMascaraCgccpf($valor)
    {
        switch (strlen($valor)) {
            case (11):
                return $this->mascaraCpf($valor);
            case (14):
                return $this->mascaraCnpj($valor);
            default:
                return '';
        }
    }

    private function mascaraCpf($cpf)
    {
        return substr($cpf, 0, 3)
            . '.'
            . substr($cpf, 3, 3)
            . '.'
            . substr($cpf, 6, 3)
            . '-'
            . substr($cpf, 9, 2);
    }

    private function mascaraCnpj($cnpj)
    {
        return substr($cnpj, 0, 2)
            . '.'
            . substr($cnpj, 2, 3)
            . '.'
            . substr($cnpj, 5, 3)
            . '/'
            . substr($cnpj, 8, 4)
            . '-'
            . substr($cnpj, 12, 2);
    }

    private function adicionaVirgulasDados($dados)
    {
        $listaPropriedades = [
            'area_total',
            'area_construcao'
        ];

        foreach ($listaPropriedades as $propriedade) {
            $dados[$propriedade] = $this->adicionaVirgulas($dados[$propriedade]);
        }

        return $dados;
    }

    private function adicionaOcorrencias($dados)
    {
        $dados['ocorrencias'] = $this->buscaOcorrenciasMatricula(
            $dados['matricula']
        );

        return $dados;
    }

    private function buscaOcorrenciasMatricula($matricula)
    {
        $oDaoIptuBase = new cl_iptubase();

        $sqlOcorrencias = $oDaoIptuBase->sql_query_buscaOcorrenciasMatricula(
            $matricula
        );
        $rsOcorrencias = db_query($sqlOcorrencias);

        $ocorrencias = db_utils::getCollectionByRecord($rsOcorrencias);

        $ocorrenciasConcatenadas = '';

        foreach ($ocorrencias as $ocorrencia) {
            $ocorrenciaValida = isset($ocorrencia->ar23_ocorrencia)
                && !empty(trim($ocorrencia->ar23_ocorrencia));
            if ($ocorrenciaValida) {
                $ocorrenciasConcatenadas .= $this->trimSpecialCharacters(
                    $ocorrencia->ar23_ocorrencia
                ) . "; ";
            }
        }

        return $this->trimSpecialCharacters(
            $ocorrenciasConcatenadas
        );
    }

    private function adicionaInformacoesConstrucoes($dados)
    {
        $dados['construcoes'] = $this->buscaInformacoesConstrucoesMatricula(
            $dados['matricula'],
            db_getsession('DB_anousu')
        );

        return $dados;
    }

    private function buscaInformacoesConstrucoesMatricula($matricula, $ano)
    {
        $oDaoIptuBase = new cl_iptubase();

        $sqlConstrucoes = $oDaoIptuBase->sql_query_buscaConstrucoesMatricula(
            $matricula,
            $ano
        );
        $rsConstrucoes = db_query($sqlConstrucoes);

        $construcoes = db_utils::getCollectionByRecord($rsConstrucoes);

        $construcoesConcatenadas = '';

        foreach ($construcoes as $construcao) {
            $areaConstr = $this->adicionaVirgulas($construcao->areaconstrucao) . 'm²';
            $situacaoConstr = trim($construcao->situacaoconstrucao);
            $valorConstr = $this->adicionaCifraoMoeda(
                $this->formataNumero($construcao->valorconstrucao)
            );
            $valorConstrExtenso = $this->traduzNumeroParaExtenso(
                $construcao->valorconstrucao
            );
            $construcoesConcatenadas .=
                "$areaConstr - $situacaoConstr - Valor Venal da Edificação: $valorConstr - $valorConstrExtenso; ";
        }

        return $this->trimSpecialCharacters(
            $construcoesConcatenadas
        );
    }

    private function adicionaOutrosProprietariosMatricula($dados)
    {
        $dados['outros_propri'] = $this->buscaInformacoesProprietariosMatricula(
            $dados['matricula']
        );

        return $dados;
    }

    private function buscaInformacoesProprietariosMatricula($matricula)
    {
        $oDaoPropri = new cl_propri();

        $sqlOutrosProprietarios = $oDaoPropri->sql_query(
            $matricula,
            null,
            "cgm.z01_numcgm,cgm.z01_nome,cgm.z01_cgccpf"
        );
        $rsOutrosProprietarios = db_query($sqlOutrosProprietarios);

        $outrosProprietarios = db_utils::getCollectionByRecord($rsOutrosProprietarios);

        $outrosProprietariosConcatenados = '';

        foreach ($outrosProprietarios as $proprietario) {
            $nome = $proprietario->z01_nome;
            $cgccpf = $this->adicionaMascaraCgccpf($proprietario->z01_cgccpf);
            $cgm = $proprietario->z01_numcgm;
            $outrosProprietariosConcatenados .= "$cgm - $nome - $cgccpf; ";
        }

        return $this->trimSpecialCharacters(
            $outrosProprietariosConcatenados
        );
    }

    private function adicionaVirgulas($valor)
    {
        return str_replace('.', ',', $valor);
    }

    private function formataNumero($valor)
    {
        return number_format($valor, 2, ',', '.');
    }

    private function adicionaCifraoMoeda($valor)
    {
        return isset($valor) && trim($valor) != ''
            ? 'R$ ' . $valor
            : '';
    }

    private function traduzNumeroParaExtenso($valor)
    {
        return trim(db_extenso($valor, true));
    }

    private function trimSpecialCharacters($sTexto)
    {
        return preg_replace('/^\W*(.*?)\W*$/', '$1', $sTexto);
    }
}
