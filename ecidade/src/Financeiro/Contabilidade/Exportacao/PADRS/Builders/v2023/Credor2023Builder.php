<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2023;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\PadBuilder;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\v2023\Credor;

class Credor2023Builder extends PadBuilder
{
    /**
     * @var Credor
     */
    protected $layout;

    protected function create()
    {
        $this->layout = new Credor();
    }

    protected function processar()
    {
        $nome = $this->dados['nome'];
        $endereco = $this->dados['endereco'];
        $cidade = $this->dados['cidade'];

        $nome = strlen($nome) > 60 ? substr($nome, 0, 60) : $nome;
        $endereco = strlen($endereco) > 50 ? substr($endereco, 0, 50) : $endereco;
        $cidade = strlen($cidade) > 30 ? substr($cidade, 0, 30) : $cidade;

        $this->layout->setCgm($this->formataNumerico($this->dados['cgm'], 10));
        $this->layout->setNome($this->formataCaractere($nome, 60));
        $this->layout->setCnp($this->formataNumerico($this->dados['cnpj_cpf'], 14));
        $this->layout->setInscricaoEstadual($this->formataNumerico($this->dados['inscricao_estadual'], 15));
        $this->layout->setInscricaoMunicipal($this->formataNumerico($this->dados['inscricao_municipal'], 15));
        $this->layout->setEndereco($this->formataCaractere($endereco, 50));
        $this->layout->setCidade($this->formataCaractere($cidade, 30));
        $this->layout->setUf($this->formataCaractere($this->dados['uf'], 2));
        $this->layout->setCep($this->formataNumerico($this->dados['cep'], 8));
        $this->layout->setFone($this->formataNumerico($this->dados['fone'], 15));
        $this->layout->setFax($this->formataNumerico($this->dados['fax'], 15));
        $this->layout->setTipoPessoa($this->formataNumerico($this->dados['tipo_pessoa'], 2));
    }
}
