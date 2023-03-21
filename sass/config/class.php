<?php
/**
 * 排班配置表
 */

return [
    //非DCS
    'cass_define' => array(
        array(
            'name' => '早班',
            'time' => '23:00-07:30',
        ),
        array(
            'name' => '白班',
            'time' => '07:30-16:00',
        ),
        array(
            'name' => '中班',
            'time' => '16:00-23:00',
        ),
        array(
            'name' => '休息',
            'time' => '00:00-23:59',
        )
    )
];
