<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use App\Models\User;

class PruebasController extends Controller
{
    public function testOrm(){

        $posts = Post::all();
        $categories = Category::all();
        // var_dump($posts);
        foreach($posts as $post){
            echo "<span>".$post->user->name."</span>";
            echo $post->category->name."<br>";
            // var_dump($post);
            // var_dump($post);
        }

        foreach($categories as $category){
            echo "<span>".$category->name."</span>";
            foreach($category->posts as $post){
                echo "<span>".$post->title."</span>";
            }

        }


        die();
    }
}
// https://github.com/ionut-botizan/docker-nginx-php-xdebug