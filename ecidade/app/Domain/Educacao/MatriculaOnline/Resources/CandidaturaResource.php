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

namespace App\Domain\Educacao\MatriculaOnline\Resources;

use App\Domain\Configuracao\Helpers\StorageHelper;
use ECidade\Lib\File\FileEstorage;
use App\Domain\Educacao\CentralMatriculas\Services\ProcessoInscricaoService;
use App\Domain\Educacao\Escola\Enums\NacionalidadeEnum;
use App\Domain\Educacao\MatriculaOnline\Models\Candidato;
use App\Domain\Educacao\MatriculaOnline\Models\Candidatura;
use App\Domain\Educacao\MatriculaOnline\Models\AtestadoNecessidadeEspecial;
use ECidade\Enum\Common\EstadoCivilEnun;
use ECidade\Enum\Saude\Ambulatorial\RacaCorEnum;
use App\Domain\Educacao\Escola\Models\Aluno;
use App\Domain\Educacao\Escola\Models\CensoEstado;
use App\Domain\Educacao\Escola\Models\CensoMunicipio;
use App\Domain\Educacao\Escola\Models\Pais;
use Illuminate\Support\Facades\DB;

class CandidaturaResource
{
    public static function toResponseConsulta(Candidatura $candidatura)
    {
        $data = $candidatura->candidato->mo01_datacad->format('d/m/Y');
        $situacao = "";
        $opcoesEscola = $candidatura->candidato->opcoesEscola;
        if (!$candidatura->fase->mo04_processada) {
            $situacao = "Em andamento";
        } else {
            if (is_null($candidatura->alocado)) {
                $situacao = 'Não alocado';
            } else {
                $opcoesEscola = [(object)[
                    "escola" => $candidatura->alocado->opcaoEscolaTurno->opcaoEscola->escola->mo53_nome,
                    "turno" => $candidatura->alocado->opcaoEscolaTurno->turno->ed15_c_nome,
                    "dataAlocacao" => $candidatura->alocado->mo13_data->format('d/m/Y')
                ]];

                $situacao = trim($candidatura->alocado->situacao->situacao->mo27_descricao);
            }
        }

        $etapa = trim($candidatura->candidato->etapa->ed11_c_descr);
        return (object)[
            "nome" => trim($candidatura->candidato->mo01_nome),
            "dataHoraInscricao" =>"{$data} as {$candidatura->candidato->mo01_horainscricao}",
            "situacao" => $situacao,
            "anoSerie" => "{$candidatura->fase->mo04_anousu} / {$etapa}",
            "opcoesEscola" => $opcoesEscola,
            "protocolo" => $candidatura->mo12_protocolo,
            "alocado" => $candidatura->foiAlocado
        ];
    }

    public static function toResponseEdicao(Candidatura $candidatura)
    {
        $service = new ProcessoInscricaoService();
        $descricaoEtapa = trim($candidatura->candidato->etapa->ed11_c_descr);
        $descricaoEnsino = trim($candidatura->candidato->etapa->ensino->ed10_c_descr);
        $fase = FaseResource::toResponse($candidatura->fase);
        $etapasFase = $service->getEtapasFasePorData($fase->codigo, $candidatura->candidato->mo01_dtnasc);
        $fase->etapas = $etapasFase;
        $livro = $candidatura->candidato->mo01_certidaolivro;
        $matriculaCertidao =
            $candidatura->candidato->mo01_certidaomatricula === '0000000000000000000000' ?
            null : $candidatura->candidato->mo01_certidaomatricula;
        $termo =  $candidatura->candidato->mo01_certidaonum === '00000000'
            ? null : $candidatura->candidato->mo01_certidaonum;
        $folha = $candidatura->candidato->mo01_certidaofolha === '0000' ?
            null : $candidatura->candidato->mo01_certidaofolha;
        $dataCertidao =  $candidatura->candidato->mo01_certidaodata === '1900-01-01' ? null :
            $candidatura->candidato->mo01_certidaodata;
        // Tratando null em Raça
        $candidatura->candidato->mo01_raca =
            $candidatura->candidato->mo01_raca === null ? 'NÃO DECLARADA' :  $candidatura->candidato->mo01_raca;
        // Tratando null em País Nascimento
        if ($candidatura->candidato->paisNascimento === null) {
            $candidatura->candidato->paisNascimento = new Pais([
                'ed228_c_descr' => 'BRASIL',
                'ed228_i_codigo' => 10
            ]);
        }

        $dados = (object)[
            "protocolo" => trim($candidatura->mo12_protocolo),
            "fase" => $fase,
            "nome" => $candidatura->candidato->mo01_nome,
            "cpf" => $candidatura->candidato->mo01_cpf,
            "identidade" => $candidatura->candidato->mo01_ident,
            "orgaoIdentidade" => [
                "label" => $candidatura->candidato->mo01_orgident,
                "value" => 0,
            ],
            "bolsaFamilia" => $candidatura->candidato->mo01_cartaobolsafamilia,
            "redeOrigem" => [
                "label" => trim($candidatura->candidato->redeOrigem->mo05_descr),
                "value" => $candidatura->candidato->redeOrigem->mo05_codigo
            ],
            "escolaOrigem" => is_null($candidatura->candidato->escolaRedeOrigem) ? null : [
                "label" => trim($candidatura->candidato->escolaRedeOrigem->mo53_nome),
                "value" => $candidatura->candidato->escolaRedeOrigem->mo53_codigo
            ],
            "dataNascimento" => $candidatura->candidato->mo01_dtnasc->format('d/m/Y'),
            "nacionalidade" => [
                "label" => (new NacionalidadeEnum(intval($candidatura->candidato->mo01_nacion)))->descricao(),
                "value" =>  (new NacionalidadeEnum(intval($candidatura->candidato->mo01_nacion)))->value()
            ],
            "nomeSocial" => $candidatura->candidato->mo01_nome_social,
            "etapaEnsino" => [
                "label" => "{$descricaoEtapa} - {$descricaoEnsino}",
                "value" =>  $candidatura->candidato->etapa->ed11_i_codigo
            ],
            "estadoCertidao" => trim($candidatura->candidato->estadoCertidao->ed260_c_nome) === 'NÃO INFORMADO'
                ? null :[
                "label" => trim($candidatura->candidato->estadoCertidao->ed260_c_nome),
                "value" =>  $candidatura->candidato->estadoCertidao->ed260_i_codigo
            ],
            "municipioCertidao" => trim($candidatura->candidato->municipioCertidao->ed261_c_nome) === 'NÃO INFORMADO'
                ? null : [
                "label" => trim($candidatura->candidato->municipioCertidao->ed261_c_nome),
                "value" =>  $candidatura->candidato->municipioCertidao->ed261_i_codigo
            ],
            "rne" => $candidatura->candidato->mo01_rnm,
            "visto" => $candidatura->candidato->mo01_visto,
            "livro" => $livro == '00000000' ? null : $livro ,
            "nomeCartorio" => $candidatura->candidato->mo01_certidaocart,
            "dataCertidao" => $dataCertidao,
            "termo" => $termo,
            "folha" => $folha,
            "sexo" =>  [
                "label" => $candidatura->candidato->mo01_sexo === 'F' ? 'FEMININO' : "MASCULINO",
                "value" => $candidatura->candidato->mo01_sexo
            ],
            "estadoCivil" => [
                "label" => mb_strtoupper((new EstadoCivilEnun(intval($candidatura->candidato->mo01_estciv)))->name()),
                "value" =>  (new EstadoCivilEnun(intval($candidatura->candidato->mo01_estciv)))->value()
            ],
            "cor" => [
                "label" => trim($candidatura->candidato->mo01_raca),
                "value" => RacaCorEnum::getValueByName(trim($candidatura->candidato->mo01_raca))
            ],
            "estadoNascimento" => [
                "label" => trim($candidatura->candidato->estadoNascimento->ed260_c_nome),
                "value" =>  $candidatura->candidato->estadoNascimento->ed260_i_codigo
            ],
            "paisNascimento" =>  [
                "label" => trim($candidatura->candidato->paisNascimento->ed228_c_descr),
                "value" =>  $candidatura->candidato->paisNascimento->ed228_i_codigo
            ],
            "municipioNascimento" => [
                "label" => trim($candidatura->candidato->municipioNascimento->ed261_c_nome),
                "value" =>  $candidatura->candidato->municipioNascimento->ed261_i_codigo
            ],
            "irmaoGemeo" => $candidatura->candidato->mo01_temirmaogemeo,
            "cadeirante" => $candidatura->candidato->mo01_cadeirante,
            "certidaoTipo" => $candidatura->candidato->mo01_nacion ==  3 ? null : [
                "label" => $candidatura->candidato->mo01_certidaotipo === 1 ? 'NASCIMENTO' : 'CASAMENTO',
                "value" => $candidatura->candidato->mo01_certidaotipo
            ],
            "certidaoModelo" => $candidatura->candidato->mo01_nacion ==  3 ? null : [
                "label" => empty($livro) || $livro == '00000000' ? 'MODELO NOVO' : 'MODELO ANTIGO',
                "value" => empty($livro) || $livro == '00000000' ? 2 : 1
            ],
            "certidaoNumero" => $matriculaCertidao,
            "cartaoSus" => $candidatura->candidato->mo01_cartaosus,
            "email" => $candidatura->candidato->mo01_email,
            "contatoPrincipal" => $candidatura->candidato->mo01_telef,
            "contatoSecundario" => $candidatura->candidato->mo01_telcel,
            "cep" => $candidatura->candidato->mo01_cep,
            "logradouro" => $candidatura->candidato->mo01_ender,
            "numero" => $candidatura->candidato->mo01_numero,
            "complemento" => $candidatura->candidato->mo01_compl,
            "bairro" => [
                "label" => $candidatura->candidato->bairro->j13_descr,
                "value" => $candidatura->candidato->bairro->j13_codi
            ],
            "zonaResidencia" => is_null($candidatura->candidato->zonaResidencia) ? null : [
                "label" => mb_strtoupper($candidatura->candidato->zonaResidencia->ed194_descriicao),
                "value" => $candidatura->candidato->zonaResidencia->ed194_id
            ],
            "rendaFamiliar" => is_null($candidatura->candidato->rendaFamiliar) ? null : [
                "label" => mb_strtoupper(utf8_encode($candidatura->candidato->rendaFamiliar->mo25_faixa)),
                "value" => $candidatura->candidato->rendaFamiliar->mo25_id
            ],
            "filiacao1" => $candidatura->candidato->mo01_pai,
            "cpfFiliacao1" => $candidatura->candidato->mo01_cpf_filiacao_um,
            "filiacao2" => $candidatura->candidato->mo01_mae,
            "cpfFiliacao2" => $candidatura->candidato->mo01_cpf_filiacao_dois,
            "profissaoFiliacao1" => is_null($candidatura->candidato->ocupacaoPai) ? null : [
                "label" => utf8_encode($candidatura->candidato->ocupacaoPai->mo304_titulo),
                "value" => $candidatura->candidato->ocupacaoPai->mo304_codigo
            ],
            "profissaoFiliacao2" => is_null($candidatura->candidato->ocupacaoMae) ? null : [
                "label" => utf8_encode($candidatura->candidato->ocupacaoMae->mo304_titulo),
                "value" => $candidatura->candidato->ocupacaoMae->mo304_codigo
            ],
            "tipoResponsavel" => $candidatura->candidato->mo01_tiporesp,
            "responsavelLegalNome" => $candidatura->candidato->mo01_nomeresp,
            "responsavelLegalCpf" => $candidatura->candidato->mo01_cpfresp,
            "responsavelLegalRG" => $candidatura->candidato->mo01_identresp,
            "responsavelLegalOrgaoRG" => [
                "label" => $candidatura->candidato->mo01_orgidresp,
                "value" => 0,
            ],
            "responsavelLegalProfissao" => is_null($candidatura->candidato->ocupacaoResponsavel) ? null : [
                "label" => utf8_encode($candidatura->candidato->ocupacaoResponsavel->mo304_titulo),
                "value" => $candidatura->candidato->ocupacaoResponsavel->mo304_codigo
            ],
            "responsavelLegalEmail" => $candidatura->candidato->mo01_emailresp,
            "responsavelLegalRNE" => $candidatura->candidato->mo01_rnmresponsavel,
            "responsavelLegalCelular" => $candidatura->candidato->mo01_telresp,
            "responsavelLegalCelularWhatsApp" => $candidatura->candidato->mo01_telrespwhatsapp,
            "responsavelLegalVisto" => $candidatura->candidato->mo01_vistoresponsavel,
            "responsavelLegalTrabalhador" => $candidatura->candidato->mo01_responsaveltrabalhador,
            "maeVitima" => $candidatura->candidato->mo01_mae_vitima,
            "matriculaServidor" => $candidatura->candidato->mo01_matriculaservidor,
            "opcoesEscola" => self::mapOcoesEscola($candidatura->candidato),
            "atestadoNecessidadeEspecial" => self::mapAtestadoNecessidadeEspecial(
                $candidatura->candidato->mo01_codigo,
                $candidatura->candidato->mo01_cpf
            ),
            "necessidadesEspeciais" => $candidatura->candidato->candidatoNecessidades
                ->map(function ($candidatoNecessidade) {
                    $necessidade = (object)[
                        "label" => $candidatoNecessidade->necessidade->ed48_c_descr,
                        "value" =>  $candidatoNecessidade->necessidade->ed48_i_codigo,
                    ];
                    $necessidade->subdivisoes =  $candidatoNecessidade->subdivisoes->map(function ($sub) {
                        return (object) [
                            'label' => trim($sub->ed185_descricao),
                            "value" => $sub->ed185_sequencial,
                            "selecionada" => $sub->selecionada === true
                        ];
                    });
                    return $necessidade;
                })
        ];

        return $dados;
    }

    public static function toResponseAluno(Aluno $aluno)
    {
        $atestadoNecessidadeEspecial = $aluno
            ->alunoNecessidade
            ->map(function ($dadosAluno) {
                if (!empty($dadosAluno->ed214_i_anexo_estorage)) {
                    return $dadosAluno->ed214_i_anexo_estorage;
                }
                return null;
            });
        $jsonArquivo = null;
        if (!empty($atestadoNecessidadeEspecial->first())) {
            self::verificaConexaoStorage();
            $laudo = StorageHelper::downloadArquivo($atestadoNecessidadeEspecial->first());
            $extensaoArquivo = explode(".", $laudo)[1];
            $nomeArquivo = "laudoEspecial".$aluno->getCpf().".".$extensaoArquivo;
            $laudo = file_get_contents($laudo);
            $base64Laudo = "data:image/$extensaoArquivo;base64,".base64_encode($laudo);

            $jsonArquivo = [
                'fileName'=>$nomeArquivo,
                'fileData'=>$base64Laudo
            ];
        }
        if ($jsonArquivo !== null) {
            $jsonArquivo = json_encode($jsonArquivo);
        }
        $censoUfCertidao = null;
        if (!empty($aluno->getCensoUfCertidao())) {
            $censoUfCertidao = CensoEstado::where('ed260_i_codigo', $aluno->getCensoUfCertidao())->first();
        }
        $municipioCertidao = null;
        if (!empty($aluno->getCensoMunicipioCertidao())) {
            $municipioCertidao = CensoMunicipio::where('ed261_i_codigo', $aluno->getCensoMunicipioCertidao())->first();
        }
        $livro = $aluno->getCertidaoLivro();
        $dataCertidao =  $aluno->getCertidaoData() === '1900-01-01' ? null : $aluno->getCertidaoData();
        $termo =  $aluno->getCertidaoNumero() === '00000000' ? null : $aluno->getCertidaoNumero();
        $folha = $aluno->getCertidaoFolha() === '0000' ? null : $aluno->getCertidaoFolha();
        $estadoNascimento = CensoEstado::where('ed260_i_codigo', $aluno->getCensoUfNascimento())->first();
        $paisNascimento = Pais::where('ed228_i_codigo', $aluno->getPaisResidencia())->first();
        $municipioNascimento = CensoMunicipio::where('ed261_i_codigo', $aluno->getCensoMunicipioNascimento())->first();
        $matriculaCertidao = $aluno->getCertidaoMatricula() === '0000000000000000000000' ?
            null : $aluno->getCertidaoMatricula();
        $sql = "select * from secretariadeeducacao.zonas_residencia where ed194_descriicao = '{$aluno->getZona()}'";
        $zonaResidencia = DB::select($sql);
        if (sizeof($zonaResidencia) > 0) {
            $zonaResidencia = $zonaResidencia[0];
        } else {
            $zonaResidencia = null;
        }
        $dados = (object)[
            'aluno' => $aluno,
            "nome" => $aluno->getNome(),
            "cpf" => $aluno->getCpf(),
            "identidade" => $aluno->getIdentidade() == "000000000" ? null : $aluno->getIdentidade(),
            "orgaoIdentidade" => [
                "label" => $aluno->getOrgaoEmissor(),
                "value" => 0,
            ],
            "bolsaFamilia" => $aluno->getBolsaFamilia(),
            "dataNascimento" => $aluno->getDataNascimento()->format('d/m/Y'),
            "nacionalidade" => [
                "label" => (new NacionalidadeEnum(intval($aluno->getNacionalidade())))->descricao(),
                "value" =>  (new NacionalidadeEnum(intval($aluno->getNacionalidade())))->value()
            ],
            "nomeSocial" => "",
            "estadoCertidao" => empty($censoUfCertidao)
                ? null :[
                    "label" => trim($censoUfCertidao->ed260_c_nome),
                    "value" => $censoUfCertidao->ed260_i_codigo
            ],
            "municipioCertidao" => empty($municipioCertidao)
                ? null : [
                "label" => trim($municipioCertidao->ed261_c_nome),
                "value" =>  $municipioCertidao->ed261_i_codigo
            ],
            "livro" => !empty($matriculaCertidao) || $livro == '00000000' ? null : $livro ,
            "nomeCartorio" => $aluno->getCerticaoCartorio(),
            "dataCertidao" => $dataCertidao,
            "termo" => !empty($matriculaCertidao) ? null : $termo,
            "folha" => !empty($matriculaCertidao) ? null : $folha,
            "sexo" =>  [
                "label" => $aluno->getSexo() === 'F' ? 'FEMININO' : "MASCULINO",
                "value" => $aluno->getSexo()
            ],
            "estadoCivil" => [
                "label" => mb_strtoupper((new EstadoCivilEnun(intval($aluno->getEstadoCivil())))->name()),
                "value" =>  (new EstadoCivilEnun(intval($aluno->getEstadoCivil())))->value()
            ],
            "cor" => [
                "label" => trim($aluno->getRaca()),
                "value" => RacaCorEnum::getValueByName(trim(utf8_encode($aluno->getRaca())))
            ],
            "estadoNascimento" => $estadoNascimento == null ? null : [
                "label" => trim($estadoNascimento->ed260_c_nome),
                "value" =>  $estadoNascimento->ed260_i_codigo
            ],
            "paisNascimento" => $paisNascimento == null ? null : [
                "label" => trim($paisNascimento->ed228_c_descr),
                "value" =>  $paisNascimento->ed228_i_codigo
            ],
            "municipioNascimento" => $municipioNascimento == null ? null : [
                "label" => trim($municipioNascimento->ed261_c_nome),
                "value" =>  $municipioNascimento->ed261_i_codigo
            ],
            "certidaoTipo" => $aluno->getNacionalidade() ==  3 ? null : [
                "label" => $aluno->getCertidaoTipo() === 'N' ? 'NASCIMENTO' : 'CASAMENTO',
                "value" => $aluno->getCertidaoTipo() === 'N' ? 1 : 2
            ],
            "certidaoModelo" => $aluno->getNacionalidade() ==  3 ? null : [
                "label" => !empty($matriculaCertidao) ? 'MODELO NOVO' : 'MODELO ANTIGO',
                "value" => !empty($matriculaCertidao) ? 2 : 1
            ],
            "certidaoNumero" => $matriculaCertidao,
            "cartaoSus" => $aluno->getCartaoSus(),
            "email" => $aluno->getEmail(),
            "contatoPrincipal" => $aluno->getTelefonia(),
            "contatoSecundario" => $aluno->getTelefoneCelular(),
            "cep" => $aluno->getCep(),
            "logradouro" => $aluno->getEndereco(),
            "complemento" => $aluno->getComplemento(),
            "zonaResidencia" => $zonaResidencia == null ? null : [
                "label" => mb_strtoupper($zonaResidencia->ed194_descriicao),
                "value" => $zonaResidencia->ed194_id
            ],
            "filiacao1" => $aluno->getNomePai(),
            "filiacao2" => $aluno->getNomeMae(),
            "responsavelLegalNome" => $aluno->getNomeResponsavel(),
            "responsavelLegalEmail" => $aluno->getEmailResponsavel(),
            "responsavelLegalCelular" => $aluno->getCelularResponsavel(),
            "rne" => $aluno->getRnm(),
            "visto" => $aluno->getVisto(),
            "atestadoNecessidadeEspecial" => $jsonArquivo,
            "necessidadesEspeciais" => $aluno->alunoNecessidade
                ->map(function ($aluno) {
                    $necessidade = (object)[
                        "label" => $aluno->necessidade->ed48_c_descr,
                        "value" =>  $aluno->necessidade->ed48_i_codigo,
                    ];
                    $necessidade->subdivisoes = $aluno->necessidadeSubdivisaoAluno->map(function ($sub) {
                        return (object) [
                            'label' => trim($sub->ed185_descricao),
                            "value" => $sub->ed185_sequencial,
                            "selecionada" => $sub->selecionada === true
                        ];
                    });
                    return $necessidade;
                })
        ];

        return $dados;
    }

    public static function verificaConexaoStorage()
    {
        try {
            return new FileEstorage();
        } catch (\Exception $error) {
            $msgError = "Se mostra necessário configurar o E-storage";
            $msgError .= " para checar os arquivos armazenados no projeto!";
            throw new \Exception($msgError);
        }
    }

    public static function mapAtestadoNecessidadeEspecial($codigoCandidato, $cpfCandidato)
    {
        $atestadoModel = AtestadoNecessidadeEspecial::where('mo31_base', $codigoCandidato)->get();
        if (!empty($atestadoModel)) {
            foreach ($atestadoModel as $atestado) {
                $idStorage = $atestado->mo31_anexo_storage;
                self::verificaConexaoStorage();
                $laudo = StorageHelper::downloadArquivo($idStorage);
                $extensaoArquivo = explode(".", $laudo)[1];
                $nomeArquivo = "laudoEspecial".$cpfCandidato.".".$extensaoArquivo;
                $laudo = file_get_contents($laudo);
                $base64Laudo = "data:image/$extensaoArquivo;base64,".base64_encode($laudo);

                $jsonArquivo = [
                    'fileName'=>$nomeArquivo,
                    'fileData'=>$base64Laudo
                ];
                return json_encode($jsonArquivo);
            }
        }
        return null;
    }

    public static function mapOcoesEscola(Candidato $candidato)
    {
        $opcoes = [];
        foreach ($candidato->basesEscola as $baseEscola) {
            $opcoes[$baseEscola->opcaoTurno->mo03_opcao] = (object) [
                "escola" => [
                    "nome" => trim($baseEscola->escola->mo53_nome),
                    "codigo" => $baseEscola->escola->mo53_codigo,
                    "turno" => [
                        "codigo" => $baseEscola->opcaoTurno->turno->ed15_i_codigo,
                        "descricao" => trim($baseEscola->opcaoTurno->turno->ed15_c_nome)
                    ],
                    "index" => $baseEscola->opcaoTurno->mo03_opcao,
                    "bairro" => $baseEscola->escola->bairro->j13_codi,
                    "bairrosAtendidos" => $baseEscola->escola->bairrosAtendidos->map(function ($bairro) {
                        return $bairro->bairro->j13_codi;
                    })
                ],
                "temIrmao" => $baseEscola->mo02_temirmaoescola,
                "nomeIrmao" => trim($baseEscola->mo02_nomeirmao)
            ];
        }
        ksort($opcoes);
        return array_values($opcoes);
    }
}
