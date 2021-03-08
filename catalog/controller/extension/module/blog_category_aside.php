<?php
class ControllerExtensionModuleBlogCategoryAside extends Controller {
	public function index() {

		$this->load->language('extension/module/blog_category_aside');
		$this->load->model('extension/blog/blog');

		 $data['categories'] = $this->model_extension_blog_blog->getArticlesCategories();
		 array_walk($data['categories'],function(&$key){
			$key['href']=$this->url->link('extension/blog/blog_list','category_id='.$key['category_id']);
		});

		if ($data['categories']) {
			return $this->load->view('extension/module/blog_category_aside', $data);	
		}
	}
}