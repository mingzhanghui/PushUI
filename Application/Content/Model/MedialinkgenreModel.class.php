<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-26
 * Time: 14:33
 */
namespace Content\Model;
use Think\Model;

class MedialinkgenreModel extends Model {
    protected $tableName = 'medialinkgenre';
    protected $fields = array('id', 'oid', 'genreid');
    protected $pk     = 'id';

    public function getGenreNameByOID($oid) {
        $t = $this->where(array('oid'=>$oid))->field('genreid')->select();
        $genreid = $t[0]['genreid'];

        $Genre = new \Content\Model\GenreModel();
        $res = $Genre->where(array('id'=>$genreid))->field('genre')->select();
        return $res[0]['genre'];
    }

    public function getgenreidbyoid($oid) {
        $t = $this->where(array('oid'=>$oid))->field('genreid')->select();
        return $t[0]['genreid'];
    }

    // MBIS_Server_MedialinkGenre
    public function setGenre($data) {
        $data = array_filter($data);
        if (array_key_exists('genreid', $data)) {
            $condition = array('oid' => $data['oid']);
            $count = $this->where($condition)->count();
            if ($count > 0) {
                // update
                unset($data['oid']);
                return $this->where($condition)->save($data);

            } else {
                // add
                return $this->data($data)->add();
            }
        }
        return 0;
    }

}