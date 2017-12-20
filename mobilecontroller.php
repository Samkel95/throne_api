<?php
    # INCLUDE CONNECTION AND HEADERS
    include_once 'connection.php';
    header('Access-Control-Allow-Origin:*') ;
    header('Access-Control-Allow-Methods','GET,POST') ;
    header('Access-Control-Allow-Headers','Content-Type/json,X-Auth-Token,Origin') ;

    $response = json_encode(array('status'=>404, 'msg'=>'Invalid request')) ;
    $action = isset($_GET['action'])?$_GET['action']:'';

switch($action){

    case 'login':
        if(isset($_GET['username']) && isset($_GET['password']) ){
            $username = $_GET['username'];
            $password = $_GET['password'];
            $res=mysqli_query($con,"SELECT * FROM throne_users WHERE USER_USERNAME='$username'");
	          $row=mysqli_fetch_array($res);
            $count = mysqli_num_rows($res);
            // if uname/pass correct it returns must be 1 row
            if($count == 1 && $row['USER_PASSWORD']==$password)
	          {
		            $response = array('status'=>200, 'token'=>$row);
            }else{
                $response = array('status'=>300, 'result'=>'Incorrect username or password');
            }
        }else{
          $response = array('status'=>100, 'result'=>'Username & Password are required');
        }
    break;

    case 'getstatus':
    $res_status=mysqli_query($con,"SELECT STATUS_STATE FROM throne_status");
    $row=mysqli_fetch_array($res_status);
    $response = array('statuso'=>200, 'result'=>$row['STATUS_STATE']);
    break;


    case 'savetrans':
        if(isset($_GET['fuserid']) && isset($_GET['fusername'])  && isset($_GET['fduration'])){
            $fuserid = $_GET['fuserid'];
            $fusername = $_GET['fusername'];
            $fduration = $_GET['fduration'];
            $ftour = $_GET['ftour'];
            $update=mysqli_query($con,"INSERT INTO throne_profile (`PRO_USERID`,`PRO_USERNAME`,`PRO_DURATION`)
            VALUES ('$fuserid','$fusername','$fduration')");
            $update2=mysqli_query($con,"UPDATE throne_users SET USER_TOURS = (USER_TOURS+1) WHERE USER_ID = '$fuserid' ");
            $update_status=mysqli_query($con,"UPDATE throne_status SET STATUS_STATE = '0'");
            if($update && $update2){
                 $response=array('status'=>200,'result'=> 'OK') ;
            } else $response = array('status'=>200,'result'=>'Unable to save') ;
        }else{
          $response = array('status'=>100, 'result'=>'no data of transaction');
        }
    break;

    case'add_members':
      function gen_random_string($length=7)
         {
        $chars ="1234567890";
        $final_rand ='';
        for($i=0;$i<$length; $i++)
        {
            $final_rand .= $chars[ rand(0,strlen($chars)-1)];
        }
        return $final_rand;
        }
        $mcode= gen_random_string();


        // if ($_FILES["file"]["error"] > 0){
        //   echo "Error Code: " . $_FILES["file"]["error"] . "<br />";
        // }
        // else
        // {
        //   echo "Uploaded file: " . $_FILES["file"]["name"] . "<br />";
        //   echo "Type: " . $_FILES["file"]["type"] . "<br />";
        //   echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kilobytes<br />";
        //
        //     if (file_exists("photos/".$_FILES["file"]["name"]))
        //       {
        //       echo $_FILES["file"]["name"] . " already exists. No joke-- this error is almost <i><b>impossible</b></i> to get. Try again, I bet 1 million dollars it won't ever happen again.";
        //       }
        //     else
        //       {
        //         move_uploaded_file($_FILES["file"]["tmp_name"],"photos/".$mcode . $_FILES["file"]["name"]);
			  //         $location="photos/" .$mcode. $_FILES["file"]["name"];
        //         //  move_uploaded_file($_FILES["file"]["tmp_name"],"/var/www/vhosts/yourdomain.com/subdomains/domainname/httpdocs/foldername/images/".$_FILES["file"]["name"]);
        //         echo "Done";
        //         //$response = array('status'=>200, 'token'=>'efxsdchvjjtcrehxcu');
        //       }
        //   }


          //        $target_path = "photos/";
          //
          //$target_path = $target_path . basename( $_FILES['file']['name']);
          //          var_dump($target_path) ;
          //
          //        $response = array('file name'=> $_FILES['temp_name']) ; exit(0) ;
          //
          //if (move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
          //   // echo "Upload and move success";
          //} else {
          ////echo $target_path;
          //   // echo "There was an error uploading the file, please try again!";
          //}

            //if(isset($_GET['lastname']) && isset($_GET['firstname']) && isset($_GET['midname']) && isset($_GET['gender']) && isset($_GET['maritalstatus'])&& isset($_GET['dob']) && isset($_GET['phone']) && isset($_GET['residence']) && isset($_GET['img'])){
            $inputusername = $_GET['inputusername'];
            $inputpassword = $_GET['inputpassword'];
            $inputtel = $_GET['inputtel'];
            // $img = $_GET['img'];
            // $location="photos/" . $img;
            // $dobn=date("Y-m-d", strtotime($dob));
            $update=mysqli_query($con,"INSERT INTO throne_users  (USER_USERNAME,USER_PASSWORD,USER_TEL,USER_TOKEN,USER_LEVEL)
            VALUES
            ('$inputusername','$inputpassword','$inputtel','$mcode','2')");
            if ($update) {
            $response = array('status'=>200, 'token'=>'Registration Successfull');
          }else {
            $response = array('status'=>200, 'token'=>'Registration Failed!');
          }


            //  }

        break;

        case 'view_members':
        $offset = $_GET['offset'] ;
        $user_query = mysqli_query($con,"select * from throne_users order by USER_USERNAME ASC LIMIT ". $offset .",6 ");
        //Check count of data
        if(mysqli_num_rows($user_query)== 0){
          $response=array();
        }else{
        while ($row = mysqli_fetch_array($user_query)) {
                $data[]=$row;
            }
        $response=$data;
            }
        break;


        case'search':
          $search=$_GET['search'];
                            $query=mysql_query("select * from throne_users where USER_USERNAME like '%$search%' or USER_TEL like '%$search%' order by USER_USERNAME ASC")or die(mysql_error());
        if (mysqli_query($con,$query)>0){
                                        while($row=mysqli_fetch_array($query)){
                          $data[]=$row;
                    }
        $response=$data;
            }else{
            $response=array();
        }
        break;



        case'notifications':
        $offset = $_GET['offset'] ;
        $today= strtotime("NOW");
				$leaddate=strtotime("+150 day",$today);

        $user_query = mysql_query("select *,TIMESTAMPDIFF(YEAR, BirthDate, CURDATE()) AS age from registration where BirthDate + interval(year(curdate())- year(BirthDate)) year =curdate() order by LastName ASC LIMIT ". $offset .",10 ")or die(mysql_error());

        $user_query_count = mysql_num_rows($user_query);
         if(mysql_num_rows($user_query)== 0){

        }else{
        while ($row = mysql_fetch_array($user_query)) {
                        $data[] = $row;
                    }
         $response=$data;
         }
        break;
}

 echo json_encode($response) ;


?>
