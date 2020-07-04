<?php

/*
Plugin Name: DataTable
Description: DataTable plugin for test ticket
Version: 1.0.0
Author: Zhentychka Misha
*/

add_shortcode('datatable', 'datatable_render');
add_action('init', 'register_script');
add_action('wp_enqueue_scripts', 'enqueue_style');

function datatable_render($attr) {
    if (!empty($attr['id'] && !empty($attr['data_link']))) {
        return '
            <select data-table="' . $attr['id'] . '">
                <option value="">-- select option --</option>
                <option value="Onsweb">' . __('Onsweb') . '</option>
                <option value="WooCommerce">' . __('WooCommerce') . '</option>
                <option value="Rotterdam">' . __('Rotterdam') . '</option>
                <option value="Lviv">' . __('Lviv') . '</option>
            </select>
            <table id="' . $attr['id'] . '">
                <thead>
                    <tr>
                        <th>' . __('Id') . '</th>
                        <th>' . __('Full name') . '</th>
                        <th>' . __('Owner login') . '</th>
                        <th>' . __('Size') . '</th>
                        <th>' . __('Stars') . '</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>' . __('Id') . '</th>
                        <th>' . __('Full name') . '</th>
                        <th>' . __('Owner login') . '</th>
                        <th>' . __('Size') . '</th>
                        <th>' . __('Stars') . '</th>
                    </tr>
                </tfoot>
            </table>
            
            <script>
                $(document).ready(function() {
                    var datatable = $("#' . $attr['id'] . '").DataTable({
                        "processing": true,
                        "serverSide": true,
                        "search": {
                            "search": "wordpress"
                        },
                        "ajax": {
                            "url" :"' . $attr['data_link'] . '",
                            "dataSrc": "items",
                            "dataFilter": function(data){
                                var json = jQuery.parseJSON(data);
                                json.recordsTotal = json.total_count;
                                json.recordsFiltered = json.total_count;
                                
                                return JSON.stringify(json);
                            },
                            "data": function (data) {
                                data.per_page = data.length;
                                data.q = data.search.value;
                                data.page = data.start / data.per_page + 1;
                                
                                if (data.order[0]) {
                                    var columnId = data.order[0].column;
                                    
                                    if (data.columns[columnId].data === "stargazers_count") {
                                        data.q += "+sort:stars-" + data.order[0].dir;   
                                    }
                                }
                            },
                            "cache": true
                        },
                        "columns": [
                            {data: "id", "orderable": false},
                            {data: "full_name", "orderable": false},
                            {data: "owner.login", "orderable": false},
                            {data: "size", "orderable": false},
                            {data: "stargazers_count", "orderable": true},
                        ]
                    });
                    
                    $(document).on("change", "[data-table=' . $attr['id'] . ']", function() {
                        var value = $(this).val();
                        
                        if (value) {
                            datatable.search($(this).val());
                            datatable.ajax.reload();
                        }
                    });
                });
            </script>
        ';
    }
    else {
        return __('Params "id" and "data_link" are required');
    }

}

function register_script() {
    wp_register_script('jquery_js', '//code.jquery.com/jquery-1.11.3.min.js');
    wp_register_script('datatable_js', '//cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js');
    wp_register_style('datatable_css', '//cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css');
}

function enqueue_style(){
    wp_enqueue_script('jquery_js');
    wp_enqueue_script('datatable_js');
    wp_enqueue_style('datatable_css');
}