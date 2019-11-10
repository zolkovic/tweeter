<?php

namespace tweeterapp\control;
use tweeterapp\view\TweeterView as TweeterView;

/* Classe TweeterController :
 *  
 * Réalise les algorithmes des fonctionnalités suivantes: 
 *
 *  - afficher la liste des Tweets 
 *  - afficher un Tweet
 *  - afficher les tweet d'un utilisateur 
 *  - afficher la le formulaire pour poster un Tweet
 *  - afficher la liste des utilisateurs suivis 
 *  - évaluer un Tweet
 *  - suivre un utilisateur
 *   
 */

class TweeterController extends \mf\control\AbstractController {
    
    public function __construct(){
        parent::__construct();
    }
    
    public function viewHome(){
        if (isset($_GET["page"])){
            $page_id = $_GET["page"];
        }else{
            $page_id = 0;
        }
        $view = new TweeterView($page_id);
        echo $view->render('home');
    }
    
    public function viewTweet(){
        $tweet_id = $_GET["id"];
        $tweet = \tweeterapp\model\Tweet::where('id','=',$tweet_id)->first();
        $view = new TweeterView($tweet);
        echo $view->render('tweet');
    }
    
    public function viewUserTweets(){
        $user_id = $_GET["id"];
        if (isset($_GET["page"])){
            $page_id = $_GET["page"];
        }else{
            $page_id = 0;
        }
        $auteur = \tweeterapp\model\User::where('id','=',$user_id)->first();
        $data[0] = $auteur;
        $data[1] = $page_id;
        $view = new TweeterView($data);
        echo $view->render('userTweets');
    }

    public function viewTweetForm() {
        $view = new TweeterView();
        echo $view->render('nouveauTweet');
    }

    public function sendTweet() {
        $auth = new \tweeterapp\auth\TweeterAuthentification();
        $user = \tweeterapp\model\User::where('username','=',$auth->user_login)->first();      
        $tweet = new \tweeterapp\model\Tweet();
        $postTweet = filter_var($_POST['text'], FILTER_SANITIZE_SPECIAL_CHARS);
        $tweet->text = $postTweet;
        $tweet->author = $user->id;
        $tweet->save();
        \mf\router\Router::executeRoute('home');
    }

    public function viewMyHome(){
        if (isset($_GET["page"])){
            $page_id = $_GET["page"];
        }else{
            $page_id = 0;
        }
        $view = new TweeterView($page_id);
        echo $view->render('myhome');
    }

    public function viewProfile(){
        if (isset($_GET["page"])){
            $page_id = $_GET["page"];
        }else{
            $page_id = 0;
        }
        $view = new TweeterView($page_id);
        echo $view->render('profile');
    }

    public function viewFollowers(){
        $view = new TweeterView();
        echo $view->render('followers');
    }

    public function viewLiked(){
        if (isset($_GET["page"])){
            $page_id = $_GET["page"];
        }else{
            $page_id = 0;
        }
        $view = new TweeterView($page_id);
        echo $view->render('liked');
    }

    public function viewDashboard(){
        if (isset($_GET["infpage"])){
            $data[0] = $_GET["infpage"];
        }else{
            $data[0] = 0;
        }
        if (isset($_GET["spherepage"])){
            $data[1] = $_GET["spherepage"];
        }else{
            $data[1] = 0;
        }
        $view = new TweeterView($data);
        echo $view->render('dashboard');
    }

    public function viewDashboardFollowers(){
        $follower_id = $_GET["id"];
        $view = new TweeterView($follower_id);
        echo $view->render('dashboardfollowers');
    }
}
