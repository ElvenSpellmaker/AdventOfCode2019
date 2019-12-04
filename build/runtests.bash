#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

FILES="$(find "$DIR/../" -regextype sed -regex '.*/d[[:digit:]]\+p[[:digit:]]\+\.php')"

exitCode=0

while read -r file; do
	DAY_NAME="$(basename "$file")"
	EXPECTED_FILE="${DAY_NAME%.php}.txt"

	ACTUAL="$(php $file)"
	EXPECTED="$(<"$DIR/$EXPECTED_FILE")"

	echo -n "$DAY_NAME: "
	if [ "$EXPECTED" == "$ACTUAL" ]; then
		echo -e "\e[0;32mPASS\e[0m"
	else
		echo -e "\e[0;31mFAIL\e[0m"
		exitCode=1
	fi
done <<< "$FILES"

exit $exitCode
