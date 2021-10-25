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

### Overriding templates

If you want to show some branding on your page, use different styling or
wording, you can edit the files in the [`templates`](templates/)
directory.

The file [`docker-compose.fullexample.yml`](docker-compose.fullexample.yml)
shows and example how to use docker volume mounts to override templates

## Running the application

Run `docker-compose up` to run the application as a standalone application
on [localhost:8090](http://localhost:8090/). On its own, it's not very
useful but you can use the `docker-compose.yml` setup for development or
to try out the functionality without a Traefik setup.

## Traefik setup

In the file [`docker-compose.fullexample.yml`](docker-compose.fullexample.yml)
you can find a full example of how to use the app:

* A **Traefik** container, configured to serve on port 80.
* The **authentication app**, running as a PHP-FPM service.
* An **Nginx** web server, running two sites:
  * `site.example.docker` serves a single static page
  * `auth.example.docker` is a reverse proxy for the authentication
      application

The single Nginx configuration is for efficiency reasons, you could also
put the authentication app behind a second Nginx or a different web server
that acts as a FastCGI proxy.

To test the example on you local machine, you need to add the following
entries to your `/etc/hosts` file:

    127.0.0.1 monitor.example.docker
    127.0.0.1 site.example.docker
    127.0.0.1 auth.example.docker

### Request handling for the protected site
```
 Request for                                         +---------------------+
 site.example.docker                 Show if OK      |                     |
---------------------> ForwardAuth ----------------->| site.example.docker |
                       |       ^                     |                     |
                       |       |                     +---------------------+
                       |       |
                       |       |
                       |       | OK or redirect to auth.example.docker/login
                       |       +------------------------------+
                       |                                      |
                       |                             +---------------------+
                       |                             |                     |
                       |        OK to access?        | auth.example.docker|
                       +---------------------------> |                     |
                                                     +---------------------+
```

### Pitfalls to look out for when configuring for your own site

Make sure you're using the right protocol for the forwardAuth middleware
address! If you set it to `http` and your authentication URL is `https`,
every forwardAuth request will fail, even when you're logged in, because
Traefik will redirect to the HTTPs protocol, which looks like a failure to
forwardAuth.

## Development

To use the pre-commit git hooks, run

    vendor/bin/captainhook install

## Possible future features
* Make base path of auth configurable and concat base path with routes.
* Use encrypted cookies instead of session - this will make the app
	storage-independent and allows for longer-lasting authentication.
* Add Page titles (Login page and index page) to config
* Unit tests and static analysis (see
    https://odan.github.io/2020/06/09/slim4-testing.html for how to test)
* Add CI (GitHub Actions) to test and build Docker image
* Expose more session configuration
* Support for CORS headers instead of central cookie domain
* "Remember me" cookie for more independence from PHP sessions
* Describe PHP session tuning for longer-lived sessions and using
    different storage mechanisms
* Rewrite in Go to get rid of the need for a FastCGI proxy.

