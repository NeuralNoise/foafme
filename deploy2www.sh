#!/bin/bash
set -x
if [ $# -lt 2 ]
then
    echo ""
    echo "Usage: $0 [src-dir] [dst-dir]"
    echo ""
    echo "Example: ./$0 . relay@relay.me:~/www/foaf.me/"
    echo ""
    echo "NOTE:"
    echo "Files(patterns) in ./rsync_exclude.txt will not be transferred."
else
    $exclude_list
    if test -f rsync_exclude.txt
    then
        $exclude_list="--exclude-from=rsync_exclude.txt"
    fi
    rsync --progress -azC --force --delete $exclude_list -e "ssh -p22" $1 $2;
fi