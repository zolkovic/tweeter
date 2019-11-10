<?php
session_start();

/* pour le chargement automatique des classes d'Eloquent (dans le répertoire vendor) */
use mf\utils\ClassLoader as Loader;
use mf\router\Router as Router;

require_once 'src/mf/utils/ClassLoader.php';
require_once 'vendor/autoload.php';

$loader = new Loader('src');
$loader->register();

$config = parse_ini_file('./conf/config.ini');

/* une instance de connexion  */
$db = new Illuminate\Database\Capsule\Manager();

$db->addConnection( $config ); /* configuration avec nos paramètres */
$db->setAsGlobal();            /* rendre la connexion visible dans tout le projet */
$db->bootEloquent();           /* établir la connexion */

$router = new Router();

$router->addRoute('home', '/home/', '\tweeterapp\control\TweeterController', 'viewHome');
$router->addRoute('view', '/view/', '\tweeterapp\control\TweeterController', 'viewTweet');
$router->addRoute('user', '/user/', '\tweeterapp\control\TweeterController', 'viewUserTweets');
$router->addRoute('post', '/post/', '\tweeterapp\control\TweeterController', 'viewTweetForm', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);
$router->addRoute('send', '/send/', '\tweeterapp\control\TweeterController', 'sendTweet', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);
$router->addRoute('signup', '/signup/', '\tweeterapp\control\TweeterAdminController', 'signUp');
$router->addRoute('login', '/login/', '\tweeterapp\control\TweeterAdminController', 'login');
$router->addRoute('logout', '/logout/', '\tweeterapp\control\TweeterAdminController', 'logout', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);
$router->addRoute('check_signup', '/check_signup/', '\tweeterapp\control\TweeterAdminController', 'checkSignUp');
$router->addRoute('check_login', '/check_login/', '\tweeterapp\control\TweeterAdminController', 'checkLogin');
$router->addRoute('following', '/following/', '\tweeterapp\control\TweeterAdminController', 'viewFollowing', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);
$router->addRoute('like', '/like/', '\tweeterapp\control\TweeterAdminController', 'like', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);
$router->addRoute('dislike', '/dislike/', '\tweeterapp\control\TweeterAdminController', 'dislike', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);
$router->addRoute('follow', '/follow/', '\tweeterapp\control\TweeterAdminController', 'follow', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);
$router->addRoute('delete', '/delete/', '\tweeterapp\control\TweeterAdminController', 'delete', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);
$router->addRoute('myhome', '/myhome/', '\tweeterapp\control\TweeterController', 'viewMyHome', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);
$router->addRoute('profile', '/profile/', '\tweeterapp\control\TweeterController', 'viewProfile', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);
$router->addRoute('followers', '/followers/', '\tweeterapp\control\TweeterController', 'viewFollowers', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);
$router->addRoute('liked', '/liked/', '\tweeterapp\control\TweeterController', 'viewLiked', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);
$router->addRoute('dashboard', '/dashboard/', '\tweeterapp\control\TweeterController', 'viewDashboard', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_CORP);
$router->addRoute('dashboardfollowers', '/dashboard/followers/', '\tweeterapp\control\TweeterController', 'viewDashboardFollowers', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_CORP);
$router->addRoute('followbyprofile', '/followbyprofile/', '\tweeterapp\control\TweeterAdminController', 'followByProfile', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);


$router->setDefaultRoute('/home/');

$router->run();