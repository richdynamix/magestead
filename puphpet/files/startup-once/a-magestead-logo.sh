#!/bin/bash

cwd=$(pwd)

# DATA="$1"
# DATA=("$@")


ARR=( )
I=0

while [ "$#" -gt 0 ]
do
        ARR[$I]="$1"
        shift
        ((I++))
done

# echo DATA['apps']['mba_12345']['type']
echo ARR

# cat "ascii-art/magestead-logo.txt"
# printf "\n"
# echo "${cwd}"