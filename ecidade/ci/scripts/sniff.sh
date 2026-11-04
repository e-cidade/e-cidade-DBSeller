#!/bin/bash

set -e

source ci/scripts/info.sh

info "Sniffing code..."

BRANCH_ATUAL="$(git branch 2> /dev/null | sed -e '/^[^*]/d' -e 's/* \(.*\)/\1/')"

BRANCH_MASTER=$1
if [[ "$BRANCH_MASTER" = '' ]]; then
    BRANCH_MASTER='origin/master'
fi

sniff_fix() {
    vendor/bin/phpcbf -q --standard=ci/phpcs/ruleset.xml --colors "$@" > /dev/null || true
}

sniff_check() {
    vendor/bin/phpcs --standard=ci/phpcs/ruleset.xml --colors "$@" 2> /dev/null || true
}

for i in $(git diff --name-only --diff-filter=d ${BRANCH_MASTER} ${BRANCH_ATUAL}); do
	sniff_fix "$i"
    sniff_check "$i"
done

info "Done :)"
