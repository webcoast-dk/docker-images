#!/usr/bin/env sh

curl -s https://api.github.com/repos/composer/composer/releases | jq -r '.[] | select(has("tag_name")) | select(.tag_name | test("^\\d+.\\d+\\.\\d+$")) | .tag_name '
