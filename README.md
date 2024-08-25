# Bolt Documentation

Welcome to **Bolt**, a fast and powerful PHP API framework designed to streamline API development. Bolt offers a clean architecture, essential libraries, and tools for building scalable and efficient APIs. Whether you're setting up routes, validating requests, managing databases, or handling file uploads, Bolt provides the foundation you need.

## Table of Contents

- [Bolt Documentation](#bolt-documentation)
  - [Table of Contents](#table-of-contents)
  - [Installation](#installation)
    - [Starting Your Application](#starting-your-application)
  - [Getting Started](#getting-started)
    - [Hello World Example](#hello-world-example)
  - [Routing](#routing)
    - [Navigation](#navigation)
    - [Redirect](#redirect)
    - [Middleware](#middleware)
    - [Validation](#validation)
      - [Example](#example)
      - [Example Breakdown](#example-breakdown)
      - [Handling Validation Results](#handling-validation-results)
  - [Database](#database)
    - [Models](#models)
      - [Example](#example-1)
  - [Functions](#functions)
    - [Example](#example-2)
    - [Realtime Database](#realtime-database)
    - [Migrations](#migrations)
  - [File Uploading](#file-uploading)
  - [Libraries](#libraries)
    - [Emails](#emails)
    - [HTTP](#http)
    - [Environment Variables](#environment-variables)
    - [Errors \& Exceptions](#errors--exceptions)
    - [IP Handling](#ip-handling)
    - [Sitemap Generation](#sitemap-generation)

## Installation

To get started with **Bolt**, clone the repository and follow the instructions below:

```bash
git clone https://github.com/aounalazzam/bolt.git
cd your-project
```

Ensure your project structure includes the essential files and directories:

- `index.php`
- `routes/`
- `lib/`
- `utils/`
- `database/`
- `middleware/` if needed

### Starting Your Application

Modify the `index.php` file in the root of your project directory to include your configuration and initialize the application:

```php
require "./vendor/autoload.php";

use Bolt\Utils\{Env};
use Bolt\Lib\{Bootstrap};
use Bolt\Lib\Database\{DatabaseConnection};

ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

DatabaseConnection::init([
    "host" => Env::get("DATABASE_HOST"),
    "username" => Env::get("DATABASE_USERNAME"),
    "password" => Env::get("DATABASE_PASSWORD"),
    "database" => Env::get("DATABASE_NAME"),
]);

Bootstrap::run([
    "name" => "YourAppName",
]);
```

- `DatabaseConnection::init()` should be called with your MySQL credentials.
- `Bootstrap::run()` initializes your app, with the `name` appearing in the `Powered-By` header.
- CORS handling is built-in.

## Getting Started

Routing in Bolt is based on a file system directory structure. Create routes within the `routes/` directory. Each file in the directory maps to a specific API route.

### Hello World Example

Create a route file like `routes/hello.php`:

```php
use Bolt\Lib\Routing\{Route};

$handleHello = function () {
    return [
        "message" => "Hello World!",
    ];
};

Route::get($handleHello);
```

- The `Route::get()` method defines a GET request handler.
- Similarly, `Route::post()` is available for POST requests.
- Both methods accept a validation schema array as an optional second parameter.

## Routing

Bolt’s routing is simple and powerful, leveraging the file system to map requests to handlers.

### Navigation

Route files are placed in the `routes/` directory. For example, a file `routes/hello/[name].php` corresponds to `/hello/{name}`. The `Router::getParams()` function allows you to retrieve these dynamic parameters.

```php
$name = Router::getParams()['name'];
```

### Redirect

To perform redirects, use the `Redirect::to()` method in your route function:

```php
return Redirect::to("/new-url");
```

### Middleware

Bolt supports middleware by default. Middleware files should be placed in the `middleware/` directory and import it at any route file and use `use` function from `Route` and add your function follow this example to know more

```php

use Bolt\Lib\Routing\{RouteRequest};
use Bolt\Utils\ServerErrorException;

$authVerifyOwner = function (RouteRequest $req) {
    $userId = $_SESSION['owner-user-id'] ?? null;

    if (!$userId) {
        throw ServerErrorException::Unauthorized("Can not access this resource");
    }
};

```

```php
...

Route::use($authVerifyOwner)::post($handleCreateEmployee, [
    "name" => [Types::string(), Required::true(), Length::max(50)],
    "role" => [Types::string(), Required::true(), Length::max(50)],
    "employmentDate" => [Types::string(), Required::true(), Length::max(50)],
    ...
]);

...
```

Code Explain

- `Route::use` this function register the middleware then not run it, is validate first the schema then run middleware callbacks then run the handler function the timeline of route execution it like this:
  - Validate Schema Of Route If Available
  - Run Middlewares Callbacks
  - Run Route Handler Function
    w

### Validation

Bolt includes a powerful and flexible validation system that ensures incoming data adheres to the expected structure. Validation in Bolt is applied directly to the route handler and works with both GET and POST requests.

When defining a route with validation, the second parameter in the `Route::get()` or `Route::post()` method is an array of validation rules. Each key in the array corresponds to a field that is expected in the request, and the value is an array that defines the data type and any other validation requirements.

#### Example

Here is a validation example for a POST request that handles a payment for coins:

```php
Route::post($handlePayForCoins, [
    "amount" => [Types::integer(), Required::true()],
    "coinsAmount" => [Types::integer(), Required::true()],
    "name" => [Types::string(), Required::true()],
    "email" => [Types::email(), Required::true()],
    "gameName" => [Types::string(), Required::true(), Length::max(50)],
    "origin" => [Types::string(), Required::true()],
]);
```

- **Types**: The `Types` class is used to specify the data type of each field. Available types include `Types::integer`, `Types::string`, `Types::email`, `Types::boolean`, and more.
- **Required**: The `Required::true` rule marks the field as mandatory. If the field is missing, the validation will fail.

#### Example Breakdown

- `"amount"`: This field expects an integer value and is required.
- `"coinsAmount"`: This field also expects an integer value and is required.
- `"name"`: This field expects a string value (e.g., a player's name) and is required.
- `"email"`: This field expects a valid email address and is required.
- `"gameName"`: This field expects a string value representing the game's name and is required.
- `"origin"`: This field expects a string value indicating the origin or source and is required.

#### Handling Validation Results

If the incoming request fails validation, Bolt will automatically return an error response, ensuring that invalid data is not processed. You do not need to manually check the data, as Bolt’s validation system handles this for you.

To access the validated data within your route handler, you can use the `RouteRequestData` object provided in the handler function:

```php
$handlePayForCoins = function (RouteRequest $req) {
    $amount = $req->body['amount'];
    $coinsAmount = $req->body['coinsAmount'];
    $name = $req->body['name'];
    $email = $req->body['email'];
    $gameName = $req->body['gameName'];
    $origin = $req->body['origin'];

    return [
        "message" => "Payment for coins successful!",
        "data" => $req->body
    ];
};
```

- `RouteRequestData $req`: This object contains the validated request data in its `body` property.
- You can then access the fields confidently, knowing that they have been validated according to the rules you defined.

By using Bolt's validation system, you can easily ensure that all incoming data is correct and well-formed before processing it within your API routes.

## Database

### Models

Bolt offers powerful database interaction through models. Define your models in `database/index.php` like this:

#### Example

```php
use Bolt\Lib\Database\{RecordOperations, Collection, CollectionTypes};

class Database
{
    static RecordOperations $users;
}

Database::$users = Collection::create("users", [
    "username" => CollectionTypes::string(length:50, nullable:true, default:''),
    "age" => CollectionTypes::number(),
]);
```

## Functions

With You Access A RecordOperations class there is some function help you to do crud operations with php instead of writing sql code

```php
function innerJoin(string $joinTable,string $onCondition,string $fields = "*",string $filter = null,int $limit = null,int $offset = 0): array
function leftJoin(string $joinTable,string $onCondition,string $fields = "*",string $filter = null,int $limit = null,int $offset = 0): array
function fullJoin(string $joinTable,string $onCondition,string $fields = "*",string $filter = null,int $limit = null,int $offset = 0): array
function create(array $data): array
function getOne(string $filter, string $fields = "*"): array|null
function getById(string $id, string $fields = "*"): array|null
function getList(int $limit, int $offset = 0, string $fields = "*"): array
function getFilteredList(string $filter, string $fields = "*"): array
function getAll(string $fields = "*"): array
function update(string $id, array $data): true
function updateByFilter(string $filter, array $data): true
function delete(string $id): true
function deleteList(string $filter): true
```

#### Example

- Basic Code Example With CRUD Operations

```php
// Create Row
$user = Database::$users->create(["username" => "jack","age"=> 30]);
// $user = [
//     "id" => "1",
//     "username" => "jack",
//     "age" => 30,
//     "created" => 17589546,
//     "updated" => 17589546,
// ]
// Update Row
Database::$users->update($user['id'], ["username" => "michel"]);
Database::$users->updateByFilter("username = 'jack'", ["username" => "michel"]);
// Get User
Database::$users->getById($user['id']);
Database::$users->getOne("username = 'michel'");
// Get All Users
Database::$users->getAll();
// Delete User
Database::$users->delete($user['id']);
...more
```

### Realtime Database

To Get Update Notifications When Data of Table/Record Changed This is Using SSE (Server-Sent-Events)

For Table

```php
include_once "./database/index.php";

use Bolt\Lib\Database\{RealtimeDatabase};

RealtimeDatabase::init();

RealtimeDatabase::listenTable(Database::$apps);
```

For Record

```php
include_once "./database/index.php";

use Bolt\Lib\Database\{RealtimeDatabase};

[$id] = Router::getParams();

RealtimeDatabase::init();

RealtimeDatabase::listenRecord($id, Database::$apps);
```

Code Explain

- `RealtimeDatabase::init` Telling PHP This File Will Be SSE Content-Type With SSE Configuration To Start
- `RealtimeDatabase::listenRecord` Listen Changes To Specific Record If Data Changes In Row Will Throw `{"action":"update"}`
- `RealtimeDatabase::listen` Listen Changes To All Table If Data Changes In Any Record In Table Will Throw `{"action":"update"}`

### Migrations

_Coming Soon_

Database migrations will allow you to version your database schema changes programmatically.

## File Uploading

Bolt simplifies file uploading with two dedicated functions:

```php
use Bolt\Utils\{FileUpload};

FileUpload::upload(array $file, array $allowedExtensions = [], int $maxFileSize = 10485760): string
```

- `uploadFile()` uploads any file type with extension and size validation and the default upload dir is `./uploads`

if you want to make your own max file size limit for all files use this function at `index.php` file.

```php
...
FileUpload::setMaxFileSize(int $maxFileSize) // $maxFileSize in bytes
...
```

if you want to change the default upload dir use this function at `index.php` file.

```php
...
FileUpload::setUploadDir(string $uploadDirPath)
...
```

## Libraries

Bolt provides various built-in libraries to streamline common tasks.

### Emails

Send emails with ease using `EmailSender::send()`:

```php
use Bolt\Lib\{EmailSender};

EmailSender::sendEmail([
    'to' => 'recipient@example.com',
    'subject' => 'Hello',
    'message' => 'This is a test email',
]);
```

### HTTP

Perform HTTP requests using `HTTP::get()` and `HTTP::post()`:

```php
use Bolt\Lib\{HTTP};

$response = HTTP::post("https://api.example.com", $data, $headers);
$response = HTTP::get("https://api.example.com", $headers);
```

### Environment Variables

Store sensitive data in a `.env` file and access them using the `env()` function:

```php
use Bolt\Utils\{Env};

$dbHost = Env::get('DATABASE_HOST');
```

### Errors & Exceptions

Bolt offers built-in error handling with predefined exceptions for common HTTP errors:

```php
use Bolt\Utils\{ServerErrorException};

throw ServerErrorException::BadRequest("Client-side error");
throw ServerErrorException::Conflict("Conflict error");
throw ServerErrorException::Forbidden("Forbidden error");
throw ServerErrorException::InternalServerError("Server error");
throw ServerErrorException::NotFound("404 error");
throw ServerErrorException::Unauthorized("Unauthorized error");
```

Note: there are all 4xx and 5xx errors.

### IP Handling

Retrieve the client's IP address easily with:

```php
use Bolt\Utils\{ClientIP};

$ip = ClientIP::get();
```

### Sitemap Generation

Generate sitemaps with `Sitemap::generate()`:

```php
use Bolt\Lib\{Sitemap};

Sitemap::init();
Sitemap::generate([
    ['loc' => "https://example.com", 'priority' => "0.8"],
]);
```

- Default values are automatically assigned if omitted.

---

Bolt is designed to make PHP API development fast and efficient. By following this guide, you'll be able to build scalable APIs with minimal effort while leveraging powerful built-in features. Dive in, and start building!
