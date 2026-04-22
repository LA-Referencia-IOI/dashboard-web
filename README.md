# dARK Dashboard Web

### Components
- docker & docker-compose
- jeroennoten/laravel-adminlte
- doctrine/dbal
- laravelcollective/html
- nesbot/carbon
- akaunting/money
- bensampo/laravel-enum
- maatwebsite/excel
- node & npm
- php 8
- leaflet

---

## Setup / Installation

### 🚀 Automated Setup (Recommended)
You can easily set up the entire application using the automated installer script. This will start the containers, sync environment variables, install dependencies, and run database migrations.

1. Run the installer:
```sh
python3 install.py
```

2. Access the application in your browser:
```sh
http://127.0.0.1:8081/login
```

**Resetting the Environment:**
If you need to destroy the current setup (including all containers, volumes, and database data) and start fresh:
```sh
python3 install.py --reset
```

---

### 🛠 Manual Setup (Detailed)
If you prefer to set up the project manually or need to troubleshoot, follow these steps:

1. **Copy the Environment File:**
   ```sh
   cp .env.example .env
   ```
   *Note: Ensure your `.env` database variables match `docker-compose.yml` and that there are no trailing semicolons or commas in the `.env` file.*

2. **Start the Docker Containers:**
   ```sh
   sudo docker-compose up -d
   ```

3. **Install NPM Dependencies (for Leaflet maps):**
   ```sh
   npm install
   ```

4. **Enter the Application Container:**
   ```sh
   sudo docker exec -it dark-app bash
   ```

5. **Install PHP Dependencies:**
   *(Inside the container)*
   ```sh
   php composer.phar install
   ```

6. **Generate Application Key:**
   *(Inside the container)*
   ```sh
   php artisan key:generate
   ``` 

7. **Run Migrations and Seeders:**
   *(Inside the container)*
   ```sh
   php artisan migrate --step --seed
   ```

8. **Create Storage Link:**
   *(Inside the container)*
   ```sh
   php artisan storage:link
   ```

9. **Access the Application:**
   Open your browser and navigate to:
   ```sh
   http://127.0.0.1:8081/login
   ```