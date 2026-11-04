#!/bin/bash

# Texto a ser inserido no início de cada arquivo
HEADER='<?php
echo "<div>
    <h3>
    Você não tem permissão de acessar esse item de menu.</h3>
    <a target='"'"'_blank'"'"' href='"'"'https://ecidade.epdvr.com.br/voltaredonda/login.php'"'"'>acesse aqui</a>
</div>";
exit;
?>
'

# Lê o arquivo de lista passado como argumento
LISTA="${1}"

if [[ -z "$LISTA" ]]; then
    echo "Uso: $0 <arquivo_de_lista>"
    exit 1
fi

if [[ ! -f "$LISTA" ]]; then
    echo "Arquivo '$LISTA' não encontrado."
    exit 1
fi

while IFS= read -r arquivo; do
    # Ignora linhas vazias
    [[ -z "$arquivo" ]] && continue

    if [[ ! -f "$arquivo" ]]; then
        echo "!!  Arquivo não encontrado, pulando: $arquivo"
        continue
    fi

    # Insere o header no início sem sobrescrever o conteúdo original
    echo "$HEADER" | cat - "$arquivo" > /tmp/_tmp_php && mv /tmp/_tmp_php "$arquivo"
    echo " Atualizado: $arquivo"

done < "$LISTA"

echo ""
echo "Concluído."