# Snowflake for Winter CMS
Version history:

- 1.0.1. initial release
## Installation with Composer

```sh
composer require skripteria/wn-snowflake-plugin

php artisan winter:up
```
## Why a new "CMS" within a CMS at all?

In real life there are at least 4 different user types that all need to be taken care of the same time:

- The technical developer with some inside knowledge of how to build custom functionality;
- The web designer, usually more interested in web design rather than backend development;
- The web interested end user who has a vision of how things should look like and trying hard to make it happen somehow. He is not afraid of playing around with customisation, a typical WordPress customer.
- The non-technical user who occasionally has to do some content editing because the job or business demands so.

The first 3 customer types are regularly taken into consideration when talking about how a CMS should look like, the last user type is often been forgotten.

However, when you develop a site for a paying customer the last customer type is the one that you will end up with.

The consequence is that you need to make content management bullet proof and hide everything but the very specific pieces of content that need to be managed.

Snowflake takes care of this by automizing the power the Winter CMS backend framework.

## Using Snowflake

Once installed, you first need to add the Snowflake component to any CMS Layout you want it to be used in. This will allow to use the Snowflake Twig Filter on the Layout itself and any Page that is using this Layout.

On these CMS pages you can now add some content variables using the `'sf'` Twig Filter, e.g.:

```html
<h1>{{ my_headline | sf('text', 'My awsome headline', 'Main headline of this page.') }}</h1>
```

The first part (`'my_headline'`) is the Snowflake Key that will be used to render the content. The Snowflake Key is just like a normal Twig variable as a reference to the content.

Every Snowflake Key must be unique within a given Page but may conflict with the keys from other Pages. When adding Snowflake Keys to a Layout name collisions with Pages can happen, therefore it is recommended generally prefix Snowflake Keys in layouts (e.g. `'layout_my_headline'`).

The `'sf'` filter then takes up to 3 arguments:

- Argument 1 defines the type of the content. This controls what backend widget is used for content management.
- Argument 2 *(optional)* allows to set a default value for the content. The default value has no effect on the types 'image', file', 'date', 'mediaimage' and 'mediafile'.
- Argument 3 *(optional)* allows to add a description for the user who is responsible for content management.

*Please note that the characters ',' and '|' (comma and pipe) must not be used within arguments.*


Currently Snowflake supports 7 standard types and 5 special ones.

The standard types are:

- `text` (simple text input, e.g. for headlines)
- `color` (Winter CMS color picker)
- `markdown` (Winter CMS markdown editor)
- `richeditor` (Winter CMS richtext editor)
- `code` (Winter CMS code editor)
- `date` (Winter CMS date picker)
- `textarea` (plain textarea field)

The 3 special cases are:

- `image`:

    To control images and will use the Winter CMS image upload widget.
    However, it uses 2 values for rendering, the image path and the img alt attribute. Therfore, the variable also needs to pass 2 values.


    This is done like this:

    ```html
    <img src="{{ my_image | sf('image','','This is the hero image on this page')}}" alt='{{ my_image___alt }}'>
    ```
    Please note that the `'sf'` filter is only added once in the `src` attribute, the `alt` attribute then just uses the same key with the suffix `'__alt'`.

- `mediaimage`:

    Sames as 'image', but 'mediaimage' uses the Winter Media Library as the image source:

    ```html
    <img src="{{ my_image | sf('mediaimage','','This is the hero image on this page')}}" alt='{{ my_image___alt }}'>
    ```

- `file`:

    To control file uploads. Like image it uses 2 values, this time the file path and the filename which shows up in the link.

    Similar to images this is how its done:

    ```html
    <a href="{{ my_file | sf('file','','This is my uploaded file')}}">{{ my_file__name }}</a>
    ```

- `mediafile`:

    Same as 'file', but 'mediafile' uses the Winter Media Library as the file source:

    ```html
    <a href="{{ my_file | sf('mediafile','',' 'This is my uploaded file')}}">{{ my_file__name }}</a>
    ```

- `link`:

    For internal Winter CMS links. The `link` type allows content managers just to copy the full url of the browser window without worrying about a proper format. It will be automatically converted into a clean relative link.

## Synchronizing with the Snowflake Backend

If you are using the Winter CMS backend to edit your code, all you need to do is to save your Page or Layout, Snowflake will automatically create or update the respective records in the database.

Once a Snowflake Key is removed (or renamed) it will be handled as unused database record by the following logic:

- There is already existing content in the record: keep it;
- Otherwise: delete it.

Alternatively there is a console command to sync all CMS Pages and Layouts:

```sh
php artisan snowflake:sync
```

You can use it to clean up all unused Snowflake Keys (caution - this also deletes the attached content):

```sh
php artisan snowflake:sync --cleanup
```
