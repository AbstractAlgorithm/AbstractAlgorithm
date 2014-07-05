AbstractAlgorithm
=================

Code for the [abstract-algorithm.com](http://abstract-algorithm.com/) website.

 - php-based MVC framework
 - Toxic templating engine

It's quite simple. I just wrote what I needed to have. Lightweight, easy to understand, without fancy stuff. Probably easy hackable. >_<


## Toxic
Features:

 - `if/else` branching
 - `foreach` loop
 - `region`s of code
 - `{variable|modifier}`

### Example

##### Template (list_posts.tmp):

```html

[foreach post in 10|Posts::recent]
    <h1>{post.title}</h1>

    <div class="content">
        {post.content}

        [if post.favorited]
            This page has been favorited.
        [end]

        [if post.comments|empty]
            This page doesn't have any comments.
        [else]
            This page has {post.comments|count} comments.

            <ul>
            [foreach comment in post.comments]
                <li>{comment.author} said: {comment.text}</li>
            [end]
            </ul>
        [end]
    </div>

    <div class="about">
        Author: [region post.author]Dragan Okanovic[end]
        Date: {post.formatDate('nice')}
    </div>
[end]
```

##### Controller code:

```php
class SampleController extends Controller {

    public function run()
    {
        # load template
        Template::load('list_posts')

        # generate html
        ->render();
    }
}
```

### Usage

Instructions are written inside the `[]` and variables are inside the `{}`.

##### Variables

You can access simple variables, class variables and their properties or arrays. You can also put modifiers.

```php
{var.prop}              # $var->prop
{var.key}               # $var['key']
{var.method()}          # $var->method()
{var|modif1,modif2}     # modif1( modif2($var) )
```

Modifiers are global or static functions.<br>
Method calls can take arguments.<br>

Example:

```php
class Post
{
    public $title;
    public $datetime;
    public $comments;

    public static $config = array( 'table' => 'post' );
    public static $className = 'Post';

    public function getTime() { ... }    
    public function formatTime($format) { ... }

    public static function getRecent($num) { ... }
}
```

```php
{post.title}                      # access property
{Post::$config.table}             # access static property

{post.getTime()}                  # call method
{post.formatTime('nice')}         # call method with parameters

# modifiers
{post.title|strtolower,ucfirst}   # ucfirst( strtolower( $post->title ) )

[foreach p in 10|Post::recent]    # use of modifier to call static method with param - a trick! :D
...
[end]

[if !post.comments|empty]         # same as it would be post.hasComments() but much prettier
Post has {post.comments|count} comms.
[end]

There is a total of {Post::all()|count} posts.

{post|var_dump}                   # good for debugging :D

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

[foreach post in 5|Post::recent]
...
[end]
```

##### Regions

If the variable named like the region is set, then that value is used, otherwise default stays.

```html
[region content]
    This text will remain as is, if not overwritten by a variable named 'content'.
[end]
```

There's no template-wise inheritance. Regions can be replaced with something like:

```html
[if content]
    {content}
[else]
    Default html.
[end]
```