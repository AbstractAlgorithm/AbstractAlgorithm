<?php

require 'models/post.php';

class DashboardController extends Controller {

    public function run() {

        $recent_posts = Post::getLast(10, 'time');

        Template::load('basic')

            ->title('Dashboard')

            ->content( Template::load('home') ->posts($list_posts) ->get() )

        ->render();
    }
}