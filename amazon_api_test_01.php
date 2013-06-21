<?php

    /*
    Product Advertising API を利用して商品カテゴリツリーを取得するサンプルです。
    */

    $access_info = json_decode(file_get_contents("../config.json"));

    $baseurl = "http://ecs.amazonaws.jp/onca/xml";

    $params = array();
    $params["AWSAccessKeyId"] = $access_info->access_key_id;
    $params["AssociateTag"]   = $access_info->associate_tag;
    $params["BrowseNodeId"]   = "2128664051";
    $params["Operation"]      = "BrowseNodeLookup";
    $params["Service"]        = "AWSECommerceService";
    $params["Timestamp"]      = gmdate("Y-m-d\TH:i:s\Z");
    $params["Version"]        = "2011-08-01";

    // sort the parameters
    ksort($params);

    // create the canonicalized query
    $canonical_string = "";

    foreach ($params as $param => $value)
    {
        $param = str_replace("%7E", "~", rawurlencode($param));
        $value = str_replace("%7E", "~", rawurlencode($value));
        $canonical_string .= "&" . $param . "=" . $value;
    }

    $canonical_string = substr($canonical_string, 1);

    // create the string to sign
    $method = "GET";
    $host   = parse_url($baseurl)["host"];
    $path   = parse_url($baseurl)["path"];

    $string_to_sign = $method . "\n" . $host . "\n" . $path . "\n" . $canonical_string;

    // calculate HMAC with SHA256 and base64-encoding
    $signature = base64_encode(hash_hmac("sha256", $string_to_sign, $access_info->secret_access_key, True));

    // encode the signature for the request
    $signature = str_replace("%7E", "~", rawurlencode($signature));

    // create request url
    $request_url = $baseurl . "?" . $canonical_string . "&Signature=" . $signature;

    echo $request_url;

?>