# Craft Fonts API

Install

```sh
composer require deuxhuithuit/craft-fonts-api
./craft plugin/install fonts-api
```

It will add a single endpoint:

- `/actions/fonts-api/urls`: Will read the content of `@webroot/fonts` and return the list of font filenames.
