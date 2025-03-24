
## a. Project Setup Instructions

# Prerequisites:

- Docker and Docker Compose installed.

- Composer installed.

# Steps to Set Up:

 ### 1.Clone the repository: 

    - git clone https://github.com/Mahm0udrabie/order-process.git

    - cd order-process

 ### 2.Install dependencies:

    - composer install

### 3.Configure environment variables:

    - Create a copy of .env.example as .env and update database credentials.

    - cp .env.example .env 

    - database configuration

        DB_CONNECTION=mysql

        DB_HOST=mysql

        DB_PORT=3306

        DB_DATABASE=order_process

        DB_USERNAME=sail

        DB_PASSWORD=password

### 4.Start the application: 

    - ./vendor/bin/sail up -d

    - ./vendor/bin/sail artisan key:generate

### 4.Run migrations:

    - ./vendor/bin/sail artisan migrate

### 5.Start the queue worker:

    - ./vendor/bin/sail artisan queue:work

### 6. Create User and Order Random

    - Please visit [Order Processing](http://localhost/order-process).

    - check your queue in the terminal to track order payment process job 

    - check laravel.logs for debugging process and expected exceptions

### 7. Run Test Case For order processing 

    - ./vendor/bin/sail artisan test

# b. Issues Encountered and Solutions

## Issue 1: Queue Worker Not Processing Jobs:
    - Solution: Ensure the QUEUE_CONNECTION is correctly set in .env and that the queue table exists.


# c. Possible Optimizations

## 1:Use Redis for Queueing:
    - Switch to Redis for better performance and advanced queue features.
## Implement Job Batching:
    - Use Laravel’s job batching to handle multiple orders simultaneously.
## Advanced Logging and Monitoring:
    - Integrate tools like Laravel Horizon or external monitoring services for better visibility into job processing.
    - Asynchronous Processing with Queued Event Listeners:
        - Implement queued event listeners for more granular control over processing.
