# Pherapeutic App

This app provides way to communicate to therapist

## Getting Started

After clone the project fallow these steps:

1.run the composer install command
2.run the php artisan migrate command
3.run php artisan key:generate command


## Configure and uses

Configure these library account and add keys into env file:
### For payment use stripe
https://stripe.com/

STRIPE_KEY=
STRIPE_SECRET=
STRIPE_CURRENCY=GBP
AMOUNT=50
STRIPE_CLIENT_ID=

### For push norification use firebase
https://firebase.google.com/

THERAPIST_PUSH_NOTIFICATION_KEY=

### For video calling use agora

https://www.agora.io/en/
AGORA_APP_ID=
AGORA_APP_CERTIFICATE=



## Built With

* [Laravel](https://laravel.com/docs/7.x) - The laravel framework used
* [PHP](https://www.php.net/docs.php) - PHP version: 7.4.4
* [Mysql](https://dev.mysql.com/doc/) - Used to manage the data at backend