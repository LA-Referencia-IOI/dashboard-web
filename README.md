
# dARK Dashboard Wed
<### Components
- docker
- jeroennoten/laravel-adminlte
- doctrine/dbal
- laravelcollective/html
- nesbot/carbon
- akaunting/money
- bensampo/laravel-enum
- maatwebsite/excel
- node
- php 8
- npm
- leaflet



### Installation
1. Up Container
```sh
 sudo docker-compose up -d
```
1.1 
```sh
 npm install leaflet
```
2. Enter the container
```sh
 sudo docker exec -it NAME-app bash
```

3. Install dependencies
```sh
 ./composer.phar install
```
Important: Need create .env and verificad .yml


4. Run Migrations and the project
```sh
 php artisan key:generate --show
``` 
```
 php artisan migrate --step --seed
```
```
 php artisan storage:link

```

5. In browser
```sh
http://127.0.0.1:8081/login
```