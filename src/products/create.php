<?
    require_once('src/db.php');
    require_once('image.php');

    // @set $request["err"] = error_msg
    function prepare_create_product_request(array &$request)
    {
        // name    
        $name = $request["name"];
        if (empty($name))
        {
            $request["err"] = "Empty name '$name'";
            return;
        }
        $max_name_size = getenv("APP_PRODUCTS_MAX_NAME_SIZE");
        if (strlen($name) > $max_name_size)
        {
            $request["err"] = "Length of name more than $max_name_size. Too long";
            return;
        }

        // description
        $descr = @$request["description"] ?: "";
        $max_descr_size = getenv("APP_PRODUCTS_MAX_DESCRIPTION_SIZE");
        if (strlen($descr) > $max_descr_size)
        {
            $request["err"] = "Length of description more than $max_descr_size. Too long";
            return;
        }
        $request["description"] = $descr;

        // price
        $price = $request["price"];
        if (!preg_match("/^\d+(\.\d+)?$/", $price))
        {
            $request["err"] = "Invalid price format '$price'";
            return;
        }
        $request["price"] = floatval($price);

        // image
        if ($image = $request["image"])
        {
            if (!isset($image["name"]) || !isset($image["tmp_name"]) || !isset($image["error"]) || !isset($image["size"]) || !is_numeric($image["size"]))
            {
                $request["err"] = "Invalid image file";
                return;
            }
            if ($image["error"] > 0)
            {
                $request["err"] = "Failed to upload image with code " .  $image["error"];
                return;
            }   
        }        
        
        unset($request["err"]);
    }

    // @return error_msg
    function db_create_product(mysqli $conn, string $name, string $descr, float $price, string $image): string
    {
        $sql = "
        INSERT INTO product (
            name,
            description,
            price,
            image
        ) VALUES (
            '$name',
            '$descr',
            $price,
            '$image'
        );";

        mysqli_query($conn, $sql);
        return mysqli_error($conn);
    }
    
    // @return array("code" => http_code, "err" => error_msg)
    function create_product(&$conn, array $request): array
    {
        prepare_create_product_request($request);
        if ($err = $request["err"])
        {
            return array(
                "code"	=> 400,
                "err" 	=> $err
            );
        }

        $img_fname = "";
        if ($img = $request["image"])
        {
            $upl_res = upload_image($request["image"]);
            if ($err = $upl_res["err"])
            {
                return array("err" 	=> $err);
            }
            $img_fname = $upl_res["data"];
        }        

        $conn = @$conn ?: db_connect();       
        $err = db_create_product($conn, $request["name"], $request["description"], $request["price"], $img_fname);
        if ($err)
        {
            return array("err" => $err);
        }

        return array("code"	=> 204);
    }