#!/usr/bin/env bash
declare -a images;

build=false
push=false
case "$1" in
    "build")
        build=true;
        folder="$2";
        ;;
    "push")
        push=true;
        folder="$2";
        ;;
    *)
        build=true
        push=true
        folder=$1;
        ;;
esac

if [ -n "$folder" ]; then
    images+=("$folder");
else
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
            docker build -t webcoastdk/"$repository":"$tag" "$repository/$tag"
        fi
        if $push; then
            docker push webcoastdk/"$repository":"$tag"
        fi
    fi
done
