<?php

$DB_CONEXAO = array();

if (isset($_GET['servidor'], $_GET['porta'], $_GET['usuario'])) {
    $CONEXAO['BASE'] = isset($_GET['base']) ? $_GET['base'] : '%';
    $CONEXAO['SERVIDOR'] = $_GET['servidor'];
    $CONEXAO['PORTA'] = $_GET['porta'];
    $CONEXAO['USUARIO'] = $_GET['usuario'];
    $CONEXAO['SENHA'] = $_GET['senha'];
    $DB_CONEXAO[] = $CONEXAO;
}

$con = pg_connect('host=grayskull.dbseller.com.br port=5434 user=array_servidores password=halegria dbname=dbdownloader_db');
$result = pg_query($con, 'SELECT * FROM clusters');

while ($row = pg_fetch_object($result)) {
    $CONEXAO['SERVIDOR'] = $row->servidor;
    $CONEXAO['BASE'] = $row->base;
    $CONEXAO['PORTA'] = $row->porta;
    $CONEXAO['USUARIO'] = $row->usuario;
    $CONEXAO['SENHA'] = $row->senha;
    $DB_CONEXAO[] = $CONEXAO;
}

pg_close($con);
