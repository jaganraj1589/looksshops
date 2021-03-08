<?php
class ControllerExtensionModuleBlogFeaturedVertical extends Controller {
	public function index($setting) {

		$this->load->language('extension/module/blog_featured_vertical');

		$this->load->model('extension/blog/blog');

		

		$data['articles'] = array();

		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}


		if (!empty($setting['article'])) {
			$articles = array_slice($setting['article'], 0, (int)$setting['limit']);

			foreach ($articles as $article_id) {
				$article_info = $this->model_extension_blog_blog->getArticleData($article_id);

				if ($article_info) {
					
					$data['articles'][] = array(
						'article_id'  => $article_info['article_id'],
						'title'        => $article_info['title'],
						'href'        => $this->url->link('extension/blog/article', 'article_id=' . $article_info['article_id'])
					);
				}
			}
		}



		if ($data['articles']) {
			return $this->load->view('extension/module/blog_featured_vertical', $data);
		}
	}
}