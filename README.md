# laravel-structure-craft
- Package for Organizing Code with Design Patterns and SOLID Principles
-This package is designed to help structure your code using the Repository and DTO (Data Transfer Object) patterns. It also implements SOLID principles to maintain clean, maintainable, and scalable business logic, especially for handling search and filter operations.

## Table of Contents
- [Installation](#installation)
- [Usage](#usage)
- [Contact](#contact)

## Getting started

### Installation
To install the package, run the following command:
```bash
composer require rateb/structure
```

## Registering the Service Provider

To use the features provided by the `rateb-structure` package, you need to register the `StructureServiceProvider`. Follow these steps:

1. Open the `config/app.php` file in your Laravel project.
2. Locate the `providers` array in the file.
3. Add the following line to the array:

   ```php
   \RatebSa\Structure\StructureServiceProvider::class,
   ```


## Example of the Providers Array

After adding the `StructureServiceProvider`, your `providers` array in the `config/app.php` file should look something like this:

```php
'providers' => [
    /*
     * Laravel Framework Service Providers...
     */
    Illuminate\Auth\AuthServiceProvider::class,
    Illuminate\Broadcasting\BroadcastServiceProvider::class,
    Illuminate\Routing\RouteServiceProvider::class,
    // ... other Laravel service providers

    /*
     * Application Service Providers...
     */
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
    
    // Add the StructureServiceProvider
    \RatebSa\Structure\StructureServiceProvider::class,
],
```

## Key Concepts

### 1. Repository Pattern:
The Repository layer provides an abstraction for handling data persistence and retrieval. Each Model in the system will have its own Repository to encapsulate the business logic related to that model. This structure promotes separation of concerns, where the controller handles user requests, the Repository manages the data access, and DTO handles data transfer.

### 2. Data Transfer Object (DTO):
The DTO is responsible for transporting data between different layers of the application, such as from the Request to the Repository. This ensures that the data is clean, validated, and transformed as needed before it reaches the business logic layer.

### 3. Filters and Search:
To facilitate flexible data retrieval, the package supports filtering and searching via pre-defined filter classes and searchable fields. This enables users to query and filter models without bloating the controller with query logic.

### 4. SOLID Principles:
The package adheres to SOLID principles, particularly:
- **Single Responsibility Principle (SRP)**: Each class has a well-defined role. Repositories handle data logic, controllers handle requests, and DTOs manage data transfer.
- **Open-Closed Principle (OCP)**: The package is designed to be easily extendable without modifying existing code, enabling new filters and search criteria to be added seamlessly.

## Usage

# Project Structure


## Routing Configuration

In your `routes/web.php` (or `routes/api.php` depending on your application structure), add the following routes to handle user creation and retrieval:

## Explanation of Routes

- **`create`**: This route is set up to handle POST requests for creating a new user. It calls the `create` method in the `UserController`. This method is responsible for processing the incoming user data and storing it in the database.

- **`index`**: This route handles POST requests to retrieve a filtered and searchable list of users. It calls the `index` method in the `UserController`. This method facilitates the retrieval of user data based on specified filters, ensuring efficient data management and response.





### UserController.php
The `UserController` handles requests from the user and interacts with the `UserRepo` repository for retrieving or storing user data.


```php
use App\Http\Controllers\UserController;

Route::post('create', [UserController::class, 'create']);
Route::post('index', [UserController::class, 'index']);
```
### UserController.php
```php
<?php

namespace App\Http\Controllers;

use App\Http\Repositories\UserRepo;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Constructor to inject the UserRepo dependency
    public function __construct(protected UserRepo $userRepository)
    {
    }

    // Method to retrieve a filtered and searchable list of users
    public function index(Request $request)
    {
        return $this->userRepository->index()->get();
    }

    // Method to handle the creation of a new user
    public function create(Request $request)
    {
        return $this->userRepository->store($request);
    }
}



```


### UserRepo.php
The `UserRepo` is responsible for handling all database interactions related to the User model. It extends the `BaseRepo` to benefit from common repository functionalities, such as filtering and searching.


## Creating Repository 

To create a Repository and a Data Transfer Object (DTO), you can use the following Artisan command:

```bash
php artisan make:repo-dto User --action=repo
```


in path **`App\Http\Repositories`**

```php
namespace App\Http\Repositories;

use App\Filters\User\StatusUserFilter;
use App\Http\DTOs\UserData;
use App\Models\User;
use RatebSa\Structure\Repositories\BaseRepo;

class UserRepo extends BaseRepo
{
    protected $filtersKeys = [
        'status' => StatusUserFilter::class,
    ];

    protected $searchFileds = ['email'];

    protected $relations = [];

    protected $realationFileds = [
        'profile' => ['first_name']
    ];

    public function __construct(User $model)
    {
        parent::__construct($model, UserData::class);
    }
}

```





### Filterable Fields
- **Filterable fields (`filtersKeys`)**: Filters such as `StatusFilter` allow users to filter users by their status , Arrays to determine the filter that was created and will come later how to create it

```php
      // class UserRepo
      // Array with filterable fields
      protected $filtersKeys = [
          'status'=>StatusUserFilter::class,
      ];

```
- And it is passed in the body in api , the methos must be `post`
  
```json
{
    "filters":{
        "status":{
            "status":true
        }
    }
}

```
### Searchable Fields
- **Searchable fields (`searchFileds`)**: Allows users to search for users by their email addresses , And it is passed in the parameter `search-key`.

```php
      // class UserRepo
      // Array containing fields that can be searched within
      protected $searchFileds = ['field'];

```

### Relations and Relation Fields
- **Relations and relation fields**: Define relationships and searchable fields within relationships, like 

```php
      // class UserRepo
       // An Array containing fields that exist within searchable relationships
     protected $realationFileds = [
     'relation1'=>[
           'field1' ,
           'field2'  
 
        ],
        'relation2'=>[
           'field3' ,
           'field4' 
        ],
];

```



### UserData.php (DTO)
The `UserData` class is responsible for collecting and validating data from requests before passing it to the repository.



# Creating DTO

To create a Repository and a Data Transfer Object (DTO), you can use the following Artisan command:

```bash
php artisan make:repo-dto User --action=dto
```

in path  **`App\Http\DTOs`**

```php
namespace App\Http\DTOs;

use Illuminate\Http\Request;
use RatebSa\Structure\DTOs\BaseDTO;

class UserData extends BaseDTO
{
    public static function fromRequest(?Request $request, ...$params): static
    {
        $instance = parent::fromRequest($request, ...$params);

        $instance->name = $request->input('name');
        $instance->email = $request->input('email');
        $instance->password = $request->input('password');
        $instance->status = $request->input('status');

        return $instance;
    }
}

```

### `fromRequest()`
- **`fromRequest()`**: Gathers data from the incoming request (e.g., name, email, password, status) and creates an instance of `UserData`.

## Extending the DTO
To extend the DTO to handle new data fields:
1. **Modify the `UserData::fromRequest()` method** to include the new fields.
2. **Ensure these fields are available** in the incoming request.



# To create them together ( DTO , Repository )

```bash
php artisan make:repo-dto User --action=all
//or
php artisan make:repo-dto User 
```

## Important Note

The name of the repository and DTO should match the name of the model. In this case, **User** refers to the **User** model. This consistency helps maintain organization within your codebase, making it easier to manage and understand the relationships between models, repositories, and DTOs.

### StatusUserFilter.php (DTO)

## Creating a Filter

To create a filter, you can use the following Artisan command:

```bash
php artisan make:filter User/StatusUserFilter
```


### StatusUserFilter.php
The `StatusFilter` class filters users by their status. This is an example of how filters are applied in the repository to ensure clean, reusable, and flexible querying.

```php
namespace App\Filters\User;

use RatebSa\Structure\Filters\Filter;

class StatusUserFilter extends Filter
{
    public static function rules(): array
    {
        return [
            'status' => []
        ];
    }

    public function apply(&$query)
    {
        return $query->where('status', $this->status);
    }
}

```
### `apply()`
- **`apply()`**: Modifies the query to filter results by status.






- **Create a User**: 
  Send a POST request to `/create` with user data (e.g., name, email, password, status).

- **List Users**: 
  Send a POST request to `/index` to retrieve a filtered and searchable list of users.



This command will generate a new filter class named StatusUserFilter. Filters are used to define the criteria for retrieving or manipulating data, allowing you to encapsulate and organize your query logic effectively.

Make sure to implement the necessary methods within the generated filter class to suit your application's requirements



To add a new filter to any repository:
1. **Create a new filter class** (e.g., `AgeFilter`).
2. **Define the filter logic** in the `apply()` method.
3. **Add the filter** to the `filtersKeys` array in the repository.





## Contact

Thank You Message

Dear Users,

I would like to extend my heartfelt thanks to you for using our package. We truly appreciate your time and effort in reading the documentation and engaging with our content.

If you have any questions or feedback, please feel free to reach out to us via email:

Email: [rateb.alsaour98@gmail.com]

We are here to assist you and look forward to hearing your thoughts!


