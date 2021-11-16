## Snowflake for Winter CMS

This is a proof of concept version of a simplified database driven Content Manager within Winter CMS.

### Why a new CMS within a CMS at all?

In real life there are at least 4 different user types that all need to be taken care of the same time:

- The technical developer with some inside knowledge of how to build custom functionality
- The web designer, usually more interested in web design rather than backend development
- The web interested end user who has a vision of how things should look like and trying hard to make it happen somehow. Hes is not afraid of playing around with customisation, a typical Wordpress customer.
- The non-technical user who has to some content editing / publishing because his job or business demands so.

The first 3 customer types are regularly taken into consideration when talking about how a CMS should look like, the last user type is often been ignored.

However, when you develop a site for a paying customer the last customer type is the one that you will end up with.

The consequencce is that you need to hide everything but the very specific pieces of content that need be managed. Otherwise you eventually end up with a messed site by accident.

Snowflake tries to take care of these customers while keeping flexibility, devlopment speed and UI benefits of Winter MCS.

## Using Snowflake

Once you have installed the plugin, you first need to add the SF Page Component to any CMS Page you want include.

On these CMS page you now can add some content variables using the 'sf' Twig Filter, e.g.:

```html

<h1>{{ my_headline | sf('text', 'This is the main headline of this page.') }}</h1>
```
The first part ('my_headline') is the Twig variable that will be used to render the content, it will be saved in the database as the corresponding 'cms_key'. Every cms_key must be unique within a page.


The 'sn' filter takes 2 parameters:

- Parameter 1 describes the type of the content. The type mainly controls what widget is used for this content.
- Parameter 2 is optional and allows to add a description for the content manager.

    Currently there are 6 standard types and 2 specific ones.

    The standard types are:

    - text (simple text field, e.g. for headlines)
    - color (Winter CMS color picker)
    - markdown (Winter MCS markdown editor)
    - html (Winter CMS richtext editor)
    - code (Winter CMS code editor)
    - date (Winter CMS date picker)

    The 2 special cases are:

    - image:

    It will use the Winter CMS image updload widget.
    However it also allows to edit the img alt attribute. Therfore the variable need to pass to values, the image path and the alt attributes.
    This is done as in this example:

    ```html
    <img src="{{ my_image.path | sf('image','This is the hero image on this page')}}" alt='{{my_image.alt }}'>
    ```
    Please note the 'sf' filter needs only to be added once for an image.

    - link : for internal Winter CMS links.

     The link type allows that the content mangers just copy the url of the browser window without worrying about the proper format. When saved it will be automatically converted into a relative link.

     ### Synchronizing with the Snowflake Backend

     All you need to do is to save your CMS page, it will create or update the respective content cms_key in the database.
     Once a Snowflake cms_key is changed or removed it do the following:

     - Check if there is existing content in the database
     - If no content exists: delete this cms_key in the database



