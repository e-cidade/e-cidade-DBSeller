#!/bin/bash

# =========================
# Cores
# =========================
VERMELHO='\e[00;31m'
VERDE='\e[00;32m'
AMARELO='\e[00;33m'
AZUL='\033[1;34m'
FIN='\e[00m'

# =========================
# Instalacao
# =========================

cd /var/www/html

echo " "
echo " "
echo "========================================================================================"
echo "================================ Criando Banco de Dados ================================"
echo "========================================================================================"
# PostgreSQL 16 removed table OIDs and changed polymorphic array signatures.
psql -U ${DB_USERNAME} -h ${DB_HOST} -f docker/init.sql && \
sed -e 's/^SET default_with_oids = true;$/SET default_with_oids = false;/' \
    -e '/^GRANT SELECT ON TABLE pg_catalog.pg_pltemplate TO plugin;$/d' \
    -e '/^CREATE AGGREGATE public.array_accum(anyelement)/,/^);$/ { s/anyelement/anycompatible/g; s/anyarray/anycompatiblearray/g; }' \
    -e 's/^ALTER AGGREGATE public.array_accum(anyelement)/ALTER AGGREGATE public.array_accum(anycompatible)/' \
    docker/ecidade_base.sql | psql -v ON_ERROR_STOP=1 -U ${DB_USERNAME} -h ${DB_HOST} ${DB_DATABASE} && \
psql -U ${DB_USERNAME} -h ${DB_HOST} ${DB_DATABASE} -f docker/pos.sql && \
echo -e "${VERDE}[OK] - Banco de dados criado${FIN}" || echo -e "${VERMELHO}[ERROR] - Problema ao criar o banco de dados${FIN}"

echo " "
echo " "
echo "========================================================================================"
echo "================================ Configurando Variaveis ================================"
echo "========================================================================================"
cp -arf /var/www/html/.env.example /var/www/html/.env && \
cp -arf /var/www/html/libs/db_conn.php.dist /var/www/html/libs/db_conn.php && \
sed -i "s/DB_HOST=.*/DB_HOST=${DB_HOST}/g" /var/www/html/.env && \
sed -i "s/DB_DATABASE=.*/DB_DATABASE=${DB_DATABASE}/g" /var/www/html/.env && \
sed -i "s/DB_PORT=.*/DB_PORT=${DB_PORT}/g" /var/www/html/.env && \
sed -i "s/DB_USERNAME=.*/DB_USERNAME=${DB_USERNAME}/g" /var/www/html/.env && \
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=${DB_PASSWORD}/g" /var/www/html/.env && \
sed -i "s/\$DB_BASE.*/\$DB_BASE = \"${DB_DATABASE}\";/g" /var/www/html/libs/db_conn.php && \
sed -i "s/\$DB_SERVIDOR.*/\$DB_SERVIDOR = \"${DB_HOST}\";/g" /var/www/html/libs/db_conn.php && \
sed -i "s/\$DB_PORTA.*/\$DB_PORTA = \"${DB_PORT}\";/g" /var/www/html/libs/db_conn.php && \
sed -i "s/\$DB_USUARIO.*/\$DB_USUARIO = \"${DB_USERNAME}\";/g" /var/www/html/libs/db_conn.php && \
sed -i "s/\$DB_SENHA.*/\$DB_SENHA = \"${DB_PASSWORD}\";/g" /var/www/html/libs/db_conn.php && \
sed -i "s/DB_VALIDA_REQUISITOS.*/DB_VALIDA_REQUISITOS = false;/g" /var/www/html/libs/db_conn.php && \
echo -e "${VERDE}[OK] - Variaveis configuradas${FIN}" || echo -e "${VERMELHO}[ERROR] - Problema ao configurar as variaveis${FIN}"

echo " "
echo " "
echo "========================================================================================="
echo "================================ Instalando Dependencias ================================"
echo "========================================================================================="
composer install && \
composer configure-desktop && \
chgrp -R www-data storage bootstrap/cache tmp && \
chmod -R g+rwX storage bootstrap/cache tmp && \
find storage bootstrap/cache tmp -type d -exec chmod g+s {} + && \
php artisan cache:clear && \
php artisan key:generate && \
php artisan passport:keys && \
php artisan passport:install && \
echo -e "${VERDE}[OK] - Dependencias Instaladas${FIN}" || echo -e "${VERMELHO}[ERROR] - Problema ao instalar as dependencias${FIN}"
echo " "
echo " "
echo " "
echo -e "${AZUL} Instalação Conluida! Acesse o e-Cidade na URL: ${AMARELO}http://localhost:8080 ou http://127.0.0.1:8080${FIN}"
echo -e "${AZUL} Login: ${AMARELO}dbseller${FIN}"
echo -e "${AZUL} Senha: ${AMARELO}dbseller${FIN}"
echo " "
echo " "
echo " "
