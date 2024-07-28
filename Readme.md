# Readme

---

## Use scenario

### 1. First start

```shell
make create
```

### 2. Start work environment

- install javascript and php dependencies
- build javascript needed for api home page
- start **dev** environment
- run tests in **test** environment

```shell
make up
```

### 3. Run tests

Make sure that working environment is started.

```shell
make tests
```

### 4. Load data from file example and test with curl examples

Make sure that working environment is started.

```shell
make make mysql-recreate-db
make mysql-rebuild-and-load-from-files
```

read about testing from section **Example for Use dev environment**.


### 5. clear cache

```shell
make clear-cache
```

### 6. Enter work environments

```shell
# Enter php container in dev environment
make php-enter
```

```shell
# Enter mysql container in dev environment
make mysql-enter
```

```shell
# Enter mysql container in test environment
make mysql-test-enter
```

### 7. Stop working environment

```shell
make down
```

---

## Migrations

### Run

```shell
make migration-run
```

### Create new migration

```shell
make migration-new
```

## load from fixtures

Please notice **this command will purge all existing data in dev environment**.

```shell
make mysql-load-fixture
```

## execute custom command

Please notice **this command will purge all existing data in dev environment**.

```shell
make mysql-rebuild-and-load-from-files
```

### recreate db in **Dev** environment

Please notice **this command will purge all existing data in dev environment**. 
By running this command you are
- dropping existing db
- creating new db
- executing all migrations to new db

Please note **you will get database without any data**.

```shell
make mysql-recreate-db
```

---

## Example for Use dev environment

### Login

In order to get access token you should query endpoint **http://localhost:9090/auth**
please for email and password provide use data for existing user.

You can obtain this by (1) importing known data (2) importing fixtures and reading directly from
database user table.

Important note if you tool like Postman or Idea Http client make sure that you have disabled redirection.
This endpoint is by default returning status 302, which is useful if it is used by javascript code
hosted on same domain, because in that case token may not be needed.

```shell
curl 'http://localhost:9090/auth' \
--header 'Content-Type: application/json' \
--data-raw '{
    "email": "mac94@moen.com",
    "password": "secret"
}'
```
Please notice that if you fail to provide correct email and password you will get status **401**.

In response, you will get json with token that you should use for authentication of other endpoints.
Example of response

```json
{"token":"tcp_6507008c2971d6d0fb621adb9207aa2c5426b9adca467f0c6bd3aaf94cf55376"}
```

This is only endpoint that is not presented in documentation in api homepage
**http://localhost:9090/api**

---curl -X -v 'GET' \


### Fetching user data

For your convenience feel free to use [api homepage](**http://localhost:9090/api**).
Just please do not forget to set token by clicking on **Authorize** button and
to enter token in input field.

User may preview only his own data on.

**http://localhost:9090/api/users**

**usage of authorization token is mandatory!**

```shell
curl -X 'GET' \
  'http://localhost:9090/api/user?page=1' \
  -H 'accept: application/json' \
  -H 'Authorization: Bearer tcp_8d8d15717148f6b86027e0b1ea273dbcc04081488f4684650787d754e9a9e6ba'
```

Please notice that if you fail to provide correct token you will get status **401**.

---

### Fetching user purchases

```shell
curl -X 'GET' \
  'http://localhost:9090/api/user/products?page=1' \
  -H 'accept: application/json' \
  -H 'Authorization: Bearer tcp_f86e928c71e2ed071cb6c81b6b6cc65f474cf77b70159aea791aaf46b7e362c1'
```

---

### Add new purchase

```shell
curl -X 'POST' \
  'http://localhost:9090/api/user/products' \
  -H 'accept: application/json' \
  -H 'Authorization: Bearer tcp_f86e928c71e2ed071cb6c81b6b6cc65f474cf77b70159aea791aaf46b7e362c1' \
  -H 'Content-Type: application/json' \
  -d '{
  "sku": "kontakt-6"
}'
```

---

### Delete existing purchase

```shell
curl -X 'DELETE' \
  'http://localhost:9090/api/user/products/3'\
  -H 'accept: */*' \
  -H 'Authorization: Bearer tcp_8d0bc7d2f733adbe1dbeb94a08734c7dc8017c427859e66f9313321a6ccf343f'
```

---

### Import of data

With task there are 3 csv files provided to be used as mock data.

Developer has 2 choices.

#### 1. import data from provided files

```shell
make mysql-rebuild-and-load-from-files
```

#### 2. import data from fixtures

```shell
make mysql-load-fixture
```

---

## Potential improvements

- better input output mapping
  - maybe relay on SKU (to be discussed)
- addition of code quality monitoring - I may recommend usage fo library grumphp
- improve documentation in homepage
  - special focus is on response codes
- better naming
- addition of admin roles - user with this role could manage data of all users
- better validation (voters validation) on input and before persist (because we have state changing)
- introduction of CORS if used from another domain
- import command to be async and more optimized for big files
