# Traefik-sessionauth

This is a small application that acts as an end point for the Traefik
forwardAuth middleware, authenticating users with a cookie and a
password-based login. You don't need any OAuth/based external services.
Think of it as "basic auth middleware without the browser popup".

## Configuration

Copy the `env.template` file to `.env` and edit it. There are two
important settings, `COOKIE_DOMAIN` and `USERS`:

`COOKIE_DOMAIN` is the top-level domain that's shared between the
application you want to secure and the login page. For example, if you
have the domains `app.example.com` and `auth.example.com` the
`COOKIE_DOMAIN` should be `example.com`.

`USERS` is a space-separated list of user names and passwords (separated
with a colon). The passwords must be hashed with PHPs `password_hash`
function. To hash a password on the command line you can run the following
command:

	php -r 'echo password_hash("your_password_here", PASSWORD_DEFAULT);'


**TODO:** describe PHP session tuning for longer-lived sessions and using
different storage mechanisms

### Overriding templates

If you want to show some branding on your page, use different styling or
wording, you can edit the files in the [`templates`](templates/)
directory.

**TODO:** describe how to override templates when using Docker


## Traefik setup

**TODO:** describe setup

## Development

To use the pre-commit git hooks, run

	vendor/bin/captainhook install

## Planned future features
* Redirection parameter to return to original page
* Add Dockerfile and build
* Add Page titles (Login page and index page) to config
* Unit tests and static analysis
* Add CI (GitHub Actions)
* Expose more session configuration
* Support for CORS headers instead of central cookie domain
* "Remember me" cookie for more independence from PHP sessions

