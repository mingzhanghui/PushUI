<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-26
 * Time: 14:11
 */
namespace Content\Model;
use Think\Model;

class MediaModel extends Model {
    protected $tableName = 'media';
    protected $fields = array('id', 'oid', 'title', 'introduction', 'rating',
        'mediatypeid', 'languageid', 'channelid', 'starttime');
    protected $pk     = 'id';

    // 电视剧总集
    public function listTVPlaySeriesTitle() {
        $fields = array("oid", "title");
        $where = array("mediatypeid" => 5); // 电视剧总集
        return $this->field($fields)->where($where)->order("ID DESC")->select();
    }

    // 电视节目 - 查找 所有电视剧总集信息列表
    //  $cond = array("title", array("LIKE", '%'.$querystring.'%'));
    public function listTVShows($cond = null) {
        $fields = array("oid", "title", "languageid");
        $where = array("mediatypeid" => 6); // 电视节目总集
        if ($cond != null) {
            $where = array_merge($where, $cond);
        }
        $list = $this->field($fields)->where($where)->order("ID DESC")->select();

        $n = count($list);
        $Country = new \Content\Model\MedialinkcountryModel();
        $Lang = new \Content\Model\LanguageModel();
        $TVShow = new \Content\Model\TvshowModel();
        $Episodes = new \Content\Model\TvshowepisodeModel();

        for ($i = 0; $i < $n; $i++) {
            $oid = $list[$i]['oid'];
            // 地区
            $list[$i]['country'] = $Country->getCountryNameByOID($oid);

            // 语言
            $list[$i]['language'] = $Lang->getLangNameByID($list[$i]['languageid']);
            unset($list[$i]['languageid']);

            // 节目类型 播出电视台
            $fields = array("tvshowtype", "sourcefrom");
            $rows = $TVShow->field($fields)->where(array("oid" => $oid))->select();
            $list[$i] = array_merge($list[$i], $rows[0]);

            // 导入集数
            $list[$i]['imported'] = $Episodes->getTVShowEpisodeCount($oid);
        }
        return $list;
    }

    /**
     * 专题节目总集 - 查找 所有专题节目总集信息列表
     * $cond = array("title", array("LIKE", '%'.$q.'%'));
     * @param null $cond
     * @return mixed
     */
    public function listSpecialSeries($cond = null) {
        $fields = array("oid", "title", "languageid");
        $where = array("mediatypeid" => 8); // 专题节目总集
        if ($cond != null) {
            $where = array_merge($where, $cond);
        }
        $list = $this->field($fields)->where($where)->order("ID DESC")->select();

        $n = count($list);
        for ($i = 0; $i < $n; $i++) {
            $oid = $list[$i]['oid'];
            $link = new MedialinkcountryModel();
            $list[$i]['country'] = $link->getCountryNameByOID($oid);

            $link = new LanguageModel();
            $list[$i]['language'] = $link->getLangNameByID($list[$i]['languageid']);
            unset($list[$i]['languageid']);

            // 类型
            $link = new MedialinkgenreModel();
            $list[$i]['genre'] = $link->getGenreNameByOID($oid);
            // 导入集数
            $se = new SpecialepisodeModel();
            $list[$i]['imported'] = $se->getSpecialsEpisodeCount($oid);
        }
        return $list;
    }

    // 电视节目总集 名称列表
    public function listSpecialSeriesTitle() {
        $fields = array("oid", "title");
        $where = array("mediatypeid" => 8); // 专题节目总集

        $list = $this->field($fields)->where($where)->order("ID DESC")->select();
        return $list;
    }

    /**
     * 修改专题节目总集
     * @param $post
     * @return mixed
     */
    public function setSpecialSeriesInfo($post) {
        if (!isset($post['soid'])) {
            throw_exception(__METHOD__ . ": soid is required");
        }
        $soid = $post['soid'];
        $title = $post['title'];

        $where = array("oid" => $soid);
        // table: media
        $data = array(
            "title" => $title,
            "introduction" => $post['introduction'],
            "languageid" => $post['languageid'],
        );
        $result['media'] = $this->where($where)->data($data)->save();

        // table: medialinkcountry 地区
        $data = array(
            "countryid" => $post['countryid'],
        );
        $link = new MedialinkcountryModel();
        $result['country'] = $link->where($where)->data($data)->save();

        // table: language 语言
        $data = array(
            "languageid" => $post['languageid'],
        );
        $link = new MedialinklanguageModel();
        $result['language'] = $link->where($where)->data($data)->save();

        // genre 类型
        $data = array(
            "genreid" => $post['genreid'],
        );
        $link = new MedialinkgenreModel();
        $result['genre'] = $link->where($where)->data($data)->save();

        // 动态添加的专题节目总集属性  @table: speciallinkattr
        $attrs = $post['attrs'];
        unset($post);

        $result['attr'] = 0; // rows affected
        $model = new SpeciallinkattrModel();

        $model->where($where)->delete();
        foreach ($attrs as $attr) {
            $data = array(
                "oid" => $soid,
                "attrid" => $attr[0],
                "attrval" => $attr[1],
            );
            $result['attr'] += ($model->data($data)->add() > 0);
        }

        return $result;
    }

    public function delMedia($table, $where) {
        return D($table)->where($where)->delete();
    }

    /**
     * 向$table表中添加$data数组数据
     * @param $table
     * @param $data
     * @param $where  array 检查是否重复的条件, 有重复则什么都不做
     * @return int|mixed
     */
    public function addMediaUniq($table, $data, $where) {
        $model = D($table);
        $count = $model->where($where)->count();
        if ($count > 0) {
            return (-1) * $count;
        }
        return $model->data($data)->add();
    }

    /**
     * 向$table表中添加$data数组数据, 遇到$where相同情况则更新数据
     * @param $table
     * @param $data
     * @param $where
     * @return bool|mixed
     */
    public function addMediaUpdate($table, $data, $where) {
        $model = D($table);
        $count = $model->where($where)->count();
        if ($count > 0) {
            return $model->data($data)->where($where)->save();
        }
        return $model->data($data)->add();
    }

    /**
     * 取得媒体基本信息  不包括总集中的分集, 用于内容查询 sidebar展示
     * @param $oid
     * @param null $mediatypeid
     */
    public function mediaPreview($oid, $mediatypeid = null) {
        if (is_null($mediatypeid)) {
            $Path = new \Content\Model\PathModel();
            $mediatypeid = $Path->getMediaTypeByOID($oid);
        }
        // 片名, 简介
        $fields = array("title", "introduction");
        $where = array("oid" => $oid);
        $rows = $this->field($fields)->where($where)->select();
        $result = $rows[0];

        // 热点视频类型 没有加入到media表?
        if (!array_key_exists("title", $result) || !isset($result['title'])) {
            isset($Path) || $Path = new \Content\Model\PathModel();
            $rows = $Path->field("asset_name")->where($where)->select();
            $result['title'] = $rows[0]['asset_name'];
        }

        // 缩略图
        $Appendix = new \Content\Model\AppendixModel();
        $result['url'] = $Appendix->getThumbUrl($oid);

        $mediatypeid = intval($mediatypeid);
        switch ($mediatypeid) {
            // 电影
            case 1:
                // 导演, 主要演员
                $fields = array("director", "actor");
                $Movie = new \Content\Model\MovieModel();
                $rows = $Movie->where($where)->field($fields)->select();
                break;
            // 电视剧总集
            case 5:
                $fields = array("director", "actor");
                $Series = new \Content\Model\SeriesModel();
                $rows = $Series->where($where)->field($fields)->select();
                break;
            // 电视节目总集
            case 6:
                $fields = array("host", "sourcefrom");
                $Tvshow = new \Content\Model\TvshowModel();
                $rows = M("tvshow")->where($where)->field($fields)->select();
                break;
            // 热点视频
            case 4:
                $fields = array("resource", "bftime"); // 播出电视台 播发日期时间
                if (!isset($result['introduction'])) {
                    array_push($fields, "introduction");
                }
                $Video = new \Content\Model\VideoModel();
                $rows = $Video->where($where)->field($fields)->select();
                break;
            // 7	戏曲
            case 7:
                $fields = array("director", "actor");
                $Opera = new \Content\Model\OperaModel();
                $rows = $Opera->where($where)->field($fields)->select();
                break;
            // 8 专题节目总集
            case 8:
                $fields = array("attrid", "attrval");
                $rows = M("speciallinkattr")->where($where)->field($fields)->select();
                $Attr = M("attr");
                // 根据属性ID得到属性名
                foreach ($rows as $k => $row) {
                    $names = $Attr->where(array("id" => $row['attrid']))->field(array("name"))->select();
                    $rows[$k]['attrname'] = $names[0]['name'];
                }
                break;
            default:
                # ...
                throw_exception(__METHOD__ . "can't handle mediatypeid: " . $mediatypeid);
        }
        if ($mediatypeid === 8) {
            $result['attrs'] = $rows; // 专题节目自定义属性
        } else {
            $result = array_merge($result, $rows[0]);
        }

        return $result;
    }

    /**
     * 查看详情/编辑 打开对话框加载数据
     * @param $oid
     * @param null $mediatypeid
     * @return mixed
     */
    public function mediaInfo($oid, $mediatypeid = null) {
        if (is_null($mediatypeid)) {
            $mediatypeid = $this->getMediaTypeByOID($oid);
        }

        // 片名, 简介, 评分
        $fields = array("oid", "title", "introduction", "rating", "languageid", "channelid");
        $where = array("oid" => $oid);
        $rows = $this->field($fields)->where($where)->select();
        $result = $rows[0];

        // 类型, 年份, 语言(?)
        $Genre = new \Content\Model\MedialinkgenreModel();
        $result['genreid'] = $Genre->getgenreidbyoid($oid);

        $Year = new \Content\Model\MedialinkyearModel();
        $result['yearid'] = $Year->getyearidbyoid($oid);
        if (!intval($result['languageid'])) {
            $Language = new \Content\Model\MedialinklanguageModel();
            $result['languageid'] = $Language->getlanguageidbyoid($oid);
        }
        $Tag = new \Content\Model\MedialinktagModel();
        $result['tagid'] = $Tag->gettagidbyoid($oid);

        $Country = new \Content\Model\MedialinkcountryModel();
        $result['countryid'] = $Country->getcountryidbyoid($oid);

        // 热点视频类型 没有加入到media表?
        $Path = new \Content\Model\PathModel();
        if (!array_key_exists("title", $result) || !isset($result['title'])) {
            $rows = $Path->field("asset_name")->where($where)->select();
            $result['title'] = $rows[0]['asset_name'];
        }

        // 缩略图 + 海报
        $Appendix = new \Content\Model\AppendixModel();
        $appendixlist = $Appendix->listAppendixByOID($oid);
        $result['appendix'] = $appendixlist;
        unset($appendixlist);

        $mediatypeid = intval($mediatypeid);
        switch ($mediatypeid) {
            // 电影
            case 1:
                // 导演, 主要演员
                $fields = array("director", "actor", "runtime");
                $Movie = new MovieModel();
                $rows = $Movie->where($where)->field($fields)->select();
                $result = array_merge($result, $rows[0]);
                unset($Movie);
                break;
            // 电视剧总集
            case 5:
                $fields = array("director", "actor", "episodes");

                $Series = new \Content\Model\SeriesModel();
                $rows = $Series->where($where)->field($fields)->select();
                $result = array_merge($result, $rows[0]);

                $Seriesepisode = new \Content\Model\SeriesepisodeModel();
                $result['epslist'] = $Seriesepisode->listTVPlayEpisodesBySoid($oid); //@$oid 总集OID

                // 取得分集编辑状态 审核?
                $n = count($result['epslist']);
                $Editstatus = new \Content\Model\EditstatusModel();
                for ($i = 0; $i < $n; $i++) {
                    $result['epslist'][$i]['editstatus'] =
                        $Editstatus->getEditStatus($result['epslist'][$i]['episodeoid'], 'editstatus');
                }
                break;
            // 2	电视剧单集
            case 2:
                $fields = array("episodeoid", "actor", "runtime", "episodeindex", "seriesoid");
                if ('' == trim($result['title'])) {
                    array_push($fields, "title");
                }
                $Seriesepisode = new \Content\Model\SeriesepisodeModel();
                $rows = $Seriesepisode->where(array("episodeoid"=>$oid))->field($fields)->select();
                $rows[0]['oid'] = $rows[0]['episodeoid'];
                $result = array_merge($result, $rows[0]);
                unset($rows);

                $Series = new \Content\Model\SeriesModel();
                $rows = $Series->where(array("oid"=>$result['seriesoid']))->field(array("episodes"))->select();
                $result['episodes'] = $rows[0]['episodes'];

                break;
            // 电视节目总集
            case 6:
                $fields = array("host", "sourcefrom", "tvshowtype");
                $rows = D("tvshow")->where($where)->field($fields)->select();
                $result = array_merge($result, $rows[0]);

                $Tvshowepisode = new \Content\Model\TvshowepisodeModel();
                $result['epslist'] = $Tvshowepisode->listTVShowEpisodeBySoid($oid); //@$oid 总集OID
                // 取得分集编辑状态 审核?
                $n = count($result['epslist']);
                $Editstatus = new \Content\Model\EditstatusModel();
                for ($i = 0; $i < $n; $i++) {
                    $result['epslist'][$i]['editstatus'] =
                        $Editstatus->getEditStatus($result['epslist'][$i]['episodeoid'], 'editstatus');
                }
                break;
            // 电视节目分集
            case 3:
                if ('' == trim($result['title'])) {array_push($fields, "title");}

                $fields = array("tvshowoid", "episodeoid", "actor", "runtime", "theme", "episodeindex");
                $rows = D("tvshowepisode")->where(array("episodeoid"=>$oid))->field($fields)->select();
                $rows[0]['oid'] = $rows[0]['episodeoid'];
                $result = array_merge($result, $rows[0]);
                // 电视节目总集名称
                $condition = array("oid" =>$rows[0]['tvshowoid']);
                $rows = D("media")->where($condition)->field(array("title"))->select();
                $result['st'] = $rows[0]['title'];
                unset($rows);

                break;
            // 热点视频
            case 4:
                $fields = array("oid", "resource", "bftime"); // 播出电视台 播发日期时间
                $rows = D("video")->where($where)->field($fields)->select();
                $result = array_merge($result, $rows[0]);
                break;
            // 7 戏曲
            case 7:
                $fields = array("director", "actor", "runtime");
                $rows = D("opera")->where($where)->field($fields)->select();
                $result = array_merge($result, $rows[0]);
                break;
            /*
            // 8 专题节目总集
            case 8:
                $sm = new SpecialModel();
                $result = $sm->getSpecialSeriesInfo($oid);
                break;
            */
            default:
                # ...
                throw_exception(__METHOD__ . "can't handle mediatypeid: " . $mediatypeid);
        }

        return $result;
    }

    // 查看详情 通过对话框编辑
    public function saveMedia($data) {
        if (!isset($data['oid'])) {
            \throw_exception(__METHOD__ . ' oid is not set!');
        }
        $oid = $data['oid'];
        $mediatypeid = $data['mediatypeid'];

        if (is_null($mediatypeid)) {
            $mediatypeid = $this->getMediaTypeByOID($oid);
        }
        // 片名, 简介, 评分
        $fields = array("title", "introduction", "rating", "languageid", "channelid");
        $where = array("oid" => $oid);
        $a = $this->fieldFilter($data, $fields);
        $result['media'] = $this->where($where)->data($a)->save();
        unset($a);

        // 类型, 年份, 语言(?)
        $Genre = new \Content\Model\MedialinkgenreModel();
        $result['genreid'] = $Genre->setGenre(
            array("oid" => $oid, "genreid" => $data['genreid']));
        $Year = new \Content\Model\MedialinkyearModel();
        $result['yearid'] = $Year->setyear(
            array("oid" => $oid, "yearid" => $data['yearid']));
        $Lang = new \Content\Model\MedialinklanguageModel();
        $result['languageid'] = $Lang->setLang(
            array("oid" => $oid, "languageid" => $data['languageid']));
        $Tag = new \Content\Model\MedialinktagModel();
        $result['tagid'] = $Tag->setTag(
            array("oid" => $oid, "tagid" => $data['tagid']));
        $Country = new \Content\Model\MedialinkcountryModel();
        $result['countryid'] = $Country->setcountry(
            array("oid" => $oid, "countryid" => $data['countryid']));

        $mediatypeid = intval($mediatypeid);
        switch ($mediatypeid) {
            // 电影
            case 1:
                // 导演, 主要演员
                $fields = array("director", "actor", "runtime");
                $a = $this->fieldFilter($data, $fields);
                $Movie = new \Content\Model\MovieModel();
                $result['movie'] = $Movie->where($where)->data($a)->save();
                break;
            // 电视剧总集
            case 5:
                $fields = array("director", "actor", "episodes");
                $a = $this->fieldFilter($data, $fields);
                $Series = new \Content\Model\SeriesModel();
                $result['series'] = $Series->where($where)->data($a)->save();
                break;
            // 电视剧分集
            case 2:
                $fields = array("episodeoid", "actor", "runtime", "episodeindex", "seriesoid", "title");
                $a = $this->fieldFilter($data, $fields);
                $Seriesepisode = new \Content\Model\SeriesepisodeModel();
                $result['seriesepisode'] = $Seriesepisode->where(array("episodeoid"=>$oid))->data($a)->save();
                $Series = new \Content\Model\SeriesModel();
                $result['series'] = $Series->where(array("oid"=>$data['oid']))->data(array("episodes"=>$data['episodes']))->save();
                break;
            // 电视节目总集
            case 6:
                $fields = array("host", "sourcefrom", "tvshowtype");
                $a = $this->fieldFilter($data, $fields);
                $Tvshow = new \Content\Model\TvshowModel();
                $result['tvshow'] = $Tvshow->where($where)->data($a)->save();
                break;
            case 3:
                $data['episodeoid'] = $data['oid'];
                $fields = array("tvshowoid", "episodeoid", "actor", "runtime", "theme", "episodeindex");
                $a = $this->fieldFilter($data, $fields);
                $Tvshowepisode = new \Content\Model\TvshowepisodeModel();
                $result['tvshowepisode'] = $Tvshowepisode->where(array("episodeoid"=>$data['episodeoid']))->data($a)->save();
                break;
            // 热点视频
            case 4:
                $fields = array("oid", "resource", "bftime"); // 播出电视台 播发日期时间
                $a = $this->fieldFilter($data, $fields);
                $Video = new \Content\Model\VideoModel();
                $result['video'] = $Video->where($where)->data($a)->save();
                break;
            // 7 戏曲
            case 7:
                $fields = array("oid", "director", "actor", "runtime");
                $a = $this->fieldFilter($data, $fields);
                $Opera = new \Content\Model\OperaModel();
                $result['opera'] = $Opera->where($where)->data($a)->save();
                break;
            default:
                # ...
                throw_exception(__METHOD__ . "can't handle mediatypeid: " . $mediatypeid);
        }

        return $result;
    }

    /**
     * 生成新的总集OID
     * @return string S2016122800000000000000000000491, S + yyyymmdd + [0...][id]
     */
    public function generateSeriesOID() {
        $maxid = $this->max('id');
        $id = $maxid + 1;
        $format = "S%s%0" . (32 - strlen("S20170313")) . "d";
        return sprintf($format, date("Ymd", time()), $id);
    }

    // 字段过滤
    private function fieldFilter(&$data, &$fields) {
        $a = array();
        foreach ($data as $key => $value) {
            if (in_array($key, $fields)) {
                $a[$key] = $value;
            }
        }
        return $a;
    }

    public function getMediaTypeByOID($oid) {
        $where = array("oid" => $oid);
        $fields = array("mediatypeid");
        $rows = $this->where($where)->field($fields)->select();
        return $rows[0]['mediatypeid'];
    }
}