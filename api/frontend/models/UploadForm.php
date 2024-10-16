<?php

namespace frontend\models;

use yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

class UploadForm extends ActiveRecord
{
    public $file;

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xls,xlsx,csv'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'file' => '上传文件'
        ];
    }

    public function upload()
    {
        $file = UploadedFile::getInstanceByName('file');
        $randnums = $this->getrandnums();                   // 生成一个随机数，为了重命名文件
        $ext = $file->getExtension();
        $tmp_file = date("YmdHis") . $randnums . '.' . $ext;
        $path = 'upload/' . 'Files/';
        if (is_dir($path)) {
            $file->saveAs($path . $tmp_file);
        } else {
            mkdir($path, 0777, true);
        }
        $file->saveAs($path . $tmp_file);
        return $tmp_file;

    }

    /**
     * 生成随机数
     * @return string 随机数
     */
    protected function getrandnums()
    {
        $arr = array();
        while (count($arr) < 10) {
            $arr[] = rand(1, 10);
            $arr = array_unique($arr);
        }
        return implode("", $arr);
    }

}
