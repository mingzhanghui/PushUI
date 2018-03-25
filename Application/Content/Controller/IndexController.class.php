<?php
namespace Content\Controller;
use Content\Common\File;
use Content\Model\EditstatusModel;
use Content\Model\MediaModel;
use Content\Model\MediatypeModel;
use Content\Model\PathModel;
use Home\Model\ConfigureModel;
use Think\Controller;

use Common\Common\Config;
use Common\Common\Logger;

// 媒体导入
class IndexController extends Controller {
    public function __construct() {
        parent::__construct();

        if (!isset($_SESSION)) {
            session_start();
        }
        $config = Config::getInstance();
        if ($config->hasLogin()) {
            if (!isset($_SESSION['username']) || !$_SESSION['content']) {
                redirect(U('Content/Login/index').'?refer='.U());
            }
        } else {
            $_SESSION['username'] = 'Guest';
            $_SESSION['content'] = 1;
        }
    }

    public function index() {

        // D:\software\wamp\www\PushUI\resource\media
        $Configure = new ConfigureModel();
        $contentpath = $Configure->getcontentpath();
        $ftppath = $contentpath . '/ftp';
        if (!is_dir($ftppath)) {
            mkdir($ftppath, 0777, true);
        }
        $this->assign('ftppath', $ftppath);
        $this->assign('contentpath', $contentpath);

        // 可导入文件列表
        $files = array();
        File::recursiveListPath($files, $ftppath);

        $pagesize = 14;

        $curpage = I('get.p');
        $pagecount = intval(count($files) / $pagesize) + 1;
        if ($curpage == null || $curpage == 0) {
            $curpage = 1;
        } else if ($curpage > $pagecount) {
            $curpage = $pagecount;
        }
        $prevpage = ($curpage - 1) < 1 ? $curpage : ($curpage - 1);
        $nextpage = ($curpage + 1) > $pagecount ? $curpage : ($curpage + 1);

        $offset = ($curpage-1)*$pagesize;
        $this->assign('offset', $offset);

        $files = array_slice($files, $offset, $pagesize);
        $this->assign('files', $files);

        $this->assign('curpage', $curpage);
        $this->assign('prevpage', $prevpage);
        $this->assign('nextpage', $nextpage);
        $this->assign('pagecount', $pagecount);

        // 媒体文件类型
        $MediaType = new MediatypeModel();
        $this->assign( 'mediatypes', $MediaType->getFileTypes() );

        // 今日导入媒体列表
        $today = date('Y-m-d', time());
        $Path = new PathModel();
        $list = $Path->getMediaByDate($today);

        $map = $MediaType->mapMediaType();
        foreach ($list as $i => $v) {
            $list[$i]['mediatype'] = $map[ $list[$i]['mediatypeid'] ];
            $list[$i]['size'] = File::humansize($list[$i]['size']);
        }

        $this->assign('list', $list);
        $this->display();
    }

    private function getcontentpath() {
        $config = new \Home\Model\ConfigureModel();
        $res =  $config->getcontentpath();
        return $res['stringvalue'];
    }

    // 导入媒体文件类型 不包括总集 老乡节目 和未定义0
    public function getmediatypes() {
        $Mediatype = M('mediatype');
        $list = $Mediatype->select();
        $list = array_filter($list, function($var) {
            if (is_array($var)) {
                if (isset($var['id']) && isset($var['mediatype'])) {
                    return ($var['id'] != 0
                        && $var['mediatype'] != '老乡节目'
                        && !mb_strpos($var['mediatype'], '总集'));
                }
            }
        });
        return $list;
    }

    /**
     * 导入文件
     * @return bool
     */
    public function upfile() {
        $srcdir = I('post.srcdir');
        $names = I('post.names');
        $mediatype = I('post.mediatype');
        $dstdir = I('post.dstdir');

        $namelist = explode('|', $names);
        if (count($namelist) < 1) {
            $msg = serialize(I('post.'));
            $this->ajaxReturn(array('code'=>-1, 'msg'=>$msg));
        }
	$date = date("Y-m-d",time());
        $Path = new PathModel();        
	// 计算大文件MD5值超时 可能>30s
        set_time_limit(60);
        foreach ($namelist as $key => $value) {
            // MBIS_Server_Path
            $parts = mb_split('/', $value);
            $asset_name = array_pop($parts);  // utf-8
            $dir = implode('/', $parts);
            unset($parts);

            // 源文件gbk
            $value = mb_convert_encoding($value, 'gbk', 'utf-8');
            $path = $srcdir . '/' . $value;

            $tmpname = date('YmdHis',time()) .rand(0,1000);
            $tmppath = $srcdir.'/'.$dir.'/'.$tmpname;
            // rename
            // copy($path, $tmppath);
            rename($path, $tmppath);



            // content type = 'application/octet-stream', suffix = 'ts'
            if (function_exists('mime_content_type')) {
                $contentType = mime_content_type($tmppath);
		if ( $contentType != 'application/octet-stream' ) {
                    $result = array('code' => 6, 'msg' => $value.'不是有效的ts文件格式');
                    $this->ajaxReturn($result);
                }
            }
            $oid = md5_file($tmppath);
            $oid = strtoupper($oid);
	    
            // query table MBIS_Server_Path
	    if ($Path->existsOID($oid)) {
                $result = array('code'=>3, 'msg'=>'请不要重复上传同一个文件:'.urlencode($value));
                $this->ajaxReturn($result);
            }

            // file size > 2G?
            $size = File::realFileSize($tmppath);
            $size = strval($size);

            // 2G
            if ($size < 2147483648) {
                if ($size % 188 != 0) {
                    $result = array('code' => 6, 'msg' => '请检查文件格式的完整性(文件以0x47开头,文件大小是188的整数倍)');
                    $this->ajaxReturn( $result );
                }
            }

            if ( disk_free_space($dstdir) < $size ) {
                $result = array('code' => 1, 'msg' => '存储目录空间不足，无法上传！');
                $this->ajaxReturn($result);
            }
            if(!is_file($tmppath)) {
                $result = array(
                    'code'=> 2,
                    'msg' => '上传文件在指定目录下不存在, 请从指定文件夹下上传文件！');
                $this->ajaxReturn($result);
            }

            $url = $this->getMediaPrefix($mediatype) .'_'.$date.'_'.$oid.'.ts';
            $data = array(
                'oid'        => $oid,
                'asset_name' => $asset_name,
                'mediatypeid'=> $mediatype,
                'url'        => $url,
                'size'       => $size,
                'date'       => $date,
                'state'      => 0
            );
            $idPath = $Path->data($data)->add();

            // MBIS_Server_Mediaxxx
            unset($data['url']);
            unset($data['size']);
            unset($data['date']);
            unset($data['state']);
            $data['title'] = $data['asset_name'];
            unset($data['Assset_Name']);

            // BEGIN ===== 特定媒体类型的表 movie, seriesepisode, ... == //
            $model = null;
            $mediatypeid = $data['mediatypeid'];
            $col = '';
            switch ($mediatypeid) {
                case '1':$model = new \Content\Model\MovieModel();  $col = 'oid'; break;
                case '2': $model = new \Content\Model\SeriesepisodeModel(); $col = 'episodeoid'; break;
                case '3': $model = new \Content\Model\TvshowepisodeModel(); $col = 'episodeoid'; break;
                case '4': $model = new \Content\Model\VideoModel(); $col = 'oid'; break;
                case '7': $model = new \Content\Model\OperaModel(); $col = 'oid'; break;
                case '9': $model = new \Content\Model\SpecialepisodeModel();  $col = 'episodeoid'; break;
            }
            $where = array($col => $oid);
            $t = $model->where($where)->field($col)->select();

            if (is_null($t[0][$col])) {
                $model->data($where)->add() > 0 ? ($res['movie'] = 1) : ($res['movie'] = 0);
            } else {
                $res['movie'] = 1;
            }
            // END ========== 特定媒体表 ==================================//

            // BEGIN ================== media表 ========================== //
            $media = new MediaModel();
            $where = array('oid' => $oid);
            $t = $media->where($where)->field('oid')->select();
            if (is_null($t[0]['oid'])) {
                $media->data($data)->add() > 0 ? ($res['media'] = 1) : ($res['media'] = 0);
            } else {
                unset($data['oid']);
                $data['title'] = $value;
                $media->data($data)->where($where)->save();
                $res['media'] = 1;
            }
            $idMedia = array_sum($res);
            unset($data);
            // END ====================== media表 ======================== //

            // MBIS_Server_EditStatus
            $EditStatus = new EditstatusModel();
            $idEditStatus = $EditStatus->newEditStatus($oid);

            //  D:\software\wamp\www\PushUI\resource\media\ftp\xx.ts ->
            //  D:\software\wamp\www\PushUI\resource\media\xx.ts
            if($idPath) {
                $res = rename($tmppath, $dstdir.'/'.$url);
                if ($res) {
                    continue;
                } else {
                    $result = array('code' => 4,
                        'msg' => sprintf("rename failed [%d-%d-%d]", $idPath, $idMedia, $idEditStatus));
                    $this->ajaxReturn($result);
                }
            } else {
                $result = array('code' => 5,
                    'msg' => sprintf("上传媒体失败，请返回重新试一次[%d-%d-%d]", $idPath, $idMedia, $idEditStatus));
                $this->ajaxReturn($result);
            }

        } // end foreach ($arr as $k => $value)

        // 内容导入log
        $config = Config::getInstance();
        if ($config->hasDailyRecord()) {
            Logger::setpath(C('DAILY_RECORD_DIR'));
            Logger::write('内容编辑/内容导入: '.json_encode($_POST), $_SESSION['username']);
        }

        $this->ajaxReturn(array('code'=>0, 'msg'=>'导入成功'));
    } // end public function upfile()

    // 生成跳转到内容编辑的URL, 要编辑的oid写入cookie
    public function getediturl() {
        $oid = I('get.oid');
        $typeid = I('get.typeid');
        setcookie('oid', $oid, time() + 60);

        $method = 'index';
        switch ($typeid) {
            case 1: $method = 'index'; break;
            case 2: $method = 'tvplay'; break;
            case 3: $method = 'tvprogram'; break;
            case 4: $method = 'video'; break;
            case 7: $method = 'opera'; break;
            case 9: $method = 'special'; break;  // special
            default: throw_exception('Unkown media type');
        }
        $this->ajaxReturn( U('Content/Edit/' . $method) );
    }

    private function getMediaPrefix($typeid) {
        $typeid = intval($typeid);
        $name = 'unknown';
        switch ($typeid) {
            case 1:$name = 'mov';
                break;
            case 2:$name = 'series';
                break;
            case 3:$name = 'program';
                break;
            case 4:$name = 'video';
                break;
            case 7:$name = 'opera';
                break;
            case 8:
            case 9:
                $name = 'sp';
        }
        return $name;
    }

}