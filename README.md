# User authentication - authenticate a user

![GitHub release](https://img.shields.io/github/release/FrancoisChaumont/user-authentication.svg)
[![contributions welcome](https://img.shields.io/badge/contributions-welcome-brightgreen.svg?style=flat)](https://github.com/FrancoisChaumont/user-authentication/issues)
[![GitHub issues](https://img.shields.io/github/issues/FrancoisChaumont/user-authentication.svg)](https://github.com/FrancoisChaumont/user-authentication/issues)
[![GitHub stars](https://img.shields.io/github/stars/FrancoisChaumont/user-authentication.svg)](https://github.com/FrancoisChaumont/user-authentication/stargazers)
![Github All Releases](https://img.shields.io/github/downloads/FrancoisChaumont/user-authentication/total.svg)

PHP library to authenticate users verifying their credentials stored in a database.  
This library provides a level of abstraction that allows it to be easily used with any database table/column definition.

## Getting started
These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Requirements
PHP 7.1+ | MySQL/MariaDB 

### Installation
Install this package with composer by simply adding the following to your composer.json file:  
```
"repositories": [
    {
        "url": "https://github.com/FrancoisChaumont/user-authentication.git",
        "type": "git"
    }
]
```
and running the following command:  
```
composer require francoischaumont/user-authentication
```

## Testing
Under the folder named *tests* you will find a SQL file and a test script ready to use.  
The SQL file is a dump of a test database which the test script relies on.  
Only run in web browser, not CLI.

## Built with
* Visual Studio Code

## Authors
* **Francois Chaumont** - *Initial work* - [FrancoisChaumont](https://github.com/FrancoisChaumont)

See also the list of [contributors](https://github.com/FrancoisChaumont/user-authentication/graphs/contributors) who particpated in this project.

## License
This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

## Notes
Todo: Add support for more databases (currently only supports MySQL and MariaDB)

