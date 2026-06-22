# Referenceregister

``` shell
docker compose pull
docker compose up --detach
docker compose exec phpfpm composer install
docker compose exec phpfpm bin/console doctrine:migrations:migrate --no-interaction
open "http://$(docker compose port nginx 8080)"
```
