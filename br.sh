#!/bin/bash

# Change brightness of LG Ultrafine display
#
# input values from 1-99
#
# @for PATH

$(xrandr --output DP-1 --brightness 0.$@)