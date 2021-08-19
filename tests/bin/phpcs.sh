#!/usr/bin/env bash

if [[ ${RUN_PHPCS} == 1 ]]; then
	CHANGED_FILES=`git diff --name-only --diff-filter=ACMR $TRAVIS_COMMIT_RANGE | grep \\\\.php | awk '{print}' ORS=' '`
	IGNORE="tests/cli/,includes/libraries/,includes/api/legacy/"


	echo "Running Code Sniffer."
	vendor/bin/phpcs --standard=phpcs.xml --ignore=$IGNORE --encoding=utf-8 -s -n -p

fi
