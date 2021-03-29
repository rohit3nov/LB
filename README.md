# Installation

Go to root of the cloned repo and do below steps:

    - Rename "env.example" to ".env" by running "mv .env.example .env".
    - Run "composer install" to install dependencies.
    - Run "sh setup.sh" to start containers.
    - Run "docker ps" to check if all the containers are running.
    
# Parse CSV

- Run <b>docker exec -t php-fpm php src/parseCsv.php</b> to parse worldcities.csv and update cities.csv with updates of asean cities.

# API

Hit below api to add/update the city to CSV.

- PUT http://localhost/cities/{city_id}

    {"city": "Jakarta","city_ascii": "Jakarta","lat": -6.2146,"lng": 106.8451,"country": "Indonesia","iso2": "ID","iso3": "IDN","admin_name": "Jakarta","capital": "primary","population": 34540000}

# Database

- Visit http://localhost:3307/ to check database.

