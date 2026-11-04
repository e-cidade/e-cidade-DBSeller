<?php
$campos  = "distinct fis_lancamentotaxadiversos.y120_sequencial,                                                     ";
$campos .= "fis_lancamentotaxadiversos.y120_cgm,                                                                     ";
$campos .= "cgm.z01_nome,                                                                                        ";
$campos .= "fis_lancamentotaxadiversos.y120_taxadiversos,                                                            ";
$campos .= "fis_taxadiversos.y119_natureza,                                                                          ";
$campos .= "fis_lancamentotaxadiversos.y120_unidade,                                                                 ";
$campos .= "fis_lancamentotaxadiversos.y120_periodo,                                                                 ";
$campos .= "fis_lancamentotaxadiversos.y120_datainicio,                                                              ";
$campos .= "fis_lancamentotaxadiversos.y120_datafim,                                                                 ";
$campos .= "fis_lancamentotaxadiversos.y120_issbase,                                                                 ";
$campos .= "diversos.dv05_obs as db_observacao,                                                                  ";
$campos .= "case when diversoslancamentotaxa.dv14_diversos is null then 0 else 1 end as db_taxa_tem_calculo      ";
