#!/bin/bash

set -o allexport; source .env; set +o allexport;
docker-compose --env-file=./.env up -d;