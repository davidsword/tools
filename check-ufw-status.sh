#!/bin/bash
#
# for Argo
#
# to deploy, needs to be sudo cron:
#
# sudo crontab -e
# * * * * * /bin/bash /home/davidsword/tools/check-ufw-status.sh


STATUS=$(ufw status)

if [[ "$STATUS" == *"inactive"* ]]; then
  OUTPUT="🔥 NO FIREWALL 🔥"
else
  OUTPUT="🔒"
fi

# @TODO check dir and file exist
# @TODO use relative dir instead of hard coded
# @TODO file permissions
echo $OUTPUT > "/home/davidsword/tools/cache/fire-wall-status.txt"
