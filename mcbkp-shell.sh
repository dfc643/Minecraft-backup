#!/bin/bash
#debug switch  -xv

#filename : mcbk.sh
#author : nyacat

#settings
  #default var
    backup_path="~/mcbackups"
    server_path="~/cauldron"
    map_name="world"
    backup_mode=1
    pcs_token=""
    pcs_appid=""
    backup_name="minecraft_server_bk-"`date +%Y%m%d_%H%M%S`".zip"

#usage
function usage () {
    echo "Usage:"
    echo "    backup mode"
    echo "    $0 -m 1/2/3"
    echo "        1.backup map only"
    echo "        2.backup map and mods"
    echo "        3.backup all files"
    echo
    echo "    show usage"
    echo "    $0 -H"
    echo
    echo "e.g:"
    echo "  $0 -m 1"
    echo
    exit 1
  }

#root user check
#    if [[ $UID -ne 0 ]];
#      then
#        echo "Please run $0 as root."
#        exit 2
#      fi

#usage check
    if [[ -z "$1" ]] || [[ -z "$2" ]] || [ $2 -gt 3 ] || [[ "$1" = "-H" ]]
      then
        usage
      fi

#first,remove older backups
    rm -f ${backup_path}"/minecraft_server_bk*.zip"

#second,create archive
    while getopts "m:H" opts
      do
        case ${opts} in
          m)
            backup_mode=${OPTARG}
          ;;
          H)
            usage
          ;;
          ?)
            usage
          ;;
          *)
            usage
          ;;
        esac
      done

    case ${backup_mode} in
          1)
            backup_name="minecraft_server_bk-"`date +%Y%m%d_%H%M%S`"-map.zip"
            server_path=${server_path}"/"${map_name}
          ;;
          2)
            backup_name="minecraft_server_bk-"`date +%Y%m%d_%H%M%S`"-map_mods.zip"
            server_path=${server_path}"/"${map_name}" "${server_path}"/mods"
          ;;
          3)
            server_path="minecraft_server_bk-"`date +%Y%m%d_%H%M%S`"-all.zip"
          ;;
          *)
            usage
          ;;
        esac

  if [ -d "$server_path" ]; then
    zip -q -r ${backup_path}"/"${backup_name} ${server_path}
  else
    echo "no such dir"
  fi

#upload to pcs
  if [ -a "${backup_path}"/"${backup_name}" ]&&[ -s "${backup_path}"/"${backup_name}" ]; then
    curl -k -L -F 'file=@'${backup_path}"/"${backup_name} 'https://c.pcs.baidu.com/rest/2.0/pcs/file?method=upload&access_token='$pcs_token'&path=/apps/'$pcs_appid'/'$backup_name
  else
    echo "no such file"
  fi
