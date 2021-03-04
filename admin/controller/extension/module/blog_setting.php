<?php

class ControllerExtensionModuleBlogSetting extends Controller
{
    private $error =array();

    public function install() {

        $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."blog_articles` (
          `article_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `title` text NOT NULL,
          `blog_image` text NOT NULL,
          `description` text NOT NULL,
          `meta_title` varchar(40) NOT NULL,
          `meta_description` text NOT NULL,
          `meta_keywords` varchar(40) NOT NULL,
          `slug` varchar(50) NOT NULL,
          `status` enum('Published','Draft','Trash') NOT NULL DEFAULT 'Draft',
          `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `modified_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `user_id` int(11) NOT NULL,
          PRIMARY KEY (`article_id`)
        )");

        $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."blog_article_category` (
          `article_category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `blog_article_id` int(255) NOT NULL,
          `blog_category_id` int(255) NOT NULL,
          PRIMARY KEY (`article_category_id`)
        )");

        $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."blog_article_comments` (
          `comment_id` int(11) NOT NULL AUTO_INCREMENT,
          `comment_article_id` int(255) NOT NULL,
          `comment_parent_id` int(11) NOT NULL,
          `customer_name` varchar(40) NOT NULL,
          `comment` text NOT NULL,
          `status` enum('0','1') NOT NULL DEFAULT '0',
          `comment_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`comment_id`)
        )");

        $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."blog_categories` (
          `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `name` text NOT NULL,
          `category_description` text NOT NULL,
          `category_slug` varchar(50) NOT NULL,
          `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `modified_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `category_image` varchar(50) NOT NULL,
          `meta_title_category` varchar(50) NOT NULL,
          `meta_description_category` varchar(50) NOT NULL,
          `meta_keywords_category` varchar(50) NOT NULL,
          `blog_category_status` enum('Active','InActive') NOT NULL DEFAULT 'Active',
          PRIMARY KEY (`category_id`)
        )");

        $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."article_tags` (
          `tag_id` int(11) NOT NULL AUTO_INCREMENT,
          `tag_article_id` int(11) NOT NULL,
          `tag` text NOT NULL,
          PRIMARY KEY (`tag_id`)
        )");

        $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."article_to_products` (
          `article_product_id` int(11) NOT NULL AUTO_INCREMENT,
          `article_id` int(11) NOT NULL,
          `product_id` int(11) NOT NULL,
          PRIMARY KEY (`article_product_id`)
        )");

        $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."category_tags` (
          `category_tag_id` int(11) NOT NULL AUTO_INCREMENT,
          `category_id` int(11) NOT NULL,
          `category_tags` varchar(20) NOT NULL,
          PRIMARY KEY (`category_tag_id`)
        )");

      if($this->config->get('module_blog_setting_allow_comments')==''){

        $this->db->query("INSERT INTO ".DB_PREFIX."setting SET `key` ='module_blog_setting_allow_comments' , `store_id` = 0 , `code` = 'module_blog_setting', value = '0' ");

       $this->db->query("INSERT INTO ".DB_PREFIX."setting SET `key` ='module_blog_setting_no_of_latest_articles' , `store_id` = 0 , `code` = 'module_blog_setting', value = '10' ");

       $this->db->query("INSERT INTO ".DB_PREFIX."setting SET `key` ='module_blog_setting_no_of_articles' , `store_id` = 0 , `code` = 'module_blog_setting', value = '10' ");
      }
      
    }

    public function index(){

        $this->install();

        $this->load->language('extension/module/blog_setting');
        $this->load->model('setting/setting');

       

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_blog_setting', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_blog_settings'),
            'href' => $this->url->link('extension/module/blog_setting', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/blog_setting', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

         $data['setting_action'] = $this->url->link('extension/module/blog_setting/saveBlogSetting', 'user_token=' . $this->session->data['user_token'], true);

        if (isset($this->request->post['module_blog_setting_status'])) {
            $data['module_blog_setting_status'] = $this->request->post['module_blog_setting_status'];
        } else {
            $data['module_blog_setting_status'] = $this->config->get('module_blog_setting_status');
        }


            $data['no_of_articles'] = $this->config->get('module_blog_setting_no_of_articles');
            $data['no_of_latest_articles'] = $this->config->get('module_blog_setting_no_of_latest_articles');
            $data['allow_comments'] = $this->config->get('module_blog_setting_allow_comments');
        


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/blog_setting', $data));
    }

    public function saveBlogSetting()
    {   

        $this->load->language('extension/module/blog_setting');
        $this->load->model('setting/setting');
        
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $data = array(
                'module_blog_setting_no_of_articles' => $this->request->post['no_of_articles'], 
                'module_blog_setting_no_of_latest_articles' => $this->request->post['no_of_latest_articles'], 
                'module_blog_setting_allow_comments' => $this->request->post['allow_comments'],
                'module_blog_setting_status' => $this->request->post['setting_status']
            );
            $this->model_setting_setting->editSetting('module_blog_setting', $data);
        }
        
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/blog_mgmt', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/blog_setting', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

         $data['setting_action'] = $this->url->link('extension/module/blog_setting/saveBlogSetting', 'user_token=' . $this->session->data['user_token'], true);

        if (isset($this->request->post)) {
            $data['no_of_articles'] = $this->request->post['no_of_articles'];
            $data['no_of_latest_articles'] = $this->request->post['no_of_latest_articles'];
            $data['allow_comments'] = $this->request->post['allow_comments'];
            $data['setting_status'] = $this->request->post['setting_status'];

        } else {
            $data['no_of_articles'] = $this->config->get('module_blog_setting_no_of_articles');
            $data['no_of_latest_articles'] = $this->config->get('module_blog_setting_no_of_latest_articles');
            $data['allow_comments'] = $this->config->get('module_blog_setting_allow_comments');
            $data['setting_status'] = $this->config->get('module_blog_setting_status');

        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/blog_setting', $data));

    }


    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/blog_setting')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}