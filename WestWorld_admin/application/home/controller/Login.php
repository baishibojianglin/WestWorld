<?php
namespace app\home\controller;
use think\Db;

class Login
{  
	
	/*---用户登录验证---*/	
    public function login()
    {
    	$data["user_name"]=$_POST["username"];
    	$data["password"]=$_POST["password"];
        $userlist=Db::name('user')->where('user_name',$data["user_name"])->find();

        //验证用户名是否为空
        if( empty($data["user_name"]) ){
        	$message["status"]=0;
        	$message["words"]="账号不能为空";
        	return json($message);        	
        }


        //验证用户是否存在
        if(empty($userlist)){
            $message["status"]=0;
            $message["words"]="账号不存在";
            return json($message);
        }

        //验证密码是否为空
        if( empty($data["password"]) ){
        	$message["status"]=0;
        	$message["words"]="密码不能为空";
        	return json($message);        	
        }
      
        //验证用户密码
        $password_string="#dlst_hunter".$data["password"];//拼接密码
        $password_result=MD5($password_string);//转换密码
        if($userlist["password"]!=$password_result){
         	$message["status"]=0;
        	$message["words"]="密码错误";
        	return json($message);          
        }
        
        //登录成功
        $message["status"]=1;
        $message["words"]="登录成功";
        return json($message); 


    }
    


}
