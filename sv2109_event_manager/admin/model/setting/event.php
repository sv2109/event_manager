<?php
/*
 * @author     sv2109
 * @copyright  2022
 * @link       http://sv2109.com
 */

namespace Opencart\Admin\Model\Extension\Sv2109EventManager\Setting;;

class Event extends \Opencart\Admin\Model\Setting\Event {

	public function editEvent(int $event_id, array $event): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "event` SET `code` = '" . $this->db->escape($event['code']) . "', `description` = '" . $this->db->escape($event['description']) . "', `trigger` = '" . $this->db->escape($event['trigger']) . "', `action` = '" . $this->db->escape($event['action']) . "', `status` = '" . (int)$event['status'] . "', `sort_order` = '" . (int)$event['sort_order'] . "' WHERE `event_id` = '" . (int)$event_id . "'");
	}

	public function getEvents(array $data = []): array {
		$sql = "SELECT * FROM `" . DB_PREFIX . "event`";

		$filter = [];
		if (!empty($data['filter_code'])) {
			$filter[] = "`code` LIKE '%" . $this->db->escape((string)str_replace(' ', '%', $data['filter_code']) . '%') . "'";
		}

		if (!empty($data['filter_trigger'])) {
			$filter[] = "`trigger` LIKE '%" . $this->db->escape((string)str_replace(' ', '%', $data['filter_trigger']) . '%') . "'";
		}

		if (!empty($data['filter_action'])) {
			$filter[] =  "`action` LIKE '%" . $this->db->escape((string)str_replace(' ', '%', $data['filter_action']) . '%') . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$filter[] =  "`status` = '" . (int)$data['filter_status'] . "'";
		}

		if ($filter) {
			$sql .= " WHERE " . implode(" AND ", $filter);
		}

		$sort_data = [
			'code',
			'trigger',
			'action',
			'sort_order',
			'status',
			'date_added'
		];

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY `" . $data['sort'] . "`";
		} else {
			$sql .= " ORDER BY `sort_order`";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalEvents(array $data = []): int {

		$sql = "SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "event`";

		$filter = [];
		if (!empty($data['filter_code'])) {
			$filter[] = "`code` LIKE '%" . $this->db->escape((string)str_replace(' ', '%', $data['filter_code']) . '%') . "'";
		}

		if (!empty($data['filter_trigger'])) {
			$filter[] = "`trigger` LIKE '%" . $this->db->escape((string)str_replace(' ', '%', $data['filter_trigger']) . '%') . "'";
		}

		if (!empty($data['filter_action'])) {
			$filter[] =  "`action` LIKE '%" . $this->db->escape((string)str_replace(' ', '%', $data['filter_action']) . '%') . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$filter[] =  "`status` = '" . (int)$data['filter_status'] . "'";
		}

		if ($filter) {
			$sql .= " WHERE " . implode(" AND ", $filter);
		}

		$query = $this->db->query($sql);

		return (int)$query->row['total'];
	}
}
