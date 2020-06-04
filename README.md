# PHP FLASH

Its a session based redirector with flash messages for php. you can set flash messages between redirects and you can get your messages only once with php ```$_SESSION``` super global array.

## Installation

Use the package manager [composer](https://getcomposer.org/) to install php flash.

```bash
composer require hexbit/php-flash
```

## Usage

```php
use Hexbit\Flash\Flash;

// first initialize it:
Flash::init();


// build a new instance of flash
$flash = new Flash();

// simple usage and redirect back!
$flash->redirectBy('contact')
        ->message('failed', 'It seems your email is not valid!')
        ->redirectBack();

// redirect to specific location
$flash->redirectBy('contact')
        ->redirectLocation("https://somwhere/")
        ->withStatus(302)
        ->message('failed', 'It seems your email is not valid!')
        ->redirect();

// redirect to specific location and then redirect again after 5 seconds!
$flash->redirectLocation("https://somwhere/")
        ->message('failed', 'It seems your email is not valid!')
        ->setSecondRedirect("https://somwhereelse/", 5)
        ->redirect();


```

Now your flash messages will be available just once (after redirection) in the superGlobal ```$_SESSION``` array.

And obviously, there will be no messages for the next times and will be cleared.
```php

if (isset($_SESSION['failed']) {
        // show your error message for example
}

```


## Contributing
Pull requests are always welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make %sure% to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)