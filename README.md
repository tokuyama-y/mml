# Project Background
This project is a dedicated tool created for the machine ["RYOAN-JI XY 2025"](https://fabacademy.org/2025/labs/kannai/Machine_Building/) developed by the FabLab Kannai team as part of the group assignment for the Mechanical Design module of The Fab Academy 2025.
We have deployed this project on AWS and made it publicly available at https://machinical-memory-landscapes.cloud/. <br>

## Local Environment Setup

Follow the steps below to set up your local environment:

1. **Clone the repository:**

   ```bash
   git clone https://github.com/tokuyama-y/mml.git
   # or
   git clone git@github.com:tokuyama-y/mml.git
   ```

2. **Place configuration files:**

   Obtain `.env` and `google-service-account.json` from the "kannai" team, and place them in the following directories:
   - `.env` → root directory of the project
   - `google-service-account.json` → `src/storage/app/google-service-account.json`

3. **Download Docker Desktop:**

   Download and install Docker Desktop from the [official website](https://www.docker.com/).

4. **Inspect the Docker network:**

   ```bash
   docker network create mml-network
   ```

5. **Build and start Docker containers:**

   ```bash
   docker-compose -f docker-compose.local.yml up -d --build
   ```

6. **Run Laravel commands in the Docker container:**

   ```bash
   docker-compose -f docker-compose.local.yml exec php sh
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   php artisan migrate
   exit
   ```

##  Access local environment

   - **Top Page:** [http://localhost:8080](http://localhost:8080)
   - **MinIO:** [http://localhost:9001](http://localhost:9001)