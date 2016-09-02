
## how to use

#### set captcha

```php

use Yaf\Session;
use Core\Captcha/Captcha;

$captcha = new Captcha(4, 100, 30);
Session::getInstance()->set('captcha', strtolower($captcha->getCode()));
$captcha->generate();

```

#### get captcha

```php

use Yaf\Session;

Session::getInstance()->get('captcha');

```