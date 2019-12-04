#!/bin/bash

FILES="$(find ../ -name 'd*.php')"

exitCode=0

while read -r file; do
	DAY="${file:3}"
	EXPECTED_FILE="${DAY%.php}.txt"

	ACTUAL="$(php $file)"
	EXPECTED="$(<"$EXPECTED_FILE")"

	echo -n "$DAY: "
	if [ "$EXPECTED" == "$ACTUAL" ]; then
		echo -e "\e[0;32mPASS\e[0m"
	else
		echo -e "\e[0;31mFAIL\e[0m"
		exitCode=1
	fi
done <<< "$FILES"

exit $exitCode
