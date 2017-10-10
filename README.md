# Docker application using Minio & PHP backend

Playground project to work with [Minio Cloud Storage](https://minio.io/).

Features:
- Create a bucket & its public policy on first usage (in the APP)
- Upload files
- List uploaded files
- Persist data in a docker volume

## Requirements

- Docker & Docker Compose

## Installation

1. Install APP dependencies
2. Update configuration (environment variables, ports) in `docker-compose.yml`
3. Start the containers
4. Browse http://localhost/index.php

### Install APP dependencies

Run composer locally in the `app` folder or use a docker container:

```bash
docker pull composer
docker run --rm --interactive --tty \
    --volume $(pwd)/app:/app \
    --user $(id -u):$(id -g) \
    composer install
```

### Start the containers

Different alternatives:

```bash
# Build (if necessary) & start containers (CTRL+C to stop them)
docker-compose up

# Build (if necessary) & start containers in background mode (stop them using: docker-compose down)
docker-compose up -d

# Rebuild & start containers
docker-compose up --build
```

## Cleanup

- Stop & remove containers & networks (no named volumes used)
- Remove uploaded files

```bash
docker-compose down

# Remove uploaded files
sudo find .minio -mindepth 1 -maxdepth 1 -type d -exec rm -r {} \;
```