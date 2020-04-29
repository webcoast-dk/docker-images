# Composer 1.10.5 with PHP 7.3

This image is meant to be used in build processes to run the build in the exact environment, the application
later runs in.

## Usage
The project root directory (the one with the composer.json) file is mounted to `/build` in the container. Composer runs
the provided `{composerCommand}`, e.g. `update` or `install`.

If you need SSH in the container, e.g. for access to non-public git repositories, the authentication agent socket can be
mounted into the container and pointed to via the environment variable `SSH_AUTH_SOCK`. Unfortunately this does not work
on MacOS due to some restrictions within docker.

If the `SSH_AUTH_SOCK` variable is **not** set in the container, a new agent process is started in the container. In this
case, the local `.ssh` directory can be mounted to `/root/.ssh` to allow access to the SSH keys.

## Examples

### Docker

**Default:**  
`docker run --rm -ti -v $(pwd):/build webcoastdk/composer:1.10-php7.3-alpine3.10 {composerCommand}`

**SSH forwarding:**  
`docker run --rm -ti -v $(pwd):/build -v $SSH_AUTH_SOCK:$SSH_AUTH_SOCK -e SSH_AUTH_SOCK=$SSH_AUTH_SOCK webcoastdk/composer:1.10-php7.3-alpine3.10 {composerCommand}`

**SSH agent:**
`docker run --rm -ti -v $(pwd):/build -v ~/.ssh:/root/.ssh webcoastdk/composer:1.10-php7.3-alpine3.10 {composerCommand}`

### docker-compose

**Default:**  
```yaml
version: '3.3'

services:
  composer:
    image: webcoastdk/composer:1.10-php7.3-alpine3.10
    volumes:
      - ./:/build
```

**SSH forwarding:**  
```yaml
version: '3.3'

services:
  composer:
      image: webcoastdk/composer:1.10-php7.3-alpine3.10
      volumes:
        - $SSH_AUTH_SOCK:$SSH_AUTH_SOCK
        - ./:/build
      environment:
        SSH_AUTH_SOCK: $SSH_AUTH_SOCK
```

**SSH agent:**  
```yaml
version: '3.3'

services:
  composer:
    image: webcoastdk/composer:1.10-php7.3-alpine3.10
    volumes:
      - ~/.ssh:/root/.ssh
      - ./:/build
```
