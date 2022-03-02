#!/usr/bin/env bash

if [[ ${RUN_PHPCS} == 1 ]]; then
	CHANGED_FILES=`git diff --name-only --diff-filter=ACMR $TRAVIS_COMMIT_RANGE | grep \\\\.php | awk '{print}' ORS=' '`
	IGNORE="webpack.config.js,tests/cli/,includes/libraries/,includes/api/legacy/,analytics/,vendor/,node_modules/,languages/,public/js/,public/css/,admin/js/,admin/css/"


	echo "Running Code Sniffer."
	vendor/bin/phpcs --standard=WordPress --ignore=$IGNORE --encoding=utf-8 -s -n -p /home/runner/work/gdpr-cookie-consent/gdpr-cookie-consent/

fi