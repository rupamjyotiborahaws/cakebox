#!/usr/bin/env bash
# wait-for-it.sh

set -e

host="mysql"
port="3306"
shift 2
cmd="$@"

echo "Waiting for $host:$port..."

for i in {1..30}; do
  if nc -z "$host" "$port"; then
    echo "$host:$port is available"
    exec $cmd
    exit
  fi
  echo "Still waiting... ($i)"
  sleep 1
done

echo "Timeout waiting for $host:$port"
exit 1
