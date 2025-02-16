
# BharatPHP MVC Framework
> BharatPHP is a lightweight Made In India PHP Framework used to develop web applications.
> Live demo [_here_](https://bharatphp.sandeepkumarpal.dev/). <!-- If you have the project hosted somewhere, include the link here. -->

## Table of Contents
* [General Info](#general-information)
* [Technologies Used](#technologies-used)
* [Features](#features)
* [Screenshots](#screenshots)
* [Setup](#setup)
* [Usage](#usage)
* [Project Status](#project-status)
* [Room for Improvement](#room-for-improvement)
* [Acknowledgements](#acknowledgements)
* [Contributing](#Contributing)
* [Contact](#contact)
<!-- * [License](#license) -->


## General Information
- BharatPHP is a web framework built in PHP programming language. 
- BharatPHP is a developer friendly framework which provides more control to a developer. 
- BharatPHP provides a MVC approach to build web based applications. 


## Technologies Used
- PHP 8.0
- Composer
- HTML
- CSS
- Javascript/Jquery


## Features
Here are the framework features
- MVC
- Lightweight
- Dynamic Routing
- Services Manager
- Events Manager
- Translations Manager
- Database Utilities: A mysql wrapper library added to generate quick sql queries
- Configuration Manager: To access configuration from anywhere in project life cycle
- Simple Array Language Translations: Add any numbers of languages
- Simple .phtml templating engine, but can be used with any templating engine like twig, smarty etc
- Extendable: Can be use to develop large applications
- A Demo Attached: A portfolio website(with bootstrap css framework) will be generated
- Use any php package from packagist.org by using composer 


## Screenshots
![Example screenshot](./img/screenshot.png)
<!-- If you have screenshots you'd like to share, include them here. -->


## Setup
What are the project requirements/dependencies? Where are they listed? A requirements.txt or a Pipfile.lock file perhaps? Where is it located?

Proceed to describe how to install / setup one's local environment / get started with the project.

### Tables to create if database session and caching via database is used
`DROP TABLE IF EXISTS sessions;
CREATE TABLE IF NOT EXISTS sessions (
  id varchar(40) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  last_activity bigint(20) UNSIGNED NOT NULL,
  data text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY ('id')
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;`

`
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `last_password_update_timestamp` timestamp NULL DEFAULT NULL,
  `user_type` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `auth_2fa` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `reset_password_secret` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `reset_password_secret_generated_time` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `password_history` longtext COLLATE utf8mb4_unicode_520_ci,
  `last_login_timestamp` datetime DEFAULT NULL,
  `is_password_reset_required` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
`


`Schema::create('cache', function (Blueprint $table) {
   15             $table->string('key')->primary();
   16             $table->mediumText('value');
   17             $table->integer('expiration');
   18         });
   19 
   20         Schema::create('cache_locks', function (Blueprint $table) {
   21             $table->string('key')->primary();
   22             $table->string('owner');
   23             $table->integer('expiration');
   24         });`


## Usage
How does one go about using it?
Provide various use cases and code examples here.

`write-your-code-here`


## Project Status
Project is: _in progress_ 


## Room for Improvement


Room for improvement:
- Documentation
- A sample application with admin panel

To do:
- Caching
- CLI support


## Acknowledgements
The project is based and inspired by the following frameworks 
- [POP PHP Framework](https://github.com/popphp/popphp).
- [Laravel](https://github.com/laravel/laravel/tree/3.0).
- [thecodeholic](https://github.com/thecodeholic/php-mvc-framework).



## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.


## Contact
Created by [@Sandeep Kumar](http://sandeepkumarpal.dev/) - feel free to contact me!


## License
[MIT](https://choosealicense.com/licenses/mit/)

<!-- Optional -->
<!-- ## License -->
<!-- This project is open source and available under the [... License](). -->

<!-- You don't have to include all sections - just the one's relevant to your project -->