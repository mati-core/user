# Mati-Core  | USER

[![Latest Stable Version](https://poser.pugx.org/mati-core/user/v)](//packagist.org/packages/mati-core/user)
[![Total Downloads](https://poser.pugx.org/mati-core/user/downloads)](//packagist.org/packages/mati-core/user)
![Integrity check](https://github.com/mati-core/menu/workflows/Integrity%20check/badge.svg)
[![Latest Unstable Version](https://poser.pugx.org/mati-core/user/v/unstable)](//packagist.org/packages/mati-core/user)
[![License](https://poser.pugx.org/mati-core/user/license)](//packagist.org/packages/mati-core/user)

Install
-------

Comoposer command:
```bash
composer require mati-core/user
```

Insert next code in class BasePresenter

```php
/**
 * @var string
 */
protected $pageRight = 'cms';

use UserPresenterAccessTrait;
```

Access control
-------

**Check access method:**
```php
public function checkAccess(string $rightSlug): bool
```

**Call in presenter**
```php
$this->checkAccess('right-slug');
```

**Call in latte**
```php
$presenter->checkAccess('right->slug');
```

Commands
--------

**Default init**

Create "Super admin" group with full access, 
"Admin" group with role "Admin" and "cms" right, 
Super admin account

```bash
php www/index.php app:user:init <username> <password> 
```

**Create user group**

Create user group. If is first user group, then be set as default.

```bash
php www/index.php app:usergroup:create <groupname>
```

**Create user**

Create user account and associate in default user group.

```bash
php www/index.php app:user:create <username> <password> 
```

API
---

**Sign In**

Link
```text
/api/v1/sign/sign-in 
```

Params (POST)
```php
function (string $login, string $password): array
```

Return
```neon
loginStatus: bool
errorMsg: null|string
```
