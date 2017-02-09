#!/usr/bin/env bash
docker pull koolob/swoole-docker
docker build -t serverx:0.1 -f docker/Dockerfile .
docker run -t -i -p 9797:9797 serverx:0.1