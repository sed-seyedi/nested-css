# nested-css
Sass/Less css nesting feature in 90 lines of PHP.

### What?

So you can write:

```css
a {
   color: blue;
   .button {
       padding:10px;
       border:1px solid grey;
   }
}
```

instead of this:

```css
a {
  color: blue;
}

a .button {
  padding: 10px;
  border:1px solid grey;
}

```

### Why?

1. In my opinion, the way CSS preprocessors handle nesting of child selectors is awesome -- the rest can be easily implemented in any programming language e.g. variables and includes (mixins).
2. It's much faster than a full implementation of Sass/Less.

### How?

Just include the `nested-css.php` in your project then you can simply do:

```php
$ncss = new NestedCSS();

#### parse nested css
echo $ncss->parse($input);

#### parse then include a .css file
$ncss->include('path/to/some.css');

#### include and cache the result
#### the cache system automatically refreshes
#### the cache once you make a change
$ncss->include('path/to/some.css',true);
```

### Future?

We are using this library in production at [SunSed.com](https://www.sunsed.com) so if you find a bug and submit it, we will fix it very quickly.

Todos:

- Add helper functions for handling colors and px calculations like in Sass.
- Try to see if you can improve compiler speed.
- Support for composer.

### Licence?

Nested-css is licensed under the MIT License.

### Known Bugs?

Your nesting should be clean or it will fail compiling it correctly.

For example:

```css
p {
    color: blue;
    .red{
        color: red;
    }
    font-family: arial;
}
```

To keep nested-css compiler fast we keep it's internal very simple and instead of making it smarter/slower, we think it's better to put the burden on the programmer. So instead, please nest your CSS selectors like this:

```css
p {
    color: blue;
    font-family: arial;
    .red{
        color: red;
    }
}
```
