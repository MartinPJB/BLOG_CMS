# CUEJ_CMS 📰

> Authors: [Martin B.](https://github.com/MartinPJB) & [Mael G.](https://github.com/Surreal-Maggie).
> Project made for a school project at the [IUT of Haguenau](https://iuthaguenau.unistra.fr/), France.

This is a simple Blog CMS made for the CUEJ (Centre Universitaire d'Enseignement du Journalisme) in Strasbourg, France.

This project is a school project, and is not meant to be used in a big production environment. It uses PHP, MySQL, and the code is Object-Oriented.

<hr>

## URL Structure 📝

The URL structure is composed of the following elements: `/:page/:action/:opt_param`

- `page`: The page to be displayed. This is the only required parameter. It will allow the router to determine which controller to use.
- `action`: The action to be performed. This is optional. If not provided, the default action (`index`) will be used. The action will allow the router to determine which method to call in the controller.
- `opt_param`: An optional parameter. This is optional. If not provided, the default value (`null`) will be used. The optional parameter will be passed to the controller method as an argument. You can basically set any value you want here, such as the id of something you would like to retrieve from the database, or the name of a file you would like to display.

*You can also decide to pass URL parameters like `?page=home&action=index&opt_param=null`. This will work as well, but it is not recommended.*
*If needed, you can also add more parameters to the URL, depending on your Controller's needs.*

> Note: The `opt_param` is not required, but if you want to use it, you must provide a value for `action`.


<hr>

## Edit the configuration ⚙️

The configuration file is located in the `config/` folder. It is a simple PHP file that contains an array of configuration values.

You can edit the config `default.php` file, but I'd recommend you to duplicate it, and rename it to `local.php`. This will allow you to keep the default config file, and to have your own config file that will not be overwritten when you update the project. (You can also add the `config/local.php` file to your `.gitignore` file, so that it won't be pushed to your repository, but you can also name it the way you want it to be named).

> Note: If you change the name of the config file, you must also change the name of the file in the `index.php` file during its inclusion.


<hr>

## Controllers 🎮

Controllers are the main part of the application. They are the ones that will handle the requests, and send back the appropriate response.

### Creating a Controller

To create a new Controller, you must create a new file in the `Controller/` folder. The name of the file must be the name of the Controller, with the first letter in uppercase, and the suffix `Controller`. For example, if you want to create a `home` Controller, you must create a file named `HomeController.php`.

The Controller must extend the `ControllerBase` class, and must implement the `ControllerInterface` interface, located in the core. This will ensure that the Controller has the required methods to work properly.

```php

// HomeController.php

// Do not forget to set the namespace in your Controller file.
namespace Controller;

// Import the required classes.
use Core\ControllerBase;
use Core\ControllerInterface;
use \Core\RequestContext;

// Create the Controller class.
class HomeController extends ControllerBase implements ControllerInterface {
  public string $name = 'Home';
  public string $description = 'Handles all requests related to the homepage.';

  /**
   * {@inheritDoc}
   */
  public function __construct(RequestContext $requestContext)
  {
    parent::__construct($requestContext);
  }
}
```

The `name` and `description` properties are used to display information about the Controller in the debug bar. They are not required, but it is recommended to set them.

The `__construct` method is required, and must call the parent constructor, passing the `RequestContext` object as an argument.

`RequestContext` is a class that contains information about the request such as the method, parameters, etc... It is automatically created by the router, and passed to the Controller's constructor.

### Controller Methods

A Controller can have as many methods as you want. However, it must have at least one method, which is the `index` method. This method will be called if no action is provided in the URL.

```php
// HomeController.php

namespace Controller;

use Core\ControllerBase;
use Core\ControllerInterface;
use \Core\RequestContext;

// Create the Controller class.
class HomeController extends ControllerBase implements ControllerInterface {
  public string $name = 'Home';
  public string $description = 'Handles all requests related to the homepage.';

  /**
   * {@inheritDoc}
   */
  public function __construct(RequestContext $requestContext)
  {
    parent::__construct($requestContext);
  }

  /**
   * The index method.
   */
  public function index(): void
  {
    // Do something here.
  }
}
```

The `ControllerBase` class provides a few methods that can be used in the Controller methods.

- `render(string $view, array $data = [])`: Renders a view. The first argument is the name of the view to render, and the second argument is an array of data that will be passed to the view. The views are located in the `Theme/{{ your theme name }}/{{ Front or Back office }}/templates/` folder.

- `redirect(string $url)`: Redirects the user to the specified URL. The URL must be relative to the root of the website.

### Controller Routing

The Controller routing is handled by the `Router` class. The `Router` class is located in the `Core/Routing` folder.

The `Router` class is a singleton, which means that there can only be one instance of this class. This class is used to register routes, and to match the current request to the appropriate route.

#### Registering a route

To register a route, you must first go in the `routes.php` folder located in the root of the project. This file contains all the routes of the application.

You can then import your Controller's class, and register a new route using the `Router` class.

```php

// routes.php

use \Core\Routing\Router;

// Import the Controller class.
use Controller\HomeController;

// Register a new route -> GET /home (the action will be index by default)
Router::addRoute('home', '', HomeController::class, 0, 'GET');

// Register a new route -> GET /home/somestuff (the action will be somestuff, which means that the method to be called will be somestuff)
Router::addRoute('home_index', 'somestuff', HomeController::class, 0, 'GET');
```