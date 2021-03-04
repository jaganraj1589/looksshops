<?php
class ControllerExtensionModuleBlogArticleTag extends Controller {
	public function index() {
		$this->load->model('extension/blog/blog');
		$this->load->language('extension/module/blog_article_tag');

		$data['tags'] = $this->model_extension_blog_blog->getTags();
		$data['href'] = $this->url->link('extension/blog/blog_list');

		return $this->load->view('extension/module/blog_article_tags', $data);
		
	}

}