# Minio demo using docker & PHP

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
3. Start containers
4. Browse http://localhost:8888 (use the configured port)

### Install APP dependencies

Run composer locally in the `app` folder or use a docker container:

```bash
docker pull composer
docker run --rm --interactive --tty \
    --volume $(pwd)/app:/app \
    --user $(id -u):$(id -g) \
    composer install
```

### Start containers

```bash
docker-compose up # add "-d" to run containers in background
```

### Stop containers

```bash
docker-compose down --volumes
```

## Clean up

```bash
docker-compose down --volumes
docker system prune -f
docker system df -v

# Remove uploaded files
sudo find .minio -mindepth 1 -maxdepth 1 -type d -exec rm -r {} \;
```