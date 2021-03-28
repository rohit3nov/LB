# docker setup

1. Go to root of the cloned repo. Run <b>"sh setup.sh"</b> to start containers.
2. Run <b>"docker ps"</b> to check if all the containers are running.
# Parse CSV
1. Run <b>"docker exec -t php-fpm php src/parseCsv.php"</b> to parse worldcities.csv and update cities.csv with updates of asean cities

# API

1. Hit below api to add/update the city to CSV.

    c) PUT http://localhost/cities/{city_id}

        {"city": "Jakarta","city_ascii": "Jakarta","lat": -6.2146,"lng": 106.8451,"country": "Indonesia","iso2": "ID","iso3": "IDN","admin_name": "Jakarta","capital": "primary","population": 34540000}