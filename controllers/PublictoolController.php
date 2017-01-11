<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/9
 * Time: 11:00
 */
namespace app\controllers;

use yii\web\Controller;
use yii;
use app\models\Matchemailignore;
class PublictoolController extends Controller
{
    /**
     * 从salesmen中导入derler_info表中的域名数据
     * @throws yii\db\Exception
     */
        public function actionImport_dealers()
        {
            $db=Yii::$app->getDb();
            ignore_user_abort();
            set_time_limit(0);
            //加密串发送
            $now=date("Y-m-d");
            $str=md5($now."csdn");
            $url="http://salesman.cc/index.php/Home/Remotecall/get_dealers/call/$str";
            $temp_data=$this->send_curl_request($url,[],'get');
            $data=json_decode($temp_data);
            //实例化域名屏蔽表
            $model_ignore=new Matchemailignore();
            $arr=[];
            if(!empty($data)){
                foreach($data as $k=>$v){
                    $url=$model_ignore->findOne(["match_str"=>$v->domain]);
                    if(is_null($url)){
                        $all[]=[
                            "match_str"=>$v->domain,
                            "detail"=>$v->dealer_name,
                            "addtime"=>time(),
                            "updatetime"=>time()
                        ];
                    }
                }
                $db->createCommand()->batchInsert("sm_matchemailignore",["match_str","detail","addtime","updatetime"],$all)->execute();
            }
        }
    /**
     * 发送curl请求
     * @access protected
     * @param $url
     * @param array $data 文件修改 $data 文件修改
     * @param string $flag 标志是 get post
     */
    public function send_curl_request($url, $data = array(), $flag = 'post')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($flag == 'get') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        } else {
            curl_setopt($ch, CURLOPT_POST, 1);           // 发送一个常规的Post请求
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);            // 显示返回的Header区域内容
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循
        $temp = curl_exec($ch);
        if (curl_errno($ch)) {
            file_put_contents('error.log', 'curl消息错误：' . curl_error($ch) . "\r\n", FILE_APPEND);
        }
        curl_close($ch);
        return $temp;
    }

    
}




