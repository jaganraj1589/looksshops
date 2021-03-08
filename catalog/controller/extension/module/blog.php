<?php
class ControllerExtensionModuleBlog extends Controller {
	public function index($setting) {

		$this->load->language('extension/module/blog');

		$this->load->model('extension/blog/blog');

		$this->load->model('tool/image');

		$data['articles'] = array();

		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}


		if (!empty($setting['article'])) {
			$articles = array_slice($setting['article'], 0, (int)$setting['limit']);

			foreach ($articles as $article_id) {
				$article_info = $this->model_extension_blog_blog->getArticleData($article_id);

				if ($article_info) {
					if ($article_info['blog_image']) {
						$image = $this->model_tool_image->resize($article_info['blog_image'], $setting['width'], $setting['height']);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
					}

					$data['articles'][] = array(
						'article_id'  => $article_info['article_id'],
						'thumb'       => $image,
						'title'        => $article_info['title'],
						'description' => utf8_substr(strip_tags(html_entity_decode($article_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
						'href'        => $this->url->link('extension/blog/article', 'article_id=' . $article_info['article_id'])
					);
				}
			}
		}


		if ($data['articles']) {
			return $this->load->view('extension/module/blog', $data);
		}
	}
}