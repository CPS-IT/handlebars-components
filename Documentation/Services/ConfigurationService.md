# [Configuration service](../../Classes/Service/ConfigurationService.php)

With the help of [`ConfigurationService`](../../Classes/Service/ConfigurationService.php)
it is possible to read TypoScript configuration. All you have to do is
specify the TypoScript configuration path.

## Example

TypoScript:

```typo3_typoscript
page {
    10 = USER
    10 {
        userFunc = Vendor\Extension\UserFunc\MyUserFunc->method
    }
}
```

PHP:

```php
$configurationService->get('page'); // returns NULL
$configurationService->get('page.'); // returns ['10' => 'USER', '10.' => ['userFunc' => 'Vendor\Extension\UserFunc\MyUserFunc->method']]
$configurationService->get('page.10.userFunc'); // returns 'Vendor\Extension\UserFunc\MyUserFunc->method'
```
