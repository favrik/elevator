# Elevator implementation in PHP
This was a technical challenge for an interview.  It's a Laravel application that is using Redis for its queues and Redis pub/sub to push elevator updates.  `supervisord` is used to run the queue workers.  Websockets are used to update the front-end of the application using `laravel-echo-server`.
It certainly is a bit over-engineered in some places, but I just wanted to touch a few areas of interests.  Mainly:

* Websockets
* Redis Pub/Sub
* Proper usage of Bootstrap
* No jQuery
* Mobile first

<p align="center"><img src="http://i.imgur.com/cSOBdPc.png" /></p>

## Requirements / Stack
* PHP 7.1
* Laravel 5.4
* Redis
* Node 6.x
* composer
* supervisor
* laravel-echo-server

## Install
* Within project directory root:
`composer install`
`npm install`
* Edit your .env file to reflect your configuration.
`php artisan migrate`
`php artisan db:seed`
`npm install -g laravel-echo-server`
* Configure and run supervisord (samples in `supervisor` directory)
* Run supervisor jobs
* Enjoy!!  

## TODO
* While the bulk request API endpoint, and the SCANSchedule algo are ready, I haven't made the change to the front-end code.  This is trivial, and will do it on the next major update.
* Since there are a lot of dependencies, a vagrang box or similar could be useful, although overkill. 
