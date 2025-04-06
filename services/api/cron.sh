#!/bin/bash

export TZ="Europe/Moscow"

CURRENT_DIRECTORY="$(dirname "$(readlink -f "$0")")"

"$CURRENT_DIRECTORY/console.sh" schedule:run &