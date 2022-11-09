# Slim Framework 4 / Twig Application


## Login/Registration Page & Basic Admin panel


To run this project as it is, the latest version of Docker and Composer should be installed on your machine.


Also, take a look at example.sql to know for a DB structure.


Preview: 


## Docker
Crete ```.env``` files in ```public``` folder, then copy content of the ```.env.example```. 
Here you can manage your DB credentials, .


In your project root run ```composer install```. This will load necessary packages from ```composer.json``` and install them to the ```vendor``` folder.


Then you can run this commands:
```
docker-compose build
docker-compose up -d
```
to build project container and start development server in detached mode.


Finally, you can go to ```http://localhost/``` to enter project main page.


## Other environments
Copy content of the folder to your server root.


Crete ```.env``` file in "```public```" folder, then copy content of the ```.env.example``` to ```.env``` file.


Now, you can manage your DB credentials and  server settings to run this project.
