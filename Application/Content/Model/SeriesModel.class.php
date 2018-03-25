<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-26
 * Time: 14:31
 */
namespace Content\Model;
use Think\Model;

class SeriesModel extends Model {
    protected $tableName = 'series';
    protected $fields = array('id', 'oid', 'episodes', 'director', 'actor');
    protected $pk = 'id';

    // 电视剧 - 查找 所有电视剧总集信息列表
    //  $cond = array("title", array("LIKE", '%'.$querystring.'%'));
    public function listTVPlaySeries($cond = null) {
        $fields = array("oid", "title", "languageid");
        $where = array("mediatypeid" => 5); // 电视剧总集
        if ($cond != null) {
            $where = array_merge($where, $cond);
        }
        $Media = new \Content\Model\MediaModel();
        $list = $Media->field($fields)->where($where)->order("ID DESC")->select();

        $SeriesEpisodes = new \Content\Model\SeriesepisodeModel();
        $Medialinkcountry = new \Content\Model\MedialinkcountryModel();
        $Language = new \Content\Model\LanguageModel();
        $Medialinkyear = new \Content\Model\MedialinkyearModel();
        $Medialinkgenre = new \Content\Model\MedialinkgenreModel();

        $n = count($list);
        for ($i = 0; $i < $n; $i++) {
            $oid = $list[$i]['oid'];
            $list[$i]['country'] = $Medialinkcountry->getCountryNameByOID($oid);

            $list[$i]['language'] = $Language->getLangNameByID($list[$i]['languageid']);
            unset($list[$i]['languageid']);

            $list[$i]['year'] = $Medialinkyear->getYearNameByOID($oid);
            // 类型
            $list[$i]['genre'] = $Medialinkgenre->getGenreNameByOID($oid);
            // 总集数
            $t = $this->where(array('oid'=>$oid))->field('episodes')->select();
            $list[$i]['episodes'] = $t[0]['episodes'];
            // 导入集数
            $list[$i]['imported'] = $SeriesEpisodes->where(array('seriesoid'=>$oid))->count();
        }
        return $list;
    }

    /**
     * 电视剧总集 通过总集oid取得总集信息
     * @param $soid
     * @return array
     */
    public function getTVPlaySeriesInfo($soid) {
        if (!isset($soid)) {
            throw_exception(__METHOD__ . " series oid soid is not set!");
        }
        $Media = D("media");
        $where = array("oid" => $soid);
        $fields = array("title", "introduction", "rating", "languageid");
        $row = $Media->field($fields)->where($where)->select();
        $result = $row[0];
        unset($row);

        // 总集数 导演 演员
        $fields = array("episodes", "director", "actor");
        $row = $this->field($fields)->where($where)->select();
        $result = array_merge($result, $row[0]);
        unset($row);

        $result['yearid'] = $this->getValueByName("medialinkyear", "yearid", $where);
        $link = new \Content\Model\MedialinkgenreModel();
        $result['genreid'] = $link->getgenreidbyoid($soid);
        $result['countryid'] = $this->getValueByName("medialinkcountry", "countryid", $where);
        $result['tagid'] = $this->getValueByName("medialinktag", "tagid", $where);

        return $result;
    }

    /**
     * 删除总集
     * @type: string
     *   "series": 电视剧总集
     *   "tvshow": 电视节目总集
     */
    public function deleteSeries($soid, $type = "series") {
        if (!isset($soid)) {
            throw_exception("soid is not set");
        }
        $where = array("oid" => $soid);

        // 删除总集 相关的表条目
        $tables = array("path", "media", $type, "medialinkyear",
            "medialinkgenre", "medialinkcountry", "medialinklanguage", "medialinktag");

        foreach ($tables as $table) {
            $model = D($table);
            $model->where($where)->delete();
        }
        unset($model);

        // 删除上传的附件
        $Model = new \Home\Model\ConfigureModel();
        $dir = $Model->getconfstrbyname("appendix_path");
        $appendixdir = rtrim($dir, "\\/") . "/";
        unset($Model);

        $model = D("appendix");
        $where = array("attachoid" => $soid);
        $rows = $model->field(array("url"))->where($where)->select();
        $n = count($rows);
        if ($n > 0) {
            for ($i = 0; $i < count($rows); $i++) {
                $path = $appendixdir . $rows[$i]['url'];
                if (is_file($path)) {unlink($path);}
            }
        } else {
            // 数据库中找不到对应的路径
            $dir = $_SERVER['DOCUMENT_ROOT'] . __ROOT__ . "/resource/appendix/" . strtoupper($soid);
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    $path = rtrim($dir, "\\/") . "/" . $file;
                    unlink($path);
                }
                closedir($dh);
            }
            rmdir($dir);
        }
        return $model->where($where)->delete();
    }

    /**
     * 修改电视剧总集信息
     * @param $data
     * array(
     *   'title' => string '来自星星的你' (length=18)
     *   'episodes' => string '21' (length=2)
     *   'rating' => string '9' (length=1)
     *   'director' => string '张太侑' (length=9)
     *   'actor' => string '金秀贤、全智贤、刘仁娜、朴海镇' (length=45)
     *   'yearid' => string '1' (length=1)
     *   'genreid' => string '1' (length=1)
     *   'countryid' => string '15' (length=2))
     *   ...
     * @param $soid
     */
    public function editTvPlaySeries($data, $soid) {
        $where = array("oid" => $soid);

        // rows affected
        $rows = array('media' => 0, 'series' => 0);

        $Media = new MediaModel();
        $mediaData = array(
            "title" => $data['title'],
            "rating" => $data['rating'],
            'languageid' => $data['languageid'],
            "introduction" => $data['introduction'],
        );
        $rows['media'] = $Media->where($where)->save($mediaData);
        unset($mediaData);

        $seriesData = array(
            "episodes" => $data['episodes'],
            "director" => $data['director'],
            "actor" => $data['actor'],
        );
        $rows['series'] = $this->where($where)->save($seriesData);
        unset($seriesData);

        $Year = new MedialinkyearModel();
        $Year->setyear(
            array('yearid' => $data['yearid'], 'oid'=>$soid)
        );

        $Genre = new MedialinkgenreModel();
        $Genre->where($where)->save();
        $Genre->setGenre(
            array("genreid" => $data['genreid'], 'oid' => $soid)
        );

        if (isset($data['countryid'])) {
            $Country = new MedialinkcountryModel();
            $Country->setcountry(
                array("countryid" => $data['countryid'], 'oid' => $soid)
            );
        }
        $Lang = new MedialinklanguageModel();
        $Lang->setLang(array("languageid" => $data['languageid'], 'oid' => $soid));

        $linktag = new \Content\Model\MedialinktagModel();
        $linktag->addMediaTag($soid, $data['tagid']);

        return $rows;
    }

    private function getValueByName($table, $name, $where) {
        $res = D($table)->where($where)->limit(0, 1)->select();
        return $res[0][$name];
    }
}