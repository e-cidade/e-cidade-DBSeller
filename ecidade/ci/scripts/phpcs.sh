#!/bin/bash

set -e
source ci/scripts/info.sh

info "Fetching remote..."
git fetch origin --prune --depth 1

for FILE in $(git diff --name-only --diff-filter=d $1 $2 src/ app/)
do
    info "Checking code quality for $FILE..."
    phpcs --colors --standard=PSR2 --extensions=php ${FILE}
done
