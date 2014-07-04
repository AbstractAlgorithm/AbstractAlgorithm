AbstractAlgorithm
=================

Code for the [abstract-algorithm.com](http://abstract-algorithm.com/) website.

 - php-based MVC framework
 - Toxic templating engine

It's quite simple. I just wrote what I needed to have. Lightweight, easy to understand, without fancy stuff. Probably easy hackable. >_<


## Toxic
Features:

 - `if/else` branching (with optional `!`)
 - `foreach` loop
 - `region`s of code
 - `{var_name.property.method()}` style of variables

### Example

##### Template (sample.tmp):

```html
<h1>{post.title}</h1>

<div>
    {post.content}

    [if post.favorited]
        This page has been favorited.
    [end]

    [if !hasComments]
        This page doesn't have any comments.
    [else]
        This page has {numComments} comments.

        <ul>
        [foreach comm in post.comments]
            <li>{comm.getAuthor()} said: {comm.body_text}</li>
        [end]
        </ul>
    [end]
</div>

<footer>
    Author: [region author]Dragan Okanovic[end]
    Date: [region post.date]1/1/1980[end]
</footer>
```

##### Controller code:

```php
class SampleController extends Controller {

    public function run()
    {
        # code logic ...
        $post = Post::getByName('sample');

        # load template
        Template::load('sample')

        # fill-in data
        ->post( $post )
        ->hasComments( count($post->comments)>0 )

        # generate html
        ->render();
    }
}
```

##### Result:

```html
<h1>Sample post</h1>

<div>
    Lorem ipsum dolor sit amet.

        This page has been favorited.

        This page has 2 comments.

        <ul>
            <li>The God said: This is very good!</li>
            <li>The Programmer said: Naah, it's just 'okay'.</li>
        </ul> 
</div>

<footer>
    Author: Dragan Okanovic
    Date: Friday, July 4th, 2014
</footer>
```

### Usage

Instructions are written inside the `[]` and variables are inside the `{}`.

##### Variables

You can access simple variables, class variables or arrays.

 - array key: `myArray.key`
 - property: `myClassVar.property_name`
 - method: `myClassVar.method()`

Methods don't support arguments currently.

Example:

```php
class Post
{
    public $title = 'Title';
    public $config = array( 'table' => 'post' );
    public function getTime() { ... }    
}
```

```html
<h1>{myPost.title}</h1>
<small>Date created: {myPost.getTime()} | Category: {myPost.config.table}</small>

```

##### If

It must have exactly one mathicng `[end]` tag, whether it has `[else]` branch of not.

 - `[if condition] ... [end]`
 - `[if !notCondition] ... [end]`
 - `[if condition] ... [else] ... [end]`

##### Foreach

Well, pretty self-explainatory.

```html
[foreach post in post.getRecent()]
    {post.show()}
[end]
```

##### Regions

If the variable named like the region is set, then that value is used, otherwise default stays.

```html
[region content]
    This text will remain as is, if not overwritten by a variable named 'content'.
[end]
```

There's no template-wise inheritance. Regions will be replaced soon with something like:

```html
[if !content]
    ...
[end]
```