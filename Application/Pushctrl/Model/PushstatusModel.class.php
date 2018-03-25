<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-26
 * Time: 15:19
 */
namespace Pushctrl\Model;
use Think\Model;
use Content\Common\File;

class PushstatusModel extends Model {

    protected $tableName = 'pushstatus';
    protected $fields = array('id', 'oid', 'pushtime', 'roundcount', 'state', 'ratio', 'packageid', 'missionid', 'missionlinkmediaid');
    protected $pk = 'id';
    protected $_validate = array(
        array('oid', 'require', '媒体文件OID必须!'),
        array('oid','[0-9A-F]{32}','媒体文件OID格式不正确',0,'regex'), // 定义的验证规则是一个正则表达式（默认）
    );

    public function listPush() {
        $Package = new \Subscribe\Model\PackageModel();
        $Mission = new \Subscribe\Model\MissionModel();
        $Path = new \Content\Model\PathModel();
        $Link = new \Subscribe\Model\MissionlinkmediaModel();

        // missionlinkmediaid: MissionlinkMedia表的ID
        $fields = array('oid', 'roundcount', 'ratio', 'packageid', 'missionid', 'missionlinkmediaid');
        /* 该内容的播发状态: 0 - 未播发 / 1 - 正在播发 / 2 - 完成 */
        /*
        $where = array(
            'state' => array('eq', 1),
            'ratio' => array('gt', 0)
        );
        $rows = $this->where($where)->field($fields)->select();
        */
        $rows = $this->field($fields)->select();

        $Mediatype = new \Content\Model\MediatypeModel();
        $mediatype = $Mediatype->mapMediaType();

        $n = count($rows);
        for ($i = 0; $i < $n; $i++) {
//            出现33位oid 多出1位!
            if (strlen($rows[$i]['oid']) > 32) {
                $oid33 = $rows[$i]['oid'];
                $rows[$i]['oid'] = substr($oid33, 0, 32);

                $this->data( array('oid' => $rows[$i]['oid']) )
                    ->where( array('oid' => $oid33) )
                    ->save();
            }
            // 总轮次
            $t = $Link->field('round')->find( $rows[$i]['missionlinkmediaid'] );
            $rows[$i]['round'] = $t['round'];
            if ($rows[$i]['roundcount'] < $rows[$i]['round']) {
                $rows[$i]['roundcount'] += 1;
            }
            // 进度
            $rows[$i]['ratio'] = $rows[$i]['ratio'] / 100;
            // 业务包名
            $t = $Package->where(array('id'=>$rows[$i]['packageid']))->field('packagename')->select();
            $rows[$i]['packagename'] = $t[0]['packagename'];
            // 业务期名
            $t =$Mission->where(array('id'=>$rows[$i]['missionid']))->field('missionname')->select();
            $rows[$i]['missionname'] = $t[0]['missionname'];
            // 内容名称
            $t = $Path->where(array('oid'=>$rows[$i]['oid']))->field(array('asset_name', 'mediatypeid', 'size'))->select();
            $rows[$i]['asset_name'] = $t[0]['asset_name'];
            $rows[$i]['mediatypeid'] = $t[0]['mediatypeid'];
            $rows[$i]['size'] = File::humansize($t[0]['size']);
            // 类型
            $rows[$i]['mediatype'] = $mediatype[ $rows[$i]['mediatypeid'] ];
        }
        return $rows;
    }
}