#!/usr/bin/env sh

curl -s https://api.github.com/repos/nginx/nginx/tags | jq -r '.[]|select(has("name"))|select(.name|test("^release-\\d+\\.\\d+\\.\\d+$")) | .name|capture("^release-(?<versionNumber>\\d+\\.\\d+\\.\\d+)$")|.versionNumber'
