# BLOG_CMS 📰

> Authors: [Martin](https://github.com/MartinPJB) & [Surreal-Maggie](https://github.com/Surreal-Maggie).

This is a simple blog CMS made during our PHP classes.

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

### I. Creating a Controller

To create a new Controller, you must create a new file in the `Controller/` folder. The name of the file must be the name of the Controller, with the first letter in uppercase, and the suffix `Controller`. For example, if you want to create a `home` Controller, you must create a file named `HomeController.php`.

The Controller must extend the `ControllerBase` class, and must implement the `ControllerInterface` interface, located in the core. This will ensure that the Controller has the required methods to work properly.

```php

// HomeController.php

// Do not forget to set the namespace in your Controller file.
namespace Controller;

// Import the required classes.
use \Core\Controller\ControllerInterface;
use \Core\Controller\ControllerBase;
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

### II. Controller Methods

A Controller can have as many methods as you want. However, it must have at least one method, which is the `index` method. This method will be called if no action is provided in the URL.

```php
// HomeController.php

namespace Controller;

use \Core\ControllerBase;
use \Core\ControllerInterface;
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

### III. Controller Routing

The Controller routing is handled by the `Router` class. The `Router` class is located in the `Core/Routing` folder.

The `Router` class is a singleton, which means that there can only be one instance of this class. This class is used to register routes, and to match the current request to the appropriate route.

#### A. Registering a route

To register a route, you must first go in the `routes.php` folder located in the root of the project. This file contains all the routes of the application.

You can then import your Controller's class, and register a new route using the `Router` class.

```php

// routes.php

use \Core\Routing\Router;

// Import the Controller class.
use \Controller\HomeController;

// Register a new route -> GET /home (the action will be index by default)
Router::addRoute('home', '', HomeController::class, 0, 'GET');

// Register a new route -> GET /home/somestuff (the action will be somestuff, which means that the method to be called will be somestuff)
Router::addRoute('home', 'somestuff', HomeController::class, 0, 'GET');
```

<hr>

## Theming 🎨

The theming system is based on the `Twig` templating engine. This means that you can use Twig's syntax in your views.

### I. Creating a Theme

> The default theme is `Default`. It is meant to be used as an example and as a base for your own themes. It is not complete, and is not meant to be used in a production environment. The semantic of the HTML is not good at all, and there are no styles.

To create a new theme, you must create a new folder in the `Themes/` folder. The name of the folder will be the name of the theme.

The theme folder must contain two folders: `Front` and `Back`. The `Front` folder will contain the templates for the front office, and the `Back` folder will contain the templates for the back office.

The `Front` and `Back` folders must contain a `templates` folder, which will contain the actual templates.

The `templates` folder must contain a `base.html.twig` file, which will be the base template for all the other templates.

All the templates must have the `.html.twig` extension, and must be located in a subfolder named after the name of the Controller that will use the template.

Here is an example of a theme structure:

```
Themes/
  MyTheme/
    Front/
      templates/
        base.html.twig
        MyController/
          index.html.twig
          somestuff.html.twig
    Back/
      templates/
        base.html.twig
        MyOtherController/
          index.html.twig
          somestuff.html.twig
```

### II. Creating a Template

A template is a simple file that contains the HTML structure of the page. It can also contain Twig's syntax, which will allow you to use variables, loops, conditions, etc...

It is recommended to create a `index.html.twig` file at the root of the template, which will be the base template for all the other templates. This file will contain the basic structure of the page, and will be extended by all the other templates. (Example [here](https://github.com/MartinPJB/BLOG_CMS/blob/master/Themes/default/Front/templates/index.html.twig))

The templates should also use the `extends` and `block` keywords to extend the base template, and to define the content of the page.

I also recommend adding a comment at the top of the template in order to describe what the template is used for.

Example:

```twig
{#
Name: index.html.twig
Path: default/Front/templates/pages/Articles/index.html.twig

Variables:
  - {@array} articles: Articles list to display:
    - {@object} article: Article to display:
      - {@object} image: Image to display:
        - {@string} path: Image path
        - {@string} alt: Image alt
      - {@string} title: Article title
      - {@string} description: Article description
      - {@string} date: Article date
      - {@string} author: Article author
      - {@object} category: Article category:
        - {@string} name: Category name
      - {@array} tags: Article tags list
#}
```

### II. Using the Theme

For now, the only way to use the theme is to modify directly your database in the `site_settings` table. You must add a new row with the **key** `theme`, and the `value` the name of your theme.
