<?php
class ControllerExtensionModuleBlogSearch extends Controller {
	public function index() {

		$this->load->language('extension/module/blog_search');
		$data['search_article_url']= $this->url->link('extension/blog/autocomplete');
		return $this->load->view('extension/module/blog_search',$data);	
		
	}
}