
## Usage

目前供支持10种格式校验
 
  - required
  - match
  - email
  - url
  - compare
  - length
  - in
  - number
  - mobile
  - date
        
```php

    $checkRules = [
        ['uid,group_id', 'required'],
        ['phone', 'match', 'pattern' => '/^1[34578]\d{9}[\d,]*$/', 'allowEmpty' => false],
        ['email', 'email', 'allowEmpty' => false],
        ['url', 'url', 'allowEmpty' => false],
        ['repassword', 'compare', 'target' => 'password', 'allowEmpty' => false],
        ['username', 'length', 'min' => 4, 'max' => 3000, 'allowEmpty' => false],
        ['status', 'in', 'range' => [0, 1], 'allowEmpty' => false],
        ['uid,group_id', 'number', 'min' => 1],
        ['phone', 'mobile', 'range' => [0, 1], 'allowEmpty' => false],
        ['birthday', 'date', 'format' => 'Y-m-d', 'allowEmpty' => false]
    ];
    
    $needCheckArr = [
        'email' => $email 
    ];
    if (Validator::validator($needCheckArr, $checkRules) !== true) {
        throw new \Exception('param error', Code::PARAMS_ERROR);
    }
```