<?php

namespace tweeterapp\view;
use mf\router\Router as Router;
use tweeterapp\model\User as User;
use mf\auth\Authentification as Authentification;


class TweeterView extends \mf\view\AbstractView {

    const TWEETS_PER_PAGE  = 7;
    const INFLUENCERS_PER_PAGE = 3;
  
    /* Constructeur 
    *
    * Appelle le constructeur de la classe parent
    */
    public function __construct( $data = []){
        parent::__construct($data);
    }

    /*************************************************************
     * Render du header
     *************************************************************/

    private function renderHeader(){
        return '<header class="theme-backcolor1">
                    <h1>Touiteur</h1>'.$this->renderTopMenu().
                '</header>';
    }

    /*************************************************************
     * Render du footer
     *************************************************************/
    
    private function renderFooter(){
        return '<footer class="theme-backcolor1">Touiteur &copy;2019 by <b>Zolkovic</b></footer>';
    }

    /*************************************************************
     * Render des derniers tweets (accueil)
     *************************************************************/
    
    private function renderHome(){
        $tweets = \tweeterapp\model\Tweet::all();
        $app_root = (new \mf\utils\HttpRequest())->root;
        $html = '';
        $page_id = $this->data;
        $start = (count($tweets)-1)-$page_id*self::TWEETS_PER_PAGE;
        $end = (count($tweets)-1)-($page_id*self::TWEETS_PER_PAGE+self::TWEETS_PER_PAGE);
        for($i=$start; $i>$end; $i--){
            if (isset($tweets[$i])){
                $auteur = User::where('id','=',$tweets[$i]->author)->first();
                $html .= '<div class="tweet">
                            <a href="'.\mf\router\Router::urlFor('view',["id" => $tweets[$i]->id]).'">
                                <div class="tweet-text">'.$tweets[$i]->text.'</div>
                            </a>
                            <div class="tweet-footer">
                                <span class="tweet-timestamp">'.$tweets[$i]->created_at.'</span>
                                <a href="'.Router::urlFor('user',["id" => $tweets[$i]->author]).'">
                                    <span class="tweet-author">'.$auteur->fullname.'</span>
                                </a>
                            </div>
                        </div>';
            }
        }
        $html .= '<div class="pager">';
        if($page_id==0){
            $html .= '<div id="page-prev"></div>';
        }else{
            $html .= '<a id="page-prev" class="tweet-control" href="'.Router::urlFor('home', ["page" => $page_id-1]).'">
                        <img alt=Previous page" src="'.$app_root.'/html/previous.png">
                    </a>';
        }
        $nb_pages = floor((count($tweets)-1)/self::TWEETS_PER_PAGE);
        if ($nb_pages > 1){
            if ((count($tweets) % $nb_pages)==0){
                $nb_pages--;
            }
        }
        if($page_id<$nb_pages){
            $html .=    '<a id="page-next" class="tweet-control" href="'.Router::urlFor('home', ["page" => $page_id+1]).'">
                            <img alt=Next page" src="'.$app_root.'/html/next.png">
                        </a>';  
        }else{
            $html .= '<div id="page-next"></div>';
        }

        return '<h2>Latest Tweets</h2>'.$html;     
    }

    /*************************************************************
     * Render des tweets d'un user en particulier
     *************************************************************/
     
    private function renderUserTweets(){
        $app_root = (new \mf\utils\HttpRequest())->root;
        $auteur = $this->data[0];
        $liste_tweets = $auteur->tweets()->get();
        $entete_html = '<h2>Tweets de '.$auteur->fullname.'</h2>
                        <h3>@'.$auteur->username.'</h3>
                        <h3>'.$auteur->followers.' followers   ';
        $auth = new \tweeterapp\auth\TweeterAuthentification();
        $user = \tweeterapp\model\User::where('username','=',$auth->user_login)->first();
        $db_follow = \tweeterapp\model\Follow::where([
            ['follower','=',$user->id],
            ['followee','=',$auteur->id],
        ])->first();
        if ($user->id != $auteur->id){
            $entete_html .= '<a class="tweet-control" href="'.Router::urlFor('followbyprofile', ["id" => $auteur->id]).'">';
            if(isset($db_follow)){
                $entete_html .= '<img alt="Follow" src="'.$app_root.'/html/follow-done.png">';
            }else{
                $entete_html .= '<img alt="Follow" src="'.$app_root.'/html/follow.png">';
            }
            $entete_html .= '</a></h3>';
        }
        $html = '';
        $page_id = $this->data[1];
        $start = (count($liste_tweets)-1)-$page_id*self::TWEETS_PER_PAGE;
        $end = (count($liste_tweets)-1)-($page_id*self::TWEETS_PER_PAGE+self::TWEETS_PER_PAGE);
        for($i=$start; $i>$end; $i--){
            if (isset($liste_tweets[$i])){
                $auteur = User::where('id','=',$liste_tweets[$i]->author)->first();
                $html .= '<div class="tweet">
                            <a href="'.\mf\router\Router::urlFor('view',["id" => $liste_tweets[$i]->id]).'">
                                <div class="tweet-text">'.$liste_tweets[$i]->text.'</div>
                            </a>
                            <div class="tweet-footer">
                                <span class="tweet-timestamp">'.$liste_tweets[$i]->created_at.'</span>
                                <a href="'.Router::urlFor('user',["id" => $liste_tweets[$i]->author]).'">
                                    <span class="tweet-author">'.$auteur->fullname.'</span>
                                </a>
                            </div>
                        </div>';
            }
        }
        $html .= '<div class="pager">';
        if($page_id==0){
            $html .= '<div id="page-prev"></div>';
        }else{
            $html .= '<a id="page-prev" class="tweet-control" href="'.Router::urlFor('user', ["id" => $auteur->id,"page" => $page_id-1]).'">
                        <img alt=Previous page" src="'.$app_root.'/html/previous.png">
                    </a>';
        }
        $nb_pages = floor((count($liste_tweets)-1)/self::TWEETS_PER_PAGE);
        if ($nb_pages > 1){
            if ((count($liste_tweets) % $nb_pages)==0){
                $nb_pages--;
            }
        }
        if($page_id<$nb_pages){
            $html .=    '<a id="page-next" class="tweet-control" href="'.Router::urlFor('user', ["id" => $auteur->id,"page" => $page_id+1]).'">
                            <img alt=Next page" src="'.$app_root.'/html/next.png">
                        </a>';  
        }else{
            $html .= '<div id="page-next"></div>';
        }
        return $entete_html.$html;
    }

    /*************************************************************
     * Render d'un tweet
     *************************************************************/
    
    private function renderViewTweet(){
        $auteur = User::where('id','=',$this->data->author)->first();
        $app_root = (new \mf\utils\HttpRequest())->root;
        $auth = new \tweeterapp\auth\TweeterAuthentification();
        $html = '<div class="tweet">
                    <div class="tweet-text">'.$this->data->text.'</div>
                    <div class="tweet-footer">
                        <span class="tweet-timestamp">'.$this->data->created_at.'</span>
                        <span class="tweet-author">
                            <a href="'.Router::urlFor('user',["id" => $auteur->id]).'">'.
                                $auteur->fullname.'
                            </a>
                        </span>
                    </div>
                    <div class="tweet-footer">
                        <hr><span class="tweet-score tweet-control">'.$this->data->score.'</span>';
        if ($auth->logged_in){
            $user = \tweeterapp\model\User::where('username','=',$auth->user_login)->first();
            if ($user->id != $auteur->id){
                $html .= '<a class="tweet-control" href="'.Router::urlFor('like', ["id" => $this->data->id]).'">';
                $db_like = \tweeterapp\model\Like::where([
                    ['tweet_id','=',$this->data->id],
                    ['user_id','=',$user->id],
                ])->first();
                if(isset($db_like)){
                    /* L'utilisateur like le tweet, pouce vert */
                    $html .= '<img alt="Like" src="'.$app_root.'/html/like-done.png">';
                }else{
                    $html .= '<img alt="Like" src="'.$app_root.'/html/like.png">';
                }
                $html .=    '</a>
                                <a class="tweet-control" href="'.Router::urlFor('dislike', ["id" => $this->data->id]).'">';
                $db_dislike = \tweeterapp\model\Dislike::where([
                    ['tweet_id','=',$this->data->id],
                    ['user_id','=',$user->id],
                ])->first();
                if(isset($db_dislike)){
                    /* L'utilisateur dislike le tweet, pouce rouge */
                    $html .= '<img alt="Like" src="'.$app_root.'/html/dislike-done.png">';
                }else{
                    $html .= '<img alt="Like" src="'.$app_root.'/html/dislike.png">';
                }
                $html .=    '</a>
                            <a class="tweet-control" href="'.Router::urlFor('follow', ["id" => $this->data->id]).'">';
                
                $db_follow = \tweeterapp\model\Follow::where([
                    ['follower','=',$user->id],
                    ['followee','=',$this->data->author],
                ])->first();
                if(isset($db_follow)){
                    $html .= '<img alt="Follow" src="'.$app_root.'/html/follow-done.png">';
                }else{
                    $html .= '<img alt="Follow" src="'.$app_root.'/html/follow.png">';
                }
                $html .= '</a>';
            }else{
                /* L'utilisateur connecté est le propriétaire du tweet, on lui donne accès à modif et suppr */
                $html .=    '<a class="tweet-control" href="'.Router::urlFor('delete', ["id" => $this->data->id]).'">
                                <img alt="Delete" src="'.$app_root.'/html/trash.png">
                            </a>';
            }
        }
        return $html.'</div></div>';
    }

    /*************************************************************
     * Render du formulaire de post d'un tweet
     *************************************************************/

    protected function renderPostTweet(){
        return '<form action="'.Router::urlFor('send').'" method="post">
                    <textarea placeholder="Écrivez votre tweet..." id="tweet-form" name="text", maxlength="150"></textarea>
                    <div>
                        <input id="send_button" type="submit" name="send" value="Envoyer">
                    </div>
                </form>';      
    }

    /*************************************************************
     * Render du formulaire d'inscription
     *************************************************************/

    protected function renderSignUp(){
        return '<form class="forms" action="'.Router::urlFor('check_signup').'" method="post">
                    <input class="forms-text" type="text" name="fullname" placeholder="Nom complet">
                    <input class="forms-text" type="text" name="username" placeholder="Pseudo">
                    <input class="forms-text" type="password" name="password" placeholder="Mot de passe">
                    <input class="forms-text" type="password" name="password_verify" placeholder="Retape mot de passe">
                    <button class="forms-button" name="login_button" type="submit">Créer son compte</button>
                </form>';
    }

    /*************************************************************
     * Render du fomulaire de connexion
     *************************************************************/

    protected function renderLogin(){
        return '<form class="forms" action="'.Router::urlFor('check_login').'" method="post">
                    <input class="forms-text" type="text" name="username" placeholder="Pseudo">
                    <input class="forms-text" type="password" name="password" placeholder="Mot de passe">
                    <button class="forms-button" name="login_button" type="submit">Se connecter</button>
                </form>';
    }

    /*************************************************************
     * Render des personnes suivies
     *************************************************************/

    protected function renderFollowing() {
        $auth = new \tweeterapp\auth\TweeterAuthentification();
        $html = '<h2>Liste des utilisateurs suivis</h2><ul id="followees">';
        $user =  User::where('username','=',$auth->user_login)->first();
        $liste_following = $user->follows()->get();
        foreach($liste_following as $l) {
            $html .= '  <li>
                            <div class="tweet">
                                <div class="tweet-text"><a href="'.Router::urlFor('user',["id" => $l->id]).'">'.$l->fullname.'</a></div>
                            </div>
                        </li>';
        }
        $html .= '</ul>';
        return $html;
    }

    /*************************************************************
     * Render du menu bas selon si connecté ou non
     *************************************************************/

    protected function renderBottomMenu(){
        return '<nav id="menu" class="theme-backcolor1">
                    <div id="nav-menu">
                        <div class="button theme-backcolor2">
                            <a href="'.Router::urlFor('post').'">Nouveau Tweet</a>
                        </div>
                    </div>
                </nav>';
    }

    /*************************************************************
     * Render du menu haut selon si connecté ou non
     *************************************************************/

    protected function renderTopMenu(){
        $app_root = (new \mf\utils\HttpRequest())->root;
        $auth = new \tweeterapp\auth\TweeterAuthentification();
        $html = '<nav id="navbar">
                    <a class="tweet-control" href="'.Router::urlFor('home').'">
                        <img alt="home" src="'.$app_root.'/html/home.png">
                    </a>';
        if ($auth->access_level > \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER){
            $html .=    '<a class="tweet-control" href="'.Router::urlFor('dashboard').'">
                            <img alt="home" src="'.$app_root.'/html/corp.png">
                        </a>';
        }
        if ($auth->logged_in){
            $html .=    '<a class="tweet-control" href="'.Router::urlFor('myhome').'">
                            <img alt="home" src="'.$app_root.'/html/antenne.png">
                        </a>
                        <a class="tweet-control" href="'.Router::urlFor('profile').'">
                            <img alt="home" src="'.$app_root.'/html/followees.png">
                        </a>
                        <a class="tweet-control" href="'.Router::urlFor('logout').'">
                            <img alt="home" src="'.$app_root.'/html/login.png">
                        </a>';
        }else{
            $html .=    '<a class="tweet-control" href="'.Router::urlFor('login').'">
                            <img alt="home" src="'.$app_root.'/html/login.png">
                        </a>
                        <a class="tweet-control" href="'.Router::urlFor('signup').'">
                            <img alt="signup" src="'.$app_root.'/html/signup.png">
                        </a>';
        }

        return $html.'</nav>';
    }

    /*************************************************************
     * Render de sa page personnelle
     *************************************************************/

    protected function renderMyHome(){
        $app_root = (new \mf\utils\HttpRequest())->root;
        $auth = new \tweeterapp\auth\TweeterAuthentification();
        $user =  User::where('username','=',$auth->user_login)->first();
        $tweets = \tweeterapp\model\Tweet::join('follow','tweet.author','=','follow.followee')
                                            ->select('tweet.*')
                                            ->where('follow.follower','=',$user->id)
                                            ->orderBy('tweet.created_at')
                                            ->get();
        $html = '';
        $page_id = $this->data;
        $start = (count($tweets)-1)-$page_id*self::TWEETS_PER_PAGE;
        $end = (count($tweets)-1)-($page_id*self::TWEETS_PER_PAGE+self::TWEETS_PER_PAGE);
        for($i=$start; $i>$end; $i--){
            if (isset($tweets[$i])){
                $auteur = User::where('id','=',$tweets[$i]->author)->first();
                $html .= '<div class="tweet">
                            <a href="'.\mf\router\Router::urlFor('view',["id" => $tweets[$i]->id]).'">
                                <div class="tweet-text">'.$tweets[$i]->text.'</div>
                            </a>
                            <div class="tweet-footer">
                                <span class="tweet-timestamp">'.$tweets[$i]->created_at.'</span>
                                <a href="'.Router::urlFor('user',["id" => $tweets[$i]->author]).'">
                                    <span class="tweet-author">'.$auteur->fullname.'</span>
                                </a>
                            </div>
                        </div>';
            }
        }
        $html .= '<div class="pager">';
        if($page_id==0){
            $html .= '<div id="page-prev"></div>';
        }else{
            $html .= '<a id="page-prev" class="tweet-control" href="'.Router::urlFor('myhome', ["page" => $page_id-1]).'">
                        <img alt=Previous page" src="'.$app_root.'/html/previous.png">
                    </a>';
        }
        $nb_pages = floor((count($tweets)-1)/self::TWEETS_PER_PAGE);
        if ($nb_pages > 1){
            if ((count($tweets) % $nb_pages)==0){
                $nb_pages--;
            }
        }
        if($page_id<$nb_pages){
            $html .=    '<a id="page-next" class="tweet-control" href="'.Router::urlFor('myhome', ["page" => $page_id+1]).'">
                            <img alt=Next page" src="'.$app_root.'/html/next.png">
                        </a>';  
        }else{
            $html .= '<div id="page-next"></div>';
        }

        return '<h2>My Tweet List</h2>'.$html;
    }

    /*************************************************************
     * Render de son propre profil
     *************************************************************/

    protected function renderProfile(){
        $auth = new \tweeterapp\auth\TweeterAuthentification();
        $app_root = (new \mf\utils\HttpRequest())->root;
        $user =  User::where('username','=',$auth->user_login)->first();
        $liste_following = $user->follows()->get();
        $liste_liked = $user->liked()->get();
        $entete_html =  '<div class="tweet">
                            <h2>Mon profil : '.$user->fullname.'<br><br>@'.$user->username.'</h2>
                            <div class="tweet-footer"><hr>
                                <a class="tweet-control" href="'.Router::urlFor('followers').'">
                                        <span class="tweet-score tweet-control">'.$user->followers.'</span> followers
                                </a>
                                <a class="tweet-control" href="'.Router::urlFor('following').'">
                                        <span class="tweet-score tweet-control">'.count($liste_following).'</span> suivis
                                </a>
                                <a class="tweet-control" href="'.Router::urlFor('liked').'">
                                        <span class="tweet-score tweet-control">'.count($liste_liked).'</span> likes
                                </a>
                            </div>
                        </div>';


        $liste_tweets = $user->tweets()->get();
        $html = '';
        $page_id = $this->data;
        $start = (count($liste_tweets)-1)-$page_id*self::TWEETS_PER_PAGE;
        $end = (count($liste_tweets)-1)-($page_id*self::TWEETS_PER_PAGE+self::TWEETS_PER_PAGE);
        for($i=$start; $i>$end; $i--){
            if (isset($liste_tweets[$i])){
                $html .= '<div class="tweet">
                            <a href="'.\mf\router\Router::urlFor('view',["id" => $liste_tweets[$i]->id]).'">
                                <div class="tweet-text">'.$liste_tweets[$i]->text.'</div>
                            </a>
                            <div class="tweet-footer">
                                <span class="tweet-timestamp">'.$liste_tweets[$i]->created_at.'</span>
                                <span class="tweet-author">'.$user->fullname.'</span>
                            </div>
                        </div>';
            }
        }
        $html .= '<div class="pager">';
        if($page_id==0){
            $html .= '<div id="page-prev"></div>';
        }else{
            $html .= '<a id="page-prev" class="tweet-control" href="'.Router::urlFor('profile', ["page" => $page_id-1]).'">
                        <img alt=Previous page" src="'.$app_root.'/html/previous.png">
                    </a>';
        }
        $nb_pages = floor((count($liste_tweets)-1)/self::TWEETS_PER_PAGE);
        if ($nb_pages > 1){
            if ((count($liste_tweets) % $nb_pages)==0){
                $nb_pages--;
            }
        }
        if($page_id<$nb_pages){
            $html .=    '<a id="page-next" class="tweet-control" href="'.Router::urlFor('profile', ["page" => $page_id+1]).'">
                            <img alt=Next page" src="'.$app_root.'/html/next.png">
                        </a>';  
        }else{
            $html .= '<div id="page-next"></div>';
        }
        return $entete_html.$html;
    }

    /*************************************************************
     * Render des followers
     *************************************************************/

    protected function renderFollowers(){
        $auth = new \tweeterapp\auth\TweeterAuthentification();
        $html = '<h2>Liste des utilisateurs qui me suivent</h2><ul id="followees">';
        $user =  User::where('username','=',$auth->user_login)->first();
        $liste_followers = $user->followedBy()->get();
        foreach($liste_followers as $l) {
            $html .= '  <li>
                            <div class="tweet">
                                <div class="tweet-text"><a href="'.Router::urlFor('user',["id" => $l->id]).'">'.$l->fullname.'</a></div>
                            </div>
                        </li>';
        }
        $html .= '</ul>';
        return $html;
    }

    /*************************************************************
     * Render des tweets likés
     *************************************************************/

    protected function renderLiked(){
        $app_root = (new \mf\utils\HttpRequest())->root;
        $auth = new \tweeterapp\auth\TweeterAuthentification();
        $user =  User::where('username','=',$auth->user_login)->first();
        $tweets = $user->liked()->get();
        $html = '';
        $page_id = $this->data;
        $start = (count($tweets)-1)-$page_id*self::TWEETS_PER_PAGE;
        $end = (count($tweets)-1)-($page_id*self::TWEETS_PER_PAGE+self::TWEETS_PER_PAGE);
        for($i=$start; $i>$end; $i--){
            if (isset($tweets[$i])){
                $auteur = User::where('id','=',$tweets[$i]->author)->first();
                $html .= '<div class="tweet">
                            <a href="'.\mf\router\Router::urlFor('view',["id" => $tweets[$i]->id]).'">
                                <div class="tweet-text">'.$tweets[$i]->text.'</div>
                            </a>
                            <div class="tweet-footer">
                                <span class="tweet-timestamp">'.$tweets[$i]->created_at.'</span>
                                <a href="'.Router::urlFor('user',["id" => $tweets[$i]->author]).'">
                                    <span class="tweet-author">'.$auteur->fullname.'</span>
                                </a>
                            </div>
                        </div>';
            }
        }
        $html .= '<div class="pager">';
        if($page_id==0){
            $html .= '<div id="page-prev"></div>';
        }else{
            $html .= '<a id="page-prev" class="tweet-control" href="'.Router::urlFor('myhome', ["page" => $page_id-1]).'">
                        <img alt=Previous page" src="'.$app_root.'/html/previous.png">
                    </a>';
        }
        $nb_pages = floor((count($tweets)-1)/self::TWEETS_PER_PAGE);
        if ($nb_pages > 1){
            if ((count($tweets) % $nb_pages)==0){
                $nb_pages--;
            }
        }
        if($page_id<$nb_pages){
            $html .=    '<a id="page-next" class="tweet-control" href="'.Router::urlFor('myhome', ["page" => $page_id+1]).'">
                            <img alt=Next page" src="'.$app_root.'/html/next.png">
                        </a>';  
        }else{
            $html .= '<div id="page-next"></div>';
        }

        return '<h2>My Liked Tweet List</h2>'.$html;
    }

    /*************************************************************
     * Render du tableau de bord Corp
     *************************************************************/

    protected function renderDashboard(){
        $app_root = (new \mf\utils\HttpRequest())->root;
        $users_list = User::select('user.*')
                            ->orderBy('user.followers')
                            ->get();

        $inf_page_id = $this->data[0];
        $inf_start = (count($users_list)-1)-$inf_page_id*self::INFLUENCERS_PER_PAGE;
        $inf_end = (count($users_list)-1)-($inf_page_id*self::INFLUENCERS_PER_PAGE+self::INFLUENCERS_PER_PAGE);

        $html = '<h2>Liste des influenceurs directs</h2><ul id="followees">';
        for($i=$inf_start; $i>$inf_end; $i--){
            if (isset($users_list[$i])){
                $html .= '  <li>
                                <div class="tweet">
                                    <div class="tweet-text"><a href="'.Router::urlFor('user',["id" => $users_list[$i]->id]).'">'.$users_list[$i]->fullname.'</a></div>
                                    <hr>
                                    <a class="tweet-control" href="'.Router::urlFor('dashboardfollowers',["id" => $users_list[$i]->id]).'">
                                            <span class="tweet-score tweet-control">'.$users_list[$i]->followers.'</span> followers
                                    </a>
                                </div>
                            </li>';
            }
        }

        $html .= '<div class="pager">';
        if($inf_page_id==0){
            $html .= '<div id="page-prev"></div>';
        }else{
            $html .= '<a id="page-prev" class="tweet-control" href="'.Router::urlFor('dashboard', ["infpage" => $inf_page_id-1, "spherepage" => $this->data[1]]).'">
                        <img alt=Previous page" src="'.$app_root.'/html/previous.png">
                    </a>';
        }
        $nb_pages = floor((count($users_list)-1)/self::INFLUENCERS_PER_PAGE);

        if($inf_page_id<$nb_pages){
            $html .=    '<a id="page-next" class="tweet-control" href="'.Router::urlFor('dashboard', ["infpage" => $inf_page_id+1, "spherepage" => $this->data[1]]).'">
                            <img alt=Next page" src="'.$app_root.'/html/next.png">
                        </a>';  
        }else{
            $html .= '<div id="page-next"></div>';
        }

        /*****************************************************
         * Partie sur la sphère d'influenceurs
         *****************************************************/

        $users = User::all();
        $users_list = [];
        $array = [];
        foreach ($users as $user) {
            $array[$user->id] = $this->sphere($user->id);
        }
        asort($array);
        foreach ($array as $user_id => $sphere) {
            $us = User::where('id','=',$user_id)->first();
            array_push($users_list, $us);
        }

        $sphere_page_id = $this->data[1];
        $sphere_start = (count($users_list)-1)-$sphere_page_id*self::INFLUENCERS_PER_PAGE;
        $sphere_end = (count($users_list)-1)-($sphere_page_id*self::INFLUENCERS_PER_PAGE+self::INFLUENCERS_PER_PAGE);


        $html .= '</ul><h2>Liste des influenceurs par leur sphère</h2><ul id="followees">';
        for($i=$sphere_start; $i>$sphere_end; $i--){
            if (isset($users_list[$i])){
                $html .= '  <li>
                                <div class="tweet">
                                    <div class="tweet-text"><a href="'.Router::urlFor('user',["id" => $users_list[$i]->id]).'">'.$users_list[$i]->fullname.'</a></div>
                                    <hr>
                                    <a href="'.Router::urlFor('dashboardfollowers',["id" => $users_list[$i]->id]).'">
                                        <span class="tweet-score tweet-control">'.$array[$users_list[$i]->id].'</span> membres dans la sphère
                                    </a>
                                </div>
                            </li>';
            }
        }

        $html .= '<div class="pager">';
        if($sphere_page_id==0){
            $html .= '<div id="page-prev"></div>';
        }else{
            $html .= '<a id="page-prev" class="tweet-control" href="'.Router::urlFor('dashboard', ["infpage" => $this->data[0], "spherepage" => $sphere_page_id-1]).'">
                        <img alt=Previous page" src="'.$app_root.'/html/previous.png">
                    </a>';
        }
        $nb_pages = floor((count($users_list)-1)/self::INFLUENCERS_PER_PAGE);

        if($sphere_page_id<$nb_pages){
            $html .=    '<a id="page-next" class="tweet-control" href="'.Router::urlFor('dashboard', ["infpage" => $this->data[0], "spherepage" => $sphere_page_id+1]).'">
                            <img alt=Next page" src="'.$app_root.'/html/next.png">
                        </a>';  
        }else{
            $html .= '<div id="page-next"></div>';
        }

        $html .= '</ul>';

        return $html;
    }

    /*************************************************************
     * Render des followers dans le dashboard
     *************************************************************/

    protected function renderDashboardFollowers(){
        echo $this->sphere($this->data);
        $user =  User::where('id','=',$this->data)->first();
        $html = '<h2>Liste des utilisateurs qui suivent '.$user->fullname.'</h2><ul id="followees">';
        $liste_followers = $user->followedBy()->get();
        foreach($liste_followers as $follower) {
            $html .= '  <li>
                            <div class="tweet">
                                <div class="tweet-text"><a href="'.Router::urlFor('user',["id" => $follower->id]).'">'.$follower->fullname.'</a></div>
                            </div>
                        </li>';
        }
        $html .= '</ul>';
        return $html;
    }

    protected function sphere($user_id){
        $follows = \tweeterapp\model\Follow::select('follow.*')
                                            ->where('follow.followee','=',$user_id)
                                            ->get();
        $sphere = [];
        return count($this->countFollowers($user_id, $sphere));
    }

    protected function countFollowers($user_id, $sphere){
        $follows = \tweeterapp\model\Follow::select('follow.*')
                                            ->where('follow.followee','=',$user_id)
                                            ->get();
        foreach ($follows as $follower) {
            if (!in_array($follower->follower, $sphere)){
                array_push($sphere, $follower->follower);
                $sphere = $this->countFollowers($follower->follower, $sphere);
            }
        }
        return $sphere;
    }
    
    /*************************************************************
     * Render global du body qui renvoie vers le bon render
     *************************************************************/
    protected function renderBody($selector){
        $html = $this->renderHeader();
        $auth = new \tweeterapp\auth\TweeterAuthentification();
        $html .= '<section><article class="theme-backcolor2">';
        switch($selector){
            case "tweet":
                $html .= $this->renderViewTweet();
                break;
            case "home":
                $html .= $this->renderHome();
                break;
            case "userTweets":
                $html .= $this->renderUserTweets();
                break;
            case "nouveauTweet":
                $html .= $this->renderPostTweet();
                break;
            case "signup":
                $html .= $this->renderSignUp();
                break;
            case "login":
                $html .= $this->renderLogin();
                break;
            case "following":
                $html .= $this->renderFollowing();
                break;
            case "myhome":
                $html .= $this->renderMyHome();
                break;
            case "profile":
                $html .= $this->renderProfile();
                break;
            case "followers":
                $html .= $this->renderFollowers();
                break;
            case "liked":
                $html .= $this->renderLiked();
                break;
            case "dashboard":
                $html .= $this->renderDashboard();
                break;
            case "dashboardfollowers":
                $html .= $this->renderDashboardFollowers();
                break;
        }
        $html .= '</article>';
        $html .= $auth->logged_in ? $this->renderBottomMenu() : '';
        $html .= '</section>';
        $html .= $this->renderFooter();
        return $html;      
    }
}