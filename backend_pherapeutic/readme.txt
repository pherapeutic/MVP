### PHP Version
  php 7.3

### LARAVEL Version

   laravel 8.0 

### Setup environments
Modify the environment variables files in root folder (.env)

### Dependency 

 If you run this command after downloading the project folder from git repository .
  composer update


### Run laravel server
 php artisan serve

### Migrate Command

#import table into the phpadmin panel run this command :- 
 	php artisan migrate 

#create new table the run this command:- 
    php artisan make:migration create_table_name_table    

### Routing
 
 # Admin route:-
There is a file in the routes/ directory called admin.php. That file is where you handle the request when admin visit your admin panel.

# Phone route:-
There is a file in the routes/ directory called api.php. That file is where you handle the requests when users visit your mobile phone via android/ios.

### Database Directory
The database directory contains your database migrations,  model factories, and seeds. If you wish, you may also use this directory to hold an SQLite database.

### Controller
 
 #Admin controller
 There is a files in the app/Http/Controllers/Admin/ directory . That directory is where you handle the admin dashboard functionality.

 #Api contoller 
  There is a files in the app/Http/Controllers/Api/ directory . That directory is where you handle the android/ios functionality.

### Model 
  There is a files in the app/Models/ directory .Each database table has a corresponding "Model" which is used to interact with that table. Models allow you to query for data in your tables, as well as insert new records into the table.

### View 
There is a files in the resources/views/admin/ directory . All admin panel html view templates available here.


