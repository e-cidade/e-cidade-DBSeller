<?php

namespace ECidade\RecursosHumanos\RH\Recadastramento;

use AssentamentoFuncional;
use AssentamentoFuncionalRepository;
use BusinessException;
use cl_cadenderpais;
use DBDate;
use DBException;
use DBString;
use Dependente;
use Exception;
use Imigrante;
use ParameterException;
use ProcessoOuvidoria;
use ServidorDeficiente;
use ServidorDocumento;
use TipoAssentamentoRepository;

class ServidorAtivo extends Servidor
{
    /**
     * @throws BusinessException
     * @throws ParameterException
     * @throws Exception
     */
    public function run()
    {
        $this->salvarDadosDependentes();
        $this->salvarDadosPessoais();
        $this->salvarDadosDocumentos();
        $this->salvarEndereco();
        $this->salvarDadosContato();
        $this->salvarEnderecoExterior();
        $this->salvarDadosEstrangeiro();
        $this->salvarDadosPCD();
    }

    /**
     * @throws BusinessException
     * @throws DBException
     * @throws ParameterException
     * @throws Exception
     */
    private function salvarDadosPessoais()
    {

        $secao = $this->form->getSecao("dados_pessoais");

//        $campo = $secao->getCampo("nome_funcionario");
//        $this->cgm->setNome($campo->getResposta());
//        $this->cgm->setNomeCompleto($campo->getResposta());

        $campo = $secao->getCampo("nome_social_esocial");
        $this->cgm->setNomeSocial($campo->getResposta());

        $campo = $secao->getCampo("sexo_funcionario");
        $this->cgm->setSexo(
            $campo->getResposta()->codigo
        );
        $this->servidor->setSexo($campo->getResposta()->codigo);

        $campo = $secao->getCampo("data_nascimento_funcionario");
        $nascimento = empty($this->apenasNumero($campo->getResposta())) ? null : new DBDate($campo->getResposta());
        $this->cgm->setDataNascimento($nascimento);
        $this->servidor->setDataNascimento($nascimento);

        $campo = $secao->getCampo("genero");
        $this->cgm->setGenero($campo->getResposta()->codigo);

        $campo = $secao->getCampo("estado_civil");
        $this->cgm->setEstadoCivil($this->estadoCivilParaEcidade($campo->getResposta()->codigo));
        $this->servidor->setEstadoCivil($this->estadoCivilParaEcidadeServidor($campo->getResposta()->codigo));

        $campo = $secao->getCampo("mae");
        $this->cgm->setNomeMae($campo->getResposta());

        $campo = $secao->getCampo("pai");
        $this->cgm->setNomePai($campo->getResposta());

        $dependentes = $this->servidor->getDependentes();

        $dependente = array_filter($dependentes, function ($dependente) {
            return $dependente->isConjuge();
        });

        if (empty($dependente)) {
            $conjuge = new Dependente();
            $conjuge->setInstituicao($this->servidor->getCodigoInstituicao());
            $conjuge->setMatricula($this->servidor->getMatricula());
            $conjuge->setGrauParentesco("C");
            $conjuge->setSalarioFamilia("N");
            $conjuge->setTipo(0);
            $conjuge->setSexo(strtolower($this->servidor->getSexo()) == 'm' ? "F" : "M"); //ver com a lorena
            $conjuge->setFinsPrevidenciarios(false);
            $conjuge->setCondicaoEspecial("N");
            $conjuge->setTipoParentesco(1);
        } else {
            $conjuge = current($dependente);
        }

        $campo = $secao->getCampo("dados_conjuge");
        $conjuge->setNome($campo->getResposta());
        $campo = $secao->getCampo("cpf_conjuge");
        $conjuge->setCpf($this->apenasNumero($campo->getResposta()));
        $campo = $secao->getCampo("data_nasc_conjuge");
        if (!empty($this->apenasNumero($campo->getResposta()))) {
            $conjuge->setDataNascimento(new DBDate($campo->getResposta()));
        }

        if (!empty($conjuge->getNome()) or !empty($conjuge->getCpf())) {
            $conjuge->save();
        }

        $campo = $secao->getCampo("municipio_nasc_esocial");
        $this->cgm->setNaturalidade($campo->getResposta()->descricao);

        $campo = $secao->getCampo("pais_nasc_esocial");
        $this->cgm->setPaisNascimento($campo->getResposta()->id);
        $this->cgm->setPaisNacionalidade($campo->getResposta()->id);

        $campo = $secao->getCampo("nacionalidade");
        $this->cgm->setNacionalidade($campo->getResposta()->codigo);

        $campo = $secao->getCampo("raca");
        $this->servidor->setRacaCor(
            $this->racaParaEcidade($campo->getResposta()->codigo)
        );

        $campo = $secao->getCampo("escolaridade");
        $this->servidor->setGrauInstrucao(
            $campo->getResposta()->codigo
        );

        if (in_array((int)$campo->getResposta()->codigo, array(8, 10, 11, 12))) {
            require_once(modification('model/processoOuvidoria.model.php'));
            $campo = $secao->getCampo("descricao_do_curso");
            $assentamento = new AssentamentoFuncional();
            $assentamento->setMatricula($this->servidor->getMatricula());
            $assentamento->setHistorico(
                empty($campo->getResposta()) ? "NÃO INFORMADO" : $campo->getResposta()->descricao
            );
            $processoOuvidoria = ProcessoOuvidoria::findByAtendimento($this->atendimentoOvidoria->getId());
            $processo = $processoOuvidoria->getProcesso();
            $assentamento->setDataTermino(new DBDate($processo->getDataProcesso()));
            $assentamento->setDataConcessao(new DBDate($processo->getDataProcesso()));
            $assentamento->setDataLancamento(date("Y-m-d"));
            $tipoAssentamento = TipoAssentamentoRepository::getInstance()->getInstanciaPorTipo("NS");
            $assentamento->setTipoAssentamento($tipoAssentamento->getSequencial());
            $assentamento->setHora(date("H:i"));
            AssentamentoFuncionalRepository::persist($assentamento);
        }

        $this->cgm->save();
        $this->servidor->save();
    }

    /**
     * @throws ParameterException
     * @throws Exception
     */
    private function salvarDadosDocumentos()
    {

        $secao = $this->form->getSecao("documentos");

        $campo = $secao->getCampo("rg_func");
        $this->cgm->setIdentidade($campo->getResposta());

        $campo = $secao->getCampo("orgao_rg_emissor");
        $this->cgm->setIdentOrgao($campo->getResposta());

        $campo = $secao->getCampo("data_emissao_rg");
        $this->cgm->setIdentDataExp(
            empty($this->apenasNumero($campo->getResposta()))
                ? null
                : new DBDate($campo->getResposta())
        );
        $this->cgm->save();

        $documento = $this->servidor->documento();
        if (!$documento) {
            $documento = new ServidorDocumento();
            $documento->setMatricula($this->servidor->getMatricula());
        }
        $campo = $secao->getCampo("titulo_eleitoral");
        $documento->setTituloDeEleitor($campo->getResposta());

        $campo = $secao->getCampo("secao_titulo_eleitor");
        $documento->setSecaoTituloDeEleitor($campo->getResposta());

        $campo = $secao->getCampo("zona_titulo_eleitor");
        $documento->setZonaTituloDeEleitor($campo->getResposta());

        $campo = $secao->getCampo("certificado_reservista");
        $documento->setReservistaNumero($campo->getResposta());

        $campo = $secao->getCampo("pispasep");
        $documento->setPis($campo->getResposta());

        $campo = $secao->getCampo("registro_orgao_de_classe");
        $documento->setOrgaoClasse($campo->getResposta());

        $campo = $secao->getCampo("data_orgao_classe");
        $documento->setOrgaoClasseData(
            empty($this->apenasNumero($campo->getResposta()))
                ? null
                : new DBDate($campo->getResposta())
        );

        $campo = $secao->getCampo("orgao_emissor_orgao_classe");
        $documento->setOrgaoClasseEmissor($campo->getResposta());

        $campo = $secao->getCampo("data_validade_orgao_classe");
        if (!empty($campo)) {
            $documento->setOrgaoClasseValidade(
                empty($this->apenasNumero($campo->getResposta()))
                    ? null
                    : new DBDate($campo->getResposta())
            );
        }
        $campo = $secao->getCampo("cnh_carteira_de_motorista");
        $documento->setCnhNumero($campo->getResposta());

        $campo = $secao->getCampo("cnh_categoria");
        $documento->setCnhCategoria(
            is_object($campo->getResposta()) ?
                $campo->getResposta()->descricao :
                $campo->getResposta()
        );

        $campo = $secao->getCampo("uf_cnh");
        $documento->setCnhUf($campo->getResposta()->descricao);

        $campo = $secao->getCampo("data_validade_cnh");
        if ($campo) {
            $documento->setCnhValidade(
                empty($this->apenasNumero($campo->getResposta()))
                    ? null
                    : new DBDate($campo->getResposta())
            );
        }

        $campo = $secao->getCampo("data_emissao_cnh");
        $documento->setCnhEmissao(
            empty($this->apenasNumero($campo->getResposta()))
                ? null
                : new DBDate($campo->getResposta())
        );

        $campo = $secao->getCampo("rne");
        if ($campo) {
            $documento->setRneRegistro($campo->getResposta());
        }

        $campo = $secao->getCampo("orgao_emissor_rne");

        if ($campo) {
            $documento->setRneOrgaoEmissor($campo->getResposta());
        }

        $campo = $secao->getCampo("data_emissao_rne");

        if ($campo) {
            $documento->setRneEmissao(
                empty($this->apenasNumero($campo->getResposta()))
                    ? null
                    : new DBDate($campo->getResposta())
            );
        }


        $campo = $secao->getCampo("data_validade_rne");
        if ($campo) {
            $documento->setRneValidade(
                empty($this->apenasNumero($campo->getResposta()))
                    ? null
                    : new DBDate($campo->getResposta())
            );
        }


        $campo = $secao->getCampo("data_entrada_rne");
        if ($campo) {
            $documento->setRneEntrada(
                empty($this->apenasNumero($campo->getResposta()))
                    ? null
                    : new DBDate($campo->getResposta())
            );
        }

        $documento->save();
    }

    /**
     * @throws Exception
     */
    private function salvarEndereco()
    {
        $this->limpaEndereco();

        $secao = $this->form->getSecao("rendereco");

        $campo = $secao->getCampo("cep_endereco");
        $this->cgm->setCep($this->apenasNumero($campo->getResposta()));

        $campo = $secao->getCampo("uf_endereco");
        $this->cgm->setUf($campo->getResposta()->descricao);

        $campo = $secao->getCampo("municipio_endereco");
        $this->cgm->setMunicipio($campo->getResposta()->descricao);

        $campo = $secao->getCampo("bairro");
        $this->cgm->setBairro(pg_escape_string($campo->getResposta()));

        $campo = $secao->getCampo("logradouro");
        $this->cgm->setLogradouro(pg_escape_string($campo->getResposta()));

        $campo = $secao->getCampo("numero_endereco");
        if (!empty($campo->getResposta()) and !is_numeric($campo->getResposta())) {
            throw new Exception("Numero de endereço inválido");
        }
        $this->cgm->setNumero($campo->getResposta());

        $campo = $secao->getCampo("complemento_endereco");
        $this->cgm->setComplemento(pg_escape_string($campo->getResposta()));

        $this->cgm->save();
    }

    /**
     * @throws Exception
     */
    private function salvarEnderecoExterior()
    {
        $secao = $this->form->getSecao("endereco_exterior");
        if (!$secao) {
            return;
        }
        $campo = $secao->getCampo("pais_exterior");
        $pais = $campo->getResposta();
        if (empty($pais)) {
            return;
        }

        if (is_object($pais)) {
            $pais = $pais->descricao;
        }
        $pais = strtoupper(trim(DBString::removerCaracteresEspeciaisAcentos($pais)));

        $cadenderpais = new  cl_cadenderpais();
        $sql = $cadenderpais->sql_query_file(
            null,
            "*",
            null,
            "db70_descricao ILIKE '%$pais%' or db70_sigla ilike '%$pais%'"
        );

        $rs = db_query($sql);
        $paisObj = pg_fetch_object($rs);

        if (empty($paisObj) or
            $paisObj->db70_sequencial == 105 or
            strtolower($pais) === "brasil"
        ) {
            return;
        }

        $this->cgm->setPaisExterior($paisObj->db70_sequencial);

        $campo = $secao->getCampo("cep_exterior");
        $this->cgm->setCodigoPostalExterior($campo->getResposta());

        $campo = $secao->getCampo("municipio_exterior");
        $this->cgm->setCidadeExterior($campo->getResposta());

        $campo = $secao->getCampo("bairro_exterior");
        $this->cgm->setBairroExterior(pg_escape_string($campo->getResposta()));

        $campo = $secao->getCampo("logradouro_exterior");
        $this->cgm->setLogradouroExterior(pg_escape_string($campo->getResposta()));

        $campo = $secao->getCampo("numero_exterior");
        if (!empty($campo->getResposta()) and !is_numeric($campo->getResposta())) {
            throw new Exception("Número de endereço exterior inválido");
        }
        $this->cgm->setNumeroExterior($campo->getResposta());

        $campo = $secao->getCampo("complemento_exterior");
        $this->cgm->setComplementoExterior(pg_escape_string($campo->getResposta()));

        $this->cgm->save();
    }

    private function salvarDadosEstrangeiro()
    {

        $secao = $this->form->getSecao("estrangeiro");
        if (!$secao) {
            return;
        }

        $campoResidencia = $secao->getCampo("tempo_de_residenci");
        $campoCondicao = $secao->getCampo("cond_ingre_traba");

        try {
            $isImigrante = $this->servidor->isImigrante();
        } catch (Exception $ex) {
            $isImigrante = false;
        }

        if ($isImigrante) {
            $imigrante = $this->servidor->getDadosImigrante();
        } else {
            $imigrante = new Imigrante();
            $imigrante->setMatricula($this->servidor->getMatricula());
            $imigrante->setInstituicao($this->servidor->getCodigoInstituicao());
        }

        if (!empty($campoCondicao->getResposta())) {
            $imigrante->setCodigoCondicao($campoCondicao->getResposta()->codigo);
        }

        if (!empty($campoResidencia->getResposta())) {
            $imigrante->setCodigoResidencia($campoResidencia->getResposta()->codigo);
        }

        if (!empty($imigrante->getCodigoCondicao()) || !empty($imigrante->getCodigoResidencia())) {
            $imigrante->save();
        }
    }

    /**
     * @throws Exception
     */
    private function salvarDadosPCD()
    {

        $deficiente = $this->servidor->deficiente();
        if (!$deficiente) {
            $deficiente = new ServidorDeficiente();
            $deficiente->setMatricula($this->servidor->getMatricula());
            $deficiente->setInstituicao($this->servidor->getCodigoInstituicao());
        }

        $secao = $this->form->getSecao("pcd");
        $pcds = $secao->getResposta();

        $deficiente->setFisica(false);
        $deficiente->setVisual(false);
        $deficiente->setAuditiva(false);
        $deficiente->setIntelectual(false);
        $deficiente->setMental(false);

        foreach ($pcds as $pcd) {
            switch ($pcd->deficiencia_fisica->codigo) {
                case $deficiente::AUDITIVA:
                    $deficiente->setAuditiva(true);
                    break;
                case $deficiente::FISICA:
                    $deficiente->setFisica(true);
                    break;
                case $deficiente::INTELECTUAL:
                    $deficiente->setIntelectual(true);
                    break;
                case $deficiente::MENTAL:
                    $deficiente->setMental(true);
                    break;
                case $deficiente::VISUAL:
                    $deficiente->setVisual(true);
                    break;
            }
        }

        $deficiente->save();
    }


    /**
     * @throws BusinessException
     * @throws ParameterException
     * @throws Exception
     */
    private function salvarDadosDependentes()
    {

        $secao = $this->form->getSecao("dependentes");
        $dependentes = $secao->getResposta();
        $dependentesOld = $this->servidor->getDependentes();

        foreach ($dependentesOld as $dependenteOld) {
            $dependenteOld->delete();
        }

        foreach ($dependentes as $dependente) {
            if (empty($dependente->nome_dependentes)) {
                continue;
            }

            $secaoDadosPessoais = $this->form->getSecao("dados_pessoais");
            $campo = $secaoDadosPessoais->getCampo("cpf_conjuge");
            $cpfConjuge = $this->apenasNumero($campo->getResposta());

            if ($this->apenasNumero($dependente->cpf_dependente) == $cpfConjuge) {
                continue;
            }

            $dependenteModel = new Dependente();
            $dependenteModel->setMatricula($this->servidor->getMatricula());
            $dependenteModel->setInstituicao($this->servidor->getCodigoInstituicao());
            $dependenteModel->setNome($dependente->nome_dependentes);
            $dependenteModel->setGrauParentesco(
                $this->parentescoParaEcidade(
                    $dependente->tipo_de_dependentes->codigo,
                    $dependente->sexo_dependente->codigo
                )
            );

            $dependenteModel->setTipoParentesco(
                str_pad(
                    $dependente->tipo_de_dependentes->codigo,
                    2,
                    "0",
                    STR_PAD_LEFT
                )
            );

            if (!empty($this->apenasNumero($dependente->data_nasc_dependente))) {
                $dependenteModel->setDataNascimento(
                    new DBDate($dependente->data_nasc_dependente)
                );
            }

            $dependenteModel->setCpf($this->apenasNumero($dependente->cpf_dependente));
            $dependenteModel->setSalarioFamilia(
                $this->salarioFamiliaParaEcidade(
                    $dependente->dependente_salario_familia->codigo,
                    $this->parentescoParaEcidade(
                        $dependente->tipo_de_dependentes->codigo,
                        $dependente->sexo_dependente->codigo
                    )
                )
            );
            $dependenteModel->setFinsPrevidenciarios(
                $dependente->dependente_fins_previdenciarios->codigo == 1
            );

            $dependenteModel->setCondicaoEspecial(
                $dependente->dependente_incapacidade->codigo == 1 ? "C" : "N"
            );
            $dependenteModel->setTipo(
                $this->irfParaEcidade(
                    $dependente->dependente_irrf->codigo,
                    $dependente->tipo_de_dependentes->codigo
                )
            );
            $dependenteModel->setSexo($dependente->sexo_dependente->codigo == 1 ? "M" : "F");
            $dependenteModel->save();
        }
    }

    /**
     * @throws Exception
     */
    private function salvarDadosContato()
    {
        $secao = $this->form->getSecao("contato");

        $campo = $secao->getCampo("celular");
        $this->cgm->setCelular($campo->getResposta());

        $campo = $secao->getCampo("email");
        $this->cgm->setEmail(
            trim(strtolower($campo->getResposta()))
        );

        $campo = $secao->getCampo("telefone");
        $this->cgm->setTelefone($campo->getResposta());

        $this->cgm->save();
    }
}
