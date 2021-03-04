<?php
class ControllerExtensionModuleBlogAside extends Controller {
	public function index($setting) {

		$this->load->language('extension/module/blog_aside');

		$this->load->model('extension/blog/blog');
		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}
		 $limit = $setting['limit'];
		 $data['articles'] = $this->model_extension_blog_blog->getLatestArticles($limit);
		 array_walk($data['articles'],function(&$key){
			$key['href']=$this->url->link('extension/blog/article','article_id='.$key['article_id']);
		});

		if ($data['articles']) {
			return $this->load->view('extension/module/blog_aside', $data);
		}
	}
}