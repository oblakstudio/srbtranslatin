#!/usr/bin/env bash

VERSION=$(cat /tmp/wp-release/VERSION)
cp .wordpress-org/readme.txt ./readme.txt

echo "version=$VERSION" >> "$GITHUB_OUTPUT"
