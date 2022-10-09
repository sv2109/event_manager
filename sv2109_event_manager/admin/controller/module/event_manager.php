<?php
/*
 * @author     sv2109
 * @copyright  2022
 * @link       http://sv2109.com
 */

namespace Opencart\Admin\Controller\Extension\Sv2109EventManager\Module;

class EventManager extends \Opencart\System\Engine\Controller {

	private $route_extension     = 'extension/sv2109_event_manager/module/event_manager';
	private $event_code          = 'module_sv2109_event_manager';

	// route separator
	// see @link https://github.com/opencart/opencart/issues/11594 for more details
	private $rs;

	public function __construct($registry) {
		parent::__construct($registry);

		if (version_compare(VERSION, '4.0.2.0', '<')) {
			$this->rs = "|";
		} else {
			$this->rs = ".";;
		}
	}

	public function index(): void {
		$this->load->language('extension/sv2109_event_manager/module/event_manager');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/sv2109_event_manager/module/event_manager', 'user_token=' . $this->session->data['user_token'])
		];

		$data['save'] = $this->url->link('extension/sv2109_event_manager/module/event_manager' . $this->rs . 'save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');

		$data['module_event_manager_status'] = $this->config->get('module_event_manager_status');

		if (isset($this->request->post['module_event_manager_options'])) {
			$data['options'] = $this->request->post['module_event_manager_options'];
		} elseif ($this->config->get('module_event_manager_options')) {
			$data['options'] = $this->config->get('module_event_manager_options');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/sv2109_event_manager/module/event_manager', $data));
	}

	public function save(): void {
		$this->load->language('extension/sv2109_event_manager/module/event_manager');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/sv2109_event_manager/module/event_manager')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('module_event_manager', $this->request->post);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function install(): void {
		//Events
		$this->load->model('setting/event');

		$events = [
			[
				'code' => $this->event_code,
				'description' => '',
				'trigger' => 'admin/view/common/column_left/before',
				'action' => $this->route_extension . $this->rs . 'eventAdminViewCommonColumnLeftBefore',
				'status' => true,
				'sort_order' => 0,
			],
		];

		foreach ($events as $event) {
			if (version_compare(VERSION, '4.0.1.0', '>=')) {
				$this->model_setting_event->addEvent($event);
			} else {
				$this->model_setting_event->addEvent($event['code'], $event['description'], $event['trigger'], $event['action']);
			}
		}
	}

	public function eventAdminViewCommonColumnLeftBefore(string &$route, array &$data, string &$code): void {

		$options = $this->config->get('module_event_manager_options');

		if ($this->config->get('module_event_manager_status') && !empty($options['menu_link'])) {

			$this->load->language('extension/sv2109_event_manager/marketplace/event_menu');

			$menu_link = [
				'name' => $this->language->get('text_event_manager'),
				'href' => $this->url->link('extension/sv2109_event_manager/marketplace/event', 'user_token=' . $this->session->data['user_token']),
				'children' => []
			];

			foreach ($data['menus'] as &$menu) {
				$children = [];
				foreach ($menu['children'] as $child) {
					if (strpos($child['href'], 'route=marketplace/event') !== false) {
						if ($options['menu_link'] == 'replace') {
							$child = $menu_link;
						} else {
							$children[] = $menu_link;
						}
					}
					$children[] = $child;
				}
				$menu['children'] = $children;
			}
		}
	}
}
