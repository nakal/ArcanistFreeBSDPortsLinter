# Portlint wrapper for [[ https://github.com/phacility/arcanist | Arcanist ]]

This PHP class is a simple linter that wraps
[[ https://www.freebsd.org/doc/en/books/porters-handbook/porting-portlint.html | portlint ]]
to be usable directly from Arcanist.

## Some advice

This PHP class is not really usable without Arcanist which operates directly
within the repository to generate proper code reviews using Differential to
send a suggestion to [[ https://reviews.freebsd.org/ | FreeBSD's Phabricator ]].

In the state it is committed on github, it expects a symlink within
the repository. Something like: `<repo-path>/ports` -> `/usr/ports`
or to where ever you keep your writeable ports where you maintain them.

In other cases you would need to adapt the `PORTS_ROOT` in the class to
make it work properly.

## Installation

As said above, Arcanist expects to be setup properly in the project
root directory. The dotfiles `.arcconfig`, `.arclint`
and the file `__phutil_library_init__.php` need to be in project
root. It is possible to move `__phutil_library_init__.php` somewhere
else, but it needs to be found according to `.arcconfig`.

You need to have Arcanist and `ports-mgmt/portlint` installed and
you need to run `arc liberate` in the repository root, so the
file `__phutil_library_map__.php` will be updated in cases when
you want to add more classes.

## Manual lint

All you can do in this state is to execute, something like:

```
arc lint ports/port-category/portname/some-file-inside-the-port-directory
```

For example:

```
arc lint ports/shells/44bsd-csh/Makefile
```

## Bugs

Bugs may may appear when someone tries to integrate it into the
[[ https://reviews.freebsd.org/diffusion/P/ | FreeBSD Ports repository in Diffusion ]].

The linter itself would work more nicely when portlint would have a more
machine-readable output.
