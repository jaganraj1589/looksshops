<?php
class ControllerExtensionModuleBlogMgmt extends Controller {
	private $error = array();

	public function index() {
		
		$this->load->language('extension/module/blog_mgmt');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/blog');


		if (isset($this->request->post['module_blog_mgmt_status'])) {
			$data['module_blog_mgmt_status'] = $this->request->post['module_blog_mgmt_status'];
		} else {
			$data['module_blog_mgmt_status'] = $this->config->get('module_blog_mgmt_status');
		}



		if (isset($this->request->get['filter_title'])) {
			$filter_title = $this->request->get['filter_title'];
		} else {
			$filter_title = '';
		}

		if (isset($this->request->get['filter_category'])) {
			$filter_category = $this->request->get['filter_category'];
		} else {
			$filter_category = '';
		}


		if (isset($this->request->get['filter_tag'])) {
			$filter_tag = $this->request->get['filter_tag'];
		} else {
			$filter_tag = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}


		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pd.name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_title'])) {
			$url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . urlencode(html_entity_decode($this->request->get['filter_category'], ENT_QUOTES, 'UTF-8'));
		}


		if (isset($this->request->get['filter_tag'])) {
			$url .= '&filter_tag=' . urlencode(html_entity_decode($this->request->get['filter_tag'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . urlencode(html_entity_decode($this->request->get['filter_status'], ENT_QUOTES, 'UTF-8'));
		}


		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
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
			'text' => $this->language->get('text_blog_list'),
			'href' => $this->url->link('extension/module/blog_mgmt', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['add'] = $this->url->link('extension/module/blog_mgmt/createPost', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['copy'] = $this->url->link('extension/module/blog_mgmt/copyPost', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/module/blog_mgmt/trashPost', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['articles'] = array();

		$limit= 10;
		$start= ($page - 1) * $limit;

		$filter_data = array(
			'filter_title'	  => $filter_title,
			'filter_category' => $filter_category,
			'filter_tag'	  => $filter_tag,
			'filter_status'   => $filter_status,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => $start,
			'limit'           => $limit
		);

		
		$data['count']=$this->model_extension_blog->countArticles($filter_data);
		$data['articles']=$this->model_extension_blog->getArticles($filter_data);
		if( $filter_title == '' && $filter_category =='' && $filter_tag =='' && $filter_status =='' ){
			$totalrecords = $data['count'];
		}
		else{
			$totalrecords = count($data['articles']);

		}

		$data['categories']=$this->model_extension_blog->getCategoriesList();

		$article_info = $data['articles'];
		
		$this->load->model('tool/image');

		foreach ($article_info as $key => $value) {

			if (is_file(DIR_IMAGE . $article_info[$key]['blog_image'])) {
				$article_info[$key]['blog_image'] = $this->model_tool_image->resize($article_info[$key]['blog_image'], 40, 40);
			}
			else {
				$article_info[$key]['blog_image'] = $this->model_tool_image->resize('no_image.png', 40, 40);
			}
		}

		foreach ($article_info as $key => $value) {
			$id = $article_info[$key]['article_id'];

			$tags = array();
			$article_info[$key]['tag'] = $this->model_extension_blog->getArticleTagsList($id);

			foreach ($article_info[$key]['tag'] as $key0 => $value0) {
				array_push($tags, $value0['tag']);
			}

			$article_info[$key]['tag'] = implode(', ', $tags);

			$categories = array();
			$article_info[$key]['name'] = $this->model_extension_blog->getArticleCategoriesList($id);
			
			foreach ($article_info[$key]['name'] as $key1 => $value1) {
				array_push($categories, $value1['name']);
			}
			
			$article_info[$key]['name'] = implode(', ', $categories);			
			
		}

		
		$data['articles']=$article_info;

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}


		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}


		$url = '';

		if (isset($this->request->get['filter_title'])) {
			$url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . urlencode(html_entity_decode($this->request->get['filter_category'], ENT_QUOTES, 'UTF-8'));
		}


		if (isset($this->request->get['filter_tag'])) {
			$url .= '&filter_tag=' . urlencode(html_entity_decode($this->request->get['filter_tag'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . urlencode(html_entity_decode($this->request->get['filter_status'], ENT_QUOTES, 'UTF-8'));
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_title'] = $this->url->link('extension/module/blog_mgmt', 'user_token=' . $this->session->data['user_token'] . '&sort=a.title' . $url, true);
		$data['sort_category'] = $this->url->link('extension/module/blog_mgmt', 'user_token=' . $this->session->data['user_token'] . '&sort=c.name' . $url, true);
		$data['sort_tag'] = $this->url->link('extension/module/blog_mgmt', 'user_token=' . $this->session->data['user_token'] . '&sort=at.tag' . $url, true);
		$data['sort_status'] = $this->url->link('extension/module/blog_mgmt', 'user_token=' . $this->session->data['user_token'] . '&sort=a.status' . $url, true);
		$data['sort_publish'] = $this->url->link('extension/module/blog_mgmt', 'user_token=' . $this->session->data['user_token'] . '&sort=a.created_at' . $url, true);



		$url = '';

		if (isset($this->request->get['filter_title'])) {
			$url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . urlencode(html_entity_decode($this->request->get['filter_category'], ENT_QUOTES, 'UTF-8'));
		}


		if (isset($this->request->get['filter_tag'])) {
			$url .= '&filter_tag=' . urlencode(html_entity_decode($this->request->get['filter_tag'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . urlencode(html_entity_decode($this->request->get['filter_status'], ENT_QUOTES, 'UTF-8'));
		}



		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		// pagination
		$pagination = new Pagination();
		$pagination->total = $totalrecords;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('extension/module/blog_mgmt', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		//current records status
		$data['results'] = sprintf($this->language->get('text_pagination'), ($totalrecords) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($totalrecords - $limit)) ? $totalrecords : ((($page - 1) * $limit) + $limit), $totalrecords, ceil($totalrecords / $limit));

		$data['filter_title'] = $filter_title;
		$data['filter_category'] = $filter_category;
		$data['filter_tag'] = $filter_tag;
		$data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['create_article']= $this->url->link('extension/module/blog_article/index', 'user_token=' . $this->session->data['user_token'], true);

		$data['all_categories'] = $this->url->link('extension/module/blog_mgmt/categories', 'user_token=' . $this->session->data['user_token'], true);
		$data['all_articles'] = $this->url->link('extension/module/blog_mgmt', 'user_token=' . $this->session->data['user_token'], true);
		$data['all_comments'] = $this->url->link('extension/module/blog_mgmt/comments', 'user_token=' . $this->session->data['user_token'], true);


		$data['create_post_url'] = $this->url->link('extension/module/blog_mgmt/createPost', 'user_token=' . $this->session->data['user_token'], true);

		$data['post_edit_url'] = $this->url->link('extension/module/blog_mgmt/editPost', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$data['post_trash_url'] = $this->url->link('extension/module/blog_mgmt/trashPost', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/blog_mgmt', $data));
	}

	public function trashArticles() {
		$this->load->language('extension/module/blog_mgmt');

		$this->document->setTitle($this->language->get('heading_title'));

		// set breadcrumbs
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

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('trash_articles'),
			'href' => $this->url->link('extension/module/blog_mgmt/trashArticles', 'user_token=' . $this->session->data['user_token'], true)
		);


		// load model
		$this->load->model('extension/blog');

		$limit= 10;
		$page=isset($this->request->get['page'])?$this->request->get['page']:1;
		$start= ($page - 1) * $limit;
		
		$data['articles'] = array();

		$totalrecords=$this->model_extension_blog->getTrashArticlesCount();
		$data['articles']=$this->model_extension_blog->getTrashArticles($start, $limit);

		$article_info = $data['articles'];
		
		$this->load->model('tool/image');

		// get article image
		foreach ($article_info as $key => $value) {

			if (is_file(DIR_IMAGE . $article_info[$key]['blog_image'])) {
				$article_info[$key]['blog_image'] = $this->model_tool_image->resize($article_info[$key]['blog_image'], 40, 40);
			}
			else {
				$article_info[$key]['blog_image'] = $this->model_tool_image->resize('no_image.png', 40, 40);
			}
		}

		
		
		foreach ($article_info as $key => $value) {
			$id = $article_info[$key]['article_id'];

			$tags = array();

			// get article tags
			$article_info[$key]['tag'] = $this->model_extension_blog->getArticleTagsList($id);

			foreach ($article_info[$key]['tag'] as $key0 => $value0) {
				array_push($tags, $value0['tag']);
			}

			// make article tags' commma separated string
			$article_info[$key]['tag'] = implode(', ', $tags);

			$categories = array();

			// get article categories
			$article_info[$key]['name'] = $this->model_extension_blog->getArticleCategoriesList($id);
			
			foreach ($article_info[$key]['name'] as $key1 => $value1) {
				array_push($categories, $value1['name']);
			}
			
			// make article categories' comma separated string
			$article_info[$key]['name'] = implode(', ', $categories);			
			
		}

		
		foreach ($article_info as $key => $value) {
			$id = $article_info[$key]['article_id'];

			$tags = array();

			// get article tags
			$article_info[$key]['tag'] = $this->model_extension_blog->getArticleTagsList($id);

			foreach ($article_info[$key]['tag'] as $key0 => $value0) {
				array_push($tags, $value0['tag']);
			}

			// make article tags' commma separated string
			$article_info[$key]['tag'] = implode(', ', $tags);

			$categories = array();

			// get article categories
			$article_info[$key]['name'] = $this->model_extension_blog->getArticleCategoriesList($id);
			
			foreach ($article_info[$key]['name'] as $key1 => $value1) {
				array_push($categories, $value1['name']);
			}
			
			// make article categories' comma separated string
			$article_info[$key]['name'] = implode(', ', $categories);			
			
		}


		$data['articles']=$article_info;

		$data['user_token'] = $this->session->data['user_token'];

		//pagination
			$pagination = new Pagination();
			$pagination->total = $totalrecords;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('extension/module/blog_mgmt/trashArticles', 'user_token=' . $this->session->data['user_token']  . '&page={page}', true);
		$data['pagination'] = $pagination->render();

		//current records status
		$data['results'] = sprintf($this->language->get('text_pagination'), ($totalrecords) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($totalrecords - $limit)) ? $totalrecords : ((($page - 1) * $limit) + $limit), $totalrecords, ceil($totalrecords / $limit));


		


		// load model
		$this->load->model('extension/blog');

		$limit= 10;
		$page=isset($this->request->get['page'])?$this->request->get['page']:1;
		$start= ($page - 1) * $limit;


		
		$data['articles'] = array();

		$totalrecords=$this->model_extension_blog->getTrashArticlesCount();
		$data['articles']=$this->model_extension_blog->getTrashArticles($start, $limit);

		$article_info = $data['articles'];
		
		$this->load->model('tool/image');

		// get article image
		foreach ($article_info as $key => $value) {

			if (is_file(DIR_IMAGE . $article_info[$key]['blog_image'])) {
				$article_info[$key]['blog_image'] = $this->model_tool_image->resize($article_info[$key]['blog_image'], 40, 40);
			}
			else {
				$article_info[$key]['blog_image'] = $this->model_tool_image->resize('no_image.png', 40, 40);
			}
		}

		
		foreach ($article_info as $key => $value) {
			$id = $article_info[$key]['article_id'];

			$tags = array();

			// get article tags
			$article_info[$key]['tag'] = $this->model_extension_blog->getArticleTagsList($id);

			foreach ($article_info[$key]['tag'] as $key0 => $value0) {
				array_push($tags, $value0['tag']);
			}

			// make article tags' commma separated string
			$article_info[$key]['tag'] = implode(', ', $tags);

			$categories = array();

			// get article categories
			$article_info[$key]['name'] = $this->model_extension_blog->getArticleCategoriesList($id);
			
			foreach ($article_info[$key]['name'] as $key1 => $value1) {
				array_push($categories, $value1['name']);
			}
			
			// make article categories' comma separated string
			$article_info[$key]['name'] = implode(', ', $categories);			
			
		}


		$data['articles']=$article_info;

		$data['user_token'] = $this->session->data['user_token'];

		//pagination
			$pagination = new Pagination();
			$pagination->total = $totalrecords;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('extension/module/blog_mgmt/trashArticles', 'user_token=' . $this->session->data['user_token']  . '&page={page}', true);
		$data['pagination'] = $pagination->render();

		//current records status
		$data['results'] = sprintf($this->language->get('text_pagination'), ($totalrecords) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($totalrecords - $limit)) ? $totalrecords : ((($page - 1) * $limit) + $limit), $totalrecords, ceil($totalrecords / $limit));



		
		$data['post_restore_url'] = $this->url->link('extension/module/blog_mgmt/restorePost', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$data['post_delete_url'] = $this->url->link('extension/module/blog_mgmt/deletePost', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$data['empty_trash_url'] = $this->url->link('extension/module/blog_mgmt/emptyTrash', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		

		/* custom codes ends here */

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['navbar'] = $this->load->controller('common/navbar');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/blog_trash_articles', $data));
	}


	public function createPost()
	{	
		$this->load->language('extension/module/blog_mgmt');

		$this->document->setTitle($this->language->get('heading_title'));

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
			'text' => $this->language->get('text_blog_list'),
			'href' => $this->url->link('extension/module/blog_mgmt', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_create_post'),
			'href' => $this->url->link('extension/module/blog_mgmt/createPost', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('tool/image');
		$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$data['cancel'] = $this->url->link('extension/module/blog_mgmt', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$this->load->model('extension/blog');
		$data['categories'] = $this->model_extension_blog->getActiveCategories();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$data['create_article'] = $this->url->link('extension/module/blog_mgmt/savePost', 'user_token=' . $this->session->data['user_token'], true);
		
		$this->response->setOutput($this->load->view('extension/module/blog_create_post' , $data));
	}


	public function savePost()
	{	
		$user_id = $this->session->data['user_id'];	
		$title = $this->request->post['title'];
		$image = $this->request->post['featured_image'];
		$description = $this->request->post['description'];
		$category= $this->request->post['category'];
		$status = $this->request->post['status'];
		$products  = $this->request->post['product'];
		$meta_title= $this->request->post['meta_title'];
		$meta_description= $this->request->post['meta_description'];
		$meta_keywords= $this->request->post['meta_keywords'];
		// add Slug
		$slug = $this->request->post['slug']; 
		$slug = preg_replace('/[^A-Za-z0-9\-]/', '-', $slug); 
		$slug = preg_replace('/-+/', '-', $slug);

		$data = array(
		'user_id'		 	=> $user_id,
		'title' 			=> $title,
		'description' 		=> $description,
		'blog_image'		=> $image,
		'meta_title' 		=>	$meta_title,
		'meta_description' 	=>	$meta_description,
		'meta_keywords' 	=>	$meta_keywords,
		'slug' 				=>	$slug,
		'status'			=> $status
		);

		$this->load->model('extension/blog');

		$data['last_article_id'] = $this->model_extension_blog->savePost($data);
		$last_article_id = $data['last_article_id'];
		
		// save tag
		$tags = $this->request->post['tags'];
		if(!empty($tags)){
			$tagsArray = explode(",", $tags);
			foreach ($tagsArray as $tag) {
					$tagData = array(
						'tag_article_id' => $last_article_id,
						'tag' =>	$tag
					);
	   			$this->model_extension_blog->saveArticleTag($tagData);
			}
		}

		foreach ($category as $category_id) {
			$data1['blog_category_id'] = $category_id;
			$data1['blog_article_id'] = $last_article_id;
			
			$this->model_extension_blog->savePostCategory( $data1);
		}

		foreach ($products as $product_id) {
			$data2['product_id'] = $product_id;
			$data2['article_id'] = $last_article_id;
			
			$this->model_extension_blog->saveArticleProduct($data2);
		}

		$this->response->redirect($this->url->link('extension/module/blog_mgmt', 'user_token=' . $this->session->data['user_token'], true));
	}

	public function editPost()
	{
		$this->load->model('tool/image');
		$this->load->language('extension/module/blog_mgmt');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_blog_mgmt', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_blog_list'),
			'href' => $this->url->link('extension/module/blog_mgmt', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_edit_post'),
			'href' => $this->url->link('extension/module/blog_mgmt/editPost', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['user_token'] = $this->session->data['user_token'];

		$data['action'] = $this->url->link('extension/module/blog_mgmt', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('extension/module/blog_mgmt', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_blog_mgmt_status'])) {
			$data['module_blog_mgmt_status'] = $this->request->post['module_blog_mgmt_status'];
		} else {
			$data['module_blog_mgmt_status'] = $this->config->get('module_blog_mgmt_status');
		}

		/* custom codes starts here */
		
		$get = $this->request->get;
		$id = $get['post_id'];
		$this->load->model('extension/blog');
		$post_data = $this->model_extension_blog->editPost($id);
		$data['status'] = $post_data[0]['status'];
		$data['post'] = $post_data[0];
		$selected_image = $data['post'];

		$data['thumb'] = $this->model_tool_image->resize($selected_image['blog_image'], 1140, 525);

		if(empty($data['thumb'])){
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}	

		$data['post_categories'] = $post_data;
		$data['categories'] = $this->model_extension_blog->getActiveCategories();
		$data['products'] = $this->model_extension_blog->getArticleProducts($id);
		$data['a_tags']  = $this->model_extension_blog->getArticleTagsList($id);
		$data['a_tags'] = json_encode($data['a_tags']);
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$data['action'] = $this->url->link('extension/module/blog_mgmt/updatePost', 'user_token=' . $this->session->data['user_token'], true);
		$this->response->setOutput($this->load->view('extension/module/blog_edit_post' , $data));
	
	
	}


	public function updatePost()
	{	
		
		$id = $this->request->post['id'];
		$title = $this->request->post['title'];
		$description = $this->request->post['description'];
		$products = $this->request->post['product'];
		$category= $this->request->post['category'];
		$image = $this->request->post['featured_image'];
		$meta_title= $this->request->post['meta_title'];
		$meta_description= $this->request->post['meta_description'];
		$meta_keywords= $this->request->post['meta_keywords'];
		$status = $this->request->post['status'];

		$tags = $this->request->post['tags'];
		$tags = str_replace(' ', '', $tags); 


		// add Slug
		$slug= $this->request->post['slug']; 
		$slug = preg_replace('/[^A-Za-z0-9\-]/', '-', $slug); 
		$slug = preg_replace('/-+/', '-', $slug);

		$data = array(
			'title' 			=> $title,
			'description' 		=> $description,
			'blog_image' 		=> $image,
			'meta_title' 		=>	$meta_title,
			'meta_description' 	=>	$meta_description,
			'meta_keywords' 	=>	$meta_keywords,
			'slug' 				=>	$slug,
			'status'			=> $status
		);

		$this->load->model('extension/blog');
		$this->model_extension_blog->deletePostCategory($id);
		$data['post'] = $this->model_extension_blog->updatePost($data,$id);
		
		// delete tag
		$this->model_extension_blog->deleteArticleTags($id);

		if(!empty($tags)){
			$tagsArray = explode(",", $tags);

			foreach ($tagsArray as $tag) {
				$tagData = array(
					'tag_article_id' => $id,
					'tag' =>	$tag
			);

   			$this->model_extension_blog->saveArticleTag($tagData);
		}
		}

		
		foreach ($category as $category_id) {
			$data1['blog_category_id'] = $category_id;
			$data1['blog_article_id'] = $id;
			$this->model_extension_blog->updatePostCategory($data1);
		}

		if(!empty($products)){
			$this->model_extension_blog->deleteArticleProducts($id);
		}

		foreach ($products as $product_id) {
			$data2['product_id'] = $product_id;
			$data2['article_id'] = $id;
			
			$this->model_extension_blog->updateArticleProducts($data2);
		}



		$this->response->redirect($this->url->link('extension/module/blog_mgmt', 'user_token=' . $this->session->data['user_token'], true));
	}

	public function copyPost() {

		$this->load->language('extension/module/blog_mgmt');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/blog');

		if(isset($this->request->post['selected'])  && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $article_id) {
				$this->model_extension_blog->copyPost($article_id);
			}

			$this->session->data['success'] = $this->language->get('copy_success');
		}

		$this->response->redirect($this->url->link('extension/module/blog_mgmt', 'user_token=' . $this->session->data['user_token'], true));
	}


	public function trashPost()
	{
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			$this->load->model('extension/blog');
			$data = array( 
					'status' => 'Trash'
			);
			foreach ($this->request->post['selected'] as $article_id) {
				$this->model_extension_blog->trashPost($article_id, $data);
			}
		}
		$this->response->redirect($this->url->link('extension/module/blog_mgmt', 'user_token=' . $this->session->data['user_token'], true));
	}

	public function restorePost()
	{
		$id = $this->request->get['post_id'];
		$data = array( 
			'status' => 'Draft'
		);
		$this->load->model('extension/blog');
		$this->model_extension_blog->restorePost($id,$data);
		$this->response->redirect($this->url->link('extension/module/blog_mgmt/trashArticles', 'user_token=' . $this->session->data['user_token'], true));
	}

	public function deletePost()
	{
		$get = $this->request->get;
		$id = $get['post_id'];
		$this->load->model('extension/blog');
		$this->model_extension_blog->deletePost($id);
		$this->model_extension_blog->deletePostCategory($id);
		$this->model_extension_blog->deleteArticleTags($id);
		$this->model_extension_blog->deleteArticleComments($id);
		$this->model_extension_blog->deleteArticleProducts($id);
		
		$this->response->redirect($this->url->link('extension/module/blog_mgmt/trashArticles', 'user_token=' . $this->session->data['user_token'], true));

	}

	public function emptyTrash()
	{
		$this->load->language('extension/module/blog_mgmt');
		
		$this->load->model('extension/blog');

		$data['article_id'] = $this->model_extension_blog->getTrashArticlesList();
		if(!empty($data['article_id']))
		{
			$article_id = $data['article_id'];
		
			foreach ($article_id as $id) {
				$this->model_extension_blog->deletePost($id);
				$this->model_extension_blog->deletePostCategory($id);
				$this->model_extension_blog->deleteArticleTags($id);
				$this->model_extension_blog->deleteArticleComments($id);
				$this->model_extension_blog->deleteArticleProducts($id);
			}
		}
		
		$this->response->redirect($this->url->link('extension/module/blog_mgmt/trashArticles', 'user_token=' . $this->session->data['user_token'], true));

	}

	//categories functions
	public function categories(){
		$this->load->language('extension/module/blog_mgmt');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/blog');

		$limit= 10;
		$limit= ($this->config->get('config_limit_admin') !== '') ? $this->config->get('config_limit_admin') : 10;
		$page=isset($this->request->get['page'])?$this->request->get['page']:1;
		
		$start= ($page - 1) * $limit;

		//call to model
		$totalrecords=$this->model_extension_blog->getCategoryTotalCount();
		$data['categories']=$this->model_extension_blog->getCategoriesLists($start, $limit);

		//pagination
		$pagination = new Pagination();
		$pagination->total = $totalrecords;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('extension/module/blog_mgmt/categories', 'user_token=' . $this->session->data['user_token']  . '&page={page}', true);
		$data['pagination'] = $pagination->render();

		//current records status
		$data['results'] = sprintf($this->language->get('text_pagination'), ($totalrecords) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($totalrecords - $limit)) ? $totalrecords : ((($page - 1) * $limit) + $limit), $totalrecords, ceil($totalrecords / $limit));

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
			'text' => $this->language->get('text_blog_categories'),
			'href' => $this->url->link('extension/module/blog_mgmt/categories', 'user_token=' . $this->session->data['user_token'], true)
		);


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$data['category_edit_url'] = $this->url->link('extension/module/blog_mgmt/editCategory', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$data['category_delete_url'] = $this->url->link('extension/module/blog_mgmt/deleteCategory', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		$data['create_category_url'] = $this->url->link('extension/module/blog_mgmt/createCategory', 'user_token=' . $this->session->data['user_token'], true);
		$this->response->setOutput($this->load->view('extension/module/blog_categories' , $data));


	}
	public function createCategory()
	{
		$this->load->language('extension/module/blog_mgmt');

		$this->document->setTitle($this->language->get('heading_title'));

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
			'text' => $this->language->get('text_blog_categories'),
			'href' => $this->url->link('extension/module/blog_mgmt/categories', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_create_category'),
			'href' => $this->url->link('extension/module/blog_mgmt/createCategory', 'user_token=' . $this->session->data['user_token'], true)
		);

		$this->load->model('tool/image');
		$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);


		$data['action'] = $this->url->link('extension/module/blog_mgmt', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('extension/module/blog_mgmt/categories', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$data['save_category'] = $this->url->link('extension/module/blog_mgmt/saveCategory', 'user_token=' . $this->session->data['user_token'], true);
		
		$this->response->setOutput($this->load->view('extension/module/blog_create_category' , $data));
	}


	public function saveCategory()
	{
		$name = $this->request->post['name'];
		$description = $this->request->post['description'];
		$category_image = $this->request->post['featured_image'];
		$meta_title_category= $this->request->post['meta_title'];
		$meta_description_category= $this->request->post['meta_description'];
		$meta_keywords_category= $this->request->post['meta_keywords'];
		$status = $this->request->post['status'];
		// add slug of category
		$slug= $this->request->post['category_slug']; 
		$slug = preg_replace('/[^A-Za-z0-9\-]/', '-', $slug); 
		$slug = preg_replace('/-+/', '-', $slug);

		$slug = strtolower($slug); 
		$data = array(
			'name' 						=> $name,
			'category_description' 		=> $description,
			'category_image' 			=> $category_image,
			'meta_title_category'		=> $meta_title_category,
			'meta_description_category' => $meta_description_category,
			'meta_keywords_category' 	=> $meta_keywords_category,
			'category_slug' 			=> $slug,
			'blog_category_status'		=> $status
		);

		$this->load->model('extension/blog');
		$data['category_last_id'] = $this->model_extension_blog->saveCategory($data);
		$category_last_id = $data['category_last_id'];
		$category_tags = $this->request->post['tags'];

		if(!empty($category_tags)){
			$tagsArray = explode(",", $category_tags);

			foreach ($tagsArray as $tag) {
					$tagData = array(
						'category_id' => $category_last_id,
						'category_tags' =>	$tag
					);

	   			$this->model_extension_blog->saveCategoryTag($tagData);
			}
	
		}
		
		$this->response->redirect($this->url->link('extension/module/blog_mgmt/categories', 'user_token=' . $this->session->data['user_token'], true));
	}

	public function editCategory()
	{
		$this->load->language('extension/module/blog_mgmt');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_blog_mgmt', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_blog_categories'),
			'href' => $this->url->link('extension/module/blog_mgmt/categories', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_edit_category'),
			'href' => $this->url->link('extension/module/blog_mgmt/editCategory', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/blog_mgmt', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('extension/module/blog_mgmt/categories', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_blog_mgmt_status'])) {
			$data['module_blog_mgmt_status'] = $this->request->post['module_blog_mgmt_status'];
		} else {
			$data['module_blog_mgmt_status'] = $this->config->get('module_blog_mgmt_status');
		}
		
		$get = $this->request->get;
		$id = $get['category_id'];
		$this->load->model('extension/blog');
		$category_data = $this->model_extension_blog->editCategory($id);
		$data['status'] = $category_data['blog_category_status'];
		$data['category'] = $category_data;
		$category_image = $data['category'];
		$this->load->model('tool/image');

		$data['thumb'] = $this->model_tool_image->resize($category_image['category_image'], 1140, 525);

		if(empty($data['thumb'])){
		$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}	

		$data['a_tags']  = $this->model_extension_blog->getCategoryTags($id);
		$data['a_tags'] = json_encode($data['a_tags']);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$data['action'] = $this->url->link('extension/module/blog_mgmt/updateCategory', 'user_token=' . $this->session->data['user_token'], true);
		
		$this->response->setOutput($this->load->view('extension/module/blog_edit_category' , $data));
	}


	public function updateCategory()
	{
		$id = $this->request->post['id'];
		$name = $this->request->post['name'];
		$description = $this->request->post['description'];
		$status = $this->request->post['status'];
		$category_image = $this->request->post['featured_image'];
		$meta_title_category= $this->request->post['meta_title'];
		$meta_description_category= $this->request->post['meta_description'];
		$meta_keywords_category= $this->request->post['meta_keywords'];
		$category_slug = $this->request->post['category_slug'];
; 
		$category_slug = preg_replace('/[^A-Za-z0-9\-]/', '-', $category_slug); 
		$category_slug = preg_replace('/-+/', '-', $category_slug);

		$tags = $this->request->post['tags'];
		$tags = str_replace(' ', '', $tags); 
		
		$data = array(
			'name' 						=> $name,
			'category_description' 		=> $description,
			'category_image' 			=> $category_image,
			'meta_title_category' 		=> $meta_title_category,
			'meta_description_category' => $meta_description_category,
			'meta_keywords_category' 	=> $meta_keywords_category,
			'modified_at'	 			=> date("Y-m-d  H:i:s", time()),
			'category_slug' 			=> $category_slug,
			'blog_category_status'      => $status
		);
		$this->load->model('extension/blog');

		// delete tag
		$this->model_extension_blog->deleteCategoryTags($id);
		
		$tagsArray = explode(",", $tags);
		foreach ($tagsArray as $tag) {
				$tagData = array(
					'category_id' => $id,
					'category_tags' =>	$tag
				);
   			$this->model_extension_blog->saveCategoryTag($tagData);
		}
		$data['category'] = $this->model_extension_blog->updateCategory($data,$id);
		
		$this->response->redirect($this->url->link('extension/module/blog_mgmt/categories', 'user_token=' . $this->session->data['user_token'], true));
	}

	public function deleteCategory()
	{
		$get = $this->request->get;
		$id = $get['category_id'];
		$this->load->model('extension/blog');
		$data['category_deleted']=$this->model_extension_blog->deleteCategory($id);
		
		$this->response->redirect($this->url->link('extension/module/blog_mgmt/categories', 'user_token=' . $this->session->data['user_token'], true));

	}

	public function comments(){
		$this->load->language('extension/module/blog_mgmt');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		$this->load->model('extension/blog');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_blog_mgmt', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$limit=10;
		$page=isset($this->request->get['page'])?$this->request->get['page']:1;
		
		$start= ($page - 1) * $limit;

		$totalrecords=$this->model_extension_blog->getCommentsTotalCount();
		
		$data['comments_detail']= $this->model_extension_blog->get_Comments($start, $limit);

		//pagination
			$pagination = new Pagination();
			$pagination->total = $totalrecords;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('extension/module/blog_mgmt/comments', 'user_token=' . $this->session->data['user_token']  . '&page={page}', true);
			
		$data['pagination'] = $pagination->render();

		//current records status
		$data['results'] = sprintf($this->language->get('text_pagination'), ($totalrecords) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($totalrecords - $limit)) ? $totalrecords : ((($page - 1) * $limit) + $limit), $totalrecords, ceil($totalrecords / $limit));



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
			'text' => $this->language->get('text_blog_comments'),
			'href' => $this->url->link('extension/module/blog_mgmt/comments', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/blog_mgmt', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_blog_mgmt_status'])) {
			$data['module_blog_mgmt_status'] = $this->request->post['module_blog_mgmt_status'];
		} else {
			$data['module_blog_mgmt_status'] = $this->config->get('module_blog_mgmt_status');
		}
		// update comments status 
		$data['update_comment_status'] = $this->url->link('extension/module/blog_mgmt/updateCommentStatus&user_token=' . $this->session->data['user_token'], true);

		//update comment 
		$data['update_comment_url'] = $this->url->link('extension/module/blog_mgmt/updateComment', 'user_token=' . $this->session->data['user_token'], true);

		$data['comment_delete_url'] = $this->url->link('extension/module/blog_mgmt/deleteComment', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		//single article 
		$data['article_url'] = HTTP_CATALOG.'index.php?route=extension/blog/article';

		/* custom codes ends here */

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['navbar'] = $this->load->controller('common/navbar');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/blog_articles_comments', $data));
	}

	public function updateCommentStatus(){

			$comment_id = $this->request->post['comment_id'];
			$status = $this->request->post['status'];
			$data = array(
				'status' => $status			
			);

			$this->load->model('extension/blog');
			$data['status'] = $this->model_extension_blog->updateCommentStatus($data, $comment_id);
	}
	
	public function updateComment(){

			$id = $this->request->post['id'];
			$comment = $this->request->post['comment'];

			$data = array(
				'comment' => $comment			
			);
			$this->load->model('extension/blog');
			$data['category'] = $this->model_extension_blog->updateComment($data, $id);

			$this->response->redirect($this->url->link('extension/module/blog_mgmt/comments', 'user_token=' . $this->session->data['user_token'], true));
	}


	public function deleteComment(){
		$comment_id = $this->request->get['comment_id'];
		$this->load->model('extension/blog');
		$this->model_extension_blog->deleteComment($comment_id);
		
		$this->response->redirect($this->url->link('extension/module/blog_mgmt/comments', 'user_token=' . $this->session->data['user_token'], true));

	}
	public function editComment($id)
	{
		$this->load->model('tool/image');
		$this->load->language('extension/module/blog_mgmt');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_blog_mgmt', $this->request->post);

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

		$data['create_article']= $this->url->link('extension/module/blog_article/index', 'user_token=' . $this->session->data['user_token'], true);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/blog_mgmt', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/blog_mgmt', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('extension/module/blog_mgmt', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_blog_mgmt_status'])) {
			$data['module_blog_mgmt_status'] = $this->request->post['module_blog_mgmt_status'];
		} else {
			$data['module_blog_mgmt_status'] = $this->config->get('module_blog_mgmt_status');
		}

		/* custom codes starts here */
		
		$get = $this->request->get;
		$id = $get['comment_id'];
		var_dump($id); exit();
		$this->load->model('extension/blog');
		$post_data = $this->model_extension_blog->editPost($id);
		$data['post'] = $post_data[0];
		$selected_image = $data['post'];

		
		$data['thumb'] = $this->model_tool_image->resize($selected_image['blog_image'], 1140, 525);	
		

		$data['post_categories'] = $post_data;
		$data['categories'] = $this->model_extension_blog->getCategoriesList();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$data['action'] = $this->url->link('extension/module/blog_mgmt/updatePost', 'user_token=' . $this->session->data['user_token'], true);
		$this->response->setOutput($this->load->view('extension/module/blog_edit_post' , $data));
	}



	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('extension/blog');
			

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			
			$start = 0;
			$results = $this->model_extension_blog->searchArticles($filter_name , $start, $limit);
			foreach ($results as $result) {

				$json[] = array(
					'article_id' => $result['article_id'],
					'title'       => strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8')),
					);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function tagAutocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/blog');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			$limit = 5;
			$start = 0;
			$results = $this->model_extension_blog->getAutoTags($filter_name , $start, $limit);
			foreach ($results as $result) {
				$json[] = array(
					'tag_id' => $result['tag_id'],
					'tag'       => strip_tags(html_entity_decode($result['tag'], ENT_QUOTES, 'UTF-8')),
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function validateCopy() {
		if (!$this->user->hasPermission('modify', 'extension/module/blog_mgmt')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/module/blog_mgmt')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function productAutocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('extension/blog');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$start = 0;
			$results = $this->model_extension_blog->getProducts($filter_name , $start, $limit);
			foreach ($results as $result) {
			
				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function getList() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = '';
		}

		if (isset($this->request->get['filter_price'])) {
			$filter_price = $this->request->get['filter_price'];
		} else {
			$filter_price = '';
		}

		if (isset($this->request->get['filter_quantity'])) {
			$filter_quantity = $this->request->get['filter_quantity'];
		} else {
			$filter_quantity = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'a.title';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/product/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['copy'] = $this->url->link('catalog/product/copy', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/product/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['products'] = array();

		$filter_data = array(
			'filter_name'	  => $filter_name,
			'filter_model'	  => $filter_model,
			'filter_price'	  => $filter_price,
			'filter_quantity' => $filter_quantity,
			'filter_status'   => $filter_status,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);

		$this->load->model('tool/image');

		$product_total = $this->model_catalog_product->getTotalProducts($filter_data);

		$results = $this->model_catalog_product->getProducts($filter_data);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$special = false;

			$product_specials = $this->model_catalog_product->getProductSpecials($result['product_id']);

			foreach ($product_specials  as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
					$special = $this->currency->format($product_special['price'], $this->config->get('config_currency'));

					break;
				}
			}

			$data['products'][] = array(
				'product_id' => $result['product_id'],
				'image'      => $image,
				'name'       => $result['name'],
				'model'      => $result['model'],
				'price'      => $this->currency->format($result['price'], $this->config->get('config_currency')),
				'special'    => $special,
				'quantity'   => $result['quantity'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'       => $this->url->link('catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'] . $url, true)
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=a.name' . $url, true);
		$data['sort_model'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.model' . $url, true);
		$data['sort_price'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.price' . $url, true);
		$data['sort_quantity'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.quantity' . $url, true);
		$data['sort_status'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
		$data['sort_order'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_model'] = $filter_model;
		$data['filter_price'] = $filter_price;
		$data['filter_quantity'] = $filter_quantity;
		$data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/product_list', $data));
	}


	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/blog_mgmt')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}