`composer install`

**_Config_**

`mkdir -p config/jwt`

`openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096`

`openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout`

`symfony server:start`

**_Commands_**

`bin/console app:gas-price`

`bin/console app:gas-station-closed`

`bin/console messenger:consume async_priority_high -vv`

`bin/console messenger:consume async_priority_low -vv`