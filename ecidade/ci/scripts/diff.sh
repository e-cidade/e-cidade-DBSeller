#!/bin/bash

set -e
set -x

source ci/scripts/info.sh
source ci/scripts/error.sh

if [[ ${CI_MERGE_REQUEST_SOURCE_BRANCH_NAME} != ${CI_MERGE_REQUEST_TITLE} ]]; then
    error "O NOME DO MERGE REQUEST DEVE SER IGUAL AO NOME DA BRANCH"
    exit 1
fi

TXT=ci/tags/${CI_MERGE_REQUEST_TITLE}.txt

info "Fetching changes..."
git fetch origin --prune --depth 1

info "Running git diff..."
#touch ${TXT}
> ${TXT}

info "Saving list of modified files..."
git diff --name-only origin/master origin/${CI_MERGE_REQUEST_TITLE} >> ${TXT}

MODIFIED_FILES=$(cat ${TXT} | sort | uniq)
echo ${MODIFIED_FILES} > ${TXT}

info "Modified files:"
echo "$(cat ${TXT})"
