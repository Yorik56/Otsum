# Otsum

> - PHP 8
> - Yarn
> - Make
> - Webpack
> - Mercure

# Install project

## Get project: 

`git clone https://github.com/Yorik56/Otsum`

## Get dependencies: 
`composer install`

## install Yarn
[https://classic.yarnpkg.com/lang/en/docs/install/#windows-stable](https://classic.yarnpkg.com/lang/en/docs/install/#windows-stable)

`npm install --global yarn` 

`yarn install`

## Install Make 

### Install chocolatey
[https://chocolatey.org/install#individual](https://chocolatey.org/install#individual)

### Run as Administrator
`choco install make`

## Create Database
> Create a database named "Otsum"

> Lauch migration script

`php bin/console doctrine:migrations:migrate`

# Configs

- [.env.exemple](https://github.com/Yorik56/Otsum/blob/main/.env.exemple)
- [config/packages/webpack_encore.yaml](https://github.com/Yorik56/Otsum/blob/main/config/packages/webpack_encore.yaml)
- [config/packages/liip_imagine.yaml](https://github.com/Yorik56/Otsum/blob/main/config/packages/liip_imagine.yaml)
- [config/packages/vich_uploader.yaml](https://github.com/Yorik56/Otsum/blob/main/config/packages/vich_uploader.yaml)
- [config/packages/mercure.yaml](https://github.com/Yorik56/Otsum/blob/main/config/packages/mercure.yaml)

# Launch project

> run make (Launch Mercure Serveur)

`make`

> run yarn watch (Compile css/js)

`yarn watch`

*GOOD TO GO*
