# Minio demo using docker & PHP

## Install PHP dependencies

Run composer locally (if installed) or running a docker container:

```bash
docker pull composer/composer
docker run --rm -v $(pwd)/app:/app --user $(id -u):$(id -g) composer/composer install
```

## Start containers

```bash
docker-compose up -d # remove "-d" to do not run containers in background
```

## Stop containers

```bash
docker-compose down --volumes
```

## Clean up

```bash
docker-compose down --volumes
docker system prune -f
docker system df -v

sudo rm -rf .minio; mkdir .minio
```