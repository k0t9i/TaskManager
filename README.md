# Task manager system using Clean Architecture, DDD and CQRS. [![CI](https://github.com/k0t9i/TaskManager/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/k0t9i/TaskManager/actions/workflows/ci.yml)

## Environment setup
1) Install Docker
2) Clone the project: git clone https://github.com/k0t9i/TaskManager.git
3) Run docker containers: `docker-compose up -d --build`
4) Setup application: make setup. This step installs composer dependency, creates db, runs migrations, warmups the cache and reloads supervisor.
## Performing checks
- `make test` - phpunit tests
- `make code-style` - php-cs-fixer checks
- `make static-analysis` - psalm checks
- `make check-all` - all of the above
## Application structure
The application contains 4 nodes:
- Projects - http://127.0.0.1:8080
- Users - http://127.0.0.1:8081
- Tasks - http://127.0.0.1:8082
- Projections

Each node is a separate bound context, communication between them occurs only through the event bus.
## Database
All data is stored on the same server in different databases, you can see the structure of the databases via adminer http://127.0.0.1:9080/.
## Repository structure
All source code is in app directory:
```scala
app
|-- config // Symfony configurations
|   |-- projections // Projection configurations
|   |-- projects // Project configurations
|   |-- shared // Common for all nodes
|   |-- tasks // Task configurations
|   `-- users // User configurations
|-- src // Application code
|   |-- Projections // Projection code
|   |-- Projects // Project code
|   |-- Shared // Common for all nodes
|   |-- Tasks // Task code
|   `-- Users // User code
|-- symfony // Symfony framework
`-- tests // Tests
    |-- Projections
    |-- Projects
    |-- Shared // Common for all nodes
    |-- Tasks
    |-- Users
    `-- bootstrap.php // Common for all nodes
```
After starting a docker containers, local directories are mapped in the same way for all containers, for example for projects:
- ./app/symfony:/var/www/symfony
- ./logs/supervisor/projects:/var/log/supervisor
- ./app/config/shared:/var/www/config/shared
- ./app/config/projects:/var/www/config/app
- ./app/src/Shared:/var/www/src/Shared
- ./app/src/Projects:/var/www/src/Projects
- ./app/tests/Shared:/var/www/tests/Shared
- ./app/tests/Projects:/var/www/tests/Projects
- ./app/tests/bootstrap.php:/var/www/tests/bootstrap.php
## Code structure
```scala
.
|-- Projects // Code related to a specific bound context
|   |-- Application // Application layer depends only on Domain layer
|   |   |-- Command
|   |   |-- Handler // Command and query handlers
|   |   |-- Query
|   |   |-- Service // Application services
|   |   `-- Subscriber // Domain event subscribers
|   |-- Domain // Domain logic layer does not depend on other layers
|   |   |-- Collection
|   |   |-- Entity // Entities and aggregate roots
|   |   |-- Exception // Domain exceptions
|   |   |-- Repository // Repository interfaces
|   |   `-- ValueObject
|   `-- Infrastructure // Infrastructure layer depends on Domain and Application layers
|       |-- Controller // Symfony controllers
|       |-- Persistence // Migrations, entity mapping in database
|       `-- Repository // Repository realizations
`-- Shared // Common code for all bound contexts
    |-- Application
    |-- Domain
    |-- Infrastructure
    `-- SharedBoundedContext
```
## Buses
Command and query buses are synchronous, implemented via the symfony messenger.

The event bus is asynchronous implemented via the symfony messenger and RabbitMQ (http://127.0.0.1:15672/). Events are received from RabbitMQ through the Symphony Messenger, which is launched using the supervisor.
