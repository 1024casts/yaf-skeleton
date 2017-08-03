
usage = you can use: make test, make clean

echo:
    @echo $(usage)
test:
    cd tests && ./vendor/phpunit/phpunit/phpunit
