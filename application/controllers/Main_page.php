<?php

/**
 * Created by PhpStorm.
 * User: mr.incognito
 * Date: 10.11.2018
 * Time: 21:36
 */
class Main_page extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        App::get_ci()->load->model('User_model');
        App::get_ci()->load->model('Login_model');
        App::get_ci()->load->model('Post_model');
        App::get_ci()->load->model('Boosterpack_model');

        if (is_prod()) {
            die('In production it will be hard to debug! Run as development environment!');
        }
    }

    public function index()
    {
        $user = User_model::get_user();


        App::get_ci()->load->view('main_page', ['user' => User_model::preparation($user, 'default')]);
    }

    public function get_all_posts()
    {
        $posts = Post_model::preparation(Post_model::get_all(), 'main_page');
        return $this->response_success(['posts' => $posts]);
    }

    public function get_post($post_id)
    { // or can be $this->input->post('news_id') , but better for GET REQUEST USE THIS

        $post_id = intval($post_id);

        if (empty($post_id)) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        try {
            $post = new Post_model($post_id);
        } catch (EmeraldModelNoDataException $ex) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
        }


        $posts = Post_model::preparation($post, 'full_info');
        return $this->response_success(['post' => $posts]);
    }


    public function comment($post_id, $message)
    { // or can be App::get_ci()->input->post('news_id') , but better for GET REQUEST USE THIS ( tests )

        if (!User_model::is_logged()) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        $post_id = intval($post_id);

        if (empty($post_id) || empty($message)) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        try {
            $post = new Post_model($post_id);
        } catch (EmeraldModelNoDataException $ex) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
        }


        $posts = Post_model::preparation($post, 'full_info');
        return $this->response_success(['post' => $posts]);
    }


    public function login($user_id)
    {
        // Right now for tests:
        $post_id = intval($user_id);

        if (empty($post_id)){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        // But data from modal window sent by POST request.  App::get_ci()->input...  to get it.


        //Todo: Authorisation

        Login_model::start_session($user_id);

        return $this->response_success(['user' => $user_id]);
    }


    public function logout()
    {
        Login_model::logout();
        redirect(site_url('/'));
    }

    public function add_money()
    {
        $user_id = $this->input->get_post('user_id');
        $amount = (float)$this->input->get_post('amount');

        if (empty($user_id) OR empty($amount)) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        User_model::changeMoney($user_id, $amount, Account_model::TYPE_DEPOSIT);

        return $this->response_success(['amount' => $amount]);
    }

    public function buy_boosterpack($id)
    {
        try {
            $booster = new Boosterpack_model($id);
        } catch (Exception $e) {
            return $this->response_error('Not found boosterpack');
        }

        $user_id = $this->input->get_post('user_id');
        if (empty($user_id)) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        $user = new User_model($user_id);

        if ($user->get_wallet_balance() < $booster->get_price()) {
            return $this->response_error('No money');
        }
        $amount = Boosterpack_model::buy($user_id, $id);

        return $this->response_success(['amount' => $amount]);
    }


    public function like()
    {
        $user_id = $this->input->get_post('user_id');
        if (empty($user_id)) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }
        $user = new User_model($user_id);

        if ($user->get_likes() < 1) {
            return $this->response_error('No likes');
        }

        User_model::like($user_id);

        return $this->response_success(['likes' => rand(1, 55)]); // Колво лайков под постом \ комментарием чтобы обновить
    }

}
