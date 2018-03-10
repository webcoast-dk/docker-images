#!/usr/bin/env bash
declare -a images;

build=false
push=false

if [ "$#" -gt 0 ]; then
    while [ "$#" -gt 0 ]; do
        case "$1" in
            "build")
                build=true;
                shift
                ;;
            "push")
                push=true;
                shift
                ;;
            *)
                if ! $build and ! $push; then
                    build=true
                    push=true
                fi
                images+=("$1")
                shift
                ;;
        esac
    done
else
    build=true
    push=true
fi

# If not folders are given, use all
if [ ${#images} -eq 0 ]; then
    for firstLevelFolder in `find . -type d -not -iname '.*' -maxdepth 1`; do
        for secondLevelFolder in `find $firstLevelFolder -type d -not -iname '.*' -mindepth 1 -maxdepth 1`; do
            images[${#images[*]}]="$secondLevelFolder";
        done
    done
fi

for path in ${images[*]}; do
    if [[ "$path" =~ ^\.?\/?([^\/]+)\/([^\/]+)\/?$ ]]; then
        repository=${BASH_REMATCH[1]}
        tag=${BASH_REMATCH[2]}
        if $build; then
            docker build --pull -t webcoastdk/"$repository":"$tag" "$repository/$tag"
        fi
        if $push; then
            docker push webcoastdk/"$repository":"$tag"
        fi
    fi
done
