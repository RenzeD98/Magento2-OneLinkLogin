# HyTales_OneLinkLogin

Magento 2 module that lets a developer log in to the admin panel via a single URL, without a password. Useful for quickly jumping into a colleague's environment or a shared dev/staging instance.

## How it works

Visiting `admin/onelinklogin/index/index?email=<email>` logs the browser in as the admin user matching `<email>`, provided that email is present in the module's configured account list. If no admin user exists yet for that email, one is created automatically with the configured role. Two-factor authentication is bypassed for this login.

The action only works when Magento is running in `developer` mode. In any other mode it returns a 404.

## Configuration

Settings live under **Stores > Configuration > Advanced > Admin > One-Link Login**:

- **Enabled** — turns the feature on or off. Disabled by default.
- **Accounts** — a list of `label`, `email`, and `role_id` entries. Only emails present here can use the one-link login.

## Security warning

This module bypasses password authentication and two-factor authentication for any email in its account list. Do not enable it on production or any environment reachable outside a trusted developer team, and never configure it with an administrator role unless that is intended.

## License

MIT, see [LICENSE](LICENSE).
