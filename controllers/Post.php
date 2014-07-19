<?php

class PostController extends Controller {

	public function run() {

        Model::Load('Post');

		$title = Request::GET('title');
        $myPost = Post::GetByTitle($title);

		Template::load('basic')
            ->title($myPost->title)
            ->postPage(true)
            ->headerMenu
            ( 
                false,
                true,
                false,
                false,
                false,
            )
            ->content
            (
                Template::load('post')
                    ->post($myPost)
                    ->comms
                    (
                        "<div id=\"disqus_thread\"></div>
                        <script type=\"text/javascript\">
                            /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
                            var disqus_shortname = 'abstractalgorithm'; // required: replace example with your forum shortname

                            /* * * DON'T EDIT BELOW THIS LINE * * */
                            (function() {
                                var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                                dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
                                (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
                            })();
                        </script>
                        <noscript>Please enable JavaScript to view the <a href=\"http://disqus.com/?ref_noscript\">comments powered by Disqus.</a></noscript>
                        <a href=\"http://disqus.com\" class=\"dsq-brlink\">Comments powered by <span class=\"logo-disqus\">Disqus</span></a>"
                    )
                    ->get()
            )
            
            ->render();
	}
}