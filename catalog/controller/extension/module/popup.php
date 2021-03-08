<?php
class ControllerExtensionModulePopup extends Controller {

	public function index($setting = null) {
		$this->load->language('extension/module/popup');

		if ($setting && $setting['status']) {
				$show_modal = false;
				$data = array();
				
				

				if (!isset($this->session->data['module_popup_pass'])) {
					$show_modal = true;
				}

				if ($show_modal) {
					$data['name'] = $setting['name'];
					$data['message'] = $setting['message'];
					$data['age'] = $setting['age'];
					$data['redirect_url'] = $setting['redirect_url'];
					$data['session_redirect'] = $this->url->link('extension/module/popup/startPopupSession');
	
					return $this->load->view('extension/module/popup', $data);
				}
		}
	}

	public function startPopupSession() { //ajax
		$this->session->data['module_popup_pass'] = $this->request->post['age'];

		if (isset ($this->session->data['module_popup_pass'] )) {
			if ($this->request->post['age'] > $this->session->data['module_popup_pass']) {
				$this->session->data['module_popup_pass'] = $this->request->get['age'] ;
			} 
		} else {
			$this->session->data['module_popup_pass'] = $this->request->post['age'];
		}

		$data = array();
		$data['success'] = true;
		$this->response->setOutput($this->load->view('extension/module/popup_session', $data));
	}
	
}