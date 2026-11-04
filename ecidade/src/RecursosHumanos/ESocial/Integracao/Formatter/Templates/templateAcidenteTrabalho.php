<?php
return array(
    'ideVinculo' => array(
        'required' => true,
        'properties' => array(
            'cpfTrab' => array(
                'required' => true,
                'label' => 'Código pessoa física do trabalhador',
                'type' => 'string',
            ),
            'matricula' => array(
                'required' => false,
                'label' => 'Matrícula atribuída ao trabalhador',
                'type' => 'string',
            ),
            'codCateg' => array(
                'required' => false,
                'label' => 'Categoria do trabalhador',
                'type' => 'int'
            ),
        )
    ),
    'cat' => array(
        'required' => true,
        'label' => 'Comunicação de Acidente de Trabalho - CAT.',
        'properties' => array(
            'dtAcid' => array(
                'required' => true,
                'label' => 'Data do acidente.',
                'type' => 'string',
            ),
            'tpAcid' => array(
                'required' => true,
                'label' => 'Tipo de acidente de trabalho.',
                'type' => 'int',
            ),
            'hrAcid' => array(
                'required' => false,
                'label' => 'Hora do acidente, no formato HHMM.',
                'type' => 'string'
            ),
            'hrsTrabAntesAcid' => array(
                'required' => false,
                'label' => 'Horas trabalhadas antes da ocorrência do acidente, no formato HHMM.',
                'type' => 'string'
            ),
            'tpCat' => array(
                'required' => true,
                'label' => 'Tipo de CAT',
                'type' => 'int'
            ),
            'indCatObito' => array(
                'required' => true,
                'label' => 'Houve óbito?',
                'type' => 'string'
            ),
            'dtObito' => array(
                'required' => false,
                'label' => 'Data do óbito.',
                'type' => 'string'
            ),
            'indComunPolicia' => array(
                'required' => true,
                'label' => 'Houve comunicação à autoridade policial?',
                'type' => 'string'
            ),
            'codSitGeradora' => array(
                'required' => true,
                'label' => 'Código da situação geradora do acidente ou da doença profissional.',
                'type' => 'int'
            ),
            'iniciatCAT' => array(
                'required' => true,
                'label' => 'Iniciativa da CAT.',
                'type' => 'int'
            ),
            'obsCAT' => array(
                'required' => false,
                'label' => 'Observação.',
                'type' => 'string'
            ),
            'ultDiaTrab' => array(
                'required' => false,
                'label' => 'Último dia trabalhado.',
                'type' => 'string'
            ),
            'houveAfast' => array(
                'required' => false,
                'label' => 'Houve afastamento?',
                'type' => 'string'
            )
        ),
        'groups' => array(
            'localAcidente' => array(
                'required' => true,
                'label' => 'Local do acidente.',
                'properties' => array(
                    'tpLocal' => array(
                        'required' => true,
                        'label' => 'Tipo de local do acidente.',
                        'type' => 'int'
                    ),
                    'dscLocal' => array(
                        'required' => false,
                        'label' => 'Especificação do local do acidente (pátio, rampa de acesso, etc.).',
                        'type' => 'string'
                    ),
                    'tpLograd' => array(
                        'required' => false,
                        'label' => 'Tipo de logradouro.',
                        'type' => 'string'
                    ),
                    'dscLograd' => array(
                        'required' => true,
                        'label' => 'Descrição do logradouro.',
                        'type' => 'string'
                    ),
                    'nrLograd' => array(
                        'required' => true,
                        'label' => 'Número do logradouro.',
                        'type' => 'string'
                    ),
                    'complemento' => array(
                        'required' => false,
                        'label' => 'Complemento do logradouro.',
                        'type' => 'string'
                    ),
                    'bairro' => array(
                        'required' => false,
                        'label' => 'Nome do bairro/distrito.',
                        'type' => 'string'
                    ),
                    'cep' => array(
                        'required' => false,
                        'label' => 'Código de Endereçamento Postal - CEP.',
                        'type' => 'string'
                    ),
                    'codMunic' => array(
                        'required' => false,
                        'label' => 'código do município, conforme tabela do IBGE.',
                        'type' => 'int'
                    ),
                    'uf' => array(
                        'required' => false,
                        'label' => 'Sigla da Unidade da Federação - UF.',
                        'type' => 'string'
                    ),
                    'pais' => array(
                        'required' => false,
                        'label' => 'Preencher com o código do país.',
                        'type' => 'string'
                    ),
                    'codPostal' => array(
                        'required' => false,
                        'label' => 'Código de Endereçamento Postal.',
                        'type' => 'string'
                    )
                ),
                'groups' => array(
                    'ideLocalAcid' => array(
                        'required' => false,
                        'label' => 'Identificação do local onde ocorreu o acidente.',
                        'properties' => array(
                            'tpInsc' => array(
                                'required' => true,
                                'label' => 'Código correspondente ao tipo de inscrição do local.',
                                'type' => 'int'
                            ),
                            'nrInsc' => array(
                                'required' => true,
                                'label' => 'Informar o número de inscrição do estabelecimento.',
                                'type' => 'string'
                            )
                        )
                    )
                )
            ),
            'parteAtingida' => array(
                'required' => true,
                'label' => 'Detalhamento da parte atingida pelo acidente de trabalho.',
                'properties' => array(
                    'codParteAting' => array(
                        'required' => true,
                        'label' => 'Preencher com o código correspondente à parte atingida.',
                        'type' => 'int'
                    ),
                    'lateralidade' => array(
                        'required' => true,
                        'label' => 'PLateralidade da(s) parte(s) atingida(s).',
                        'type' => 'int'
                    )
                )
            ),
            'agenteCausador' => array(
                'required' => true,
                'label' => 'Detalhamento do agente causador do acidente de trabalho.',
                'properties' => array(
                    'codAgntCausador' => array(
                        'required' => true,
                        'label' => 'Preencher com o código correspondente ao agente causador do acidente.',
                        'type' => 'int'
                    )
                )
            ),
            'atestado' => array(
                'required' => true,
                'label' => 'Atestado médico.',
                'properties' => array(
                    'dtAtendimento' => array(
                        'required' => true,
                        'label' => 'Data do atendimento.',
                        'type' => 'string'
                    ),
                    'hrAtendimento' => array(
                        'required' => true,
                        'label' => 'Hora do atendimento, no formato HHMM.',
                        'type' => 'string'
                    ),
                    'indInternacao' => array(
                        'required' => true,
                        'label' => 'Indicativo de internação.',
                        'type' => 'string'
                    ),
                    'durTrat' => array(
                        'required' => true,
                        'label' => 'Duração estimada do tratamento, em dias.',
                        'type' => 'int'
                    ),
                    'indAfast' => array(
                        'required' => true,
                        'label' => 'Indicativo de afastamento do trabalho durante o tratamento.',
                        'type' => 'string'
                    ),
                    'dscLesao' => array(
                        'required' => true,
                        'label' => 'Preencher com a descrição da natureza da lesão.',
                        'type' => 'int'
                    ),
                    'dscCompLesao' => array(
                        'required' => false,
                        'label' => 'Descrição complementar da lesão.',
                        'type' => 'string'
                    ),
                    'diagProvavel' => array(
                        'required' => false,
                        'label' => 'Diagnóstico provável.',
                        'type' => 'string'
                    ),
                    'codCID' => array(
                        'required' => true,
                        'label' => 'Classificação Internacional de Doenças - CID.',
                        'type' => 'string'
                    ),
                    'observacao' => array(
                        'required' => false,
                        'label' => 'Observação.',
                        'type' => 'string'
                    )
                ),
                'groups' => array(
                    'emitente' => array(
                        'required' => true,
                        'label' => 'Médico/Dentista que emitiu o atestado.',
                        'properties' => array(
                            'nmEmit' => array(
                                'required' => true,
                                'label' => 'Nome do médico/dentista que emitiu o atestado.',
                                'type' => 'string'
                            ),
                            'ideOC' => array(
                                'required' => true,
                                'label' => 'Órgão de classe.',
                                'type' => 'int'
                            ),
                            'nrOC' => array(
                                'required' => true,
                                'label' => 'Número de inscrição no órgão de classe.',
                                'type' => 'string'
                            ),
                            'ufOC' => array(
                                'required' => true,
                                'label' => 'Sigla da UF do órgão de classe.',
                                'type' => 'string'
                            )
                        )
                    )
                )
            ),
            'catOrigem' => array(
                'required' => false,
                'label' => 'Grupo que indica a CAT anterior, caso de CAT de reabertura ou de comunicação de óbito.',
                'properties' => array(
                    'nrRecCatOrig' => array(
                        'required' => true,
                        'label' => 'Informar o número do recibo da última CAT',
                        'type' => 'string'
                    )
                )
            )
        )
    )
);
