<?php

namespace tweeterapp\control;
use tweeterapp\view\TweeterView as TweeterView;

class TweeterAdminController extends \mf\control\AbstractController {

    public function __construct(){
        parent::__construct();
    }

    public function login(){
        $view = new TweeterView();
        $auth = new \tweeterapp\auth\TweeterAuthentification();
        if ($auth->logged_in){
            $auth->logout();
        }
        echo $view->render('login');
    }

    public function signUp(){
        $view = new TweeterView();
        $auth = new \tweeterapp\auth\TweeterAuthentification();
        if ($auth->logged_in){
            $auth->logout();
        }
        echo $view->render('signup');
    }

    public function checkSignUp(){
        if (isset($_POST['fullname']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['password_verify'])){
            if ($_POST['password'] == $_POST['password_verify']){
                $tweet_auth = new \tweeterapp\auth\TweeterAuthentification();
                $tweet_auth->createUser($_POST['username'], $_POST['password'], $_POST['fullname']);
                \mf\router\Router::executeRoute('home');
            }else{
                /* Exception mot de passes non identiques */
                \mf\router\Router::executeRoute('signup');
            }
        }else {
            /* Exception champ manquant */
            \mf\router\Router::executeRoute('signup');
        }
    }

    public function checkLogin(){
        if (isset($_POST['username']) && isset($_POST['password'])){
            $tweet_auth = new \tweeterapp\auth\TweeterAuthentification();
            $tweet_auth->loginUser($_POST['username'], $_POST['password']);
        }
        if ($tweet_auth->logged_in){
            \mf\router\Router::executeRoute('myhome');
        }else{
            \mf\router\Router::executeRoute('login');
        }
    }

    public function viewFollowing(){
        $view = new TweeterView();
        echo $view->render('following');
    }

    public function logout(){
        $tweet_auth = new \tweeterapp\auth\TweeterAuthentification();
        $tweet_auth->logout();
        \mf\router\Router::executeRoute('home');
    }

    public function like(){
        $auth = new \tweeterapp\auth\TweeterAuthentification();
        $user = \tweeterapp\model\User::where('username','=',$auth->user_login)->first(); 
        $tweet_id = $_GET["id"];
        $tweet = \tweeterapp\model\Tweet::where('id','=',$tweet_id)->first();

        $db_like = \tweeterapp\model\Like::where([
            ['tweet_id','=',$tweet_id],
            ['user_id','=',$user->id],
        ])->first();
        $db_dislike = \tweeterapp\model\Dislike::where([
            ['tweet_id','=',$tweet_id],
            ['user_id','=',$user->id],
        ])->first();
        if (!isset($db_like) && !isset($db_dislike) && ($tweet->author != $user->id)){
            /* L'utilisateur n'a ni like ni dislike le tweet et n'est pas le propriétaire du tweet */
            /* On procède donc à un like */
            $tweet->score++;
            $tweet->save();
            $like = new \tweeterapp\model\Like();
            $like->user_id = $user->id;
            $like->tweet_id = $tweet_id;
            $like->save();
        }else if(isset($db_like)){
            /* L'utilisateur a déjà like le tweet, on lui retire son like */
            $tweet->score--;
            $tweet->save();
            $db_like->delete();
        }
        $controller = new TweeterController();
        $controller->viewTweet();
    }

    public function dislike(){
        $auth = new \tweeterapp\auth\TweeterAuthentification();
        $user = \tweeterapp\model\User::where('username','=',$auth->user_login)->first(); 
        $tweet_id = $_GET["id"];
        $tweet = \tweeterapp\model\Tweet::where('id','=',$tweet_id)->first();

        $db_dislike = \tweeterapp\model\Dislike::where([
            ['tweet_id','=',$tweet_id],
            ['user_id','=',$user->id],
        ])->first();
        $db_like = \tweeterapp\model\Like::where([
            ['tweet_id','=',$tweet_id],
            ['user_id','=',$user->id],
        ])->first();
        
        if (!isset($db_like) && !isset($db_dislike) && ($tweet->author != $user->id)){
            /* L'utilisateur n'a ni like ni dislike le tweet et n'est pas le propriétaire du tweet */
            /* On procède donc à un dislike */
            $tweet->score--;
            $tweet->save();
            $dislike = new \tweeterapp\model\Dislike();
            $dislike->user_id = $user->id;
            $dislike->tweet_id = $tweet_id;
            $dislike->save();
        }else if(isset($db_dislike)){
            /* L'utilisateur a déjà dislike le tweet, on lui retire son dislike */
            $tweet->score++;
            $tweet->save();
            $db_dislike->delete();
        }
        $controller = new TweeterController();
        $controller->viewTweet();
    }

    public function follow(){
        $auth = new \tweeterapp\auth\TweeterAuthentification();
        $user = \tweeterapp\model\User::where('username','=',$auth->user_login)->first();
        $following = $user->id;
        $tweet = \tweeterapp\model\Tweet::where('id','=',$_GET["id"])->first();
        $followed = $tweet->author;
        $db_follow = \tweeterapp\model\Follow::where([
            ['follower','=',$following],
            ['followee','=',$followed],
        ])->first();
        $user = \tweeterapp\model\User::where('id','=',$followed)->first();
        if(!isset($db_follow)){
            if ($following != $followed){
                /* L'utilisateur ne follow pas la personne et n'est pas sur son tweet */
                $follow = new \tweeterapp\model\Follow();
                $follow->follower = $following;
                $follow->followee = $followed;
                $follow->save();
                $user->followers++;
                $user->save();
            }
        }else{
            $db_follow->delete();
            $user->followers--;
            $user->save();
        }
        $controller = new TweeterController();
        $controller->viewTweet();
    }

    public function followByProfile(){
        $auth = new \tweeterapp\auth\TweeterAuthentification();
        $user = \tweeterapp\model\User::where('username','=',$auth->user_login)->first();
        $following = $user->id;
        $followed = $_GET["id"];
        $db_follow = \tweeterapp\model\Follow::where([
            ['follower','=',$following],
            ['followee','=',$followed],
        ])->first();
        $user = \tweeterapp\model\User::where('id','=',$followed)->first();
        if(!isset($db_follow)){
            if ($following != $followed){
                /* L'utilisateur ne follow pas la personne et n'est pas sur son tweet */
                $follow = new \tweeterapp\model\Follow();
                $follow->follower = $following;
                $follow->followee = $followed;
                $follow->save();
                $user->followers++;
                $user->save();
            }
        }else{
            $db_follow->delete();
            $user->followers--;
            $user->save();
        }
        $controller = new TweeterController();
        $controller->viewUserTweets();
    }

    public function delete(){
        $auth = new \tweeterapp\auth\TweeterAuthentification();
        $user = \tweeterapp\model\User::where('username','=',$auth->user_login)->first();
        $tweet = \tweeterapp\model\Tweet::where('id','=',$_GET["id"])->first();
        $user_tweet = $tweet->author;
        if ($user_tweet == $user->id){
            $tweet->delete();
            \mf\router\Router::executeRoute('home');
        }else{
            $controller = new TweeterController();
            $controller->viewTweet();
        }
    }

}