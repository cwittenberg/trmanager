#!/bin/bash

#Start SSH agent if none is running
ssh-add -l &>/dev/null
if [ "$?" == 2 ]; then
  test -r /tmp/.ssh-agent && \
    eval "$(</tmp/.ssh-agent)" >/dev/null

  ssh-add -l &>/dev/null

  if [ "$?" == 2 ]; then
    (umask 066; ssh-agent > /tmp/.ssh-agent)
    eval "$(</tmp/.ssh-agent)" >/dev/null
    ssh-add
  fi
fi
