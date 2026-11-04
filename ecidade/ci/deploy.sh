#!/bin/sh

IFS=$'\n\t'
unset GREP_OPTIONS
unalias -a

if [[ "$DBSELLER_ID_RSA" = "" ]]; then
    echo "Variavel nao encontrada: DBSELLER_ID_RSA"
    exit 2
fi

if [[ "$DBSELLER_TOKEN" = "" ]]; then
    echo "Variavel nao encontrada: DBSELLER_TOKEN"
    exit 2
fi

if [[ "$DBSELLER_IP_DOCKER" = "" ]]; then
    echo "Variavel nao encontrada: DBSELLER_IP_DOCKER"
    exit 2
fi

chmod og= $DBSELLER_ID_RSA

apk update && apk add openssh-client

#ssh -i $DBSELLER_ID_RSA -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null dbseller-deployer@$DBSELLER_IP_DOCKER -p ${1:-22} "git config --global --add safe.directory /opt/dbseller/aplicacoes/ecidade/"
#ssh -i $DBSELLER_ID_RSA -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null dbseller-deployer@$DBSELLER_IP_DOCKER -p ${1:-22} "cd /opt/dbseller/aplicacoes/ecidade/ && git config core.fileMode false"

echo "FAZENDO CHECKOUT"
ret=$(ssh -i $DBSELLER_ID_RSA -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null dbseller-deployer@$DBSELLER_IP_DOCKER -p ${1:-22} "cd /opt/dbseller/aplicacoes/ecidade/ && git checkout .  > /dev/null 2>&1 && echo 0 || echo 1")
if [[ "$ret" != "0" ]]; then
    echo "FALHOU AO FAZER O CHECKOUT"
    exit 1
fi

echo "FAZENDO CHECKOUT PARA MASTER"
ret=$(ssh -i $DBSELLER_ID_RSA -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null dbseller-deployer@$DBSELLER_IP_DOCKER -p ${1:-22} "cd /opt/dbseller/aplicacoes/ecidade/ && git checkout master > /dev/null 2>&1 && echo 0 || echo 1")
if [[ "$ret" != "0" ]]; then
    echo "FALHOU AO FAZER CHECKOUT PARA MASTER"
    exit 1
fi

echo "FAZENDO FETCH"
ret=$(ssh -i $DBSELLER_ID_RSA -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null dbseller-deployer@$DBSELLER_IP_DOCKER -p ${1:-22} "export TOKEN=$DBSELLER_TOKEN; export GIT_ASKPASS=/home/dbseller-deployer/.git-askpass; cd /opt/dbseller/aplicacoes/ecidade/ && git fetch -p --all > /dev/null 2>&1 && echo 0 || echo 1")
if [[ "$ret" != "0" ]]; then
    echo "FALHOU AO FAZER O FETCH"
    exit 1
fi

echo "FAZENDO PULL"
ret=$(ssh -i $DBSELLER_ID_RSA -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null dbseller-deployer@$DBSELLER_IP_DOCKER -p ${1:-22} "export TOKEN=$DBSELLER_TOKEN; export GIT_ASKPASS=/home/dbseller-deployer/.git-askpass; cd /opt/dbseller/aplicacoes/ecidade/ && git pull origin master > /dev/null 2>&1 && echo 0 || echo 1")
if [[ "$ret" != "0" ]]; then
    echo "FALHOU AO FAZER O PULL"
    exit 1
fi

echo "FAZENDO COMPOSER INSTALL"
ret=$(ssh -i $DBSELLER_ID_RSA -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null dbseller-deployer@$DBSELLER_IP_DOCKER -p ${1:-22} "cd /opt/dbseller/aplicacoes/ecidade/ && /usr/bin/php7.4 /usr/local/bin/composer install --no-interaction --prefer-dist --no-progress --no-suggest > /dev/null 2>&1 && echo 0 || echo 1")
if [[ "$ret" != "0" ]]; then
    echo "FALHOU AO FAZER O COMPOSER INSTALL"
    exit 1
fi

echo "FAZENDO MIGRATE"
ret=$(ssh -i $DBSELLER_ID_RSA -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null dbseller-deployer@$DBSELLER_IP_DOCKER -p ${1:-22} "cd /opt/dbseller/aplicacoes/ecidade/ && /usr/bin/php7.4 artisan migrate --force > /dev/null 2>&1 && echo 0 || echo 1")
if [[ "$ret" != "0" ]]; then
    echo "FALHOU AO FAZER O MIGRATE"
    exit 1
fi

echo "CRIANDO ESTRUTURA DE DIRETORIOS TEMPORARIOS"
ret=$(ssh -i $DBSELLER_ID_RSA -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null dbseller-deployer@$DBSELLER_IP_DOCKER -p ${1:-22} "cd /opt/dbseller/aplicacoes/ecidade/ && mkdir -p /tmp/.cypress &> /dev/null && chmod -R 777 /tmp/.cypress &> /dev/null && mkdir -p /tmp/.electron &> /dev/null && chmod -R 777 /tmp/.electron &> /dev/null && echo 0 || echo 1")
if [[ "$ret" != "0" ]]; then
    echo "FALHOU AO CRIAR ESTRUTURA DE DIRETORIOS TEMPORARIOS"
    exit 1
fi

echo "REALIZANDO NPM CI"
ret=$(ssh -i $DBSELLER_ID_RSA -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null dbseller-deployer@$DBSELLER_IP_DOCKER -p ${1:-22} "cd /opt/dbseller/aplicacoes/ecidade/ && export CYPRESS_CACHE_FOLDER=/tmp/.cypress ; export electron_config_cache=/tmp/.electron; npm ci &> /dev/null && echo 0 || echo 1")
if [[ "$ret" != "0" ]]; then
    echo "FALHOU AO REALIZAR NPM CI"
    exit 1
fi

echo "REALIZANDO NPM RUN PROD"
ret=$(ssh -i $DBSELLER_ID_RSA -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null dbseller-deployer@$DBSELLER_IP_DOCKER -p ${1:-22} "cd /opt/dbseller/aplicacoes/ecidade/ && export CYPRESS_CACHE_FOLDER=/tmp/.cypress ; export electron_config_cache=/tmp/.electron; npm run prod &> /dev/null && echo 0 || echo 1")
if [[ "$ret" != "0" ]]; then
     echo "FALHOU AO REALIZAR NPM RUN PROD"
    exit 1
fi

echo "REALIZANDO LIMPEZA NODE_MODULES"
ret=$(ssh -i $DBSELLER_ID_RSA -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null dbseller-deployer@$DBSELLER_IP_DOCKER -p ${1:-22} "cd /opt/dbseller/aplicacoes/ecidade/ && rm -rf node_modules &> /dev/null && echo 0 || echo 1")
if [[ "$ret" != "0" ]]; then
     echo "FALHOU AO REALIZAR LIMPEZA NODE_MODULES"
    exit 1
fi

echo "REALIZANDO ARTISAN DBSELLER:CLEAR"
ret=$(ssh -i $DBSELLER_ID_RSA -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null dbseller-deployer@$DBSELLER_IP_DOCKER -p ${1:-22} "cd /opt/dbseller/aplicacoes/ecidade/ && /usr/bin/php7.4 artisan dbseller:clear > /dev/null 2>&1 && echo 0 || echo 1")
if [[ "$ret" != "0" ]]; then
    echo "FALHOU AO REALIZAR ARTISAN DBSELLER:CLEAR"
    exit 1
fi

echo "REALIZANDO CHOWN WWW-DATA"
ssh -i $DBSELLER_ID_RSA -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null dbseller-deployer@$DBSELLER_IP_DOCKER -p ${1:-22} "sudo /bin/chown -R www-data.www-data /opt/dbseller/aplicacoes/ecidade/"
