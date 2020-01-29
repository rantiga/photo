#!/bin/bash

docker-compose up -d 
echo "Waiting! Sockets turn on..."
sleep 20 
docker exec -i mysql sh -c 'exec mysql -u user -p"password" -f develop' < ./dump/dump.sql
