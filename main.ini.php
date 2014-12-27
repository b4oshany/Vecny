<?php
# define package usage
use libs\vecni\http\Response;
use libs\vecni\http\Request;
use libs\vecni\Vecni as app;
use controller\user\User;

User::start_session();

$less = app::use_less();
Response::init();

// Set the default title of website.
app::$twig->addGlobal("title", app::$BRAND_NAME);
if(User::is_login()){
    app::$twig->addGlobal("user", User::get_current_user());
}


app::set_route("/", "welcome");
app::set_route("/home", "welcome");/**
* Render the welcome page.
* @example http://exmaple.com/
* @exmaple http://exmaple.com/home.
*/
function welcome(){
    if(User::is_login()){
        return app::$twig->render("demo/vecni_docs.html",
                    array(
                        "html_class"=>"welcome"
                    ));
    }else{
        return app::$twig->render('demo/vecni_docs.html',
                      array(
                        "html_class"=>"welcome",
                        "title"=>app::$BRAND_NAME
                      )
                  );
    }
}


app::set_route("/user/signin", "signin_require");
/**
* Render the sign in page for users
* @example http://vecni.com/user/signin
*/
function signin_require($message=""){
    return app::$twig->render('user_signin.html',
              array(
                "html_class"=>"signin",
                "title"=>"Signin Required",
                "message"=>$message
              )
          );
}


app::set_route("/user/signin/process", "process_login");
/**
* Process the user signin.
* @example http://vecni.com/user/sigin/process
*/
function process_login(){
    if(!empty($_POST['email']) && !empty($_POST['password'])){
        $email = $_POST['email'];
        $pass = $_POST['password'];
        $status = User::login($email, $pass);
        if(Request::is_async()){
            if($status){
                return Response::json_response(200, $email);
            }else{
                return Response::abort("$email, does not exists in our system. Please register for account if you don't have one");
            }
        }else{
            if($status){
                return app::nav_back();
            }else{
                return signin_require();
            }
        }
    }
}



app::set_route("/user/registration", "reg_request");
/**
* Render the registration page.
* @example http://vecni.com/user/registration
*/
function reg_request($message=""){
    if(User::is_login()){
        app::redirect();
    }
    return app::$twig->render('user_registration.html',
                        array("html_class"=>"user-registration",
                             "title"=>"Registration",
                             )
                        );
}

app::set_route("/user/registration/process", "register");
/**
* Process the user registration.
* @example http://vecni.com/user/sigin/process
*/
function register(){
    global $user;
    if(($first_name = Request::POST('first_name')) &&
       ($last_name =  Request::POST('last_name')) &&
       ($password = Request::POST('password')) &&
       ($email = Request::POST('email'))){
        $new_user = new User();
        $new_user->first_name = $first_name;
        $new_user->last_name = $last_name;
        if($dob = Request::POST('dob')){
            $new_user->dob  = $dob;
        }else{
            $new_user->dob = "0000-00-00";
        }
        $new_user->gender = Request::POST('gender', "other");
        $status = $new_user->register($email, $password);
        if(Request::is_async()){
            if($status){
                return Response::json_response(200, $email);
            }else{
                return Response::abort("This accound has already been registered");
            }
        }else{
            if($status){
                app::redirect();
            }else{
                app::redirect();
            }
        }
    }
}

app::set_route("/user/facebook/login", "login_with_social_network");
app::set_route("/user/googleplus/login", "login_with_social_network");
app::set_route("/user/twitter/login", "login_with_social_network");
/**
* Process the user sigin or registration via social netowrk.
* @example http://vecni.com/user/facebook/login
* @example http://vecni.com/user/googleplus/login
* @exmaple http://vecni.com/user/twitter/login
*/
function login_with_social_network(){
    global $user;
    if(User::is_login()){
        app::redirect();
    }
    if(!empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['social_network']) && !empty($_POST['social_network_id']) && !empty($_POST['email'])){
        $new_user = new User();
        $new_user->first_name = $_POST['first_name'];
        $new_user->last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $new_user->dob  = DateTime::createFromFormat('m/d/Y',
                                           $_POST['dob']);
        $new_user->gender = $_POST['gender'];
        if(!empty($_POST['school'])){
            $new_user->school = $_POST['school'];
        }
        $account_type = $_POST['social_network'];
        $account_id = $_POST['social_network_id'];
        $status = $new_user->login_with_social_network($email, $account_type, $account_id);
        if($status){
            return Response::json_response(200, $email);
        }else{
            return Response::json_response(204, "Something went wrong");
        }
    }
}


app::set_route("/logout", "log_out");
/**
* Log out users out of the system.
* @example http://vecni.com/logout
*/
function log_out(){
    if(User::is_login()){
        User::log_out();
        app::$twig->addGlobal("user", new User());
    }
    app::redirect();
}

?>


