<?php


if (!class_exists('WP_List_Table')) {
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Validated_Emails_Table extends WP_List_Table
{
	function __construct()
	{
		parent::__construct(array(
			'singular' => 'validated_email',
			'plural' => 'validated_emails',
			'ajax' => false
		));
	}

	function column_default($item, $column_name)
	{
		$value = $item[$column_name];
		if ($column_name === 'raw_data'){
			return '<textarea style="width:100%; height:80px">'.$value.'</textarea>';
		}
		return $value;
	}

	function get_columns()
	{
		return array(
			'email' => __('Email', 'turbosmtp-email-validator-for-woocommerce'),
			'status' => __('Status', 'turbosmtp-email-validator-for-woocommerce'),
			'validated_at' => __('Validated At', 'turbosmtp-email-validator-for-woocommerce'),
			'raw_data' => __('Raw Data', 'turbosmtp-email-validator-for-woocommerce'),
		);
	}

	protected function get_sortable_columns() {
		return array(
			'title'    => array( 'email', true ),
			'status'   => array( 'status', true ),
			'validated_at' => array( 'validated_at', true ),
			'raw_data' => array( 'raw_data', false )
		);
	}

	function prepare_items()
	{

		global $wpdb;
		$table_name = $wpdb->prefix . 'validated_emails'; // do not forget about tables prefix

		$per_page = 10;

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		// here we configure table headers, defined in our methods
		$this->_column_headers = array($columns, $hidden, $sortable);

		// will be used in pagination settings
		$total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

		$paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged'] - 1) * $per_page) : 0;
		$orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'validated_at';
		$order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

		$this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

		$this->set_pagination_args(array(
			'total_items' => $total_items, // total items defined above
			'per_page' => $per_page, // per page constant defined at top of method
			'total_pages' => ceil($total_items / $per_page) // calculate pages count
		));


	}
}
