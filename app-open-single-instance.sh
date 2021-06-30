#!/bin/sh

# Script for custom launcher shortcuts to switch to active window 
# if one exists, instead of opening a new window
#
# Eg. for terminal super+t custom shortcut: 
# > /app-open-single-instance.sh gnome-terminal
#
# @credit: https://askubuntu.com/questions/82273/single-instance-of-the-gnome-terminal
# @requires: wmctrl

if ps ax | grep -v grep | grep $@ > /dev/null
then
  wmctrl -xa $@
else
  $@
fi