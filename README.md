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
<?php
session_start();

include_once "./lib/db.php";
include_once "./lib/app.php";

DatabaseConnection::init([
    "host" => env("DATABASE_HOST"),
    "username" => env("DATABASE_USERNAME"),
    "password" => env("DATABASE_PASSWORD"),
    "database" => env("DATABASE_NAME"),
]);

App::run([
    "name" => "YourAppName",
]);
```

- `DatabaseConnection::init()` should be called with your MySQL credentials.
- `App::run()` initializes your app, with the `name` appearing in the `Powered-By` header.
- CORS handling is built-in.

## Getting Started

Routing in Bolt is based on a file system directory structure. Create routes within the `routes/` directory. Each file in the directory maps to a specific API route.

### Hello World Example

Create a route file like `routes/hello.php`:

```php
$handleHello = function () {
    return [
        "message" => "Hello World!",
    ];
};

API::get($handleHello);
```

- The `API::get()` method defines a GET request handler.
- Similarly, `API::post()` is available for POST requests.
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

Bolt supports middleware by default. Middleware files should be placed in the `middleware/` directory. The file `middleware/index.php` has the highest priority. Middleware can modify requests before they reach route handlers.

### Validation

Bolt includes a powerful and flexible validation system that ensures incoming data adheres to the expected structure. Validation in Bolt is applied directly to the route handler and works with both GET and POST requests.

When defining a route with validation, the second parameter in the `API::get()` or `API::post()` method is an array of validation rules. Each key in the array corresponds to a field that is expected in the request, and the value is an array that defines the data type and any other validation requirements.

#### Example

Here is a validation example for a POST request that handles a payment for coins:

```php
API::post($handlePayForCoins, [
    "amount" => [Types::integer, Required::true],
    "coinsAmount" => [Types::integer, Required::true],
    "name" => [Types::string, Required::true],
    "email" => [Types::email, Required::true],
    "gameName" => [Types::string, Required::true],
    "origin" => [Types::string, Required::true],
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
$handlePayForCoins = function (RouteRequestData $req) {
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
class Database
{
    static RecordOperations $users;
}

Database::$users = Collection::create("users", [
    "username" => CollectionTypes::string(length:50, nullable:true, default:''),
    "age" => CollectionTypes::number(),
    "created_at" => CollectionTypes::timestamp(),
]);
```

- **CRUD Operations**: Easily perform Create, Read, Update, Delete operations with predefined methods such as `create()`, `getOne()`, `update()`, and `delete()`.

### Migrations

_Coming Soon_

Database migrations will allow you to version your database schema changes programmatically.

## File Uploading

Bolt simplifies file uploading with two dedicated functions:

```php
function uploadFile($file): string
function uploadImage($file): string
```

- `uploadFile()` uploads any file type.
- `uploadImage()` validates the file as an image before uploading.

## Libraries

Bolt provides various built-in libraries to streamline common tasks.

### Emails

Send emails with ease using `EmailSender::send()`:

```php
EmailSender::send([
    'to' => 'recipient@example.com',
    'subject' => 'Hello',
    'message' => 'This is a test email',
]);
```

### HTTP

Perform HTTP requests using `HTTP::get()` and `HTTP::post()`:

```php
$response = HTTP::post("https://api.example.com", $data);
```

### Environment Variables

Store sensitive data in a `.env` file and access them using the `env()` function:

```php
$dbHost = env('DATABASE_HOST');
```

### Errors & Exceptions

Bolt offers built-in error handling with predefined exceptions for common HTTP errors:

```php
throw new ClientException("Client-side error");
throw new InternalServerErrorException("Server error");
```

### IP Handling

Retrieve the client's IP address easily with:

```php
$ip = getClientIP();
```

### Sitemap Generation

Generate sitemaps with `Sitemap::generate()`:

```php
Sitemap::init();
Sitemap::generate([
    ['loc' => "https://example.com", 'priority' => "0.8"],
]);
```

- Default values are automatically assigned if omitted.

---

Bolt is designed to make PHP API development fast and efficient. By following this guide, you'll be able to build scalable APIs with minimal effort while leveraging powerful built-in features. Dive in, and start building!
