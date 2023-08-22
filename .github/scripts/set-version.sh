#!/usr/bin/env bash

VERSION=$(cat /tmp/wp-release/VERSION)

echo "version=$VERSION" >> "$GITHUB_OUTPUT"
