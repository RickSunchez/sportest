#!/bin/bash

SCRIPT=$1
php $SCRIPT > /dev/null 2> /dev/null &!
