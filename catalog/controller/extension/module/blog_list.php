<?php
class ControllerExtensionModuleBlogList extends Controller {
public function index(){	
		$this->load->language('extension/module/blog');
		$this->load->model('extension/blog/blog');

		if(isset($this->request->get['category_id'])){
			$id = $this->request->get['category_id'];

			$data['get_categories_meta']= $this->model_extension_blog_blog->getCategoriesMeta($id);
			$info_meta = $data['get_categories_meta'];
			$this->document->setTitle($info_meta['meta_title_category']);
			$this->document->setDescription($info_meta['meta_description_category']);
			$this->document->setKeywords($info_meta['meta_keywords_category']);

			}

		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		if (isset($this->request->get['route'])) {
			$this->document->addLink($this->config->get('config_url'), 'canonical');
		}

		

		$limit = 5;
		$page=isset($this->request->get['page'])?$this->request->get['page']:1;
		$start= ($page - 1) * $limit;
		
		$latest_article_limit = $this->config->get('module_blog_setting_no_of_latest_articles');
		$data['latest_articles'] = $this->model_extension_blog_blog->getLatestArticles($latest_article_limit);
		
		array_walk($data['latest_articles'],function(&$key){
			$key['href']=$this->url->link('extension/blog/article','article_id='.$key['article_id']);
			
		});
		
		if(isset($this->request->get['filter_search']) || isset($this->request->get['tag']) || isset($this->request->get['category_id']) || isset($this->request->get['category_name']) )
		{
			if(isset($this->request->get['filter_search'])){
				$filter = $this->request->get['filter_search'];
				$data['articles'] = $this->model_extension_blog_blog->filterArticles($filter, $start, $limit);
				$count = $this->model_extension_blog_blog->searchCountSearch($filter)[0]['count'];
				$totalrecords = $count;
			}
			
			if(isset($this->request->get['tag'])){
				$filter = $this->request->get['tag'];
				$data['articles'] = $this->model_extension_blog_blog->getArticlesByTags($filter, $start, $limit);
				$count = $this->model_extension_blog_blog->countTags($filter)[0]['count'];
				$totalrecords = $count;
			}

			if(isset($this->request->get['category_id'])){
				$id = $this->request->get['category_id'];
				$data['category_name'] = $this->model_extension_blog_blog->getCategoryName($id);
				$filter = $data['category_name'];
				$data['articles'] = $this->model_extension_blog_blog->articlesByCategory($id, $start, $limit);
				$count = $this->model_extension_blog_blog->countCategory($id)[0]['count'];
				$totalrecords = $count;

			}

			if(empty($data['articles'])){
				$data['noRecord']= '<h3> No result found for '.'"'.$filter.'"'.'</h3>';
				$totalrecords = 0;
			}
			
			$description = $data['articles'];
			foreach ($description as $key => $value) {
				$description[$key]['description'] = html_entity_decode($description[$key]['description']);
				}
				$data['articles'] = $description ;
			}
		else{

			$data['articles']=$this->model_extension_blog_blog->getArticle($start, $limit);
			$description = $data['articles'];
			foreach ($description as $key => $value) {
			$description[$key]['description'] = strip_tags(html_entity_decode($description[$key]['description']));
			}
			$data['articles'] = $description ;

			$totalrecords= $this->model_extension_blog_blog->getArticlesTotalCount();
		}

		$this->load->model('tool/image');
		
		$article_info = $data['articles'];

		foreach ($article_info as $key => $value) {
			$article_info[$key]['blog_image'] = $this->model_tool_image->resize($article_info[$key]['blog_image'], 828, 348);
		}

		$data['articles'] = $article_info;

		array_walk($data['articles'],function(&$key){
			$key['href']=$this->url->link('extension/blog/article','article_id='.$key['article_id']);
			
		});

		if($totalrecords !=0 ){
		$pagination = new Pagination();
		$pagination->total = $totalrecords;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('extension/blog/blog_list', '&page={page}', true);
		$data['pagination'] = $pagination->render();

		
		$data['results'] = sprintf($this->language->get('text_pagination'), ($totalrecords) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($totalrecords - $limit)) ? $totalrecords : ((($page - 1) * $limit) + $limit), $totalrecords, ceil($totalrecords / $limit));
		}

		$data['search_article_url']= $this->url->link('extension/blog/autocomplete');
		$data['search_article_url2']= $this->url->link('extension/blog/blog_list');
		$data['get_single_article']= $this->url->link('extension/blog/article');

		return $this->load->view('extension/module/module_blog_list', $data);	


		}

	}


	