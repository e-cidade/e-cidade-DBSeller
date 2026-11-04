#!/bin/bash

set -e
set -x

source ci/scripts/info.sh
source ci/scripts/error.sh

REF=$1
git fetch origin --prune --depth 1
COMMITS_BEHIND=$(git rev-list --count "$REF..origin/master")
COMMITS_BEHIND=$((${COMMITS_BEHIND}-1))

if [[ ${COMMITS_BEHIND} > 0 ]]; then
    error "BRANCH DESNIVELADA. $COMMITS_BEHIND COMMITS BEHIND MASTER"
    exit 1
fi