<?php
class OCA_Statistics extends WP_List_Table
{
    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $action = $this->current_action();

        $data = $this->table_data();
        usort($data, array(&$this, 'usort_reorder'));

        $perPage = 20;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args(array(
            'total_items' => $totalItems,
            'per_page' => $perPage,
        ));

        $data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);
        $this->_column_headers = array($columns, $hidden, $sortable);
      
        $newdata = array();
        if($data){
            $pos = 1;
            foreach($data as $d){
                $arr = array();
                foreach($d as $key => $val){
                    if($key === 'top_ten'){
                        if($pos <= 10){
                            $arr[$key] = $val;
                        }else{
                            $arr[$key] = '---';
                        }
                    }else{
                        $arr[$key] = $val;
                    }
                }

                $newdata[] = $arr;
                $pos++;
            }
        }

        $this->items = $newdata;
    }

    // Sorting function
    function usort_reorder($a, $b)
    {
        // If no sort, default to user_login
        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'top_ten';
        // If no order, default to asc
        $order = (!empty($_GET['order'])) ? $_GET['order'] : 'desc';
        // Determine sort order
        $result = strnatcmp($a[$orderby], $b[$orderby]);
        
        // Send final sort direction to usort
        return ($order === 'asc') ? $result : -$result;
    }
    
    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
            'user_name' => 'Name',
            'user_email' => 'Email',
            'stars' => 'Stars',
            'user_comments' => 'Comments',
            'last_comments' => 'Last Comments',
            'top_ten'   => "Top 10"
        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return array(
            'user_name' => array('user_name', true),
            'user_email' => array('user_email', true),
            'stars' => array('stars', true),
            'user_comments' => array('user_comments', true),
            'last_comments' => array('last_comments', true),
            'top_ten' => array('top_ten', true),
        );
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data()
    {
        global $wpdb;
        $data = array();
        $minval = ((get_option('oca_statistic_min_comments')) ? get_option('oca_statistic_min_comments') : 10);

        $users = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}comments WHERE `comment_approved` = 1 GROUP BY comment_author_email");

        $emailsExc = get_option( 'exclude_rank_emails' );
        if(!is_array($emailsExc)){
            $emailsExc = array();
        }

        if($users){
            foreach($users as $user){
                $arr = array(
                    'ID' => $user->comment_ID,
                    'user_name' => $user->comment_author,
                    'user_email' => $user->comment_author_email,
                    'stars' => oca_get_stars( $user->comment_author_email, false ),
                    'user_comments' => oca_get_author_comments_count($user->comment_author_email),
                    'last_comments' => oca_get_author_last_comment_date($user->comment_author_email),
                    'top_ten' => ((!in_array($user->comment_author_email, $emailsExc)) ? oca_get_top10_counts($user->comment_author_email) : '---')
                );

                if(intval($arr['user_comments']) >= $minval){
                    if($user->comment_author_email){
                        $data[] = $arr;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case $column_name:
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    // All form actions
    public function current_action()
    {
        global $wpdb;
        if (isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'records') {
            if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete' && isset($_REQUEST['record'])) {
                if(is_array($_REQUEST['record'])){
                    $ids = $_REQUEST['record'];
                    foreach($ids as $ID){
                        
                    }
                }else{
                    $ID = intval($_REQUEST['record']);
                    
                }
            }
        }
    }

} //class
