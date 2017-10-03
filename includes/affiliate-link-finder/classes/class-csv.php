<?php

if( ! class_exists( 'CSV' ) ) {

class CSV {

    public $rows = array();
    public $filtered_rows = array();
    public $col_headers = array();

    public function __construct($path) {
        $this->rows = $this->csv_to_array($path);
        $this->col_headers = $this->filter_rows_by_index([0])[0];
        echo 'CSV object created...<br>';
    }

    public function filter_rows_by_col_value($col_name='product_id', $match_value) {
        $matches = array();
        $col_index = $this->get_column_id($col_name);

        foreach($this->rows as $row_id=>$row) {
            $value = $row[$col_index];
            if($value == $match_value){
                array_push($matches,$this->rows[$row_id]);
            }
        }
        return $matches;
    }

    public function filter_rows_by_col_value_contains($col_name='product_id', $match_value) {
        $matches = array();
        $col_index = $this->get_column_id($col_name);

        foreach($this->rows as $row_id=>$row) {
            $value = $row[$col_index];
            if(strpos($value, $match_value) !== false ){
                array_push($matches,$this->rows[$row_id]);
            }
        }
        return $matches;
    }

    public function filter_rows_by_index($row_index_array) {
        $filtered_rows = $this->filtered_rows = array();
        foreach($row_index_array as $index) {
            array_push($filtered_rows,$this->rows[$index]);
            array_push($this->filtered_rows,$this->rows[$index]);
        }
        return $filtered_rows;
    }

    public function get_product_links($col_name = 'link', $array_of_rows) {
        $links = array();
        $link_col_index = $this->get_column_id($col_name);
        foreach($array_of_rows as $row) {
            array_push($links,$row[$link_col_index]);
        }
        return $links;
    }

    public function get_column_id($column_name) {
        $col_index = array_search($column_name,$this->col_headers);
        return $col_index;
    }

    public function print_rows($row_index_array) {
        foreach($row_index_array as $index) {
            print_r($this->rows[$index]);
        }
    }

    public function print_csv() {
        echo '<pre>' . print_r($this->rows) . '</pre>';
    }

    private function csv_to_array($path) {
        $rows = array();

        $csv = fopen($path, "r");
        while (($csv_data = fgetcsv($csv)) !== FALSE) {
            array_push($rows,$csv_data);
        }
        fclose($csv);
        return $rows;
    }

}

}


?>
