# HyTales_OneLinkLogin

Magento 2 module that lets a developer log in to the admin panel via a single URL, without a password. Makes local and shared development environments faster to work with by skipping the login form, and refuses to run unless Magento is confirmed to be in `developer` mode — it will not function in production.

## Installation

Available on [Packagist](https://packagist.org/packages/hytales/module-one-link-login). Install via Composer:

```
composer require hytales/module-one-link-login
bin/magento module:enable HyTales_OneLinkLogin
bin/magento setup:upgrade
```

## How it works

Visiting `admin/onelinklogin/index/index?email=<email>` logs the browser in as the admin user matching `<email>`, provided that email is present in the module's configured account list. If no admin user exists yet for that email, one is created automatically with the configured role. Two-factor authentication is bypassed for this login.

The action only works when Magento is running in `developer` mode. In any other mode it returns a 404.

## Configuration

Settings live under **Stores > Configuration > Advanced > Admin > One-Link Login**:

- **Enabled** — turns the feature on or off. Disabled by default.
- **Accounts** — a list of `label`, `email`, and `role_id` entries. Only emails present here can use the one-link login.

The **Enabled** setting can also be set via CLI:

```
bin/magento config:set admin/onelinklogin/enabled 1
```

## Security warning

This module bypasses password authentication and two-factor authentication for any email in its account list. It checks that Magento is running in `developer` mode and returns a 404 otherwise, so it will not function on a production-mode instance — but this check is the only safeguard in place. Do not enable it on any environment reachable outside a trusted developer team, and never configure it with an administrator role unless that is intended.

## License

MIT, see [LICENSE](LICENSE).
