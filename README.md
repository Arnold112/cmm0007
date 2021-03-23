# cmm0007

## Frontend setup
- Clone repo
- cd arnold-fe
- npm install
- npm run serve

## Backend setup
- xampp must be on
- on phpmyadmin create a database
- cd arnold-be
- cp .env.example .env
- in the .env file specify database name to the created database 
- also specify your DB port, username and password 
- specify app evnironment production in your .env file
- composer update
- php artisan jwt:secret
- php artisan storage:link
- php artisan migrate
- php artisan serve
