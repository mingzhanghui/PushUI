<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-27
 * Time: 11:09
 */
namespace Content\Model;
use Think\Model;

use Content\Model\MediaModel;
use Content\Model\EditstatusModel;

class OperaModel extends Model {
    protected $tableName = 'opera';
    protected $fields = array('id','oid','director','actor','runtime');
    protected $pk     = 'id';

    public function getOperaInfo($oid) {
        // 取得 戏曲信息
        $where = array("oid" => $oid);
        $fields = array("director", "actor", "runtime");
        $rows = $this->field($fields)->where($where)->select();
        $result = $rows[0];
        unset($rows);
        unset($fields);

        // 媒体通用信息
        $Media = new MediaModel();
        $fields = array("title", "introduction", "rating", "languageid", "channelid");
        $rows = $Media->field($fields)->where($where)->select();
        $result = array_merge($result, $rows[0]);

        // 类型genre
        $result['genreid'] = $this->getValueByName("medialinkgenre", "genreid", $where);
        // 年份 year
        $result['yearid'] = $this->getValueByName("medialinkyear", "yearid", $where);
        // 标签 tag
        $result['tagid'] = $this->getValueByName("medialinktag", "tagid", $where);
        // 国家
        $result['country'] = $this->getValueByName("medialinkcountry", "countryid", $where);

        // 备播状态 table: EditStatus
        // SliceStatus	Int	分片状态，0 未分片 1已分片
        $EditStatus = new EditstatusModel();
        $result['slicestatus'] = $EditStatus->getEditStatus($oid, "slicestatus");

        return $result;
    }

    public function setOperaInfo($post) {
        $result = array();
        // Opera
        $oid = $post['oid'];
        $where = array("oid" => $oid);
        $data = array(
            "director" => $post['director'],
            "actor" => $post['actor'],
            "runtime" => $post['runtime'],
        );
        $result['opera'] = $this->where($where)->save($data);

        // 媒体通用信息
        $title = $post['title'];
        $data = array(
            "title" => $title,
            "introduction" => $post['introduction'],
            "rating" => $post['rating'],
            "languageid" => $post['languageid'],
            "channelid" => $post['channelid'],
        );
        $Media = new \Content\Model\MediaModel();
        $result['media'] = $Media->where($where)->save($data);

        $path = new PathModel();
        $result['path'] = $path->where($where)->data(array('asset_name'=>$title))->save();

        // 类型genre
        $Genre = new \Content\Model\MedialinkgenreModel();
        $result['genre'] = $Genre->setGenre(array("oid" => $oid, "genreid" => $post['genreid']));
        // 年份 year
        $Year = new \Content\Model\MedialinkyearModel();
        $result['year'] = $Year->setyear(array("oid" => $oid, "yearid" => $post['yearid']));
        // 标签 tag
        $Tag = new \Content\Model\MedialinktagModel();
        $result['tag'] = $Tag->setTag(array("oid" => $oid, "tagid" => $post['tagid']));

        // language
        $Lang = new \Content\Model\MedialinklanguageModel();
        $result['languageid'] = $Lang->setLang(array('oid'=>$oid, 'languageid'=> $post['languageid']));

        // 国家
        $Country = new \Content\Model\MedialinkcountryModel();
        $data = array("countryid" => $post['countryid']);
        $result['country'] = $Country->setcountry(array_merge($data, $where));

        // 备播状态 table: EditStatus
        // EditStatus	Int	编辑状态，0 未编辑 1 草稿 2 提交允许编辑 3 审核允许编辑
        // $result['editstatus'] = M("editstatus")->data(array("editstatus" => 1))->where($where)->save();
        $EditStatus = new EditstatusModel();
        $result['editstatus'] = $EditStatus->setEditStatus($oid, 'editstatus', 1);

        return $result;
    }

    private function getValueByName($table, $name, $where) {
        $model = D($table);
        $res = $model->where($where)->limit(0, 1)->select();
        return $res[0][$name];
    }
}