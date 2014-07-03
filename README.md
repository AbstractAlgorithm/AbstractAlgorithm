AbstractAlgorithm
=================

Code for the [abstract-algorithm.com](http://abstract-algorithm.com/) website.

 - php-based MVC framework
 - Toxic templating engine

It's quite simple. I just wrote what I needed to have. Lightweight, easy to understand, without fancy stuff. Probably easy hackable. >_<


## Toxic
Supports `if` branching, `foreach` loop, `region`s of code and `{variables}`.

### Example

#### Template (sample.tmp):

```html
<h1>{title}</h1>

<div>
    {content}

    [if favorited]
        This page has been favorited.
    [end]

    [if !hasComments]
        This page doesn't have any comments.
    [else]
        This page has {numComments} comments.

        <ul>
        [foreach comm in comments]
            <li>{comm.getAuthor()} said: {comm.body_text}</li>
        [end]
        </ul>
    [end]
</div>

<footer>
    Author: [region author]Dragan Okanovic[end]
    Date: [region date]??/??/????[end]
</footer>
```

#### Controller code:

```php
class SampleController extends Controller {

    public function run()
    {
        $post = Post::getByName('sample');

        Template::load('sample')

        ->title( $post->title )

        ->content( $post->content )

        ->comments( $post->comments )

        ->hasComments( count($post->comments)>0 )

        ->favorited( $post->fav )

        ->date( $post->datetime )

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