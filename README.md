# Dependency Analyzer for PHP
## Overview
Analyze dependencies on specific classes.We hope this will be useful for major version upgrades of dependent libraries.
For example, if you know that there is a disruptive change in a particular class, you can immediately see which classes in your project are affected.
Combined with a library upgrade tool such as [dependebot](https://github.com/dependabot/dependabot-core), it is also possible to comment the affected classes in the generated PR.
## Installation
Via Composer
```shell
composer require --dev tasuku43/dependency-analyzer
```

## Usage
Use [laravel/laravel](https://github.com/laravel/laravel) as a sample project to check the operation.
```shell
$ tree -L 1
.
├── README.md
├── app
├── artisan
├── bootstrap
├── composer.json
├── composer.lock
├── config
├── database
├── lang
├── package.json
├── phpunit.xml
├── public
├── resources
├── routes
├── storage
├── tests
├── vendor
└── webpack.mix.js

11 directories, 7 files
```

### Check the list of classes that depend on the specified namespace
```shell
$ ./vendor/bin/dependency-analyzer analyse --path app --pattern "Illuminate\Support"
 [============================] 100%
 ---------------- ------------------------------------
  Depender         App\Providers\AppServiceProvider
 ---------------- ------------------------------------
  Dependent List   Illuminate\Support\ServiceProvider
 ---------------- ------------------------------------

 ---------------- -----------------------------------
  Depender         App\Providers\AuthServiceProvider
 ---------------- -----------------------------------
  Dependent List   Illuminate\Support\Facades\Gate
 ---------------- -----------------------------------

 ---------------- ----------------------------------------
  Depender         App\Providers\RouteServiceProvider
 ---------------- ----------------------------------------
  Dependent List   Illuminate\Support\Facades\RateLimiter
                   Illuminate\Support\Facades\Route
 ---------------- ----------------------------------------

 ---------------- ----------------------------------------
  Depender         App\Providers\BroadcastServiceProvider
 ---------------- ----------------------------------------
  Dependent List   Illuminate\Support\Facades\Broadcast
                   Illuminate\Support\ServiceProvider
 ---------------- ----------------------------------------

 ---------------- ------------------------------------
  Depender         App\Providers\EventServiceProvider
 ---------------- ------------------------------------
  Dependent List   Illuminate\Support\Facades\Event
 ---------------- ------------------------------------

 ---------------- ---------------------------------------------
  Depender         App\Http\Middleware\RedirectIfAuthenticated
 ---------------- ---------------------------------------------
  Dependent List   Illuminate\Support\Facades\Auth
 ---------------- ---------------------------------------------


 [OK] Found 6 dependers
```

### Check the list of classes that depend on the specified class.
```shell
$ ./vendor/bin/dependency-analyzer analyse --path app --pattern "Illuminate\Support\ServiceProvider"
 [============================] 100%
 ---------------- ------------------------------------
  Depender         App\Providers\AppServiceProvider
 ---------------- ------------------------------------
  Dependent List   Illuminate\Support\ServiceProvider
 ---------------- ------------------------------------

 ---------------- ----------------------------------------
  Depender         App\Providers\BroadcastServiceProvider
 ---------------- ----------------------------------------
  Dependent List   Illuminate\Support\ServiceProvider
 ---------------- ----------------------------------------


 [OK] Found 2 dependers
```

### Check the list of classes that depend on the specified class by grouping them with Dependent Classe.
```shell
$ ./vendor/bin/dependency-analyzer analyse --path app --pattern "Illuminate\Support\ServiceProvider" --group-by dependent
 [============================] 100%
 --------------- ----------------------------------------
  Dependent       Illuminate\Support\ServiceProvider
 --------------- ----------------------------------------
  Depender List   App\Providers\AppServiceProvider
                  App\Providers\BroadcastServiceProvider
 --------------- ----------------------------------------


 [OK] Found 1 dependents
```

## License
The MIT License (MIT). Please see [LICENSE](https://github.com/tasuku43/puml2php/blob/main/LICENSE) for more information.
 
