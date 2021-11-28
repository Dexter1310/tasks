***
**PRPOJECT SYMFONY 5.3**
***

**Technical Requirements**

* Documentation : https://symfony.com/doc/current/index.html
* Install PHP 7.2.5 =<
* Install Composer : https://getcomposer.org/download/
* Install Symfony : https://symfony.com/download
* Restart services and your edit code.
* run: composer install 
* From the  project run with : **symfony server:start**
* then url default : http://127.0.0.1:8000/

**Configuration database**

* Configurate file .env in the line:  DATABASE_URL="mysql://user:pass@127.0.0.1:3306/nameDataBase?serverVersion=5.7"
* php bin/console doctrine:database:create
* php bin/console doctrine:schema:update --force
 
**Added users**

* php bin/console doctrine:fixtures:load
`(*)This data record is in the file src\DataFixtures\AppFixtures.php`

**Create Entity**

1. 



**GIT**

4vjsj5WwksB8sNL4gbaQ

**COMANDS**
* php bin/console cache:clear