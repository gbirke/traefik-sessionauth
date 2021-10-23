# Traefik-sessionauth

This is a small application that acts as an end point for the Traefik
forwardAuth middleware, presenting a password-based login. You don't
need any OAuth/based external services. Think of it as "basic auth
middleware without the annoying browser popup".

## Installation

**TODO:** describe configuration options
**TODO:** describe PHP session tuning

## Traefik setup

**TODO:** describe setup

## Planned future features
* Configure domain, cookie name, and users from .env file
* Redirection parameter to return to original page
* Templating for user-accessible pages
* Show form again when login fails
* "Remember me" cookie for more independence from PHP sessions
