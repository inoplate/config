## Inoplate database configuration

Inoplate database configuration is a PHP package to load configuration from database.

## Example

```php
$config->has($key) // determine if config has value
$config->get($key, $default); // retrieve config value from database
$config->all() // retrieve all configuration
$config->set($key, $value, $cacheExpiration); // set config value to database and automatically cached. Default cache expiration is 10 minutes
```
## Instalation

> composer install inoplate/config

## Integration

Currently this package only provide integration to [Laravel  ^5.1](https://www.laravel.com). To integrate, please follow the steps described below.
>
    1. Add Inoplate\Config\Laravel\ConfigServiceProvider to your providers configuration.
    2. Migrate your database.
    3. Change the bootstrappers on your kernel, this file defaultly located in app/Http/Kernel.php and app/Console/Kernel.php.
    4. Look at their parent class, we need to overrider the bootstrappers property.
      * For Http located at vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php
      * For Console loacated at vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php
    5. Copy and paste the bootstrappers and change 'Illuminate\Foundation\Bootstrap\LoadConfiguration' with 'Inoplate\Config\Laravel\LoadConfiguration'
    6. Use as usual

**Please Note.**

> 
    1. This package will only persist config via set() call. Call via push(), prepend() and ArrayAccess will not persisted.
    2. This package will do necessary cache under the hood
    3. Use fully qualified name such as **app.name** and it's value. This package is not supporting nested key such as [app => ['name' => 'My Application', 'site' => 'https://mysites.com']] yet
    
**Running the tests**

This package using phpspec as specification testing and PHPUnit as integration testing. To run phpspec simply run **vendor/bin/phpspec run**. To run laravel integration test run **vendor/bin/phpunit -c tests/laravel/phpunit.xml**.

**Contribution**

Thank you for considering contributing to the this package. Just create pull request to and elaborate your purpose. We can discuss about the coding standart, branching strategy, etc.

**License**

This project is licensed under the [MIT License](https://opensource.org/licenses/MIT)