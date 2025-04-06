#!/bin/bash

CURRENT_DIRECTORY="$(dirname "$(readlink -f "$0")")"
cd "$CURRENT_DIRECTORY" || exit 1

RESULT=$(php "$CURRENT_DIRECTORY/console" "$@" &)

echo "$RESULT" >> "/var/log/$(date +%Y-%m-%d)-console.log" 2>&1