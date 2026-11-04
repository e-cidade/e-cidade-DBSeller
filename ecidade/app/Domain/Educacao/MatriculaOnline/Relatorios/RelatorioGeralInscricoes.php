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

namespace App\Domain\Educacao\MatriculaOnline\Relatorios;

use ECidade\File\Csv\Dumper\Dumper;

class RelatorioGeralInscricoes extends Dumper
{

    protected $dados = [];

    protected $cabecalho = [
        'fase' => 'FASE',
        'mobase' => 'MOBASE',
        'nome_candidato' => 'CANDIDATO',
        'rua' => 'RUA',
        'bairro' => 'BAIRRO',
        'numero' => 'NUMERO',
        'complemento' => 'COMPLEMENTO',
        'cep' => 'CEP',
        'uf' => 'UF',
        'municipio' => 'MUNICIPIO',
        'zona_residencia' => 'ZONA RESIDENCIA',
        'pais_nascimento' => 'PAIS NASCIMENTO',
        'nacionalidade' => 'NACIONALIDADE',
        'telefone' => 'TELEFONE',
        'cpf_candidato' => 'CPF CANDIDATO',
        'identidade' => 'IDENTIDADE CANDIDATO',
        'orgao_identidade' => 'ORGAO ID CANDIDATO',
        'cartao_sus' => 'CARTAO SUS',
        'bolsa_familia' => 'BOLSA FAMILIA',
        'numero_bolsa_familia' => 'NUM BOLSA FAMILIA',
        'visto_candidato' => 'VISTO CANDIDATO',
        'rne_candidato' => 'RNE CANDIDATO',
        'tipo_certidao' => 'TIPO CERTIDAO',
        'termo_certidao' => 'TERMO CERTIDAO',
        'livro_certidao' => 'LIVRO CERTIDAO',
        'folha_certidao' => 'FOLHA CERTIDAO',
        'cartorio_certidao' => 'CARTORIO CERTIDAO',
        'matricula_certidao' => 'MATRICULA CERTIDAO',
        'estado_certidao' => 'UF CERTIDAO',
        'municipio_certidao' => 'MUNICIPIO CERTIDAO',
        'data_certidao' => 'DATA CERTIDAO',
        'data_nascimento' => 'DATA NASCIMENTO',
        'estado_nascimento' => 'UF NASCIMENTO',
        'municipio_nascimento' => 'MUNICIPIO NASCIMENTO',
        'estado_civil' => 'ESTADO CIVIL',
        'email_candidato' => 'EMAIL CANDIDATO',
        'sexo' => 'SEXO',
        'raca_candidato' => 'RACA / COR',
        'cadeirante' => 'CADEIRANTE?',
        'necessidades_especiais' => 'NECESSIDADES ESPECIAIS',
        'filiacao_um' => 'FILIACAO UM',
        'cpf_filiacao_um' => 'CPF FILIACAO UM',
        'filiacao_dois' => 'FILIACAO DOIS',
        'cpf_filiacao_dois' => 'CPF FILIACAO DOIS',
        'tipo_responsavel' => 'TIPO RESPONSAVEL',
        'nome_responsavel' => 'NOME RESPONSAVEL',
        'telefone_responsavel' => 'TELEFONE RESPONSAVEL',
        'identidade_responsavel' => 'IDENTIDADE RESPONSAVEL',
        'orgao_identidade_responsavel' => 'ORGAO ID RESPONSAVEL',
        'cpf_responsavel' => 'CPF RESPONSAVEL',
        'email_responsavel' => 'EMAIL RESPONSAVEL',
        'visto_responsavel' => 'VISTO RESPONSAVEL',
        'rne_responsavel' => 'RNE RESPONSAVEL',
        'responsavel_trabalhador' => 'RESPONSAVEL TRABALHADOR',
        'mae_vitima_violencia' => 'MAE VITIMA DE VIOLENCIA DOMESTICA',
        'telefone_adicional' => 'TELEFONE ADICIONAL',
        'renda_familiar' => 'RENDA FAMILIAR',
        'etapa_pretendida' => 'ETAPA PRETENDIDA',
        'rede_origem' => 'REDE ORIGEM',
        'irmao_gemeo' => 'IRMAO GEMEO',
        'data_cadastro' => 'DATA CADASTRO',
        'hora_cadastro' => 'HORA CADASTRO',
        'data_hora_manutencao' => 'DATA / HORA MANUTENCAO',
        'protocolo' => 'PROTOCOLO',
        'base' => 'BASE',
        'situacao_alocado' => 'SITUACAO ALOCADO',
        'escola_alocado' => 'ESCOLA ALOCADO',
        'criterios_alocacao' => 'CRITERIOS ALOCACAO',
        'escola_opcao_um' => 'ESCOLA OPCAO 1',
        'classificacao_opcao_um' => 'CLASSIFICACAO OPCAO 1',
        'escola_opcao_dois' => 'ESCOLA OPCAO 2',
        'classificacao_opcao_dois' => 'CLASSIFICACAO OPCAO 2',
        'escola_opcao_tres' => 'ESCOLA OPCAO 3',
        'classificacao_opcao_tres' => 'CLASSIFICACAO OPCAO 3',
        'escola_opcao_quatro' => 'ESCOLA OPCAO 4',
        'classificacao_opcao_quatro' => 'CLASSIFICACAO OPCAO 4',
        'escola_opcao_cinco' => 'ESCOLA OPCAO 5',
        'classificacao_opcao_cinco' => 'CLASSIFICACAO OPCAO 5'
    ];

    /**
     * @param null $dados
     */
    public function __construct($dados)
    {
        $this->dados = $dados;
    }

    public function emitir()
    {
        $this->setCsvControl(";", '"');
        $fileName = 'tmp/geral-inscricoes' . time() . '.csv';
        $this->dumpToFile($this->organizaDados(), $fileName);
        return [
            "name" => "Relatório Geral de Inscrições CSV",
            "path" => $fileName,
            'pathExterno' => ECIDADE_REQUEST_PATH . $fileName
        ];
    }

    private function organizaDados()
    {
        $dadosImprimir = [];
        $dadosImprimir[] = $this->cabecalho;

        foreach ($this->dados as $key => $fase) {
            $nomeFase = $key;
            foreach ($fase as $candidato) {
                $linha = [
                    utf8_encode($nomeFase),
                    $candidato->mobase,
                    utf8_encode($candidato->nome_candidato),
                    utf8_encode(str_replace(["'", '"', ",", ";"], " ", $candidato->rua)),
                    utf8_encode($candidato->bairro),
                    $candidato->numero,
                    utf8_encode(str_replace(["'", '"', ",", ";"], " ", $candidato->complemento)),
                    $candidato->cep,
                    utf8_encode($candidato->uf),
                    utf8_encode($candidato->municipio),
                    utf8_encode($candidato->zona_residencia),
                    utf8_encode($candidato->pais_nascimento),
                    utf8_encode($candidato->nacionalidade),
                    $candidato->telefone,
                    $candidato->cpf_candidato,
                    $candidato->identidade,
                    utf8_encode($candidato->orgao_identidade),
                    $candidato->cartao_sus,
                    utf8_encode($candidato->bolsa_familia),
                    $candidato->numero_bolsa_familia,
                    $candidato->visto_candidato,
                    $candidato->rne_candidato,
                    utf8_encode($candidato->tipo_certidao),
                    $candidato->termo_certidao,
                    utf8_encode($candidato->livro_certidao),
                    $candidato->folha_certidao,
                    $candidato->cartorio_certidao,
                    $candidato->matricula_certidao,
                    utf8_encode($candidato->estado_certidao),
                    utf8_encode($candidato->municipio_certidao),
                    $candidato->data_certidao,
                    $candidato->data_nascimento,
                    utf8_encode($candidato->estado_nascimento),
                    utf8_encode($candidato->municipio_nascimento),
                    utf8_encode($candidato->estado_civil),
                    utf8_encode($candidato->email_candidato),
                    utf8_encode($candidato->sexo),
                    utf8_encode($candidato->raca_candidato),
                    utf8_encode($candidato->cadeirante),
                    utf8_encode($candidato->necessidades_especiais),
                    utf8_encode($candidato->filiacao_um),
                    $candidato->cpf_filiacao_um,
                    utf8_encode($candidato->filiacao_dois),
                    $candidato->cpf_filiacao_dois,
                    utf8_encode($candidato->tipo_responsavel),
                    utf8_encode($candidato->nome_responsavel),
                    $candidato->telefone_responsavel,
                    $candidato->identidade_responsavel,
                    utf8_encode($candidato->orgao_identidade_responsavel),
                    $candidato->cpf_responsavel,
                    utf8_encode(str_replace(["'", '"', ",", ";"], " ", $candidato->email_responsavel)),
                    $candidato->visto_responsavel,
                    $candidato->rne_responsavel,
                    utf8_encode($candidato->responsavel_trabalhador),
                    utf8_encode($candidato->mae_vitima_violencia),
                    $candidato->telefone_adicional,
                    utf8_encode($candidato->renda_familiar),
                    utf8_encode($candidato->etapa_pretendida),
                    utf8_encode($candidato->rede_origem),
                    utf8_encode($candidato->irmao_gemeo),
                    $candidato->data_cadastro,
                    $candidato->hora_cadastro,
                    $candidato->data_hora_manutencao,
                    $candidato->protocolo,
                    $candidato->base,
                    utf8_encode($candidato->situacao_alocado),
                    !empty($candidato->escola_alocado) ? utf8_encode($candidato->escola_alocado) : '-'
                ];
                if (array_key_exists('criteriosAlocacao', $candidato)) {
                    $criterios = implode(', ', $candidato->criteriosAlocacao);
                    $linha[] = utf8_encode($criterios);
                } else {
                    $linha[] = '-';
                }
                foreach ($candidato->opcoes as $opcao) {
                    $linha[] = utf8_encode($opcao->escola);
                    $linha[] = $opcao->classificacao;
                }
                $linha = array_pad($linha, count($this->cabecalho), '-');
                $dadosImprimir[] = $linha;
            }
        }
        return $dadosImprimir;
    }
}
