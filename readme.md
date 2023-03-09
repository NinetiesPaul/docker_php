## This is a PHP's Symfony base application running on Docker

After cloning this rep, cd into the project's folder and run the following commands:

- ```docker-compose build```
- ```docker-compose up -d```
- ```docker-compose exec php composer install```
- ```docker-compose exec php php bin/console doctrine:migrations:migrate```
- ```docker-compose exec php php bin/console lexik:jwt:generate-keypair```

### What To Do With It

This is just a simple API that you can:

- User registration
- User authentication using JWT
- CRUD operations of Tasks

My main goal is to showcase some of Symfony's features like:

- Migrations and model relationship
- Authentication configuration
- Routes definitions
- SOLID and KISS principles on a MVC architecture

### How To Use It

#### To create users
```
curl --location 'http://localhost:8015/login' \
--header 'Content-Type: application/json' \
--data-raw '{
    "username": "t.soprano@mail.com",
    "password": "123456"
}'
```
If it goes well it should return a 200 status response.

#### To Login and Authenticate
```
curl --location 'http://localhost:8015/login' \
--header 'Content-Type: application/json' \
--data-raw '{
    "username": "t.soprano@mail.com",
    "password": "123456"
}'
```
Copy the token retrieved by that endpoint under the "token" attribute. Add it to the 'Authorization: Bearer ' header on all Task related requests

#### To Create a Task
```
curl --location 'http://localhost:8015/api/task' \
--header 'Authorization: Bearer {place_token_here}' \
--header 'Content-Type: application/json' \
--data '{
    "title": "Insert Task Title Here",
    "description": "This is the task Description, it can be a really really really long text"
}'
```
That endpoint create a new Task. Those two attributes are required. It will the token's authenticated user as the Owner

#### To Retrieve all Tasks
```
curl --location 'http://localhost:8015/api/tasks' \
--header 'Authorization: Bearer {place_token_here}'
```
That endpoint retrieve all tasks that you create

#### To Update a Task
```
curl --location --request PUT 'http://localhost:8015/api/task/{taskId}' \
--header 'Authorization: Bearer {place_token_here}' \
--header 'Content-Type: application/json' \
--data '{
    "description": "this is the task new description"
}'
```
That endpoint updates information of an existing task. Replace {taskId} with the id of an existing Task

#### To Close a Task
```
curl --location --request PUT 'http://localhost:8015/api/task/{taskId}/close' \
--header 'Authorization: Bearer {place_token_here}'
```
That endpoint closes an existing task. Replace {taskId} with the id of an existing Task

#### To Delete a Task
```
curl --location --request DELETE 'http://localhost:8015/api/task/{taskId}' \
--header 'Authorization: Bearer {place_token_here}'
```
That endpoint deletes an existing task. Replace {taskId} with the id of an existing Task
