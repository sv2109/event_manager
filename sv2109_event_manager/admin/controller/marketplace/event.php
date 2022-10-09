<?php
/*
 * @author     sv2109
 * @copyright  2022
 * @link       http://sv2109.com
 */

namespace Opencart\Admin\Controller\Extension\Sv2109EventManager\Marketplace;

class Event extends \Opencart\System\Engine\Controller {

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
		$this->load->language('marketplace/event');
		$this->load->language('extension/sv2109_event_manager/marketplace/event');

		$this->document->setTitle($this->language->get('heading_title'));

		$url = '';

		if (isset($this->request->get['filter_code'])) {
			$url .= '&filter_code=' . urlencode(html_entity_decode($this->request->get['filter_code'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_trigger'])) {
			$url .= '&filter_trigger=' . urlencode(html_entity_decode($this->request->get['filter_trigger'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_action'])) {
			$url .= '&filter_action=' . $this->request->get['filter_action'];
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

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/sv2109_event_manager/marketplace/event', 'user_token=' . $this->session->data['user_token'] . $url)
		];

		$data['delete'] = $this->url->link('extension/sv2109_event_manager/marketplace/event' . $this->rs . 'delete', 'user_token=' . $this->session->data['user_token']);

		$data['save'] = $this->url->link('extension/sv2109_event_manager/marketplace/event' . $this->rs . 'save', 'user_token=' . $this->session->data['user_token']);
		$data['list_action'] = $this->url->link('extension/sv2109_event_manager/marketplace/event' . $this->rs . 'list', 'user_token=' . $this->session->data['user_token']);

		$data['list'] = $this->getList();

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/sv2109_event_manager/marketplace/event', $data));
	}

	public function list(): void {
    $this->load->language('marketplace/event');
		$this->load->language('extension/sv2109_event_manager/marketplace/event');

		$this->response->setOutput($this->getList());
	}

	public function getList(): string {

		if (isset($this->request->get['filter_code'])) {
			$filter_code = $this->request->get['filter_code'];
		} else {
			$filter_code = '';
		}

		if (isset($this->request->get['filter_trigger'])) {
			$filter_trigger = $this->request->get['filter_trigger'];
		} else {
			$filter_trigger = '';
		}

		if (isset($this->request->get['filter_action'])) {
			$filter_action = $this->request->get['filter_action'];
		} else {
			$filter_action = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'code';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_code'])) {
			$url .= '&filter_code=' . urlencode(html_entity_decode($this->request->get['filter_code'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_trigger'])) {
			$url .= '&filter_trigger=' . urlencode(html_entity_decode($this->request->get['filter_trigger'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_action'])) {
			$url .= '&filter_action=' . $this->request->get['filter_action'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['action'] = $this->url->link('extension/sv2109_event_manager/marketplace/event' . $this->rs . 'list', 'user_token=' . $this->session->data['user_token'] . $url);

		$data['events'] = [];

		$filter_data = [
			'filter_code'     => $filter_code,
			'filter_trigger'  => $filter_trigger,
			'filter_action'   => $filter_action,
			'filter_status'   => $filter_status,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit' => $this->config->get('config_pagination_admin')
		];

		$this->load->model('extension/sv2109_event_manager/setting/event');

		$event_total = $this->model_extension_sv2109_event_manager_setting_event->getTotalEvents($filter_data);

		$results = $this->model_extension_sv2109_event_manager_setting_event->getEvents($filter_data);

		foreach ($results as $result) {
			$data['events'][] = [
				'event_id'    => $result['event_id'],
				'code'        => $result['code'],
				'description' => $result['description'],
				'trigger'     => $result['trigger'],
				'action'      => $result['action'],
				'status'      => $result['status'],
				'sort_order'  => $result['sort_order'],
				'enable'      => $this->url->link('extension/sv2109_event_manager/marketplace/event' . $this->rs . 'enable', 'user_token=' . $this->session->data['user_token'] . '&event_id=' . $result['event_id']),
				'disable'     => $this->url->link('extension/sv2109_event_manager/marketplace/event' . $this->rs . 'disable', 'user_token=' . $this->session->data['user_token'] . '&event_id=' . $result['event_id'])
			];
		}

		$url = '';

		if (isset($this->request->get['filter_code'])) {
			$url .= '&filter_code=' . urlencode(html_entity_decode($this->request->get['filter_code'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_trigger'])) {
			$url .= '&filter_trigger=' . urlencode(html_entity_decode($this->request->get['filter_trigger'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_action'])) {
			$url .= '&filter_action=' . $this->request->get['filter_action'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_code'] = $this->url->link('extension/sv2109_event_manager/marketplace/event' . $this->rs . 'list', 'user_token=' . $this->session->data['user_token'] . '&sort=code' . $url);
		$data['sort_sort_order'] = $this->url->link('extension/sv2109_event_manager/marketplace/event' . $this->rs . 'list', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url);

		$url = '';

		if (isset($this->request->get['filter_code'])) {
			$url .= '&filter_code=' . urlencode(html_entity_decode($this->request->get['filter_code'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_trigger'])) {
			$url .= '&filter_trigger=' . urlencode(html_entity_decode($this->request->get['filter_trigger'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_action'])) {
			$url .= '&filter_action=' . $this->request->get['filter_action'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $event_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('extension/sv2109_event_manager/marketplace/event' . $this->rs . 'list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($event_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($event_total - $this->config->get('config_pagination_admin'))) ? $event_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $event_total, ceil($event_total / $this->config->get('config_pagination_admin')));

		$data['filter_code'] = $filter_code;
		$data['filter_trigger'] = $filter_trigger;
		$data['filter_action'] = $filter_action;
		$data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;

		return $this->load->view('extension/sv2109_event_manager/marketplace/event_list', $data);
	}

	public function enable(): void {
    $this->load->language('marketplace/event');

		$json = [];

		if (isset($this->request->get['event_id'])) {
			$event_id = (int)$this->request->get['event_id'];
		} else {
			$event_id = 0;
		}

		if (!$this->user->hasPermission('modify', 'extension/sv2109_event_manager/marketplace/event')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('extension/sv2109_event_manager/setting/event');

			$this->model_extension_sv2109_event_manager_setting_event->editStatus($event_id, 1);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function disable(): void {
    $this->load->language('marketplace/event');

		$json = [];

		if (isset($this->request->get['event_id'])) {
			$event_id = (int)$this->request->get['event_id'];
		} else {
			$event_id = 0;
		}

		if (!$this->user->hasPermission('modify', 'extension/sv2109_event_manager/marketplace/event')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('extension/sv2109_event_manager/setting/event');

			$this->model_extension_sv2109_event_manager_setting_event->editStatus($event_id, 0);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function delete(): void {
    $this->load->language('marketplace/event');

		$json = [];

		if (isset($this->request->post['selected'])) {
			$selected = (array)$this->request->post['selected'];
		} else {
			$selected = [];
		}

		if (!$this->user->hasPermission('modify', 'extension/sv2109_event_manager/marketplace/event')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('extension/sv2109_event_manager/setting/event');

			foreach ($selected as $event_id) {
				$this->model_extension_sv2109_event_manager_setting_event->deleteEvent($event_id);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function save(): void {
    $this->load->language('marketplace/event');
		$this->load->language('extension/sv2109_event_manager/marketplace/event');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/sv2109_event_manager/marketplace/event')) {
			$json['error']['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen(trim($this->request->post['code'])) < 1) || (utf8_strlen($this->request->post['code']) > 128)) {
			$json['error']['code'] = $this->language->get('error_code');
		}
		if (empty(trim($this->request->post['trigger']))) {
			$json['error']['trigger'] = $this->language->get('error_trigger');
		}
		if (empty(trim($this->request->post['action']))) {
			$json['error']['action'] = $this->language->get('error_action');
		}

		if (isset($json['error']) && !isset($json['error']['warning'])) {
			$json['error']['warning'] = $this->language->get('error_warning');
		}

		if (!$json) {

			$this->load->model('extension/sv2109_event_manager/setting/event');

			$event_id = !empty($this->request->post['event_id']) ? (int)$this->request->post['event_id'] : false;
			$code = trim($this->request->post['code']);
			$trigger = trim($this->request->post['trigger']);
			$action = trim($this->request->post['action']);
			$description = !empty($this->request->post['description']) ? trim($this->request->post['description']) : "";
			$status = !empty($this->request->post['status']) ? boolval($this->request->post['status']) : false;
			$sort_order = !empty($this->request->post['sort_order']) ? (int)$this->request->post['sort_order'] : 0;

			$event = [
				'code' => $code,
				'trigger' => $trigger,
				'action' => $action,
				'description' => $description,
				'status' => $status,
				'sort_order' => $sort_order,
			];

			if (!empty($event_id)) {
				$this->model_extension_sv2109_event_manager_setting_event->editEvent($event_id, $event);
			} else {
				if (version_compare(VERSION, '4.0.1.0', '>=')) {
					$json['event_id'] = $this->model_extension_sv2109_event_manager_setting_event->addEvent($event);
				} else {
					$json['event_id'] = $this->model_extension_sv2109_event_manager_setting_event->addEvent($code, $description, $trigger, $action, $status, $sort_order);
				}
			}

			$json['success'] = $this->language->get('text_success_event');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
