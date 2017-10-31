# Docker application using Minio & PHP backend

Playground project to work with [Minio Cloud Storage](https://minio.io/).

Features:
- Create a bucket & its public policy on first usage (in the APP)
- Upload files
- List uploaded files
- Persist data in a docker volume

## Requirements

- Docker & Docker Compose
- VirtualBox & Docker Machine for playing with a remote docker host

## Installation using a local docker host/daemon

1. Clone this project
2. Install APP dependencies
3. Start the containers
4. Browse http://localhost/index.php

### Clone this project

```bash
git clone https://github.com/aanton/docker-minio-demo.git docker-minio-demo
cd docker-minio-demo
```

### Install APP dependencies

Run composer locally in the `app` folder:

```bash
composer install --working-dir app
```

### Start the containers

```bash
docker-compose up # stop them: CTRL+C
docker-compose up -d # stop them using: docker-compose down
```

### Cleanup

- Stop & remove containers & networks
- Remove uploaded files

```bash
docker-compose down

sudo find .minio -mindepth 1 -maxdepth 1 -type d -exec rm -r {} \;
```

## Installation using a docker host/daemon in a virtual machine

1. Create the virtual machine (VirtualBox is used)
    - Set share folder
2. Set the docker client to connect to use the remote docker host/daemon
3. Clone this project in the container
4. Install APP dependencies in the container
5. Start the containers
6. Browse http://[docker-host]/index.php

### Create virtual machine

```bash
# Set the local working directory
mkdir demo-minio && cd $_

# Create virtual machine
docker-machine create -d virtualbox --virtualbox-share-folder $(pwd):$(pwd) vm1

# Get docker host IP
docker-machine ip vm1
```

### Set the docker client

```bash
# Set the (local) docker client to wotk with the remote docker host/daemon
eval $(docker-machine env vm1)

# Check there are no images, containers & volumes in the remote docker host/daemon
docker system df
```

### Clone this project

```bash
docker run --rm \
    --volume $(pwd):/git \
    --user $(id -u):$(id -g) \
    composer \
    git clone https://github.com/aanton/docker-minio-demo.git /git
```

### Install APP dependencies

Run composer in the `app` folder:

```bash
docker run --rm --interactive --tty \
    --volume $(pwd)/app:/app \
    --user $(id -u):$(id -g) \
    composer install
```

### Start the containers

- Set docker host IP (required in docker-compose.yml)
- Start the containers

```bash
export DOCKER_HOST_IP=$(docker-machine ip ${DOCKER_MACHINE_NAME:-default})

docker-compose up # stop them: CTRL+C
docker-compose up -d # stop them using: docker-compose down
```

### Cleanup

- Stop & remove containers & networks
- Stop & destroy virtual machine
- Remove uploaded files


```bash
docker-compose down

docker-machine stop vm1
docker-machine rm vm1

sudo find .minio -mindepth 1 -maxdepth 1 -type d -exec rm -r {} \;
```
