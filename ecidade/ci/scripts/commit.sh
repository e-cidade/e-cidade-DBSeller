#!/bin/bash

set -e

source ci/scripts/info.sh
source ci/scripts/error.sh

BASE_PATH=$PWD
CVS_PATH=dbportal_prj
TAG_FILES_TXT=ci/tags/${CI_BUILD_TAG}.txt

if [[ ! -f "$TAG_FILES_TXT" ]]; then
    error "O NOME DA TAG DEVE SER IGUAL AO NOME DO MERGE REQUEST"
    exit 1;
fi

checkout() {
    cd ${BASE_PATH}
    info "Checking out CVS..."
    rm -rf ${CVS_PATH}
    cvs -Q checkout -P -d ${CVS_PATH} dbportal_prj
}

commit() {
    PATH_FRAGMENT=$1

    if [[ -f "$PATH_FRAGMENT" ]]; then
        info "Committing $PATH_FRAGMENT..."
        cvs -Q commit -m "GitLab Tag $CI_BUILD_TAG" ${PATH_FRAGMENT}

        info "Tagging $PATH_FRAGMENT..."
        cvs -Q tag -F ${CI_BUILD_TAG} ${PATH_FRAGMENT}
    fi
}

info "Logging into CVS..."
cvs -Q login

if [[ -d "$CVS_PATH" ]]; then
    info "Updating CVS..."
    cd ${CVS_PATH}
    cvs -Q update || checkout
    cd ${BASE_PATH}
else
    checkout
fi

info "Getting modified files..."
TAG_FILES=$(cat "$TAG_FILES_TXT")

for TAG_FILE in ${TAG_FILES}
do
    if [[ -z "$TAG_FILE" ]]; then
        continue
    fi

    info "Syncing $TAG_FILE..."

    set +e
    rsync -azq --relative ${TAG_FILE} ${CVS_PATH} 2> /dev/null
    EXIT_CODE=$?
    set -e

    case ${EXIT_CODE}
    in
        23)
            info "Entering $CVS_PATH..."
            cd ${CVS_PATH}

            info "Untagging $TAG_FILE..."
            cvs -Q tag -d ${CI_BUILD_TAG} ${TAG_FILE}

            info "Removing $TAG_FILE..."
            rm -rf ${TAG_FILE}

            info "Running cvs remove for $TAG_FILE..."
            cvs -Q remove ${TAG_FILE}

            info "Committing $TAG_FILE..."
            cvs -Q commit -m "Arquivo removido na GitLab Tag $CI_BUILD_TAG" ${TAG_FILE}

            info "Entering $BASE_PATH..."
            cd ${BASE_PATH}

            continue
        ;;
    esac

    info "Entering $CVS_PATH... "
    cd ${CVS_PATH}

    IFS='/' read -ra PATHS <<<"$TAG_FILE"
    for PATH_FRAGMENT in "${PATHS[@]}"; do
        info "Adding $PATH_FRAGMENT..."
        cvs -Q add -kkv ${PATH_FRAGMENT} || info "No changes for $PATH_FRAGMENT"

        commit ${PATH_FRAGMENT}
    
        if [[ -d "$PATH_FRAGMENT" ]]; then
            info "Entering $PATH_FRAGMENT..."
            cd ${PATH_FRAGMENT}
        fi
    done

    info "Entering $BASE_PATH..."
    cd ${BASE_PATH}
done

info "Successfully updated CVS"
