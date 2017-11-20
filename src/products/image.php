<?
    $img_path = "/www/site/products/images";
    $allowed_types = array("jpg", "png", "jpeg", "gif");

    // @return base_name or ""
    function move_img_file(string $tmp_name, string $ext): string
    {
        global $img_path;

        $fname = uniqid("", TRUE);
        $ok = move_uploaded_file($tmp_name, "$img_path/$fname.$ext");
        return $ok ? "$fname.$ext" : "";
    }

    // @return error_msg
    function check_ext(string $ext): string
    {
        global $allowed_types;

        if(!in_array($ext, $allowed_types))
        {
            return "Only '" . implode("', '", $allowed_types) . "' files are allowed";
        }
        return "";
    }

    // @return array("err" => error_msg, "data" => file_name)    
    function upload_image(array $file_obj): array
    {
        $ext = pathinfo($file_obj["name"], PATHINFO_EXTENSION);

        if ($err = check_ext($ext))
        {
            return array("err" => $err);
        }

        if ($fname = move_img_file($file_obj["tmp_name"], $ext))
        {
            return array("data" => $fname);
        }
        
        return array("err" => "Move failed");
    }

