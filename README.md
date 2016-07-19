# SingleSO

A Single Sign-On server for a private central account system with an OAuth 2 service provider and support for login through third-party account system providers.


## Installation

**1:** Install the package as a project.

```sh
composer global require "fxp/composer-asset-plugin:~1.1.1"
composer create-project --prefer-dist --stability=dev singleso/singleso singleso
```

**2:** Copy the `*.sample.php` files in the `config` directory renaming them without `.sample` and configure all the settings.

**3:** From the project root, run the database migrations.

```sh
php singleso migrate/up --migrationPath=@vendor/dektrium/yii2-user/migrations
php singleso migrate/up
```

**4:** Ideally configure your web server to serve the `web` folder as the document root.

**5:** For pretty URL's (enabled by default in the config settings), in the `web` directory, create an `.htaccess` like the following (or otherwise configure your server to achieve the same result).

```
RewriteEngine on
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php
```

(If not configured to be the root of the domain, you will need to adjust the `RewriteBase` path to match).

**6:** Access the web front end and register an account, following the link in the email to finish registering.

**7:** Access your database through the interface of your choice and update the `is_admin` column of the `user` table for the user you just created.


## Theming

You can add or replace default styling using a custom theme.

An example custom theme can be created by creating the following files in the `themes` directory.

```
themes/
- example-theme/
- - static/
- - - theme.css
- - - theme.js
- - theme.json
```

`theme.json`

```json
{
	"name": "Example Theme",
	"sourcePath": "static",
	"css": [
		"theme.css"
	],
	"js": [
		"theme.js"
	],
	"depends": [
		"app\\assets\\DefaultThemeAsset"
	],
	"bootstrap": true,
	"juiTheme": true
}
```

`theme.css`

```css
.navbar-brand {
	background: blue;
}
```

`theme.js`

```js
(function() {
	'use strict';
	var navbarBrand = document.querySelector('.navbar-brand');
	var text = navbarBrand.textContent;
	var toggle = false;
	setInterval(function() {
		navbarBrand.textContent = toggle ? text : 'Sample Theme JavaScript!';
		toggle = !toggle;
	}, 1000);
})();
```


## Bugs

If you find a bug or have compatibility issues, please open a ticket under issues section for this repository.


## License

Licensed under the Mozilla Public License, v. 2.0.
