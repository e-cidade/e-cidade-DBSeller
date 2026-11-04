#!/bin/bash

set -e

error() {
    MESSAGE=$1

    printf "\e[1m\e[31m"
    figlet -f standard "ERRO"
    echo -e ${MESSAGE}
    printf "\e[0m\e[0m"
}
