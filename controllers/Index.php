<?php

class Tekst
{
    public $tekst;
    public function __construct($str)
    {
        $this->tekst = $str;
    }
    public function deo($broj, $dodatak, $extra)
    {
        return substr($this->tekst, 0, $broj).$dodatak.($extra?'eksta':'<==3');
    }
}

class Blogpost
{
    public $tekst;
    public $naslov;
    public $datum;
    public $komentari;
    public $glasovi;

    public static $posts = array();
    public static $ime = 'Blogpost';
    public static $config = array( 'table' => 'post', 'rel'=>'boom');

    public function __construct($na, $te, $da)
    {
        $this->naslov = $na;
        $this->tekst = $te;
        $this->datum = $da;
        $this->glasovi = rand(0, 5);
        $this->komentari = array();
        self::$posts[] = $this;
    }

    public function addComm($co)
    {
        $this->komentari[] = $co;
        return $this;
    }

    public static function recent($num)
    {
        $res = array();
        $n = count(self::$posts);
        for($i=0; $i<$num && $i<$n; $i++)
            $res[] = self::$posts[$n-1-$i];
        return $res;
    }

    public static function all()
    {
        return self::$posts;
    }
}

class IndexController extends Controller {

    public function run()
    {
        $post1 = new Blogpost("High dynamic range rendering HDR(R)", "
            <p>
            There are few effects that play major roles in turning a plastic and non-realistic scene into a lifelike, dynamic and visually appealing. All these effects mimic some of the physical phenomenons from the nature, therefore bringing realism into the games. HDR imagery is a vital part of a realistic representation of the scene. It provides much better and color-dynamic scenes, and combined with tone mapping it can bring certain atmosphere into the environment.
            </p>

            <h2>Physics:</h2>
            <p>The nature itself provides much greater range of colors than [0,255] supported by our screens. Although the human eye is capable of distinguishing around 10M colors, which is significantly smaller than 255<sup>3</sup> = 16.5M, our eye has the ability to adjust itself based on the light present in the nearby area. Longer exposure in the dark rooms reveals new details because eye started adjusting itself and picking as many light as it possibly can trying to recognize certain shapes that might help us coordinate ourselves and get out of that situation.</p>
            
            <h2>Solution:</h2>
            <p>To achieve the effect of wide range of colors, artists use something known as HDR images. Photographers take shots of the same image under different contrast and exposure level which, when combined together, represent a single HDR (high-dynamic range) image. There are different formats of HDR image, but they usually end up using floats for representation of the certain color channel. So instead of limiting one channel to a 0-255 integer range, devs use HDR images that store channels as float values that have much greater range of values. However, in order to show the image on the screen, those float values must be scaled somehow to the 0-255 range. That's the part that <strong>tone mapping</strong> does.</p>
            
            <p>
            So tone mapping (as somewhere implied by its name) does a job of scaling the high-range color values to the low-range values (0-255) so the colors could be produced on the screen. To do this, tone mapping implements algorithm similar to this:
            </p>

            <ul>
                <li>find lowest (L) and highest (H) value on the visible part o the image (darkest and brightest)</li>
                <li>apply tone mapping formula</li>
                <li>output color</li>
            </ul>

<h2>1. Finding lowest and highest value</h2>
<p>
There are quite a few approaches how to do this step. No matter how you do it, the point is to sample scene's colorbuffer image at every pixel (if not every, than big percent of them) and to find minimum and maximum values. Probably the best way to do it - use compute shaders. They're built for things exactly like this, so you'll get best performance and you can use counter which may be beneficial. [For versions of API that do not support compute shaders, you might render some very simple primitive (point perhaps) on a 2x1 framebuffer and just use its fragment shader to sample colorbuffer of the scene]
</p>

<h2>2. Apply tone mapping formula</h2>

<p>There are also plenty of formulas to try for tone mapping:</p>
<ul>
    <li>linear</li>
    <li>exponential</li>
    <li>logarithmic</li>
    <li>filmic</li>
    <li>adaptive</li>
</ul>

<p>
Any way you go, the point is to scale the values between L->H, down to 0->255 range.
For example, linear tone mapping would look like this:
</p>

<p>
    <code>
    var outputColor = (inputColor - L)/(H - L) * 255;
    </code>
</p>

<p>
Tone mapping really helps bringing the sense of reality into the scene, so choosing good formula plays a big role in the artistic process.
</p>

<p>
Tone mapping can also be combined with some additional color effect, so putting more blue color into the final output could represent a more metal, industrial, cold and modern feel, while adding more orange could lead to more artistic, more fantasy/fairytale look, something that assembles greater Sun presence and warmth. Of course, tone mapping can be separated with color FX, and is probably better that way so you could dynamically change any additional per-pixel adjustments to represent different environmental feels and have greater control over the process.
</p>

<h2>3. Output color</h2>

<p>Well, do your final magic.</p>

<hr>

<p>That is, in short, an algorithm used for tone mapping. For now, you should have the knowledge to tell what is HDR, what is tone mapping, why and how it can be used.</p>

<p>There is also a line on Wikipedia, on the HDR article, that really confused me when I first read it: bright things can be really bright, dark things can be really dark, and details can be seen in both.</p>

<p>By now, you should understand what previous sentence meant. When you enter the area that is quite dark, the tone mapping would do it's job and scale the values to the 0-255 range, so you retrieve the details that were perhaps lost in the full image which has both very dark and very bright areas. Similarly, when entering very bright area, tone mapping would also scale colors on that part, yet again revealing you all the details.</p>", "11. jun, 2014");
        $post1->addComm("Kul prvi post.");
        $post1->addComm("Stvarno do jaja.");

        $post2 = new Blogpost("2nd post", "Ovo je drugi post.", '8. oktobar, 2013');
        $post2->addComm("Losiji tekst");
        $post2->addComm("slabo");
        $post2->addComm("bedno");

        $post3 = new Blogpost("Treca sreca", "Lorem ipusm dolor sit amet.", '7. jul, 2014');
        $blogpostovi = array();
        $blogpostovi[] = $post1;
        $blogpostovi[] = $post2;
        $blogpostovi[] = $post3;

        $proba = array();
        $proba[] = 'prvi';
        $proba[] = 'drugi';

        Template::load('basic')

        ->title("Blog")

        ->css('new_style.css')

        ->content
        (
            Template::load('test2')

            ->tekstic('superman')

            ->postovi($blogpostovi)

            ->uslov(true)

            ->televizor($proba)

            ->get()
        )

        ->render();

    }

    public static function kapitalizuj($str)
    {
        return ucfirst($str).'*^&%*$';
    }
}