<?php
$plan = db_get('*', TB_PLANS, "ids = '".uri('segment',4)."'");

$request->plan_permissions = false;
if( $plan ){
    if( $plan->permissions != "" ){
        $request->plan_permissions = json_decode( $plan->permissions );
    }
}

if(uri('segment',1) == "plans"){
    $configs = get_blocks("block_permissions");

    if( ! empty($configs) ){
        $menus = [];
        foreach ($configs as $config) {
            if( isset( $config['menu'] ) && ( !isset( $config['show_plan'] ) || $config['show_plan'] ) && (!isset( $config['role'] ) || $config['role'] == 0 ) || ( isset( $config['show_plan'] ) && $config['show_plan'] ) ){
                $config['menu']['id'] =  isset($config['id'])?$config['id']:false;
                $config['menu']['icon'] = isset($config['icon'])?$config['icon']:false;
                $config['menu']['color'] = isset($config['color'])?$config['color']:false;
                $config['menu']['data'] = isset($config['data'])?$config['data']:false;

                if( !isset($config['menu']['name']) ){
                    $config['menu']['name'] = isset($config['name'])?$config['name']:false;
                }

                $menus[] = $config['menu'];
            }
        }

        if( count($menus) > 2 ){
            usort($menus, function($a, $b) {
                if(isset($a['position']) && isset($b['position'])){
                    return $a['position'] <=> $b['position'];
                }
            });
            $menus = array_reverse($menus);
        }

        if( count($menus) > 2 ){
            usort($menus, function($a, $b) {
                if(isset($a['tab']) && isset($b['tab'])){
                    return $a['tab'] <=> $b['tab'];
                }
            });
        }

        //TOP TAB
        $top_tabs = [];
        foreach ($menus as $row) {
            $tab = $row['id'];
            $top_tabs[$tab][] = $row;
        }

    }

    $request->permissions = $top_tabs;
}

$configs = get_blocks("block_plans", false);

if( ! empty($configs) ){
    $plans = [];
    $plan_items = [];
    foreach ($configs as $config) {
        if(isset( $config['data'] )){
            $data = $config['data'];
            if( isset($data['items']) ){
                foreach ($data['items'] as $key => $value) {
                    $data['items'][$key]['icon'] = $config['icon'];
                    $data['items'][$key]['color'] = $config['color'];
                    if(!isset($data['items'][$key]['name'])){
                        $data['items'][$key]['name'] = $config['name'];
                    }
                }
            }
            $plan_items[] = $data;
        }
    }

    usort($plan_items, function($a, $b) {
        return $a['position'] <=> $b['position'];
    });

    foreach ($plan_items as $key => $plan_item) {

        $plans[ $plan_item['tab'] ]['position'] = $plan_item['position'];
        $plans[ $plan_item['tab'] ]['tab'] = $plan_item['tab'];
        $plans[ $plan_item['tab'] ]['label'] = $plan_item['label'];
        $plans[ $plan_item['tab'] ]['permission'] = isset($plan_item['permission'])?$plan_item['permission']:false;

        if(!empty($plan_item['items'])){
            if(!isset($plans[ $plan_item['tab'] ]['items'])){
                $plans[ $plan_item['tab'] ]['items'] = [];
            }
            
            foreach ($plan_item['items'] as $key => $value) {
                $plans[ $plan_item['tab'] ]['items'][] = $value;
            }
        }elseif( !empty($plan_item['custom_items']) ){
            foreach ($plan_item['custom_items'] as $key => $value) {
                if ($key == "after") {
                    $plans[ $plan_item['tab'] ]['after_items'] = $value;
                }
                if ($key == "before") {
                    $plans[ $plan_item['tab'] ]['before_items'] = $value;
                }

            }
        }


    }

    usort($plans, function($a, $b) {
        return $a['tab'] <=> $b['tab'];
    });
}

$request->plans = $plans;