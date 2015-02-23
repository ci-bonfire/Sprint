<?php

class CallbackSampler {

    public function index($params)
    {
        $list = '<ul>';

        foreach ($params as $key => $value)
        {
            $list .= "<li>{$key} = {$value}</li>";
        }

        $list .="</ul>";

        return $list;
    }

    //--------------------------------------------------------------------

}
