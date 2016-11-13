# Jelly

Let's say you want to create a new [Cranberry command-line application](https://github.com/cranberry/cli) called `whizzbang`. You can use Jelly as a template to get up and running quickly.

## Getting Started

First, let's clone this repository's `jelly` branch into a new local directory `whizzbang`:

```
$ git clone --recursive --branch jelly https://github.com/cranberry/jelly.git whizzbang
```

Next, let's run `jelly init` to step through a quick setup wizard:

```
$ cd whizzbang
$ bin/jelly init
```

Choose a new application name:

```
Application name: whizzbang
```

and a PHP namespace for any project-specific code (press Enter to accept the default):

```
PHP Namespace [Whizzbang]:
```

Rather than start a brand new project with all the history of the `jelly` project, the wizard helps you start fresh using a new "orphan" branch. Press Enter to accept the default `master` or enter a different branch name:

```
New branch name [master]: develop
```

Jelly includes "last-chance" exception handling, which presenting a nicely formatted and informational log to help the application developer diagnose the issue at hand. When **Project URL** is set, Jelly also directs the user to file a bug report.

If you already know that URL, you can set it now (or press Enter to skip):

```
Project URL: https://github.com/ashur/whizzbang/issues
```

> **Note** — You can update this value later in `lib/config.json`

In an effort to leave you with a clean working copy, the wizard offers to delete the original `jelly` branch:

```
Delete branch 'jelly'? [yes]
```

Finally, since you probably don't want to ship the  `init` wizard command with your application, it offers to delete itself:

```
Delete command 'init'? [yes]
```

That's it! Let's take `whizzbang` for a spin:

```
$ bin/whizzbang
usage: whizzbang [--help] [--version] <command> [<args>]

Commands are:
   install    Symlink whizzbang to a convenient path

See 'whizzbang --help <command>' to read about a specific command.
```

## Install

Let's take a quick detour to talk about a built-in command: Jelly includes `install` to help simplify the process of symlinking the executable to a directory:

```
$ bin/whizzbang install /usr/local/bin
Linked to '/usr/local/bin/whizzbang'
```

If we choose a directory that is on our shell `$PATH`, we can run `whizzbang` from anywhere on the command line, not just from within the project directory:

```
$ cd ~/Desktop
$ whizzbang --version
whizzbang version 0.1.0
```

Unlike `init`, `install` may be a command you want to include in your own application. If not, just delete `lib/commands/install.php`.


## whizzbang hello

Let's add a new command `hello`.

Jelly automatically includes all `.php` files located in `lib/commands` . Create `lib/commands/hello.php`:

```
$ cd whizzbang
$ touch lib/commands/hello.php
```

> **Note** — The filename isn't meaningful to Jelly, but it's helpful to name it after the command it contains.

A barebones Cranberry command file looks like this:

```
<?php

use Cranberry\CLI\Command;

/**
 * @name            hello
 * @description     Say hello
 * @usage           hello [<who>]
 */
$command = new Command\Command( 'hello', 'Say hello', function( $who='world' )
{
    $this->output->line( "Hello, {$who}." );
});

return $command;
```

A few points...

First, note that the command Closure takes an optional argument `$who`. Cranberry automatically turns this into an optional command-line argument for the command `hello`:

```
$ whizzbang --help hello
usage: whizzbang hello [<who>]
$ whizzbang hello
Hello, world.
$ whizzbang hello Dolly
Hello, Dolly.
```

Second, note that output is handled here by a `Cranberry\CLI\Output\Output` object made available via `$this->output`.

> See [Output.php](https://github.com/cranberry/cli/blob/master/src/CLI/Output/Output.php) for more information on available methods.

Finally, note that the `Cranberry\CLI\Command\Command` object is also returned:

```
return $command;
```

If not, Jelly will include the file but not register the command.
