AbstractAlgorithm
=================

Code for the [abstract-algorithm.com](http://abstract-algorithm.com/) website.

 - php-based MVC framework
 - Toxic templating engine

It's quite simple. I just wrote what I needed to have. Lightweight, easy to understand, without fancy stuff. Probably easy hackable. >_<


## Toxic
Features:

 - `if`, `if/else` branching (with optional `!`)
 - `foreach` loop
 - `region`s of code
 - `{var_name.property.method()}` style of variables

Code blocks are inbetween `[]`, and variables are inbetween `{}`.

### Example

#### Template (sample.tmp):

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
    Date: [region post.date]??/??/????[end]
</footer>
```

#### Controller code:

```php
class SampleController extends Controller {

    public function run()
    {
        # code logic ...
        $post = Post::getByName('sample');

        #load template
        Template::load('sample')

        # fillin data
        ->post( $post )
        ->hasComments( count($post->comments)>0 )

        # generate html
        ->render();
    }
}
```

#### Result:

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