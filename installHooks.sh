#!/bin/bash

get_script_dir () {
     SOURCE="${BASH_SOURCE[0]}"
     # While $SOURCE is a symlink, resolve it
     while [ -h "$SOURCE" ]; do
          DIR="$( cd -P "$( dirname "$SOURCE" )" && pwd )"
          SOURCE="$( readlink "$SOURCE" )"
          # If $SOURCE was a relative symlink (so no "/" as prefix, need to resolve it relative to the symlink base directory
          [[ $SOURCE != /* ]] && SOURCE="$DIR/$SOURCE"
     done
     DIR="$( cd -P "$( dirname "$SOURCE" )" && pwd )"
     echo "$DIR"
}

if [ "$(git rev-parse --is-inside-work-tree)" == "true" ]; then

  _directory=$(git rev-parse --show-toplevel)
  _hooksDirectory=$_directory/.git/hooks

  hooks=$(get_script_dir)/hooks/*

  for hook in $hooks;
  do
    # Here we loop the files found in the codequality kit hooks folder
    # And symlink them to the new folder where this command is being called
    # maybe we should also create a base config.yml in the root of the users project
    ln -fs $hook $_directory/.git/hooks $_hooksDirectory

    echo $hook
  done

  echo "FROM: $(get_script_dir)/hooks"
  echo "TO:   $_hooksDirectory"

fi
