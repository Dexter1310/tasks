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
* You can read about this in the documentation at this link: https://symfony.com/doc/current/doctrine.html
* Configurate file .env in the line:  DATABASE_URL="mysql://user:pass@127.0.0.1:3306/nameDataBase?serverVersion=5.7"
* php bin/console doctrine:database:create
* php bin/console doctrine:schema:update --force
 
**Added users**

* php bin/console doctrine:fixtures:load
`(*)This data record is in the file src\DataFixtures\AppFixtures.php`

**Create Entity**

1. php bin/console make:entity

2. php bin/console doctrine:migrations:diff

3. php bin/console doctrine:migrations:migrate



**GIT**

4vjsj5WwksB8sNL4gbaQ

**COMANDS**
* php bin/console cache:clear


***
****ABOUT THE CIRCLE****


*USER:*

Only a user when it is active will be accessible in login to the application. It is activated when you receive an activation email to your email account that you have entered in the form.

CRON for TASK PERIODIC:

$crontab -e 

example:
50 13 * * * cd  /home/dexter/Escritorio/projects/the_circle && /usr/local/bin/symfony console app:task-periodic

after **control+X**  for load 
