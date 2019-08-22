#!/usr/bin/env bash
declare -a images;

build=false
push=false
verbose=false
useLocalCache=false

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
            "-v"|"--verbose")
                verbose=true
                shift;;
            "-l"|"--local-cache")
                useLocalCache=true
                shift;;
            *)
                if [ ${build} == false -a ${push} == false ]; then
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
    for firstLevelFolder in `find . -maxdepth 1 -type d -not -iname '.*'`; do
        for secondLevelFolder in `find $firstLevelFolder -mindepth 1 -maxdepth 1 -type d -not -iname '.*' -not -name 'template'`; do
            images[${#images[*]}]="$secondLevelFolder";
        done
    done
fi

for path in ${images[*]}; do
    if [ -d "$path" ] && [[ ! "$path" =~ template$ ]] && [[ "$path" =~ ^\.?\/?([^\/]+)\/([^\/]+)\/?$ ]]; then
        repository=${BASH_REMATCH[1]}
        tag=${BASH_REMATCH[2]}
        if ${build}; then
            if ! ${useLocalCache}; then
                cacheFrom=" --cache-from webcoastdk/$repository:$tag"
            fi
            if ${verbose}; then
                docker build --pull ${cacheFrom} -t webcoastdk/"$repository":"$tag" "$repository/$tag"
            else
                echo -n "Building $repository:$tag... "
                output=$(docker build --pull ${cacheFrom} -t webcoastdk/"$repository":"$tag" "$repository/$tag" 2>&1)
                result=$?
                if [ ${result} -gt 0 ]; then
                    echo "failed"
                    echo "$output"
                    exit 1;
                else
                    echo "ok"
                fi
            fi
        fi

        if ${push}; then
            if [ ${verbose} == true ]; then
                docker push webcoastdk/"$repository":"$tag"
            else
                echo -n "Pushing $repository:$tag... "
                output=$(docker push webcoastdk/"$repository":"$tag" 2>&1)
                result=$?
                if [ ${result} -gt 0 ]; then
                    echo "failed"
                    echo "$output"
                    exit 1;
                else
                    echo "ok"
                fi
            fi
        fi
    fi
done
