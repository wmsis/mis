<?php
/**
 * 统一标准名称英文简称
 */

return [
    //非DCS
    'not_dcs' => array(
        //键名ljrkl 为配置文件中定义的英文名，可与数据库中定义的不同, 一旦定义不能更改
        'ljrkl' => array(
            'cn_name' => '垃圾入库量', //数据库中定义的中文名
            'en_name' => 'ljrkl' //数据库中定义的英文名，如需修改，必须与数据库中一致
        ),
        'ljrll' => array(
            'cn_name' => '垃圾入炉量', //数据库中定义的中文名
            'en_name' => 'ljrll' //数据库中定义的英文名，如需修改，必须与数据库中一致
        ),
        'cydl' => array(
            'cn_name' => '厂用电量',
            'en_name' => 'cydl'
        ),
        'fdl' => array(
            'cn_name' => '发电量',
            'en_name' => 'fdl'
        ),
        'no1_fdl' => array(
            'cn_name' => '1#机发电量',
            'en_name' => 'no1_fdl'
        ),
        'no2_fdl' => array(
            'cn_name' => '2#机发电量',
            'en_name' => 'no2_fdl'
        ),
        'no1_swdl' => array(
            'cn_name' => '1#机上网电量',
            'en_name' => 'no1_swdl'
        ),
        'no2_swdl' => array(
            'cn_name' => '2#机上网电量',
            'en_name' => 'no2_swdl'
        ),
        'swdl' => array(
            'cn_name' => '上网电量',
            'en_name' => 'swdl'
        ),
    ),

    //日报中计算每天的最大值最小值平均值指标
    'daily' => array(
        'gl1_lw' => array(
            'cn_name' => '1#锅炉给水温度',
            'en_name' => 'lw'
        ),
        // 'gl1_gswd' => array(
        //     'cn_name' => '1#锅炉给水温度',
        //     'en_name' => 'gl1_gswd'
        // ),
        // 'gl2_gswd' => array(
        //     'cn_name' => '2#锅炉给水温度',
        //     'en_name' => 'gl2_gswd'
        // ),
        // 'gl3_gswd' => array(
        //     'cn_name' => '3#锅炉给水温度',
        //     'en_name' => 'gl3_gswd'
        // ),
    ),
    'boiler' => array(
        'GL1_LTSBWD_L' => array(
            'cn_name' => '1#炉炉膛内上部断面左墙温度',
            'en_name' => 'GL1_LTSBWD_L'
        ),
    ),
    'user' => array(
        'instation'=>'电厂用户',
        'webmaster'=>'电厂管理员',
        'group'=>'集团用户',
        'admin'=>'超级管理员',
    ),
    'role' => array(
        'instation'=>'电厂角色',
        'group'=>'集团角色',
        'admin'=>'超级管理员',
    )
];
