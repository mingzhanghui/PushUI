<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-28
 * Time: 09:39
 */
namespace Content\Model;
use Think\Model;

class SpecialModel extends Model {
    protected $tableName = 'special';
    protected $fields = array('id', 'oid', 'episodes');
    protected $pk = 'id';

    /**
     * 新的专题节目总集
     * @param $post
     * $post['attrs'] = array(
     *    0 => [5, '数学']
     *    1 => [4, 'php'],
     *    ...
     * );
     */
    public function newSpecialSeries($post) {
        $Media = new MediaModel();
        if (!isset($post['soid'])) {
            $soid = $Media->generateSeriesOID();
        } else {
            $soid = $post['soid'];
        }
        $title = $post['title'];

        // table: media
        $data = array(
            "oid" => $soid,
            "title" => $title,
            "introduction" => $post['introduction'],
            "mediatypeid" => 8, // 专题节目总集
            "languageid" => $post['languageid'],
        );
        $where = array(
            "title" => $title,
            "mediatypeid" => 8,
        );
        $rows = $Media->field("title")->where($where)->select();
        if (is_null($rows[0]['title'])) {
            $result['media'] = $Media->data($data)->add();
        } else {
            $result['media'] = -1; // 专题节目总集名已经存在
            return $result;
        }

        // table: special
        $data = array(
            "oid" => $soid,
            "episodes" => $post['episodes'], // 总集数
        );
        $result['special'] = $this->data($data)->add();

        // table: medialinkcountry 地区
        $data = array(
            "oid" => $soid,
            "countryid" => $post['countryid'],
        );
        $link = new MedialinkcountryModel();
        $result['country'] = $link->data($data)->add();

        // table: language 语言
        $data = array(
            "oid" => $soid,
            "languageid" => $post['languageid'],
        );
        $link = new MedialinklanguageModel();
        $result['language'] = $link->data($data)->add();

        // genre 类型
        $data = array(
            "oid" => $soid,
            "genreid" => $post['genreid'],
        );
        $link = new MedialinkgenreModel();
        $result['genre'] = $link->data($data)->add();

        // 动态添加的专题节目总集属性  @table: speciallinkattr
        $attrs = $post['attrs'];
        unset($post);
        $model = new SpeciallinkattrModel();

        $result['attr'] = 0; // rows affected
        foreach ($attrs as $attr) {
            $data = array(
                "oid" => $soid,
                "attrid" => $attr[0],
                "attrval" => $attr[1],
            );
            // need test
            $rows = $model->field(array("id"))->where($data)->select();
            $id = $rows[0]['id'];
            if (is_null($id)) {
                $result['attr'] += ($model->data($data)->add() > 0);
            } else {
                $where = array("id" => $id);
                $result['attr'] += $model->data($data)->where($where)->save();
            }
        }

        return $result;
    }


    /**
     * 取得专题节目总集信息
     * @param $soid: 总集ID
     */
    public function getSpecialSeriesInfo($soid) {
        $result = array("info" => array(), "attrs" => array(), "pic" => array(), "episodes" => array());

        // 节目名, 简介, 语言
        $where = array("oid" => $soid);
        $fields = array("title", "introduction", "languageid");
        $rows = M("media")->field($fields)->where($where)->select();
        $result["info"] = $rows[0];
        unset($rows);
        unset($fields);

        // 类型, 地区
        $rows = M("medialinkgenre")->field(array("genreid"))->where($where)->select();
        $result['info']['genreid'] = $rows[0]['genreid'];
        $rows = M("medialinkcountry")->field(array("countryid"))->where($where)->select();
        $result['info']['countryid'] = $rows[0]['countryid'];

        // 自定义属性
        $fields = array("attrid", "attrval");
        $attrs = M("speciallinkattr")->field($fields)->where($where)->select();
        $Attr = M("attr");

        foreach ($attrs as $attr) {
            $where = array("id" => $attr['attrid']);
            $rows = $Attr->field(array("name"))->where($where)->select();
            $attr['attrname'] = $rows[0]['name'];

            array_push($result['attrs'], $attr);
        }
        unset($attrs);

        // 缩略图, 海报
        $appendix = new AppendixModel();
        $result['pic'] = $appendix->listAppendixByOID($soid);
        unset($rows);

        // 分集oid array
        $where = array("specialoid" => $soid);
        $fields = array("episodeoid", "episodeindex");
        $episodes = M("specialepisode")->field($fields)->where($where)->select();

        $fields = array("asset_name", "size");
        $Path = new PathModel();
        // 分集名 分集文件大小
        foreach ($episodes as $episode) {
            $where = array("oid" => $episode['episodeoid']);
            $rows = $Path->where($where)->field($fields)->select();
            $rows[0]['size'] = \Content\Common\File::humansize($rows[0]['size']);
            array_push($result['episodes'], array_merge($episode, $rows[0]));
        }

        return $result;
    }

}