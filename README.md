# API Platform test

## Local setup
1. Set up the local environment with Docker Compose:
```
docker-compose up -d
```

2. Connect to the container and set up the project (install vendors, run migrations, load fixtures, generate the SSL keys for JWT authentication)
```
docker exec -it api-test-php bash
composer install
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
php bin/console lexik:jwt:generate-keypair
```

3. Tests are currently implemented to use the data provided in fixtures. Please run the tests before making changes in the database.
```
php bin/phpunit
```

4. See and interact with the APIs at: http://localhost:8077/api

### Authorization
The authorization part was implemented in a basic way, using JWT authentication (LexikJWTAuthenticationBundle).

For testing purposes, the users have been defined in memory: user1/pass1, user2/pass2.
To test the APIs:
1. Use the login API (POST /api/login_check) to create and retrieve a JWT token
2. Add the token using the `Authorize` button in the Swagger documentation.
3. Use APIs

### Future improvements
1. Improve data model (add more information, add users in database, link between users and orders, manage stock and different prices etc.)
2. Validation
3. Etc. (all the other features needed by a ecommerce :) ) 
