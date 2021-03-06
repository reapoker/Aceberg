<?php
/**
 * Created by PhpStorm.
 * User: silsuer
 * Date: 2018/5/3
 * Time: 1:05
 */

namespace App;
use Illuminate\Database\Capsule\Manager as DB;

// 锚点类，在初始化的时候，挂载所有锚点方法，当运行到埋点处，执行该点所有方法
class Anchor
{
    public static $anchors = [];  // 键是锚点名，值是锚点对应的方法数组（没有先后顺序）
    // 在放置位置埋一个叫做$name的点，在执行到此处的时候，执行这个点上挂载的所有方法
    public static function burialPoint($name,\swoole_http_request $request){
     // 必须传入一个名字和request，用来从session中寻找属于当前用户的容器
        if(array_key_exists($name,self::$anchors)){
            // 锚点存在，执行锚点方法
            foreach (self::$anchors[$name] as $v){
                $v($request);  // 执行方法，方法体内如果需要任何参数，全部通过session从容器中获取
            }
        }
    }

    // 在名字为$name的点上挂载一个方法，当运行到埋点部位，会执行这个方法
    public static function mount($name,$handle){
        // 在锚点表中，查找这个name的，如果有，取出来，序列化后加入handle，再次序列化入库
        // 如果没有，挂载后入库
        $res =allToArray( DB::table('anchors')->where('name',$name)->first());
        if (empty($res)){  // 空的
            $h = [];
            $h[] = $handle;
            $insert = [
                'name'=>$name,
                'handle' => serialize($h),
            ];
            DB::table('anchors')->insert($insert);
        }else{
            $h = unserialize($res['handle']);
            $h[] = $handle;
            DB::table('anchors')->where('name',$name)->update(['handle'=>serialize($h)]);
        }
      return true;  // 返回结果
    }

}