# Welcome!
Hey! This is one of my first (let's just call it my first) project working in Symfony. Any feedback is always welcome!

## Project Description
I enjoy video games. I especially enjoy RPGs and action adventure games. The problem with a lot of older RPGs and JRPGs
is that they have a lot of missable items. Stuff that if you don't obtain it by a certain point of the game, it's lost
forever (well until you start a new playthrough). I hate that. 

So I wanted to create an app where you could keep track of missables in games. Here's how it works:

- Users can upload guides (I call them templates).
- Each template will have a list of items that are missable in the game.
- When you start a new playthrough of a game with missables, you can find a template for that game and create a new
playthrough using that template. You'll then be able to check off missables as you progress.
  
So if you were to start a playthrough of say, *Tales of Vesperia*, you'd search for a template of that game created by
another user (or add one yourself). You would then start a new playthrough based off that template and check items off
as you progress.

### Setup
 
After you've cloned the project, run ```docker-compose up --build -d``` to set up containers.

Once the containers are up we'll need to install our dependencies by running 
```docker-compose exec php composer install```.

You can run commands from inside the php container with: ```docker-compose exec php bash```. This starts a shell inside 
the container.

### Database

We'll need to add an environment variable for the database connection. You can add this to the .env file, but I usually
keep it in .env.local: 

```DATABASE_URL="mysql://application:password@database/application"```

Once that's set up, run migrations. Assuming you've bashed into the php container, 
run: ```php bin/console doctrine:migrations:migrate```.

### Security

This project uses lexik/jwt-authentication-bundle for security. Set up is pretty simple.
Run ```php bin/console lekix:jwt:generate-keypair``` to generate a keypair that will live at config/jwt. Installing the
bundle should have installed environment variables pointing to these files. 

### Data Fixtures

To populate the database with some dummy data, run: ```php/bin/console doctrine:fixtures:load```.

This will create a singer user account. The email (for login) will be 'testuser@example.com'. The username will be 
'testAdmin. The password is 'password' (how original).

You'll also get dummy data for games, templates, playthroughs, sections, steps, etc.

### Internet Game Database

When games are added to the database it uses [Internet Game Database](https://www.igdb.com/discover) to find information
about game as well as cover art. You'll need to add your IGDB id and api secret as environment variables:

```
API_ID=id
API_SECRET=secret
```

###Makefile

There are some makefile commands to make navigating the project a bit easier. Each of these will need to begin with 
make. So the entire command for ```build``` is ```make build```. 

- ```build``` will build and start the containers.
- ```start``` will start the containers.
- ```exec-database``` will connect you into the database (it will ask for password).
- ```exec-php``` will start a shell inside the php container.
- ```exec-migration``` will create a migration file from outside container.
- ```exec-migrate``` will run migrations from outside container.
