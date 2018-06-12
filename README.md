# API IoT
### A lightweight API for your IoT projects

This projects is designed to save data from IoT sensors in a secure way. It uses two databases, one relational (MySQL or MariaDB) and another
No-Relational (MongoDB).

Relational database is used to store data from users, it has an admin and a non-admin user built-in on he DDL.

To use this project it is mandatory to install MySQL or MariaDB, MongoDB, mysqli extension for php and MongoDB Driver for php.

The system uses security token, they expire every 24 hours, if you want to renew then just perform login again with the same credentials of the user
your are using, the database will care about all!!!

## Technology used

- PHP 7.1.16

- MariaDB Server 5.5.57

- MongoDB Server 3.4.15

## Installation

- Launch the script for database creation in your MySQL:

``` mysql -h YOUR_MYSQL_HOST -u root -p < database/sensordb_DDL.sql ```

- Enable mysqli extension on your php.ini by removing semicolon for this line:

```  ;extension=php_mysqli.dll  ```

``` extension=php_mysqli.dll ```

If you do not have mysqli extension your can install with this command:

For php7:

``` sudo apt-get install php7.0-mysqli ```

For php5:

``` sudo apt-get install php5-mysqli ```

For mac is already installed of latest versions.

- Install MongoDB driver:

For GNU/Linux:

``` sudo pecl install mongodb ```

For mac:

``` brew install php70-mongodb ```

You can get more insformation about how to install MongoDb Driver on these page:

[http://php.net/manual/es/mongodb.installation.pecl.php](http://php.net/manual/es/mongodb.installation.pecl.php)

[http://php.net/manual/es/mongodb.installation.homebrew.php](http://php.net/manual/es/mongodb.installation.homebrew.php)

Then on your php.ini create a new entry at the end of the file and point the full path to the mongodb.so extension:

For linux just use:

``` extension=mongodb.so ```

For instance, this is my current extension path on mac:

``` extension="/usr/local/php5-7.1.13-20180201-134129/lib/php/extensions/no-debug-non-zts-20160303/mongodb.so" ```

## Docker installation

This project includes a docker-compose file that you can use to easily set up the project. If you have installed docker and docker-compose you don't need anything else to start with. You have to run the command:

```docker-compose up```

There are some caveats to be aware:

- The docker configuration is just a testing one, you should use the dockerfiles on the docker folder as an starting point.
- The relational database is created in a persistent docker volume and only is populated when the image is created, if you want to reinstall the system you will have to delete the volume.
- The configuration file ```utils/config.php``` is populated with the docker values, if you change the dockerfiles or the docker-compose.yml included you will have to change also this file.
- The project files are copied to the web image, if you change the source code you will have to rebuild the image ```docker-compose build```, you can also make a path mapping between the source code and the workdir of the image.


## Database and web services credentials

| Username      | Password      |
| ------------- |:-------------:|
| admin         | admin1234     |
| api_user      | api_user1234  |


All password are hashed and salted.

You can also try the API with a Postman sample available on database folder.

## .htaccess file

This project has an htaccess file to hide the file extensions, rename the file to:

``` .htaccess ```

## Examples

Lets say that you have the project on your Apache DocumentRoot listening on port 8080, then all services will point on:

``` /api/login ```

``` /api/users ```

``` /api/values ```

You can use the Postman file in "database/Sensors (Github).postman_collection.json", if you do not have Postman get it here:

[https://www.getpostman.com/](https://www.getpostman.com/)

Or if you do not want to use this application you can also use the examples below in cURL.

These are some examples for each request with cURL program:

- Create new user

```
curl -X POST \
  -L \
  http://localhost:8080/api/users \
  -H 'Cache-Control: no-cache' \
  -H 'Content-Type: application/json' \
  -H 'Token: aca6038665c811e8a96100089be8caec' \
  -d '{
	"username" : "user_3",
	"password" : "passwordUser3",
	"name" : "User3",
	"surname" : "User3 description",
	"description" : "API user",
	"admin" : "0"
}'
```

- Update user:

```
curl -X PUT \
  -L \
  http://localhost:8080/api/users \
  -H 'Cache-Control: no-cache' \
  -H 'Content-type: application/json' \
  -H 'Token: aca6038665c811e8a96100089be8caec' \
  -d '{
	"username" : "user_3",
	"password" : "passwordUser3",
	"name" : "User3",
	"surname" : "User3 description modified",
	"description" : "API user",
	"admin" : "0",
	"user_id" : "3"
}'
```

- Delete user

```
curl -X DELETE \
  -L \
  http://localhost:8080/api/users \
  -H 'Cache-Control: no-cache' \
  -H 'Content-type: application/json' \
  -H 'Token: aca6038665c811e8a96100089be8caec' \
  -d '{"user_id" : "3"}'
```

- Get user information

```
curl -X GET \
  -L \
  'http://localhost:8080/api/users?user_id=1' \
  -H 'Cache-Control: no-cache' \
  -H 'Content-type: application/json' \
  -H 'Token: aca6038665c811e8a96100089be8caec' \
```

- Perform user login

```
curl -X POST \
  -L \
  http://localhost:8080/api/login \
  -H 'Cache-Control: no-cache' \
  -H 'Content-type: application/json' \
  -d '{
	"username" : "api_user",
	"password" : "api_user1234"
}'
```

- Insert new document in MongoDB database

```
curl -X POST \
  -L \
  http://localhost:8080/api/values \
  -H 'Cache-Control: no-cache' \
  -H 'Content-type: application/json' \
  -H 'Token: aca6038665c811e8a96100089be8caec' \
  -d '{
	"key1" : "value1",
	"key2" : "value2",
	"key3" : "value3",
	"key4" : "value4",
	"key5" : "value5",
	"key6" : "value6",
	"key7" : "value7"
}'
```

- Query documents from MongoDB with limit 1

```
curl -X PUT \
  -L \
  http://localhost:8080/api/values \
  -H 'Cache-Control: no-cache' \
  -H 'Content-type: application/json' \
  -H 'Token: aca6038665c811e8a96100089be8caec' \
  -d '{
	"docs" : "1"
}'
```

- Query documents from MongoDB with limit 1 between two dates given

```
curl -X PUT \
  -L \
  http://localhost:8080/api/values \
  -H 'Cache-Control: no-cache' \
  -H 'Content-type: application/json' \
  -H 'Token: aca6038665c811e8a96100089be8caec' \
  -d '{
	"docs" : "1",
	"date_from" : "2018-05-01 00:00:00",
	"date_to" : "2018-05-30 00:00:00"
}'
```

## Configuration

Set your connection parameters for MySQL/MariaDB and Mongo on **utils/config.php**

ENJOY!
