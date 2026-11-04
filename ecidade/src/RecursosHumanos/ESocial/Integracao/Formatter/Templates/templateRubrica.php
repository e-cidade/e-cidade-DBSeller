<?php
return array(
    'ideRubrica' => array(
        'properties' => array(
            'codRubr',
            'ideTabRubr',
            'iniValid',
            'fimValid'
        )
    ),
    'dadosRubrica' => array(
        'properties' => array(
            'dscRubr',
            'natRubr' => array(
                'type' => 'int'
            ),
            'tpRubr' => array(
                'type' => 'int'
            ),
            'codIncCP',
            'codIncIRRF',
            'codIncFGTS',
            'codIncCPRP',
            'codIncPisPasep',
            'tetoRemun',
            'observacao',
        ),
        'groups' => array(
            'ideProcessoCP' => array(
                'type' => 'array',
                'items' => array(
                    'properties' => array(
                        'tpProc' => array(
                            'type' => 'int'
                        ),
                        'nrProc',
                        'extDecisao' => array(
                            'type' => 'int'
                        ),
                        'codSusp'
                    )
                )
            ),
            'ideProcessoIRRF' => array(
                'type' => 'array',
                'items' => array(
                    'properties' => array(
                        'nrProc',
                        'codSusp'
                    )
                )
            ),
            'ideProcessoFGTS' => array(
                'type' => 'array',
                'items' => array(
                    'properties' => array(
                        'nrProc'
                    )
                )
            )
        )
    )
);
