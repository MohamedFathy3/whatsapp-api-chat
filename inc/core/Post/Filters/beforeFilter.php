<?php
platfrom([
    "ids" => ids(),
    "platform_id" => 1,
    "name" => __("Social Media Management"),
    "default_page" => "dashboard",
    "icon" => "fad fa-paper-plane",
    "color" => "#ff0e0e",
    "status" => 1
]);

if(uri('segment',1) == "post"){
    $configs = get_blocks("block_frame_posts", false, true);

    $menus = [];
    if( ! empty($configs) ){
        $menus = $configs;
        if( count($menus) >= 2 ){
            usort($menus, function($a, $b) {
                if( isset($a['data']['position']) &&  isset($b['data']['position']) )
                    return $a['data']['position'] <=> $b['data']['position'];
            });
        }
    }
    $request->block_frame_posts = $menus;
}