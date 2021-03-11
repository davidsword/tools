#!/bin/bash
#
# this script to be fired by cron, and deployed to PATH

# load in config variable
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
. "$DIR/_config.conf"

SIZEOFSOURCE=$(du -s --block-size=G ~)

# check the current size
CHECK="`du -hs ~`"
CHECK=${CHECK%G*}
echo "Home is: $CHECK GB"

if (( $(echo "$CHECK > $BACKUPSIZE" |bc -l) )); then
  echo "can't backup. target destination is only $BACKUPSIZE GB"
  #todo gnome notification
else
  echo "starting backup of ~/ to '$BACKUPTO'"
  #TODO add --delete when sure this works
  # rsync -a ~/ '$BACKUPTO'
fi



