<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-28
 * Time: 10:49
 */
namespace Content\Model;
use Think\Model;

class TvshowModel extends Model {
    protected $tableName = 'tvshow';
    protected $fields = array('id', 'oid', 'episodes', 'host', 'sourcefrom', 'tvshowtype');
    protected $pk = 'id';

    /**
     * 电视节目总集 通过总集oid取得总集信息
     * @param $soid
     * @return array
     */
    public function getTVShowsInfo($soid) {
        if (!isset($soid)) {
            throw_exception(__METHOD__ . " soid is not set!");
        }
        // 节目名 全片简介 评分 语言
        $Media = D("media");
        $where = array("oid" => $soid);
        $fields = array("title", "introduction", "rating", "languageid");
        $row = $Media->field($fields)->where($where)->select();
        $result = $row[0];
        unset($row);

        $fields = array("episodes", "host", "sourcefrom", "tvshowtype");
        $row = $this->field($fields)->where($where)->select();
        $result = array_merge($result, $row[0]);
        unset($row);

        $rows = D("medialinkcountry")->where($where)->field(array("countryid"))->select();
        $result['countryid'] = $rows[0]['countryid'];

        return $result;
    }

    /**
     * 修改电视节目总集
     * @return array
     */
    public function editTvShowSeries($data, $soid) {
        $where = array("oid" => $soid);

        // rows affected
        $rows = array('media' => 0, 'tvshow' => 0);

        $mediaData = array(
            "title" => $data['title'],
            "rating" => $data['rating'],
            'languageid' => $data['languageid'],
            "introduction" => $data['introduction'],
        );
        $Media = new MediaModel();
        $rows['media'] = $Media->where($where)->save($mediaData);
        unset($mediaData);

        $showsData = array(
            "host" => $data['host'],
            "sourcefrom" => $data['sourcefrom'],
            "tvshowtype" => $data['tvshowtype'],
        );
        $rows['tvshow'] = $this->where($where)->save($showsData);
        unset($showsData);

        $Lang = new MedialinklanguageModel();
        $Lang->setLang(array("languageid" => $data['languageid'], "oid" => $soid));

        if (isset($data['countryid'])) {
            $Country = new MedialinkcountryModel();
            $Country->setcountry(array("countryid" => $data['countryid'], 'oid' => $soid));
        }
        return $rows;
    }

}

