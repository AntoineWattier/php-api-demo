# PHP API Demo

## Installation

Run `composer`

## Configuration

- Copy `.env.example` to `.env` and modify the database settings

- Run `php artisan migrate` to create the database structure

- Run `php artisan db:seed` to populate the database (Optional)

## Testing

- Set your test database in `phpunit.xml`
    `<env name="DB_DATABASE" value="phpapidemo_test"/>`

- Run `phpunit`

## License

PHP API Demo is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
