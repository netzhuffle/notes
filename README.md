# Notes
Homework project

## Design decisions

The app was built based on the task description using the following design decisions and trade-offs:

* Database, REST API, and tests only; no frontend (reason: task description)
* Built in PHP with Laravel (reason: gaining experience based on job position description)
* Following Laravel setup and coding standards (reason: Laravel is an opinionated framework by itself, also this helps in understanding the code quickly)
* Following REST best practices (JSON, HTTP verbs, plural nouns in paths, standard HTTP error codes …)
* Test driven development (reason: Assure all code and features are testable and maintainable from the start, limit bugs and the need to debug issues to keep development time short)
* Functionality:
    * Multiple users (reason: task description)
    * Each user has their own notes (reason: task description asks for having database relationships & constraints)
    * Notes have a (required) title and a (optional, text-only) content (leaving it to clients if they want to expose the title to the user as its own input field or as the first line of the note itself as well as if they like to use markdown or other formats as note content; reason: keeping note enumeration simple and performant, allowing empty notes to still require title for easy finding, and to demonstrate a contraint on database-level as well as validation on implementation-level which the task description asks for)
    * Enumerating a user’s notes, paginated to 20 notes newest first (GET /notes; reason: task description asks to demonstrate scalability & performance considerations)
    * Getting a user’s note (GET /notes/{id}; reason: while not part of task description, this makes sense for a full CRUD operation set)
    * Creating a new note (PUSH /notes; reason: task description)
    * Updating a note (PUT /notes/{id}; reason: task description)
    * Partially updating a note (PATCH /notes/{id}; reason: usually part of RESTful APIs)
    * Deleting a note (DELETE /notes/{id}; reason: task description)
    * Accessing a note of a different user returns a 404 (reason: prevent knowing note existance that would be possible by returning 403 for forbidden but 404 for not existing at all)
* Out of scope (to avoid complexity beyond the task description and over-engineering):
    * New user registration or updating user settings
    * Note search, filtering, and sorting functionality
    * Note content format validation (e.g. requiring & validating for a specific text format like markdown)
    * Note sharing with other users or the public
    * Note content versioning
    * Restoring deleted notes (e.g. trash folder functionality)
    * Performant tests (only using integration tests using database for assuring full functionality)
    * Laravel [API Resource transformation objects](https://laravel.com/docs/10.x/eloquent-resources#main-content) (for the chosen database model, sending all model data to the clients is fine, thus avoiding overengineering)

## Database design

* Table user:
    * id (numeric)
    * token (textual)
* Table note:
    * id (numeric)
    * userid (numeric, foreign key)
    * title (textual)
    * content (textual, optional)

## Setup & run

```
cp .env.example .env
# Set APP_URL in .env
composer install
./vendor/bin/sail up -d &&
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
```

And get a user token (for Authorization: Bearer xyz) at POST /api/v1/users/{1|2|3}/token.
